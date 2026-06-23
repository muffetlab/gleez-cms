<?php

/**
 * Admin Base Controller
 *
 * @package    Gleez\Controller\Admin
 * @author     Gleez Team
 * @version    1.2.0
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Admin extends Template {

	/**
	 * Page template
     * @var string|View
	 */
	public $template = 'layouts/admin';

	/**
	 * Currently destination
	 * @var string
	 */
	protected $_destination;

	/**
	 * Currently form action
	 * @var string
	 */
	protected $_form_action;

	/**
     * Currently logged-in user
	 * @var
	 */
	protected $_current_user;

    /**
     * The before() method is called before controller action
     *
     * @throws HTTP_Exception
     * @throws Http_Exception_415
     * @throws Kohana_Exception
     * @throws View_Exception
     * @uses  ACL::required
     * @uses  Theme::$is_admin
     */
	public function before()
	{
		// Inform tht we're in admin section for themers/developers
		Theme::$is_admin = TRUE;

		if($this->request->action() != 'login')
		{
			ACL::redirect('administer site', 'admin/login');
		}

		parent::before();
	}

    /**
     * @throws Kohana_Exception
     * @throws View_Exception
     */
    public function action_login(){
		
		if ($this->_auth->logged_in())
		{
			// redirect to the user account
			$this->request->redirect(Route::get('admin')->uri(), 200);
		}

		// Disable sidebars on login page
		$this->_sidebars = FALSE;

		$this->title = __('Sign In');
        $user = ORM::factory('User');

		// Create form action
        $destination = $_GET['destination'] ?? 'admin';
		$params      = array('action' => 'login');
		$action      = Route::get('admin/login')->uri($params).URL::query(array('destination' => $destination));

        if (kohana::find_file('views', 'layouts/login'))
		{
			$this->template->set_filename('layouts/login');
		}			
			
		$view = View::factory('admin/login')
			->set('use_username', Kohana::$config->load('auth')->get('username'))
			->set('post',         $user)
			->set('action',       $action)
			->bind('errors',      $this->_errors);

		if ($this->valid_post('login'))
		{
			try
			{
				// Check Auth
				$user->login($this->request->post());

				// If the post data validates using the rules setup in the user model
				Message::success(__('Welcome, %title!', array('%title' => $user->nick)));
				Kohana::$log->add(Log::INFO, 'User :name logged in.', array(':name' => $user->name));

				// redirect to the user account
                $this->request->redirect($_GET['destination'] ?? 'admin', 200);
			}
			catch (Validation_Exception $e)
			{
                $this->_errors = $e->array->errors('login');
			}
		}

		$this->response->body($view);
	}

	public function after()
	{
		Assets::css('admin', "media/css/admin.css", array('default'), array('weight' => 60));
		parent::after();
	}

	public function index()
	{
		$this->response->body(__('Welcome to admin'));
	}
}
