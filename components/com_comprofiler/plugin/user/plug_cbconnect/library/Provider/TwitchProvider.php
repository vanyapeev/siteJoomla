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

class TwitchProvider extends Provider
{
	/**
	 * https://github.com/justintv/Twitch-API/blob/master/authentication.md#scopes
	 *
	 * @var array
	 */
	protected $scope			=	array( 'user_read' );
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://api.twitch.tv/kraken',
											'authorize'	=>	'https://api.twitch.tv/kraken/oauth2/authorize',
											'access'	=>	'https://api.twitch.tv/kraken/oauth2/token'
										);

	/**
	 * Authenticates a Twitch user (redirect and token exchange)
	 * https://github.com/justintv/Twitch-API/blob/master/authentication.md
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'twitch.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'twitch.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'twitch.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'grant_type'	=>	'authorization_code',
																'redirect_uri'	=>	$this->callback,
																'code'			=>	$code,
																'state'			=>	$this->session()->get( 'twitch.state', null, GetterInterface::STRING )
															)
									);

			try {
				$result			=	$client->post( $this->urls['access'], $options );
			} catch( ClientException $e ) {
				$response		=	$this->response( $e->getResponse() );

				if ( ( $response instanceof ParamsInterface ) && $response->get( 'message', null, GetterInterface::STRING ) ) {
					$error		=	CBTxt::T( 'FAILED_EXCHANGE_CODE_ERROR', 'Failed to exchange code. Error: [error]', array( '[error]' => $response->get( 'message', null, GetterInterface::STRING ) ) );
				} else {
					$error		=	$e->getMessage();
				}

				$this->debug( $e );

				throw new Exception( $error );
			}

			$response			=	$this->response( $result );

			$this->debug( $result, $response );

			if ( ( $response instanceof ParamsInterface ) && $response->get( 'access_token', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'twitch.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'twitch.state', $state );

			$url				=	$this->urls['authorize']
								.	'?response_type=code'
								.	'&client_id=' . urlencode( $this->clientId )
								.	'&redirect_uri=' . urlencode( $this->callback )
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
		return ( $this->session()->get( 'twitch.access_token', null, GetterInterface::STRING ) != '' );
	}

	/**
	 * Request current users Twitch profile
	 * https://github.com/justintv/Twitch-API/blob/master/v3_resources/users.md#get-user
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/user' );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap			=	array(	'id'			=>	'_id',
											'username'		=>	'display_name',
											'name'			=>	'name',
											'email'			=>	'email',
											'avatar'		=>	'logo'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'twitch.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Twitch API request
	 * https://github.com/justintv/Twitch-API
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

		if ( $this->session()->get( 'twitch.access_token', null, GetterInterface::STRING ) ) {
			$headers['Authorization']	=	'OAuth ' . $this->session()->get( 'twitch.access_token', null, GetterInterface::STRING );
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
