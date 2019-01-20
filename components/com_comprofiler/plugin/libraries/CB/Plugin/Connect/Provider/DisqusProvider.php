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

class DisqusProvider extends Provider
{
	/**
	 * https://disqus.com/api/docs/permissions/
	 *
	 * @var array
	 */
	protected $scope			=	array( 'read', 'email' );
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://disqus.com/api/3.0',
											'authorize'	=>	'https://disqus.com/api/oauth/2.0/authorize',
											'access'	=>	'https://disqus.com/api/oauth/2.0/access_token/'
										);

	/**
	 * Authenticates a Disqus user (redirect and token exchange)
	 * https://disqus.com/api/docs/auth/
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'disqus.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'disqus.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'disqus.code', $code );

			$client				=	new Client( array( 'allow_redirects' => false ) );

			$options			=	array(	'body'	=>	array(	'grant_type'	=>	'authorization_code',
																'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret,
																'redirect_uri'	=>	$this->callback,
																'code'			=>	$code
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
				$this->session()->set( 'disqus.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'disqus.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'disqus.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	( $this->scope ? '&scope=' . urlencode( implode( ',', $this->scope ) ) : null )
								.	'&response_type=code'
								.	'&redirect_uri=' . urlencode( $this->callback )
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

		if ( $this->session()->get( 'disqus.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'disqus.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Disqus profile
	 * https://disqus.com/api/docs/users/details/
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/users/details.json' );

		if ( $response instanceof ParamsInterface ) {
			$response			=	$response->subTree( 'response' );

			$fieldMap			=	array(	'id'			=>	'id',
											'username'		=>	'username',
											'name'			=>	'name',
											'email'			=>	'email',
											'avatar'		=>	'avatar.permalink'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'disqus.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Disqus API request
	 * https://disqus.com/api/docs/requests/
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

		$params['api_key']				=	$this->clientId;
		$params['api_secret']			=	$this->clientSecret;

		if ( $this->session()->get( 'disqus.access_token', null, GetterInterface::STRING ) ) {
			$params['access_token']		=	$this->session()->get( 'disqus.access_token', null, GetterInterface::STRING );
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
