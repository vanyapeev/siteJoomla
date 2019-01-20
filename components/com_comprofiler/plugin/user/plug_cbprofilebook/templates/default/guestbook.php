<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\ProfileBook\Table\EntryTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\ProfileBook\CBProfileBook;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var bool         $isOwner
 * @var bool         $isModerator
 * @var UserTable    $user
 * @var UserTable    $viewer
 * @var TabTable     $tab
 * @var EntryTable[] $rows
 * @var cbPageNav    $pageNav
 */

global $_CB_framework, $_PLUGINS;

cbValidator::loadValidation();
initToolTip();

$js					=	"$( '.pbGuestbookNew:not(.pbGuestbookNewOpen)' ).on( 'click', function() {"
					.		"$( this ).find( '.pbGuestbookNewFooter,.pbGuestbookInputGroup' ).removeClass( 'hidden' );"
					.		"$( this ).addClass( 'pbGuestbookNewOpen' );"
					.		"$( this ).find( 'textarea' ).attr( 'rows', 3 ).autosize({"
					.			"append: '',"
					.			"resizeDelay: 0,"
					.			"placeholder: false"
					.		"});"
					.	"});"
					.	"$( '.pbGuestbookNewFeedback:not(.pbGuestbookNewFeedbackOpen)' ).on( 'click', function() {"
					.		"$( this ).find( '.pbGuestbookNewFeedbackFooter,.pbGuestbookInputGroup' ).removeClass( 'hidden' );"
					.		"$( this ).addClass( 'pbGuestbookNewFeedbackOpen' );"
					.		"$( this ).find( 'textarea' ).autosize({"
					.			"append: '',"
					.			"resizeDelay: 0,"
					.			"placeholder: false"
					.		"});"
					.	"});"
					.	"$( '.pbGuestbook .cbMoreLess' ).cbmoreless();"
					.	"$( '.pbGuestbook .rateit' ).rateit();";

$_CB_framework->outputCbJQuery( $js, array( 'cbmoreless', 'autosize', 'rateit' ) );

$showCaptcha		=	$tab->params->get( 'pbCaptcha', 1, GetterInterface::INT );

if ( $isModerator || ( ( $showCaptcha == 1 ) && $viewer->get( 'id', 0, GetterInterface::INT ) ) ) {
	$showCaptcha	=	0;
}

if ( $showCaptcha ) {
	$showCaptcha	=	implode( '', $_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( true ) ) );
}

$showRatings		=	$tab->params->get( 'pbEnableRating', 2, GetterInterface::INT );
$cbUserOwner		=	CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false );
?>
<div class="pbGuestbook">
	<?php echo implode( '', $_PLUGINS->trigger( 'pb_onBeforeDisplayGuestbook', array( &$rows, &$pageNav, $viewer, $user, $tab ) ) ); ?>
	<div class="pbGuestbookRows">
		<?php if ( ( ! $isOwner ) && ( $isModerator || $viewer->get( 'id', 0, GetterInterface::INT ) || ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) && $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) ) ) { ?>
		<div class="pbGuestbookRow pbGuestbookNew panel panel-default">
			<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'save' ) ); ?>" method="post" name="pbGuestbookFormNew" class="pbGuestbookForm cbValidation">
				<div class="pbGuestbookInputGroup pbGuestbookInputMessageContainer cb_form_line clearfix">
					<textarea name="postercomment" rows="1" class="pbGuestbookInput pbGuestbookInputMessage form-control no-border required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Would you like to sign this guestbook?' ) ); ?>"></textarea>
				</div>
				<?php if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) { ?>
				<div class="pbGuestbookInputGroup pbGuestbookInputNameContainer border-default cb_form_line clearfix hidden">
					<span class="pbGuestbookInputGroupLabel form-control"><?php echo CBTxt::T( 'Name' ); ?></span>
					<div class="pbGuestbookInputGroupInput border-default cb_field">
						<input type="text" name="postername" class="pbGuestbookInput pbGuestbookInputName form-control no-border required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Please provide your name.' ) ); ?>" />
					</div>
				</div>
				<div class="pbGuestbookInputGroup pbGuestbookInputEmailContainer border-default cb_form_line clearfix hidden">
					<span class="pbGuestbookInputGroupLabel form-control"><?php echo CBTxt::T( 'Email' ); ?></span>
					<div class="pbGuestbookInputGroupInput border-default cb_field">
						<input type="text" name="posteremail" class="pbGuestbookInput pbGuestbookInputEmail form-control no-border required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Please provide your email address.' ) ); ?>" />
					</div>
				</div>
				<?php } ?>
				<?php if ( $showRatings ) { ?>
				<div class="pbGuestbookInputGroup pbGuestbookInputRateContainer border-default cb_form_line clearfix hidden">
					<span class="pbGuestbookInputGroupLabel form-control"><?php echo CBTxt::T( 'Rate' ); ?></span>
					<div class="pbGuestbookInputGroupInput border-default cb_field">
						<input type="text" id="pbGuestbookRatingNew" name="postervote" value="0"<?php echo ( $showRatings == 3 ? ' class="required"' . cbValidator::getRuleHtmlAttributes( 'range', array( 1, 5 ) ) : null ); ?> />
						<div class="pbGuestbookInput pbGuestbookInputRating rateit form-control no-border" data-rateit-backingfld="#pbGuestbookRatingNew" data-rateit-step="1" data-rateit-value="0" data-rateit-ispreset="true" data-rateit-resetable="<?php echo ( $showRatings == 3 ? 'false' : 'true' ); ?>" data-rateit-min="0" data-rateit-max="5"></div>
					</div>
				</div>
				<?php } ?>
				<?php if ( $showCaptcha ) { ?>
				<div class="pbWallInputGroup pbWallInputCaptchaContainer border-default cb_form_line clearfix hidden">
					<span class="pbWallInputGroupLabel form-control"><?php echo CBTxt::T( 'Captcha' ); ?></span>
					<div class="pbWallInputGroupInput border-default">
						<?php echo $showCaptcha; ?>
					</div>
				</div>
				<?php } ?>
				<div class="pbGuestbookNewFooter panel-footer text-right hidden">
					<button type="submit" class="pbButton pbButtonSubmit btn btn-primary btn-xs" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?>><?php echo CBTxt::T( 'Sign Guestbook' ); ?></button>
				</div>
				<input type="hidden" name="mode" value="g" />
				<input type="hidden" name="userid" value="<?php echo $user->get( 'id', 0, GetterInterface::INT ); ?>" />
				<?php echo cbGetSpoofInputTag( 'plugin' ); ?>
			</form>
		</div>
		<?php } ?>
		<?php
		if ( $rows ) foreach ( $rows as $row ) {
			$integrations					=	implode( '', $_PLUGINS->trigger( 'pb_onDisplayGuestbook', array( &$row, $viewer, $user, $tab ) ) );
			$rowOwner						=	( $viewer->get( 'id', 0, GetterInterface::INT ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'posterid', 0, GetterInterface::INT ) ) );
			$cbUser							=	CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false );
			$feedback						=	$row->get( 'feedback', null, GetterInterface::HTML );
			$menu							=	null;

			if ( $isModerator || $isOwner || $rowOwner ) {
				$menuItems					=	'<ul class="pbGuestbookMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
											.		'<li class="pbGuestbookMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( $isModerator || $isOwner ) {
					if ( $row->get( 'published' ) == -1 ) {
						$menuItems			.=		'<li class="pbGuestbookMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems			.=		'<li class="pbGuestbookMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this guestbook signature?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems			.=		'<li class="pbGuestbookMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				$menuItems					.=		'<li class="pbGuestbookMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this guestbook signature?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
											.	'</ul>';

				$menu						=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );
			}

			$feedbackMenu					=	null;

			if ( $feedback && ( $isOwner || $isModerator ) ) {
				$menuItems					=	'<ul class="pbGuestbookMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
											.		'<li class="pbGuestbookMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>'
											.		'<li class="pbGuestbookMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this feedback?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
											.	'</ul>';

				$feedbackMenu				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );
			}
		?>
		<div class="pbGuestbookRow panel panel-default">
			<div class="pbGuestbookRowHeader media panel-heading clearfix">
				<div class="pbGuestbookRowAvatar media-left">
				<?php
				if ( ! $row->get( 'posterid', 0, GetterInterface::INT ) ) {
					echo '<img src="' . selectTemplate() . 'images/avatar/tnnophoto_n.png" class="cbImgPict cbThumbPict img-thumbnail" />';
				} else {
					echo $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true );
				}
				?>
				</div>
				<div class="pbGuestbookRowDetails media-body">
					<div class="pbGuestbookRowAuthor text-muted">
						<strong>
						<?php
						if ( ! $row->get( 'posterid', 0, GetterInterface::INT ) ) {
							echo $row->get( 'postername', CBTxt::T( 'Anonymous' ), GetterInterface::STRING );
						} else {
							echo $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true );
						}
						?>
						</strong>
					</div>
					<div class="pbGuestbookRowDate text-small text-muted">
						<?php echo cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ); ?>
						<?php if ( $row->get( 'editdate', null, GetterInterface::STRING ) && ( $row->get( 'editdate', null, GetterInterface::STRING ) != '0000-00-00 00:00:00' ) ) { ?>
							<span class="pbGuestbookRowEdited fa fa-edit" title="<?php echo htmlspecialchars( cbFormatDate( $row->get( 'editdate', null, GetterInterface::STRING ) ) ); ?>"></span>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="pbGuestbookRowMessage panel-body">
				<div class="cbMoreLess">
					<div class="cbMoreLessContent">
						<?php echo CBProfileBook::parseMessage( $row->get( 'postercomment', null, GetterInterface::HTML ), $tab ); ?>
					</div>
					<div class="cbMoreLessOpen fade-edge hidden">
						<a href="javascript: void(0);" class="cbMoreLessButton"><?php echo CBTxt::T( 'See More' ); ?></a>
					</div>
				</div>
			</div>
			<?php if ( ( ( ! $rowOwner ) && ( $isOwner || $isModerator ) ) || $feedback ) { ?>
			<div class="pbGuestbookRowFooter panel-footer">
				<div class="pbGuestbookFeedback">
					<div class="pbGuestbookRows">
						<?php if ( $feedback ) { ?>
						<div class="pbGuestbookRow">
							<div class="pbGuestbookRowHeader media clearfix">
								<div class="pbGuestbookRowAvatar media-left">
									<?php echo $cbUserOwner->getField( 'avatar', null, 'html', 'none', 'list', 0, true ); ?>
								</div>
								<div class="pbGuestbookRowDetails media-body">
									<div class="cbMoreLess">
										<div class="cbMoreLessContent">
											<strong> <?php echo $cbUserOwner->getField( 'formatname', null, 'html', 'none', 'list', 0, true ); ?> </strong>
											<?php echo CBProfileBook::parseMessage( $feedback, $tab ); ?>
										</div>
										<div class="cbMoreLessOpen fade-edge hidden">
											<a href="javascript: void(0);" class="cbMoreLessButton"><?php echo CBTxt::T( 'See More' ); ?></a>
										</div>
									</div>
								</div>
							</div>
							<?php if ( ( ! $rowOwner ) && ( $isOwner || $isModerator ) ) { ?>
							<div class="pbGuestbookMenu btn-group">
								<button type="button" <?php echo $feedbackMenu; ?>><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>
							</div>
							<?php } ?>
						</div>
						<?php } else { ?>
						<div class="pbGuestbookRow pbGuestbookNewFeedback<?php echo ( $feedback ? ' hidden' : null ); ?>">
							<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ); ?>" method="post" name="pbGuestbookFormNew" class="pbGuestbookForm cbValidation">
								<div class="pbGuestbookInputGroup pbGuestbookInputFeedbackContainer cb_form_line clearfix">
									<textarea name="feedback" rows="1" class="pbGuestbookInput pbGuestbookInputFeedback form-control required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Write feedback...' ) ); ?>"><?php echo htmlspecialchars( $feedback ); ?></textarea>
								</div>
								<div class="pbGuestbookNewFeedbackFooter text-right hidden">
									<button type="submit" class="pbButton pbButtonSubmit btn btn-primary btn-xs" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?>><?php echo CBTxt::T( 'Save Feedback' ); ?></button>
								</div>
								<?php echo cbGetSpoofInputTag( 'plugin' ); ?>
							</form>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if ( $integrations ) { ?>
			<div class="pbGuestbookRowIntegrations panel-footer">
				<?php echo $integrations; ?>
			</div>
			<?php } ?>
			<?php if ( $menu || ( $row->get( 'postervote', 0, GetterInterface::INT ) && $showRatings ) || ( ( ! $row->get( 'status', 0, GetterInterface::INT ) ) && $isOwner ) ) { ?>
			<div class="pbGuestbookMenu text-right">
				<?php if ( ( ! $row->get( 'status', 0, GetterInterface::INT ) ) && $isOwner ) { ?>
					<span class="pbGuestbookRowNew label label-success"><?php echo CBTxt::T( 'New' ); ?></span>
				<?php } ?>
				<?php if ( $menu ) { ?>
				<div class="pbGuestbookRowMenu btn-group">
					<button type="button" <?php echo $menu; ?>><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>
				</div>
				<?php } ?>
				<?php if ( $row->get( 'postervote', 0, GetterInterface::INT ) && $showRatings ) { ?>
				<div class="pbGuestbookRowRating">
					<div class="rateit" data-rateit-step="1" data-rateit-value="<?php echo $row->get( 'postervote', 0, GetterInterface::INT ); ?>" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-min="0" data-rateit-max="5"></div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } else { ?>
		<div class="pbGuestbookRow pbGuestbookEmpty">
			<?php echo ( $isOwner ? CBTxt::T( 'Your guestbook currently has no signatures.' ) : CBTxt::T( 'This users guestbook currently has no signatures.' ) ); ?>
		</div>
		<?php } ?>
	</div>
	<?php if ( $tab->params->get( 'pbPagingEngabbled', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) { ?>
	<div class="pbGuestbookPaging text-center">
		<?php echo $pageNav->getListLinks(); ?>
	</div>
	<?php } ?>
	<?php echo implode( '', $_PLUGINS->trigger( 'pb_onAfterDisplayGuestbook', array( $rows, $pageNav, $viewer, $user, $tab ) ) ); ?>
</div>