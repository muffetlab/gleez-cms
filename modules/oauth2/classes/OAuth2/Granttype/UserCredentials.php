<?php
/**
 * Helper OAuth2 Password Grant Type
 *
 * @package    Gleez\OAuth2
 * @author     Gleez Team
 * @version    1.0.0
 * @copyright  (c) 2011-2013 Gleez Technologies
 * @license    https://gleezcms.org/license Gleez CMS License
 */
class Oauth2_GrantType_UserCredentials implements Oauth2_GrantType_Interface
{
	protected $userInfo;
	protected $config;
	protected $request;
	protected $response;

    public function __construct(array $config = [])
	{
		$this->config = $config;
	}

    public function getQuerystringIdentifier(): string
    {
		return 'password';
	}

    /**
     * @throws Oauth2_Exception
     * @throws Kohana_Exception
     */
    public function validateRequest(Request $request, Response $response): bool
    {
		$this->request  = $request;
		$this->response = $response;

		if (!$request->post("password") || !$request->post("username")) {
			throw Oauth2_Exception::factory(400, 'invalid_request', 'Missing parameters: "username" and "password" required');
		}

		if (! $userInfo = $this->checkUserCredentials($request->post("username"), $request->post("password"))) {
			throw Oauth2_Exception::factory(400, 'invalid_grant', 'Invalid username and password combination');
		}

		if (empty($userInfo)) {
			throw Oauth2_Exception::factory(400, 'invalid_grant', 'Unable to retrieve user information');
		}

		if (!isset($userInfo['id'])) {
			throw new Kohana_Exception("you must set the user_id on the array returned by checkUserCredentials");
			//$this->setError(500, 'server_error', 'you must set the user_id on the array returned by checkUserCredentials');
		}

		$this->userInfo = $userInfo;

		return TRUE;
	}

	public function getClientId()
	{
		return NULL;
	}

	public function getUserId()
	{
		//return isset($this->userInfo['user_id']) ? $this->userInfo['user_id'] : NULL;
        return $this->userInfo['id'] ?? NULL;
	}

	public function getScope()
	{
        return $this->userInfo['scope'] ?? NULL;
	}

    /**
     * @throws Oauth2_Exception
     */
    public function createAccessToken($client_id, $user_id, $scope = NULL)
	{
		try
		{
			$issueRefreshToken = Kohana::$config->load('oauth2')->get('includeRefreshToken', true);
			return Model::factory('oauth')->createAccessToken($client_id, $user_id, $scope, $issueRefreshToken);
		}
		catch (Exception $e)
		{
			throw Oauth2_Exception::factory(500, 'server_error', 'The Token server encountered an unexpected condition which prevented it from fulfilling the request.');
		}
	}

	protected function checkUserCredentials($name, $pass)
	{
		return Model::factory('oauth')->checkUserCredentials($name, $pass);
	}
}