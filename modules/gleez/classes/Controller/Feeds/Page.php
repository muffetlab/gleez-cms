<?php
/**
 * Page Feed Controller
 *
 * @package    Gleez\Controller\Feed
 * @author     Sandeep Sangamreddi - Gleez
 * @author     Sergey Yakovlev - Gleez
 * @version    1.1.0
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Feeds_Page extends Controller_Feeds_Base {

	/**
	 * The before() method is called before controller action
	 *
	 * Setting the type for tags, categories, etc.
	 */
	public function before()
	{
		parent::before();

		$this->_type = 'page';
	}

    /**
     * Get list of pages.
     *
     * @throws Kohana_Exception
     * @uses  Config_Group::get
     * @uses  URL::site
     * @uses  Cache::set
     * @uses  Config::load
     */
	public function action_list()
	{
		if (empty($this->_items))
		{
			$config = Kohana::$config->load('page');

			// Cache is Empty so Re-Cache
            $pages = ORM::factory('Page')
						->where('status', '=', 'publish')
						->order_by('pubdate', 'DESC')
						->limit($this->_limit)
						->offset($this->_offset)
						->find_all();

            $items = $this->postsToItems($pages, $config);

			$this->_cache->set($this->_cache_key, $items, $this->_ttl);
			$this->_items = $items;
		}

		if (isset($this->_items[0]))
		{
			$this->_info['title']   = __('Pages - Recent updates');
			$this->_info['link']    = Route::url('rss', array('controller' => 'page'), TRUE);
			$this->_info['pubDate'] = $this->_items[0]['pubDate'];
		}
	}

	/**
	 * Get a list of pages with a specific term
	 *
	 * @since  1.1.0
	 *
	 * @uses  Controller_Feed_Base::_term
	 */
	public function action_term()
	{
		parent::_term();
	}

	/**
	 * Get a list of posts with a specific tag
	 *
	 * @since  1.1.0
	 *
	 * @uses  Controller_Feed_Base::_tag
	 */
	public function action_tag()
	{
		parent::_tag();
	}
}