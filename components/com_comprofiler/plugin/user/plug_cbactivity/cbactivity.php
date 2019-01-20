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
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\CBActivity;
use CB\Plugin\Activity\Activity;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteActivity', '\CB\Plugin\Activity\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\Activity\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onPrepareMenus', 'getNotifications','\CB\Plugin\Activity\Trigger\MenuTrigger' );

$_PLUGINS->registerFunction( 'activity_onLoadActivityStream', 'activityLoad', '\CB\Plugin\Activity\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onLoadNotificationsStream', 'activityLoad', '\CB\Plugin\Activity\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onAssetSource', 'assetSource', '\CB\Plugin\Activity\Trigger\ActivityTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array(	'activity'			=>	'\CB\Plugin\Activity\Field\ActivityField',
											'notifications'		=>	'\CB\Plugin\Activity\Field\NotificationsField',
											'comments'			=>	'\CB\Plugin\Activity\Field\CommentsField',
											'follow'			=>	'\CB\Plugin\Activity\Field\FollowField',
											'like'				=>	'\CB\Plugin\Activity\Field\LikeField'
										));

class cbactivityTab extends cbTabHandler
{

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params	=	new Registry( $tab->params );
		}

		$asset				=	$tab->params->get( 'activity_asset', null, GetterInterface::STRING );

		if ( ! $asset ) {
			if ( $user->get( 'id', 0, GetterInterface::INT ) != Application::MyUser()->getUserId() ) {
				$asset		=	array( 'user', 'global' );
			} else {
				$asset		=	null;
			}
		}

		$activity			=	new Activity( $asset, $user );

		$activity->parse( $tab->params, 'activity_' );

		$activity->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );

		$layout				=	$tab->params->get( 'activity_layout', 'stream', GetterInterface::STRING );

		if ( $layout == 'button' ) {
			return $activity->activity( 'button' );
		} else {
			if ( ( ! Application::Config()->get( 'showEmptyTabs', 1, GetterInterface::INT ) ) && ( ! $activity->rows( 'count' ) ) && ( ! CBActivity::canCreate( 'activity', $activity ) ) ) {
				return null;
			}

			return $activity->activity();
		}
	}
}