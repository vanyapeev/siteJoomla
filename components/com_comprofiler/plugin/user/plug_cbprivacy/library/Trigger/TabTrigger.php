<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Trigger;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Privacy\CBPrivacy;
use CB\Plugin\Privacy\Privacy;
use CB\Database\Table\TabTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;

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
		if ( ( ! in_array( $reason, array( 'edit', 'register' ) ) ) || ( ! $tab instanceof TabTable ) ) {
			return;
		}

		$tabId						=	$tab->get( 'tabid', 0, GetterInterface::INT );

		if ( ( $reason != 'register' ) && ( ! CBPrivacy::checkTabEditAccess( $tab ) ) ) {
			$content				=	' ';
		} else {
			if ( ! $tab->params instanceof ParamsInterface ) {
				$tab->params		=	new Registry( $tab->params );
			}

			$display				=	$tab->params->get( 'cbprivacy_display', 0, GetterInterface::INT );

			if ( ( $reason == 'register' ) && ( ! $tab->params->get( 'cbprivacy_display_reg', false, GetterInterface::BOOLEAN ) ) ) {
				$display			=	0;
			}

			if ( ( $display == 1 ) || ( ( $display == 2 ) && Application::MyUser()->isGlobalModerator() ) ) {
				$privacy			=	new Privacy( 'profile.tab.' . $tabId, $user );

				if ( $display == 2 ) {
					$privacy->set( 'options_moderator', true );
				}

				$privacy->parse( $tab->params, 'privacy_' );

				switch ( $formatting ) {
					case 'tabletrs':
						$return		=	'<tr id="cbtp_' . (int) $tabId . '" class="cb_table_line cbft_privacy cbtt_select cb_table_line_field">'
									.		'<td class="fieldCell text-right" colspan="2" style="width: 100%;">'
									.			$privacy->privacy( 'edit' )
									.		'</td>'
									.	'</tr>';
						break;
					default:
						$return		=	'<div class="cbft_privacy cbtt_select form-group cb_form_line clearfix cbtwolinesfield" id="cbtp_' . (int) $tabId . '">'
									.		'<div class="cb_field col-sm-12">'
									.			'<div class="text-right">'
									.				$privacy->privacy( 'edit' )
									.			'</div>'
									.		'</div>'
									.	'</div>';
						break;
				}

				$content			=	$return
									.	$content;
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
		if ( ( ! $user instanceof UserTable ) || $user->getError() || ( ! $tabs ) ) {
			return;
		}

		if ( ( $reason == 'profile' ) && ( ! Application::Cms()->getClientId() ) && ( ! Application::MyUser()->isGlobalModerator() ) && ( Application::MyUser()->getUserId() != $user->get( 'id', 0, GetterInterface::INT ) ) ) {
			foreach ( $tabs as $tabId => $tab ) {
				if ( isset( $tabs[$tabId] ) && ( ! CBPrivacy::checkTabDisplayAccess( $tab, $user ) ) ) {
					unset( $tabs[$tabId] );
				}
			}
		} elseif ( in_array( $reason, array( 'edit', 'editsave' ) ) && $user->get( 'id', 0, GetterInterface::INT ) ) {
			foreach ( $tabs as $tabId => $tab ) {
				if ( isset( $tabs[$tabId] ) && ( ! CBPrivacy::checkTabEditAccess( $tab ) ) ) {
					unset( $tabs[$tabId] );
				}
			}
		}
	}
}