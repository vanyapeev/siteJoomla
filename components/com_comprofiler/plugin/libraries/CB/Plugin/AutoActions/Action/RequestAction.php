<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class RequestAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return mixed
	 */
	public function execute( $user )
	{
		$return								=	null;

		foreach ( $this->autoaction()->params()->subTree( 'request' ) as $row ) {
			/** @var ParamsInterface $row */
			$url							=	$this->string( $user, $row->get( 'url', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $row->get( 'url', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );

			if ( ! $url ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_REQUEST_NO_URL', ':: Action [action] :: Request skipped due to missing url', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$client							=	new \GuzzleHttp\Client();

			try {
				$options					=	array();

				if ( $row->get( 'auth', 'none', GetterInterface::STRING ) == 'basic' ) {
					$username				=	$this->string( $user, $row->get( 'auth_username', null, GetterInterface::STRING ) );
					$password				=	$this->string( $user, $row->get( 'auth_password', null, GetterInterface::STRING ) );

					if ( $username && $password ) {
						$options['auth']	=	array( $username, $password );
					}
				}

				$body						=	array();

				foreach ( $row->subTree( 'request' ) as $request ) {
					/** @var ParamsInterface $request */
					$key					=	$request->get( 'key', null, GetterInterface::STRING );

					if ( $key ) {
						$body[$key]			=	$this->string( $user, $request->get( 'value', null, GetterInterface::RAW ), false, $request->get( 'translate', false, GetterInterface::BOOLEAN ) );
					}
				}

				$headers					=	array();

				foreach ( $row->subTree( 'header' ) as $header ) {
					/** @var ParamsInterface $header */
					$key					=	$header->get( 'key', null, GetterInterface::STRING );

					if ( $key ) {
						$headers[$key]		=	$this->string( $user, $header->get( 'value', null, GetterInterface::RAW ), false, $header->get( 'translate', false, GetterInterface::BOOLEAN ) );
					}
				}

				if ( $headers ) {
					$options['headers']		=	$headers;
				}

				if ( $row->get( 'method', 'GET', GetterInterface::STRING ) == 'POST' ) {
					if ( $body ) {
						$options['body']	=	$body;
					}

					$result					=	$client->post( $url, $options );
				} else {
					if ( $body ) {
						$options['query']	=	$body;
					}

					$result					=	$client->get( $url, $options );
				}

				if ( $result->getStatusCode() != 200 ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_REQUEST_FAILED', ':: Action [action] :: Request failed. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $result->getStatusCode() ) ) );
					continue;
				}
			} catch ( \Exception $e ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_REQUEST_FAILED', ':: Action [action] :: Request failed. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $e->getMessage() ) ) );
				continue;
			}

			if ( $result !== false ) {
				switch( $result->getHeader( 'Content-Type' ) ) {
					case 'application/xml':
						$content			=	CBTxt::T( 'HTTP Request XML response handling is not yet implemented.' );
						break;
					case 'application/json':
						$content			=	$this->jsonResults( $result->json() );
						break;
					default:
						$content			=	(string) $result->getBody();
						break;
				}

				$return						.=	$content;
			}
		}

		return $return;
	}

	/**
	 * @param array|object $json
	 * @return null|string
	 */
	private function jsonResults( $json )
	{
		$return				=	null;

		foreach ( $json as $k => $v ) {
			$return			.=	'<div class="form-group cb_form_line clearfix">';

			if ( trim( $k ) !== '' ) {
				$return		.=		'<label class="control-label col-sm-3">'
							.			$k
							.		'</label>';

				$size		=	'col-sm-9';
			} else {
				$size		=	'col-sm-9 col-sm-offset-3';
			}

			$return			.=		'<div class="cb_field ' . $size . '">'
							.			'<div>';

			if ( is_object( $v ) || is_array( $v ) ) {
				$return		.=				$this->jsonResults( $v );
			} else {
				$return		.=				$v;
			}

			$return			.=			'</div>'
							.		'</div>'
							.	'</div>';
		}

		return $return;
	}
}