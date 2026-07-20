<?php

/**
 * Admin Page Controller
 *
 * @package    Gleez\Controller\Admin
 * @author     Gleez Team
 * @version    1.0.1
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Admin_Page extends Controller_Admin {

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
		ACL::required('administer page');

		parent::before();
	}

    /**
     * The after() method is called after controller action
     *
     * @throws Kohana_Exception
     * @uses  Route::uri
     * @uses  Route::get
     */
	public function after()
	{
		// Tabs
        $this->_tabs = [
            ['link' => Route::get('admin/page')->uri(['action' => 'index']), 'text' => __('Statistics')],
            ['link' => Route::get('admin/page')->uri(['action' => 'list']), 'text' => __('List')],
            ['link' => Route::get('admin/page')->uri(['action' => 'settings']), 'text' => __('Settings')],
        ];

		parent::after();
	}

    /**
     * Page management dashboard
     *
     * Displays Page statistics
     * @throws View_Exception|Kohana_Exception
     */
	public function action_index()
	{
		$this->title = __('Page Statistics');

		$view = View::factory('admin/page/stats')
				->bind('stats', $stats);

        $categories = ORM::factory('Term')->where('type', '=', 'page')->find_all();
        $tags = ORM::factory('Tag')->where('type', '=', 'page')->find_all();
        $articles = ORM::factory('Page')->where('type', '=', 'page')->find_all();
        $comments = ORM::factory('Comment')->where('type', '=', 'page')->find_all();

        $stats = [];
		$stats['categories']['total'] = count($categories);
		$stats['tags']['total']       = count($tags);
		$stats['articles']['total']   = count($articles);
		$stats['comments']['total']   = count($comments);

		$this->response->body($view);
	}

    /**
     * Setting the display of pages
     *
     * @throws Kohana_Exception
     * @uses  Config::load
     * @uses  Message::success
     * @uses  Arr::merge
     */
	public function action_settings()
	{
		$this->title = __('Page Settings');

        $config = Kohana::$config->load('page');
        $action = Route::get('admin/page')->uri(['action' => 'settings']);

		$view = View::factory('admin/page/settings')
                ->set('config', $config)
					->set('action',  $action);

		if ($this->valid_post('page_settings'))
		{
			unset($_POST['page_settings'], $_POST['_token'], $_POST['_action']);

            $cats = $config->get('category', []);

			foreach ($_POST as $key => $value)
			{
				if ($key == 'category')
				{
					$terms = array_diff($cats, $value);
					if ($terms)
					{
						DB::delete('posts_terms')
							->where('parent_id', 'IN', array_values($terms))
							->execute();
					}
				}
                $config->set($key, $value);
			}

			Kohana::$log->add(Log::INFO, 'Page Settings updated.');
			Message::success(__('Page Settings updated!'));

            $this->request->redirect(Route::get('admin/page')->uri(['action' => 'settings']), 200);
		}

		$this->response->body($view);
	}

    /**
     * Displays list of pages
     *
     * @throws Kohana_Exception
     * @throws Exception
     * @uses  Assets::popup
     */
	public function action_list()
	{
		Assets::popup();

        $url = Route::url('admin/page', ['action' => 'list'], TRUE);
        $redirect = Route::get('admin/page')->uri(['action' => 'list']);
        $form_action = Route::get('admin/page')->uri(['action' => 'bulk']);
		$destination = '?destination='.$redirect;
		
		$is_datatables = Request::is_datatables();
        $pages = ORM::factory('Page');

		if ($is_datatables)
		{
            $this->_datatables = $pages->dataTables(['id', 'title', 'author', 'status', 'updated']);

			foreach ($this->_datatables->result() as $page)
			{
                $this->_datatables->add_row([
						Form::checkbox('posts['.$page->id.']', $page->id, isset($_POST['posts'][$page->id])),
						HTML::anchor($page->url, $page->title),
						HTML::anchor($page->user->url, $page->user->nick),
						HTML::label(__($page->status), $page->status),
						Date::formatted_time($page->updated, 'M d, Y'),
                    HTML::icon($page->edit_url . $destination, 'fa far fa-edit', [
                        'class' => 'btn btn-sm btn-default action-edit',
                        'title' => __('Edit Page')
                    ])
                    . '&nbsp;'
                    . HTML::icon($page->delete_url . $destination, 'fa fas fa-trash-can', [
                        'class' => 'btn btn-sm btn-default action-delete',
                        'title' => __('Delete Page'),
                        'data-toggle' => 'popup',
                        'data-table' => '#admin-list-pages'
                    ])
                ]);
			}
		}

		$this->title = __('Page List');
		
		$view = View::factory('admin/page/list')
				->bind('datatables',   $this->_datatables)
				->set('is_datatables', $is_datatables)
				->set('action',        $form_action)
				->set('actions',       Post::bulk_actions(TRUE, 'page'))
				->set('url',           $url);

		$this->response->body($view);
	}

    /**
     * Perform bulk actions
     *
     * @throws Kohana_Exception
     * @uses  Route::uri
     * @uses  Request::redirect
     * @uses  Post::bulk_delete
     * @uses  Message::success
     * @uses  Message::error
     * @uses  DB::select
     * @uses  Route::get
     */
	public function action_bulk()
	{
        $redirect = Route::get('admin/page')->uri(['action' => 'list']);

		$this->title = __('Bulk Actions');
		$post = $this->request->post();

		// If deletion is not desired, redirect to list
        if (isset($post['no']) && $this->valid_post())
		{
			$this->request->redirect($redirect);
		}

		// If deletion is confirmed
        if (isset($post['yes']) && $this->valid_post())
		{
			$pages = array_filter($post['items']);

			Post::bulk_delete($pages, 'page');

			Message::success(__('The delete has been performed!'));

			$this->request->redirect($redirect);
		}

		if ($this->valid_post('page-bulk-actions'))
		{
            if (isset($post['operation']) && empty($post['operation']))
			{
				Message::error(__('No bulk operation selected.'));
				$this->request->redirect($redirect);
			}

            if (!isset($post['posts']) || !is_array($post['posts']) || !count(array_filter($post['posts'])))
			{
				Message::error(__('No pages selected.'));
				$this->request->redirect($redirect);
			}

			try
			{
				if ($post['operation'] == 'delete')
				{
					$pages = array_filter($post['posts']); // Filter out unchecked posts
					$this->title = __('Delete Pages');

					$items = DB::select('id', 'title')
							->from('posts')
							->where('id', 'IN', $pages)
							->execute()
							->as_array('id', 'title');

					$view = View::factory('form/confirm_multi')
							->set('action', '')
							->set('items', $items);

					$this->response->body($view);
					return;
				}

				$this->_bulk_update($post);

				Message::success(__('The update has been performed!'));
				$this->request->redirect($redirect);
			}
			catch( Exception $e)
			{
				Message::error(__('The update has not been performed!'));
			}
		}
		
		// always redirect to list, if no action performed
		$this->request->redirect($redirect);
	}
	
	/**
	 * Bulk updates
	 *
     * @param array $post
	 * @uses   Post::bulk_actions
	 * @uses   Arr::callback
	 */
    private function _bulk_update(array $post)
	{
        $operations = Post::bulk_actions();
		$operation  = $operations[$post['operation']];
		$pages = array_filter($post['posts']); // Filter out unchecked pages

		if ($operation['callback'])
		{
            list($func) = Arr::callback($operation['callback']);
			if (isset($operation['arguments']))
			{
                $args = array_merge([$pages], $operation['arguments']);
			}
			else
			{
                $args = [$pages];
			}

			// set model name
			$args['type'] = 'page';

			// execute the bulk operation
			call_user_func_array($func, $args);
		}
	}

}
