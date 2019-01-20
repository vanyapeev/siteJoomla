<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Language\CBTxt;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Input\Get;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerFunction( 'onBeforefieldClass', 'getAjaxResponse', 'CBfield_ajaxfields' );
$_PLUGINS->registerFunction( 'onBeforegetFieldRow', 'getAjaxDisplay', 'CBfield_ajaxfields' );

class CBfield_ajaxfields extends cbFieldHandler
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbcorefieldsajax' );
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

		$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbcorefieldsajax' );

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
	 * Reloads and outputs the JS headers for ajax output
	 *
	 * @return string
	 */
	private function reloadHeaders()
	{
		global $_CB_framework;

		if ( Application::Input()->get( 'format', null, GetterInterface::STRING ) != 'raw' ) {
			return null;
		}

		$_CB_framework->getAllJsPageCodes();

		// Reset meta headers as they can't be used inline anyway:
		$_CB_framework->document->_head['metaTags']		=	array();

		// Remove all non-jQuery scripts as they'll likely just cause errors due to redeclaration:
		foreach( $_CB_framework->document->_head['scriptsUrl'] as $url => $script ) {
			if ( ( strpos( $url, 'jquery.' ) === false ) || ( strpos( $url, 'migrate' ) !== false ) ) {
				unset( $_CB_framework->document->_head['scriptsUrl'][$url] );
			}
		}

		$header				=	$_CB_framework->document->outputToHead();

		if ( ! $header ) {
			return null;
		}

		$return				=	'<div class="cbAjaxHeaders" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;">'
							.		'<script type="text/javascript">window.jQuery = cbjQuery; window.$ = cbjQuery;</script>'
							.		$header
							.	'</div>';

		return $return;
	}

	/**
	 * Checks if the user can ajax edit the supplied field
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $output
	 * @param string     $reason
	 * @param bool       $ignoreEmpty
	 * @return bool
	 */
	private function canAjaxEdit( &$field, &$user, $output, $reason, $ignoreEmpty = false )
	{
		$exclude			=	array( 'points', 'rating' );

		if ( Application::Cms()->getClientId()
			 || ( $output != 'html' )
			 || ( ! in_array( $reason, array( 'profile', 'list' ) ) )
			 || ( ! $field instanceof FieldTable )
			 || ( ! $user instanceof UserTable )
			 || ( ! $field->getTableColumns() )
			 || in_array( $field->get( 'type', null, GetterInterface::STRING ), $exclude )
			 || $field->get( '_noAjax', false, GetterInterface::BOOLEAN )
		) {
			return false;
		}

		if ( ! ( $field->params instanceof ParamsInterface ) ) {
			$params			=	new Registry( $field->params );
		} else {
			$params			=	$field->params;
		}

		$value				=	$user->get( $field->get( 'name', null, GetterInterface::STRING ) );
		$notEmpty			=	( ( ! ( ( $value === null ) || ( $value === '' ) ) ) || Application::Config()->get( 'showEmptyFields', 1, GetterInterface::INT ) || cbReplaceVars( CBTxt::T( $field->params->get( 'ajax_placeholder', null, GetterInterface::STRING ) ), $user ) || ( $field->get( 'type', null, GetterInterface::STRING ) == 'image' ) );
		$readOnly			=	$field->get( 'readonly', 0, GetterInterface::INT );

		if ( ( $field->get( 'name', null, GetterInterface::STRING ) == 'username' ) && ( ! Application::Config()->get( 'usernameedit', 1, GetterInterface::INT ) ) ) {
			$readOnly		=	true;
		}

		if ( ( ! $readOnly ) && ( $notEmpty || $ignoreEmpty ) && ( ! cbCheckIfUserCanPerformUserTask( $user->get( 'id', 0, GetterInterface::INT ), 'allowModeratorsUserEdit' ) ) ) {
			if ( ( $reason == 'profile' ) && $params->get( 'ajax_profile', false, GetterInterface::BOOLEAN ) && Application::MyUser()->canViewAccessLevel( $params->get( 'ajax_profile_access', 2, GetterInterface::INT ) ) ) {
				return true;
			} elseif ( ( $reason == 'list' ) && $params->get( 'ajax_list', false, GetterInterface::BOOLEAN ) && Application::MyUser()->canViewAccessLevel( $params->get( 'ajax_list_access', 2, GetterInterface::INT ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the user can ajax update the supplied field
	 *
	 * @param FieldTable $field
	 * @param string     $output
	 * @param string     $reason
	 * @return bool
	 */
	private function canAjaxUpdate( &$field, $output, $reason )
	{
		$exclude		=	array( 'points', 'rating' );

		if ( ( ! in_array( $reason, array( 'edit', 'register' ) ) )
			 || ( $output != 'htmledit' )
			 || ( ! $field instanceof FieldTable )
			 || in_array( $field->get( 'type', null, GetterInterface::STRING ), $exclude )
			 || $field->get( '_noAjax', false, GetterInterface::BOOLEAN )
		) {
			return false;
		}

		if ( ! ( $field->params instanceof ParamsInterface ) ) {
			$params		=	new Registry( $field->params );
		} else {
			$params		=	$field->params;
		}

		if ( ! $params->get( 'ajax_update', null, GetterInterface::STRING ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Direct access to field for custom operations, like for Ajax
	 *
	 * WARNING: direct unchecked access, except if $user is set, then check well for the $reason ...
	 *
	 * @param  FieldTable $field
	 * @param  UserTable  $user
	 * @param  array      $postdata
	 * @param  string     $reason 'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @return string
	 */
	public function getAjaxResponse( &$field, &$user, &$postdata, $reason )
	{
		global $_CB_framework, $_CB_database, $_PLUGINS;

		switch ( $this->input( 'function', null, GetterInterface::STRING ) ) {
			case 'ajax_edit':
				if ( ! $this->canAjaxEdit( $field, $user, 'html', $reason, true ) ) {
					return null;
				}

				$format									=	( $field->params->get( 'fieldVerifyInput', 0, GetterInterface::INT ) ? 'div' : 'none' );

				$field->set( '_noAjax', true );

				if ( $format != 'none' ) {
					$formatted							=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'htmledit', $format, 'edit', 0 ), $field );
				} else {
					$formatted							=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'htmledit', 'none', 'edit', 0 ), $field );
				}

				$field->set( '_noAjax', false );

				if ( trim( $formatted ) == '' ) {
					return ' ';
				}

				if ( Application::Cms()->getClientId() ) {
					/** @noinspection PhpUnusedLocalVariableInspection */
					$saveUrl							=	$_CB_framework->backendViewUrl( 'fieldclass', true, array( 'field' => $field->get( 'name', null, GetterInterface::STRING ), 'function' => 'ajax_save', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'reason' => $reason ), 'raw' );
				} else {
					/** @noinspection PhpUnusedLocalVariableInspection */
					$saveUrl							=	$_CB_framework->viewUrl( 'fieldclass', true, array( 'field' => $field->get( 'name', null, GetterInterface::STRING ), 'function' => 'ajax_save', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'reason' => $reason ), 'raw' );
				}

				ob_start();
				require self::getTemplate( $field->params->get( 'ajax_template', null, GetterInterface::STRING ), 'edit' );
				$html									=	ob_get_contents();
				ob_end_clean();

				return $html;
				break;
			case 'ajax_save':
				if ( ! $this->canAjaxEdit( $field, $user, 'html', $reason, true ) ) {
					return null;
				}

				$field->set( '_noAjax', true );

				if ( in_array( $field->get( 'name', null, GetterInterface::STRING ), array ( 'firstname', 'middlename', 'lastname' ) ) ) {
					if ( $field->get( 'name', null, GetterInterface::STRING ) != 'firstname' ) {
						$postdata['firstname']			=	$user->get( 'firstname', null, GetterInterface::STRING );
					}

					if ( $field->get( 'name', null, GetterInterface::STRING ) != 'middlename' ) {
						$postdata['middlename']			=	$user->get( 'middlename', null, GetterInterface::STRING );
					}

					if ( $field->get( 'name', null, GetterInterface::STRING ) != 'lastname' ) {
						$postdata['lastname']			=	$user->get( 'lastname', null, GetterInterface::STRING );
					}
				}

				$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'fieldClass', array( &$field, &$user, &$postdata, $reason ), $field );

				$oldUserComplete						=	clone $user;
				$orgValue								=	$user->get( $field->get( 'name', null, GetterInterface::STRING ) );

				$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'prepareFieldDataSave', array( &$field, &$user, &$postdata, $reason ), $field );

				$store									=	false;

				if ( ! count( $_PLUGINS->getErrorMSG( false ) ) ) {
					$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'commitFieldDataSave', array( &$field, &$user, &$postdata, $reason ), $field );

					if ( ! count( $_PLUGINS->getErrorMSG( false ) ) ) {
						if ( Application::MyUser()->getUserId() == $user->get( 'id', 0, GetterInterface::INT ) ) {
							$user->set( 'lastupdatedate', Application::Database()->getUtcDateTime() );
						}

						$_PLUGINS->trigger( 'onBeforeUserUpdate', array( &$user, &$user, &$oldUserComplete, &$oldUserComplete ) );

						$clearTextPassword				=	null;

						if ( $field->get( 'name', null, GetterInterface::STRING ) == 'password' ) {
							$clearTextPassword			=	$user->get( 'password', null, GetterInterface::STRING );

							$user->set( 'password', $user->hashAndSaltPassword( $clearTextPassword ) );
						}

						$store							=	$user->store();

						if ( $clearTextPassword ) {
							$user->set( 'password', $clearTextPassword );
						}

						$_PLUGINS->trigger( 'onAfterUserUpdate', array( &$user, &$user, $oldUserComplete ) );
					} else {
						$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'rollbackFieldDataSave', array( &$field, &$user, &$postdata, $reason ), $field );
						$_PLUGINS->trigger( 'onSaveUserError', array( &$user, $user->getError(), $reason ) );
					}
				}

				if ( ! $store ) {
					if ( $orgValue != $user->get( $field->get( 'name', null, GetterInterface::STRING ) ) ) {
						$user->set( $field->get( 'name', null, GetterInterface::STRING ), $orgValue );
					}
				}

				$cbUser									=	CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false );
				$placeholder							=	$cbUser->replaceUserVars( CBTxt::T( $field->params->get( 'ajax_placeholder', null, GetterInterface::STRING ) ) );
				$emptyValue								=	$cbUser->replaceUserVars( Application::Config()->get( 'emptyFieldsText', '-', GetterInterface::STRING ) );
				$return									=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'html', 'none', $reason, 0 ), $field );

				if ( ( ( trim( $return ) == '' ) || ( $return == $emptyValue ) ) && $placeholder ) {
					$return								=	$placeholder;
				} elseif ( ( trim( $return ) == '' ) && ( ! Application::Config()->get( 'showEmptyFields', 1, GetterInterface::INT ) ) ) {
					$return								=	$emptyValue;
				}

				$error									=	$this->getFieldAjaxError( $field, $user, $reason );
				$return									=	( $error ? '<div class="alert alert-danger">' . $error . '</div>' : null )
														.	$return
														.	$this->reloadHeaders();

				$field->set( '_noAjax', false );

				return $return;
				break;
			case 'ajax_update':
				if ( ! $this->canAjaxUpdate( $field, 'htmledit', $reason ) ) {
					return null;
				}

				$updateFields							=	$field->params->get( 'ajax_update', null, GetterInterface::STRING );

				if ( ! $updateFields ) {
					return null;
				}

				static $cache							=	array();

				if ( ! isset( $cache[$updateFields] ) ) {
					$query								=	"SELECT " . $_CB_database->NameQuote( 'name' )
														.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_fields' )
														.	"\n WHERE " . $_CB_database->NameQuote( 'fieldid' ) . " IN " . $_CB_database->safeArrayOfIntegers( explode( '|*|', $updateFields ) );
					$_CB_database->setQuery( $query );
					$cache[$updateFields]				=	$_CB_database->loadResultArray();
				}

				$allowedFields							=	$cache[$updateFields];

				if ( ! in_array( $field->get( 'name', null, GetterInterface::STRING ), $allowedFields ) ) {
					$allowedFields[]					=	$field->get( 'name', null, GetterInterface::STRING );
				}

				if ( ! ( $user instanceof UserTable ) ) {
					$user								=	new UserTable();
				}

				$tempUser								=	clone $user;

				foreach ( $postdata as $k => $v ) {
					if ( ( ! $k ) || ( ! in_array( $k, $allowedFields ) ) ) {
						continue;
					}

					if ( is_array( $v ) ) {
						$v								=	$this->_implodeCBvalues( $v );
					}

					$tempUser->set( $k, Get::clean( $v, GetterInterface::STRING ) );
				}

				$cbUser									=	CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false );

				$cbUser->_cbuser						=	$tempUser;

				$field->set( '_noAjax', true );
				$field->set( '_noCondition', true );
				$field->set( '_noPrivacy', true );

				$format									=	( $field->params->get( 'fieldVerifyInput', 0, GetterInterface::INT ) ? 'div' : 'none' );

				if ( $format != 'none' ) {
					$return								=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$tempUser, 'htmledit', $format, 'edit', 0 ), $field );
				} else {
					$return								=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$tempUser, 'htmledit', 'none', 'edit', 0 ), $field );
				}

				$cbUser->_cbuser						=	$user;

				$field->set( '_noAjax', false );
				$field->set( '_noCondition', false );
				$field->set( '_noPrivacy', false );

				if ( trim( $return ) == '' ) {
					return ' ';
				}

				return $return . $this->reloadHeaders();
				break;
		}

		return null;
	}

	/**
	 * Formatter:
	 * Returns a field in specified format
	 *
	 * @param  FieldTable  $field
	 * @param  UserTable   $user
	 * @param  string      $output               'html', 'xml', 'json', 'php', 'csvheader', 'csv', 'rss', 'fieldslist', 'htmledit'
	 * @param  string      $formatting           'tr', 'td', 'div', 'span', 'none',   'table'??
	 * @param  string      $reason               'profile' for user profile view, 'edit' for profile edit, 'register' for registration, 'search' for searches
	 * @param  int         $list_compare_types   IF reason == 'search' : 0 : simple 'is' search, 1 : advanced search with modes, 2 : simple 'any' search
	 * @return mixed
	 */
	public function getAjaxDisplay( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		global $_CB_framework, $_PLUGINS, $ueConfig;

		$canAjaxUpdate				=	$this->canAjaxUpdate( $field, $output, $reason );
		$canAjaxEdit				=	$this->canAjaxEdit( $field, $user, $output, $reason );

		if ( ( ! $canAjaxUpdate ) && ( ! $canAjaxEdit ) ) {
			return null;
		}

		$cbSpoofField				=	cbSpoofField();
		$cbSpoofString				=	cbSpoofString( null, 'fieldclass' );
		$regAntiSpamFieldName		=	cbGetRegAntiSpamFieldName();
		$regAntiSpamValues			=	cbGetRegAntiSpams();

		if ( $canAjaxUpdate ) {
			$updateOn				=	cbToArrayOfInt( explode( '|*|', $field->params->get( 'ajax_update', null, GetterInterface::STRING ) ) );
			$selectors				=	array();

			foreach ( $updateOn as $updateField ) {
				if ( ! $updateField ) {
					continue;
				}

				$selectors[]		=	'#cbfr_' . (int) $updateField . ',#cbfrd_' . (int) $updateField;
			}

			if ( $selectors ) {
				$_CB_framework->addJQueryPlugin( 'cbajaxfield', '/components/com_comprofiler/plugin/user/plug_cbcorefieldsajax/js/jquery.cbcorefieldsajax.js' );

				if ( Application::Cms()->getClientId() ) {
					$updateUrl		=	$_CB_framework->backendViewUrl( 'fieldclass', false, array( 'field' => $field->get( 'name', null, GetterInterface::STRING ), 'function' => 'ajax_update', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'reason' => $reason, $cbSpoofField => $cbSpoofString, $regAntiSpamFieldName => $regAntiSpamValues[0] ), 'raw' );
				} else {
					$updateUrl		=	$_CB_framework->viewUrl( 'fieldclass', false, array( 'field' => $field->get( 'name', null, GetterInterface::STRING ), 'function' => 'ajax_update', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'reason' => $reason, $cbSpoofField => $cbSpoofString, $regAntiSpamFieldName => $regAntiSpamValues[0] ), 'raw' );
				}

				$js					=	"$( '#cbfr_" . $field->get( 'fieldid', 0, GetterInterface::INT ) . ",#cbfrd_" . $field->get( 'fieldid', 0, GetterInterface::INT ) . "' ).cbajaxfield({"
									.		"mode: 'update',"
									.		"selectors: '" . addslashes( implode( ',', $selectors ) ) . "',"
									.		"url: '" . addslashes( $updateUrl ) . "'"
									.	"});";

				$_CB_framework->outputCbJQuery( $js, 'cbajaxfield' );
			}
		}

		if ( $canAjaxEdit ) {
			$field->set( '_noAjax', true );

			$hasEdit				=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'htmledit', 'none', 'edit', $list_compare_types ), $field );

			if ( trim( $hasEdit ) == '' ) {
				$field->set( '_noAjax', false );

				return null;
			}

			$placeholder			=	cbReplaceVars( CBTxt::T( $field->params->get( 'ajax_placeholder', null, GetterInterface::STRING ) ), $user );
			$formatted				=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, $output, 'none', $reason, $list_compare_types ), $field );

			if ( ( ( trim( $formatted ) == '' ) || ( $formatted == $ueConfig['emptyFieldsText'] ) ) && $placeholder ) {
				$formatted			=	$placeholder;
			}

			if ( trim( $formatted ) == '' ) {
				$field->set( '_noAjax', false );

				return null;
			}

			if ( Application::Cms()->getClientId() ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$editUrl			=	$_CB_framework->backendViewUrl( 'fieldclass', true, array( 'field' => $field->get( 'name', null, GetterInterface::STRING ), 'function' => 'ajax_edit', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'reason' => $reason, $cbSpoofField => $cbSpoofString, $regAntiSpamFieldName => $regAntiSpamValues[0] ), 'raw' );
			} else {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$editUrl			=	$_CB_framework->viewUrl( 'fieldclass', true, array( 'field' => $field->get( 'name', null, GetterInterface::STRING ), 'function' => 'ajax_edit', 'user' => $user->get( 'id', 0, GetterInterface::INT ), 'reason' => $reason, $cbSpoofField => $cbSpoofString, $regAntiSpamFieldName => $regAntiSpamValues[0] ), 'raw' );
			}

			ob_start();
			require self::getTemplate( $field->params->get( 'ajax_template', null, GetterInterface::STRING ), 'display' );
			$html					=	ob_get_contents();
			ob_end_clean();

			$return					=	$this->renderFieldHtml( $field, $user, $html, $output, $formatting, $reason, array() );

			$field->set( '_noAjax', false );

			return $return;
		}

		return null;
	}

	/**
	 * Parse field validation errors into user readable
	 *
	 * @param FieldTable $field
	 * @param UserTable  $user
	 * @param string     $reason
	 * @return mixed|null
	 */
	private function getFieldAjaxError( &$field, &$user, $reason )
	{
		global $_PLUGINS;

		$errors	=	$_PLUGINS->getErrorMSG( false );
		$title	=	cbFieldHandler::getFieldTitle( $field, $user, 'text', $reason );

		if ( $errors ) foreach ( $errors as $error ) {
			if ( stristr( $error, $title ) ) {
				return str_replace( $title . ' : ', '', $error );
			}
		}

		return null;
	}
}