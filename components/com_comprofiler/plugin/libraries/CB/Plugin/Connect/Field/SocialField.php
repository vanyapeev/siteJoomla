<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Connect\Field;

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CB\Plugin\Connect\CBConnect;
use CB\Plugin\Connect\Connect;
use CBLib\Registry\GetterInterface;

defined('CBLIB') or die();

class SocialField extends \cbFieldHandler
{

	/**
	 * Translates a fields name to its provider id
	 *
	 * @param FieldTable $field
	 * @return null|string
	 */
	private function fieldToProviderId( $field )
	{
		$return				=	null;

		foreach ( CBConnect::getProviders() as $id => $provider ) {
			if ( $provider['field'] == $field->get( 'name', null, GetterInterface::STRING ) ) {
				$return		=	$id;
				break;
			}
		}

		return $return;
	}

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
		$providerId					=	$this->fieldToProviderId( $field );

		if ( ! $providerId ) {
			return null;
		}

		$connect					=	new Connect( $providerId );
		$value						=	$user->get( $field->get( 'name', null, GetterInterface::STRING ), null, GetterInterface::STRING );
		$return						=	null;

		switch( $output ) {
			case 'htmledit':
				if ( $reason == 'search' ) {
					$return			=	$this->_fieldSearchModeHtml( $field, $user, $this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, null ), 'text', $list_compare_types );
				} else {
					if ( Application::Cms()->getClientId() ) {
						$return		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'text', $value, null );
					} elseif ( $value && ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) ) {
						$values		=	array();
						// CBTxt::T( 'UNLINK_PROVIDER_ACCOUNT', 'Unlink your [provider] account', array( '[provider]' => $connect->name() ) )
						$values[]	=	\moscomprofilerHTML::makeOption( '1', CBTxt::T( 'UNLINK_' . strtoupper( $connect->id ) . '_ACCOUNT UNLINK_PROVIDER_ACCOUNT', 'Unlink your [provider] account', array( '[provider]' => $connect->name() ) ) );

						$return		=	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'multicheckbox', null, null, $values );
					} elseif ( $value && ( ! Application::MyUser()->getUserId() ) ) {
						$url		=	$connect->profileUrl( $value );

						if ( $url ) {
							$url	=	'<a href="' . $url . '" target="_blank" rel="nofollow">'
											// CBTxt::T( 'PROVIDER_PROFILE', '[provider] profile', array( '[provider]' => $connect->name() ) )
									.		CBTxt::T( strtoupper( $connect->id ) . '_PROFILE PROVIDER_PROFILE', '[provider] profile', array( '[provider]' => $connect->name() ) )
									.	'</a>';
						}

						if ( ! $url ) {
							// CBTxt::T( 'PROVIDER_PROFILE_ID', '[provider] profile id [provider_id]', array( '[provider]' => $connect->name(), '[provider_id]' => $value ) )
							$url	=	CBTxt::T( strtoupper( $connect->id ) . '_PROFILE_ID PROVIDER_PROFILE_ID', '[provider] profile id [provider_id]', array( '[provider]' => $connect->name(), '[provider_id]' => $value ) );
						}

						// CBTxt::T( 'PROVIDER_PROFILE_LINKED_TO_ACCOUNT', 'Your [provider_profile] will be linked to this account.', array( '[provider]' => $connect->name(), '[provider_profile]' => $url, '[provider_id]' => $value ) )
						$return		=	CBTxt::T( strtoupper( $connect->id ) . '_PROFILE_LINKED_TO_ACCOUNT PROVIDER_PROFILE_LINKED_TO_ACCOUNT', 'Your [provider_profile] will be linked to this account.', array( '[provider]' => $connect->name(), '[provider_profile]' => $url, '[provider_id]' => $value ) )
									.	$this->_fieldEditToHtml( $field, $user, $reason, 'input', 'hidden', $value, null );
					} elseif ( ( ! $value ) && ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) && $connect->params()->get( 'link', true, GetterInterface::BOOLEAN ) && $field->params->get( 'connect_link', false, GetterInterface::BOOLEAN ) ) {
						$return		=	'<div class="cbConnectButtons">'
									.		$connect->button()
									.	'</div>';
					}
				}
				break;
			case 'html':
			case 'rss':
				if ( $value ) {
					$url			=	$connect->profileUrl( $value );

					if ( $url ) {
						$value		=	'<a href="' . $url . '" target="_blank" rel="nofollow">'
											// CBTxt::T( 'VIEW_PROVIDER_PROFILE', 'View [provider] Profile', array( '[provider]' => $connect->name() ) )
									.		CBTxt::T( 'VIEW_' . strtoupper( $connect->id ) . '_PROFILE VIEW_PROVIDER_PROFILE', 'View [provider] Profile', array( '[provider]' => $connect->name() ) )
									.	'</a>';
					}
				}

				$return				=	$this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $value, $output, false ), $reason, $field, $user, false );
				break;
			default:
				$return				=	$this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $value, $output );
				break;
		}

		return $return;
	}

	/**
	 * Mutator:
	 * Prepares field data for saving to database (safe transfer from $postdata to $user)
	 * Override
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user      RETURNED populated: touch only variables related to saving this field (also when not validating for showing re-edit)
	 * @param  array       $postdata  Typically $_POST (but not necessarily), filtering required.
	 * @param  string      $reason    'edit' for save user edit, 'register' for save registration
	 */
	public function prepareFieldDataSave( &$field, &$user, &$postdata, $reason )
	{
		if ( ! $this->fieldToProviderId( $field ) ) {
			return;
		}

		$fieldName							=	$field->get( 'name', null, GetterInterface::STRING );
		$currentValue						=	$user->get( $fieldName, null, GetterInterface::STRING );
		$value								=	cbGetParam( $postdata, $fieldName );

		if ( $currentValue && ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) ) {
			if ( is_array( $value ) ) {
				if ( isset( $value[0] ) && ( $value[0] == 1 ) ) {
					$postdata[$fieldName]	=	'';
				}
			}

			$value							=	cbGetParam( $postdata, $fieldName );
		}

		if ( ( ! Application::Cms()->getClientId() ) && $user->get( 'id', 0, GetterInterface::INT ) && $currentValue && ( $value !== '' ) ) {
			$postdata[$fieldName]			=	$currentValue;
		}

		parent::prepareFieldDataSave( $field, $user, $postdata, $reason );
	}
}