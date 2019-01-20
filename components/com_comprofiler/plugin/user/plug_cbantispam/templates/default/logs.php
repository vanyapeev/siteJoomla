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
use CB\Plugin\AntiSpam\Table\LogTable;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamLogs
{

	/**
	 * @param LogTable[]   $rows
	 * @param cbPageNav    $pageNav
	 * @param UserTable    $viewer
	 * @param UserTable    $user
	 * @param TabTable     $tab
	 * @param cbTabHandler $plugin
	 * @return string
	 */
	static public function showLogs( $rows, $pageNav, $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework;

		initToolTip();

		/** @var Registry $params */
		$params						=	$tab->params;
		$returnUrl					=	base64_encode( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ) );

		$return						=	'<div class="logsTab">'
									.		'<form action="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ) . '" method="post" name="logsForm" id="logsForm" class="logsForm">'
									.			'<table class="logsItemsContainer table table-hover table-responsive">'
									.				'<thead>'
									.					'<tr class="logsItemsHeader">'
									.						'<th class="text-left">' . CBTxt::T( 'IP Address' ) . '</th>'
									.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
									.						'<th style="width: 10%;" class="text-center hidden-xs">' . CBTxt::T( 'Count' ) . '</th>'
									.						'<th style="width: 1%;" class="text-right">&nbsp;</th>'
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return					.=					'<tr class="logsItemContainer">'
									.						'<td class="text-left">' . $row->get( 'ip_address', null, GetterInterface::STRING ) . '</td>'
									.						'<td style="width: 25%;" class="text-left hidden-xs">' . cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), false ) . '</td>'
									.						'<td style="width: 10%;" class="text-center hidden-xs">' . $row->get( 'count', 0, GetterInterface::INT ) . '</td>'
									.						'<td style="width: 1%;" class="text-right">'
									.							'<a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this log?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'log', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span></a>'
									.						'</td>'
									.					'</tr>';
		} else {
			$return					.=					'<tr class="logsItemsEmpty">'
									.						'<td colspan="3" class="text-left">';

			if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) {
				$return				.=							CBTxt::T( 'You have no logs.' );
			} else {
				$return				.=							CBTxt::T( 'This user has no logs.' );
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr class="logsItemsPaging">'
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