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

class WindowsLiveProvider extends Provider
{
	/**
	 * https://msdn.microsoft.com/en-us/library/hh243646.aspx
	 *
	 * @var array
	 */
	protected $scope			=	array( 'wl.basic', 'wl.emails', 'wl.signin' );
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://apis.live.net/v5.0',
											'authorize'	=>	'https://login.live.com/oauth20_authorize.srf',
											'access'	=>	'https://login.live.com/oauth20_token.srf'
										);

	/**
	 * Authenticates a WindowsLive user (redirect and token exchange)
	 * https://msdn.microsoft.com/en-us/library/hh243647.aspx
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'windowslive.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'windowslive.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'windowslive.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'grant_type'	=>	'authorization_code',
																'redirect_uri'	=>	$this->callback,
																'code'			=>	$code,
															)
									);

			try {
				$result			=	$client->post( $this->urls['access'], $options );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'error.message', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'error.message', null, GetterInterface::STRING ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'access_token', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'windowslive.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'windowslive.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'windowslive.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	'&display=page'
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	'&response_type=code'
								.	( $this->scope ? '&scope=' . urlencode( implode( ' ', $this->scope ) ) : null )
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
		$expired			=	true;

		if ( $this->session()->get( 'windowslive.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'windowslive.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users WindowsLive profile
	 * https://msdn.microsoft.com/en-us/library/hh243648.aspx#user
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/me' );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap			=	array(	'id'			=>	'id',
											'name'			=>	'name',
											'firstname'		=>	'first_name',
											'lastname'		=>	'last_name',
											'email'			=>	'emails.preferred'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'windowslive.id', $profile->get( 'id', null, GetterInterface::STRING ) );

				if ( $this->session()->get( 'windowslive.access_token', null, GetterInterface::STRING ) ) {
					$profile->set( 'avatar', $this->urls['base'] . '/me/picture?size=large&access_token=' . $this->session()->get( 'windowslive.access_token', null, GetterInterface::STRING ) );
				}
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom WindowsLive API request
	 * https://msdn.microsoft.com/en-us/library/hh243648.aspx
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

		if ( $this->session()->get( 'windowslive.access_token', null, GetterInterface::STRING ) ) {
			$params['access_token']		=	$this->session()->get( 'windowslive.access_token', null, GetterInterface::STRING );
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

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'message', null, GetterInterface::STRING ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'message', null, GetterInterface::STRING ) ) );
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
