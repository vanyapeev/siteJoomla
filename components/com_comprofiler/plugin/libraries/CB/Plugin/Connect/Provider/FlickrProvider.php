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

class FlickrProvider extends Provider
{
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://api.flickr.com/services/rest',
											'authorize'	=>	'https://www.flickr.com/services/oauth/authorize',
											'request'	=>	'https://www.flickr.com/services/oauth/request_token',
											'access'	=>	'https://www.flickr.com/services/oauth/access_token'
										);

	/**
	 * Authenticates a Flickr user (redirect and token exchange)
	 * https://www.flickr.com/services/api/auth.oauth.html
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$verifier				=	Application::Input()->get( 'oauth_verifier', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'flickr.oauth_token', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'flickr.oauth_token', null, GetterInterface::STRING ) != Application::Input()->get( 'oauth_token', null, GetterInterface::STRING ) ) ) {
			$verifier			=	null;
		}

		if ( $verifier ) {
			$this->session()->set( 'flickr.oauth_verifier', $verifier );

			$client				=	new Client( array( 'defaults' => array( 'auth' => 'oauth' ) ) );

			$oauth				=	new Oauth1( array(	'request_method'	=>	'query',
														'consumer_key'		=>	$this->clientId,
														'consumer_secret'	=>	$this->clientSecret,
														'token'				=>	$this->session()->get( 'flickr.oauth_token', null, GetterInterface::STRING ),
														'token_secret'		=>	$this->session()->get( 'flickr.oauth_token_secret', null, GetterInterface::STRING ),
														'verifier'			=>	$verifier
													));

			$client->getEmitter()->attach( $oauth );

			try {
				$result			=	$client->get( $this->urls['access'] );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'oauth_problem', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_TOKEN_ERROR', 'Failed to exchange token. Error: [error]', array( '[error]' => $response->get( 'oauth_problem', null, GetterInterface::STRING ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'oauth_token', null, GetterInterface::STRING ) ) {
				if ( $response->get( 'user_nsid', null, GetterInterface::STRING ) ) {
					$this->session()->set( 'flickr.id', $response->get( 'user_nsid', null, GetterInterface::STRING ) );
					$this->session()->set( 'flickr.username', $response->get( 'username', null, GetterInterface::STRING ) );
					$this->session()->set( 'flickr.name', $response->get( 'fullname', null, GetterInterface::STRING ) );
				}

				$this->session()->set( 'flickr.oauth_access_token', $response->get( 'oauth_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'flickr.oauth_access_secret', $response->get( 'oauth_token_secret', null, GetterInterface::STRING ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$client				=	new Client( array( 'defaults' => array( 'auth' => 'oauth' ) ) );

			$oauth				=	new Oauth1( array(	'request_method'	=>	'query',
														'consumer_key'		=>	$this->clientId,
														'consumer_secret'	=>	$this->clientSecret,
														'callback'			=>	$this->callback
													));

			$client->getEmitter()->attach( $oauth );

			try {
				$result			=	$client->get( $this->urls['request'] );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'oauth_problem', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_REQUEST_TOKEN_ERROR', 'Failed to request token. Error: [error]', array( '[error]' => $response->get( 'oauth_problem', null, GetterInterface::STRING ) ) );
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
					$this->session()->set( 'flickr.oauth_token', $response->get( 'oauth_token', null, GetterInterface::STRING ) );
					$this->session()->set( 'flickr.oauth_token_secret', $response->get( 'oauth_token_secret', null, GetterInterface::STRING ) );
				}

				cbRedirect( $this->urls['authorize'] . '?oauth_token=' . urlencode( $response->get( 'oauth_token', null, GetterInterface::STRING ) ) . '&perms=read' );
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
		return ( $this->session()->get( 'flickr.oauth_access_token', null, GetterInterface::STRING ) != '' );
	}

	/**
	 * Request current users Flickr profile
	 * https://www.flickr.com/services/api/flickr.profile.getProfile.html
	 * https://www.flickr.com/services/api/flickr.people.getInfo.html
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();
		$params					=	array(	'method'			=>	'flickr.profile.getProfile',
											'nojsoncallback'	=>	1,
											'format'			=>	'json'
										);

		if ( $this->session()->get( 'flickr.id', null, GetterInterface::STRING ) ) {
			$params['user_id']	=	$this->session()->get( 'flickr.id', null, GetterInterface::STRING );
		}

		$response				=	$this->api( null, 'GET', $params );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap			=	array(	'id'		=>	'profile.id',
											'firstname'	=>	'profile.first_name',
											'lastname'	=>	'profile.last_name',
											'email'		=>	'profile.email'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'flickr.id', $profile->get( 'id', null, GetterInterface::STRING ) );

				$params			=	array(	'method'			=>	'flickr.people.getInfo',
											'user_id'			=>	$profile->get( 'id', null, GetterInterface::STRING ),
											'nojsoncallback'	=>	1,
											'format'			=>	'json'
										);

				$person			=	$this->api( null, 'GET', $params );

				if ( $person instanceof ParamsInterface ) {
					$fieldMap	=	array(	'username'	=>	'person.username._content',
											'name'		=>	'person.realname._content'
										);

					foreach ( $fieldMap as $cbField => $pField ) {
						$profile->set( $cbField, $person->get( $pField, null, GetterInterface::STRING ) );
					}

					if ( $person->get( 'person.iconserver', 0, GetterInterface::INT ) > 0 ) {
						$profile->set( 'avatar', 'http://farm' . $person->get( 'person.iconfarm', 0, GetterInterface::INT ) . '.staticflickr.com/' . $person->get( 'person.iconserver', 0, GetterInterface::INT ) . '/buddyicons/' . $person->get( 'person.id', null, GetterInterface::STRING ) . '_m.jpg' );
					}

					$response->load( $person );
				}
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Flickr API request
	 * https://www.flickr.com/services/api/
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

		if ( $this->session()->get( 'flickr.oauth_access_token', null, GetterInterface::STRING ) ) {
			$oauthParams['token']			=	$this->session()->get( 'flickr.oauth_access_token', null, GetterInterface::STRING );
			$oauthParams['token_secret']	=	$this->session()->get( 'flickr.oauth_access_secret', null, GetterInterface::STRING );
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

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'err.msg', null, GetterInterface::STRING ) ) {
				$error						=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'err.msg', null, GetterInterface::STRING ) . ' (' . $response->get( 'err.code', null, GetterInterface::STRING ) . ')' ) );
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
