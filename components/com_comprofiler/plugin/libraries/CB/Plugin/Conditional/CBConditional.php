<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Conditional;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\ParamsInterface;
use CB\Database\Table\FieldTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class CBConditional
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbconditional' );
			$params		=	new Registry();

			if ( $plugin ) {
				$params->load( $plugin->params );
			}
		}

		return $params;
	}

	/**
	 * Utility function for grabbing a cached field object
	 *
	 * @param int $fieldId
	 * @param int $profileId
	 * @return FieldTable|null
	 */
	static public function getField( $fieldId, $profileId = null )
	{
		if ( ! $fieldId ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		if ( $profileId === null ) {
			$profileId						=	$userId;
		}

		static $fields						=	array();

		if ( ! isset( $fields[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$fields[$profileId][$userId]	=	$profileUser->_getCbTabs( false )->_getTabFieldsDb( null, $profileUser->getUserData(), 'adminfulllist', null, true, true );
		}

		if ( is_string( $fieldId ) ) {
			$field							=	null;

			foreach ( $fields[$profileId][$userId] as $fld ) {
				/** @var FieldTable $fld */
				if ( $fld->get( 'name', null, GetterInterface::STRING ) != $fieldId ) {
					continue;
				}

				$field						=	$fld;
				break;
			}

			if ( ! $field ) {
				return null;
			}
		} else {
			if ( ! isset( $fields[$profileId][$userId][$fieldId] ) ) {
				return null;
			}

			$field							=	$fields[$profileId][$userId][$fieldId];
		}

		if ( ! ( $field->params instanceof ParamsInterface ) ) {
			$field->params					=	new Registry( $field->params );
		}

		return $field;
	}

	/**
	 * Utility function for grabbing a cache tab object
	 *
	 * @param int $tabId
	 * @param int $profileId
	 * @return TabTable|null
	 */
	static public function getTab( $tabId, $profileId = null )
	{
		static $profileTab					=	null;

		if ( ! $tabId ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		if ( $profileId === null ) {
			$profileId						=	$userId;
		}

		static $tabs						=	array();

		if ( ! isset( $tabs[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$tabs[$profileId][$userId]		=	$profileUser->_getCbTabs( false )->_getTabsDb( $profileUser->getUserData(), 'adminfulllist' );
		}

		if ( ! isset( $tabs[$profileId][$userId][$tabId] ) ) {
			return null;
		}

		$tab								=	$tabs[$profileId][$userId][$tabId];

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params					=	new Registry( $tab->params );
		}

		return $tab;
	}

	/**
	 * Utility function for grabbing a tabs cached field objects
	 *
	 * @param int $tabId
	 * @param int $profileId
	 * @return FieldTable[]
	 */
	static public function getTabFields( $tabId, $profileId = null )
	{
		if ( ! $tabId ) {
			return null;
		}

		$userId										=	Application::MyUser()->getUserId();

		if ( $profileId === null ) {
			$profileId								=	$userId;
		}

		static $fields								=	array();

		if ( ! isset( $fields[$tabId][$profileId][$userId] ) ) {
			$profileUser							=	\CBuser::getInstance( $profileId, false );

			$fields[$tabId][$profileId][$userId]	=	$profileUser->_getCbTabs( false )->_getTabFieldsDb( $tabId, $profileUser->getUserData(), 'adminfulllist', null, true, true );
		}

		return $fields[$tabId][$profileId][$userId];
	}

	/**
	 * Check is a tab has been conditioned
	 *
	 * @param int|TabTable $tab
	 * @param string       $reason
	 * @param int          $userId
	 * @param bool         $outputJs
	 * @return bool|int                 0|false = not conditioned, 1|true = conditioned, 2 = static conditioned
	 */
	static public function getTabConditional( $tab, $reason, $userId, $outputJs = false )
	{
		global $_CB_framework;

		static $cache												=	array();
		static $jsCache												=	array();
		static $jsOutput											=	array();

		if ( ! $tab instanceof TabTable ) {
			$tab													=	self::getTab( $tab, $userId );

			if ( ! $tab ) {
				return false;
			}
		}

		$tabId														=	$tab->get( 'tabid', 0, GetterInterface::INT );

		if ( ! isset( $cache[$tabId][$userId][$reason] ) ) {
			if ( ! $tab->params instanceof ParamsInterface ) {
				$tab->params										=	new Registry( $tab->params );
			}

			$display												=	$tab->params->get( 'cbconditional_conditioned', 0, GetterInterface::INT );
			$debug													=	$tab->params->get( 'cbconditional_debug', false, GetterInterface::BOOLEAN );

			$conditioned											=	false;
			$static													=	true;
			$js														=	null;

			if ( $display ) {
				$cbUser												=	\CBuser::getInstance( (int) $userId, false );

				$orMatched											=	false;

				$jsTargets											=	array();
				$jsConditions										=	array();

				$conditionsUsed										=	0;

				foreach ( $tab->params->subTree( 'cbconditional_conditions' ) as $orIndex => $orCondition ) {
					/** @var ParamsInterface $orCondition */
					$andMatched										=	true;
					$andConditions									=	array();

					foreach ( $orCondition->subTree( 'condition' ) as $andIndex => $andCondition ) {
						/** @var ParamsInterface $andCondition */
						if ( $reason == 'profile' ) {
							if ( ! $andCondition->get( 'location_profile_view', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'edit' ) {
							if ( ! $andCondition->get( 'location_profile_edit', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'register' ) {
							if ( ! $andCondition->get( 'location_registration', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'search' ) {
							if ( ! $andCondition->get( 'location_userlist_search', false, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'list' ) {
							if ( ! $andCondition->get( 'location_userlist_view', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						}

						$fieldName									=	$andCondition->get( 'field', null, GetterInterface::STRING );

						if ( $fieldName ) {
							$operator								=	$andCondition->get( 'operator', 0, GetterInterface::INT );

							if ( in_array( $operator, array( 8, 9, 12, 13 ) ) ) {
								$delimiter							=	$andCondition->get( 'delimiter', null, GetterInterface::STRING );
							} else {
								$delimiter							=	null;
							}

							$value									=	null;

							if ( ! in_array( $fieldName, array( 'customviewaccesslevels', 'customusergroups', 'customlanguages', 'custommoderators', 'customusers' ) ) ) {
								$value								=	$cbUser->replaceUserVars( $andCondition->get( 'value', null, GetterInterface::RAW ), false, true, self::getSubstitutions(), $andCondition->get( 'value_translate', false, GetterInterface::BOOLEAN ) );

								if ( in_array( $operator, array( '6', '7' ) ) ) {
									$value							=	null;
								}
							}

							$field									=	null;

							switch ( $fieldName ) {
								case 'customvalue':
									$fieldValue						=	$cbUser->replaceUserVars( $andCondition->get( 'field_custom', null, GetterInterface::RAW ), false, true, self::getSubstitutions(), $andCondition->get( 'field_custom_translate', false, GetterInterface::BOOLEAN ) );
									break;
								case 'customviewaccesslevels':
									$accessLevels					=	cbToArrayOfInt( explode( '|*|', $andCondition->get( 'field_viewaccesslevels', null, GetterInterface::STRING ) ) );
									$userAccessLevels				=	Application::User( (int) $userId )->getAuthorisedViewLevels();
									$fieldValue						=	0;

									foreach ( $accessLevels as $accessLevel ) {
										if ( in_array( $accessLevel, $userAccessLevels ) ) {
											$fieldValue				=	1;
											break;
										}
									}

									$operator						=	$andCondition->get( 'operator_viewaccesslevels', 0, GetterInterface::INT );
									$value							=	1;
									break;
								case 'customusergroups':
									$userGroups						=	cbToArrayOfInt( explode( '|*|', $andCondition->get( 'field_usergroups', null, GetterInterface::STRING ) ) );
									$userUsergroups					=	Application::User( (int) $userId )->getAuthorisedGroups();
									$fieldValue						=	0;

									foreach ( $userGroups as $userGroup ) {
										if ( in_array( $userGroup, $userUsergroups ) ) {
											$fieldValue				=	1;
											break;
										}
									}

									$operator						=	$andCondition->get( 'operator_usergroups', 0, GetterInterface::INT );
									$value							=	1;
									break;
								case 'customlanguages':
									$fieldValue						=	$cbUser->getUserData()->getUserLanguage();
									$operator						=	$andCondition->get( 'operator_languages', 12, GetterInterface::INT );
									$delimiter						=	'|*|';
									$value							=	$andCondition->get( 'field_languages', null, GetterInterface::STRING );

									$field							=	self::getField( 'params', $userId );
									break;
								case 'custommoderators':
									$fieldValue						=	(int) Application::User( (int) $userId )->isGlobalModerator();
									$operator						=	$andCondition->get( 'operator_moderators', 0, GetterInterface::INT );
									$value							=	1;
									break;
								case 'customusers':
									$fieldValue						=	(int) $userId;
									$operator						=	$andCondition->get( 'operator_users', 12, GetterInterface::INT );
									$delimiter						=	',';
									$value							=	$cbUser->replaceUserVars( $andCondition->get( 'field_users', null, GetterInterface::STRING ), true, false, array(), false );
									break;
								default:
									$field							=	self::getField( $fieldName, $userId );

									if ( ! $field ) {
										continue 2;
									}

									$fieldValue						=	self::getFieldValue( $userId, $field, $reason );
									break;
							}

							if ( $field ) {
								// If there's a field available lets check if it's being output to determine if it's a non-static condition with JS:
								if ( $reason == 'register' ) {
									if ( $field->get( 'registration', 1, GetterInterface::INT ) > 0 ) {
										$static						=	false;
									}
								} elseif ( $reason == 'edit' ) {
									if ( $field->get( 'edit', 1, GetterInterface::INT ) > 0 ) {
										$static						=	false;
									}
								} elseif ( $reason == 'search' ) {
									if ( $field->get( 'searchable', false, GetterInterface::BOOLEAN ) ) {
										$static						=	false;
									}
								}
							}

							$isMatched								=	self::getConditionMatch( $fieldValue, $operator, $value, $delimiter );

							if ( $andMatched ) {
								$andMatched							=	$isMatched;
							}

							if ( $debug ) {
								$extras								=	array(	'[tab]' => $tabId,
																				'[and]' => ( $andIndex + 1 ),
																				'[or]' => ( $orIndex + 1 ),
																				'[input]' => ( $fieldValue === null ? 'NULL' : ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ),
																				'[operator]' => self::getOperator( $operator ),
																				'[value]' => ( $value === null ? 'NULL' : ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) )
																			);

								if ( $isMatched ) {
									$_CB_framework->enqueueMessage( CBTxt::T( 'TAB_CONDITION_DEBUG_SUCCESS', 'Tab [tab] condition [and] (AND) of [or] (OR) matched with: "[input]" [operator] "[value]"', $extras ) );
								} else {
									$_CB_framework->enqueueMessage( CBTxt::T( 'TAB_CONDITION_DEBUG_FAILED', 'Tab [tab] condition [and] (AND) of [or] (OR) failed to match with: "[input]" [operator] "[value]"', $extras ), 'error' );
								}
							}

							switch ( $fieldName ) {
								case 'customvalue':
								case 'customviewaccesslevels':
								case 'customusergroups':
								case 'custommoderators':
								case 'customusers':
									$andConditions[]				=	"{"
																	.		"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ) ) . "',"
																	.		"operator: " . (int) $operator . ","
																	.		( $delimiter ? "delimiter: '" . addslashes( $delimiter ) . "'," : null )
																	.		"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) ) ) . "'"
																	.	"}";
									break;
								case 'customlanguages':
									$jsTargets[]					=	'#params_language';

									$andConditions[]				=	"{"
																	.		"element: '#params_language',"
																	.		"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ) ) . "',"
																	.		"operator: " . (int) $operator . ","
																	.		( $delimiter ? "delimiter: '" . addslashes( $delimiter ) . "'," : null )
																	.		"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) ) ) . "'"
																	.	"}";
									break;
								default:
									$fieldId						=	$field->get( 'fieldid', 0, GetterInterface::INT );

									$jsTargets[]					=	'#cbfr_' . $fieldId;
									$jsTargets[]					=	'#cbfrd_' . $fieldId;

									$andConditions[]				=	"{"
																	.		"element: '#cbfr_" . (int) $fieldId . ",#cbfrd_" . (int) $fieldId . "',"
																	.		"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ) ) . "',"
																	.		"operator: " . (int) $operator . ","
																	.		( $delimiter ? "delimiter: '" . addslashes( $delimiter ) . "'," : null )
																	.		"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) ) ) . "'"
																	.	"}";
									break;
							}
						}

						if ( $andConditions ) {
							$jsConditions[]							=	"[" . implode( ",", $andConditions ) . "]";
						}

						$conditionsUsed++;
					}

					if ( ( ! $orMatched  ) && $andMatched ) {
						$orMatched									=	true;
					}
				}

				if ( $conditionsUsed ) {
					$conditioned									=	$orMatched;

					if ( $jsConditions ) {
						$showHide									=	array( '#cbtabpane' . $tabId, '#cbtf_' . $tabId, '#cbtp_' . $tabId );

						foreach ( self::getTabFields( $tabId, $userId ) as $tabField ) {
							/** @var  FieldTable $tabField */
							$fId									=	$tabField->get( 'fieldid', 0, GetterInterface::INT );

							$showHide[]								=	'#cbfr_' . $fId;
							$showHide[]								=	'#cbfrd_' . $fId;

							switch ( $tabField->get( 'type', null, GetterInterface::STRING ) ) {
								case 'password':
									$showHide[]						=	'#cbfr_' . $fId . '__verify';
									$showHide[]						=	'#cbfrd_' . $fId . '__verify';
									$showHide[]						=	'#cbfr_' . $fId . '__current';
									$showHide[]						=	'#cbfrd_' . $fId . '__current';
									break;
								case 'email':
									$showHide[]						=	'#cbfr_' . $fId . '__verify';
									$showHide[]						=	'#cbfrd_' . $fId . '__verify';
									break;
							}
						}
	
						$js											.=	"$( " . ( $jsTargets ? "'" . implode( ",", $jsTargets ) . "'" : 'window' ) . " ).cbcondition({"
																	.		"conditions: [" . implode( ",", $jsConditions ) . "],"
																	.		( $display == 1 ? "show: ['" . implode( "','", $showHide ) . "']," : "hide: ['" . implode( "','", $showHide ) . "']," )
																	.		"reset: " . self::getGlobalParams()->get( 'conditions_reset', 1, GetterInterface::INT ) . ","
																	.		"debug: " . (int) $debug
																	.	"});";
					}

					if ( $display == 1 ) {
						// Since we're displaying on match we need to reverse the condition:
						if ( $conditioned ) {
							$conditioned							=	false;
						} else {
							$conditioned							=	true;
						}
					}
				}
			}

			if ( $conditioned && $static ) {
				// Set the condition to static since we've no actual need for the JS:
				$conditioned										=	2;
			}

			$cache[$tabId][$userId][$reason]						=	$conditioned;
			$jsCache[$tabId][$userId][$reason]						=	$js;
		}

		$conditioned												=	$cache[$tabId][$userId][$reason];

		if ( $conditioned !== 2 ) {
			// Only output the JS if it's needed (not static):
			$js														=	$jsCache[$tabId][$userId][$reason];

			if ( $outputJs && $js && ( ! isset( $jsOutput[$tabId][$userId][$reason] ) ) ) {
				$_CB_framework->addJQueryPlugin( 'cbcondition', '/components/com_comprofiler/plugin/user/plug_cbconditional/js/cbcondition.js' );

				$_CB_framework->outputCbJQuery( $js, 'cbcondition' );

				$jsOutput[$tabId][$userId][$reason]					=	true;
			}

			if ( $conditioned && ( ! $js ) ) {
				// No JS to output so just treat it as static:
				return 2;
			}
		}

		return $conditioned;
	}

	/**
	 * Check is a field has been conditioned
	 *
	 * @param int|FieldTable $field
	 * @param string         $reason
	 * @param int            $userId
	 * @param bool           $outputJs
	 * @return bool|int               0|false = not conditioned, 1|true = conditioned, 2 = static conditioned
	 */
	static public function getFieldConditional( $field, $reason, $userId, $outputJs = false )
	{
		global $_CB_framework;

		static $cache												=	array();
		static $jsCache												=	array();
		static $jsOutput											=	array();

		if ( ! $field instanceof FieldTable ) {
			$field													=	CBConditional::getField( $field, $userId );

			if ( ! $field ) {
				return false;
			}
		}

		$fieldId													=	$field->get( 'fieldid', 0, GetterInterface::INT );

		if ( ! isset( $cache[$fieldId][$userId][$reason] ) ) {
			if ( ! $field->params instanceof ParamsInterface ) {
				$field->params										=	new Registry( $field->params );
			}

			$display												=	$field->params->get( 'cbconditional_conditioned', 0, GetterInterface::INT );
			$debug													=	$field->params->get( 'cbconditional_debug', false, GetterInterface::BOOLEAN );

			$conditioned											=	false;
			$static													=	true;
			$js														=	null;

			if ( $display ) {
				$cbUser												=	\CBuser::getInstance( (int) $userId, false );

				$orMatched											=	false;

				$jsTargets											=	array();
				$jsConditions										=	array();

				$conditionsUsed										=	0;

				foreach ( $field->params->subTree( 'cbconditional_conditions' ) as $orIndex => $orCondition ) {
					/** @var ParamsInterface $orCondition */
					$andMatched										=	true;
					$andConditions									=	array();

					foreach ( $orCondition->subTree( 'condition' ) as $andIndex => $andCondition ) {
						/** @var ParamsInterface $andCondition */
						if ( $reason == 'register' ) {
							if ( ! $andCondition->get( 'location_registration', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'edit' ) {
							if ( ! $andCondition->get( 'location_profile_edit', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'profile' ) {
							if ( ! $andCondition->get( 'location_profile_view', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'search' ) {
							if ( ! $andCondition->get( 'location_userlist_search', false, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						} elseif ( $reason == 'list' ) {
							if ( ! $andCondition->get( 'location_userlist_view', true, GetterInterface::BOOLEAN ) ) {
								continue;
							}
						}

						$conditionFieldPair							=	explode( ',', $andCondition->get( 'field', null, GetterInterface::STRING ) );

						if ( count( $conditionFieldPair ) < 2 ) {
							array_unshift( $conditionFieldPair, 0 );
						}

						$conditionFieldId							=	(int) array_shift( $conditionFieldPair );
						$conditionFieldName							=	array_pop( $conditionFieldPair );

						if ( $conditionFieldName ) {
							$operator								=	$andCondition->get( 'operator', 0, GetterInterface::INT );

							if ( in_array( $operator, array( 8, 9, 12, 13 ) ) ) {
								$delimiter							=	$andCondition->get( 'delimiter', null, GetterInterface::STRING );
							} else {
								$delimiter							=	null;
							}

							$value									=	null;

							if ( ! in_array( $conditionFieldName, array( 'customviewaccesslevels', 'customusergroups', 'customlanguages', 'custommoderators', 'customusers' ) ) ) {
								$value								=	$cbUser->replaceUserVars( $andCondition->get( 'value', null, GetterInterface::RAW ), false, true, self::getSubstitutions(), $andCondition->get( 'value_translate', false, GetterInterface::BOOLEAN ) );

								if ( in_array( $operator, array( '6', '7' ) ) ) {
									$value							=	null;
								}
							}

							$conditionField							=	null;

							switch ( $conditionFieldName ) {
								case 'customvalue':
									$fieldValue						=	$cbUser->replaceUserVars( $andCondition->get( 'field_custom', null, GetterInterface::RAW ), false, true, self::getSubstitutions(), $andCondition->get( 'field_custom_translate', false, GetterInterface::BOOLEAN ) );
									break;
								case 'customviewaccesslevels':
									$accessLevels					=	cbToArrayOfInt( explode( '|*|', $andCondition->get( 'field_viewaccesslevels', null, GetterInterface::STRING ) ) );
									$userAccessLevels				=	Application::User( (int) $userId )->getAuthorisedViewLevels();
									$fieldValue						=	0;

									foreach ( $accessLevels as $accessLevel ) {
										if ( in_array( $accessLevel, $userAccessLevels ) ) {
											$fieldValue				=	1;
											break;
										}
									}

									$operator						=	$andCondition->get( 'operator_viewaccesslevels', 0, GetterInterface::INT );
									$value							=	1;
									break;
								case 'customusergroups':
									$userGroups						=	cbToArrayOfInt( explode( '|*|', $andCondition->get( 'field_usergroups', null, GetterInterface::STRING ) ) );
									$userUsergroups					=	Application::User( (int) $userId )->getAuthorisedGroups();
									$fieldValue						=	0;

									foreach ( $userGroups as $userGroup ) {
										if ( in_array( $userGroup, $userUsergroups ) ) {
											$fieldValue				=	1;
											break;
										}
									}

									$operator						=	$andCondition->get( 'operator_usergroups', 0, GetterInterface::INT );
									$value							=	1;
									break;
								case 'customlanguages':
									$fieldValue						=	$cbUser->getUserData()->getUserLanguage();
									$operator						=	$andCondition->get( 'operator_languages', 12, GetterInterface::INT );
									$delimiter						=	'|*|';
									$value							=	$andCondition->get( 'field_languages', null, GetterInterface::STRING );

									$conditionField					=	CBConditional::getField( 'params', $userId );
									break;
								case 'custommoderators':
									$fieldValue						=	(int) Application::User( (int) $userId )->isGlobalModerator();
									$operator						=	$andCondition->get( 'operator_moderators', 0, GetterInterface::INT );
									$value							=	1;
									break;
								case 'customusers':
									$fieldValue						=	(int) $userId;
									$operator						=	$andCondition->get( 'operator_users', 12, GetterInterface::INT );
									$delimiter						=	',';
									$value							=	$cbUser->replaceUserVars( $andCondition->get( 'field_users', null, GetterInterface::STRING ), true, false, array(), false );
									break;
								default:
									$conditionField					=	CBConditional::getField( $conditionFieldId, $userId );

									if ( ! $conditionField ) {
										continue 2;
									}

									$fieldValue						=	self::getFieldValue( $userId, $conditionField, $reason );
									break;
							}

							if ( $conditionField ) {
								// If there's a field available lets check if it's being output to determine if it's a non-static condition with JS:
								if ( $reason == 'register' ) {
									if ( $conditionField->get( 'registration', 1, GetterInterface::INT ) > 0 ) {
										$static						=	false;
									}
								} elseif ( $reason == 'edit' ) {
									if ( $conditionField->get( 'edit', 1, GetterInterface::INT ) > 0 ) {
										$static						=	false;
									}
								} elseif ( $reason == 'search' ) {
									if ( $conditionField->get( 'searchable', false, GetterInterface::BOOLEAN ) ) {
										$static						=	false;
									}
								}
							}

							$isMatched								=	self::getConditionMatch( $fieldValue, $operator, $value, $delimiter );

							if ( $andMatched ) {
								$andMatched							=	$isMatched;
							}

							if ( $debug ) {
								$extras								=	array(	'[field]' => $fieldId,
																				'[and]' => ( $andIndex + 1 ),
																				'[or]' => ( $orIndex + 1 ),
																				'[input]' => ( $fieldValue === null ? 'NULL' : ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ),
																				'[operator]' => self::getOperator( $operator ),
																				'[value]' => ( $value === null ? 'NULL' : ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) )
																			);

								if ( $isMatched ) {
									$_CB_framework->enqueueMessage( CBTxt::T( 'FIELD_CONDITION_DEBUG_SUCCESS', 'Field [field] condition [and] (AND) of [or] (OR) matched with: "[input]" [operator] "[value]"', $extras ) );
								} else {
									$_CB_framework->enqueueMessage( CBTxt::T( 'FIELD_CONDITION_DEBUG_FAILED', 'Field [field] condition [and] (AND) of [or] (OR) failed to match with: "[input]" [operator] "[value]"', $extras ), 'error' );
								}
							}

							switch ( $conditionFieldName ) {
								case 'customvalue':
								case 'customviewaccesslevels':
								case 'customusergroups':
								case 'custommoderators':
								case 'customusers':
									$andConditions[]				=	"{"
																	.		"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ) ) . "',"
																	.		"operator: " . (int) $operator . ","
																	.		( $delimiter ? "delimiter: '" . addslashes( $delimiter ) . "'," : null )
																	.		"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) ) ) . "'"
																	.	"}";
									break;
								case 'customlanguages':
									$jsTargets[]					=	'#params_language';

									$andConditions[]				=	"{"
																	.		"element: '#params_language',"
																	.		"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ) ) . "',"
																	.		"operator: " . (int) $operator . ","
																	.		( $delimiter ? "delimiter: '" . addslashes( $delimiter ) . "'," : null )
																	.		"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) ) ) . "'"
																	.	"}";
									break;
								default:
									$jsTargets[]					=	'#cbfr_' . $conditionFieldId;
									$jsTargets[]					=	'#cbfrd_' . $conditionFieldId;

									$andConditions[]				=	"{"
																	.		"element: '#cbfr_" . (int) $conditionFieldId . ",#cbfrd_" . (int) $conditionFieldId . "',"
																	.		"input: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $fieldValue ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $fieldValue ) : $fieldValue ) ) ) . "',"
																	.		"operator: " . (int) $operator . ","
																	.		( $delimiter ? "delimiter: '" . addslashes( $delimiter ) . "'," : null )
																	.		"value: '" . addslashes( str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), ( is_array( $value ) ? implode( ( $delimiter ? $delimiter : '|*|' ), $value ) : $value ) ) ) . "'"
																	.	"}";
									break;
							}
						}

						if ( $andConditions ) {
							$jsConditions[]							=	"[" . implode( ",", $andConditions ) . "]";
						}

						$conditionsUsed++;
					}

					if ( ( ! $orMatched  ) && $andMatched ) {
						$orMatched									=	true;
					}
				}

				if ( $conditionsUsed ) {
					$conditioned									=	$orMatched;

					if ( $jsConditions ) {
						$showHide									=	array( '#cbfr_' . $fieldId, '#cbfrd_' . $fieldId );

						switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
							case 'password':
								$showHide[]							=	'#cbfr_' . $fieldId . '__verify';
								$showHide[]							=	'#cbfrd_' . $fieldId . '__verify';
								$showHide[]							=	'#cbfr_' . $fieldId . '__current';
								$showHide[]							=	'#cbfrd_' . $fieldId . '__current';
								break;
							case 'email':
								$showHide[]							=	'#cbfr_' . $fieldId . '__verify';
								$showHide[]							=	'#cbfrd_' . $fieldId . '__verify';
								break;
						}

						$js											.=	"$( " . ( $jsTargets ? "'" . implode( ",", $jsTargets ) . "'" : 'window' ) . " ).cbcondition({"
																	.		"conditions: [" . implode( ",", $jsConditions ) . "],"
																	.		( $display == 1 ? "show: ['" . implode( "','", $showHide ) . "']," : "hide: ['" . implode( "','", $showHide ) . "']," )
																	.		"reset: " . self::getGlobalParams()->get( 'conditions_reset', 1, GetterInterface::INT ) . ","
																	.		"debug: " . (int) $debug
																	.	"});";
					}

					if ( $display == 1 ) {
						// Since we're displaying on match we need to reverse the condition:
						if ( $conditioned ) {
							$conditioned							=	false;
						} else {
							$conditioned							=	true;
						}
					}
				}
			}

			if ( $conditioned && $static ) {
				// Set the condition to static since we've no actual need for the JS:
				$conditioned										=	2;
			}

			$cache[$fieldId][$userId][$reason]						=	$conditioned;
			$jsCache[$fieldId][$userId][$reason]					=	$js;
		}

		$conditioned												=	$cache[$fieldId][$userId][$reason];

		if ( $conditioned !== 2 ) {
			// Only output the JS if it's needed (not static):
			$js														=	$jsCache[$fieldId][$userId][$reason];

			if ( $outputJs && $js && ( ! isset( $jsOutput[$fieldId][$userId][$reason] ) ) ) {
				$_CB_framework->addJQueryPlugin( 'cbcondition', '/components/com_comprofiler/plugin/user/plug_cbconditional/js/cbcondition.js' );

				$_CB_framework->outputCbJQuery( $js, 'cbcondition' );

				$jsOutput[$fieldId][$userId][$reason]				=	true;
			}

			if ( $conditioned && ( ! $js ) ) {
				// No JS to output so just treat it as static:
				return 2;
			}
		}

		return $conditioned;
	}

	/**
	 * Compares condition values based off operator
	 *
	 * @param string $value
	 * @param int    $operator
	 * @param string $input
	 * @param string $delimiter
	 * @return bool
	 */
	static public function getConditionMatch( $value, $operator, $input, $delimiter = null )
	{
		if ( is_array( $value ) ) {
			$value			=	implode( ( $delimiter ? $delimiter : '|*|' ), $value );
		}

		if ( is_array( $input ) ) {
			$input			=	implode( ( $delimiter ? $delimiter : '|*|' ), $input );
		}

		$value				=	trim( $value );
		$input				=	trim( $input );

		switch ( $operator ) {
			case 1:
				$match		=	( $value != $input );
				break;
			case 2:
				$match		=	( $value > $input );
				break;
			case 3:
				$match		=	( $value < $input );
				break;
			case 4:
				$match		=	( $value >= $input );
				break;
			case 5:
				$match		=	( $value <= $input );
				break;
			case 6:
				$match		=	( ! $value );
				break;
			case 7:
				$match		=	( $value );
				break;
			case 8:
				if ( $delimiter ) {
					$match	=	( in_array( $input, explode( $delimiter, $value ) ) );
				} else {
					if ( $input === '' ) {
						// Can't have an empty needle so fallback to simple equal to check:
						return self::getConditionMatch( $value, 0, $input, $delimiter );
					}

					$match	=	( stristr( $value, $input ) );
				}
				break;
			case 9:
				if ( $delimiter ) {
					$match	=	( ! in_array( $input, explode( $delimiter, $value ) ) );
				} else {
					if ( $input === '' ) {
						// Can't have an empty needle so fallback to simple not equal to check:
						return self::getConditionMatch( $value, 1, $input, $delimiter );
					}

					$match	=	( ! stristr( $value, $input ) );
				}
				break;
			case 10:
				if ( $input === '' ) {
					// Can't have an empty regexp so fallback to simple equal to check:
					return self::getConditionMatch( $value, 0, $input, $delimiter );
				}

				$match		=	( preg_match( $input, $value ) );
				break;
			case 11:
				if ( $input === '' ) {
					// Can't have an empty regexp so fallback to simple not equal to check:
					return self::getConditionMatch( $value, 1, $input, $delimiter );
				}

				$match		=	( ! preg_match( $input, $value ) );
				break;
			case 12:
				if ( $delimiter ) {
					$match	=	( in_array( $input, explode( $delimiter, $value ) ) );
				} else {
					if ( $value === '' ) {
						// Can't have an empty needle so fallback to simple equal to check:
						return self::getConditionMatch( $value, 0, $input, $delimiter );
					}

					$match	=	( stristr( $input, $value ) );
				}
				break;
			case 13:
				if ( $delimiter ) {
					$match	=	( ! in_array( $input, explode( $delimiter, $value ) ) );
				} else {
					if ( $value === '' ) {
						// Can't have an empty needle so fallback to simple not equal to check:
						return self::getConditionMatch( $value, 1, $input, $delimiter );
					}

					$match	=	( ! stristr( $input, $value ) );
				}
				break;
			case 0:
			default:
				$match		=	( $value == $input );
				break;
		}

		return (bool) $match;
	}

	/**
	 * Returns human readable text for operator
	 *
	 * @param int $operator
	 * @return string
	 */
	static public function getOperator( $operator )
	{
		switch ( $operator ) {
			case 1:
				$operator	=	CBTxt::T( 'Not Equal To' );
				break;
			case 2:
				$operator	=	CBTxt::T( 'Greater Than' );
				break;
			case 3:
				$operator	=	CBTxt::T( 'Less Than' );
				break;
			case 4:
				$operator	=	CBTxt::T( 'Greater Than or Equal To' );
				break;
			case 5:
				$operator	=	CBTxt::T( 'Less Than or Equal To' );
				break;
			case 6:
				$operator	=	CBTxt::T( 'Empty' );
				break;
			case 7:
				$operator	=	CBTxt::T( 'Not Empty' );
				break;
			case 8:
				$operator	=	CBTxt::T( 'Does Contain' );
				break;
			case 9:
				$operator	=	CBTxt::T( 'Does Not Contain' );
				break;
			case 10:
				$operator	=	CBTxt::T( 'Is REGEX' );
				break;
			case 11:
				$operator	=	CBTxt::T( 'Is Not REGEX' );
				break;
			case 12:
				$operator	=	CBTxt::T( 'Is In' );
				break;
			case 13:
				$operator	=	CBTxt::T( 'Is Not In' );
				break;
			case 0:
			default:
				$operator	=	CBTxt::T( 'Equal To' );
				break;
		}

		return $operator;
	}

	/**
	 * Grabs field value from POST or user object based off location
	 *
	 * @param int|UserTable $user
	 * @param FieldTable    $field
	 * @param string        $reason
	 * @return array|mixed|string
	 */
	static public function getFieldValue( $user, $field, $reason )
	{
		global $_PLUGINS;

		static $values											=	array();

		$fieldId												=	$field->get( 'fieldid', 0, GetterInterface::INT );

		if ( ! $fieldId ) {
			return null;
		}

		if ( ! $user instanceof UserTable ) {
			$user												=	\CBuser::getUserDataInstance( (int) $user );
		}

		$post													=	Application::Input()->getNamespaceRegistry( 'post' );
		$checkPost												=	false;

		if ( in_array( $reason, array( 'register', 'edit' ) ) && $post->count() ) {
			$view												=	Application::Input()->get( 'view', null, GetterInterface::STRING );

			if ( Application::Cms()->getClientId() && in_array( $view, array( 'apply', 'save' ) ) ) {
				$checkPost										=	true;
			} elseif ( in_array( $view, array( 'saveregisters', 'saveuseredit' ) ) ) {
				$checkPost										=	true;
			}

			if ( $checkPost ) {
				if ( $field->get( 'readonly', 0, GetterInterface::INT ) && ( $reason != 'register' ) && ( ! Application::Cms()->getClientId() ) ) {
					$checkPost									=	false;
				} elseif ( ( $reason == 'register' ) && ( ! $field->get( 'registration', 1, GetterInterface::INT ) ) ) {
					$checkPost									=	false;
				} elseif ( ( $reason == 'edit' ) && ( ! $field->get( 'edit', 1, GetterInterface::INT ) ) ) {
					$checkPost									=	false;
				}
			}
		}

		$userId													=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $values[$fieldId][$userId][$reason][$checkPost] ) ) {
			if ( ! $field->params instanceof ParamsInterface ) {
				$field->params									=	new Registry( $field->params );
			}

			$fieldName											=	$field->get( 'name', null, GetterInterface::STRING );
			$fieldValue											=	null;

			if ( $checkPost ) {
				$postUser										=	new UserTable();

				foreach ( array_keys( get_object_vars( $user ) ) as $k ) {
					if ( substr( $k, 0, 1 ) != '_' ) {
						$postUser->set( $k, $user->get( $k ) );
					}
				}

				if ( ! $post->has( $fieldName ) ) {
					if ( self::getGlobalParams()->get( 'conditions_reset', true, GetterInterface::BOOLEAN ) ) {
						switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
							case 'date':
								$post->set( $fieldName, '0000-00-00' );
								break;
							case 'datetime':
								$post->set( $fieldName, '0000-00-00 00:00:00' );
								break;
							case 'integer':
							case 'points':
							case 'rating':
							case 'checkbox':
							case 'terms':
							case 'counter':
								$post->set( $fieldName, 0 );
								break;
							case 'image':
								$post->set( $fieldName, '' );
								$post->set( $fieldName . 'approved', 0 );
								break;
							default:
								foreach ( $field->getTableColumns() as $column ) {
									$post->set( $column, '' );
								}
								break;
						}
					} else {
						$post->set( $fieldName, null );
					}
				}

				$postUser->bindThisUserFromDbArray( $post->asArray() );

				$fieldValue										=	$postUser->get( $fieldName );

				if ( is_array( $fieldValue ) ) {
					$fieldValue									=	implode( '|*|', $fieldValue );
				}

				if ( $fieldValue === null ) {
					$field->set( '_noCondition', true );

					$fieldValue									=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$postUser, 'php', 'none', 'profile', 0 ), $field );

					$field->set( '_noCondition', false );

					if ( is_array( $fieldValue ) ) {
						$fieldValue								=	array_shift( $fieldValue );

						if ( is_array( $fieldValue ) ) {
							$fieldValue							=	implode( '|*|', $fieldValue );
						}
					}
				}
			}

			if ( $fieldValue === null ) {
				$fieldValue										=	$user->get( $fieldName );

				if ( is_array( $fieldValue ) ) {
					$fieldValue									=	implode( '|*|', $fieldValue );
				}

				if ( $fieldValue === null ) {
					$field->set( '_noCondition', true );

					$fieldValue									=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'php', 'none', 'profile', 0 ), $field );

					$field->set( '_noCondition', false );

					if ( is_array( $fieldValue ) ) {
						$fieldValue								=	array_shift( $fieldValue );

						if ( is_array( $fieldValue ) ) {
							$fieldValue							=	implode( '|*|', $fieldValue );
						}
					}
				}
			}

			$values[$fieldId][$userId][$reason][$checkPost]		=	$fieldValue;
		}

		return $values[$fieldId][$userId][$reason][$checkPost];
	}

	/**
	 * Parses substitution extras array from available variables
	 *
	 * @return array
	 */
	static public function getSubstitutions()
	{
		static $extras		=	array();

		if ( empty( $extras ) ) {
			$input			=	Application::Input();

			$get			=	$input->getNamespaceRegistry( 'get' );

			if ( $get ) {
				self::prepareExtras( 'get', $get->asArray(), $extras );
			}

			$post			=	$input->getNamespaceRegistry( 'post' );

			if ( $post ) {
				self::prepareExtras( 'post', $post->asArray(), $extras );
			}

			$files			=	$input->getNamespaceRegistry( 'files' );

			if ( $files ) {
				self::prepareExtras( 'files', $files->asArray(), $extras );
			}

			$cookie			=	$input->getNamespaceRegistry( 'cookie' );

			if ( $cookie ) {
				self::prepareExtras( 'cookie', $cookie->asArray(), $extras );
			}

			$server			=	$input->getNamespaceRegistry( 'server' );

			if ( $server ) {
				self::prepareExtras( 'server', $server->asArray(), $extras );
			}

			$env			=	$input->getNamespaceRegistry( 'env' );

			if ( $env ) {
				self::prepareExtras( 'env', $env->asArray(), $extras );
			}
		}

		return $extras;
	}

	/**
	 * Converts array or object into pathed extras substitutions
	 *
	 * @param string       $prefix
	 * @param array|object $items
	 * @param array        $extras
	 */
	static public function prepareExtras( $prefix, $items, &$extras )
	{
		foreach ( $items as $k => $v ) {
			if ( is_array( $v ) ) {
				$multi					=	false;

				foreach ( $v as $kv => $cv ) {
					if ( is_numeric( $kv ) ) {
						$kv				=	(int) $kv;
					}

					if ( is_object( $cv ) || is_array( $cv ) || ( $kv && ( ! is_int( $kv ) ) ) ) {
						$multi			=	true;
					}
				}

				if ( ! $multi ) {
					$v					=	implode( '|*|', $v );
				}
			}

			$k							=	'_' . ltrim( str_replace( ' ', '_', trim( strtolower( $k ) ) ), '_' );

			if ( ( ! is_object( $v ) ) && ( ! is_array( $v ) ) ) {
				$extras[$prefix . $k]	=	$v;
			} elseif ( $v ) {
				if ( is_object( $v ) ) {
					/** @var object $v */
					$subItems			=	get_object_vars( $v );
				} else {
					$subItems			=	$v;
				}

				self::prepareExtras( $prefix . $k, $subItems, $extras );
			}
		}
	}

	/**
	 * @return array
	 */
	static public function loadFields()
	{
 		global $_CB_database;

		static $values		=	null;

		if ( $values === null ) {
			$values			=	array();

			$query			=	"SELECT CONCAT_WS( ',', f." . $_CB_database->NameQuote( 'fieldid' ) . ", f." . $_CB_database->NameQuote( 'name' ) . " ) AS value"
							.	", f." . $_CB_database->NameQuote( 'title' ) . " AS text"
							.	", f." . $_CB_database->NameQuote( 'name' )
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' ) . " AS f"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler_tabs' ) . " AS t"
							.	" ON t." . $_CB_database->NameQuote( 'tabid' ) . " = f." . $_CB_database->NameQuote( 'tabid' )
							.	"\n WHERE f." . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	"\n AND f." . $_CB_database->NameQuote( 'name' ) . " != " . $_CB_database->Quote( 'NA' )
							.	"\n ORDER BY t." . $_CB_database->NameQuote( 'position' ) . ", t." . $_CB_database->NameQuote( 'ordering' ) . ", f." . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$fields			=	$_CB_database->loadObjectList();

			foreach ( $fields as $field ) {
				$values[]	=	\moscomprofilerHTML::makeOption( $field->value, CBTxt::T( $field->text ) . ' (' . $field->name . ')' );
			}
		}

		return $values;
	}
}
