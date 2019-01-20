<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Trigger;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Privacy\CBPrivacy;
use CB\Plugin\Privacy\Privacy;
use CB\Database\Table\FieldTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class FieldTrigger extends \cbFieldHandler
{

	/**
	 * @param null|FieldTable $field
	 * @param UserTable       $user
	 * @param string          $reason
	 * @return string
	 */
	private function fieldOverride( $field, $user, $reason )
	{
		if ( ! $field->params instanceof ParamsInterface ) {
			$field->params	=	new Registry( $field->params );
		}

		switch( $reason ) {
			case 'register':
			case 'edit':
				$override	=	CBTxt::T( $field->params->get( 'cbprivacy_edit_override', null, GetterInterface::RAW ) );
				break;
			case 'list':
			case 'profile':
			default:
				$override	=	CBTxt::T( $field->params->get( 'cbprivacy_display_override', null, GetterInterface::RAW ) );
				break;
		}

		$override			=	trim( preg_replace( '/\[cb:(userdata +field|userfield +field)="' . preg_quote( $field->get( 'name', null, GetterInterface::STRING ) ) . '"[^]]+\]/i', '', $override ) );

		if ( $override ) {
			$override		=	\CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ) )->replaceUserVars( $override, false, false, array(), false );
		}

		return $override;
	}

	/**
	 * @param FieldTable[] $fields
	 * @param UserTable    $user
	 * @param string       $reason
	 * @param int          $tabid
	 * @param int|string   $fieldIdOrName
	 * @param bool         $fullAccess
	 */
	public function fieldsFetch( &$fields, &$user, $reason, $tabid, $fieldIdOrName, $fullAccess )
	{
		if ( $fieldIdOrName || ( ! $fields ) || ( ! $user instanceof UserTable ) || $user->getError() || Application::Cms()->getClientId() || Application::MyUser()->isGlobalModerator() || $fullAccess ) {
			return;
		}

		if ( $reason == 'profile' ) {
			foreach ( $fields as $fieldId => $field ) {
				if ( isset( $fields[$fieldId] ) && ( ! CBPrivacy::checkFieldDisplayAccess( $field, $user ) ) && ( ! $this->fieldOverride( $field, $user, $reason ) ) ) {
					unset( $fields[$fieldId] );
				}
			}
		} elseif ( ( $reason == 'edit' ) && $user->get( 'id', 0, GetterInterface::INT ) ) {
			foreach ( $fields as $fieldId => $field ) {
				if ( isset( $fields[$fieldId] ) && ( ! CBPrivacy::checkFieldEditAccess( $field ) ) && ( ! $this->fieldOverride( $field, $user, $reason ) ) ) {
					unset( $fields[$fieldId] );
				}
			}
		}
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $formatting
	 * @param string     $reason
	 * @param int        $list_compare_types
	 * @return mixed|null|string
	 */
	public function fieldDisplay( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		if ( $field->get( '_noPrivacy', false, GetterInterface::BOOLEAN ) || Application::Cms()->getClientId() || Application::MyUser()->isGlobalModerator() ) {
			return null;
		}

		$return					=	null;

		$field->set( '_noPrivacy', true );

		if ( ( $output == 'html' ) && ( $reason != 'search' ) ) {
			if ( ! CBPrivacy::checkFieldDisplayAccess( $field, $user ) ) {
				$return			=	$this->fieldOverride( $field, $user, $reason );

				if ( $return ) {
					$return		=	$this->renderFieldHtml( $field, $user, $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $return, $output, true ), $output, $formatting, $reason, array() );
				}

				if ( ! $return ) {
					$return		=	' ';
				}
			}
		} elseif ( ( $output == 'htmledit' ) && ( $reason != 'search' ) && $user->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ! CBPrivacy::checkFieldEditAccess( $field ) ) {
				$return			=	$this->fieldOverride( $field, $user, $reason );

				if ( $return ) {
					$return		=	$this->renderFieldHtml( $field, $user, $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $return, $output, true ), $output, $formatting, $reason, array() );
				}

				if ( ! $return ) {
					$return		=	' ';
				}
			}
		}

		$field->set( '_noPrivacy', false );

		return $return;
	}

	/**
	 * @param \cbFieldHandler $fieldHandler
	 * @param FieldTable      $field
	 * @param UserTable       $user
	 * @param string          $output
	 * @param string          $reason
	 * @param string          $tag
	 * @param string          $type
	 * @param string          $value
	 * @param string          $additional
	 * @param string          $allValues
	 * @param bool            $displayFieldIcons
	 * @param bool            $required
	 * @return null|string
	 */
	public function fieldIcons( &$fieldHandler, &$field, &$user, $output, $reason, $tag, $type, $value, $additional, $allValues, $displayFieldIcons, $required )
	{
		global $_CB_fieldIconDisplayed, $_CB_fieldPrivacyDisplayed;

		if ( ( ! in_array( $reason, array( 'edit', 'register' ) ) ) || ( ! $field instanceof FieldTable ) || ( ! $field->get( 'profile', 1, GetterInterface::INT ) ) ) {
			return null;
		}

		$fieldId									=	$field->get( 'fieldid', 0, GetterInterface::INT );

		if ( isset( $_CB_fieldPrivacyDisplayed[$fieldId] ) ) {
			return null;
		}

		$return										=	null;

		if ( ! $field->params instanceof ParamsInterface ) {
			$field->params							=	new Registry( $field->params );
		}

		$display									=	$field->params->get( 'cbprivacy_display', 0, GetterInterface::INT );

		if ( ( $reason == 'register' ) && ( ! $field->params->get( 'cbprivacy_display_reg', false, GetterInterface::BOOLEAN ) ) ) {
			$display								=	0;
		}

		if ( ( $display == 1 ) || ( ( $display == 2 ) && Application::MyUser()->isGlobalModerator() ) ) {
			$_CB_fieldPrivacyDisplayed[$fieldId]	=	true;

			$privacy								=	new Privacy( 'profile.field.' . $fieldId, $user );

			if ( $display == 2 ) {
				$privacy->set( 'options_moderator', true );
			}

			$privacy->parse( $field->params, 'privacy_' );

			$return									=	$privacy->privacy( 'edit' );

			if ( ! isset( $_CB_fieldIconDisplayed[$fieldId] ) ) {
				$_CB_fieldIconDisplayed[$fieldId]	=	true;

				if ( $displayFieldIcons ) {
					$return							.=	' ' . getFieldIcons( null, $required, null, $fieldHandler->getFieldDescription( $field, $user, $output, $reason ), $fieldHandler->getFieldTitle( $field, $user, $output, $reason ), false, $field->params->get( 'fieldLayoutIcons', null ) );
				}
			}
		}

		return $return;
	}

	/**
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param array      $postdata
	 * @param string     $reason
	 * @return null|string
	 */
	public function fieldPrepareSave( &$field, &$user, &$postdata, $reason )
	{
		$return			=	null;

		if ( ( $reason != 'search' ) && $user->get( 'id', 0, GetterInterface::INT ) && ( ! CBPrivacy::checkFieldEditAccess( $field ) ) ) {
			$return		=	' ';
		}

		return $return;
	}
}