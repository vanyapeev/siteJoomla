<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileBook\Field;

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

class ProfileRatingField extends \cbFieldHandler
{

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null
	 */
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		$field->set( 'registration', 0 );
		$field->set( 'required', 0 );
		$field->set( 'readonly', 0 );

		return parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		global $_CB_framework, $_CB_database;

		if ( ! $user->get( 'id', 0, GetterInterface::INT ) ) {
			return null;
		}

		static $ratings			=	array();

		$userId					=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $ratings[$userId] ) ) {
			$query				=	'SELECT ROUND( AVG( ' . $_CB_database->NameQuote( 'postervote' ) . ' ), 1 )'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " IN " . $_CB_database->safeArrayOfStrings( array( 'g', 'w' ) )
								.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $userId
								.	"\n AND " . $_CB_database->NameQuote( 'postervote' ) . " > 0";
			$_CB_database->setQuery( $query );
			$ratings[$userId]	=	$_CB_database->loadResult();
		}

		$rating					=	$ratings[$userId];

		switch ( $output ) {
			case 'html':
			case 'htmledit':
				if ( $reason == 'search' ) {
					return null;
				}

				$_CB_framework->outputCbJQuery( "$( '.pbProfileRating .rateit' ).rateit();", 'rateit' );

				$return			=	'<div class="pbProfileRating">'
								.		'<div class="rateit" data-rateit-step="1" data-rateit-value="' . (float) $rating . '" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-min="0" data-rateit-max="5"></div>'
								.	'</div>';

				return $this->formatFieldValueLayout( $return, $reason, $field, $user );
				break;
			default:
				return $this->_formatFieldOutput( $field->get( 'name'. null, GetterInterface::STRING ), (float) $rating, $output, false );
				break;
		}
	}
}