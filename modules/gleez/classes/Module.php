<?php

/**
 * This is the API for handling modules
 *
 * @package    Gleez\Module
 * @author     Gleez Team
 * @version    1.1.0
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 *
 * @todo      [!!] This class does not do any permission checking
 */
class Module
{
	/**
	 * Array of modules
	 * @var array
	 */
    public static $modules = [];

	/**
	 * List of active modules
	 * @var array
	 */
    public static $active = [];

	/**
	 * List of available modules, including uninstalled modules
	 * @var array
	 */
    public static $available = [];

    /**
     * Set the version of the corresponding Module_Model
     *
     * @param string $name Module name
     * @param string $version Module version
     * @throws Kohana_Exception
     * @throws ORM_Validation_Exception
     * @throws ReflectionException
     */
    public static function set_version(string $name, string $version)
	{
		$module = self::get($name);

		if (!$module->loaded()) {
			$module->name = $name;

			// Only user is active by default
			$module->active = ($name == 'user');
		}

		$module->version = $version;
		$module->save();

		if (Kohana::$environment === Kohana::DEVELOPMENT) {
            Kohana::$log->add(Log::DEBUG, ':name : version is now :version', [
                ':name' => $name,
                ':version' => $version
            ]);
		}
	}

    /**
     * Load the corresponding Model_Module
     *
     * @param string $name Module name
     * @return  ORM
     * @throws Kohana_Exception
     */
    public static function get(string $name): ORM
    {
		if (empty(self::$modules[$name]) || !(self::$modules[$name] instanceof ORM)) {
            return ORM::factory('Module')->where('name', '=', $name)->find();
		}

		return self::$modules[$name];
	}

    /**
     * Get the information about a module
     *
     * @param string $name Module name
     * @return ArrayObject|null An ArrayObject containing the module information from the module.info file, or null if not found
     * @throws Kohana_Exception
     */
    public static function info(string $name): ?ArrayObject
    {
		$module_list = self::available();

        return $module_list->$name ?? null;
	}

	/**
	 * Check to see if a module is installed
	 *
     * @param string $name Module name
	 * @return  boolean
	 */
    public static function is_installed(string $name): bool
    {
		return array_key_exists($name, self::$modules);
	}

	/**
	 * Check to see if a module is active
	 *
     * @param string $name Module name
	 * @return  boolean
	 */
    public static function is_active(string $name): bool
    {
		return array_key_exists($name, self::$active);
	}

    /**
     * Return the list of available modules, including uninstalled modules
     *
     * @throws Kohana_Exception
     * @uses  HTML::anchor
     * @uses  Route::get
     * @uses  Route::uri
     * @uses  Message::warn
     */
	public static function available()
	{
		if (empty(self::$available))
		{
			$upgrade = false;
            $modules = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);
            $paths = (array) Kohana::$config->load('site')->get('module_paths', [MODPATH]);

			// Make sure MODPATH is set else add last
			if(!in_array(MODPATH, $paths)) {
                $paths[] = MODPATH;
			}

			// Iterate over each config path
			foreach ($paths as $path) {
				foreach (glob($path . "*/module.info") as $file) {
					$name           = basename(dirname($file));
					$modules->$name = new ArrayObject(parse_ini_file($file), ArrayObject::ARRAY_AS_PROPS);

					$m =& $modules->$name;
					$m->active       = self::is_active($name);
					$m->title 		 = isset($m->title) ? (string) $m->title : $name;
                    $m->code_version = (string) $m->version;
                    $m->version = self::get_version($name) ?: $m->code_version;
					$m->locked       = false;
                    $m->visible = !isset($m->visible) || $m->visible;
					$m->author    	 = isset($m->author)    ? (string) $m->author 	 : 'Gleez Team';
					$m->authorURL    = isset($m->authorURL) ? (string) $m->authorURL : 'https://gleezcms.org/';
					$m->path 		 = realpath( dirname($file) ).DIRECTORY_SEPARATOR;

					// Skip this module in list if the module is hidden
					if($m->visible === false && isset($modules[$name]))
					{
						unset($modules[$name]);
					}

					// Check installed and available version and set message
					if ($m->active && $m->version != $m->code_version)
					{
						$upgrade = true;
					}
				}
			}

			if ($upgrade) {
                Message::warn(__('Some of your modules are out of date. :upgrade_url', [
                    ':upgrade_url' => HTML::anchor(Route::get('admin/module')->uri([
                        'action' => 'upgrade'
                    ]), __('Upgrade now!'))
                ]));
			}

			// Lock certain modules
			$modules->user->locked = true;

			$modules->ksort();
			self::$available = $modules;
		}

		return self::$available;
	}

	/**
	 * Return a list of all the active modules in no particular order
	 *
	 * @return array
	 */
    public static function active(): array
    {
		return self::$active;
	}

    /**
     * Check that the module can be activated. (i.e. all the prerequisites exist)
     *
     * @param string $module_name Module name
     * @return array An array of warning or error messages to be displayed
     * @throws Kohana_Exception
     */
    public static function can_activate(string $module_name): array
    {
		self::_add_to_path($module_name);
        $messages = [];

		$installer_class = ucfirst($module_name).'_Installer';
        if (is_callable([$installer_class, "can_activate"])) {
            $messages = call_user_func([
				$installer_class,
				"can_activate"
            ]);
		}

		// Remove it from the active path
		self::_remove_from_path($module_name);
		return $messages;
	}

	/**
	 * Allow modules to indicate the impact of deactivating the specified module
	 * @param string $module_name
	 * @return array an array of warning or error messages to be displayed
	 */
    public static function can_deactivate(string $module_name): array
    {
        $data = (object) ["module" => $module_name, "messages" => []];
		self::event("pre_deactivate", $data);

		return $data->messages;
	}

    /**
     * Install a module.  This will call <module>_installer::install(), which is responsible for
     * creating database tables, setting module variables and calling module::set_version().
     * Note that after installing, the module must be activated before it is available for use.
     *
     * @param string $module_name
     * @throws Kohana_Exception|ReflectionException
     */
    public static function install(string $module_name)
	{
		self::_add_to_path($module_name);

		//Call DB migrations for this module
        self::migrate($module_name);

		$installer_class = ucfirst($module_name).'_Installer';
        if (is_callable([$installer_class, "install"])) {
            call_user_func_array([$installer_class, "install"], []);
		} else {
			self::set_version($module_name, 1);
		}

		// Set the weight of the new module, which controls the order in which the modules are
		// loaded. By default, new modules are installed at the end of the priority list.  Since the
		// id field is monotonically increasing, the easiest way to guarantee that is to set the weight
		// the same as the id.  We don't know that until we save it for the first time
        $module = ORM::factory('Module')->where('name', '=', $module_name)->find();
		if ($module->loaded()) {
			$module->weight = $module->id;
			$module->save();
		}

		// clear any cache for sure
		Cache::instance()->delete('load_modules');

        self::load_modules();

		// Now the module is installed but inactive, so don't leave it in the active path
		self::_remove_from_path($module_name);

        Kohana::$log->add(Log::INFO, 'Installed module :module_name', [':module_name' => $module_name]);
	}

    /**
     * @throws Kohana_Exception
     */
    private static function _add_to_path($module)
	{
        $available = static::$available;

        if (isset($available[$module])) {
			$module = $available[$module];

			$modules = Kohana::modules();
			array_unshift($modules, $module->path);
			Kohana::modules($modules);

            // Rebuild the include path so the module installer can benefit from autoloading
            Kohana::include_paths();

			return $module;
		}

		return false;
	}

    /**
     * @throws Kohana_Exception
     */
    private static function _remove_from_path($module)
	{
        $available = static::$available;
		$kohana_modules = Kohana::modules();

        if (isset($available[$module])) {
			$module = $available[$module];

			if (($key = array_search($module->path, $kohana_modules)) !== false) {
				unset($kohana_modules[$key]);
				$kohana_modules = array_values($kohana_modules); // reindex
			}

			Kohana::modules($kohana_modules);
            Kohana::include_paths();
		}
	}

	/**
	 * Upgrade a module
	 *
	 * This will call <module>_installer::upgrade(), which is responsible for
	 * modifying database tables, changing module variables and calling module::set_version().
	 * Note that after upgrading, the module must be activated before it is available for use.
	 *
	 * @param string $module_name
	 * @throws Cache_Exception
	 * @throws Exception
	 */
    public static function upgrade(string $module_name)
	{
        // It's safe to call here, migrations won't run twice. It runs only if not already run.
        self::migrate($module_name);

		$version_before  = self::get_version($module_name);
		$installer_class = ucfirst($module_name).'_Installer';
        if (is_callable([$installer_class, "upgrade"])) {
            call_user_func_array([$installer_class, "upgrade"], [$version_before]);
		} else {
			$available = self::available();
			if (isset($available->$module_name->code_version)) {
				self::set_version($module_name, $available->$module_name->code_version);
			} else {
				throw new Exception("@todo UNKNOWN_MODULE");
			}
		}

        // Now the module is upgraded so deactivate it, but we can't deactivate gleez or user.
        if (!in_array($module_name, ['gleez', 'user'])) {
			self::deactivate($module_name);
		}

		// clear any cache for sure
		Cache::instance()->delete('load_modules');

        self::load_modules();

		$version_after = self::get_version($module_name);
		if ($version_before != $version_after) {
            Kohana::$log->add(Log::INFO, 'Upgraded module :module from :before to :after', [
                ':module' => $module_name,
                ':before' => $version_before,
                ':after' => $version_after
            ]);
		}
	}

    /**
     * Activate an installed module.  This will call <module>_installer::activate() which should take
     * any steps to make sure that the module is ready for use.  This will also activate any
     * existing graphics rules for this module.
     *
     * @param string $module_name
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @throws ORM_Validation_Exception
     * @throws ReflectionException
     */
    static function activate(string $module_name)
	{
		$module = self::_add_to_path($module_name);

		if($module) {
            // It's safe to call here, migrations won't run twice. It runs only if not already run.
            self::migrate($module_name);

			$installer_class = ucfirst($module_name).'_Installer';

            if (is_callable([$installer_class, "activate"])) {
                call_user_func_array([$installer_class, "activate"], []);
			}

            $activeModule = self::get($module->name);

            if ($activeModule->loaded()) {
                $activeModule->active = true;
                $activeModule->path = $module->path;
                $activeModule->save();
			}

			// clear any cache for sure
			Cache::instance()->delete('load_modules');

            self::load_modules();

			// @todo
			//Widget::activate($module_name);
			//Menu_Item::rebuild(true);

            Kohana::$log->add(Log::INFO, 'Activated module :module_name', [':module_name' => $module->title]);

            unset($module, $activeModule);
		}
	}

    /**
     * Deactivate an installed module.  This will call <module>_installer::deactivate() which should
     * take any cleanup steps to make sure that the module isn't visible in any way.  Note that the
     * module remains available in Kohana's cascading file system until the end of the request!
     *
     * @param string $module_name
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @throws ORM_Validation_Exception
     * @throws ReflectionException
     */
    static function deactivate(string $module_name)
	{
		$installer_class = ucfirst($module_name).'_Installer';
        if (is_callable([$installer_class, "deactivate"])) {
            call_user_func_array([$installer_class, "deactivate"], []);
		}

		$module = self::get($module_name);
		if ($module->loaded()) {
			$module->active = false;
			$module->save();
		}

		// clear any cache for sure
		Cache::instance()->delete('load_modules');

        self::load_modules();

        Kohana::$log->add(Log::INFO, 'Deactivated module :module_name', [':module_name' => $module_name]);
	}

    /**
     * Uninstall a deactivated module.  This will call <module>_installer::uninstall() which should
     * take whatever steps necessary to make sure that all traces of a module are gone.
     *
     * @param string $module_name
     * @throws Kohana_Exception
     */
    public static function uninstall(string $module_name)
	{
		//Call DB migrations for this module
		self::migrate($module_name, 'down');

		$installer_class = ucfirst($module_name).'_Installer';
        if (is_callable([$installer_class, "uninstall"])) {
            call_user_func([$installer_class, "uninstall"]);
		}

		$module = self::get($module_name);
		if ($module->loaded()) {
			$module->delete();
		}

        self::load_modules();

		// remove widgets when the module is uninstalled
		Widgets::uninstall($module_name);

        Kohana::$log->add(Log::INFO, 'Uninstalled module :module_name', [':module_name' => $module_name]);
	}

    /**
     * Load the active modules
     *
     * This is called at bootstrap time
     *
     * @param boolean $reset Reset true to clear the cache.
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @uses   Cache::get
     * @uses   Log::add
     * @uses   Arr::merge
     */
    public static function load_modules(bool $reset = true)
	{
        self::$modules = [];
        self::$active = [];

        $kohana_modules = [];
        $cache = Cache::instance();
		$data            = $cache->get('load_modules', false);

		if (false === $reset && $data && isset($data['kohana_modules'])) {
			// use data from cache
			self::$modules  = $data['modules'];
			self::$active   = $data['active'];
			$kohana_modules = $data['kohana_modules'];

			unset($data);
		} else {
            $modules = ORM::factory('Module')
				->order_by('weight','ASC')
				->order_by('name','ASC')
				->find_all();

            $_cache_modules = $_cache_active = [];
			foreach ($modules as $module) {
				self::$modules[$module->name] = $module;
				$_cache_modules[$module->name]  = $module->as_array();

				if (!$module->active) {
					continue;
				}

				//fix for old installations, where gleez exists in db
				if ($module->name != 'gleez') {
					self::$active[$module->name] = $module;
					$_cache_active[$module->name]  = $module->as_array();

					// try to get module path from db if it set
					if( !empty($module->path) && is_dir($module->path)) {
						$kohana_modules[$module->name] = $module->path;
					} else {
						$kohana_modules[$module->name] = MODPATH . $module->name;
					}
				}
			}

            $data = [];
            $data['modules'] = $_cache_modules;
            $data['active']  = $_cache_active;
            $data['kohana_modules'] = $kohana_modules;

            $cache->set('load_modules', $data, Date::DAY);
            unset($data, $_cache_modules, $_cache_active);
		}

		Kohana::modules(Arr::merge($kohana_modules, Kohana::modules()));
	}

	/**
	 * Check to see if a module installed and active
	 *
     * @param string $module_name Module name
	 * @return boolean
	 */
    public static function exists(string $module_name): bool
    {
		return self::is_active($module_name);
	}

	/**
	 * Run a specific event on all active modules.
     *
     * @param string $name The event name
     * @param mixed ...$args Data to pass to each event handler
	 */
    public static function event(string $name, ...$args)
	{
		$function = str_replace(".", "_", $name);

		if (method_exists('Gleez_Event', $function)) {
			switch (count($args)) {
				case 0:
					Gleez_Event::$function();
					break;
				case 1:
					Gleez_Event::$function($args[0]);
					break;
				case 2:
					Gleez_Event::$function($args[0], $args[1]);
					break;
				case 3:
					Gleez_Event::$function($args[0], $args[1], $args[2]);
					break;
                // Context menu events have 4 arguments; optimize for them here.
                case 4:
					Gleez_Event::$function($args[0], $args[1], $args[2], $args[3]);
					break;
				default:
                    call_user_func_array(['Gleez_Event', $function], $args);
			}
		}

		foreach (self::$active as $name => $module) {
			$class = "{$name}_Event";
            if ($name != 'gleez' && is_callable([$class, $function])) {
				try {
                    call_user_func_array([$class, $function], $args);
				}
				catch(Exception $e){}
			}
		}

	}

	/**
     * Call to execute a Module action.
     *
     * @param string $action The name of the action to execute
     * @param mixed $return The initial return value
     * @param mixed ...$filterArgs Additional arguments to pass to the action handler
	 */
    public static function action(string $action, $return, ...$filterArgs)
	{
		$function = str_replace(".", "_", $action);

		foreach (self::$active as $name => $module) {
			$class = ucfirst($name).'_Action';
            $args = $filterArgs;
			array_unshift($args, $return);

            if (is_callable([$class, $function])) {
				try {
                    $return = call_user_func_array([$class, $function], $args);
				}
				catch(Exception $e){}
			}
		}

		return $return;
	}

    /**
     * Return the version of the installed module
     *
     * @param string $name Module name
     * @return string Module version
     * @throws Kohana_Exception
     */
    public static function get_version(string $name): string
    {
        return (string) self::get($name)->version;
	}

	/**
     * Migrate the database of the module.
	 *
     * @param string $module_name Module name
     * @param string $dir Migration direction up/down
	 * @return  void
	 */
    private static function migrate(string $module_name, string $dir = 'up')
	{
		try {
			$task = ($dir == 'down') ? 'db:migrate:down' : 'db:migrate:up';

            $options = [
					'task'  => $task,
					'group' => $module_name,
					'quiet' => 'quiet'
            ];

			//Call DB migrations for this module
			Minion_Task::factory($options)->execute();
		}
		catch(Exception $e){}
	}
}
