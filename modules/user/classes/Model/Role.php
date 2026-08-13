<?php

/**
 * Default auth role
 *
 * @package    Gleez\User
 * @author     Gleez Team
 * @version    1.0.0
 * @copyright  (c) 2011-2015 Gleez Technologies
 */
class Model_Role extends Gleez_Model
{
	/**
	 * Table columns
	 * @var array
	 */
    protected $_table_columns = [
        'id' => ['type' => 'int'],
        'name' => ['type' => 'string'],
        'description' => ['type' => 'string'],
        'special' => ['type' => 'int'],
        'deleted' => ['type' => 'int'],
    ];

    /**
     * Soft-delete column
     * @var array
     */
    protected $_deleted_column = ['column' => 'deleted', 'format' => true];

	/**
	 * A role has many users
	 *
	 * @var array Relationships
	 */
    protected $_has_many = [
        'users' => [
            'through' => 'roles_users'
        ]
    ];

	/**
	 * Rules for the role model
	 *
	 * @return array Rules
	 */
	public function rules(): array
    {
        return [
            'name' => [
                ['not_empty'],
                ['min_length', [':value', 4]],
                ['max_length', [':value', 32]],
            ],
            'description' => [
                ['max_length', [':value', 255]],
            ]
        ];
	}

	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels(): array
    {
        return [
            'name' => __('Name'),
            'description' => __('Description'),
            'special' => __('Special Role'),
        ];
	}

	/**
	 * Override the save method to clear cache
     *
     * @throws Kohana_Exception|ReflectionException
     */
    public function save(Validation $validation = null): Kohana_ORM
    {
		parent::save( $validation );

		//cleanup the cache
        Cache::instance()->delete_all();

		return $this;
	}

    /**
     * Override the delete method to clear cache.
     *
     * @param bool $soft
     * @return Kohana_ORM
     * @throws Cache_Exception
     * @throws Kohana_Exception
     */
    public function delete(bool $soft = false): Kohana_ORM
    {
        parent::delete($soft);

		//cleanup the cache
        Cache::instance()->delete_all();

		return $this;
	}

    /**
     * Reading data from inaccessible properties.
     *
     * @param string $column
     * @return mixed
     * @throws Kohana_Exception
     * @uses  Route::uri
     * @uses  Route::get
     */
    public function __get(string $column)
	{
        switch ($column) {
			case 'edit_url':
                return Route::get('admin/role')->uri(['action' => 'edit', 'id' => $this->id]);
            case 'delete_url':
                return Route::get('admin/role')->uri(['action' => 'delete', 'id' => $this->id]);
            case 'perm_url':
                return Route::get('admin/permission')->uri(['action' => 'role', 'id' => $this->id]);
        }

        return $this->get($column);
	}

}
