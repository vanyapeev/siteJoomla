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

$_PLUGINS->registerFunction( 'onAfterLoginForm', 'getLoginButtons', '\CB\Plugin\Connect\Trigger\ButtonTrigger' );
$_PLUGINS->registerFunction( 'onAfterLogoutForm', 'getLoginButtons', '\CB\Plugin\Connect\Trigger\ButtonTrigger' );
$_PLUGINS->registerFunction( 'onBeforeRegisterFormDisplay', 'getRegistrationButtons', '\CB\Plugin\Connect\Trigger\ButtonTrigger' );


$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'socialid' => '\CB\Plugin\Connect\Field\SocialField' ) );