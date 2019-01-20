<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Privacy\Table\PrivacyTable;

defined('CBLIB') or die();

class CBPrivacy
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' );
			$params		=	new Registry();

			if ( $plugin ) {
				$params->load( $plugin->params );
			}
		}

		return $params;
	}

	/**
	 * @param null|string $template
	 * @param null|string $file
	 * @param bool|array  $headers
	 * @return null|string
	 */
	static public function getTemplate( $template = null, $file = null, $headers = array( 'template', 'override' ) )
	{
		global $_CB_framework, $_PLUGINS;

		$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' );

		if ( ! $plugin ) {
			return null;
		}

		static $defaultTemplate			=	null;

		if ( $defaultTemplate === null ) {
			$defaultTemplate			=	self::getGlobalParams()->get( 'general_template', 'default', GetterInterface::STRING );
		}

		if ( ( $template === '' ) || ( $template === null ) || ( $template === '-1' ) ) {
			$template					=	$defaultTemplate;
		}

		if ( ! $template ) {
			$template					=	'default';
		}

		$livePath						=	$_PLUGINS->getPluginLivePath( $plugin );
		$absPath						=	$_PLUGINS->getPluginPath( $plugin );

		$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );
		$return							=	null;

		if ( $file ) {
			if ( $headers !== false ) {
				$headers[]				=	$file;
			}

			$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';

			if ( ! file_exists( $php ) ) {
				$php					=	$absPath . '/templates/default/' . $file . '.php';
			}

			if ( file_exists( $php ) ) {
				$return					=	$php;
			}
		}

		if ( $headers !== false ) {
			static $loaded				=	array();

			$loaded[$template]			=	array();

			// Global CSS File:
			if ( in_array( 'template', $headers ) && ( ! in_array( 'template', $loaded[$template] ) ) ) {
				$global					=	'/templates/' . $template . '/template.css';

				if ( ! file_exists( $absPath . $global ) ) {
					$global				=	'/templates/default/template.css';
				}

				if ( file_exists( $absPath . $global ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $global );
				}

				$loaded[$template][]	=	'template';
			}

			// File or Custom CSS/JS Headers:
			foreach ( $headers as $header ) {
				if ( in_array( $header, $loaded[$template] ) || in_array( $header, array( 'template', 'override' ) ) ) {
					continue;
				}

				$header					=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $header );

				if ( ! $header ) {
					continue;
				}

				$css					=	'/templates/' . $template . '/' . $header . '.css';
				$js						=	'/templates/' . $template . '/' . $header . '.js';

				if ( ! file_exists( $absPath . $css ) ) {
					$css				=	'/templates/default/' . $header . '.css';
				}

				if ( file_exists( $absPath . $css ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $css );
				}

				if ( ! file_exists( $absPath . $js ) ) {
					$js					=	'/templates/default/' . $header . '.js';
				}

				if ( file_exists( $absPath . $js ) ) {
					$_CB_framework->document->addHeadScriptUrl( $livePath . $js );
				}

				$loaded[$template][]	=	$header;
			}

			// Override CSS File:
			if ( in_array( 'override', $headers ) && ( ! in_array( 'override', $loaded[$template] ) ) ) {
				$override				=	'/templates/' . $template . '/override.css';

				if ( file_exists( $absPath . $override ) ) {
					$_CB_framework->document->addHeadStyleSheet( $livePath . $override );
				}

				$loaded[$template][]	=	'override';
			}
		}

		return $return;
	}

	/**
	 * Returns an array of users connections
	 *
	 * @param int  $profileId
	 * @param bool $raw
	 * @return array
	 */
	static public function getConnections( $profileId, $raw = false )
	{
		if ( ( ! $profileId ) || ( ! Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) ) {
			return array();
		}

		static $cache				=	array();

		if ( ! isset( $cache[$profileId] ) ) {
			$cbConnection			=	new \cbConnection( $profileId );

			$cache[$profileId]		=	$cbConnection->getActiveConnections( $profileId );
		}

		if ( ! $raw ) {
			$connections			=	array();

			foreach ( $cache[$profileId] as $connection ) {
				$connections[]		=	(int) $connection->id;
			}

			return $connections;
		}

		return $cache[$profileId];
	}

	/**
	 * Checks if two users are actively connected to one another
	 *
	 * @param int $fromUserId
	 * @param int $toUserId
	 * @return bool
	 */
	static public function getConnectionOfConnection( $fromUserId, $toUserId )
	{
		if ( ( ! $fromUserId ) || ( ! $toUserId ) || ( ! Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) ) {
			return false;
		}

		static $connected							=	array();

		if ( ! isset( $connected[$fromUserId][$toUserId] ) ) {
			$cbConnection							=	new \cbConnection( $fromUserId );

			$connected[$fromUserId][$toUserId]		=	( $cbConnection->getDegreeOfSepPathArray( $fromUserId, $toUserId, 1, 2 ) ? true : false );
		}

		return $connected[$fromUserId][$toUserId];
	}

	/**
	 * Returns an options array of available privacy values
	 *
	 * @param null|Privacy $privacy
	 * @param bool         $raw
	 * @return array
	 */
	static public function getPrivacyOptions( $privacy = null, $raw = false )
	{
		global $_PLUGINS, $ueConfig;

		static $cache						=	null;

		if ( Application::Cms()->getClientId() ) {
			$privacy						=	null;
			$raw							=	false;
		}

		if ( $privacy ) {
			$privacyId						=	$privacy->id();
		} else {
			$privacyId						=	0;
		}

		if ( ! isset( $cache[$privacyId] ) ) {
			if ( $privacy ) {
				$userId						=	$privacy->user()->get( 'id', 0, GetterInterface::INT );
				$params						=	$privacy;
				$prefix						=	null;
			} else {
				$userId						=	Application::MyUser()->getUserId();
				$params						=	self::getGlobalParams();
				$prefix						=	'privacy_';
			}

			$options						=	array();

			$_PLUGINS->trigger( 'privacy_onBeforePrivacyOptions', array( &$options, $privacy ) );

			if ( $params->get( $prefix . 'options_visible', true, GetterInterface::BOOLEAN ) ) {
				$options[]					=	\moscomprofilerHTML::makeOption( '0', CBTxt::T( 'PRIVACY_PUBLIC', 'Public' ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconPublic fa fa-globe"></span>' ) . '"' );
			}

			if ( ( ( $ueConfig['profile_viewaccesslevel'] == 1 ) && $params->get( $prefix . 'options_users', true, GetterInterface::BOOLEAN ) ) ) {
				$options[]					=	\moscomprofilerHTML::makeOption( '1', CBTxt::T( 'PRIVACY_USERS', 'Users' ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconUsers fa fa-user"></span>' ) . '"' );
			}

			if ( $params->get( $prefix . 'options_invisible', true, GetterInterface::BOOLEAN ) ) {
				$options[]					=	\moscomprofilerHTML::makeOption( '99', CBTxt::T( 'PRIVACY_PRIVATE', 'Private' ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconPrivate fa fa-lock"></span>' ) . '"' );
			}

			if ( $params->get( $prefix . 'options_moderator', false, GetterInterface::BOOLEAN ) ) {
				$options[]					=	\moscomprofilerHTML::makeOption( '999', CBTxt::T( 'PRIVACY_MODERATORS', 'Moderators' ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconModerators fa fa-user-secret"></span>' ) . '"' );
			}

			if ( $ueConfig['allowConnections'] ) {
				if ( $params->get( $prefix . 'options_conn', true, GetterInterface::BOOLEAN ) ) {
					$options[]				=	\moscomprofilerHTML::makeOption( '2', CBTxt::T( 'PRIVACY_CONNECTIONS', 'Connections' ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconConnections fa fa-users"></span>' ) . '"' );
				}

				if ( $params->get( $prefix . 'options_connofconn', true, GetterInterface::BOOLEAN ) ) {
					$options[]				=	\moscomprofilerHTML::makeOption( '3', CBTxt::T( 'PRIVACY_CONNECTIONS_OF_CONNECTIONS', 'Connections of Connections' ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconConnectionsOfConnections fa fa-users"></span>' ) . '"' );
				}

				if ( $ueConfig['connection_categories'] && $params->get( $prefix . 'options_conntype', true, GetterInterface::BOOLEAN ) ) {
					$types					=	self::getConnectionTypes();

					if ( $types ) {
						$connTypes			=	explode( '|*|', $params->get( $prefix . 'options_conntypes', '0', GetterInterface::STRING ) );

						$options[]			=	\moscomprofilerHTML::makeOptGroup( CBTxt::T( 'PRIVACY_CONNECTION_TYPES', 'Connection Types' ) );

						foreach ( $types as $type ) {
							if ( in_array( '0', $connTypes ) || in_array( $type->value, $connTypes ) ) {
								$options[]	=	\moscomprofilerHTML::makeOption( 'CONN-' . (string) $type->value, $type->text, 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconConnectionType fa fa-cog"></span>' ) . '"' );
							}
						}

						$options[]			=	\moscomprofilerHTML::makeOptGroup( null );
					}
				}
			}

			if ( $params->get( $prefix . 'options_viewaccesslevel', false, GetterInterface::BOOLEAN ) ) {
				$accessLevels				=	Application::CmsPermissions()->getAllViewAccessLevels( true, Application::User( $userId ) );

				if ( $accessLevels ) {
					$viewAccessLevels		=	explode( '|*|', $params->get( $prefix . 'options_viewaccesslevels', '0', GetterInterface::STRING ) );

					$options[]				=	\moscomprofilerHTML::makeOptGroup( CBTxt::T( 'PRIVACY_VIEWACCESSLEVELS', 'View Access Levels' ) );

					foreach ( $accessLevels as $accessLevel ) {
						if ( in_array( '0', $viewAccessLevels ) || in_array( $accessLevel->value, $viewAccessLevels ) ) {
							$options[]		=	\moscomprofilerHTML::makeOption( 'ACCESS-' . (string) $accessLevel->value, CBTxt::T( $accessLevel->text ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconViewAccessLevel fa fa-cog"></span>' ) . '"' );
						}
					}

					$options[]				=	\moscomprofilerHTML::makeOptGroup( null );
				}
			}

			if ( $params->get( $prefix . 'options_usergroup', false, GetterInterface::BOOLEAN ) ) {
				$groups						=	Application::CmsPermissions()->getAllGroups( true, '' );

				if ( $groups ) {
					$userGroups				=	explode( '|*|', $params->get( $prefix . 'options_usergroups', '0', GetterInterface::STRING ) );

					$options[]				=	\moscomprofilerHTML::makeOptGroup( CBTxt::T( 'PRIVACY_USERGROUPS', 'Usergroups' ) );

					foreach ( $groups as $group ) {
						if ( in_array( '0', $userGroups ) || in_array( $group->value, $userGroups ) ) {
							$options[]		=	\moscomprofilerHTML::makeOption( 'GROUP-' . (string) $group->value, CBTxt::T( $group->text ), 'value', 'text', null, null, 'data-cbprivacy-option-icon="' . htmlspecialchars( '<span class="cbPrivacySelectOptionIconUsergroup fa fa-cog"></span>' ) . '"' );
						}
					}

					$options[]				=	\moscomprofilerHTML::makeOptGroup( null );
				}
			}

			$_PLUGINS->trigger( 'privacy_onAfterPrivacyOptions', array( &$options, $privacy ) );

			$cache[$privacyId]				=	$options;
		}

		if ( $raw ) {
			$opts							=	array();

			foreach ( $cache[$privacyId] as $opt ) {
				if ( is_array( $opt->value ) ) {
					continue;
				}

				$opts[$opt->value]			=	$opt->text;
			}

			return $opts;
		}

		return $cache[$privacyId];
	}

	/**
	 * Returns an options array of connection types
	 *
	 * @return array
	 */
	static public function getConnectionTypes()
	{
		static $options			=	null;

		if ( $options === null ) {
			$options			=	array();

			if ( Application::Config()->get( 'connection_categories', null, GetterInterface::STRING ) ) {
				$types			=	explode( "\n", Application::Config()->get( 'connection_categories', null, GetterInterface::STRING ) );

				foreach ( $types as $type ) {
					if ( trim( $type ) == '' ) {
						continue;
					}

					$options[]	=	\moscomprofilerHTML::makeOption( htmlspecialchars( trim( $type ) ), CBTxt::T( trim( $type ) ) );
				}
			}
		}

		return $options;
	}

	/**
	 * Returns an array of users privacy rows
	 *
	 * @param null|int|UserTable $profileId
	 * @return array
	 */
	static public function getPrivacy( $profileId = null )
	{
		global $_CB_database;

		if ( $profileId === null ) {
			$profileId				=	Application::MyUser()->getUserId();
		} elseif ( $profileId instanceof UserTable ) {
			$profileId				=	$profileId->get( 'id', 0, GetterInterface::INT );
		}

		if ( ! $profileId ) {
			return array();
		}

		static $cache					=	array();

		if ( ! isset( $cache[$profileId] ) ) {
			$query						=	"SELECT *"
										.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $profileId;
			$_CB_database->setQuery( $query );
			$rules						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Privacy\Table\PrivacyTable', array( $_CB_database ) );

			$privacy					=	array();

			/** @var PrivacyTable[] $rules */
			foreach ( $rules as $id => $rule ) {
				$asset					=	$rule->get( 'asset', null, GetterInterface::STRING );

				$privacy[$asset][$id]	=	$rule;
			}

			$cache[$profileId]			=	$privacy;
		}

		return $cache[$profileId];
	}

	/**
	 * @param UserTable $user
	 * @param null|int  $tabId
	 * @param null|int  $fieldId
	 * @return bool
	 */
	static public function checkProfileDisplayAccess( $user = null, $tabId = null, $fieldId = null )
	{
		if ( ! $user ) {
			$user						=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user						=	\CBuser::getInstance( $user, false )->getUserData();
		}

		if ( Application::Cms()->getClientId() || Application::MyUser()->isGlobalModerator() || ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) ) {
			return true;
		}

		static $cache					=	array();

		static $field					=	null;
		static $hideTabs				=	array();
		static $hideFields				=	array();

		$userId							=	$user->get( 'id', 0, GetterInterface::INT );
		$myId							=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$userId][$myId] ) ) {
			$authorized					=	true;

			if ( ! $field ) {
				$field					=	new FieldTable();

				$field->load( array( 'name' => 'privacy_profile', 'published' => 1 ) );

				if ( ! $field->params instanceof ParamsInterface ) {
					$field->params		=	new Registry( $field->params );
				}

				$hideTabs				=	cbToArrayOfInt( explode( '|*|', $field->params->get( 'cbprivacy_profile_tabs', null, GetterInterface::STRING ) ) );
				$hideFields				=	cbToArrayOfInt( explode( '|*|', $field->params->get( 'cbprivacy_profile_fields', null, GetterInterface::STRING ) ) );
			}

			if ( $field->get( 'fieldid', 0, GetterInterface::INT ) && ( $field->get( 'edit', 1, GetterInterface::INT ) || $field->get( 'registration', 1, GetterInterface::INT ) ) ) {
				$privacy				=	new Privacy( 'profile', $user );

				$privacy->parse( $field->params, 'privacy_' );

				if ( ! $privacy->authorized( $myId ) ) {
					$authorized			=	false;
				}
			}

			$cache[$userId][$myId]		=	$authorized;
		}

		$access							=	$cache[$userId][$myId];

		if ( ! $access ) {
			if ( $fieldId ) {
				if ( ! in_array( $fieldId, $hideFields ) ) {
					$access				=	true;
				}
			} elseif ( $tabId ) {
				if ( ! in_array( $tabId, $hideTabs ) ) {
					$access				=	true;
				}
			}
		}

		return $access;
	}

	/**
	 * @param TabTable|int $tab
	 * @param UserTable    $user
	 * @return bool
	 */
	static public function checkTabDisplayAccess( $tab, $user = null )
	{
		if ( ! $user ) {
			$user								=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user								=	\CBuser::getInstance( $user, false )->getUserData();
		}

		if ( Application::Cms()->getClientId() || Application::MyUser()->isGlobalModerator() || ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) ) {
			return true;
		}

		static $cache							=	array();
		static $tabs							=	array();

		if ( is_integer( $tab ) ) {
			if ( ! isset( $tabs[$tab] ) ) {
				$loadedTab						=	new TabTable();

				$loadedTab->load( $tab );

				$tabs[$tab]						=	$loadedTab;
			}

			$tab								=	$tabs[$tab];
		}

		if ( ! $tab instanceof TabTable ) {
			return true;
		}

		$myId									=	Application::MyUser()->getUserId();
		$userId									=	$user->get( 'id', 0, GetterInterface::INT );
		$tabId									=	$tab->get( 'tabid', 0, GetterInterface::INT );

		if ( ! isset( $cache[$tabId][$userId][$myId] ) ) {
			$authorized							=	true;

			if ( ! $tab->params instanceof ParamsInterface ) {
				$tab->params					=	new Registry( $tab->params );
			}

			$display							=	$tab->params->get( 'cbprivacy_display', 0, GetterInterface::INT );

			if ( $display == 4 ) {
				if ( ! Application::User( $myId )->isGlobalModerator() ) {
					$authorized					=	false;
				}
			} elseif ( $display ) {
				$privacy						=	new Privacy( 'profile.tab.' . $tabId, $user );

				$privacy->parse( $tab->params, 'privacy_' );

				if ( ! $privacy->authorized( $myId, ( $display == 3 ? true : false ) ) ) {
					$authorized					=	false;
				}
			}

			if ( $authorized && ( ! self::checkProfileDisplayAccess( $user, $tabId ) ) ) {
				$authorized						=	false;
			}

			$cache[$tabId][$userId][$myId]		=	$authorized;
		}

		return $cache[$tabId][$userId][$myId];
	}

	/**
	 * @param TabTable|int $tab
	 * @return bool
	 */
	static public function checkTabEditAccess( $tab )
	{
		if ( Application::Cms()->getClientId() ) {
			return true;
		}

		static $cache				=	array();
		static $tabs				=	array();

		if ( is_integer( $tab ) ) {
			if ( ! isset( $tabs[$tab] ) ) {
				$loadedTab			=	new TabTable();

				$loadedTab->load( $tab );

				$tabs[$tab]			=	$loadedTab;
			}

			$tab					=	$tabs[$tab];
		}

		if ( ! $tab instanceof TabTable ) {
			return true;
		}

		$tabId						=	$tab->get( 'tabid', 0, GetterInterface::INT );
		$myId						=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$tabId][$myId] ) ) {
			$authorized				=	true;

			if ( ! $tab->params instanceof ParamsInterface ) {
				$tab->params		=	new Registry( $tab->params );
			}

			$display				=	$tab->params->get( 'cbprivacy_edit', 0, GetterInterface::INT );

			if ( ( $display == 1 )
				 || ( ( $display == 2 ) && ( ! Application::MyUser()->isGlobalModerator() ) )
				 || ( ( $display == 3 ) && ( ! Application::MyUser()->canViewAccessLevel( $tab->params->get( 'cbprivacy_edit_access', 1, GetterInterface::INT ) ) ) && ( ! Application::MyUser()->isGlobalModerator() ) )
				 || ( ( $display == 4 ) && ( ! in_array( $tab->params->get( 'cbprivacy_edit_group', 2, GetterInterface::INT ), Application::MyUser()->getAuthorisedGroups( true ) ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
				$authorized			=	false;
			}

			$cache[$tabId][$myId]	=	$authorized;
		}

		return $cache[$tabId][$myId];
	}

	/**
	 * @param FieldTable|int $field
	 * @param UserTable      $user
	 * @return bool
	 */
	static public function checkFieldDisplayAccess( $field, $user = null )
	{
		if ( ! $user ) {
			$user									=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user									=	\CBuser::getInstance( $user, false )->getUserData();
		}

		if ( Application::Cms()->getClientId() || Application::MyUser()->isGlobalModerator() || ( ! $field->get( 'profile', 1, GetterInterface::INT ) ) || ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) ) {
			return true;
		}

		static $cache								=	array();
		static $fields								=	array();

		if ( is_numeric( $field ) ) {
			if ( ! isset( $fields[$field] ) ) {
				$loadedField						=	new FieldTable();

				$loadedField->load( $field );

				$fields[$field]						=	$loadedField;
			}

			$field									=	$fields[$field];
		}

		if ( ! $field instanceof FieldTable ) {
			return true;
		}

		$fieldId									=	$field->get( 'fieldid', 0, GetterInterface::INT );
		$userId										=	$user->get( 'id', 0, GetterInterface::INT );
		$myId										=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$fieldId][$userId][$myId] ) ) {
			$tabId									=	$field->get( 'tabid', 0, GetterInterface::INT );
			$authorized								=	true;

			if ( ! $field->params instanceof ParamsInterface ) {
				$field->params						=	new Registry( $field->params );
			}

			$display								=	$field->params->get( 'cbprivacy_display', 0, GetterInterface::INT );

			if ( $display == 4 ) {
				if ( ! Application::User( $myId )->isGlobalModerator() ) {
					$authorized						=	false;
				}
			} elseif ( $display ) {
				$privacy							=	new Privacy( 'profile.field.' . $fieldId, $user );

				$privacy->parse( $field->params, 'privacy_' );

				if ( ! $privacy->authorized( $myId, ( $display == 3 ? true : false ) ) ) {
					$authorized						=	false;
				}
			}

			if ( $authorized && ( ! self::checkTabDisplayAccess( $tabId, $user ) ) ) {
				$authorized							=	false;
			}

			if ( $authorized && ( ! self::checkProfileDisplayAccess( $user, $tabId, $fieldId ) ) ) {
				$authorized							=	false;
			}

			$cache[$fieldId][$userId][$myId]		=	$authorized;
		}

		return $cache[$fieldId][$userId][$myId];
	}

	/**
	 * @param FieldTable|int $field
	 * @return bool
	 */
	static public function checkFieldEditAccess( $field )
	{
		if ( Application::MyUser()->isGlobalModerator() || Application::MyUser()->isGlobalModerator() ) {
			return true;
		}

		static $cache					=	array();
		static $fields					=	array();

		if ( is_numeric( $field ) ) {
			if ( ! isset( $fields[$field] ) ) {
				$loadedField			=	new FieldTable();

				$loadedField->load( $field );

				$fields[$field]			=	$loadedField;
			}

			$field						=	$fields[$field];
		}

		if ( ! $field instanceof FieldTable ) {
			return true;
		}

		$fieldId						=	$field->get( 'fieldid', 0, GetterInterface::INT );
		$myId							=	Application::MyUser()->getUserId();

		if ( ! isset( $cache[$fieldId][$myId] ) ) {
			$tabId						=	$field->get( 'tabid', 0, GetterInterface::INT );
			$authorized					=	true;

			if ( ! $field->params instanceof ParamsInterface ) {
				$field->params			=	new Registry( $field->params );
			}

			$display					=	$field->params->get( 'cbprivacy_edit', 0, GetterInterface::INT );

			if ( ( $display == 1 ) // This is for stored B/C (not edit display at all)
				 || ( ( $display == 2 ) && ( ! Application::MyUser()->isGlobalModerator() ) )
				 || ( ( $display == 3 ) && ( ! Application::MyUser()->canViewAccessLevel( $field->params->get( 'cbprivacy_edit_access', 1, GetterInterface::INT ) ) ) )
				 || ( ( $display == 4 ) && ( ! in_array( $field->params->get( 'cbprivacy_edit_group', 2, GetterInterface::INT ), Application::MyUser()->getAuthorisedGroups( true ) ) ) ) ) {
				$authorized				=	false;
			}

			if ( $authorized && ( ! self::checkTabEditAccess( $tabId ) ) ) {
				$authorized				=	false;
			}

			$cache[$fieldId][$myId]		=	$authorized;
		}

		return $cache[$fieldId][$myId];
	}
}
