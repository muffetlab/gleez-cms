<?php

/**
 * Blog Controller
 *
 * @package    Gleez\Controller
 * @author     Gleez Team
 * @version    1.0.4
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Blog extends Template {

    /**
     * The before() method is called before controller action
     *
     * @throws HTTP_Exception_403
     * @throws Http_Exception_415
     * @throws Kohana_Exception
     * @throws View_Exception
     * @uses  Request::action
     * @uses  ACL::required
     * @uses  Request::param
     */
	public function before()
	{
		$id = $this->request->param('id', FALSE);

        if ($id && $this->request->action() == 'index')
		{
			$this->request->action('view');
		}

        if (!$id && $this->request->action() == 'index')
		{
			$this->request->action('list');
		}

		ACL::required('access content');

		parent::before();
	}

    /**
     * The after() method is called after controller action
     *
     * @throws Kohana_Exception
     */
	public function after()
	{
        if ($this->request->action() == 'add' || $this->request->action() == 'edit')
		{
			// Add RichText Support
			Assets::editor('.textarea', I18n::$lang);

			// Flag to disable left/right sidebars
			$this->_sidebars = FALSE;
		}

		parent::after();
	}

    /**
     * List of blog posts
     *
     * @throws Kohana_Exception
     * @uses  ACL::check
     * @uses  ORM::reset
     * @uses  Log::add
     * @uses  Gleez_Config::load
     * @uses  Gleez_Config_Group::get
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  Meta::links
     * @uses  URL::canonical
     * @uses  URL::site
     */
	public function action_list()
	{
        $posts = ORM::factory('Blog');

		if ( ! ACL::check('administer blog'))
		{
			$posts->where('status', '=', 'publish');
		}

		$this->title = __('Blogs');
		$this->schemaType = 'WebPage';

		/**
		 * Bug in ORM to repeat the `where()` methods after using `count_all()`
		 * @link http://forum.kohanaframework.org/discussion/7736 Solved
		 */
		$total = $posts->reset(FALSE)->count_all();

		if ($total == 0)
		{
			Kohana::$log->add(Log::INFO, 'No blogs found.');
			$this->response->body(View::factory('blog/none'));
			return;
		}

		$config = Kohana::$config->load('blog');

		$view = View::factory('blog/list')
			->set('teaser',      TRUE)
			->set('config',      $config)
			->bind('rss_link',   $rss_link)
			->bind('pagination', $pagination)
			->bind('posts',      $posts);

		$url        = Route::get('blog')->uri();
        $rss_link = Route::get('rss')->uri(['controller' => 'blog']);
        $pagination = Pagination::factory([
            'current_page' => ['source' => 'cms', 'key' => 'page'],
            'total_items' => $total,
            'items_per_page' => $config->get('items_per_page', 15),
            'uri' => $url,
        ]);

		$posts = $posts->order_by('sticky', 'DESC')
			->order_by('created', 'DESC')
            ->limit($pagination->itemsPerPage())
            ->offset($pagination->offset())
			->find_all();

		$this->response->body($view);

		// Set the canonical and shortlink for search engines
		if ($this->auto_render)
		{
            Meta::links(URL::canonical($url, $pagination), ['rel' => 'canonical']);
            Meta::links(Route::url('blog', [], TRUE), ['rel' => 'shortlink']);
            Meta::links(URL::site('rss/blog', TRUE), [
                'rel' => 'alternate',
                'type' => 'application/rss+xml',
                'title' => Template::getSiteName() . ' : ' . __('Blogs'),
            ]);
		}
	}

    /**
     * Blog view post
     *
     * @throws Cache_Exception
     * @throws HTTP_Exception
     * @throws HTTP_Exception_403
     * @throws HTTP_Exception_404
     * @throws HTTP_Exception_503
     * @throws Kohana_Exception
     * @throws ReflectionException
     * @throws Request_Exception
     * @throws View_Exception
     * @uses    Gleez_Config::load
     * @uses    Post::dynamicCache
     * @uses    ACL::post
     * @uses    ACL::check
     * @uses    Auth::logged_in
     * @uses    Comment::form
     * @uses    User::providers
     * @uses    Meta::links
     * @uses    URL::canonical
     */
	public function action_view()
	{
		$id     = (int) $this->request->param('id', 0);
		$config = Kohana::$config->load('blog');

        $post = Post::dynamicCache($id, 'blog', $config);

		if ( ! ACL::post('view', $post))
		{
			// If the post was not loaded, we return access denied.
			throw HTTP_Exception::factory(403, 'Access denied!');
		}

		if (ACL::post('edit', $post))
		{
            $this->_tabs[] = ['link' => $post->url, 'text' => __('View')];
            $this->_tabs[] = ['link' => $post->edit_url, 'text' => __('Edit')];
		}

		if (ACL::post('delete', $post))
		{
            $this->_tabs[] = ['link' => $post->delete_url, 'text' => __('Delete')];
		}

        if (
            ($post->comment == Comment::COMMENT_OPEN || $post->comment == Comment::COMMENT_CLOSED)
            and ACL::check('access comment')
        )
		{
			// Determine pagination offset
			$p = ((int) $this->request->param('page', 0)) ? '/p'.$this->request->param('page', 0) : FALSE;

			// Handle comment listing
			$comments = Request::factory('comments/blog/public/'.$id.$p)->execute()->body();
		}

        if ($post->comment == Comment::COMMENT_OPEN && ACL::check('post comment'))
		{
            if ($this->_auth->logged_in() || $config->comment_anonymous && !$this->_auth->logged_in())
			{
				// Handle comment posting
				$comment_form = Comment::form($this, $post);
			}

		}

		// show site and other provider login buttons
        if ($post->comment == Comment::COMMENT_OPEN && $config->use_provider_buttons)
		{
			$provider_buttons = User::providers();
		}

		$this->title = $post->title;
		$this->schemaType = 'Article';

		$view = View::factory('blog/post')
			->set('title',             $this->title)
			->set('blog',              $post->content)
			->bind('comments',         $comments)
			->bind('comment_form',     $comment_form)
			->bind('provider_buttons', $provider_buttons);

		$this->response->body($view);

		// Set the canonical and shortlink for search engines
		if ($this->auto_render)
		{
            Meta::links(URL::canonical($post->url), ['rel' => 'canonical']);
            Meta::links($post->rawurl, ['rel' => 'shortlink']);
		}
	}

    /**
     * Creates blog post
     *
     * @throws HTTP_Exception_403
     * @throws Kohana_Exception
     * @throws ReflectionException
     * @throws View_Exception
     * @uses  ACL::required
     * @uses  Config::load
     * @uses  Config_Group::get
     * @uses  Request::query
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  URL::query
     * @uses  ORM::select_list
     * @uses  Log::add
     * @uses  Message::success
     */
	public function action_add()
	{
		ACL::required('create blog');

		$this->title = __('Add Blog');
		$config = Kohana::$config->load('blog');

		// Set form destination
        $destination = !is_null($this->request->query('destination'))
            ? ['destination' => $this->request->query('destination')]
            : [];
		// Set form action
        $action = Route::get('blog')->uri(['action' => 'add']) . URL::query($destination);

		$view = View::factory('blog/form')
			->set('destination', $destination)
			->set('action',      $action)
			->set('config',      $config)
			->set('created',     FALSE)
			->set('author',      FALSE)
			->set('path',        FALSE)
            ->set('tags', $_POST['form_tags'] ?? FALSE)
			->set('image',        FALSE)
			->bind('errors',     $this->_errors)
			->bind('terms',      $terms)
			->bind('blog',       $post);


        $post = ORM::factory('Blog');
		$post->status = $config->get('default_status', 'draft');

		if ($config->get('use_category', FALSE))
		{
            $terms = ORM::factory('Term', ['type' => 'blog', 'lvl' => 1])->select_list('id', 'name', '--');
		}

		if ($config->get('use_captcha', FALSE))
		{
			$captcha = Captcha::instance();
			$view->set('captcha', $captcha);
		}

		if ($this->valid_post('blog'))
		{
			try
			{
                $post->values($_POST, ['title', 'body', 'format', 'status', 'sticky', 'promote', 'comment'])->save();

                Kohana::$log->add(Log::INFO, 'Blog :title created.', [':title' => $post->title]);
                Message::success(__('Blog %title created', ['%title' => $post->title]));

				$this->request->redirect($post->url);
			}
			catch (ORM_Validation_Exception $e)
			{
				// @todo Added messages
                $this->_errors = $e->errors('models');
			}
		}

		$this->response->body($view);
	}

    /**
     * Edit blog post
     *
     * @throws HTTP_Exception
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     * @throws ReflectionException
     * @throws View_Exception
     * @uses    ACL::post
     * @uses    Gleez_Config::load
     * @uses    Request::query
     * @uses    Request::redirect
     * @uses    Route::get
     * @uses    Route::uri
     * @uses    URL::query
     * @uses    Tags::implode
     * @uses    Date::date_time
     * @uses    Path::load
     * @uses    Message::success
     * @uses    Log::add
     */
	public function action_edit()
	{
		$id = (int) $this->request->param('id', 0);
        $post = ORM::factory('Blog', $id);

		if ( ! ACL::post('edit', $post))
		{
			// If the post was not loaded, we return access denied.
			throw HTTP_Exception::factory(403, 'Access denied!');
		}

		$this->title = $post->title;
		$config = Kohana::$config->load('blog');

		// Set form destination
        $destination = !is_null($this->request->query('destination'))
            ? ['destination' => $this->request->query('destination')]
            : [];
		// Set form action
        $action = Route::get('blog')->uri(['id' => $id, 'action' => 'edit']) . URL::query($destination);

		$view = View::factory('blog/form')
			->set('destination',  $destination)
			->set('action',       $action)
			->set('config',       $config)
			->set('path',         FALSE)
			->set('created',      $post->created)
			->set('author',       $post->user->name)
			->set('tags',         Tags::implode($post->tags_form))
			->set('image',        FALSE)
			->bind('errors',      $this->_errors)
			->bind('terms',       $terms)
			->bind('blog',        $post);

		if ($config->get('use_captcha', FALSE))
		{
			$captcha = Captcha::instance();
			$view->set('captcha', $captcha);
		}

		if ($path = Path::load($post->rawurl))
		{
			$view->set('path', $path['alias']);
		}

		if ($config->get('use_category', FALSE))
		{
            $terms = ORM::factory('Term', ['type' => 'blog', 'lvl' => 1])
				->select_list('id', 'name', '--');
		}

		if($this->valid_post('blog'))
		{
			try
			{
                $post->values($_POST, ['title', 'body', 'format', 'status', 'sticky', 'promote', 'comment'])->save();

                Kohana::$log->add(Log::INFO, 'Blog :title updated.', [':title' => $post->title]);
                Message::success(__('Blog %title updated', ['%title' => $post->title]));

				$this->request->redirect(empty($destination) ? $post->url : $this->request->query('destination'));
			}
			catch (ORM_Validation_Exception $e)
			{
				// @todo Add messages
                $this->_errors = $e->errors('models');
			}
		}

        $this->_tabs = [
            ['link' => $post->url, 'text' => __('View')],
            ['link' => $post->edit_url, 'text' => __('Edit')],
        ];

		if (ACL::post('delete', $post))
		{
            $this->_tabs[] = ['link' => $post->delete_url, 'text' => __('Delete')];
		}

		$this->response->body($view);
	}

    /**
     * Delete page
     *
     * @throws HTTP_Exception
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     * @throws View_Exception
     * @uses    ACL::post
     * @uses    Request::query
     * @uses    Request::redirect
     * @uses    Route::get
     * @uses    Route::uri
     * @uses    URL::query
     * @uses    ORM::delete
     * @uses    Cache::delete
     * @uses    Message::success
     * @uses    Message::error
     * @uses    Log::add
     */
	public function action_delete()
	{
		$id = (int) $this->request->param('id', 0);
        $post = ORM::factory('Blog', $id);

		if( ! ACL::post('delete', $post))
		{
			// If the post was not loaded, we return access denied.
			throw HTTP_Exception::factory(403, 'Access denied!');
		}

        $this->title = __('Delete :title', [':title' => $post->title]);

		$destination = ($this->request->query('destination') !== NULL) ?
            ['destination' => $this->request->query('destination')] : [];

        $view = View::factory('form/confirm')
            ->set('action', Route::get('blog')->uri([
                    'action' => 'delete',
                    'id' => $post->id
                ]) . URL::query($destination))
            ->set('title', $post->title);

		// If deletion is not desired, redirect to post
		if ($this->valid_post('no'))
		{
			$this->request->redirect($post->url);
		}

		// If deletion is confirmed
		if ($this->valid_post('yes'))
		{
			$title = $post->title;

			try
			{
				$post->delete();

                Cache::instance()->delete('blog:blog-' . $id);

                Kohana::$log->add(Log::INFO, 'Blog :title deleted.', [':title' => $title]);
                Message::success(__('Blog %title deleted successful!', ['%title' => $title]));
			}
			catch (Exception $e)
			{
                Kohana::$log->add(Log::ERROR, 'Error occurred deleting blog id: :id, :msg', [
                    ':id' => $post->id,
                    ':msg' => $e->getMessage()
                ]);
                Message::error(__('An error occurred deleting blog %post', ['%post' => $title]));
			}

            $redirect = empty($destination)
                ? Route::get('blog')->uri(['action' => 'list'])
                : $this->request->query('destination');

			$this->request->redirect($redirect);
		}

		$this->response->body($view);
	}

    /**
     * Category selector
     *
     * @throws  HTTP_Exception_403
     * @throws  HTTP_Exception_404
     * @throws Kohana_Exception
     */
	public function action_term()
	{
		$config = Kohana::$config->load('blog');

		if ( ! $config->use_category)
		{
			throw HTTP_Exception::factory(403, 'Attempt to access disabled feature.');
		}

		$id    = (int) $this->request->param('id', 0);
        $array = ['id' => $id, 'type' => 'blog'];
        $term = ORM::factory('Term', $array)->where('lvl', '!=', 1);

		if ( ! $term->loaded())
		{
            throw HTTP_Exception::factory(404, 'Category ":term" not found', [':term' => $id]);
		}

        $this->title = __(':term', [':term' => $term->name]);
		$view = View::factory('blog/list')
			->set('teaser',      TRUE)
			->set('config',      $config)
			->bind('rss_link',   $rss_link)
			->bind('pagination', $pagination)
			->bind('posts',       $posts);

		$posts = $term->posts;

        if (!ACL::check('administer terms') && !ACL::check('administer content'))
		{
			$posts->where('status', '=', 'publish');
		}

		$total = $posts->reset(FALSE)->count_all();

		if ($total == 0)
		{
			Kohana::$log->add(Log::INFO, 'No blogs found.');
			$this->response->body(View::factory('blog/none'));
			return;
		}

        $rss_link = Route::get('rss')->uri(['controller' => 'blog', 'action' => 'term', 'id' => $term->id]);
        $pagination = Pagination::factory([
            'current_page' => ['source' => 'cms', 'key' => 'page'],
            'total_items' => $total,
            'items_per_page' => $config->get('items_per_page', 15),
            'uri' => $term->url,
        ]);

		$posts = $posts->order_by('sticky', 'DESC')
			->order_by('created', 'DESC')
            ->limit($pagination->itemsPerPage())
            ->offset($pagination->offset())
			->find_all();

		$this->response->body($view);

		// Set the canonical and shortlink for search engines
		if ($this->auto_render)
		{
            Meta::links(URL::canonical($term->url, $pagination), ['rel' => 'canonical']);
            Meta::links(Route::url('blog', ['action' => 'term', 'id' => $term->id], TRUE), [
                'rel' => 'shortlink'
            ]);
            Meta::links(Route::url('rss', ['controller' => 'blog', 'action' => 'term', 'id' => $term->id], TRUE), [
                'rel' => 'alternate',
                'type' => 'application/rss+xml',
                'title' => Template::getSiteName() . ' : ' . $term->name,
            ]);
		}
	}

    /**
     * Tags view
     *
     * @throw HTTP_Exception_404
     * @throws Kohana_Exception
     */
	public function action_tag()
	{
		$config = Kohana::$config->load('blog');
		$id = (int) $this->request->param('id', 0);
        $tag = ORM::factory('Tag', ['id' => $id, 'type' => 'blog']);

		if ( ! $tag->loaded())
		{
            throw HTTP_Exception::factory(404, 'Tag ":tag" Not Found', [':tag' => $id]);
		}

        $this->title = __(':title', [':title' => Text::ucfirst($tag->name)]);
		$view = View::factory('blog/list')
			->set('teaser',      TRUE)
			->set('config',      $config)
			->bind('rss_link',   $rss_link)
			->bind('pagination', $pagination)
			->bind('posts',      $posts);

		$posts = $tag->posts;

        if (!ACL::check('administer tags') && !ACL::check('administer content'))
		{
			$posts->where('status', '=', 'publish');
		}

		$total = $posts->reset(FALSE)->count_all();

		if ($total == 0)
		{
			Kohana::$log->add(Log::INFO, 'No blogs found.');
			$this->response->body(View::factory('blog/none'));
			return;
		}

        $rss_link = Route::get('rss')->uri(['controller' => 'blog', 'action' => 'tag', 'id' => $tag->id]);
        $pagination = Pagination::factory([
            'current_page' => ['source' => 'cms', 'key' => 'page'],
            'total_items' => $total,
            'items_per_page' => $config->get('items_per_page', 15),
            'uri' => $tag->url,
        ]);

		$posts = $posts->order_by('created', 'DESC')
            ->limit($pagination->itemsPerPage())
            ->offset($pagination->offset())
			->find_all();

		$this->response->body($view);

		// Set the canonical and shortlink for search engines
		if ($this->auto_render)
		{
            Meta::links(URL::canonical($tag->url, $pagination), ['rel' => 'canonical']);
            Meta::links(Route::url('blog', ['action' => 'tag', 'id' => $tag->id], TRUE), [
                'rel' => 'shortlink'
            ]);
            Meta::links(Route::url('rss', ['controller' => 'blog', 'action' => 'tag', 'id' => $tag->id], TRUE), [
                'rel' => 'alternate',
                'type' => 'application/rss+xml',
                'title' => Template::getSiteName() . ' : ' . $tag->name,
            ]);
		}
	}
}
