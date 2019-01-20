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
use CB\Plugin\AntiSpam\Table\BlockTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AntiSpam\CBAntiSpam;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class HTML_cbantispamBlock
{

	/**
	 * @param BlockTable      $row
	 * @param array           $input
	 * @param string          $type
	 * @param UserTable       $viewer
	 * @param UserTable       $user
	 * @param cbPluginHandler $plugin
	 */
	static public function showBlock( $row, $input, $type, $viewer, $user, $plugin )
	{
		global $_CB_framework, $ueConfig;

		$js				=	"$( '#durations' ).on( 'change', function() {"
						.		"var value = $( this ).val();"
						.		"if ( value ) {"
						.			"$( '#duration' ).attr( 'value', value ).focus();"
						.			"$( this ).attr( 'value', '' );"
						.		"}"
						.	"});"
						.	"$( '#type' ).on( 'change', function() {"
						.		"if ( $( this ).val() == 'user' ) {"
						.			"$( '.blockEditDate,.blockEditDuration,.blockEditReason,.blockEditBan,.blockEditBlock' ).removeClass( 'hidden' );"
						.			"$( '.blockEditBlockAccount' ).addClass( 'hidden' );"
						.			"$( '#ban_user' ).trigger( 'change' );"
						.		"} else if ( $( this ).val() == 'account' ) {"
						.			"$( '.blockEditDate,.blockEditDuration,.blockEditReason,.blockEditBlock' ).addClass( 'hidden' );"
						.			"$( '.blockEditBan,.blockEditBlockAccount' ).removeClass( 'hidden' );"
						.			"$( '#ban_user' ).trigger( 'change' );"
						.		"} else {"
						.			"$( '.blockEditDate,.blockEditDuration,.blockEditReason' ).removeClass( 'hidden' );"
						.			"$( '.blockEditBan,.blockEditBlock,.blockEditBanReason,.blockEditBlockAccount' ).addClass( 'hidden' );"
						.		"}"
						.	"}).change();"
						.	"$( '#ban_user' ).on( 'change', function() {"
						.		"if ( $( this ).val() == 1 ) {"
						.			"$( '.blockEditBanReason' ).removeClass( 'hidden' );"
						.		"} else {"
						.			"$( '.blockEditBanReason' ).addClass( 'hidden' );"
						.		"}"
						.	"});";

		$_CB_framework->outputCbJQuery( $js );

		cbValidator::loadValidation();
		initToolTip();

		if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
			$pageTitle	=	CBTxt::T( 'BLOCK_NAME', 'Block [name]', array( '[name]' => CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'profile', 0, true ) ) );
		} else {
			$pageTitle	=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Edit Block' ) : CBTxt::T( 'New Block' ) );
		}

		$_CB_framework->setPageTitle( $pageTitle );

		$returnUrl		=	CBAntiSpam::getReturn( true, true );

		if ( ! $returnUrl ) {
			$returnUrl	=	'index.php';
		}

		$return			=	'<div class="blockEdit">'
						.		'<form action="' . $_CB_framework->pluginClassUrl( $plugin->element, true, array( 'action' => 'block', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ), 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'return' => CBAntiSpam::getReturn( true ) ) ) . '" method="post" enctype="multipart/form-data" name="blockForm" id="blockForm" class="cb_form blockForm form-auto cbValidation">'
						.			( $pageTitle ? '<div class="blockTitle page-header"><h3>' . $pageTitle . '</h3></div>' : null )
						.			'<div class="blockEditType cbft_select cbtt_select form-group cb_form_line clearfix">'
						.				'<label for="type" class="col-sm-3 control-label">' . CBTxt::T( 'Type' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['type']
						.					getFieldIcons( 1, 1, null, CBTxt::T( 'Select the block type. Type determines what value should be supplied.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="blockEditValue cbft_text cbtt_input form-group cb_form_line clearfix">'
						.				'<label for="value" class="col-sm-3 control-label">' . CBTxt::T( 'Value' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['value']
						.					getFieldIcons( 1, 1, null, CBTxt::T( 'Input block value in relation to the type. User type use the users user_id (e.g. 42). IP Address type use a full valid IP Address (e.g. 192.168.0.1). IP Address Range type use two full valid IP Addresses separated by a colon (e.g. 192.168.0.1:192.168.0.100). Email type use a fill valid email address (e.g. invalid@cb.invalid). Email Domain type use a full email address domain after @ (e.g. example.com). Additionally IP Address, Email Address, and Email Domain types support % wildcard.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="blockEditDate cbft_date cbtt_input form-group cb_form_line clearfix">'
						.				'<label for="date" class="col-sm-3 control-label">' . CBTxt::T( 'Date' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['date']
						.					getFieldIcons( 1, 1, null, CBTxt::T( 'Select the date and time the block should go in affect. Note date and time always functions in UTC.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="blockEditDuration cbft_text cbtt_input form-group cb_form_line clearfix">'
						.				'<label for="duration" class="col-sm-3 control-label">' . CBTxt::T( 'Duration' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['duration'] . ' ' . $input['durations']
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Input the strtotime relative date (e.g. +1 Day). This duration will be added to the datetime specified above. Leave blank for a forever duration.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="blockEditReason cbft_textarea cbtt_textarea form-group cb_form_line clearfix">'
						.				'<label for="reason" class="col-sm-3 control-label">' . CBTxt::T( 'Reason' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['reason']
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input block reason. If left blank will default to spam.' ) )
						.				'</div>'
						.			'</div>';

		if ( isset( $ueConfig['allowUserBanning'] ) && $ueConfig['allowUserBanning'] ) {
			$return		.=			'<div class="blockEditBan cbft_select cbtt_select form-group cb_form_line clearfix hidden">'
						.				'<label for="ban_user" class="col-sm-3 control-label">' . CBTxt::T( 'Ban Profile' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['ban_user']
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Ban the users profile using Community Builder moderator ban feature. Note normal ban notification will be sent with the ban.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="blockEditBanReason cbft_textarea cbtt_textarea form-group cb_form_line clearfix hidden">'
						.				'<label for="ban_reason" class="col-sm-3 control-label">' . CBTxt::T( 'Ban Reason' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['ban_reason']
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input reason for profile ban.' ) )
						.				'</div>'
						.			'</div>';
		}

		$return			.=			'<div class="blockEditBlock cbft_select cbtt_select form-group cb_form_line clearfix hidden">'
						.				'<label for="block_user" class="col-sm-3 control-label">' . CBTxt::T( 'Block Profile' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					$input['block_user']
						.					getFieldIcons( 1, 0, null, CBTxt::T( 'Block the users profile using Joomla block state.' ) )
						.				'</div>'
						.			'</div>'
						.			'<div class="blockEditBlockAccount cbft_delimiter form-group cb_form_line clearfix hidden">'
						.				'<label for="block_user" class="col-sm-3 control-label">' . CBTxt::T( 'Block Profile' ) . '</label>'
						.				'<div class="cb_field col-sm-9">'
						.					CBTxt::T( 'The specified user id will have their profile blocked using Joomla block state.' )
						.				'</div>'
						.			'</div>'
						.			'<div class="form-group cb_form_line clearfix">'
						.				'<div class="col-sm-offset-3 col-sm-9">'
						.					'<input type="submit" value="' . htmlspecialchars( ( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Update Block' ) : CBTxt::T( 'Create Block' ) ) ) . '" class="blockButton blockButtonSubmit btn btn-primary"' . cbValidator::getSubmitBtnHtmlAttributes() . ' />&nbsp;'
						.					' <input type="button" value="' . htmlspecialchars( CBTxt::T( 'Cancel' ) ) . '" class="blockButton blockButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( htmlspecialchars( $returnUrl ) ) . '\'; })" />'
						.				'</div>'
						.			'</div>'
						.			cbGetSpoofInputTag( 'plugin' )
						.		'</form>'
						.	'</div>';

		echo $return;
	}
}