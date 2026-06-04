<?php

/**
 * Admin Path Controller
 *
 * @package    Gleez\Controller\Admin
 * @author     Gleez Team
 * @version    1.0.1
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Admin_Path extends Controller_Admin {

    /**
     * The before() method is called before controller action
     *
     * @throws HTTP_Exception
     * @throws HTTP_Exception_403
     * @throws Http_Exception_415
     * @throws Kohana_Exception
     * @throws View_Exception
     * @uses  ACL::required
     */
	public function before()
	{
		ACL::required('administer paths');

		parent::before();
	}

    /**
     * List path aliases
     *
     * @throws Kohana_Exception
     * @uses  ORM::dataTables
     * @uses  HTML::chars
     * @uses  HTML::icon
     * @uses  Route::url
     * @uses  Assets::popup
     * @uses  Request::is_datatables
     */
	public function action_list()
	{
		Assets::popup();

		$is_datatables = Request::is_datatables();
        $paths = ORM::factory('Path');

		if ($is_datatables)
		{
			$this->_datatables = $paths->dataTables(array('source', 'alias'));

			foreach ($this->_datatables->result() as $path)
			{
				$this->_datatables->add_row(
					array(
                        HTML::chars($path->source),
                        HTML::chars($path->alias),
                        HTML::icon($path->edit_url, 'fa far fa-edit', array('class' => 'btn btn-sm btn-default action-edit', 'title' => __('Edit Alias'))) . '&nbsp;' .
                        HTML::icon($path->delete_url, 'fa fas fa-trash-can', array('class' => 'btn btn-sm btn-default action-delete', 'title' => __('Delete Alias'), 'data-toggle' => 'popup', 'data-table' => '#admin-list-paths'))
					)
				);
			}
		}

		$this->title = __('Path Aliases');
		$add_url     = Route::get('admin/path')->uri(array('action' =>'add'));
		$url         = Route::url('admin/path', array('action' => 'list'), TRUE);

		$view = View::factory('admin/path/list')
				->bind('datatables',   $this->_datatables)
				->set('is_datatables', $is_datatables)
				->set('add_url',       $add_url)
				->set('url',           $url);

		$this->response->body($view);
	}

    /**
     * Add path alias
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses  Route::uri
     * @uses  URL::site
     * @uses  Message::success
     * @uses  Route::get
     */
	public function action_add()
	{
		$this->title = __('Creating an Alias');
		$action      = Route::get('admin/path')->uri(array('action' =>'add'));

		$view = View::factory('admin/path/form')
			->bind('errors', $this->_errors)
			->bind('post',   $post)
            ->set('url', URL::site('', TRUE))
			->set('action',  $action);

        $post = ORM::factory('Path');

		if($this->valid_post('add_path'))
		{
            $post->values($_POST, ['source', 'alias']);
			try
			{
				$post->save();

				Message::success(__('Alias %name saved successful!', array('%name' => $post->alias)));

				$this->request->redirect(Route::get('admin/path')->uri(array('action' => 'list')), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$this->response->body($view);
	}

    /**
     * Edit path alias
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses  Route::uri
     * @uses  Message::error
     * @uses  Message::success
     * @uses  URL::site
     * @uses  Route::get
     */
	public function action_edit()
	{
		$id = (int) $this->request->param('id', 0);

        $post = ORM::factory('Path', $id);

		if ( ! $post->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent alias.');
			Message::error(__('Alias doesn\'t exists!'));

			$this->request->redirect(Route::get('admin/path')->uri(array('action' => 'list')), 404);
		}

		$this->title = __('Edit Alias %name', array('%name' => $post->source));
		$action      = Route::get('admin/path')->uri( array('id' => $post->id, 'action' => 'edit'));

		$view = View::factory('admin/path/form')
				->bind('errors', $this->_errors)
				->bind('post',   $post)
            ->set('url', URL::site('', TRUE))
				->set('action',  $action);

		if ($this->valid_post('add_path'))
		{
            $post->values($_POST, ['source', 'alias']);

			try
			{
				$post->save();

				Message::success(__('Alias %name saved successful!', array('%name' => $post->source)));

				$this->request->redirect(Route::get('admin/path')->uri(array('action' => 'list')), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$this->response->body($view);
	}

    /**
     * Delete path alias
     *
     * @throws Kohana_Exception
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  Message::error
     */
	public function action_delete()
	{
		$id = (int) $this->request->param('id', 0);

        $path = ORM::factory('Path', $id);

		if ( ! $path->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent alias.');
			Message::error(__('Alias doesn\'t exists!'));

			$this->request->redirect(Route::get('admin/path')->uri( array('action' => 'list')), 404);
		}

		$this->title = __('Delete Alias %title', array('%title' => $path->source));

		$view = View::factory('form/confirm')
			->set('action',  $path->delete_url)
			->set('title',   $path->alias);

		// If deletion is not desired, redirect to list
		if ( isset($_POST['no']) AND $this->valid_post())
		{
			$this->request->redirect(Route::get('admin/path')->uri());
		}

		// If deletion is confirmed
		if ( isset($_POST['yes']) AND $this->valid_post() )
		{
			try
			{
				$path->delete();
				Message::success(__('Alias %name deleted successful!', array('%name' => $path->alias)));

				$this->request->redirect(Route::get('admin/path')->uri( array('action' => 'list')), 200);
			}
			catch (Exception $e)
			{
				Kohana::$log->add(Log::ERROR, 'Error occured deleting alias id: :id, :msg',
					array(':id' => $path->id, ':message' => $e->getMessage())
				);
				Message::error('An error occurred deleting alias %path',array(':path' => $path->alias));

				$this->request->redirect(Route::get('admin/path')->uri( array('action' => 'list')), 503);
			}
		}

		$this->response->body($view);
	}

}
