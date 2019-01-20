<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Conditional\Trigger;

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Conditional\CBConditional;

defined('CBLIB') or die();

class FieldTrigger extends \cbPluginHandler
{

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
		if ( $fieldIdOrName ) {
			// getFields usage provides this and in this case $user is the viewing user and not the profile owner so skip this check:
			return;
		}

		$post									=	$this->getInput()->getNamespaceRegistry( 'post' );
		$view									=	$this->input( 'view', null, GetterInterface::STRING );

		if ( ( ! Application::Cms()->getClientId() ) && ( ! $fullAccess ) ) {
			$isSave								=	( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'saveregisters', 'saveuseredit' ) ) );
			$isProfile							=	( $reason == 'profile' );
		} elseif ( Application::Cms()->getClientId() && CBConditional::getGlobalParams()->get( 'conditions_backend', false, GetterInterface::BOOLEAN ) && ( ! $fullAccess ) ) {
			$isSave								=	( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() && in_array( $view, array( 'apply', 'save' ) ) );
			$isProfile							=	( $reason == 'profile' );
		} else {
			$isSave								=	false;
			$isProfile							=	false;
		}

		if ( ( $isSave || $isProfile ) && $fields && ( $user && ( $user instanceof UserTable ) && ( ! $user->getError() ) ) ) {
			$reset								=	CBConditional::getGlobalParams()->get( 'conditions_reset', true, GetterInterface::BOOLEAN );

			foreach ( $fields as $k => $field ) {
				$conditioned					=	CBConditional::getTabConditional( $field->get( 'tabid', 0, GetterInterface::INT ), $reason, $user->get( 'id', 0, GetterInterface::INT ) );

				if ( ! $conditioned ) {
					$conditioned				=	CBConditional::getFieldConditional( $field, $reason, $user->get( 'id', 0, GetterInterface::INT ) );
				}

				if ( $conditioned ) {
					if ( ! $field->params instanceof ParamsInterface ) {
						$field->params			=	new Registry( $field->params );
					}

					if ( $isSave && $reset ) {
						$fieldName				=	$field->get( 'name', null, GetterInterface::STRING );

						switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
							case 'date':
								if ( isset( $user->$fieldName ) ) {
									$user->set( $fieldName, '0000-00-00' );
								}
								break;
							case 'datetime':
								if ( isset( $user->$fieldName ) ) {
									$user->set( $fieldName, '0000-00-00 00:00:00' );
								}
								break;
							case 'integer':
							case 'points':
							case 'rating':
							case 'checkbox':
							case 'terms':
							case 'counter':
								if ( isset( $user->$fieldName ) ) {
									$user->set( $fieldName, 0 );
								}
								break;
							case 'image':
								if ( isset( $user->$fieldName ) ) {
									$user->set( $fieldName, '' );
								}

								$approvedName	=	$fieldName . 'approved';

								if ( isset( $user->$approvedName ) ) {
									$user->set( $approvedName, 0 );
								}
								break;
							default:
								foreach ( $field->getTableColumns() as $column ) {
									if ( isset( $user->$column ) ) {
										$user->set( $column, '' );
									}
								}
								break;
						}
					}

					unset( $fields[$k] );
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
		$return							=	null;

		if ( ( ! $field->get( '_noCondition', false, GetterInterface::BOOLEAN  ) ) && ( ( ! Application::Cms()->getClientId() ) || CBConditional::getGlobalParams()->get( 'conditions_backend', false, GetterInterface::BOOLEAN ) ) ) {
			$field->set( '_noCondition', true );

			if ( $output == 'html' ) {
				$conditioned			=	CBConditional::getTabConditional( $field->get( 'tabid', 0, GetterInterface::INT ), $reason, $user->get( 'id', 0, GetterInterface::INT ) );

				if ( ! $conditioned ) {
					$conditioned		=	CBConditional::getFieldConditional( $field, $reason, $user->get( 'id', 0, GetterInterface::INT ) );
				}

				$display				=	true;

				if ( $conditioned ) {
					$display			=	false;
				}

				if ( ! $display ) {
					$return				=	' ';
				}
			} elseif ( $output == 'htmledit' ) {
				$conditioned			=	CBConditional::getTabConditional( $field->get( 'tabid', 0, GetterInterface::INT ), $reason, $user->get( 'id', 0, GetterInterface::INT ), ( $formatting != 'none' ) );
				$display				=	true;

				if ( $conditioned === 2 ) {
					$display			=	false;
				} elseif ( ( $formatting == 'none' ) && $conditioned ) {
					$display			=	false;
				}

				if ( $display ) {
					$conditioned		=	CBConditional::getFieldConditional( $field, $reason, $user->get( 'id', 0, GetterInterface::INT ), ( $formatting != 'none' ) );

					if ( $conditioned === 2 ) {
						$display		=	false;
					} elseif ( ( $formatting == 'none' ) && $conditioned ) {
						$display		=	false;
					}
				}

				if ( ! $display ) {
					$return				=	' ';
				}
			}

			$field->set( '_noCondition', false );
		}

		return $return;
	}
}