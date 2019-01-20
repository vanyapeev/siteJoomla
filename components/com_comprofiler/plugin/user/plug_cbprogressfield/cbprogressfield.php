<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\FieldTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'progress' => 'cbprogressfieldField' ) );

class cbprogressfieldField extends cbFieldHandler
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprogressfield' );
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

		$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprogressfield' );

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
	public function getFieldRow( &$field, &$user, $output, $formatting, $reason, $list_compare_types )
	{
		if ( ( ! Application::Cms()->getClientId() ) && ( $output == 'html' ) ) {
			if ( $field->params->get( 'prg_hide', false, GetterInterface::BOOLEAN ) ) {
				if ( $this->getComplete( $user, $field, true ) == 100 ) {
					return null;
				}
			}

			if ( $field->params->get( 'prg_private', true, GetterInterface::BOOLEAN ) && ( $user->get( 'id', 0, GetterInterface::INT ) != Application::MyUser()->getUserId() ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
				return null;
			}
		}

		return parent::getFieldRow( $field, $user, $output, $formatting, $reason, $list_compare_types );
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
		if ( ! $user->get( 'id', 0, GetterInterface::INT ) ) {
			return null;
		}

		switch ( $output ) {
			case 'html':
			case 'rss':
			case 'htmledit':
				if ( $reason == 'search' ) {
					return	null;
				} else {
					return $this->formatFieldValueLayout( $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $this->getComplete( $user, $field ), $output, false ), $reason, $field, $user );
				}
				break;
			default:
				return $this->_formatFieldOutput( $field->get( 'name', null, GetterInterface::STRING ), $this->getComplete( $user, $field, true ), $output, false );
				break;
		}
	}

	/**
	 * @param  UserTable   $user
	 * @param  FieldTable  $field
	 * @param  bool        $raw
	 * @return mixed
	 */
	private function getComplete( $user, $field, $raw = false )
	{
		$cbFields				=	$field->params->get( 'prg_fields', null, GetterInterface::STRING );

		if ( ! $cbFields ) {
			return null;
		}

		$cbFields				=	explode( '|*|', $cbFields );

		cbArrayToInts( $cbFields );

		$userFields				=	$this->getUserFields( $user, $cbFields, $field );

		if ( ! $userFields ) {
			return null;
		}

		$complete				=	0;
		$worth					=	( 100 / count( $userFields ) );

		foreach ( $userFields as $userField ) {
			if ( $userField->complete ) {
				$complete		+=	$worth;
			}
		}

		if ( $complete ) {
			if ( $complete > 100 ) {
				$complete		=	100;
			} else {
				$complete		=	round( $complete, 0 );
			}
		}

		if ( $raw ) {
			return $complete;
		}

		ob_start();
		require self::getTemplate( $field->params->get( 'prg_template', null, GetterInterface::STRING ), 'progress' );
		$html					=	ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * @param  UserTable  $user
	 * @param  int[]      $cbFields
	 * @param  FieldTable $cbField
	 * @return array
	 */
	private function getUserFields( $user, $cbFields, $cbField )
	{
		global $_PLUGINS;

		static $cache								=	array();
		/** @var FieldTable[] $fields */
		static $fields								=	array();

		$userId										=	$user->get( 'id', 0, GetterInterface::INT );
		$progress									=	array();

		foreach ( $cbFields as $cbFieldId ) {
			if ( $cbFieldId == $cbField->get( 'fieldid', 0, GetterInterface::INT ) ) {
				continue;
			}

			if ( ! isset( $cache[$cbFieldId][$userId] ) ) {
				if ( ! isset( $fields[$cbFieldId] ) ) {
					$loadField						=	new FieldTable();

					$loadField->load( (int) $cbFieldId );

					if ( ! ( $loadField->params instanceof ParamsInterface ) ) {
						$loadField->params			=	new Registry( $loadField->params );
					}

					$fields[$cbFieldId]				=	$loadField;
				}

				$field								=	$fields[$cbFieldId];

				if ( ( $field->tablecolumns != '' ) && ( ! trim( $_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'htmledit', 'none', 'edit', 0 ), $field ) ) ) ) {
					continue;
				}

				$fieldValue							=	$user->get( $field->get( 'name', null, GetterInterface::STRING ) );

				if ( is_array( $fieldValue ) ) {
					$fieldValue						=	implode( '|*|', $fieldValue );
				}

				if ( ( $fieldValue === null ) && ( ! $field->get( 'tablecolumns', null, GetterInterface::STRING ) ) ) {
					$fieldValue						=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldRow', array( &$field, &$user, 'php', 'none', 'profile', 0 ), $field );

					if ( is_array( $fieldValue ) ) {
						$fieldValue					=	array_shift( $fieldValue );

						if ( is_array( $fieldValue ) ) {
							$fieldValue				=	implode( '|*|', $fieldValue );
						}
					}
				}

				if ( ( $fieldValue == '0000-00-00 00:00:00' ) || ( $fieldValue == '0000-00-00' ) ) {
					$fieldValue						=	null;
				}

				$progressField						=	new stdClass();
				$progressField->id					=	$field->get( 'fieldid', 0, GetterInterface::INT );
				$progressField->title				=	$_PLUGINS->callField( $field->get( 'type', null, GetterInterface::STRING ), 'getFieldTitle', array( &$field, &$user, 'html', 'profile' ), $field );

				if ( ! $progressField->title ) {
					$progressField->title			=	$field->get( 'name', null, GetterInterface::STRING );
				}

				$progressField->value				=	$fieldValue;

				switch ( $field->get( 'type', null, GetterInterface::STRING ) ) {
					case 'checkbox':
						$progressField->complete	=	( (int) $fieldValue === 1 ? true : false );
						break;
					case 'progress':
						$progressField->complete	=	( (int) $fieldValue === 100 ? true : false );
						break;
					default:
						$progressField->complete	=	( $fieldValue != '' ? true : false );
						break;
				}

				$cache[$cbFieldId][$userId]			=	$progressField;
			}

			$progress[]								=	$cache[$cbFieldId][$userId];
		}

		return $progress;
	}
}