<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect\Provider;

use CBLib\Application\Application;
use CB\Plugin\Connect\Provider;
use CB\Plugin\Connect\Profile;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Xml\SimpleXMLElement;
use GuzzleHttp\Client;
use CBLib\Language\CBTxt;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use Exception;

defined('CBLIB') or die();

class TwitterProvider extends Provider
{
	/** @var array  */
	protected $urls			=	array(	'base'		=>	'https://api.twitter.com/1.1',
										'authorize'	=>	'https://api.twitter.com/oauth/authenticate',
										'request'	=>	'https://api.twitter.com/oauth/request_token',
										'access'	=>	'https://api.twitter.com/oauth/access_token'
									);

	/**
	 * Authenticates a Twitter user (redirect and token exchange)
	 * https://dev.twitter.com/web/sign-in/implementing
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$verifier				=	Application::Input()->get( 'oauth_verifier', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'twitter.oauth_token', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'twitter.oauth_token', null, GetterInterface::STRING ) != Application::Input()->get( 'oauth_token', null, GetterInterface::STRING ) ) ) {
			$verifier			=	null;
		}

		if ( $verifier ) {
			$this->session()->set( 'twitter.oauth_verifier', $verifier );

			$client				=	new Client( array( 'defaults' => array( 'auth' => 'oauth' ) ) );

			$oauth				=	new Oauth1( array(	'consumer_key'		=>	$this->clientId,
														'consumer_secret'	=>	$this->clientSecret,
														'token'				=>	$this->session()->get( 'twitter.oauth_token', null, GetterInterface::STRING )
													));

			$client->getEmitter()->attach( $oauth );

			try {
				$result			=	$client->post( $this->urls['access'], array( 'body' => array( 'oauth_verifier' => $verifier ) ) );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'errors.0.message', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_TOKEN_ERROR', 'Failed to exchange token. Error: [error]', array( '[error]' => $response->get( 'errors.0.message', null, GetterInterface::STRING ) . ' (' . $response->get( 'errors.0.code', null, GetterInterface::STRING ) . ')' ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'oauth_token', null, GetterInterface::STRING ) ) {
				if ( $response->get( 'user_id', null, GetterInterface::STRING ) ) {
					$this->session()->set( 'twitter.id', $response->get( 'user_id', null, GetterInterface::STRING ) );
				}

				$this->session()->set( 'twitter.oauth_access_token', $response->get( 'oauth_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'twitter.oauth_access_secret', $response->get( 'oauth_token_secret', null, GetterInterface::STRING ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$client				=	new Client( array( 'defaults' => array( 'auth' => 'oauth' ) ) );

			$oauth				=	new Oauth1( array(	'consumer_key'		=>	$this->clientId,
														'consumer_secret'	=>	$this->clientSecret,
														'callback'			=>	$this->callback
													));

			$client->getEmitter()->attach( $oauth );

			try {
				$result			=	$client->post( $this->urls['request'] );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'errors.0.message', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_REQUEST_TOKEN_ERROR', 'Failed to request token. Error: [error]', array( '[error]' => $response->get( 'errors.0.message', null, GetterInterface::STRING ) . ' (' . $response->get( 'errors.0.code', null, GetterInterface::STRING ) . ')' ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( $response instanceof ParamsInterface ) {
				if ( $response->get( 'oauth_callback_confirmed', false, GetterInterface::BOOLEAN ) !== true ) {
					throw new Exception( CBTxt::T( 'Callback failed to confirm.' ) );
				}

				if ( $response->get( 'oauth_token', null, GetterInterface::STRING ) ) {
					$this->session()->set( 'twitter.oauth_token', $response->get( 'oauth_token', null, GetterInterface::STRING ) );
					$this->session()->set( 'twitter.oauth_token_secret', $response->get( 'oauth_token_secret', null, GetterInterface::STRING ) );
				}

				cbRedirect( $this->urls['authorize'] . '?oauth_token=' . urlencode( $response->get( 'oauth_token', null, GetterInterface::STRING ) ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to request callback.' ) );
			}
		}
	}

	/**
	 * Checks if access token exists and ensures it's not expired
	 *
	 * @return bool
	 */
	public function authorized()
	{
		return ( $this->session()->get( 'twitter.oauth_access_token', null, GetterInterface::STRING ) != '' );
	}

	/**
	 * Request current users Twitter profile
	 * https://dev.twitter.com/rest/reference/get/account/verify_credentials
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/account/verify_credentials.json', 'GET', array( 'include_email' => 'true' ) );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap			=	array(	'id'		=>	'id',
											'username'	=>	'screen_name',
											'name'		=>	'name',
											'email'		=>	'email',
											'avatar'	=>	'profile_image_url',
											'canvas'	=>	'profile_banner_url'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'twitter.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Twitter API request
	 * https://dev.twitter.com/rest/public
	 *
	 * @param string $api
	 * @param string $type
	 * @param array  $params
	 * @param array  $headers
	 * @return string|Registry|SimpleXMLElement
	 * @throws Exception
	 */
	public function api( $api, $type = 'GET', $params = array(), $headers = array() )
	{
		$client								=	new Client( array( 'defaults' => array( 'auth' => 'oauth' ) ) );

		$oauthParams						=	array(	'consumer_key'		=>	$this->clientId,
														'consumer_secret'	=>	$this->clientSecret
													);

		if ( $this->session()->get( 'twitter.oauth_access_token', null, GetterInterface::STRING ) ) {
			$oauthParams['token']			=	$this->session()->get( 'twitter.oauth_access_token', null, GetterInterface::STRING );
			$oauthParams['token_secret']	=	$this->session()->get( 'twitter.oauth_access_secret', null, GetterInterface::STRING );
		}

		$oauth								=	new Oauth1( $oauthParams );

		$client->getEmitter()->attach( $oauth );

		$options							=	array();

		if ( $headers ) {
			$options['headers']				=	$headers;
		}

		if ( $params ) {
			if ( $type == 'POST' ) {
				$options['body']			=	$params;
			} else {
				$options['query']			=	$params;
			}
		}

		try {
			if ( $type == 'POST' ) {
				$result						=	$client->post( $this->urls['base'] . $api, $options );
			} else {
				$result						=	$client->get( $this->urls['base'] . $api, $options );
			}
		} catch( ClientException $e ) {
			$response						=	$this->response( $e->getResponse() );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'errors.0.message', null, GetterInterface::STRING ) ) {
				$error						=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'errors.0.message', null, GetterInterface::STRING ) . ' (' . $response->get( 'errors.0.code', null, GetterInterface::STRING ) . ')' ) );
			} else {
				$error						=	$e->getMessage();
			}

			$this->debug( $e );

			throw new Exception( $error );
		}

		$response							=	$this->response( $result );

		$this->debug( $result, $response );

		return $response;
	}
}
