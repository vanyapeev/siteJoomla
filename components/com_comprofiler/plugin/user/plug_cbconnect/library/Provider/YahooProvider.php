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

class YahooProvider extends Provider
{
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://query.yahooapis.com/v1/yql',
											'authorize'	=>	'https://api.login.yahoo.com/oauth2/request_auth',
											'access'	=>	'https://api.login.yahoo.com/oauth2/get_token'
										);

	/**
	 * Authenticates a Yahoo user (redirect and token exchange)
	 * https://developer.yahoo.com/oauth2/guide/flows_authcode/
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'yahoo.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'yahoo.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'yahoo.code', $code );

			$client				=	new Client( array( 'defaults' => array( 'auth' => array( $this->clientId, $this->clientSecret ) ) ) );

			$options			=	array(	'body'	=>	array(	'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'redirect_uri'	=>	$this->callback,
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
				if ( $response->get( 'xoauth_yahoo_guid', null, GetterInterface::STRING ) ) {
					$this->session()->set( 'yahoo.id', $response->get( 'xoauth_yahoo_guid', null, GetterInterface::STRING ) );
				}

				$this->session()->set( 'yahoo.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'yahoo.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'yahoo.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
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
		$expired			=	true;

		if ( $this->session()->get( 'yahoo.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'yahoo.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Yahoo profile
	 * https://developer.yahoo.com/social/rest_api_guide/extended-profile-resource.html
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		$response				=	$this->api( 'select * from social.profile where guid=me' );

		if ( $response instanceof ParamsInterface ) {
			$response			=	$response->subTree( 'query.results.profile' );

			$fieldMap			=	array(	'id'			=>	'guid',
											'username'		=>	'nickname',
											'firstname'		=>	'givenName',
											'lastname'		=>	'familyName',
											'avatar'		=>	'image.imageUrl'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'yahoo.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			if ( $response->get( 'emails', null, GetterInterface::RAW ) ) {
				if ( $response->get( 'emails.handle', null, GetterInterface::STRING ) ) {
					$profile->set( 'email', $response->get( 'emails.handle', null, GetterInterface::STRING ) );
				} else {
					foreach ( $response->subTree( 'emails' ) as $email ) {
						/** @var Registry $email */
						if ( ! $email->get( 'primary', null, GetterInterface::STRING ) ) {
							continue;
						}

						$profile->set( 'email', $email->get( 'handle', null, GetterInterface::STRING ) );
						break;
					}
				}
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Yahoo API request
	 * https://developer.yahoo.com/social/rest_api_guide/ysp_api_book.html
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

		if ( $this->session()->get( 'yahoo.access_token', null, GetterInterface::STRING ) ) {
			$headers['Authorization']	=	'Bearer ' . $this->session()->get( 'yahoo.access_token', null, GetterInterface::STRING );
		}

		$options						=	array();

		if ( $headers ) {
			$options['headers']			=	$headers;
		}

		$params['q']					=	$api;

		if ( ! isset( $params['format'] ) ) {
			$params['format']			=	'json';
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
				$result					=	$client->post( $this->urls['base'], $options );
			} else {
				$result					=	$client->get( $this->urls['base'], $options );
			}
		} catch( ClientException $e ) {
			$response					=	$this->response( $e->getResponse() );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'error_description', null, GetterInterface::STRING ) ) {
				$error					=	CBTxt::T( 'FAILED_API_REQUEST_ERROR', 'Failed API request [api]. Error: [error]', array( '[api]' => $api, '[error]' => $response->get( 'error_description', null, GetterInterface::STRING ) ) );
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
