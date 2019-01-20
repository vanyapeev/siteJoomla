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
use CB\Plugin\AntiSpam\Table\BlockTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AntiSpam\CBAntiSpam;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamBlocks
{

	/**
	 * @param BlockTable[] $rows
	 * @param cbPageNav    $pageNav
	 * @param UserTable    $viewer
	 * @param UserTable    $user
	 * @param TabTable     $tab
	 * @param cbTabHandler $plugin
	 * @return string
	 */
	static public function showBlocks( $rows, $pageNav, $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework;

		initToolTip();

		/** @var Registry $params */
		$params						=	$tab->params;
		$tabBlockAccount			=	$params->get( 'tab_block_account', true, GetterInterface::BOOLEAN );
		$tabBlockUser				=	$params->get( 'tab_block_user', true, GetterInterface::BOOLEAN );
		$tabBlockIp					=	$params->get( 'tab_block_ip', true, GetterInterface::BOOLEAN );
		$tabBlockEmail				=	$params->get( 'tab_block_email', false, GetterInterface::BOOLEAN );
		$tabBlockDomain				=	$params->get( 'tab_block_domain', false, GetterInterface::BOOLEAN );
		$returnUrl					=	base64_encode( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ) );

		$return						=	'<div class="blocksTab">'
									.		'<form action="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ) . '" method="post" name="blocksForm" class="blocksForm">';

		if ( $tabBlockAccount || $tabBlockUser || $tabBlockIp || $tabBlockEmail || $tabBlockDomain ) {
			$return					.=			'<div class="blocksHeader text-left" style="margin-bottom: 10px;">'
									.				'<div class="btn-group">'
									.					( $tabBlockAccount ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'block', 'func' => 'account', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="blocksButton blocksButtonBlockAccount btn btn-default"><span class="fa fa-user"></span> ' . CBTxt::T( 'Block Account' ) . '</button>' : null )
									.					( $tabBlockUser ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'block', 'func' => 'user', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="blocksButton blocksButtonBlockUser btn btn-default"><span class="fa fa-user"></span> ' . CBTxt::T( 'Block User' ) . '</button>' : null )
									.					( $tabBlockIp ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'block', 'func' => 'ip', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="blocksButton blocksButtonBlockIP btn btn-default"><span class="fa fa-flag"></span> ' . CBTxt::T( 'Block IP Address' ) . '</button>' : null )
									.					( $tabBlockEmail ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'block', 'func' => 'email', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="blocksButton blocksButtonBlockEmail btn btn-default"><span class="fa fa-at"></span> ' . CBTxt::T( 'Block Email Address' ) . '</button>' : null )
									.					( $tabBlockDomain ? '<button type="button" onclick="location.href=\'' . $_CB_framework->pluginClassUrl( $plugin->element, false, array( 'action' => 'block', 'func' => 'domain', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '\';" class="blocksButton blocksButtonBlockDomain btn btn-default"><span class="fa fa-globe"></span> ' . CBTxt::T( 'Block Email Domain' ) . '</button>' : null )
									.				'</div>'
									.			'</div>';
		}

		$return						.=			'<table class="blocksItemsContainer table table-hover table-responsive">'
									.				'<thead>'
									.					'<tr class="blocksItemsHeader">'
									.						'<th class="text-left">' . CBTxt::T( 'Value' ) . '</th>'
									.						'<th style="width: 20%;" class="text-center hidden-xs">' . CBTxt::T( 'Type' ) . '</th>'
									.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
									.						'<th style="width: 5%;" class="text-center">' . CBTxt::T( 'Blocked' ) . '</th>'
									.						'<th style="width: 1%;" class="text-right">&nbsp;</th>'
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$menuItems				=	'<ul class="blocksMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
									.		'<li class="blocksMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'block', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>'
									.		'<li class="blocksMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this block?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'block', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
									.	'</ul>';

			$menuAttr				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="blocksMenu btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );

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

			$return					.=					'<tr class="blocksItemContainer">'
									.						'<td class="text-left">' . $row->get( 'value', null, GetterInterface::STRING ) . '</td>'
									.						'<td style="width: 20%;" class="text-center hidden-xs">' . $type . '</td>'
									.						'<td style="width: 25%;" class="text-left hidden-xs">';

			if ( $row->get( 'duration', null, GetterInterface::STRING ) ) {
				$return				.=							'<div class="text-info" title="' . htmlspecialchars( CBTxt::T( 'Start' ) ) . '">' . cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), false ) . '</div>'
									.							'<div class="text-success" title="' . htmlspecialchars( CBTxt::T( 'End' ) ) . '">' . cbFormatDate( $row->expiry(), false ) . '</div>';
			} else {
				$return				.=							CBTxt::T( 'Forever' );
			}

			$return					.=						'</td>'
									.						'<td style="width: 5%;" class="text-center">';

			if ( $row->blocked() ) {
				$return				.=							'<span class="fa fa-check text-success" title="' . htmlspecialchars( CBTxt::T( 'Blocked' ) ) . '"></span>';
			} else {
				$return				.=							'<span class="fa fa-times text-danger" title="' . htmlspecialchars( CBTxt::T( 'Not Blocked' ) ) . '"></span>';
			}

			$return					.=						'</td>'
									.						'<td style="width: 1%;" class="text-right">'
									.							'<button type="button"' . $menuAttr . '><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>'
									.						'</td>'
									.					'</tr>';
		} else {
			$return					.=					'<tr class="blocksItemsEmpty">'
									.						'<td colspan="5" class="text-left">';

			if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) {
				$return				.=							CBTxt::T( 'You have no blocks.' );
			} else {
				$return				.=							CBTxt::T( 'This user has no blocks.' );
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr class="blocksItemsPaging">'
									.						'<td colspan="5" class="text-center">'
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