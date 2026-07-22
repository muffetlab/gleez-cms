<?php

/**
 * Admin Menu Controller
 *
 * @package    Gleez\Controller\Admin
 * @author     Gleez Team
 * @version    1.0.1
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Admin_Menu extends Controller_Admin {

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
		ACL::required('administer menu');

		parent::before();
	}

    /**
     * List menus
     *
     * @throws Kohana_Exception
     * @uses  ORM::reset
     * @uses  ORM::dataTables
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  Route::url
     * @uses  Assets::popup
     * @uses  Request::is_datatables
     * @uses  HTML::chars
     * @uses  HTML::icon
     */
	public function action_list()
	{
		Assets::popup();

		$is_datatables = Request::is_datatables();
        $menus = ORM::factory('Menu')->where('lft', '=', 1);

		if ($is_datatables)
		{
            $this->_datatables = $menus->dataTables(['title', 'descp']);

			foreach ($this->_datatables->result() as $menu)
			{
                $this->_datatables->add_row([
                    HTML::chars($menu->title) . '<div class="description">' . HTML::chars($menu->descp) . '</div>',
                    HTML::icon($menu->list_items_url, 'fas fa-th-list', [
                        'class' => 'action-list',
                        'title' => __('List Links')
                    ]),
                    HTML::icon($menu->add_item_url, 'fas fa-plus', [
                        'class' => 'action-add',
                        'title' => __('Add Link')
                    ]),
                    HTML::icon($menu->edit_url, 'far fa-edit', [
                        'class' => 'action-edit',
                        'title' => __('Edit Menu')
                    ]),
                    HTML::icon($menu->delete_url, 'fas fa-trash-can', [
                        'class' => 'action-delete',
                        'title' => __('Delete Menu'),
                        'data-toggle' => 'popup',
                        'data-table' => '#admin-list-menus'
                    ])
                ]);
			}
		}

		$this->title = __('Menus');
        $add_url = Route::get('admin/menu')->uri(['action' => 'add']);
        $url = Route::url('admin/menu', ['action' => 'list'], TRUE);

		$view = View::factory('admin/menu/list')
				->bind('datatables',   $this->_datatables)
				->set('is_datatables', $is_datatables)
				->set('add_url',       $add_url)
				->set('url',           $url);

		$this->response->body($view);
	}

    /**
     * Add menu
     *
     * @throws Kohana_Exception
     * @uses  Request::redirect
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  DB::insert
     * @uses  ORM::save
     * @uses  ORM::make_root
     * @uses  Message::success
     * @uses  Cache::delete
     * @uses  Message::success
     */
	public function action_add()
	{
        $post = ORM::factory('Menu');
        $action = Route::get('admin/menu')->uri(['action' => 'add']);

		if ($this->valid_post('menu'))
		{
            $post->values($_POST, ['title', 'descp']);
			try
			{
				$post->make_root();
                DB::insert('widgets', ['name', 'title', 'module'])
                    ->values(['menu/' . $post->name, $post->title, 'gleez'])
                    ->execute();

                Message::success(__('Menu %name created successful!', ['%name' => $post->title]));
                Cache::instance()->delete('menus:' . $post->name);

				// Redirect to listing
				$this->request->redirect(Route::get('admin/menu')->uri(), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}
		$this->title = __('Creating a Menu');

		$view = View::factory('admin/menu/form')
				->bind('post', $post)
				->bind('action', $action)
				->bind('errors', $this->_errors);

		$this->response->body($view);
	}

    /**
     * Edit menu
     *
     * @throws Kohana_Exception|ReflectionException
     * @uses  Message::success
     * @uses  Log::add
     * @uses  Request::redirect
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  Cache::delete
     * @uses  ORM::save
     * @uses  Message::error
     */
	public function action_edit()
	{
		$id = (int) $this->request->param('id', 0);
        $post = ORM::factory('Menu', $id);

		if ( ! $post->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent Menu.');
            Message::error(__("Menu doesn't exists!"));

			// Redirect to listing
			$this->request->redirect(Route::get('admin/menu')->uri(), 404);
		}

        $this->title = __('Edit %name menu', ['%name' => $post->title]);
        $action = Route::get('admin/menu')->uri(['action' => 'edit', 'id' => $id]);

		if ($this->valid_post('menu'))
		{
            $post->values($_POST, ['title', 'descp']);
			try
			{
				$post->save();
                Message::success(__('Menu %name saved successful!', ['%name' => $post->title]));
                Cache::instance()->delete('menus:' . $post->name);

				// Redirect to listing
				$this->request->redirect(Route::get('admin/menu')->uri(), 200);
			}
			catch (ORM_Validation_Exception $e)
			{
                $this->_errors = $e->errors('models');
			}
		}

		$view = View::factory('admin/menu/form')
					->bind('post',    $post)
					->bind('action',  $action)
					->bind('errors',  $this->_errors);

		$this->response->body($view);
	}

    /**
     * Delete menu
     *
     * @throws Kohana_Exception
     * @uses  Message::error
     * @uses  Message::success
     * @uses  Request::redirect
     * @uses  Request::uri
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  Route::url
     * @uses  Cache::delete
     * @uses  ORM::delete
     * @uses  DB::delete
     * @uses  Log::add
     */
	public function action_delete()
	{
		$id = (int) $this->request->param('id', 0);
        $menu = ORM::factory('Menu', $id);

		if ( ! $menu->loaded())
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to access non-existent menu.');
			Message::error(__("Menu doesn't exists!"));

			// Redirect to listing
			$this->request->redirect(Route::get('admin/menu')->uri(), 404);
		}
		// If it is an external request and id == 2
		elseif ($menu->id == 2)
		{
			Kohana::$log->add(Log::ERROR, 'Attempt to delete system menu.');
			Message::error(__("You can't delete system menu!"));

			// Redirect to listing
			$this->request->redirect(Route::get('admin/menu')->uri(), 403);
		}

        $this->title = __('Delete Menu :title', [':title' => $menu->title]);

		$view = View::factory('form/confirm')
			->set('action', $menu->delete_url)
			->set('title',  $menu->title);


		// If deletion is not desired, redirect to list
        if (isset($_POST['no']) && $this->valid_post())
		{
			$this->request->redirect(Route::get('admin/menu')->uri());
		}

		// If deletion is confirmed
        if (isset($_POST['yes']) && $this->valid_post())
		{
            // If it is an internal request (e.g., popup dialog) and id < 3
			if ($menu->id == 2)
			{
				Kohana::$log->add(Log::ERROR, 'Attempt to delete system menu.');
                $this->_errors = [__("You can't delete system menu!")];
			}
			else
			{
				try
				{
					$name = $menu->title;
					DB::delete('widgets')->where('name', '=', 'menu/'.$menu->name)->execute();
                    Cache::instance()->delete('menus:' . $menu->name);

					$menu->delete();
                    Message::success(__('Menu %name deleted successful!', ['%name' => $name]));
				}
				catch (Exception $e)
				{
                    Kohana::$log->add(Log::ERROR, 'Error occurred deleting menu :term, id: :id, :msg', [
                        ':id' => $menu->id,
                        ':term' => $menu->name,
                        ':msg' => $e->getMessage()
                    ]);
                    $this->_errors = [__('An error occurred deleting menu %menu: :message', [
                        '%menu' => $menu->name,
                        ':message' => $e->getMessage()
                    ])];
				}
			}

			$this->request->redirect(Route::get('admin/menu')->uri());
		}

		$this->response->body($view);
	}

}
