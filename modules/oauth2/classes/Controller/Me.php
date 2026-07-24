<?php

class Controller_Me extends Controller
{
    /**
     * @throws Exception
     */
    public function action_index()
	{
		if ($header = Request::current()->headers('Authorization')) 
		{
			// Check for special case, because cURL sometimes does an
			// internal second request and doubles the authorization header,
			// which always resulted in an error.
			//
			// 1st request: Authorization: Bearer XXX
			// 2nd request: Authorization: Bearer XXX, Bearer XXX
			if (strpos($header, ',') !== false) 
			{
			    $headerPart = explode(',', $header);
			    $accessToken = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $headerPart[0]));
			} 
			else 
			{
			    $accessToken = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $header));
			}

			$accessToken = ($accessToken === 'Bearer') ? '' : $accessToken;
        } else {
            $accessToken = Request::current()->query('access_token');
		}

		if (empty($accessToken)) 
		{
			//return Oauth::$exceptions['invalid_request'];
			throw new Exception('Access token is missing');
		}

        $token = Model::factory('oauth')->getAccessToken($accessToken);

        if ($token['access_expires'] < time())
		{
			//return Oauth::$exceptions['invalid_grant'];
			throw new Exception('Access token is expired');
		}

        $user = User::lookup($token['user_id']);

		if ($user)
		{
            $user_info = ['id' => $user->id, 'email' => $user->mail, 'name' => $user->nick];
		}
		else
		{
            $user_info = ['message' => "User doesnt exists", 'Status code' => 400];
		}

		$this->response->body(json_encode($user_info));
	}
}