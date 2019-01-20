<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
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

global $_CB_framework, $_PLUGINS;

switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
	case 'g':
		$pageTitle		=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Edit Signature' ) : CBTxt::T( 'New Signature' ) );
		$postTitle		=	CBTxt::T( 'Signature' );
		$buttonTitle	=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Update Signature' ) : CBTxt::T( 'Sign Guestbook' ) );
		$showRatings	=	$tab->params->get( 'pbEnableRating', 2, GetterInterface::INT );
		break;
	case 'b':
		$pageTitle		=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Edit Blog' ) : CBTxt::T( 'New Blog' ) );
		$postTitle		=	CBTxt::T( 'Blog' );
		$buttonTitle	=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Update Blog' ) : CBTxt::T( 'Create Blog' ) );
		$showRatings	=	0;
		break;
	case 'w':
	default:
		$pageTitle		=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Edit Post' ) : CBTxt::T( 'New Post' ) );
		$postTitle		=	CBTxt::T( 'Post' );
		$buttonTitle	=	( $row->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'Update Post' ) : CBTxt::T( 'Post' ) );
		$showRatings	=	$tab->params->get( 'pbEnableRating', 0, GetterInterface::INT );
		break;
}

$_CB_framework->setPageTitle( $pageTitle );

cbValidator::loadValidation();
initToolTip();

$_CB_framework->outputCbJQuery( "$( '.pbEdit .rateit' ).rateit();", 'rateit' );

$showCaptcha		=	$tab->params->get( 'pbCaptcha', 1, GetterInterface::INT );

if ( Application::MyUser()->isGlobalModerator() || ( ( $showCaptcha == 1 ) && $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( $row->get( 'mode', null, GetterInterface::STRING ) == 'b' ) || $row->get( 'id', 0, GetterInterface::INT ) ) {
	$showCaptcha	=	0;
}

if ( $showCaptcha ) {
	$showCaptcha	=	implode( '', $_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( true ) ) );
}
?>
<div class="pbEdit">
	<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ); ?>" method="post" name="pbFormEdit" class="pbForm cb_form form-auto cbValidation">
		<?php if ( $pageTitle ) { ?>
		<div class="pbEditTitle page-header"><h3><?php echo $pageTitle; ?></h3></div>
		<?php } ?>
		<?php if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'userid', 0, GetterInterface::INT ) ) ) || Application::MyUser()->isGlobalModerator() ) { ?>
		<div class="cbft_select cbtt_select form-group cb_form_line clearfix">
			<label for="published" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Published' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['published']; ?>
			</div>
		</div>
		<?php } ?>
		<?php if ( $row->get( 'mode', null, GetterInterface::STRING ) == 'b' ) { ?>
		<div class="cbft_text cbtt_input form-group cb_form_line clearfix">
			<label for="postertitle" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Title' ); ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['postertitle']; ?>
			</div>
		</div>
		<?php } ?>
		<div class="cbft_textarea cbtt_textarea form-group cb_form_line clearfix">
			<label for="postercomment" class="col-sm-3 control-label"><?php echo $postTitle; ?></label>
			<div class="cb_field col-sm-9">
				<?php echo $input['postercomment']; ?>
			</div>
		</div>
		<?php if ( in_array( $row->get( 'mode', null, GetterInterface::STRING ), array( 'g', 'w' ) ) ) { ?>
			<?php if ( ! $row->get( 'posterid', 0, GetterInterface::INT ) ) { ?>
			<div class="cbft_text cbtt_input form-group cb_form_line clearfix">
				<label for="postername" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Name' ); ?></label>
				<div class="cb_field col-sm-9">
					<?php echo $input['postername']; ?>
				</div>
			</div>
			<?php if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || Application::MyUser()->isGlobalModerator() ) { ?>
			<div class="cbft_text cbtt_input form-group cb_form_line clearfix">
				<label for="posteremail" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Email' ); ?></label>
				<div class="cb_field col-sm-9">
					<?php echo $input['posteremail']; ?>
				</div>
			</div>
			<?php } ?>
			<?php } ?>
			<?php if ( $showRatings ) { ?>
			<div class="cbft_delimiter form-group cb_form_line clearfix">
				<label for="postervote" class="col-sm-3 control-label"><?php echo CBTxt::T( 'Rate' ); ?></label>
				<div class="cb_field col-sm-9">
					<input type="text" id="pbRatingEdit" name="postervote" value="<?php echo $row->get( 'postervote', 0, GetterInterface::INT ); ?>"<?php echo ( $showRatings == 3 ? ' class="required"' . cbValidator::getRuleHtmlAttributes( 'range', array( 1, 5 ) ) : null ); ?> />
					<div class="rateit" data-rateit-backingfld="#pbRatingEdit" data-rateit-step="1" data-rateit-value="<?php echo $row->get( 'postervote', 0, GetterInterface::INT ); ?>" data-rateit-ispreset="true" data-rateit-resetable="<?php echo ( $showRatings == 3 ? 'false' : 'true' ); ?>" data-rateit-min="0" data-rateit-max="5"></div>
				</div>
			</div>
			<?php } ?>
			<?php if ( $showCaptcha ) { ?>
			<div class="cbft_delimiter form-group cb_form_line clearfix">
				<label class="col-sm-3 control-label"><?php echo CBTxt::T( 'Captcha' ); ?></label>
				<div class="cb_field col-sm-9">
					<?php echo $showCaptcha; ?>
				</div>
			</div>
			<?php } ?>
		<?php } ?>
		<div class="cbft_delimiter form-group cb_form_line clearfix">
			<div class="col-sm-offset-3 col-sm-9">
				<input type="submit" value="<?php echo htmlspecialchars( $buttonTitle ); ?>" class="pbButton pbButtonSubmit btn btn-primary" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?> />
				<input type="button" value="<?php echo htmlspecialchars( CBTxt::T( 'Cancel' ) ); ?>" class="pbButton pbButtonCancel btn btn-default" onclick="cbjQuery.cbconfirm( '<?php echo addslashes( CBTxt::T( 'Are you sure you want to cancel? All unsaved data will be lost!' ) ); ?>' ).done( function() { window.location.href = '<?php echo addslashes( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ) ); ?>'; })" />
			</div>
		</div>
		<input type="hidden" name="mode" value="<?php echo $row->get( 'mode', null, GetterInterface::STRING ); ?>" />
		<input type="hidden" name="userid" value="<?php echo $row->get( 'userid', 0, GetterInterface::INT ); ?>" />
		<?php echo cbGetSpoofInputTag( 'plugin' ); ?>
	</form>
</div>
