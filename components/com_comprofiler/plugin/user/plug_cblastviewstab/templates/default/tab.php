<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CB\Database\Table\UserViewTable;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var UserTable       $user
 * @var UserTable       $viewer
 * @var TabTable        $tab
 * @var UserViewTable[] $rows
 * @var int             $viewsCount
 * @var int             $guestCount
 */

$isModerator			=	Application::MyUser()->isGlobalModerator();
$isOwner				=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) );
$count					=	null;

if ( $viewsCount || $guestCount ) {
	$userViews			=	CBTxt::T( 'LAST_VIEWS_USERS', '%%COUNT%% user|%%COUNT%% users', array( '%%COUNT%%' => $viewsCount ) );
	$guestViews			=	CBTxt::T( 'LAST_VIEWS_GUESTS', '%%COUNT%% guest|%%COUNT%% guests', array( '%%COUNT%%' => $guestCount ) );

	if ( $isOwner ) {
		if ( $viewsCount && $guestCount ) {
			$count		=			CBTxt::T( 'LAST_VIEWS_YOUR_PROFILE_USERS_AND_GUESTS', 'Your profile has been viewed by [user_views] and [guest_views].', array( '[user_views]' => $userViews, '[guest_views]' => $guestViews ) );
		} else {
			$count		=			CBTxt::T( 'LAST_VIEWS_YOUR_PROFILE_USERS_OR_GUESTS', 'Your profile has been viewed by [views].', array( '[views]' => ( $viewsCount ? $userViews : '' ) . ( $guestCount ? $guestViews : '' ) ) );
		}
	} else {
		if ( $viewsCount && $guestCount ) {
			$count		=			CBTxt::T( 'LAST_VIEWS_THIS_PROFILE_USERS_AND_GUESTS', 'This profile has been viewed by [user_views] and [guest_views].', array( '[user_views]' => $userViews, '[guest_views]' => $guestViews ) );
		} else {
			$count		=			CBTxt::T( 'LAST_VIEWS_THIS_PROFILE_USERS_OR_GUESTS', 'This profile has been viewed by [views].', array( '[views]' => ( $viewsCount ? $userViews : '' ) . ( $guestCount ? $guestViews : '' ) ) );
		}
	}
}
?>
<div class="lastViewsTab">
	<?php if ( $count ) { ?>
	<div class="lastViewsHeader" style="margin-bottom: 10px;">
		<?php echo $count; ?>
	</div>
	<?php } ?>
	<div class="lastViewsRows">
		<?php
		if ( $rows ) foreach ( $rows as $row ) {
			$cbUser			=	CBuser::getInstance( $row->get( 'viewer_id', 0, GetterInterface::INT ), false );

			$details		=	CBTxt::T( 'VIEWER_VIEWED_DATE', 'Viewed: [date]', array( '[date]' => cbFormatDate( $row->get( 'lastview', null, GetterInterface::STRING ) ) ) )
							.	'<br />' . CBTxt::T( 'VIEWER_VIEWS_COUNT', 'Views: %%COUNT%%', array( '%%COUNT%%' => $row->get( 'viewscount', 0, GetterInterface::INT ) ) );

			if ( $isModerator ) {
				$details	.=	'<br />' . CBTxt::T( 'VIEWER_IP_ADDRESS', 'IP Address: [ip]', array( '[ip]' => $row->get( 'lastip', null, GetterInterface::STRING ) ) );
			}

			$avatar			=	cbTooltip( null, $details, CBTxt::T( 'VIEWER_DETAILS', 'Viewer Details' ), null, null, $cbUser->getField( 'avatar', null, 'html', 'none', 'list', 0, true ), null, 'style="display: block; width: 100%; height: 100%;"' );
		?>
		<div class="lastViewsRow cbCanvasBox cbCanvasBoxSm img-thumbnail">
			<div class="cbCanvasBoxTop bg-muted">
				<div class="cbCanvasBoxBackground">
					<?php echo $cbUser->getField( 'canvas', null, 'html', 'none', 'list', 0, true ); ?>
				</div>
				<div class="cbCanvasBoxPhoto cbCanvasBoxPhotoLeft text-left">
					<?php echo $avatar; ?>
				</div>
			</div>
			<div class="cbCanvasBoxBottom bg-default">
				<div class="cbCanvasBoxRow text-nowrap text-overflow">
					<?php echo $cbUser->getField( 'onlinestatus', null, 'html', 'none', 'profile', 0, true, array( 'params' => array( 'displayMode' => 1 ) ) ); ?>
					<?php echo $cbUser->getField( 'formatname', null, 'html', 'none', 'list', 0, true, array( 'params' => array( 'fieldHoverCanvas' => false ) ) ); ?>
				</div>
			</div>
		</div>
		<?php } else { ?>
			<?php echo ( $isOwner ? CBTxt::T( 'You have no views.' ) : CBTxt::T( 'This user has no views.' ) ); ?>
		<?php } ?>
	</div>
</div>
