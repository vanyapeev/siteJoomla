<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_CB_framework;

initToolTip();

$js		=	"$( 'select.cbPrivacySelect' ).cbprivacy({"
		.		"custom: '" . addslashes( CBTxt::T( 'PRIVACY_CUSTOM', 'Custom' ) ) . "'"
		.	"});";

$_CB_framework->outputCbJQuery( $js, 'cbprivacy' );