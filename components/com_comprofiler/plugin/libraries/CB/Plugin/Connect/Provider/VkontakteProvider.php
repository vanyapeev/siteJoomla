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

class VkontakteProvider extends Provider
{
	/**
	 * https://vk.com/dev/permissions
	 *
	 * @var array
	 */
	protected $scope		=	array( 'email' );
	/** @var array  */
	protected $fields		=	array( 'id', 'displayName', 'nickname', 'image', 'cover', 'emails' );
	/** @var array  */
	protected $urls			=	array(	'base'		=>	'https://api.vk.com/method',
										'authorize'	=>	'https://oauth.vk.com/authorize',
										'access'	=>	'https://oauth.vk.com/access_token'
									);

	/**
	 * Authenticates a Vkontakte user (redirect and token exchange)
	 * https://vk.com/dev/auth_sites
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( $code ) {
			$this->session()->set( 'vkontakte.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'code'			=>	$code,
																'redirect_uri'	=>	$this->callback
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
				if ( $response->get( 'user_id', null, GetterInterface::STRING ) ) {
					$this->session()->set( 'vkontakte.id', $response->get( 'user_id', null, GetterInterface::STRING ) );
				}

				if ( $response->get( 'email', null, GetterInterface::STRING ) ) {
					$this->session()->set( 'vkontakte.email', $response->get( 'email', null, GetterInterface::STRING ) );
				}

				$this->session()->set( 'vkontakte.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'vkontakte.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	( $this->scope ? '&scope=' . urlencode( implode( ',', $this->scope ) ) : null )
								.	'&redirect_uri=' . urlencode( $this->callback )
								.	'&response_type=code';

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

		if ( $this->session()->get( 'vkontakte.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'vkontakte.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Vkontakte profile
	 * https://vk.com/dev/users.get
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

		$response				=	$this->api( '/users.get', 'GET', $params );

		if ( $response instanceof ParamsInterface ) {
			$response			=	$response->subTree( 'response.0' );

			$fieldMap			=	array(	'id'			=>	'uid',
											'username'		=>	'nickname',
											'firstname'		=>	'first_name',
											'lastname'		=>	'last_name',
											'avatar'		=>	'photo_max_orig'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'vkontakte.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			if ( $this->session()->get( 'vkontakte.email', null, GetterInterface::STRING ) ) {
				$profile->set( 'email', $this->session()->get( 'vkontakte.email', null, GetterInterface::STRING ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Vkontakte API request
	 * https://vk.com/dev/methods
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

		if ( $this->session()->get( 'vkontakte.access_token', null, GetterInterface::STRING ) ) {
			$params['access_token']		=	$this->session()->get( 'vkontakte.access_token', null, GetterInterface::STRING );
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
