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
use CBLib\Xml\SimpleXMLElement;
use GuzzleHttp\Client;
use CBLib\Language\CBTxt;
use GuzzleHttp\Exception\ClientException;
use CBLib\Registry\ParamsInterface;
use Exception;

defined('CBLIB') or die();

class VimeoProvider extends Provider
{
	/**
	 * https://developer.vimeo.com/api/authentication#supported-scopes
	 *
	 * @var array
	 */
	protected $scope			=	array( 'public' );
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://api.vimeo.com',
											'authorize'	=>	'https://api.vimeo.com/oauth/authorize',
											'access'	=>	'https://api.vimeo.com/oauth/access_token'
										);

	/**
	 * Authenticates a Vimeo user (redirect and token exchange)
	 * https://developer.vimeo.com/api/authentication
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'vimeo.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'vimeo.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'vimeo.code', $code );

			$client				=	new Client( array( 'defaults' => array( 'auth' => array( $this->clientId, $this->clientSecret ) ) ) );

			$options			=	array(	'body'	=>	array(	'redirect_uri'	=>	$this->callback,
																'code'			=>	$code,
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
				if ( $response->has( 'user' ) ) {
					$this->session()->set( 'vimeo.user', $response->subTree( 'user' )->asArray() );
				}

				$this->session()->set( 'vimeo.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'vimeo.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	( $this->scope ? '&scope=' . urlencode( implode( ' ', $this->scope ) ) : null )
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	'&response_type=code'
								.	'&state=' . urlencode( $state );

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
		return ( $this->session()->get( 'vimeo.access_token', null, GetterInterface::STRING ) != '' );
	}

	/**
	 * Request current users Vimeo profile
	 * https://developer.vimeo.com/api/endpoints/users#GET/users/{user_id}
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		if ( $this->session()->has( 'vimeo.user' ) ) {
			$response			=	$this->session()->subTree( 'vimeo.user' );
		} else {
			$response			=	$this->api( '/me' );
		}

		if ( $response instanceof ParamsInterface ) {
			$profile->set( 'id', str_replace( '/users/', '', $response->get( 'uri', null, GetterInterface::STRING ) ) );
			$profile->set( 'name', $response->get( 'name', null, GetterInterface::STRING ) );

			$username			=	str_replace( 'https://vimeo.com/', '', $response->get( 'link', null, GetterInterface::STRING ) );

			if ( strpos( $username, 'user' ) === false ) {
				$profile->set( 'username', $username );
			}

			$pictures			=	$response->subTree( 'pictures' );

			if ( $pictures->has( 'sizes' ) ) {
				$pictures		=	$pictures->subTree( 'sizes' );
			}

			if ( $pictures->count() ) {
				$profile->set( 'avatar', $pictures->get( ( $pictures->count() - 1 ) . '.link', null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'vimeo.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Vimeo API request
	 * https://developer.vimeo.com/api/endpoints
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

		if ( $this->session()->get( 'vimeo.access_token', null, GetterInterface::STRING ) ) {
			$headers['Authorization']	=	'Bearer ' . $this->session()->get( 'vimeo.access_token', null, GetterInterface::STRING );
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
