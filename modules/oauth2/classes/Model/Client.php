<?php

class Model_Client extends Gleez_Model
{
    protected $_table_name = 'oauth_clients';

	/**
     * Autofill create and update columns
	 */
    protected $_created_column = ['column' => 'created', 'format' => true];

    protected $_updated_column = ['column' => 'updated', 'format' => true];

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

    public function save(Validation $validation = null): Kohana_ORM
    {
		$this->user_id   		= User::active_user()->id;
		$this->client_id 		= sha1($this->user_id.uniqid().microtime());
		$this->client_secret    = sha1($this->user_id.uniqid().microtime());
		
		return parent::save($validation);
	}
    
}