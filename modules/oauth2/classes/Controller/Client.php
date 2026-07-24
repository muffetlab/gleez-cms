<?php

class Controller_Client extends Template {

    /**
     * @throws HTTP_Exception_404
     * @throws View_Exception
     * @throws Kohana_Exception
     * @throws Cache_Exception
     */
    public function action_list()
	{ 
		if ( Request::is_datatables() )
		{
			if ( ! ACL::check('access oaclient2'))
			{
				throw new HTTP_Exception_404('You have no permission to access oauth2 clients.');
			}

            $posts = ORM::factory('OAClient');
			
			if ( ! User::is_admin())
			{
				$user = Auth::instance()->get_user();
				$posts->where('user_id', '=', $user->id);
			}

            $this->_datatables = $posts->dataTables(['title', 'client_id', 'user_id', 'created']);

            foreach ($this->_datatables->result() as $client)
			{
                $this->_datatables->add_row([
                    HTML::anchor($client->url, HTML::chars($client->title)),
                    $client->client_id,
                    $client->user->nick,
                    System::date('M d, Y', $client->created),
                    HTML::icon($client->edit_url, 'far fa-edit', [
                        'class' => 'action-edit',
                        'data-toggle' => 'popup1',
                        'title' => __('Edit')
                    ])
                    . '&nbsp;'
                    . HTML::icon($client->delete_url, 'fas fa-trash-can', [
                        'class' => 'action-delete',
                        'data-toggle' => 'popup',
                        'title' => __('Delete')
                    ])
                ]);
			}
		}

		$this->title = __('Oauth Clients');

        $view = View::factory('client/list')
            ->bind('datatables', $this->_datatables)
            ->set('url', Route::url('oauth2/client', ['action' => 'list'], TRUE))
            ->set('show', TRUE);
		
		$this->response->body($view);
	}

    /**
     * @throws View_Exception
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     * @throws ReflectionException
     * @throws Cache_Exception
     */
    public function action_Register()
	{
		if ( ! ACL::check('administer oauth2'))
		{
			throw new HTTP_Exception_404('You have no permission to add oauth2 clients.');
		}

        $client = ORM::factory('OAClient');

        if (isset($_POST['cancel']) && $this->valid_post())
		{
            $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		}
		
		if ($this->valid_post('save'))
		{
            $client->values($this->request->post(), ['title', 'redirect_uri', 'description', 'status']);
		    
		    try
		    {
                if (!empty($_POST['grant_types']))
			    {
					$grant_types_selected = implode(" ", $_POST['grant_types']);
                    $client->grant_types = $grant_types_selected;
			    }

                if (isset($_FILES['logo']))
			    {
				    $filename = uniqid().preg_replace('/\s+/u', '_', $_FILES['logo']['name']);

                    if (Upload::save($_FILES['logo'], $filename, APPPATH . '/media/logos'))
				    {
                        $client->logo = $filename;
				    }
			    }

                $client->save();
                Message::success(__('Client registered :title ', [':title' => $client->title]));
                $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		    }
		    catch(ORM_Validation_Exception $e)
		    {
				$this->_errors = $e->errors('models');
		    }
		}

        $this->title = __('Oaclient Registration');
        $grant_types = Kohana::$config->load('oauth2')->get('grant_types');
        $view = View::factory('client/form')
            ->set('grant_types', $grant_types)
            ->bind('oaclient', $client)
            ->bind('errors', $this->_errors);

		$this->response->body($view);
	}

    /**
     * @throws View_Exception
     * @throws HTTP_Exception_404
     * @throws Kohana_Exception
     * @throws ReflectionException
     * @throws Cache_Exception
     */
    public function action_edit()
	{
		if ( ! ACL::check('edit oaclient2'))
		{
			throw new HTTP_Exception_404('You have no permission to edit oauth2 clients.');
		}
		
		$id       = (int) $this->request->param('id');
        $client = ORM::factory('OAClient', $id);

        if (!$client->loaded())
		{
            Message::error(__("Client: doesn't exists!"));
			Kohana::$log->add(Log::ERROR, 'Attempt to edit non-existent client');

            $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		}

        if (isset($_POST['cancel']) && $this->valid_post())
		{
            $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		}
		
		if ($this->valid_post('save'))
		{
            $client->values($this->request->post(), ['title', 'redirect_uri', 'description', 'status']);
		    
		    try
		    {
			    //$grant_types_selected = 'authorization_code';
                if (!empty($_POST['grant_types']))
			    {
				$grant_types_selected = implode(" ", $_POST['grant_types']);
                    $client->grant_types = $grant_types_selected;
			    }

                if (isset($_FILES['logo']))
			    {
				    $filename = uniqid().preg_replace('/\s+/u', '_', $_FILES['logo']['name']);

                    if (Upload::save($_FILES['logo'], $filename, APPPATH . '/media/logos'))
				    {
                        $client->logo = $filename;
				    }
			    }

                $client->save();
                Message::success(__('Client :title updated successfully', [':title' => $client->title]));
                $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		    }
		    catch(ORM_Validation_Exception $e)
		    {
				$this->_errors = $e->errors('models');
		    }
		}
		
		$grant_types    = Kohana::$config->load('oauth2')->get('grant_types');
		$this->title    = __('Edit oaclient');
        $this->subtitle = HTML::chars($client->title);
        $view = View::factory('client/form')
            ->set('grant_types', $grant_types)
            ->bind('oaclient', $client)
            ->bind('errors', $this->_errors);
		
		$this->response->body($view);
	}

    /**
     * @throws Kohana_Exception
     * @throws HTTP_Exception_404
     * @throws View_Exception
     * @throws Cache_Exception
     */
    public function action_view()
	{
		if ( ! ACL::check('access oaclient2'))
		{
			throw new HTTP_Exception_404('You have no permission to access oauth2 clients.');
		}
		
		$id       = (int) $this->request->param('id');
        $client = ORM::factory('OAClient', $id);

        if (!$client->loaded())
		{
            Message::error(__("Client: doesn't exists!"));
			Kohana::$log->add(Log::ERROR, 'Attempt to edit non-existent client');

            $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		}
		
		$this->title    = __('Client info');
        $this->response->body(View::factory('client/view')->set('oaclient', $client));
	}

    /**
     * @throws HTTP_Exception_404
     * @throws View_Exception
     * @throws Kohana_Exception
     * @throws Cache_Exception
     */
    public function action_delete()
	{
		if ( ! ACL::check('delete oaclient2'))
		{
			throw new HTTP_Exception_404('You have no permission to delete oauth2 clients.');
		}
		
		$id       = (int) $this->request->param('id');
        $redirect = empty($this->redirect) ? Route::get('oauth2/client')->uri(['action' => 'list']) : $this->redirect;
        $client = ORM::factory('OAClient', $id);

        if (!$client->loaded())
		{
            Message::error(__("oaclient: doesn't exists!"));
			Kohana::$log->add(Log::ERROR, 'Attempt to delete non-existent oaclient');

            $this->request->redirect(Route::get('oauth2/client')->uri(['action' => 'list']));
		}

        if (!Access::oaclient('delete', $client))
		{
			// If the lead was not loaded, we return access denied.
			throw new HTTP_Exception_404('Attempt to non-existent oaclient.');
		}
		
		$this->title    = __('Delete oaclient');
        $this->subtitle = HTML::chars($client->client_id);
        $form = View::factory('form/confirm')->set('action', $client->delete_url)->set('title', $client->client_id);

		// If deletion is not desired, redirect to post
        if (isset($_POST['no']) && $this->valid_post())
			$this->request->redirect( 'oauth2/client' );

		// If deletion is confirmed
        if (isset($_POST['yes']) && $this->valid_post())
		{
            $clonedClient = clone $client;

			try
			{
                $client->delete();

                Message::success(__('oaclient: :title deleted successfully', [':title' => $clonedClient->client_id]));
				$this->request->redirect($redirect);
			}
			catch(Exception $e)
			{
                Message::error(__('oaclient: :title unable to delete the record', [
                    ':title' => $clonedClient->client_id
                ]));
				$this->request->redirect($redirect);
			}			
		}
		
		$this->response->body($form);		
	}
}