<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam;

use CB\Plugin\AntiSpam\Table\AttemptTable;
use CB\Plugin\AntiSpam\Table\LogTable;
use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\AntiSpam\Table\BlockTable;
use CB\Plugin\AntiSpam\Table\WhitelistTable;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class CBAntiSpam
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$params		=	new Registry();

			if ( $plugin ) {
				$params->load( $plugin->params );
			}
		}

		return $params;
	}

	/**
	 * @param null|array $files
	 * @param bool       $loadGlobal
	 * @param bool       $loadHeader
	 * @param bool       $loadPHP
	 */
	static public function getTemplate( $files = null, $loadGlobal = true, $loadHeader = true, $loadPHP = true )
	{
		global $_CB_framework, $_PLUGINS;

		static $tmpl							=	array();

		if ( ! $files ) {
			$files								=	array();
		} elseif ( ! is_array( $files ) ) {
			$files								=	array( $files );
		}

		$id										=	md5( serialize( array( $files, $loadGlobal, $loadHeader, $loadPHP ) ) );

		if ( ! isset( $tmpl[$id] ) ) {
			static $plugin						=	null;
			static $params						=	null;

			if ( ! $plugin ) {
				$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );

				if ( ! $plugin ) {
					return;
				}

				$params							=	self::getGlobalParams();
			}

			$livePath							=	$_PLUGINS->getPluginLivePath( $plugin );
			$absPath							=	$_PLUGINS->getPluginPath( $plugin );

			$template							=	$params->get( 'general_template', 'default', GetterInterface::STRING );
			$paths								=	array( 'global_css' => null, 'php' => null, 'css' => null, 'js' => null, 'override_css' => null );

			foreach ( $files as $file ) {
				$file							=	preg_replace( '/[^-a-zA-Z0-9_]/', '', $file );
				$globalCss						=	'/templates/' . $template . '/template.css';
				$overrideCss					=	'/templates/' . $template . '/override.css';

				if ( $file ) {
					$php						=	$absPath . '/templates/' . $template . '/' . $file . '.php';
					$css						=	'/templates/' . $template . '/' . $file . '.css';
					$js							=	'/templates/' . $template . '/' . $file . '.js';
				} else {
					$php						=	null;
					$css						=	null;
					$js							=	null;
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( ! file_exists( $absPath . $globalCss ) ) {
						$globalCss				=	'/templates/default/template.css';
					}

					if ( file_exists( $absPath . $globalCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $globalCss );

						$paths['global_css']	=	$livePath . $globalCss;
					}
				}

				if ( $file ) {
					if ( $loadPHP ) {
						if ( ! file_exists( $php ) ) {
							$php				=	$absPath . '/templates/default/' . $file . '.php';
						}

						if ( file_exists( $php ) ) {
							require_once( $php );

							$paths['php']		=	$php;
						}
					}

					if ( $loadHeader ) {
						if ( ! file_exists( $absPath . $css ) ) {
							$css				=	'/templates/default/' . $file . '.css';
						}

						if ( file_exists( $absPath . $css ) ) {
							$_CB_framework->document->addHeadStyleSheet( $livePath . $css );

							$paths['css']		=	$livePath . $css;
						}

						if ( ! file_exists( $absPath . $js ) ) {
							$js					=	'/templates/default/' . $file . '.js';
						}

						if ( file_exists( $absPath . $js ) ) {
							$_CB_framework->document->addHeadScriptUrl( $livePath . $js );

							$paths['js']		=	$livePath . $js;
						}
					}
				}

				if ( $loadGlobal && $loadHeader ) {
					if ( file_exists( $absPath . $overrideCss ) ) {
						$_CB_framework->document->addHeadStyleSheet( $livePath . $overrideCss );

						$paths['override_css']	=	$livePath . $overrideCss;
					}
				}
			}

			$tmpl[$id]							=	$paths;
		}
	}

	/**
	 * Returns the current return url or generates one from current page
	 *
	 * @param bool|false $current
	 * @param bool|false $raw
	 * @return null|string
	 */
	static public function getReturn( $current = false, $raw = false )
	{
		static $cache				=	array();

		if ( ! isset( $cache[$current] ) ) {
			$url					=	null;

			if ( $current ) {
				$returnUrl			=	Application::Input()->get( 'get/return', '', GetterInterface::BASE64 );

				if ( $returnUrl ) {
					$returnUrl		=	base64_decode( $returnUrl );

					if ( \JUri::isInternal( $returnUrl ) || ( $returnUrl[0] == '/' ) ) {
						$url		=	$returnUrl;
					}
				}
			} else {
				$isHttps			=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
				$returnUrl			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

				if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
					$returnUrl		.=	$_SERVER['REQUEST_URI'];
				} else {
					$returnUrl		.=	$_SERVER['SCRIPT_NAME'];

					if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
						$returnUrl	.=	'?' . $_SERVER['QUERY_STRING'];
					}
				}

				$url				=	cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $returnUrl ) ) ) ) );
			}

			$cache[$current]		=	$url;
		}

		$return						=	$cache[$current];

		if ( ( ! $raw ) && $return ) {
			$return					=	base64_encode( $return );
		}

		return $return;
	}

	/**
	 * Redirects to the return url if available otherwise to the url specified
	 *
	 * @param string      $url
	 * @param null|string $message
	 * @param string      $messageType
	 */
	static public function returnRedirect( $url, $message = null, $messageType = 'message' )
	{
		$returnUrl		=	self::getReturn( true, true );

		cbRedirect( ( $returnUrl ? $returnUrl : $url ), $message, $messageType );
	}

	/**
	 * Returns the domain portion of an email address
	 *
	 * @param string|UserTable $email
	 * @return string
	 */
	static public function getEmailDomain( $email )
	{
		if ( $email instanceof UserTable ) {
			$email			=	$email->get( 'email', null, GetterInterface::STRING );
		}

		$emailParts			=	explode( '@', $email );
		$emailDomain		=	null;

		if ( count( $emailParts ) > 1 ) {
			$emailDomain	=	array_pop( $emailParts );
		}

		return $emailDomain;
	}

	/**
	 * Returns the viewers current ip address
	 *
	 * @return string
	 */
	static public function getCurrentIP()
	{
		$ipAddresses	=	cbGetIParray();

		return trim( array_shift( $ipAddresses ) );
	}

	/**
	 * Returns a users current ip address
	 *
	 * @param int|string|UserTable $user
	 * @return null|string
	 */
	static public function getUserIP( $user = null )
	{
		global $_CB_database;

		static $cache			=	array();

		if ( $user === null ) {
			$user				=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user				=	\CBuser::getUserDataInstance( (int) $user );
		}

		$userId					=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ! $userId ) {
			return self::getCurrentIP();
		}

		if ( ! isset( $cache[$userId] ) ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
			$_CB_database->setQuery( $query, 0, 1 );
			$log				=	new LogTable();
			$_CB_database->loadObject( $log );

			if ( ! $log->get( 'id', 0, GetterInterface::INT ) ) {
				$ipAddress		=	$user->get( 'registeripaddr', null, GetterInterface::STRING );
			} else {
				$ipAddress		=	$log->get( 'ip_address', null, GetterInterface::STRING );
			}

			if ( ! $ipAddress ) {
				$ipAddress		=	self::getCurrentIP();
			}

			$cache[$userId]		=	$ipAddress;
		}

		return $cache[$userId];
	}

	/**
	 * Returns the users sessions count
	 *
	 * @param int $userId
	 * @return mixed
	 */
	static public function countUserSessions( $userId )
	{
		global $_CB_database;

		if ( ! $userId ) {
			return 0;
		}

		static $cache			=	array();

		if ( ! isset( $cache[$userId] ) ) {
			$query				=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__session' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'userid' ) . " = " . (int) $userId;
			$_CB_database->setQuery( $query );
			$cache[$userId]		=	$_CB_database->loadResult();
		}

		return $cache[$userId];
	}

	/**
	 * Checks if the type and value have an active block
	 *
	 * @param string $type
	 * @param string $value
	 * @return bool
	 */
	static public function checkBlocked( $type, $value )
	{
		global $_CB_database;

		if ( ( ! self::getGlobalParams()->get( 'general_block', true, GetterInterface::BOOLEAN ) ) || ( ! $value ) ) {
			return false;
		}

		static $cache				=	array();

		if ( ! isset( $cache[$type][$value] ) ) {
			$blocked				=	false;

			$query					=	"SELECT *"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type );
			if ( $type == 'ip_range' ) {
				$query				.=	"\n AND ( INET_ATON( " . $_CB_database->Quote( $value ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) )";
			} elseif ( $type != 'user' ) {
				$query				.=	"\n AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
									.	" AND " . $_CB_database->Quote( $value ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
									.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $value ) . " )";
			} else {
				$query				.=	"\n AND " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $value );
			}
			$_CB_database->setQuery( $query );
			$blocks					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\BlockTable', array( $_CB_database ) );

			/** @var BlockTable[] $blocks */
			foreach ( $blocks as $block ) {
				if ( $block->blocked() ) {
					$blocked		=	true;
					break;
				}
			}

			$cache[$type][$value]	=	$blocked;
		}

		return $cache[$type][$value];
	}

	/**
	 * Checks if the type and value have been whitelisted
	 *
	 * @param string $type
	 * @param string $value
	 * @return bool
	 */
	static public function checkWhitelisted( $type, $value )
	{
		global $_CB_database;

		if ( ( ! self::getGlobalParams()->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) ) || ( ! $value ) ) {
			return false;
		}

		static $cache				=	array();

		if ( ! isset( $cache[$type][$value] ) ) {
			$query					=	"SELECT COUNT(*)"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type );
			if ( $type == 'ip_range' ) {
				$query				.=	"\n AND ( INET_ATON( " . $_CB_database->Quote( $value ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) )";
			} elseif ( $type != 'user' ) {
				$query				.=	"\n AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
									.	" AND " . $_CB_database->Quote( $value ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
									.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $value ) . " )";
			} else {
				$query				.=	"\n AND " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $value );
			}
			$_CB_database->setQuery( $query );

			$cache[$type][$value]	=	( $_CB_database->loadResult() ? true : false );
		}

		return $cache[$type][$value];
	}

	/**
	 * Checks if user or ip address can be blocked
	 *
	 * @param int|string|UserTable $user
	 * @param null|string          $ipAddress
	 * @return bool
	 */
	static public function checkBlockable( $user = null, $ipAddress = null )
	{
		$params				=	self::getGlobalParams();

		if ( ! $params->get( 'general_block', true, GetterInterface::BOOLEAN ) ) {
			return false;
		}

		if ( $user === null ) {
			$user			=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user			=	\CBuser::getUserDataInstance( (int) $user );
		}

		if ( Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
			return false;
		}

		if ( ! $params->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) ) {
			return true;
		}

		if ( $ipAddress === null ) {
			$ipAddress		=	self::getUserIP( $user );
		}

		return ( self::getWhitelists( $user, $ipAddress ) ? false : true );
	}

	/**
	 * Returns array of user or ip address blocks
	 *
	 * @param int|string|UserTable $user
	 * @param null|string          $ipAddress
	 * @return BlockTable[]
	 */
	static public function getBlocks( $user = null, $ipAddress = null )
	{
		global $_CB_database;

		if ( ! self::getGlobalParams()->get( 'general_block', true, GetterInterface::BOOLEAN ) ) {
			return array();
		}

		static $cache								=	array();

		if ( $user === null ) {
			$user									=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user									=	\CBuser::getUserDataInstance( (int) $user );
		}

		$userId										=	$user->get( 'id', 0, GetterInterface::INT );
		$email										=	$user->get( 'email', null, GetterInterface::STRING );

		if ( $ipAddress === null ) {
			$ipAddress								=	self::getUserIP( $user );
		}

		if ( ! isset( $cache[$userId][$email][$ipAddress] ) ) {
			$where									=	array();

			if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
													.	" AND " . $_CB_database->NameQuote( 'value' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )";
			}

			if ( $user->get( 'email', null, GetterInterface::STRING ) ) {
				$emailDomain						=	self::getEmailDomain( $user );

				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'email' )
													.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
													.	" AND " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
													.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " ) )";

				if ( $emailDomain ) {
					$where[]						.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'domain' )
													.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
													.	" AND " . $_CB_database->Quote( $emailDomain ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
													.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $emailDomain ) . " ) )";
				}
			}

			if ( $ipAddress ) {
				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
													.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
													.	" AND " . $_CB_database->Quote( $ipAddress ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
													.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $ipAddress ) . " ) )";

				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip_range' )
													.	" AND ( INET_ATON( " . $_CB_database->Quote( $ipAddress ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) ) )";
			}

			$blocks									=	array();

			if ( $where ) {
				$query								=	"SELECT *"
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' )
													.	"\n WHERE " . implode( " OR ", $where );
				$_CB_database->setQuery( $query );
				$blocks								=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\BlockTable', array( $_CB_database ) );
			}

			$cache[$userId][$email][$ipAddress]		=	$blocks;
		}

		return $cache[$userId][$email][$ipAddress];
	}

	/**
	 * Returns the user or ip addresses most recent block
	 *
	 * @param null|int|UserTable $user
	 * @param null|string        $ipAddress
	 * @return BlockTable|null
	 */
	static public function getBlock( $user = null, $ipAddress = null )
	{
		if ( ! self::getGlobalParams()->get( 'general_block', true, GetterInterface::BOOLEAN ) ) {
			return null;
		}

		static $cache								=	array();

		if ( $user === null ) {
			$user									=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user									=	\CBuser::getUserDataInstance( (int) $user );
		}

		$userId										=	$user->get( 'id', 0, GetterInterface::INT );
		$email										=	$user->get( 'email', null, GetterInterface::STRING );

		if ( $ipAddress === null ) {
			$ipAddress								=	self::getUserIP( $user );
		}

		if ( ! isset( $cache[$userId][$email][$ipAddress] ) ) {
			$blocked								=	null;

			if ( self::checkBlockable( $user, $ipAddress ) ) {
				$blocks								=	self::getBlocks( $user, $ipAddress );

				foreach ( $blocks as $block ) {
					if ( $block->blocked() ) {
						$blocked					=	$block;
						break;
					}
				}
			}

			$cache[$userId][$email][$ipAddress]		=	$blocked;
		}

		return $cache[$userId][$email][$ipAddress];
	}

	/**
	 * Returns array of user or ip address whitelists
	 *
	 * @param int|string|UserTable $user
	 * @param null|string          $ipAddress
	 * @return WhitelistTable[]
	 */
	static public function getWhitelists( $user = null, $ipAddress = null )
	{
		global $_CB_database;

		if ( ! self::getGlobalParams()->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) ) {
			return array();
		}

		static $cache								=	array();

		if ( $user === null ) {
			$user									=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user									=	\CBuser::getUserDataInstance( (int) $user );
		}

		$userId										=	$user->get( 'id', 0, GetterInterface::INT );
		$email										=	$user->get( 'email', null, GetterInterface::STRING );

		if ( $ipAddress === null ) {
			$ipAddress								=	self::getUserIP( $user );
		}

		if ( ! isset( $cache[$userId][$email][$ipAddress] ) ) {
			$where									=	array();

			if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'user' )
													.	" AND " . $_CB_database->NameQuote( 'value' ) . " = " . $user->get( 'id', 0, GetterInterface::INT ) . " )";
			}

			if ( $user->get( 'email', null, GetterInterface::STRING ) ) {
				$emailDomain						=	self::getEmailDomain( $user );

				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'email' )
													.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
													.	" AND " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
													.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $user->get( 'email', null, GetterInterface::STRING ) ) . " ) )";

				if ( $emailDomain ) {
					$where[]						.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'domain' )
													.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
													.	" AND " . $_CB_database->Quote( $emailDomain ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
													.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $emailDomain ) . " ) )";
				}
			}

			if ( $ipAddress ) {
				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip' )
													.	" AND ( ( " . $_CB_database->NameQuote( 'value' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( '%', true ) . '%' )
													.	" AND " . $_CB_database->Quote( $ipAddress ) . " LIKE " . $_CB_database->NameQuote( 'value' ) . " )"
													.	" OR " . $_CB_database->NameQuote( 'value' ) . " = " . $_CB_database->Quote( $ipAddress ) . " ) )";

				$where[]							.=	"( " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'ip_range' )
													.	" AND ( INET_ATON( " . $_CB_database->Quote( $ipAddress ) . " ) BETWEEN INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', 1 ) ) AND INET_ATON( SUBSTRING_INDEX( " . $_CB_database->NameQuote( 'value' ) . ", ':', -1 ) ) ) )";
			}

			$whitelists								=	array();

			if ( $where ) {
				$query								=	"SELECT *"
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_whitelist' )
													.	"\n WHERE " . implode( " OR ", $where );
				$_CB_database->setQuery( $query );
				$whitelists							=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\WhitelistTable', array( $_CB_database ) );
			}

			$cache[$userId][$email][$ipAddress]		=	$whitelists;
		}

		return $cache[$userId][$email][$ipAddress];
	}

	/**
	 * Returns the user or ip addresses most recent whitelist
	 *
	 * @param null|int|UserTable $user
	 * @param null|string        $ipAddress
	 * @return WhitelistTable|null
	 */
	static public function getWhitelist( $user = null, $ipAddress = null )
	{
		if ( ! self::getGlobalParams()->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) ) {
			return null;
		}

		static $cache								=	array();

		if ( $user === null ) {
			$user									=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user									=	\CBuser::getUserDataInstance( (int) $user );
		}

		$userId										=	$user->get( 'id', 0, GetterInterface::INT );
		$email										=	$user->get( 'email', null, GetterInterface::STRING );

		if ( $ipAddress === null ) {
			$ipAddress								=	self::getUserIP( $user );
		}

		if ( ! isset( $cache[$userId][$email][$ipAddress] ) ) {
			$whitelists								=	self::getWhitelists( $user, $ipAddress );
			$whitelisted							=	null;

			foreach ( $whitelists as $whitelist ) {
				$whitelisted						=	$whitelist;
				break;
			}

			$cache[$userId][$email][$ipAddress]		=	$whitelisted;
		}

		return $cache[$userId][$email][$ipAddress];
	}

	/**
	 * Logs an ip address use
	 *
	 * @param null|int|UserTable $user
	 * @param string             $type
	 */
	static public function logIPAddress( $user = null, $type = 'login' )
	{
		global $_CB_database;

		$params					=	self::getGlobalParams();

		if ( ( ! $params->get( 'general_attempts', true, GetterInterface::BOOLEAN ) ) && ( ! $params->get( 'general_log', true, GetterInterface::BOOLEAN ) ) ) {
			return;
		}

		$ipAddress				=	self::getCurrentIP();

		if ( ! $ipAddress ) {
			return;
		}

		if ( $params->get( 'general_attempts', true, GetterInterface::BOOLEAN ) ) {
			$query				=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
			if ( $type ) {
				$query			.=	"\n AND " . $_CB_database->NameQuote( 'type' ) . ( is_array( $type ) ? " IN " . $_CB_database->safeArrayOfStrings( $type ) : " = " . $_CB_database->Quote( $type ) );
			}
			$query				.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
			$_CB_database->setQuery( $query );
			$attempts			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AntiSpam\Table\AttemptTable', array( $_CB_database ) );

			/** @var AttemptTable[] $attempts */
			foreach ( $attempts as $attempt ) {
				$attempt->delete();
			}
		}

		if ( ! $params->get( 'general_log', true, GetterInterface::BOOLEAN ) ) {
			return;
		}

		if ( $user === null ) {
			$user				=	\CBuser::getMyUserDataInstance();
		} elseif ( ! ( $user instanceof UserTable ) ) {
			$user				=	\CBuser::getUserDataInstance( (int) $user );
		}

		if ( ! $user->get( 'id', 0, GetterInterface::INT ) ) {
			return;
		}

		$query					=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
								.	"\n AND " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress )
								.	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
		$_CB_database->setQuery( $query, 0, 1 );
		$row					=	new LogTable();
		$_CB_database->loadObject( $row );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			$row->set( 'user_id', $user->get( 'id', 0, GetterInterface::INT ) );
			$row->set( 'ip_address', $ipAddress );
			$row->set( 'count', 1 );
		} else {
			$row->set( 'count', ( $row->get( 'count', 0, GetterInterface::INT ) + 1 ) );
		}

		$row->set( 'date', Application::Database()->getUtcDateTime() );

		$row->store();
	}

	/**
	 * Logs an attempt
	 *
	 * @param string   $type
	 * @return bool|BlockTable
	 */
	static public function logAttempt( $type = 'login' )
	{
		global $_CB_database;

		$params					=	self::getGlobalParams();

		if ( ! $params->get( 'general_attempts', true, GetterInterface::BOOLEAN ) ) {
			return false;
		}

		$ipAddress				=	self::getCurrentIP();

		if ( ! $ipAddress ) {
			return false;
		}

		switch ( $type ) {
			case 'login':
				$timeframe		=	$params->get( 'login_autoblock_timeframe', '-1 MONTH', GetterInterface::STRING );
				break;
			case 'forgot':
				$timeframe		=	$params->get( 'forgot_autoblock_timeframe', '-1 WEEK', GetterInterface::STRING );
				break;
			case 'reg':
				$timeframe		=	$params->get( 'reg_autoblock_timeframe', '-1 MONTH', GetterInterface::STRING );
				break;
			case 'captcha':
				$timeframe		=	$params->get( 'captcha_autoblock_timeframe', '-1 DAY', GetterInterface::STRING );
				break;
			default:
				$timeframe		=	null;
				break;
		}

		$query					=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'ip_address' ) . " = " . $_CB_database->Quote( $ipAddress );
		if ( $type ) {
			$query				.=	"\n AND " . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( $type );
		}
		$query					.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . " DESC";
		$_CB_database->setQuery( $query, 0, 1 );
		$attempt				=	new AttemptTable();
		$_CB_database->loadObject( $attempt );

		if ( ! $attempt->get( 'id', 0, GetterInterface::INT ) ) {
			$attempt->set( 'ip_address', $ipAddress );
			$attempt->set( 'type', $type );
			$attempt->set( 'count', 1 );
		} elseif ( ( ! $timeframe ) || ( Application::Date( $attempt->get( 'date', null, GetterInterface::STRING ), 'UTC' )->getTimestamp() >= Application::Date( 'now', 'UTC' )->modify( strtoupper( $timeframe ) )->getTimestamp() ) ) {
			// We are counting all failed attempts over a lifetime or the failed attempt is within the specified timeframe (e.g. new failed attempt is within 1 month of the previous):
			$attempt->set( 'count', ( $attempt->get( 'count', 0, GetterInterface::INT ) + 1 ) );
		} else {
			// We're outside the timeframe limit so reset the attempt count:
			$attempt->set( 'count', 1 );
		}

		$attempt->set( 'date', Application::Database()->getUtcDateTime() );

		$attempt->store();

		if ( ! self::checkBlockable( null, $ipAddress ) ) {
			return false;
		}

		$block					=	false;
		$duration				=	'+1 HOUR';
		$reason					=	null;
		$method					=	0;

		switch ( $type ) {
			case 'login':
				$duration		=	$params->get( 'login_autoblock_dur', '+1 HOUR', GetterInterface::STRING  );
				$reason			=	$params->get( 'login_autoblock_reason', 'Too many failed login attempts.', GetterInterface::STRING  );
				$method			=	$params->get( 'login_autoblock_method', 0, GetterInterface::INT );

				if ( $params->get( 'login_autoblock', false, GetterInterface::BOOLEAN ) ) {
					$count		=	$params->get( 'login_autoblock_count', 5, GetterInterface::INT );

					if ( ! $count ) {
						$count	=	5;
					}

					if ( $attempt->get( 'count', 0, GetterInterface::INT ) >= $count ) {
						$block	=	true;
					}
				}
				break;
			case 'forgot':
				$duration		=	$params->get( 'forgot_autoblock_dur', '+1 HOUR', GetterInterface::STRING  );
				$reason			=	$params->get( 'forgot_autoblock_reason', 'Too many forgot login attempts.', GetterInterface::STRING  );
				$method			=	$params->get( 'forgot_autoblock_method', 0, GetterInterface::INT );

				if ( $params->get( 'forgot_autoblock', false, GetterInterface::BOOLEAN ) ) {
					$count		=	$params->get( 'forgot_autoblock_count', 5, GetterInterface::INT );

					if ( ! $count ) {
						$count	=	5;
					}

					if ( $attempt->get( 'count', 0, GetterInterface::INT ) >= $count ) {
						$block	=	true;
					}
				}
				break;
			case 'reg':
				$duration		=	$params->get( 'reg_autoblock_dur', '+1 HOUR', GetterInterface::STRING  );
				$reason			=	$params->get( 'reg_autoblock_reason', 'Too many failed registration attempts.', GetterInterface::STRING  );
				$method			=	$params->get( 'reg_autoblock_method', 0, GetterInterface::INT );

				if ( $params->get( 'reg_autoblock', false, GetterInterface::BOOLEAN  ) ) {
					$count		=	$params->get( 'reg_autoblock_count', 5, GetterInterface::INT );

					if ( ! $count ) {
						$count	=	5;
					}

					if ( $attempt->get( 'count', 0, GetterInterface::INT ) >= $count ) {
						$block	=	true;
					}
				}
				break;
			case 'captcha':
				$duration		=	$params->get( 'captcha_autoblock_dur', '+1 HOUR', GetterInterface::STRING  );
				$reason			=	$params->get( 'captcha_autoblock_reason', 'Too many failed captcha attempts.', GetterInterface::STRING  );
				$method			=	$params->get( 'captcha_autoblock_method', 0, GetterInterface::INT );

				if ( $params->get( 'captcha_autoblock', false, GetterInterface::BOOLEAN  ) ) {
					$count		=	$params->get( 'captcha_autoblock_count', 20, GetterInterface::INT );

					if ( ! $count ) {
						$count	=	20;
					}

					if ( $attempt->get( 'count', 0, GetterInterface::INT ) >= $count ) {
						$block	=	true;
					}
				}
				break;
		}

		if ( ! $block ) {
			return false;
		}

		$row					=	new BlockTable();

		$row->set( 'type', 'ip' );
		$row->set( 'value', $ipAddress );
		$row->set( 'date', Application::Database()->getUtcDateTime() );
		$row->set( 'duration', $duration );
		$row->set( 'reason', $reason );

		if ( $method ) {
			$row->store();
		}

		return $row;
	}

	/**
	 * Returns a preview of captcha output
	 *
	 * @return null|string
	 */
	public function previewCaptcha()
	{
		$captcha	=	new Captcha( 'preview' );

		$captcha->set( 'internal_ajax', false );

		return $captcha->captcha();
	}

	/**
	 * Returns internal clean up urls
	 *
	 * @param string $name
	 * @return string
	 */
	public function loadCleanUpURL( $name )
	{
		global $_CB_framework;

		switch( $name ) {
			case 'cleanup_attempts':
				$function		=	'attempts';
				break;
			case 'cleanup_log':
				$function		=	'log';
				break;
			case 'cleanup_block':
				$function		=	'block';
				break;
			case 'cleanup_all':
			default:
				$function		=	'all';
				break;
		}

		return '<a href="' . $_CB_framework->pluginClassUrl( 'cbantispam', true, array( 'action' => 'prune', 'func' => $function, 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '" target="_blank">' . CBTxt::T( 'Click to Process' ) . '</a>';
	}

	/**
	 * Generate and output a simple math equation
	 *
	 * @param int $value
	 * @param int $leftMin
	 * @param int $leftMax
	 * @param int $rightMin
	 * @param int $rightMax
	 * @return string
	 */
	static public function getMathEquation( &$value = 0, $leftMin = 1, $leftMax = 10, $rightMin = 1, $rightMax = 10 )
	{
		if ( $leftMin < 1 ) {
			$leftMin			=	1;
		}

		if ( $leftMax <= $leftMin ) {
			$leftMax			=	( $leftMin + 1 );
		}

		if ( $rightMin < 1 ) {
			$rightMin			=	1;
		}

		if ( $rightMax <= $rightMin ) {
			$rightMax			=	( $rightMin + 1 );
		}

		$operators				=	array( '+', 'add', 'plus', '-', 'subtract', 'minus', 'x', 'times', 'multiply', '/', 'divide' );
		$left					=	rand( $leftMin, $leftMax );
		$right					=	rand( $rightMin, $rightMax );
		$operator				=	$operators[array_rand( $operators, 1 )];

		switch ( $operator ) {
			case '+':
			case 'add':
			case 'plus':
				$value			=	( $left + $right );
				break;
			case '-':
			case 'subtract':
			case 'minus':
				// Prevent a negative value:
				if ( $right > $left ) {
					$rightOld	=	$right;
					$leftOld	=	$left;

					$right		=	$leftOld;
					$left		=	$rightOld;
				}

				$value			=	( $left - $right );
				break;
			case 'x':
			case 'multiply':
				$value			=	( $left * $right );
				break;
			case '/':
			case 'divide':
				// Prevent a negative value:
				if ( $right > $left ) {
					$rightOld	=	$right;
					$leftOld	=	$left;

					$right		=	$leftOld;
					$left		=	$rightOld;
				}

				$value			=	( $left / $right );

				if ( round( $value ) !== $value ) {
					$value		=	0;
				}
				break;
		}

		if ( $value <= 0 ) {
			return self::getMathEquation( $value, $leftMin, $leftMax, $rightMin, $rightMax );
		}

		if ( strlen( $operator ) == 1 ) {
			$textValues			=	array(	1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven',
											8 => 'eight', 9 => 'nine', 10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
											14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
											19 => 'nineteen', 20 => 'twenty', 30 => 'thirty', 40 => 'fourty', 50 => 'fifty', 60 => 'sixty',
											70 => 'seventy', 80 => 'eighty', 90 => 'ninety'
										);

			if ( isset( $textValues[$left] ) && ( rand( 1, 2 ) == 2 ) ) {
				$left			=	$textValues[$left];
			}

			if ( is_int( $left ) && isset( $textValues[$right] ) && ( rand( 1, 2 ) == 2 ) ) {
				$right			=	$textValues[$right];
			}
		}

		return CBTxt::T( 'CAPTCHA_MATH_EQUATION', 'What is [left] [operator] [right]?', array( '[left]' => ( ! is_int( $left ) ? CBTxt::T( $left ) : $left ), '[operator]' => ( strlen( $operator ) > 1 ? CBTxt::T( $operator ) : $operator ), '[right]' => ( ! is_int( $right ) ? CBTxt::T( $right ) : $right ) ) );
	}
}
