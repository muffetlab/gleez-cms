<?php
/**
 * Admin Modules Controller
 *
 * @package    Gleez\Controller\Admin
 * @author     Gleez Team
 * @version    1.0.3
 * @copyright  (c) 2011-2014 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Controller_Admin_Modules extends Controller_Admin {

    /**
     * Module list.
     *
     * @throws Kohana_Exception
     * @uses  Module::load_modules
     * @uses  Module::available
     * @uses  Route::uri
     * @uses  Route::get
     * @uses  Cache::delete
     */
	public function action_list()
	{
        if ($this->valid_post('modules')) {
            $messages = ['error' => [], 'warn' => []];

            foreach (Module::available() as $moduleName => $info) {
                if ($info->locked) {
                    continue;
                }

                $desired = Arr::get($this->request->post(), $moduleName) === '1';

                if ($info->active && !$desired && Module::is_active($moduleName)) {
                    $messages = Arr::merge($messages, Module::can_deactivate($moduleName));
                } elseif (!$info->active && $desired && !Module::is_active($moduleName)) {
                    $messages = Arr::merge($messages, Module::can_activate($moduleName));
                }
            }

            // Clear any cache for sure
            Cache::instance()->delete_all();

            if (!empty($messages['error'])) {
                Message::error($messages['error']);
            } elseif (!empty($messages['warn'])) {
                Message::warn($messages['warn']);
            } else {
                $this->_do_save();

                $this->request->redirect(Route::get('admin/module')->uri(), 200);
            }
        }

		// Clear any cache for sure
		// Note: Gleez Caching only available in production
		Cache::instance()->delete('load_modules');

		// Load modules
        Module::load_modules();

		$view = View::factory('admin/module/list')
				->set('available', Module::available())
                ->set('action', Route::get('admin/module')->uri());

		$this->title = __('Modules');
		$this->response->body($view);
	}

	/**
	 * Do save
	 *
     * @throws Cache_Exception|Kohana_Exception
     * @uses  Arr::get
	 * @uses  Module::available
	 * @uses  Module::is_active
	 * @uses  Module::deactivate
	 * @uses  Module::is_installed
	 * @uses  Module::upgrade
	 * @uses  Module::install
	 * @uses  Module::activate
	 * @uses  Module::event
	 * @uses  Cache::delete_all
	 * @uses  Log::add
	 * @uses  Kohana_Exception::text
	 */
	private function _do_save()
	{
		$changes = new stdClass();
        $changes->activate = [];
        $changes->deactivate = [];
        $activated_names = [];
        $deactivated_names = [];

		foreach (Module::available() as $module_name => $info)
		{
			if ($info->locked)
			{
				continue;
			}

			try
			{
				$desired = Arr::get($_POST, $module_name) == 1;

				if ($info->active AND ! $desired AND Module::is_active($module_name))
				{
					Module::deactivate($module_name);
					$changes->deactivate[] = $module_name;
					$deactivated_names[] = __($info->name);
				}
				elseif ( ! $info->active AND $desired AND ! Module::is_active($module_name))
				{
					if (Module::is_installed($module_name))
					{
						Module::upgrade($module_name);
					}
					else
					{
						Module::install($module_name);
					}

					Module::activate($module_name);

					$changes->activate[] = $module_name;
					$activated_names[] = __($info->name);
				}
			}
			catch (Exception $e)
			{
				Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e));
			}
		}

		Module::event('module_change', $changes);

		// @todo This type of collation is questionable from an i18n perspective
		if ($activated_names)
		{
			Message::success(__('Activated: %names', array('%names' => join(", ", $activated_names))));
		}
		if ($deactivated_names)
		{
			Message::success(__('Deactivated: %names', array('%names' => join(", ", $deactivated_names))));
		}

		// Clear any cache for sure
		Cache::instance()->delete_all();
	}

}
