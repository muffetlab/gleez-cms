<?php
/**
 * Comment Model Class
 *
 * @package    Gleez\ORM\Comment
 * @author     Gleez Team
 * @version    1.0.2
 * @copyright  (c) 2011-2015 Gleez Technologies
 * @license    https://gleezcms.org/license  Gleez CMS License
 */
class Model_Comment extends Gleez_Model
{

	/**
	 * Table columns
	 * @var array
	 */
	protected $_table_columns = array(
		'id'          => array( 'type' => 'int' ),
		'post_id'     => array( 'type' => 'int' ),
		'pid'         => array( 'type' => 'int' ),
		'author'      => array( 'type' => 'int' ),
		'title'       => array( 'type' => 'string' ),
		'body'        => array( 'type' => 'string' ),
		'hostname'    => array( 'type' => 'string' ),
		'created'     => array( 'type' => 'int' ),
		'updated'     => array( 'type' => 'int' ),
		'status'      => array( 'type' => 'string' ),
		'type'        => array( 'type' => 'string' ),
		'format'      => array( 'type' => 'int' ),
		'thread'      => array( 'type' => 'string' ),
		'guest_email' => array( 'type' => 'string' ),
		'guest_name'  => array( 'type' => 'string' ),
		'guest_url'   => array( 'type' => 'string' ),
		'karma'       => array( 'type' => 'int' ),
	);

	/**
	 * "Belongs to" relationships
	 * @var array
	 */
	protected $_belongs_to = array(
		'post' => array(
            'model' => 'Post',
			'foreign_key' => 'post_id'
		),
		'user' => array(
			'foreign_key' => 'author'
		)
	);

	/**
	 * Rules for the post model
	 *
	 * @return  array  Rules
	 */
	public function rules(): array
    {
		return array(
			'author' => array(
				array('not_empty'),
			),
			'post_id' => array(
				array('not_empty'),
				array(array($this, 'valid_post'), array(':validation', ':field')),
			),
			'guest_name' => array(
				array(array($this, 'valid_author'), array(':validation', ':field')),
			),
			'guest_email' => array(
				array(array($this, 'valid_email'), array(':validation', ':field')),
			),
			'guest_url' => array(
				array('url'),
			),
			'status' => array(
				array('Comment::valid_state', array(':value')),
			),
			'body' => array(
				array('not_empty'),
			),
			'type' => array(
				array('not_empty'),
			),
		);
	}

	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels(): array
    {
		return array(
			'title'       => __('Title'),
			'body'        => __('Comment'),
			'guest_name'  => __('Name'),
			'guest_email' => __('Email'),
			'guest_url'   => __('Website'),
			'author'      => __('Author'),
		);
	}

    /**
     * Updates or Creates the record depending on loaded()
     *
     * @param Validation|null $validation Validation object
     * @return  ORM
     * @throws Cache_Exception
     * @throws Kohana_Exception
     * @throws ORM_Validation_Exception
     * @throws ReflectionException
     * @uses    ACL::check
     * @uses    Text::limit_words
     * @uses    Text::markup
     * @uses    Request::$client_ip
     * @uses    User::active_user
     */
	public function save(Validation $validation = NULL): Kohana_ORM
    {
		// Set some defaults
		$this->updated = time();
        $this->format = empty($this->format) ? Kohana::$config->load('input_filter.default_format') : $this->format;
		$this->author = empty($this->author) ? User::active_user()->id : $this->author;

		if ( ! $this->loaded())
		{
			// New comment
			$this->created = $this->updated;
			$this->hostname = substr(Request::$client_ip, 0, 32); //set hostname only if its new comment.

			if (empty($this->status))
			{
				$this->status = ACL::check('skip comment approval') ? 'publish' : 'draft';
			}
		}

		// Validate the comment's title. If not specified, extract from comment body.
        if (trim($this->title) == '' && !empty($this->body))
		{
			// The body may be in any format, so:
			// 1) Filter it into HTML
			// 2) Strip out all HTML tags
			// 3) Convert entities back to plain-text.
			$this->title = Text::limit_words(trim( UTF8::clean(strip_tags(Text::markup($this->body, $this->format)))), 10, '');

			// Edge cases where the comment body is populated only by HTML tags will
			// require a default subject.
			if ($this->title == '')
			{
				$this->title = __('(No subject)');
			}
		}

		parent::save($validation);

		return $this;
	}

    /**
     * Reading data from inaccessible properties.
     *
     * @param string $column
     * @return mixed
     * @throws Kohana_Exception
     * @uses    Text::markup
     * @uses    Route::get
     * @uses    Route::uri
     * @uses    HTML::chars
     */
    public function __get(string $column)
	{
        switch ($column) {
			case 'title':
                return HTML::chars($this->get('title') ?? '');
            case 'body':
				return Text::markup(parent::__get('body'), $this->format);
            case 'rawtitle':
                // Raw fields without markup. Usage: during edit or etc.!
				return parent::__get('title');
            case 'rawbody':
                // Raw fields without markup. Usage: during edit or etc.!
                return $this->get('body') ?? '';
            case 'url':
				return Route::get('comment')->uri( array('id' => $this->id, 'action' => 'view'));
            case 'edit_url':
				return Route::get('comment')->uri(array('id' => $this->id, 'action' => 'edit'));
            case 'delete_url':
				return Route::get('comment')->uri(array('id' => $this->id, 'action' => 'delete'));
        }

        return parent::__get($column);
	}

    /**
     * Make sure we have a valid author id set, or a guest id.
     *
     * Validation callback.
     *
     * @param Validation $validation Validation object
     * @param string $field Field name
     * @throws Kohana_Exception
     * @uses    DB::select
     * @uses    DB::expr
     * @uses    Validation::error
     * @uses    User::lookup_by_name
     */
    public function valid_author(Validation $validation, string $field)
	{
        if (!empty($this->author_name) && !($account = User::lookup_by_name($this->author_name)))
		{
			$validation->error('author', 'invalid', array($this->author_name));
		}
		else
		{
			if (isset($account))
			{
				$this->author = $account->id;
			}
		}

		if (empty($this->author))
		{
			$validation->error($field, 'not_empty', array($validation[$field]));
        } elseif ($this->author == 1 && empty($this->guest_name)) {
			$validation->error('guest_name', 'not_empty', array($validation[$field]));
        } elseif ($this->author == 1 && !empty($this->guest_name)) {
			$result = DB::select(array(DB::expr('COUNT(*)'), 'total_count'))
						->from('users')
						->where('name', 'LIKE', $this->guest_name)
						->or_where('nick', 'LIKE', $this->guest_name)
						->execute($this->_db)
						->get('total_count');

			if ($result > 0)
			{
				$validation->error($field, 'registered_user', array($validation[$field]));
			}
		}
	}

	/**
	 * Make sure that the email address is legal
	 *
	 * @param   Validation  $validation  Validation object
     * @param string $field Field name
	 *
	 * @uses    Valid::email
	 */
    public function valid_email(Validation $validation, string $field)
	{
		if ($this->author == 1)
		{
			if (empty($validation[$field]))
			{
				$validation->error($field, 'not_empty', array($validation[$field]));
			}
			elseif ( ! Valid::email($validation[$field]))
			{
				$validation->error($field, 'invalid', array($validation[$field]));
			}
		}
	}

    /**
     * Check by triggering error if post exists
     *
     * Validation callback.
     *
     * @param Validation $validation Validation object
     * @param string $field Field name
     * @throws Kohana_Exception
     * @uses    DB::select
     */
    public function valid_post(Validation $validation, string $field)
	{
		$result = DB::select(array(DB::expr('COUNT(*)'), 'total_count'))
				->from('posts')
				->where('id', '=', $this->post_id)
				->execute($this->_db)
				->get('total_count');

		if ($result  != 1)
		{
			$validation->error($field, 'invalid', array($validation[$field]));
		}
	}

    /**
     * Make sure the user has permission to do the action on this object
     *
     * @param boolean|string $action The action view|edit|delete default view [Optional]
     * @param Model_User|null $user The user object to check permission, defaults to logged in user [Optional]
     * @return Model_Comment
     * @throws Cache_Exception
     * @throws HTTP_Exception
     * @throws Kohana_Exception
     * @uses    ACL::check
     * @uses    Module::event
     */
    public function access($action = FALSE, Model_User $user = NULL): Model_Comment
    {
		if ( ! $action)
		{
			$action = 'view';
		}

		if ( ! in_array($action, array('view', 'edit', 'delete', 'add', 'list'), TRUE))
		{
			// If the $action was not one of the supported ones, we return access denied.
			throw HTTP_Exception::factory(404, 'Unauthorized attempt to access non-existent action :act.',
				array(':act' => $action));
		}

		if ( ! $this->loaded())
		{
			// If the $action was not one of the supported ones, we return access denied.
			throw HTTP_Exception::factory(404, 'Attempt to access non-existent post.');
		}

		// If no user object is supplied, the access check is for the current user.
        empty($user) and $user = User::active_user();

		if (ACL::check('bypass comment access', $user))
		{
			return $this;
		}

		// Allow other modules to interact with access
		Module::event('comment_access', $action, $this);

		if ($action === 'view')
		{
            if ($this->status === 'publish' && ACL::check('access comment', $user))
			{
				return $this;
			}
			// Check if authors can view their own unpublished posts.
            elseif ($this->status != 'publish' && $this->author == (int) $user->id && $user->id != 1)
			{
				return $this;
			}
			elseif (ACL::check('administer comment', $user))
			{
				return $this;
			}
			else
			{
				throw HTTP_Exception::factory(403, 'Unauthorized attempt to view comment :post.',
					array(':post' => $this->id));
			}
		}

		if ($action === 'edit')
		{
            if (ACL::check('edit own comment') && $this->author == (int) $user->id && $user->id != 1)
			{
				return $this;
			}
			elseif (ACL::check('administer comment', $user))
			{
				return $this;
			}
			else
			{
				throw HTTP_Exception::factory(403, 'Unauthorized attempt to edit comment :post',
					array(':post' => $this->id));
			}
		}

		if ($action === 'delete')
		{
            if (
                (ACL::check('delete own comment') or ACL::check('delete any comment'))
                && $this->author == (int) $user->id
                && $user->id != 1
            )
			{
				return $this;
			}
			elseif (ACL::check('administer comment', $user))
			{
				return $this;
			}
			else
			{
				throw HTTP_Exception::factory(403, 'Unauthorised attempt to delete comment :post',
					array(':post' => $this->id));
			}
		}

		return $this;
	}


    /**
     * Make sure the user has permission to do the action on this object
     *
     * Similar to Comment::access but this return True/False instead of exception
     *
     * @param bool|string $action The action view|edit|delete default view
     * @param Model_User|null $user The user object to check permission, defaults to logged in user
     * @return  boolean|Model_Comment
     * @throws Cache_Exception
     * @throws HTTP_Exception
     * @throws Kohana_Exception
     * @uses    Log::add
     * @uses    User::active_user
     * @uses    ACL::check
     * @uses    Module::event
     */
    public function user_can($action = FALSE, Model_User $user = NULL)
	{
		if( ! $action) $action = 'view';

		if ( ! in_array($action, array('view', 'edit', 'delete', 'add', 'list'), TRUE))
		{
			// If the $action was not one of the supported ones, we return access denied.
			Kohana::$log->add(Log::NOTICE, 'Unauthorised attempt to access non-existent action :act.',
				array(':act' => $action)
			);
			return FALSE;
		}

		if ( ! $this->loaded())
		{
			// If the $action was not one of the supported ones, we return access denied.
			throw HTTP_Exception::factory(404, 'Attempt to access non-existent comment.');
		}

		// If no user object is supplied, the access check is for the current user.
		if (empty($user)) $user = User::active_user();

		if (ACL::check('bypass comment access', $user))
		{
			return TRUE;
		}

		//allow other modules to interact with access
		Module::event('comment_access', $action, $this);

		// can view?
		if ($action === 'view')
		{
            if ($this->status === 'publish' && ACL::check('access comment', $user))
			{
				return $this;
			}
			// Check if commentators can view their own unpublished comments.
            elseif ($this->status != 'publish' && $this->author == (int) $user->id && $user->id != 1)
			{
				return $this;
			}
			elseif (ACL::check('administer comment', $user))
			{
				return $this;
			}
			else
			{
				Kohana::$log->add(Log::NOTICE, 'Unauthorised attempt to view comment :post.',
					array(':post' => $this->id)
				);
				return FALSE;
			}
		}

		// can edit?
		if ($action === 'edit')
		{
            if (ACL::check('edit own comment') && $this->author == (int) $user->id && $user->id != 1)
			{
				return $this;
			}
			elseif (ACL::check('administer comment', $user))
			{
				return $this;
			}
			else
			{
				Kohana::$log->add(Log::NOTICE, 'Unauthorised attempt to edit comment :post.',
					array(':post' => $this->id)
				);
				return FALSE;
			}
		}

		// can delete?
		if ($action === 'delete')
		{
            if (
                (ACL::check('delete own comment') or ACL::check('delete any comment'))
                && $this->author == (int) $user->id
                && $user->id != 1
            )
			{
				return $this;
			}
			elseif (ACL::check('administer comment', $user))
			{
				return $this;
			}
			else
			{
				Kohana::$log->add(Log::NOTICE, 'Unauthorised attempt to delete comment :post.',
					array(':post' => $this->id)
				);
				return FALSE;
			}
		}

		return TRUE;
	}
}
