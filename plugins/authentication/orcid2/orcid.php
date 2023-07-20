<?php

defined('_HZEXEC_') or die();

use Orcid2\Oauth;

require_once __DIR__ . '/Orcid2/Http/Curl.php';
require_once __DIR__ . '/Orcid2/Oauth.php';

class plgAuthenticationOrcid2 extends \Hubzero\Plugin\OauthClient
{
    protected $_autoloadLanguage = true;

    public function logout(){

    }

    public function status(){

    }

    public function login(&$credentials, &$options)
    {

        $client = new Ouath();
        if ($this->params->get('environment') == 'sandbox ')
        {
            $client->useSandboxEnvironment();
        }
        $client->setClientId($this->params->get('app_id'))
               ->setClientSecret($this->params->get('app_secret'))
               ->setRedirectUri(self::getRedirectUri('orcid'));

        if ($code = Request::getString('code'))
        {
            $client->authenticate($code);

            Session::set('orcid.token', $client->getAccessToken());
        }
        else
        {
            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . base64_encode('/members/myaccount')),
                Lang::txt('PLG_AUTHENTICATION_SCISTARTER_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
                'error'
            );
        }
    }

    public function display($view, $tpl)
    {
        $client = new Oauth();
        if ($this->params->get('environment') == 'sandbox')
        {
            $client->useSandboxEnvironment();
        }
        $client->setClientId($this->params->get('app_id'))
               ->setRedirectUri(self::getRedirectUri('orcid'));
        App::redirect($client->getAuthorizationUrl());
    }

    public function onAuthenticate($credentials, $options, &$response)
    {
        return $this->onUserAuthenticate($credentials, $options, $response);
    }

    public function onUserAuthenticate($credentials, $options, &$response)
    {
        $client = new Oauth();
        if ($this->params->get('environment') == 'sandbox')
        {
            $client->useSandboxEnvironment();
        }
        $client->setClientId($this->params->get('app_id'))
               ->setClientSecret($this->params->get('app_secret'))
               ->setRedirectUri(self::getRedirectUri('orcid'));
        
        if (App::get('session')->get('orcid.token', null))
        {
            $client->setAccessToken(App:get('session')->get('orcid.token'));
        }

        if ($client->isAuthenticated())
        {
            $account = $client->getUserData();

            $accountIsOk = true;
            if (!isset($account->user_id) || $account->user_id <= 0)
			{
				$accountIsOk = false;
				$error_message = $response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED_NO_UID');
			}
			elseif (!isset($account->email) || !$account->email)
			{
				$accountIsOk = false;
				$error_message = $response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED_NO_EMAIL');
			}

            if ($accountIsOk)
            {
                $username = (string) $account->email;
                $method = (Component::params('com_members')->get('allowUserRegistration', false)) ? 'find_or_create' : 'find';
                $hzal = \Hubzero\Auth\Link::$method('authentication', 'orcid', null, $username);

                if ($hzal === false)
                {
                    $response->status = \Hubzero\Auth\Status::FAILURE;
                    $response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_UNKNOWN_USER');
                    return;
                }

                $hzal->set('email', $account->email);

                $response->auth_link = $hzal;
                $response->type      = 'orcid';
                $response->status    = \Hubzero\Auth\Status::SUCCESS;
                $response->fullname  = $account->email;

                if($hzal->user_id)
                {
                    $user = User::getInstance($hzal->user_id);

					$response->username = $user->username;
					$response->email    = $user->email;
					$response->fullname = $user->name;
                }
                else
                {
                    $response->username = '-' . $hzal->id;
					$response->email    = $response->username . '@invalid';

					// Also set a suggested username for their hub account
					$sub_email    = explode('@', $account->email, 2);
					$tmp_username = $sub_email[0];

					Session::set('auth_link.tmp_username', $tmp_username);
                }

                $hzal->update();

                if (isset($user) && is_object($user))
                {
                    $prefs = array(
                        'user_id'       => $user->get('id'),
                        'user_img'      => $user->picture(0, false),
                        'authenticator' => 'orcid'
                    );

                    $namespace = 'authenticator';
                    $lifetime = time() + 365*24*60*60;
                    \Hubzero\Utility\Cookie::bake($namespace, $lifetime, $prefs);
                }
            }
            else
            {
                $response->status = \Hubzero\Auth\Status::FAILURE;
                $response->error_message = $error_message;
            }
        }
        else
        {
            $response->status = \Hubzero\Auth\Status::FAILURE;
            $response->error_message = Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED');
        }
    }

    public function link($options=array())
    {
        $client = new Oauth();
        if ($this->params->get('environment') == 'sandbox'){
            $client->useSandboxEnvironment();
        }
        $client->setClientId($this->params->get('app_id'))
               ->setClientSecret($this->params->get('app_secret'))
               ->setRedirectUri(self::getRedirectUri('orcid'));
        if ($code = Request::getString('code'))
        {
            $client->authenticate($code);
        }
        else
        {
            App::redirect(
                Route::url('index.php?option=com_users&view=login&return=' . base64_encode('/members/myaccount')),
                Lang::txt('PLG_AUTHENTICATION_SCISTARTER_MUST_AUTHORIZE_TO_LOGIN', Config::get('sitename')),
                'error'
            );
        }
        if ($client->isAuthenticated())
        {
            $account = $client->getUserData();
        }
        else
        {
            App:redirect(
                Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
                Lang::txt('PLG_AUTHENTICATION_SCISTARTER_MUST_AUTHORIZE_TO_LINK', Config::get('sitename')),
                'error'
            );
        }

        if ($account->user_id > 0)
        {
            $username = (string) $account->email;
            $hzad = \Hubzero\Auth\Domain::getInstance('authentication', 'orcid', '');

            if (\Hubzero\Auth\Link::getInstance($hzad->id, $username))
            {
                App::redirect(
                    Route::url('index.php?option=com_members&id=' . User::get('id') . '&acitve=account'),
                    Lang::txt('PLG_AUTHENTICATION_SCISTARTER_ACCOUNT_ALREADY_LINKED'),
                    'error'
                );
            }
            else
            {
                $hzal = \Hubzero\Auth\Link::find_or_create('authentication', 'orcid', null, $username);
                if ($hzal)
				{
					$hzal->set('user_id', User::get('id'));
					$hzal->update();
				}
				else
				{
					Log::error(sprintf('Hubzero\Auth\Link::find_or_create("authentication", "scistarter", null, %s) returned false', $username));
				}
            }
        }
        else {
            App::redirect(
				Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=account'),
				Lang::txt('PLG_AUTHENTICATION_SCISTARTER_AUTHENTICATION_FAILED', Config::get('sitename')),
				'error'
			);
        }
    }


}