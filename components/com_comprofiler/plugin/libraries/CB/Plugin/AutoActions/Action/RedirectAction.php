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
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\AutoActions\CBAutoActions;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class RedirectAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_framework;

		$params						=	$this->autoaction()->params()->subTree( 'redirect' );
		$redirect					=	$this->string( $user, $params->get( 'url', null, GetterInterface::STRING ), ( preg_match( '/^\[[a-zA-Z0-9-_]+\]$/', $params->get( 'url', null, GetterInterface::STRING ) ) ? false : array( '\CB\Plugin\AutoActions\CBAutoActions', 'escapeURL' ) ) );

		if ( ! $redirect ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_REDIRECT_NO_URL', ':: Action [action] :: Redirect skipped due to missing url', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$message					=	$this->string( $user, CBTxt::T( $params->get( 'message', null, GetterInterface::RAW ) ), false );
		$messageType				=	$params->get( 'type', 'message', GetterInterface::STRING );

		if ( $messageType == 'custom' ) {
			$messageType			=	$this->string( $user, $params->get( 'custom_type', null, GetterInterface::STRING ) );
		}

		if ( substr( strtolower( $redirect ), 0, 6 ) == 'goback' ) {
			$back					=	(int) substr( strtolower( $redirect ), 6 );

			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, ( $messageType ? $messageType : null ) );
			}

			$_CB_framework->document->addHeadScriptDeclaration( ( $back && ( $back > 0 ) ? "window.history.go( -$back );" : "window.history.back();" ) );
		} elseif ( strtolower( $redirect ) == 'reload' ) {
			if ( $message ) {
				$_CB_framework->enqueueMessage( $message, ( $messageType ? $messageType : null ) );
			}

			$_CB_framework->document->addHeadScriptDeclaration( "window.location.reload();" );
		} else {
			if ( strtolower( $redirect ) == 'return' ) {
				$redirect			=	CBAutoActions::getCurrentURL();

				if ( preg_match( '/index.php\?option=com_comprofiler&task=confirm&confirmCode=|index.php\?option=com_comprofiler&view=confirm&confirmCode=|index.php\?option=com_comprofiler&task=login|index.php\?option=com_comprofiler&view=login/', $redirect ) ) {
					$redirect		=	'index.php';
				}
			}

			cbRedirect( $redirect, $message, ( $message ? ( $messageType ? $messageType : null ) : null ) );
		}

		return null;
	}
}