<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var int       $userId
 * @var UserTable $user
 */

global $_CB_framework;

cbValidator::loadValidation();
initToolTip();

if ( $userId != $user->get( 'id', 0, GetterInterface::INT ) ) {
	$pageTitle		=	CBTxt::T( 'DISABLE_USER_ACCOUNT', 'Disable [user] Account', array( '[user]' => CBuser::getInstance( $userId, false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ) );
} else {
	$pageTitle		=	CBTxt::T( 'Disable My Account' );
}

if ( $pageTitle ) {
	$_CB_framework->setPageTitle( $pageTitle );
}
?>
<div class="privacyDisableAccount">
	<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'disable', 'func' => 'save', 'id' => $userId ) ); ?>" method="post" enctype="multipart/form-data" name="privacyForm" class="cb_form privacyForm form-auto cbValidation">
		<?php if ( $pageTitle ) { ?>
			<div class="privacyTitle page-header"><h3><?php echo $pageTitle; ?></h3></div>
		<?php } ?>
		<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">
			<label for="reason" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Reason' ); ?></label>
			<div class="cb_field col-sm-9">
				<textarea name="reason" class="form-control" cols="40" rows="5"<?php echo cbTooltip( null, CBTxt::T( 'Optionally input a reason for disabling your account.' ), null, null, null, null, null, 'data-hascbtooltip="true"' ); ?>></textarea>
				<?php echo getFieldIcons( 1, 0, null, CBTxt::T( 'Optionally input a reason for disabling your account.' ) ); ?>
			</div>
		</div>
		<div class="form-group cb_form_line clearfix">
			<div class="col-sm-offset-3 col-sm-9">
				<input type="submit" value="<?php echo htmlspecialchars( CBTxt::T( 'Disable Account' ) ); ?>" class="privacyButton privacyButtonSubmit btn btn-primary"<?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?> />
				<input type="button" value="<?php echo htmlspecialchars( CBTxt::T( 'Cancel' ) ); ?>" class="privacyButton privacyButtonCancel btn btn-default" onclick="window.location.href = '<?php echo addslashes( $_CB_framework->userProfileUrl( $userId ) ); ?>';" />
			</div>
		</div>
		<?php echo cbGetSpoofInputTag( 'plugin' ); ?>
	</form>
</div>