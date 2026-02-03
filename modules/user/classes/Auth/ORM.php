<?php
/**
 * User authorization library
 *
 * Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Gleez\Auth\Base
 * @author     Gleez Team
 * @version    1.1.2
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class Auth_ORM extends Kohana_Auth_ORM
{
    /**
     * Stores the last error key for failed login attempts.
     *
     * @var string|null
     */
    public static $lastErrorKey = null;

	/**
	 * Get enabled oAuth2 providers
	 * @return array
	 *
	 * @uses   Module::is_active
	 */
	public static function providers()
	{
		if ( ! Module::is_active('oauth2'))
			return array();

		$config    = Kohana::$config->load('oauth2')->get('providers', array());
		$providers = array();

		foreach($config as $name => $provider)
		{
			if ($provider['enable'] === TRUE)
			{
				$providers[$name] = array(
					'name' => $name,
					'url'  => Route::get('oauth2/provider')->uri(array('provider' => $name, 'action' => 'login')),
					'icon' => isset($provider['icon']) ? $provider['icon'] : 'facebook',
				);
			}
		}

		return $providers;
	}

	/**
	 * Checks if a user logged in via an OAuth provider.
	 *
	 * @param   string   $provider  Provider name (e.g. 'twitter', 'google', etc.) [Optional]
	 * @return  boolean
	 */
	public function logged_in_oauth($provider = NULL)
	{
		// For starters, the user needs to be logged in
		if ( ! parent::logged_in())
			return FALSE;

		// Get the user from the session.
		// Because parent::logged_in returned TRUE, we know this is a valid user ORM object.
		$user = $this->get_user();

		if ($provider !== NULL)
		{
			// Check for one specific OAuth provider
			$provider = $provider.'_id';
			//return ! empty($user->$provider);
		}

		// Otherwise, just check the password field.
		// We don't store passwords for OAuth users.
		//return empty($user->pass);
	}

	/**
	 * Get 3rd party provider used to sign in
	 *
	 * @return  string
	 */
	public function get_provider()
	{
		return $this->_session->get($this->_config['session_key'] . '_provider', NULL);
	}

    /**
     * Perform a hmac hash, using the configured method.
     *
     * @param string $str  String to hash
     * @return  string
     */
    public function hash(string $str): string
    {
        $algo = Kohana::$config->load('auth')->get('hash_method', '');
        $key = Kohana::$config->load('site')->get('auth_hash_key', '');

        return hash_hmac($algo, $str, $key);
    }

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username string, or user ORM object
	 * @return  string
	 */
	public function password($user): string
    {
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
            $user = ORM::factory('User');
			$user->where($user->unique_key($username), '=', $username)->find();
		}

		return $user->pass;
	}

	/**
	 * Compare password with original (hashed). Works for current (logged in) user
	 *
	 * @param   string  $password
	 * @return  bool
	 */
	public function check_password($password): bool
    {
		$user_model = $this->get_user();
		$user = $user_model->original_values();

		if ( ! $user)
		{
			return FALSE;
		}

		//Avoid Timing attacks
		return System::hashEquals($user['pass'], $this->hash($password));
	}

	/**
	 * Forces a user to be logged in when using SSO, without specifying a password.
	 *
	 * @param   ORM      $user
	 * @param   boolean  $mark_session_as_forced
	 * @return  boolean
	 */
	public function force_sso_login(ORM $user, $mark_session_as_forced = FALSE)
	{
		if ($mark_session_as_forced === TRUE)
		{
			// Mark the session as forced, to prevent users from changing account information
			$this->_session->set('auth_forced', TRUE);
		}

		// Token data
		$data = array(
			'user_id'    => $user->id,
			'expires'    => time() + $this->_config['lifetime'],
			'user_agent' => sha1(Request::$user_agent),
		);

		// Create a new autologin token
        $token = ORM::factory('User_Token')
            ->values($data, ['user_id', 'expires', 'user_agent'])
			->create();

		// Set the autologin cookie
		Cookie::set('authautologin', $token->token, $this->_config['lifetime']);

		// Run the standard completion
		$this->complete_login($user);
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable autologin
	 * @return  bool
	 */
	protected function _login($username, $password, $remember): bool
    {
        $config = Kohana::$config->load('auth')->get('auth');
        $cache = Cache::instance();

        $maxFailedLogins = (int) ($config['max_failed_logins'] ?? 0);
        $loginJailTime = (int) ($config['login_jail_time'] ?? 0);

        $failedAttemptsKey = 'auth:failed_attempts:' . $username;
        $failedAttempts = (int) $cache->get($failedAttemptsKey, 0);

        if ($maxFailedLogins > 0 && $failedAttempts >= $maxFailedLogins) {
            $lastAttemptTime = (int) $cache->get($failedAttemptsKey . ':time', 0);
            if (time() - $lastAttemptTime < $loginJailTime) {
                // Add a small delay to prevent timing attacks on jail status
                sleep(1);

                Auth_ORM::$lastErrorKey = 'too_many_attempts';

                return false;
            } else {
                // Jail time expired, reset attempts
                $cache->delete($failedAttemptsKey);
                $cache->delete($failedAttemptsKey . ':time');
                $failedAttempts = 0;
            }
        }

        // Load the user
        $user = ORM::factory('User');
        $user->where($user->unique_key($username), '=', $username)->find();

        // If user not found, introduce a delay to prevent enumeration
        if (!$user->loaded()) {
            // Delay for 1 second
            sleep(1);

            if ($maxFailedLogins > 0) {
                $cache->set($failedAttemptsKey, $failedAttempts + 1, $loginJailTime);
                $cache->set($failedAttemptsKey . ':time', time(), $loginJailTime);
            }

            return false;
        }

		// If the passwords match, perform a login! role id: 2
		if ($user->has('roles', 2) AND User::check_pass($user, $password) AND $user->id !== 1)
		{
            // Successful login, reset failed attempts
            if ($maxFailedLogins > 0) {
                $cache->delete($failedAttemptsKey);
                $cache->delete($failedAttemptsKey . ':time');
            }

			if ($remember === TRUE)
			{
				// Token data
				$data = array(
					'user_id'    => $user->id,
					'expires'    => time() + $this->_config['lifetime'],
					'user_agent' => sha1(Request::$user_agent),
					'type'	     => 'autologin',
					'created'    => time(),
				);

				// Create a new autologin token
                $token = ORM::factory('User_Token')
                    ->values($data, ['user_id', 'expires', 'user_agent', 'type', 'created'])
					->create();

				// Set the autologin cookie
				Cookie::set('authautologin', $token->token, $this->_config['lifetime']);
			}

			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
        if ($maxFailedLogins > 0) {
            $cache->set($failedAttemptsKey, $failedAttempts + 1, $loginJailTime);
            $cache->set($failedAttemptsKey . ':time', time(), $loginJailTime);
        }

		return FALSE;
	}
}
