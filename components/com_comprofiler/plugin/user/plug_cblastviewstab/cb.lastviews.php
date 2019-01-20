<?php
/**
 * Community Builder (TM)
 * @version $Id: $
 * @package CommunityBuilder
 * @author Trail, Nant (modified for CB 2.0)
 * @copyright (C)2005-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
 */

use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class getLastViewsTab extends cbTabHandler
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.lastviews' );
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

		$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.lastviews' );

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
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_database;

		if ( ! $tab->params instanceof ParamsInterface ) {
			$tab->params	=	new Registry( $tab->params );
		}

		/** @noinspection PhpUnusedLocalVariableInspection */
		$viewer				=	CBuser::getMyUserDataInstance();
		$exclude			=	$tab->params->get( 'display_exclude', '42', GetterInterface::STRING );
		$limit				=	$tab->params->get( 'display_limit', 15, GetterInterface::INT );

		if ( ! $limit ) {
			$limit			=	15;
		}

		$query				=	'SELECT a.*'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_views' ) . " AS a"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS b"
							.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'viewer_id' )
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS c"
							.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = b.' . $_CB_database->NameQuote( 'id' )
							.	"\n WHERE a." . $_CB_database->NameQuote( 'profile_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
							.	"\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " > 0"
							.	( $exclude ? "\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( explode( ',', $exclude ) ) : null )
							.	"\n AND b." . $_CB_database->NameQuote( 'approved' ) . " = 1"
							.	"\n AND b." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
							.	"\n AND c." . $_CB_database->NameQuote( 'block' ) . " = 0"
							.	"\n ORDER BY " . $_CB_database->NameQuote( 'lastview' ) . " DESC";
		$_CB_database->setQuery( $query, 0, $limit );
		$rows				=	$_CB_database->loadObjectList( null, '\CB\Database\Table\UserViewTable', array( $_CB_database ) );

		if ( $tab->params->get( 'display_total_views', true, GetterInterface::BOOLEAN ) ) {
			$query			=	'SELECT COUNT(*)'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_views' ) . " AS a"
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS b"
							.	' ON b.' . $_CB_database->NameQuote( 'id' ) . ' = a.' . $_CB_database->NameQuote( 'viewer_id' )
							.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS c"
							.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = b.' . $_CB_database->NameQuote( 'id' )
							.	"\n WHERE a." . $_CB_database->NameQuote( 'profile_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
							.	"\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " > 0"
							.	( $exclude ? "\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " NOT IN " . $_CB_database->safeArrayOfIntegers( explode( ',', $exclude ) ) : null )
							.	"\n AND b." . $_CB_database->NameQuote( 'approved' ) . " = 1"
							.	"\n AND b." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
							.	"\n AND c." . $_CB_database->NameQuote( 'block' ) . " = 0";
			$_CB_database->setQuery( $query );
			$viewsCount		=	(int) $_CB_database->loadResult();
		} else {
			$viewsCount		=	0;
		}

		if ( $tab->params->get( 'display_guest_views', true, GetterInterface::BOOLEAN ) ) {
			$query			=	'SELECT COUNT(*)'
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_views' ) . " AS a"
							.	"\n WHERE a." . $_CB_database->NameQuote( 'profile_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
							.	"\n AND a." . $_CB_database->NameQuote( 'viewer_id' ) . " = 0";
			$_CB_database->setQuery( $query );
			$guestCount		=	(int) $_CB_database->loadResult();
		} else {
			$guestCount		=	0;
		}

		if ( ( ! $rows ) && ( ! $viewsCount ) && ( ! $guestCount ) ) {
			return null;
		}

		ob_start();
		require self::getTemplate( null, 'tab' );
		$html				=	ob_get_contents();
		ob_end_clean();

		$class				=	$this->params->get( 'general_class', null );

		$return				=	'<div class="cbLastViews' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
							.		$html
							.	'</div>';

		return $return;
	}
}