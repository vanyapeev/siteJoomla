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

$js					=	"$( '.pbWallNew:not(.pbWallNewOpen)' ).on( 'click', function() {"
					.		"$( this ).find( '.pbWallNewFooter,.pbWallInputGroup' ).removeClass( 'hidden' );"
					.		"$( this ).addClass( 'pbWallNewOpen' );"
					.		"$( this ).find( 'textarea' ).attr( 'rows', 3 ).autosize({"
					.			"append: '',"
					.			"resizeDelay: 0,"
					.			"placeholder: false"
					.		"});"
					.	"});"
					.	"$( '.pbWallNewFeedback:not(.pbWallNewFeedbackOpen)' ).on( 'click', function() {"
					.		"$( this ).find( '.pbWallNewFeedbackFooter,.pbWallInputGroup' ).removeClass( 'hidden' );"
					.		"$( this ).addClass( 'pbWallNewFeedbackOpen' );"
					.		"$( this ).find( 'textarea' ).autosize({"
					.			"append: '',"
					.			"resizeDelay: 0,"
					.			"placeholder: false"
					.		"});"
					.	"});"
					.	"$( '.pbWall .cbMoreLess' ).cbmoreless();"
					.	"$( '.pbWall .rateit' ).rateit();";

$_CB_framework->outputCbJQuery( $js, array( 'cbmoreless', 'autosize', 'rateit' ) );

$showCaptcha		=	$tab->params->get( 'pbCaptcha', 1, GetterInterface::INT );

if ( $isModerator || ( ( $showCaptcha == 1 ) && $viewer->get( 'id', 0, GetterInterface::INT ) ) ) {
	$showCaptcha	=	0;
}

if ( $showCaptcha ) {
	$showCaptcha	=	implode( '', $_PLUGINS->trigger( 'onGetCaptchaHtmlElements', array( true ) ) );
}

$showRatings		=	$tab->params->get( 'pbEnableRating', 0, GetterInterface::INT );
$cbUserOwner		=	CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false );
?>
<div class="pbWall">
	<?php echo implode( '', $_PLUGINS->trigger( 'pb_onBeforeDisplayWall', array( &$rows, &$pageNav, $viewer, $user, $tab ) ) ); ?>
	<div class="pbWallRows">
		<?php if ( $isOwner || $isModerator || $viewer->get( 'id', 0, GetterInterface::INT ) || ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) && $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) ) { ?>
		<div class="pbWallRow pbWallNew panel panel-default">
			<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'save' ) ); ?>" method="post" name="pbWallFormNew" class="pbWallForm cbValidation">
				<div class="pbWallInputGroup pbWallInputMessageContainer cb_form_line clearfix">
					<textarea name="postercomment" rows="1" class="pbWallInput pbWallInputMessage form-control no-border required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Have a post to share?' ) ); ?>"></textarea>
				</div>
				<?php if ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) { ?>
				<div class="pbWallInputGroup pbWallInputNameContainer border-default cb_form_line clearfix hidden">
					<span class="pbWallInputGroupLabel form-control"><?php echo CBTxt::T( 'Name' ); ?></span>
					<div class="pbWallInputGroupInput border-default">
						<input type="text" name="postername" class="pbWallInput pbWallInputName form-control no-border required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Please provide your name.' ) ); ?>" />
					</div>
				</div>
				<div class="pbWallInputGroup pbWallInputEmailContainer border-default cb_form_line clearfix hidden">
					<span class="pbWallInputGroupLabel form-control"><?php echo CBTxt::T( 'Email' ); ?></span>
					<div class="pbWallInputGroupInput border-default">
						<input type="text" name="posteremail" class="pbWallInput pbWallInputEmail form-control no-border required email" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Please provide your email address.' ) ); ?>" />
					</div>
				</div>
				<?php } ?>
				<?php if ( $showRatings ) { ?>
				<div class="pbWallInputGroup pbWallInputRateContainer border-default cb_form_line clearfix hidden">
					<span class="pbWallInputGroupLabel form-control"><?php echo CBTxt::T( 'Rate' ); ?></span>
					<div class="pbWallInputGroupInput border-default">
						<input type="text" id="pbWallRatingNew" name="postervote" value="0"<?php echo ( $showRatings == 3 ? ' class="required"' . cbValidator::getRuleHtmlAttributes( 'range', array( 1, 5 ) ) : null ); ?> />
						<div class="pbWallInput pbWallInputRating rateit form-control no-border" data-rateit-backingfld="#pbWallRatingNew" data-rateit-step="1" data-rateit-value="0" data-rateit-ispreset="true" data-rateit-resetable="<?php echo ( $showRatings == 3 ? 'false' : 'true' ); ?>" data-rateit-min="0" data-rateit-max="5"></div>
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
				<div class="pbWallNewFooter panel-footer text-right hidden">
					<button type="submit" class="pbButton pbButtonSubmit btn btn-primary btn-xs" <?php echo cbValidator::getSubmitBtnHtmlAttributes(); ?>><?php echo CBTxt::T( 'Post' ); ?></button>
				</div>
				<input type="hidden" name="mode" value="w" />
				<input type="hidden" name="userid" value="<?php echo $user->get( 'id', 0, GetterInterface::INT ); ?>" />
				<?php echo cbGetSpoofInputTag( 'plugin' ); ?>
			</form>
		</div>
		<?php } ?>
		<?php
		if ( $rows ) foreach ( $rows as $row ) {
			$integrations					=	implode( '', $_PLUGINS->trigger( 'pb_onDisplayWall', array( &$row, $viewer, $user, $tab ) ) );
			$rowOwner						=	( $viewer->get( 'id', 0, GetterInterface::INT ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'posterid', 0, GetterInterface::INT ) ) );
			$cbUser							=	CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false );
			$feedback						=	$row->get( 'feedback', null, GetterInterface::HTML );
			$menu							=	null;

			if ( $isModerator || $isOwner || $rowOwner ) {
				$menuItems					=	'<ul class="pbWallMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
											.		'<li class="pbWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( $isModerator || $isOwner ) {
					if ( $row->get( 'published' ) == -1 ) {
						$menuItems			.=		'<li class="pbWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Approve' ) . '</a></li>';
					} elseif ( $row->get( 'published' ) == 1 ) {
						$menuItems			.=		'<li class="pbWallMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this post?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
					} else {
						$menuItems			.=		'<li class="pbWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
					}
				}

				$menuItems					.=		'<li class="pbWallMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this post?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
											.	'</ul>';

				$menu						=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );
			}

			$feedbackMenu					=	null;

			if ( $feedback && ( $isOwner || $isModerator ) ) {
				$menuItems					=	'<ul class="pbWallMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
											.		'<li class="pbWallMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>'
											.		'<li class="pbWallMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this feedback?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
											.	'</ul>';

				$feedbackMenu				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );
			}
		?>
		<div class="pbWallRow panel panel-default">
			<div class="pbWallRowHeader media panel-heading clearfix">
				<div class="pbWallRowAvatar media-left">
				<?php
				if ( ! $row->get( 'posterid', 0, GetterInterface::INT ) ) {
					echo '<img src="' . selectTemplate() . 'images/avatar/tnnophoto_n.png" class="cbImgPict cbThumbPict img-thumbnail" />';
				} else {
					echo $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true );
				}
				?>
				</div>
				<div class="pbWallRowDetails media-body">
					<div class="pbWallRowAuthor text-muted">
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
					<div class="pbWallRowDate text-small text-muted">
						<?php echo cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, 'timeago' ); ?>
						<?php if ( $row->get( 'editdate', null, GetterInterface::STRING ) && ( $row->get( 'editdate', null, GetterInterface::STRING ) != '0000-00-00 00:00:00' ) ) { ?>
							<span class="pbWallRowNewEdited fa fa-edit" title="<?php echo htmlspecialchars( cbFormatDate( $row->get( 'editdate', null, GetterInterface::STRING ) ) ); ?>"></span>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="pbWallRowMessage panel-body">
				<div class="cbMoreLess">
					<div class="cbMoreLessContent">
						<?php echo CBProfileBook::parseMessage( $row->get( 'postercomment', null, GetterInterface::HTML ), $tab ); ?>
					</div>
					<div class="cbMoreLessOpen fade-edge hidden">
						<a href="javascript: void(0);" class="cbMoreLessButton"><?php echo CBTxt::T( 'See More' ); ?></a>
					</div>
				</div>
			</div>
			<?php if ( ( ( ! $rowOwner ) && ( $isOwner || $isModerator ) ) || $feedback || $integrations ) { ?>
			<div class="pbWallRowFooter panel-footer">
				<div class="pbWallFeedback">
					<div class="pbWallRows">
						<?php if ( $feedback ) { ?>
						<div class="pbWallRow">
							<div class="pbWallRowHeader media clearfix">
								<div class="pbWallRowAvatar media-left">
									<?php echo $cbUserOwner->getField( 'avatar', null, 'html', 'none', 'list', 0, true ); ?>
								</div>
								<div class="pbWallRowDetails media-body">
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
							<div class="pbWallMenu btn-group">
								<button type="button" <?php echo $feedbackMenu; ?>><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>
							</div>
							<?php } ?>
						</div>
						<?php } else { ?>
						<div class="pbWallRow pbWallNewFeedback<?php echo ( $feedback ? ' hidden' : null ); ?>">
							<form action="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'feedback', 'func' => 'save', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ); ?>" method="post" name="pbWallFormNew" class="pbWallForm cbValidation">
								<div class="pbWallInputGroup pbWallInputFeedbackContainer cb_form_line clearfix">
									<textarea name="feedback" rows="1" class="pbWallInput pbWallInputFeedback form-control required" placeholder="<?php echo htmlspecialchars( CBTxt::T( 'Write feedback...' ) ); ?>"><?php echo htmlspecialchars( $feedback ); ?></textarea>
								</div>
								<div class="pbWallNewFeedbackFooter text-right hidden">
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
			<div class="pbWallRowIntegrations panel-footer">
				<?php echo $integrations; ?>
			</div>
			<?php } ?>
			<?php if ( $menu || ( $row->get( 'postervote', 0, GetterInterface::INT ) && $showRatings ) || ( ( ! $row->get( 'status', 0, GetterInterface::INT ) ) && $isOwner ) ) { ?>
			<div class="pbWallMenu text-right">
				<?php if ( ( ! $row->get( 'status', 0, GetterInterface::INT ) ) && $isOwner ) { ?>
					<span class="pbWallRowNew label label-success"><?php echo CBTxt::T( 'New' ); ?></span>
				<?php } ?>
				<?php if ( $menu ) { ?>
				<div class="pbWallRowMenu btn-group">
					<button type="button" <?php echo $menu; ?>><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>
				</div>
				<?php } ?>
				<?php if ( $row->get( 'postervote', 0, GetterInterface::INT ) && $showRatings ) { ?>
				<div class="pbWallRowRating">
					<div class="rateit" data-rateit-step="1" data-rateit-value="<?php echo $row->get( 'postervote', 0, GetterInterface::INT ); ?>" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-min="0" data-rateit-max="5"></div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php } else { ?>
		<div class="pbWallRow pbWallEmpty">
			<?php echo ( $isOwner ? CBTxt::T( 'You currently have no posts.' ) : CBTxt::T( 'This user currently has no posts.' ) ); ?>
		</div>
		<?php } ?>
	</div>
	<?php if ( $tab->params->get( 'pbPagingEngabbled', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) { ?>
	<div class="pbWallPaging text-center">
		<?php echo $pageNav->getListLinks(); ?>
	</div>
	<?php } ?>
	<?php echo implode( '', $_PLUGINS->trigger( 'pb_onAfterDisplayWall', array( $rows, $pageNav, $viewer, $user, $tab ) ) ); ?>
</div>