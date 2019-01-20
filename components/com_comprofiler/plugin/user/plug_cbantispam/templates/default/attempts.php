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
use CB\Plugin\AntiSpam\Table\AttemptTable;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamAttempts
{

	/**
	 * @param AttemptTable[] $rows
	 * @param cbPageNav                 $pageNav
	 * @param UserTable                 $viewer
	 * @param UserTable                 $user
	 * @param TabTable                  $tab
	 * @param cbTabHandler              $plugin
	 * @return string
	 */
	static public function showAttempts( $rows, $pageNav, $viewer, $user, $tab, $plugin )
	{
		global $_CB_framework;

		initToolTip();

		/** @var Registry $params */
		$params						=	$tab->params;
		$returnUrl					=	base64_encode( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ) );

		$return						=	'<div class="attemptsTab">'
									.		'<form action="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ) . '" method="post" name="attemptsForm" class="attemptsForm">'
									.			'<table class="attemptsItemsContainer table table-hover table-responsive">'
									.				'<thead>'
									.					'<tr class="attemptsItemsHeader">'
									.						'<th class="text-left">' . CBTxt::T( 'IP Address' ) . '</th>'
									.						'<th style="width: 25%;" class="text-left hidden-xs">' . CBTxt::T( 'Date' ) . '</th>'
									.						'<th style="width: 20%;" class="text-center">' . CBTxt::T( 'Type' ) . '</th>'
									.						'<th style="width: 10%;" class="text-center hidden-xs">' . CBTxt::T( 'Count' ) . '</th>'
									.						'<th style="width: 1%;" class="text-right">&nbsp;</th>'
									.					'</tr>'
									.				'</thead>'
									.				'<tbody>';

		if ( $rows ) foreach ( $rows as $row ) {
			$return					.=					'<tr class="attemptsItemContainer">'
									.						'<td class="text-left">' . $row->get( 'ip_address', null, GetterInterface::STRING ) . '</td>'
									.						'<td style="width: 25%;" class="text-left hidden-xs">' . cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), false ) . '</td>'
									.						'<td style="width: 20%;" class="text-center">' . $row->get( 'type', null, GetterInterface::STRING ) . '</td>'
									.						'<td style="width: 10%;" class="text-center hidden-xs">' . $row->get( 'count', 0, GetterInterface::INT ) . '</td>'
									.						'<td style="width: 1%;" class="text-right">'
									.							'<a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this attempt?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'attempt', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'return' => $returnUrl ) ) ) . '\'; })"><span class="fa fa-trash-o"></span></a>'
									.						'</td>'
									.					'</tr>';
		} else {
			$return					.=					'<tr class="attemptsItemsEmpty">'
									.						'<td colspan="3" class="text-left">';

			if ( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) {
				$return				.=							CBTxt::T( 'You have no attempts.' );
			} else {
				$return				.=							CBTxt::T( 'This user has no attempts.' );
			}

			$return					.=						'</td>'
									.					'</tr>';
		}

		$return						.=				'</tbody>';

		if ( $params->get( 'tab_paging', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) {
			$return					.=				'<tfoot>'
									.					'<tr class="attemptsItemsPaging">'
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