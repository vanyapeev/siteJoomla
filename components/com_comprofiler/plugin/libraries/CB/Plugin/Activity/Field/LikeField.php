<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Field;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Likes;
use CB\Plugin\Activity\CBActivity;

defined('CBLIB') or die();

class LikeField extends \cbFieldHandler
{

	/**
	 * Accessor:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getField( &$field, &$user, $output, $reason, $list_compare_types )
	{
		if ( ! ( ( $output == 'html' ) && ( $reason == 'profile' ) ) ) {
			return null;
		}

		$asset			=	str_replace( '[field_id]', $field->get( 'fieldid', 0, GetterInterface::INT ), $field->params->get( 'likes_asset', null, GetterInterface::STRING ) );

		if ( ! $asset ) {
			$asset		=	'profile.' . $user->get( 'id', 0, GetterInterface::INT );
		}

		$likes			=	new Likes( $asset, $user );

		$likes->parse( $field->params, 'likes_' );

		$likes->set( 'field', $field->get( 'fieldid', 0, GetterInterface::INT ) );

		if ( ( ! Application::Config()->get( 'showEmptyFields', 1, GetterInterface::INT ) ) && ( ! $likes->rows( 'count' ) ) && ( ! CBActivity::canCreate( 'like', $likes ) ) ) {
			return cbReplaceVars( Application::Config()->get( 'emptyFieldsText', null, GetterInterface::STRING ), $user );
		}

		if ( $field->params->get( 'likes_layout', 'button', GetterInterface::STRING ) == 'stream' ) {
			$return		=	$likes->likes();
		} else {
			$return		=	$likes->likes( 'button' );
		}

		if ( ! $return ) {
			return null;
		}

		return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $return, $output, false ), $reason, $field, $user );
	}
}