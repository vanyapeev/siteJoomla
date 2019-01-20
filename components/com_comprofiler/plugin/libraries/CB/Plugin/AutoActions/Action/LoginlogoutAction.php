<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class LoginlogoutAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		$params							=	$this->autoaction()->params()->subTree( 'loginlogout' );

		cbimport( 'cb.authentication' );

		$cbAuthenticate					=	new \CBAuthentication();

		$isHttps						=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
		$returnUrl						=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

		if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
			$returnUrl					.=	$_SERVER['REQUEST_URI'];
		} else {
			$returnUrl					.=	$_SERVER['SCRIPT_NAME'];

			if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
				$returnUrl				.=	'?' . $_SERVER['QUERY_STRING'];
			}
		}

		$returnUrl						=	cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $returnUrl ) ) ) ) );

		if ( preg_match( '/index.php\?option=com_comprofiler&task=confirm&confirmCode=|index.php\?option=com_comprofiler&view=confirm&confirmCode=|index.php\?option=com_comprofiler&task=login|index.php\?option=com_comprofiler&view=login/', $returnUrl ) ) {
			$returnUrl					=	'index.php';
		}

		$redirect						=	$this->string( $user, $params->get( 'redirect', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $params->get( 'redirect', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );
		$message						=	$this->string( $user, CBTxt::T( $params->get( 'message', null, GetterInterface::RAW ) ), false );
		$messageType					=	$params->get( 'message_type', 'message', GetterInterface::STRING );

		if ( $messageType == 'custom' ) {
			$messageType				=	$this->string( $user, $params->get( 'custom_message_type', null, GetterInterface::STRING ) );
		}

		if ( $params->get( 'mode', true, GetterInterface::BOOLEAN ) ) {
			if ( Application::MyUser()->getUserId() ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_LOGGED_IN_ALREADY', ':: Action [action] :: Login skipped due to already logged in', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				return;
			}

			$messagesToUser				=	array();
			$alertMessages				=	array();

			switch ( $params->get( 'method', 1, GetterInterface::INT ) ) {
				case 2:
					$userId				=	$this->string( $user, $params->get( 'user_id', null, GetterInterface::STRING ) );

					if ( ! $userId ) {
						$credentials	=	$user->get( 'username', null, GetterInterface::STRING );
					} else {
						$credentials	=	\CBuser::getUserDataInstance( (int) $userId )->get( 'username', null, GetterInterface::STRING );
					}

					$method				=	0;
					break;
				case 0:
					$credentials		=	$this->string( $user, $params->get( 'email', null, GetterInterface::STRING ) );

					if ( ! $credentials ) {
						$credentials	=	$user->get( 'email', null, GetterInterface::STRING );
					}

					$method				=	1;
					break;
				case 1:
				default:
					$credentials		=	$this->string( $user, $params->get( 'username', null, GetterInterface::STRING ) );

					if ( ! $credentials ) {
						$credentials	=	$user->get( 'username', null, GetterInterface::STRING );
					}

					$method				=	0;
					break;
			}

			$password					=	$params->get( 'password', null, GetterInterface::STRING );

			if ( $password ) {
				$password				=	$this->string( $user, $password );
			} else {
				$password				=	false;
			}

			$resultError				=	$cbAuthenticate->login( $credentials, $password, 0, 1, $returnUrl, $messagesToUser, $alertMessages, $method );

			$message					=	( $resultError ? $resultError : ( $message ? $message : ( $alertMessages && $params->get( 'alerts', true, GetterInterface::BOOLEAN ) ? stripslashes( implode( '<br />', $alertMessages ) ) : null ) ) );
			$messageType				=	( $resultError ? 'error' : ( $messageType ? $messageType : 'message' ) );

			$this->redirect( $returnUrl, $redirect, $message, $messageType );
		} else {
			if ( ! Application::MyUser()->getUserId() ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_NOT_LOGGED_IN', ':: Action [action] :: Logout skipped due to not logged in', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				return;
			}

			$resultError				=	$cbAuthenticate->logout( $returnUrl );

			$message					=	( $resultError ? $resultError : ( $message ? $message : ( $params->get( 'alerts', true, GetterInterface::BOOLEAN ) ? CBTxt::T( 'LOGOUT_SUCCESS', 'You have successfully logged out' ) : null ) ) );
			$messageType				=	( $resultError ? 'error' : ( $messageType ? $messageType : 'message' ) );

			$this->redirect( $returnUrl, $redirect, $message, $messageType );
		}

		return null;
	}

	/**
	 * @param string $returnUrl
	 * @param string $redirect
	 * @param string $message
	 * @param string $messageType
	 */
	private function redirect( $returnUrl, $redirect, $message, $messageType )
	{
		global $_CB_framework;

		if ( substr( strtolower( $redirect ), 0, 6 ) == 'goback' ) {
			$back			=	(int) substr( strtolower( $redirect ), 6 );

			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, $messageType );
			}

			$_CB_framework->document->addHeadScriptDeclaration( ( $back && ( $back > 0 ) ? "window.history.go( -$back );" : "window.history.back();" ) );
		} elseif ( strtolower( $redirect ) == 'reload' ) {
			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, $messageType );
			}

			$_CB_framework->document->addHeadScriptDeclaration( "window.location.reload();" );
		} else {
			if ( strtolower( $redirect ) == 'return' ) {
				$redirect	=	$returnUrl;
			}

			if ( $redirect ) {
				cbRedirect( $redirect, $message, $messageType );
			} elseif ( $message ) {
				$_CB_framework->enqueueMessage( $message, $messageType );
			}
		}
	}
}