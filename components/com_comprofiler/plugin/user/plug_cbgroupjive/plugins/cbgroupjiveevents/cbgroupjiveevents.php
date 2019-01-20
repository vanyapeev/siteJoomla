<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2014 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'gj_onAdminMenu', 'adminMenu', '\CB\Plugin\GroupJiveEvents\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onBuildRoute', 'build', '\CB\Plugin\GroupJiveEvents\Trigger\RouterTrigger' );
$_PLUGINS->registerFunction( 'onParseRoute', 'parse', '\CB\Plugin\GroupJiveEvents\Trigger\RouterTrigger' );

$_PLUGINS->registerFunction( 'gj_onCanCreateGroupContent', 'canCreate', '\CB\Plugin\GroupJiveEvents\Trigger\EventsTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroupEdit', 'editGroup', '\CB\Plugin\GroupJiveEvents\Trigger\EventsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteGroup', 'deleteGroup', '\CB\Plugin\GroupJiveEvents\Trigger\EventsTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayNotifications', 'editNotifications', '\CB\Plugin\GroupJiveEvents\Trigger\EventsTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterCreateUser', 'storeNotifications', '\CB\Plugin\GroupJiveEvents\Trigger\EventsTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroup', 'showEvents', '\CB\Plugin\GroupJiveEvents\Trigger\EventsTrigger' );

$_PLUGINS->registerFunction( 'activity_onLoadActivityStream', 'activityPrefetch', '\CB\Plugin\GroupJiveEvents\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onLoadNotificationsStream', 'activityPrefetch', '\CB\Plugin\GroupJiveEvents\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamActivity', 'activityDisplay', '\CB\Plugin\GroupJiveEvents\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamNotification', 'activityDisplay', '\CB\Plugin\GroupJiveEvents\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onAssetSource', 'assetSource', '\CB\Plugin\GroupJiveEvents\Trigger\ActivityTrigger' );