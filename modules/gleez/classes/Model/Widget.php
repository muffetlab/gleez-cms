<?php
/**
 * Widget Model Class
 *
 * @package    Gleez\ORM\Widget
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Model_Widget extends ORM {

	/**
	 * Table columns
	 * @var array
	 */
    protected $_table_columns = [
        'id' => ['type' => 'int'],
        'name' => ['type' => 'string'],
        'title' => ['type' => 'string'],
        'module' => ['type' => 'string'],
        'theme' => ['type' => 'string'],
        'status' => ['type' => 'int'],
        'region' => ['type' => 'string'],
        'weight' => ['type' => 'int'],
        'cache' => ['type' => 'int'],
        'visibility' => ['type' => 'int'],
        'pages' => ['type' => 'string'],
        'show_title' => ['type' => 'int'],
        'roles' => ['type' => 'string'],
        'body' => ['type' => 'string'],
        'format' => ['type' => 'int'],
        'icon' => ['type' => 'string'],
    ];

	/**
	 * Rules for the post model
	 *
	 * @return  array  Rules
	 */
	public function rules(): array
    {
        return [
            'name' => [
                ['not_empty'],
            ],
        ];
	}

    /**
     * Updates or Creates the record depending on loaded()
     *
     * @param Validation|null $validation Validation object [Optional]
     * @return  ORM
     * @throws Kohana_Exception
     * @throws ORM_Validation_Exception
     * @throws ReflectionException
     */
	public function save(Validation $validation = NULL): Kohana_ORM
    {
        if (is_array($this->roles) && count($this->roles) > 0)
		{
			$this->roles = implode(',', $this->roles);
		}
		else
		{
			$this->roles = NULL;
		}

		return parent::save($validation);
	}

    /**
     * Reading data from inaccessible properties
     *
     * @param string $column
     * @return  mixed
     * @throws Kohana_Exception
     * @uses  Route::uri
     * @uses  System::icons
     * @uses  Route::get
     */
    public function __get(string $column)
	{
        switch ($column) {
			case 'edit_url':
                return Route::get('admin/widget')->uri(['id' => $this->id, 'action' => 'edit']);
            case 'icons':
				return System::icons();
        }

        return parent::__get($column);
	}

}
