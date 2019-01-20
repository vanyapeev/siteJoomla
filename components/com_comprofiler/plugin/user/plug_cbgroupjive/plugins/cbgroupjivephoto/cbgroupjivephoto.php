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

$_PLUGINS->registerFunction( 'gj_onAdminMenu', 'adminMenu', '\CB\Plugin\GroupJivePhoto\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onBuildRoute', 'build', '\CB\Plugin\GroupJivePhoto\Trigger\RouterTrigger' );
$_PLUGINS->registerFunction( 'onParseRoute', 'parse', '\CB\Plugin\GroupJivePhoto\Trigger\RouterTrigger' );

$_PLUGINS->registerFunction( 'gj_onCanCreateGroupContent', 'canCreate', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroupEdit', 'editGroup', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteCategory', 'deleteCategory', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterDeleteGroup', 'deleteGroup', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayNotifications', 'editNotifications', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );
$_PLUGINS->registerFunction( 'gj_onAfterCreateUser', 'storeNotifications', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );
$_PLUGINS->registerFunction( 'gj_onBeforeDisplayGroup', 'showPhotos', '\CB\Plugin\GroupJivePhoto\Trigger\PhotoTrigger' );

$_PLUGINS->registerFunction( 'activity_onLoadActivityStream', 'activityPrefetch', '\CB\Plugin\GroupJivePhoto\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onLoadNotificationsStream', 'activityPrefetch', '\CB\Plugin\GroupJivePhoto\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamActivity', 'activityDisplay', '\CB\Plugin\GroupJivePhoto\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamNotification', 'activityDisplay', '\CB\Plugin\GroupJivePhoto\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onAssetSource', 'assetSource', '\CB\Plugin\GroupJivePhoto\Trigger\ActivityTrigger' );