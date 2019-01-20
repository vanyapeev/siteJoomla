<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Conditional\Trigger;

use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Conditional\CBConditional;

defined('CBLIB') or die();

class TabTrigger extends \cbPluginHandler
{

	/**
	 * @param string    $content
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param array     $postdata
	 * @param string    $output
	 * @param string    $formatting
	 * @param string    $reason
	 * @param bool      $tabbed
	 */
	public function tabEdit( &$content, &$tab, &$user, &$postdata, $output, $formatting, $reason, $tabbed )
	{
		if ( ( ! Application::Cms()->getClientId() ) || CBConditional::getGlobalParams()->get( 'conditions_backend', false, GetterInterface::BOOLEAN ) ) {
			if ( $output == 'htmledit' ) {
				$conditioned		=	CBConditional::getTabConditional( $tab, $reason, $user->get( 'id', 0, GetterInterface::INT ), ( $formatting != 'none' ) );
				$display			=	true;

				if ( $conditioned === 2 ) {
					$display		=	false;
				} elseif ( ( $formatting == 'none' ) && $conditioned ) {
					$display		=	false;
				}

				if ( ! $display ) {
					$content		=	'';
				}
			}
		}
	}

	/**
	 * @param TabTable[] $tabs
	 * @param UserTable  $user
	 * @param string     $reason
	 */
	public function tabsFetch( &$tabs, &$user, $reason )
	{
		$post				=	$this->getInput()->getNamespaceRegistry( 'post' );
		$view				=	$this->input( 'view', null, GetterInterface::STRING );

		if ( ! Application::Cms()->getClientId() ) {
			$checkView		=	( ( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'saveregisters', 'saveuseredit' ) ) ) || ( $reason == 'profile' ) );
		} elseif ( Application::Cms()->getClientId() && CBConditional::getGlobalParams()->get( 'conditions_backend', false, GetterInterface::BOOLEAN ) ) {
			$checkView		=	( ( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'apply', 'save' ) ) ) || ( $reason == 'profile' ) );
		} else {
			$checkView		=	false;
		}

		if ( $checkView && $tabs && ( $user && ( $user instanceof UserTable ) && ( ! $user->getError() ) ) ) {
			foreach ( $tabs as $k => $tab ) {
				if ( CBConditional::getTabConditional( $tab, $reason, $user->get( 'id', 0, GetterInterface::INT ) ) ) {
					unset( $tabs[$k] );
				}
			}
		}
	}
}