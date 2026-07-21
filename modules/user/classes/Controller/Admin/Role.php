<?php
/**
 * Admin Role Controller
 *
 * @package   Gleez\User\Admin\Controller
 * @author    Gleez Team
 * @version   1.0.1
 * @copyright (c) 2011-2014 Gleez Technologies
 * @license   https://gleezcms.org/license
 */
class Controller_Admin_Role extends Controller_Admin {

    /**
     * The before() method is called before controller action
     *
     * @throws Cache_Exception
     * @throws HTTP_Exception
     * @throws Http_Exception_415
     * @throws Kohana_Exception
     * @throws View_Exception
     * @uses ACL::required
     */
	public function before()
	{
		ACL::required('administer users');

		parent::before();
	}

    /**
     * List user roles
     *
     * @throws Kohana_Exception
     * @uses ORM::dataTables
     * @uses Request::is_datatables
     */
	public function action_list()
	{
		$is_datatables = Request::is_datatables();

		if ($is_datatables)
		{
            $roles = ORM::factory('Role');
            $this->_datatables = $roles->dataTables(['name', 'description', 'special']);

			foreach ($this->_datatables->result() as $role)
			{
                $this->_datatables->add_row([
                    HTML::chars($role->name),
                    HTML::chars($role->description),
                    $role->special ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-ban"></i>',
                    $role->special
                        ? HTML::icon($role->perm_url, 'fas fa-lock', ['title' => __('Edit Permissions')])
                        : HTML::icon($role->edit_url, 'far fa-edit', ['title' => __('Edit Role')])
                        . '&nbsp;'
                        . HTML::icon($role->delete_url, 'fas fa-trash-can', ['title' => __('Delete Role')])
                        . '&nbsp;'
                        . HTML::icon($role->perm_url, 'fas fa-lock', ['title' => __('Edit Permissions')])
                ]);
			}
		}

		$this->title = __('Roles');
        $add_url = Route::get('admin/role')->uri(['action' => 'add']);
        $url = Route::url('admin/role', ['action' => 'list'], TRUE);

		$view = View::factory('admin/role/list')
				->bind('datatables',   $this->_datatables)
				->set('is_datatables', $is_datatables)
				->set('add_url',       $add_url)
				->set('url',           $url);


		$this->response->body($view);
	}

    /**
     * Add new role
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses Log:add
     * @uses Route::get
     * @uses Route::uri
     * @uses Message::success
     */
	public function action_add()
	{
        $action = Route::get('admin/role')->uri(['action' => 'add']);

		$view = View::factory('admin/role/form')
					->set('action',  $action)
					->bind('post',   $post)
					->bind('errors', $this->_errors);

		$this->title = __('Add Role');
        $post = ORM::factory('Role');

		if ($this->valid_post('role'))
		{
            $post->values($_POST, ['name', 'description', 'special']);
			try
			{
				$post->save();
                Message::success(__('Role %name saved successful!', ['%name' => $post->name]));

				$this->request->redirect(Route::get('admin/role')->uri(), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$this->response->body($view);
	}

    /**
     * Add new role
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses Message::error
     * @uses Log:add
     * @uses Route::get
     * @uses Route::uri
     * @uses Message::success
     */
	public function action_edit()
	{
		$id = (int) $this->request->param('id', 0);

        $post = ORM::factory('Role', $id);

		if(!$post->loaded())
		{
			Message::error(__("Role doesn't exists!"));
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent role.');

			$this->request->redirect(Route::get('admin/role')->uri());
		}

        $this->title = __('Edit role %name', ['%name' => $post->name]);
        $action = Route::get('admin/role')->uri(['id' => $post->id, 'action' => 'edit']);

		$view = View::factory('admin/role/form')
					->set('action', $action)
					->set('errors', $this->_errors)
					->bind('post',  $post);

		if ( $this->valid_post('role') )
		{
            $post->values($_POST, ['name', 'description', 'special']);

			try
			{
				$post->save();

                Message::success(__('Role %name updated successful!', ['%name' => $post->name]));

				$this->request->redirect(Route::get('admin/role')->uri(), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$this->response->body($view);
	}

    /**
     * @throws View_Exception
     * @throws Kohana_Exception
     */
    public function action_delete()
	{
		$id = (int) $this->request->param('id', 0);

        $role = ORM::factory('Role', $id);

		if ( ! $role->loaded())
		{
            Message::error(__("Role: doesn't exists!"));
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent role.');
			$this->request->redirect(Route::get('admin/role')->uri());
		}

        $this->title = __('Delete :title', [':title' => $role->name]);

        $view = View::factory('form/confirm')
            ->set('action', Route::url('admin/role', ['action' => 'delete', 'id' => $role->id]))
            ->set('title', $role->name);

		// If deletion is not desired, redirect to list
        if (isset($_POST['no']) && $this->valid_post())
		{
			$this->request->redirect(Route::get('admin/role')->uri());
		}

		// If deletion is confirmed
        if (isset($_POST['yes']) && $this->valid_post())
		{
			try
			{
				$role->delete(); //delete the role
                Message::success(__('Role: :name deleted successful!', [':name' => $role->name]));

				$this->request->redirect(Route::get('admin/role')->uri());
			}
			catch (Exception $e)
			{
                Kohana::$log->add(Log::ERROR, 'Error occurred deleting role id: :id, :message', [
                    ':id' => $role->id,
                    ':message' => $e->getMessage()
                ]);
                Message::error(__('An error occurred while deleting the role :name.', [':name' => $role->name]));

				$this->request->redirect(Route::get('admin/role')->uri());
			}
		}

		$this->response->body($view);
	}
}
