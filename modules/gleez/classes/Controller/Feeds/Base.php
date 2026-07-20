<?php
/**
 * Base Feed Controller
 *
 * @package    Gleez\Controller\Feed
 * @author     Gleez Team
 * @version    1.1.2
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Feeds_Base extends Controller_Feeds_Template {

    /**
     * Get list of promoted posts
     *
     * @throws Kohana_Exception
     * @uses  URL::site
     * @uses  Cache::set
     * @uses  Config::load
     * @uses  Config_Group::get
     * @uses  DB::select
     */
	public function action_list()
	{
		if (empty($this->_items))
		{
			$config = Kohana::$config->load('page');

			// Cache is Empty so Re-Cache
            $posts = ORM::factory('Post')
						->where('status', '=', 'publish')
						->where('promote', '=', 1)
						->order_by('pubdate', 'DESC')
						->limit($this->_limit)
						->offset($this->_offset)
						->find_all();

            $items = $this->postsToItems($posts, $config);

			$this->_cache->set($this->_cache_key, $items, $this->_ttl);
			$this->_items = $items;
		}

		if (isset($this->_items[0]))
		{
			$this->_info['pubDate'] = $this->_items[0]['pubDate'];
		}
	}

    /**
     * Get a list of posts (pages|blogs|etc.) with a specific tag
     *
     * @throws HTTP_Exception
     * @throws Kohana_Exception
     * @since  1.1.0
     * @uses    Config::load
     * @uses    Config_Group::get
     * @uses    Log::add
     * @uses    URL::site
     * @uses    Cache::set
     */
	protected function _tag()
	{
		if (empty($this->_items))
		{
			$config = Kohana::$config->load($this->_type);

			$id  = $this->request->param('id', 0);
            $tag = ORM::factory('Tag', ['id' => $id, 'type' => $this->_type]);

			if ( ! $tag->loaded())
			{
                throw HTTP_Exception::factory(404, 'Tag ":tag" Not Found', [':tag' => $id]);
			}

			$posts = $tag->posts
					->where('status', '=', 'publish')
					->order_by('pubdate', 'DESC')
					->limit($this->_limit)
					->offset($this->_offset)
					->find_all();

            $items = $this->postsToItems($posts, $config);

			$items['title'] = $tag->name;
			$this->_items   = $items;

			$this->_cache->set($this->_cache_key, $this->_items, $this->_ttl);
		}

		if (isset($this->_items[0]))
		{
            $this->_info['title'] = __(':tag - Recent updates', [':tag' => ucfirst($this->_items['title'])]);
            $this->_info['link'] = Route::url('rss', [
                'controller' => $this->_type,
                'action' => 'tag',
                'id' => (int) $this->request->param('id')
            ], TRUE);
			$this->_info['pubDate'] = $this->_items[0]['pubDate'];
		}
	}

    /**
     * Convert posts to feed items.
     *
     * @param Database_Result $posts Collection of posts
     * @param Config_Group $config Configuration object
     * @return array Feed items
     * @throws Kohana_Exception
     */
    protected function postsToItems(Database_Result $posts, Config_Group $config): array
    {
        $items = [];

        foreach ($posts as $post) {
            $item = [];
            $item['guid'] = $post->id;
            $item['title'] = $post->title;
            $item['link'] = URL::site($post->url, true);
            if ($config->get('use_submitted', false)) {
                $item['author'] = $post->user->nick;
            }
            $item['description'] = $post->teaser;
            $item['pubDate'] = $post->pubdate;

            $items[] = $item;
        }

        return $items;
    }

    /**
     * Get a list of posts (pages|blogs|etc.) with a specific term
     *
     * @throws HTTP_Exception
     * @throws Kohana_Exception
     * @since   1.1.0
     * @uses    Config::load
     * @uses    Config_Group::get
     * @uses    Cache::set
     * @uses    Log::add
     * @uses    URL::site
     */
	protected function _term()
	{
		if (empty($this->_items))
		{
			$config = Kohana::$config->load($this->_type);

			$id   = $this->request->param('id', 0);
            $term = ORM::factory('Term')
						->where('id', '=', $id)
						->where('type', '=', $this->_type)
						->where('lvl', '!=', 1)
						->find();

			if ( ! $term->loaded())
			{
                throw HTTP_Exception::factory(404, 'Term ":term" Not Found', [':term' => $id]);
			}

			$posts = $term->posts
					->where('status', '=', 'publish')
					->order_by('pubdate', 'DESC')
					->limit($this->_limit)
					->offset($this->_offset)
					->find_all();

            $items = $this->postsToItems($posts, $config);

			$items['title'] = $term->name;
			$this->_items   = $items;

			$this->_cache->set($this->_cache_key, $this->_items, $this->_ttl);
		}

		if (isset($this->_items[0]))
		{
            $this->_info['title'] = __(':term - Recent updates', [':term' => ucfirst($this->_items['title'])]);
            $this->_info['link'] = Route::url('rss', [
                'controller' => $this->_type,
                'action' => 'term',
                'id' => (int) $this->request->param('id')
            ], TRUE);
			$this->_info['pubDate'] = $this->_items[0]['pubDate'];
		}
	}

}