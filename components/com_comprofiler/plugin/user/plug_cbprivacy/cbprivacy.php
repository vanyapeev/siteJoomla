<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\Privacy\Trigger\AdminTrigger' );

$_PLUGINS->registerFunction( 'onBeforeUserProfileAccess', 'getProfile', '\CB\Plugin\Privacy\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deletePrivacy', '\CB\Plugin\Privacy\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onDuringLogin', 'checkDisabled', '\CB\Plugin\Privacy\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterNewUser', 'saveRegistrationPrivacy', '\CB\Plugin\Privacy\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterUserRegistration', 'saveRegistrationPrivacy', '\CB\Plugin\Privacy\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterUpdateUser', 'saveEditPrivacy', '\CB\Plugin\Privacy\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterUserUpdate', 'saveEditPrivacy', '\CB\Plugin\Privacy\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'onAfterEditATab', 'tabEdit', '\CB\Plugin\Privacy\Trigger\TabTrigger' );
$_PLUGINS->registerFunction( 'onAfterTabsFetch', 'tabsFetch', '\CB\Plugin\Privacy\Trigger\TabTrigger' );

$_PLUGINS->registerFunction( 'onAfterFieldsFetch', 'fieldsFetch', '\CB\Plugin\Privacy\Trigger\FieldTrigger' );
$_PLUGINS->registerFunction( 'onBeforeprepareFieldDataSave', 'fieldPrepareSave', '\CB\Plugin\Privacy\Trigger\FieldTrigger' );
$_PLUGINS->registerFunction( 'onFieldIcons', 'fieldIcons', '\CB\Plugin\Privacy\Trigger\FieldTrigger' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'fieldDisplay', '\CB\Plugin\Privacy\Trigger\FieldTrigger' );

$_PLUGINS->registerFunction( 'onBeforeDisplayUsersList', 'getList', '\CB\Plugin\Privacy\Trigger\UserlistTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array(	'privacy_profile'		=>	'\CB\Plugin\Privacy\Field\PrivacyField',
											'privacy_disable_me'	=>	'\CB\Plugin\Privacy\Field\DisableField',
											'privacy_delete_me'		=>	'\CB\Plugin\Privacy\Field\DeleteField'
										));