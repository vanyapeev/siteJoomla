<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileUpdateLogger;

use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class CBProfileUpdateLogger
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.pulog' );
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

		$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.pulog' );

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
}
