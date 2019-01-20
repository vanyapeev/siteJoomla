<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Plugin\Privacy\CBPrivacy;
use CB\Plugin\Privacy\Privacy;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Privacy\Table\PrivacyTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var CBplug_cbprivacy $this
 * @var PrivacyTable[]   $rows
 * @var array            $selected
 * @var string           $ajaxUrl
 * @var UserTable        $viewer
 * @var Privacy          $privacy
 */

global $_CB_framework;

include_once CBPrivacy::getTemplate( $privacy->get( 'template', null, GetterInterface::STRING ), 'edit_jquery', false );

$name					=	md5( 'privacy_' . $privacy->asset() );

if ( ! $selected ) {
	$selected			=	explode( '|*|', $privacy->get( 'options_default', '0', GetterInterface::STRING ) );
}

$selected				=	$this->input( $name, $selected, GetterInterface::RAW );
$layout					=	$privacy->get( 'layout', 'button', GetterInterface::STRING );

switch ( $layout ) {
	case 'tags':
		$layoutClass	=	'cbPrivacySelectTags form-control';
		break;
	case 'icon':
		$layoutClass	=	'cbPrivacySelectIcon';
		break;
	case 'button':
	default:
		$layoutClass	=	'cbPrivacySelectButton btn btn-sm btn-default';
		break;
}

$ajax					=	null;

if ( $privacy->get( 'ajax', false, GetterInterface::BOOLEAN ) ) {
	$ajax				=	' data-cbprivacy-ajax="' . $ajaxUrl . '"';
}

echo str_replace( $name . '__', md5( $privacy->id() . '_privacy_' . rand() ), moscomprofilerHTML::selectList( $privacy->rules(), $name . '[]', 'class="cbPrivacySelect ' . htmlspecialchars( $layoutClass ) . '" multiple="multiple" data-cbprivacy-layout="' . htmlspecialchars( $layout ) . '" data-cbselect-dropdown-css-class="cbPrivacySelectOptions"' . $ajax, 'value', 'text', $selected, 0, false, false, false ) );
