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

class AmazonProvider extends Provider
{
	/**
	 * https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf#page=6
	 *
	 * @var array
	 */
	protected $scope			=	array( 'profile' );
	/** @var array  */
	protected $urls				=	array(	'base'		=>	'https://api.amazon.com',
											'authorize'	=>	'https://www.amazon.com/ap/oa',
											'access'	=>	'https://api.amazon.com/auth/o2/token'
										);

	/**
	 * Authenticates a Amazon user (redirect and token exchange)
	 * https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf#page=28
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'amazon.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'amazon.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'amazon.code', $code );

			$client				=	new Client();

			$options			=	array(	'body'	=>	array(	'grant_type'	=>	'authorization_code',
																'code'			=>	$code,
																'redirect_uri'	=>	$this->callback,
																'client_id'		=>	$this->clientId,
																'client_secret'	=>	$this->clientSecret
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
				$this->session()->set( 'amazon.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'amazon.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'amazon.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	( $this->scope ? '&scope=' . urlencode( implode( ' ', $this->scope ) ) : null )
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

		if ( $this->session()->get( 'amazon.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'amazon.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Amazon profile
	 * https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf#page=6
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile				=	new Profile();

		$response				=	$this->api( '/user/profile' );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap			=	array(	'id'			=>	'user_id',
											'name'			=>	'name',
											'email'			=>	'email'
										);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'amazon.id', $profile->get( 'id', null, GetterInterface::STRING ) );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Amazon API request
	 * https://images-na.ssl-images-amazon.com/images/G/01/lwa/dev/docs/website-developer-guide._TTH_.pdf
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

		if ( $this->session()->get( 'amazon.access_token', null, GetterInterface::STRING ) ) {
			$params['access_token']		=	$this->session()->get( 'amazon.access_token', null, GetterInterface::STRING );
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
