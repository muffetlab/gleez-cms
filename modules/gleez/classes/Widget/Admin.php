<?php
/**
 * Admin Widget class
 *
 * @package    Gleez\Widget
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Widget_Admin extends Widget {

	public function info() {}
	public function form() {}
	public function save(array $post) {}
	public function delete(array $post) {}

    /**
     * @throws Kohana_Exception
     * @throws View_Exception
     */
    public function render(): string
    {
		switch($this->name)
		{
			case 'donate':
				return $this->donate();
            case 'welcome':
				return $this->welcome();
            case 'info':
				return $this->system_info();
            case 'shortcut':
				return $this->shortcut();
            default:
                return '';
			}
	}

    /**
     * @throws Kohana_Exception
     * @throws View_Exception
     */
    public function shortcut(): string
    {
		$menus = Menu::items('management')->get_items();
		unset($menus['administer']);

        return View::factory('widgets/shortcuts')
            ->set(['items' => $menus])
            ->render();
	}

    /**
     * @throws View_Exception
     */
    public function donate(): string
    {
        return View::factory('widgets/static')->set([
            'title' => __('Donate'),
            'content' => __('If you use Gleez, we ask that you donate to ensure future development is possible.')
        ])->render();
	}

    /**
     * @throws View_Exception
     */
    public function welcome(): string
    {
        return View::factory('widgets/welcome')->set([
            'title' => __('Welcome'),
        ])->render();
    }

    /**
     * @throws View_Exception
     * @throws Kohana_Exception
     */
    public function system_info(): string
    {
        $dbVersion = DB::query(Database::SELECT, 'SHOW VARIABLES WHERE variable_name = "version"')
            ->execute()
            ->get('Value');

        return View::factory('widgets/systeminfo')
            ->set('dbVersion', $dbVersion)
            ->render();
	}
}