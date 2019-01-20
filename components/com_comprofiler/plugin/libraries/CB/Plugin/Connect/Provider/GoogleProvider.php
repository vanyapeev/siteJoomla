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
use Exception;

defined('CBLIB') or die();

class GoogleProvider extends Provider
{
	/**
	 * https://developers.google.com/+/web/api/rest/oauth#authorization-scopes
	 * https://developers.google.com/identity/protocols/googlescopes
	 *
	 * @var array
	 */
	protected $scope			=	array( 'profile', 'email' );
	/** @var array  */
	protected $fields			=	array( 'id', 'displayName', 'nickname', 'image', 'cover', 'emails' );
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://www.googleapis.com/plus/v1',
											'authorize'	=>	'https://accounts.google.com/o/oauth2/v2/auth',
											'access'	=>	'https://www.googleapis.com/oauth2/v4/token'
										);

	/**
	 * Authenticates a Google user (redirect and token exchange)
	 * https://developers.google.com/identity/protocols/OAuth2WebServer
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'google.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'google.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'google.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'code'			=>	$code,
																'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'redirect_uri'	=>	$this->callback,
																'grant_type'	=>	'authorization_code'
															)
									);

			try {
				$result			=	$client->post( $this->urls['access'], $options );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'error_description', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'error_description', null, GetterInterface::STRING ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'access_token', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'google.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'google.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'google.state', $state );

			$url				=	$this->urls['authorize']
								.	'?response_type=code'
								.	'&client_id=' . urlencode( $this->clientId )
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	( $this->scope ? '&scope=' . urlencode( implode( ' ', $this->scope ) ) : null )
								.	'&state=' . urlencode( $state )
								.	'&access_type=online';

			cbRedirect( $url );
		}
	}

	/**
	 * Checks if access token exists and ensures it's not expired
	 *
	 * @return bool
	 */
	public function authorized()
	{
		$expired			=	true;

		if ( $this->session()->get( 'google.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'google.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Google profile
	 * https://developers.google.com/+/web/api/rest/latest/people
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		if ( ! $fields ) {
			$fields				=	$this->fields;
		}

		$params					=	array();

		if ( $fields ) {
			$params['fields']	=	implode( ',', $fields );
		}

		$response				=	$this->api( '/people/me', 'GET', $params );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap			=	array(	'id'			=>	'id',
											'username'		=>	'nickname',
											'name'			=>	'displayName',
											'firstname'		=>	'name.givenName',
											'middlename'	=>	'name.middleName',
											'lastname'		=>	'name.familyName',
											'email'			=>	'emails.0.value',
											'avatar'		=>	'image.url',
											'canvas'		=>	'cover.coverPhoto.url'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'google.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'avatar', null, GetterInterface::STRING ) ) {
				$profile->set( 'avatar', rtrim( preg_replace( '/sz=\d+(&|)/', '', $profile->get( 'avatar', null, GetterInterface::STRING ) ), '?&' ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Google API request
	 * https://developers.google.com/+/web/api/rest/latest/
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
		$client							=	new Client();

		if ( $this->session()->get( 'google.access_token', null, GetterInterface::STRING ) ) {
			$params['access_token']		=	$this->session()->get( 'google.access_token', null, GetterInterface::STRING );
		}

		$options						=	array();

		if ( $headers ) {
			$options['headers']			=	$headers;
		}

		if ( $params ) {
			if ( $type == 'POST' ) {
				$options['body']		=	$params;
			} else {
				$options['query']		=	$params;
			}
		}

		try {
			if ( $type == 'POST' ) {
				$result					=	$client->post( $this->urls['base'] . $api, $options );
			} else {
				$result					=	$client->get( $this->urls['base'] . $api, $options );
			}
		} catch( ClientException $e ) {
			$response					=	$this->response( $e->getResponse() );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'error.message', null, GetterInterface::STRING ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'error.message', null, GetterInterface::STRING ) ) );
			} else {
				$error					=	$e->getMessage();
			}

			$this->debug( $e );

			throw new Exception( $error );
		}

		$response						=	$this->response( $result );

		$this->debug( $result, $response );

		return $response;
	}
}
