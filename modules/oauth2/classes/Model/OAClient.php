<?php

class Model_OAClient extends ORM {

	protected $_table_name = "oauth_clients";

    protected $_table_columns = [
        'id' => ['type' => 'int'],
        'title' => ['type' => 'string'],
        'user_id' => ['type' => 'int'],
        'client_id' => ['type' => 'string'],
        'client_secret' => ['type' => 'string'],
        'redirect_uri' => ['type' => 'string'],
        'grant_types' => ['type' => 'string'],
        'description' => ['type' => 'string'],
        'logo' => ['type' => 'string'],
        'status' => ['type' => 'int'],
        'created' => ['type' => 'int'],
        'updated' => ['type' => 'int'],
    ];

	/**
     * Autofill create and update columns
	 */
    protected $_created_column = ['column' => 'created', 'format' => TRUE];

    protected $_updated_column = ['column' => 'updated', 'format' => TRUE];

    protected $_belongs_to = [
        'user' => ['model' => 'User', 'foreign_key' => 'user_id'],
    ];

    public function rules(): array
    {
        return [
            'title' => [
                ['not_empty'],
            ],
            'redirect_uri' => [
                ['not_empty'],
            ],
        ];
	}

    public function __get(string $column)
	{
        if ($column === 'url')
            return Route::get('oauth2/client')->uri(['id' => $this->id, 'action' => 'view']);

        if ($column === 'edit_url')
            return Route::get('oauth2/client')->uri(['id' => $this->id, 'action' => 'edit']);

        if ($column === 'delete_url')
            return Route::get('oauth2/client')->uri(['id' => $this->id, 'action' => 'delete']);

        return parent::__get($column);
	}

    public function save(Validation $validation = NULL): Kohana_ORM
    {
		$this->user_id   		= User::active_user()->id;
		$this->client_id 		= sha1($this->user_id.uniqid().microtime());
		$this->client_secret    = sha1($this->user_id.uniqid().microtime());
		
		return parent::save($validation);
	}
    
}