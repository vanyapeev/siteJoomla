<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteItems', '\CB\Plugin\Gallery\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\Gallery\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onAfterModeratorModule', 'approvalLink', '\CB\Plugin\Gallery\Trigger\WorkflowTrigger' );

$_PLUGINS->registerFunction( 'gallery_onAssetSource', 'assetSource', '\CB\Plugin\Gallery\Trigger\GalleryTrigger' );

// CB Activity:
$_PLUGINS->registerFunction( 'activity_onActivity', 'extendParameters', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onComments', 'extendParameters', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onQueryActivityStream', 'activityQuery', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onQueryNotificationsStream', 'activityQuery', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onLoadActivityStream', 'activityLoad', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onLoadNotificationsStream', 'activityLoad', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamActivity', 'activityDisplay', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamNotification', 'activityDisplay', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamComment', 'commentDisplay', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onAssetSource', 'assetSource', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamActivityNew', 'activityUploadNew', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamCommentNew', 'activityUploadNew', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamActivityEdit', 'activityUploadEdit', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onDisplayStreamCommentEdit', 'activityUploadEdit', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onBeforeUpdateStreamActivity', 'activityUploadSave', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onBeforeCreateStreamActivity', 'activityUploadSave', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onBeforeUpdateStreamComment', 'activityUploadSave', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );
$_PLUGINS->registerFunction( 'activity_onBeforeCreateStreamComment', 'activityUploadSave', '\CB\Plugin\Gallery\Trigger\ActivityTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'gallery' => '\CB\Plugin\Gallery\Field\GalleryField' ) );

class cbgalleryTab extends cbTabHandler
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

		$gallery			=	new Gallery( $tab->params->get( 'gallery_asset', null, GetterInterface::STRING ), $user );

		$gallery->set( 'tab', $tab->get( 'tabid', 0, GetterInterface::INT ) );

		$gallery->parse( $tab->params, 'gallery_' );

		if ( ( ! Application::Config()->get( 'showEmptyTabs', 1, GetterInterface::INT ) ) && ( ! $gallery->folders( 'count' ) ) && ( ! $gallery->items( 'count' ) ) && ( ! CBGallery::canCreateFolders( $gallery ) ) && ( ! CBGallery::canCreateItems( 'all', 'both', $gallery ) ) ) {
			return null;
		}

		return $gallery->gallery();
	}
}