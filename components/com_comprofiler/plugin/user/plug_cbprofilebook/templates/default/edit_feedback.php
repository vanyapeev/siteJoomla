<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\ProfileBook\Table\EntryTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var UserTable  $viewer
 * @var TabTable   $tab
 * @var EntryTable $row
 */

global $_CB_framework;

$pageTitle	=	CBTxt::T( 'Edit Feedback' );

$_CB_framework->setPageTitle( $pageTitle );

cbValidator::loadValidation();
initToolTip();
?>
<div class="pbEditFeedback">
	<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ); ?>" method="post" name="pbFormEdit" class="pbForm cb_form form-auto cbValidation">
		<?php if ( $pageTitle ) { ?>
		<div class="pbEditFeedbackTitle page-header"><h3><?php echo $pageTitle; ?></h3></div>
		<?php } ?>
		<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">
			<label for="feedback" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Feedback' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['feedback']; ?>
			</div>
		</div>
		<div class="cbft_delimiter form-group cb_form_line clearfix">
			<div class="col-sm-offset-3 col-sm-9">
				<input type="submit" value="<?php echo htmlspecialchars( CBTxt::T( 'Update Feedback' ) ); ?>" class="pbButton pbButtonSubmit btn btn-primary" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?> />
				<input type="button" value="<?php echo htmlspecialchars( CBTxt::T( 'Cancel' ) ); ?>" class="pbButton pbButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( '<?php echo addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ); ?>' ).done( function() { window.location.href = '<?php echo addslashes( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ) ); ?>'; })" />
			</div>
		</div>
		<?php echo cbGetSpoofInputTag( 'plugin' ); ?>
	</form>
</div>
