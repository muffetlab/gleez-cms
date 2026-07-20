<?php

/**
 * Admin Tag Controller
 *
 * @package    Gleez\Controller\Admin
 * @author     Gleez Team
 * @version    1.0.1
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Admin_Tag extends Controller_Admin {

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
		ACL::required('administer tags');

		parent::before();
	}

    /**
     * List tags
     *
     * @throws Kohana_Exception
     * @uses  ORM::dataTables
     * @uses  HTML::chars
     * @uses  HTML::icon
     * @uses  Route::url
     * @uses  Route::get
     * @uses  Assets::popup
     * @uses  Request::is_datatables
     */
	public function action_list()
	{
		Assets::popup();

		$is_datatables = Request::is_datatables();

		if ($is_datatables)
		{
            $tags = ORM::factory('Tag');
            $this->_datatables = $tags->dataTables(['name', 'id', 'type']);

			foreach ($this->_datatables->result() as $tag)
			{
                $this->_datatables->add_row([
                        HTML::chars($tag->name),
						HTML::anchor($tag->url, $tag->url),
                        HTML::chars($tag->type),

                    HTML::icon($tag->edit_url, 'fa far fa-edit', [
                        'class' => 'btn btn-sm btn-default action-edit',
                        'title' => __('Edit Tag')
                    ])
                    . '&nbsp;'
                    . HTML::icon($tag->delete_url, 'fa fas fa-trash-can', [
                        'class' => 'btn btn-sm btn-default action-delete',
                        'title' => __('Delete Tag'),
                        'data-toggle' => 'popup',
                        'data-table' => '#admin-list-tags'
                    ])
                ]);
			}
		}

		$this->title = __('Tags');
        $url = Route::url('admin/tag', ['action' => 'list'], TRUE);

		$view = View::factory('admin/tag/list')
				->bind('datatables',   $this->_datatables)
				->set('is_datatables', $is_datatables)
				->set('url',           $url);

		$this->response->body($view);
	}

    /**
     * Add new tag
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses  Route::url
     * @uses  Route::get
     * @uses  Request::redirect
     * @uses  Message::success
     */
	public function action_add()
	{
		$this->title = __('Add New Tag');
        $post = ORM::factory('Tag');
        $action = Route::get('admin/tag')->uri(['action' => 'add']);

		if ($this->valid_post('tag'))
		{
            $post->values($_POST, ['name', 'type']);
			try
			{
				$post->save();
                Message::success(__('Tag %name saved successful!', ['%name' => $post->name]));
				$this->request->redirect(Route::get('admin/tag')->uri(), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$view = View::factory('admin/tag/form')
				->set('post',   $post)
				->set('action', $action)
				->set('errors', $this->_errors)
				->set('path', 	FALSE);
		
		$this->response->body($view);
	}

    /**
     * Edit tag
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses  Message::error
     * @uses  Route::url
     * @uses  Route::get
     * @uses  Request::redirect
     * @uses  Log::add
     * @uses  Message::success
     */
	public function action_edit()
	{
		$id   = (int) $this->request->param('id', 0);
        $post = ORM::factory('Tag', $id);

		if ( ! $post->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent tag.');
			Message::error(__("Tag doesn't exists!"));

			$this->request->redirect(Route::get('admin/tag')->uri(), 404);
		}

        $this->title = __('Edit Tag %name', ['%name' => $post->name]);

		if ($this->valid_post('tag'))
		{
            $post->values($_POST, ['name', 'type']);
			try
			{
				$post->save();

                Kohana::$log->add(Log::INFO, 'Tag :name saved successful.', [':name' => $post->name]);
                Message::success(__('Tag %name saved successful!', ['%name' => $post->name]));

				$this->request->redirect(Route::get('admin/tag')->uri(), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$view = View::factory('admin/tag/form')
					->set('post',    $post)
					->set('action',  $post->edit_url)
					->set('errors',  $this->_errors)
					->set('path', 	 $post->url);
		
		$this->response->body($view);
	}

    /**
     * Delete tag
     *
     * @throws Kohana_Exception
     * @uses  Message::success
     * @uses  Route::url
     * @uses  Route::get
     * @uses  Request::redirect
     * @uses  Message::error
     */
	public function action_delete()
	{
		$id  = (int) $this->request->param('id', 0);
        $tag = ORM::factory('Tag', $id);

		if ( ! $tag->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent tag.');
			Message::error(__("Tag doesn't exists!"));

			$this->request->redirect(Route::get('admin/tag')->uri(), 404);
		}

        $this->title = __('Delete Tag %title', ['%title' => $tag->name]);

		$view = View::factory('form/confirm')
				->set('action', $tag->delete_url)
				->set('title',  $tag->name);

		// If deletion is not desired, redirect to list
        if (isset($_POST['no']) && $this->valid_post())
		{
			$this->request->redirect(Route::get('admin/tag')->uri());
		}

		// If deletion is confirmed
        if (isset($_POST['yes']) && $this->valid_post())
		{
			try
			{
				$tag->delete();
                Message::success(__('Tag %name deleted successful!', ['%name' => $tag->name]));
				$this->request->redirect(Route::get('admin/tag')->uri(), 200);
			}
			catch (Exception $e)
			{
                Kohana::$log->add(Log::ERROR, 'Error occurred deleting tag id: :id, :msg', [
                    ':id' => $tag->id,
                    ':msg' => $e->getMessage()
                ]);
                Message::error(__('An error occurred while deleting tag %tag', ['%tag' => $tag->name]));
                $this->_errors = [__('An error occurred while deleting tag %tag', ['%tag' => $tag->name])];

				$this->request->redirect(Route::get('admin/tag')->uri(), 503);
			}
		}

		$this->response->body($view);
	}

}
