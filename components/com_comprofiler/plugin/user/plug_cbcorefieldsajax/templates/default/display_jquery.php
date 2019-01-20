<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework;

cbValidator::loadValidation();
initToolTip();

$_CB_framework->addJQueryPlugin( 'cbajaxfield', '/components/com_comprofiler/plugin/user/plug_cbcorefieldsajax/js/jquery.cbcorefieldsajax.js' );

$js		=	"$( '.cb_tab_overlib_container,.cb_tab_overlib_fix_container,.cb_tab_overlib_sticky_container' ).on( 'cbtooltip.render', function( e, cbtooltip, event, api ) {"
		.		"$( api.elements.content ).find( '.cbAjaxContainerDisplay' ).cbajaxfield();"
		.	"});"
		.	"$( '.cbAjaxContainerDisplay' ).cbajaxfield();";

$_CB_framework->outputCbJQuery( $js, array( 'cbajaxfield', 'form' ) );