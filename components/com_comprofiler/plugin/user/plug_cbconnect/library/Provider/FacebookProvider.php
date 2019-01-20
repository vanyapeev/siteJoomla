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

class FacebookProvider extends Provider
{
	/**
	 * https://developers.facebook.com/docs/facebook-login/permissions
	 *
	 * @var array
	 */
	protected $scope		=	array( 'email', 'public_profile' );
	/** @var array  */
	protected $fields		=	array( 'id', 'name', 'first_name', 'middle_name', 'last_name', 'email', 'cover' );
	/** @var array  */
	protected $urls			=	array(	'base'		=>	'https://graph.facebook.com/v2.8',
										'authorize'	=>	'https://www.facebook.com/v2.8/dialog/oauth',
										'access'	=>	'https://graph.facebook.com/v2.8/oauth/access_token'
									);

	/**
	 * Authenticates a Facebook user (redirect and token exchange)
	 * https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow
	 *
	 * @throws Exception
	 */
	public function authenticate()
	{
		$code					=	Application::Input()->get( 'code', null, GetterInterface::STRING );

		if ( ( ! $this->session()->get( 'facebook.state', null, GetterInterface::STRING ) ) || ( $this->session()->get( 'facebook.state', null, GetterInterface::STRING ) != Application::Input()->get( 'state', null, GetterInterface::STRING ) ) ) {
			$code				=	null;
		}

		if ( $code ) {
			$this->session()->set( 'facebook.code', $code );

			$client				=	new Client();

			$options			=	array(	'query'	=>	array(	'client_id'		=>	$this->clientId,
																'redirect_uri'	=>	$this->callback,
																'client_secret'	=>	$this->clientSecret,
																'code'			=>	$code
															)
									);

			try {
				$result			=	$client->get( $this->urls['access'], $options );
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
				$this->session()->set( 'facebook.access_token', $response->get( 'access_token', null, GetterInterface::STRING ) );
				$this->session()->set( 'facebook.expires', Application::Date( 'now', 'UTC' )->add( $response->get( 'expires_in', 0, GetterInterface::INT ) . ' SECONDS' )->getTimestamp() );
			} else {
				throw new Exception( CBTxt::T( 'Failed to retrieve access token.' ) );
			}
		} elseif ( ! $this->authorized() ) {
			$state				=	uniqid();

			$this->session()->set( 'facebook.state', $state );

			$url				=	$this->urls['authorize']
								.	'?client_id=' . urlencode( $this->clientId )
								.	'&state=' . urlencode( $state )
								.	( $this->scope ? '&scope=' . urlencode( implode( ',', $this->scope ) ) : null )
								.	'&redirect_uri=' . urlencode( $this->callback );

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

		if ( $this->session()->get( 'facebook.access_token', null, GetterInterface::STRING ) ) {
			$expires		=	$this->session()->get( 'facebook.expires', 0, GetterInterface::INT );

			if ( $expires ) {
				$expired	=	( Application::Date( 'now', 'UTC' )->getDateTime() > Application::Date( $expires, 'UTC' )->getDateTime() );
			}
		}

		return ( ! $expired );
	}

	/**
	 * Request current users Facebook profile
	 * https://developers.facebook.com/docs/graph-api/reference/user/
	 *
	 * @param array $fields
	 * @return Profile
	 * @throws Exception
	 */
	public function profile( $fields = array() )
	{
		$profile					=	new Profile();

		if ( ! $fields ) {
			$fields					=	$this->fields;
		}

		$params						=	array();

		if ( $fields ) {
			$ignore					=	array( 'avatar' );

			foreach ( $fields as $k => $v ) {
				if ( ! in_array( $v, $ignore ) ) {
					continue;
				}

				unset( $fields[$k] );
			}

			$params['fields']		=	implode( ',', $fields );
		}

		$response					=	$this->api( '/me', 'GET', $params );

		if ( $response instanceof ParamsInterface ) {
			$fieldMap				=	array(	'id'			=>	'id',
												'name'			=>	'name',
												'firstname'		=>	'first_name',
												'middlename'	=>	'middle_name',
												'lastname'		=>	'last_name',
												'email'			=>	'email',
												'canvas'		=>	'cover.source'
											);

			foreach ( $fieldMap as $cbField => $pField ) {
				$profile->set( $cbField, $response->get( $pField, null, GetterInterface::STRING ) );
			}

			if ( $profile->get( 'id', null, GetterInterface::STRING ) ) {
				$this->session()->set( 'facebook.id', $profile->get( 'id', null, GetterInterface::STRING ) );

				$profile->set( 'avatar', $this->urls['base'] . '/' . $profile->get( 'id', null, GetterInterface::STRING ) . '/picture?height=800&width=800&type=large' );
			}

			if ( $response->get( 'education', null, GetterInterface::RAW ) && in_array( 'education', $fields ) ) {
				$schools			=	array();

				foreach ( $response->subTree( 'education' ) as $school ) {
					/** @var Registry $school */
					$schools[]		=	$school->get( 'year.name', null, GetterInterface::STRING ) . ' - ' . $school->get( 'type', null, GetterInterface::STRING );
				}

				$profile->set( 'education', $schools );
			}

			if ( $response->get( 'languages', null, GetterInterface::RAW ) && in_array( 'languages', $fields ) ) {
				$languages			=	array();

				foreach ( $response->subTree( 'languages' ) as $language ) {
					/** @var Registry $language */
					$languages[]	=	$language->get( 'name', null, GetterInterface::STRING );
				}

				$profile->set( 'languages', $languages );
			}

			if ( $response->get( 'favorite_athletes', null, GetterInterface::RAW ) && in_array( 'favorite_athletes', $fields ) ) {
				$athletes			=	array();

				foreach ( $response->subTree( 'favorite_athletes' ) as $athlete ) {
					/** @var Registry $athlete */
					$athletes[]		=	$athlete->get( 'name', null, GetterInterface::STRING );
				}

				$profile->set( 'favorite_athletes', $athletes );
			}

			if ( $response->get( 'favorite_teams', null, GetterInterface::RAW ) && in_array( 'favorite_teams', $fields ) ) {
				$teams				=	array();

				foreach ( $response->subTree( 'favorite_teams' ) as $team ) {
					/** @var Registry $team */
					$teams[]		=	$team->get( 'name', null, GetterInterface::STRING );
				}

				$profile->set( 'favorite_teams', $teams );
			}

			if ( $response->get( 'inspirational_people', null, GetterInterface::RAW ) && in_array( 'inspirational_people', $fields ) ) {
				$people				=	array();

				foreach ( $response->subTree( 'inspirational_people' ) as $person ) {
					/** @var Registry $person */
					$people[]		=	$person->get( 'name', null, GetterInterface::STRING );
				}

				$profile->set( 'inspirational_people', $people );
			}

			if ( $response->get( 'sports', null, GetterInterface::RAW ) && in_array( 'sports', $fields ) ) {
				$sports				=	array();

				foreach ( $response->subTree( 'sports' ) as $sport ) {
					/** @var Registry $sport */
					$sports[]		=	$sport->get( 'name', null, GetterInterface::STRING );
				}

				$profile->set( 'sports', $sports );
			}

			if ( $response->get( 'work', null, GetterInterface::RAW ) && in_array( 'work', $fields ) ) {
				$jobs				=	array();

				foreach ( $response->subTree( 'work' ) as $job ) {
					/** @var Registry $job */
					$jobs[]			=	$job->get( 'employer.name', null, GetterInterface::STRING ) . ' - ' . $job->get( 'position.name', null, GetterInterface::STRING );
				}

				$profile->set( 'work', $jobs );
			}

			$profile->set( 'profile', $response );
		}

		return $profile;
	}

	/**
	 * Make a custom Facebook API request
	 * https://developers.facebook.com/docs/graph-api/reference
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

		if ( $this->session()->get( 'facebook.access_token', null, GetterInterface::STRING ) ) {
			$params['access_token']		=	$this->session()->get( 'facebook.access_token', null, GetterInterface::STRING );
			$params['appsecret_proof']	=	hash_hmac( 'sha256', $params['access_token'], $this->clientSecret );
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
