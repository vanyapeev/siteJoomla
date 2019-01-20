<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\Registry;
use CB\Plugin\AntiSpam\Table\WhitelistTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AntiSpam\CBAntiSpam;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamWhitelists
{

	/**
	 * @param WhitelistTable[] $rows
	 * @param cbPageNav        $pageNav
	 * @param UserTable        $viewer
	 * @param UserTable        $user
	 * @param TabTable         $tab
	 * @param cbTabHandler     $plugin
	 * @return string
	 */
	static public function showWhitelists( $rows, $pageNav, $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework;

		initToolTip();

		/** @var Registry $params */
		$params						=	$tab->params;
		$tabWhitelistUser			=	$params->get( 'tab_whitelist_user', true, GetterInterface::BOOLEAN );
		$tabWhitelistIp				=	$params->get( 'tab_whitelist_ip', true, GetterInterface::BOOLEAN );
		$tabWhitelistEmail			=	$params->get( 'tab_whitelist_email', false, GetterInterface::BOOLEAN );
		$tabWhitelistDomain			=	$params->get( 'tab_whitelist_domain', false, GetterInterface::BOOLEAN );
		$returnUrl					=	base64_encode( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ) );

		$return						=	'<div class="whitelistsTab">'
									.		'<form action="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ) . '" method="post" name="whitelistsForm" class="whitelistsForm">';

		if ( $tabWhitelistUser || $tabWhitelistIp || $tabWhitelistEmail || $tabWhitelistDomain ) {
			$return					.=			'<div class="whitelistsHeader text-left" style="margin-bottom: 10px;">'
									.				'<div class="btn-group">'
									.					( $tabWhitelistUser ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'whitelist', 'func' => 'user', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="whitelistsButton whitelistsButtonWhitelistUser btn btn-default"><span class="fa fa-user"></span> ' . CBTxt::T( 'Whitelist User' ) . '</button>' : null )
									.					( $tabWhitelistIp ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'whitelist', 'func' => 'ip', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="whitelistsButton whitelistsButtonWhitelistIP btn btn-default"><span class="fa fa-flag"></span> ' . CBTxt::T( 'Whitelist IP Address' ) . '</button>' : null )
									.					( $tabWhitelistEmail ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'whitelist', 'func' => 'email', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="whitelistsButton whitelistsButtonWhitelistEmail btn btn-default"><span class="fa fa-at"></span> ' . CBTxt::T( 'Whitelist Email Address' ) . '</button>' : null )
									.					( $tabWhitelistDomain ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'whitelist', 'func' => 'domain', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="whitelistsButton whitelistsButtonWhitelistDomain btn btn-default"><span class="fa fa-globe"></span> ' . CBTxt::T( 'Whitelist Email Domain' ) . '</button>' : null )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<table class="whitelistsItemsContainer table table-hover table-responsive">'
									.				'<thead>'
									.					'<tr class="whitelistsItemsHeader">'
									.						'<th class="text-left">' . CBTxt::T( 'Value' ) . '</th>'
									.						'<th style="width: 20%;" class="text-center hidden-xs">' . CBTxt::T( 'Type' ) . '</th>'
									.						'<th style="width: 30%;" class="text-left hidden-xs">' . CBTxt::T( 'Reason' ) . '</th>'
									.						'<th style="width: 1%;" class="text-right">&nbsp;</th>'
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$menuItems				=	'<ul class="whitelistsMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
									.		'<li class="whitelistsMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'whitelist', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>'
									.		'<li class="whitelistsMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this whitelist?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'whitelist', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
									.	'</ul>';

			$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="whitelistsMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

			switch ( $row->get( 'type', null, GetterInterface::STRING ) ) {
				case 'user':
					$type			=	CBTxt::T( 'User' );
					break;
				case 'ip':
					$type			=	CBTxt::T( 'IP Address' );
					break;
				case 'email':
					$type			=	CBTxt::T( 'Email Address' );
					break;
				case 'domain':
					$type			=	CBTxt::T( 'Email Domain' );
					break;
				default:
					$type			=	CBTxt::T( 'Unknown' );
					break;
			}

			$return					.=					'<tr class="whitelistsItemContainer">'
									.						'<td class="text-left">' . $row->get( 'value', null, GetterInterface::STRING ) . '</td>'
									.						'<td style="width: 20%;" class="text-center hidden-xs">' . $type . '</td>'
									.						'<td style="width: 30%;" class="text-left hidden-xs">' . $row->get( 'reason', null, GetterInterface::STRING ) . '</td>'
									.						'<td style="width: 1%;" class="text-right">'
								.								'<button type="button"' . $menuAttr . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
									.						'</td>'
									.					'</tr>';
		} else {
			$return					.=					'<tr class="whitelistsItemsEmpty">'
									.						'<td colspan="3" class="text-left">';

			if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) {
				$return				.=							CBTxt::T( 'You have no whitelists.' );
			} else {
				$return				.=							CBTxt::T( 'This user has no whitelists.' );
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr class="whitelistsItemsPaging">'
									.						'<td colspan="3" class="text-center">'
									.							$pageNav->getListLinks()
									.						'</td>'
									.					'</tr>'
									.				'</tfoot>';
		}

		$return						.=			'</table>'
									.			$pageNav->getLimitBox( false )
									.		'</form>'
									.	'</div>';

		return $return;
	}
}
?>