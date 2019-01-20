<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\ProfileBook;

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CB\Database\Table\TabTable;
use CBLib\Registry\ParamsInterface;

defined('CBLIB') or die();

class CBProfileBook
{

	/** @var array  */
	protected static $regexp	=	array(	'link'		=>	'#^((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?������]))$#i',
											'email'		=>	'/^[a-z0-9!#$%&\'*+\\\\\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\\\\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i',
											'bbcode'	=>	array(	'img'		=>	'%\[img(?: size=(\d+))?\](.+)\[/img\]%U',
																	'video'		=>	'%\[video type=youtube\]([a-zA-Z0-9]+)\[/video\]%U',
																	'size'		=>	'%\[size=(\d+)\](.+)\[/size\]%U',
																	'color'		=>	'%\[color=([a-zA-Z]+|#[a-zA-Z0-9]+)\](.+)\[/color\]%U',
																	'align'		=>	'%\[align=(left|center|right)\](.+)\[/align\]%U',
																	'url'		=>	'%\[url(?:=(.+))?\](.+)\[/url\]%U',
																	'link'		=>	'%\[link(?:=(.+))?\](.+)\[/link\]%U',
																	'email'		=>	'%\[email(?:=(.+))?\](.+)\[/email\]%U',
																	'bold'		=>	'%\[b\](.+)\[/b\]%U',
																	'italic'	=>	'%\[i\](.+)\[/i\]%U',
																	'underline'	=>	'%\[u\](.+)\[/u\]%U',
																	'quote'		=>	'%\[quote\](.+)\[/quote\]%U',
																	'list'		=>	'%\[(ul|ol)\](.+)\[/(?:ul|ol)\]%U',
																	'listItem'	=>	'%\[li\](.+)\[/li\]%U'
																)
										);

	protected static $smilies	=	array(	':)' => 'smile.png', ';)' => 'wink.png', 'B)' => 'cool.png' , '8)' => 'cool.png', ':lol:' => 'grin.png', ':laugh:' => 'laughing.png', ':cheer:' => 'cheerful.png', ':kiss:' => 'kissing.png', ':silly:' => 'silly.png',
											':ohmy:' => 'shocked.png', ':woohoo:' => 'w00t.png', ':whistle:' => 'whistling.png', ':(' => 'sad.png', ':angry:' => 'angry.png', ':blink:' => 'blink.png', ':sick:' => 'sick.png', ':unsure:' => 'unsure.png', ':dry:' => 'ermm.png',
											':huh:' => 'wassat.png', ':pinch:' => 'pinch.png', ':side:' => 'sideways.png', ':evil:' => 'devil.png', ':blush:' => 'blush.png', ':-)' => 'smile.png', ':-(' => 'sad.png', ';-)' => 'wink.png', ':S' => 'dizzy.png', ':P' => 'tongue.png',
											':D' => 'laughing.png', ':X' => 'sick.png'
										);

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.profilebook' );
			$params		=	new Registry();

			if ( $plugin ) {
				$params->load( $plugin->params );
			}
		}

		return $params;
	}

	/**
	 * Utility function for grabbing the profilebook tab while also ensuring proper display access to it
	 *
	 * @param string $tabClass
	 * @param int    $profileId
	 * @return TabTable|null
	 */
	static public function getTab( $tabClass, $profileId = null )
	{
		static $profileTab					=	null;

		if ( ! $tabClass ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		if ( $profileId === null ) {
			$profileId						=	$userId;
		}

		static $tabs						=	array();

		if ( ! isset( $tabs[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$tabs[$profileId][$userId]		=	$profileUser->_getCbTabs( false )->_getTabsDb( $profileUser->getUserData(), 'profile' );
		}

		static $tab							=	array();

		if ( ! isset( $tab[$profileId][$tabClass] ) ) {
			$foundTab						=	null;

			foreach ( $tabs[$profileId][$userId] as $profileTab ) {
				if ( $profileTab->pluginclass != $tabClass ) {
					continue;
				}

				$foundTab					=	$profileTab;
				break;
			}

			$tab[$profileId][$tabClass]		=	$foundTab;
		}

		/** @var TabTable|null $tab */
		$found								=	$tab[$profileId][$tabClass];

		if ( ! $found ) {
			return null;
		}

		if ( ! ( $found->params instanceof ParamsInterface ) ) {
			$found->params					=	new Registry( $found->params );
		}

		return $found;
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

		$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.profilebook' );

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
	 * Parses a message for bbcode and html
	 *
	 * @param string   $message
	 * @param TabTable $tab
	 * @return string
	 */
	static public function parseMessage( $message, $tab = null )
	{
		global $_PLUGINS;

		static $plugin				=	null;

		if ( $plugin === null ) {
			$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cb.profilebook' );
		}

		$params						=	self::getGlobalParams();
		$smilies					=	true;
		$bbcode						=	true;
		$bbcodeImg					=	true;
		$bbcodeVideo				=	true;
		$template					=	null;

		if ( $tab ) {
			$smilies				=	$tab->params->get( 'pbAllowSmiles', true, GetterInterface::BOOLEAN );
			$bbcode					=	$tab->params->get( 'pbAllowBBCode', true, GetterInterface::BOOLEAN );

			if ( $bbcode ) {
				$bbcodeImg			=	$tab->params->get( 'pbAllowImgBBCode', false, GetterInterface::BOOLEAN );
				$bbcodeVideo		=	$tab->params->get( 'pbAllowVideoBBCode', false, GetterInterface::BOOLEAN );
			} else {
				$bbcodeImg			=	false;
				$bbcodeVideo		=	false;
			}

			$template				=	$tab->params->get( 'template', null, GetterInterface::STRING );
		}

		if ( ( $template === '' ) || ( $template === null ) || ( $template === '-1' ) ) {
			$template				=	$params->get( 'general_template', 'default', GetterInterface::STRING );
		}

		// Smilies:
		if ( $smilies ) {
			foreach ( self::$smilies as $smiley => $image ) {
				$message			=	str_ireplace( $smiley, '<img src="' . $_PLUGINS->getPluginLivePath( $plugin ) . '/templates/' . htmlspecialchars( $template ) . '/images/' . htmlspecialchars( $image ) . '" class="pbSmiley" />', $message );
			}
		}

		// BBCode Images:
		if ( $bbcodeImg && preg_match_all( self::$regexp['bbcode']['img'], $message, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				if ( ! preg_match( self::$regexp['link'], $match[2] ) ) {
					continue;
				}

				$size				=	(int) $match[1];

				if ( $size > 499 ) {
					$size			=	499;
				}

				$message			=	str_replace( $match[0], '<img src="' . htmlspecialchars( $match[2] ) . '" class="pbBBCodeImg"' . ( $size ? ' style="width: ' . (int) $size . 'px;"' : null ) . ' />', $message );
			}
		}

		// BBCode Videos:
		if ( $bbcodeVideo && preg_match_all( self::$regexp['bbcode']['video'], $message, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $match ) {
				$message			=	str_replace( $match[0], '<iframe width="100%" height="360" src="https://www.youtube.com/embed/' . htmlspecialchars( $match[1] ) . '" frameborder="0" allowfullscreen class="pbBBCodeVideo"></iframe>', $message );
			}
		}

		if ( $bbcode ) {
			// BBCode Size:
			if ( preg_match_all( self::$regexp['bbcode']['size'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$size			=	(int) $match[1];

					switch ( $size ) {
						case 5:
							$class	=	'VeryLarge';
							$size	=	'200%';
							break;
						case 4:
							$class	=	'Large';
							$size	=	'125%';
							break;
						case 2:
							$class	=	'Small';
							$size	=	'90%';
							break;
						case 1:
							$class	=	'VerySmall';
							$size	=	'80%';
							break;
						case 3:
						default:
							$class	=	'Normal';
							$size	=	'100%';
							break;
					}

					$message		=	str_replace( $match[0], '<span class="pbBBCodeSize pbBBCodeSize' . htmlspecialchars( $class ) . '" style="font-size: ' . htmlspecialchars( $size ) . ';">' . $match[2] . '</span>', $message );
				}
			}

			// BBCode Color:
			if ( preg_match_all( self::$regexp['bbcode']['color'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$message		=	str_replace( $match[0], '<span class="pbBBCodeColor" style="color: ' . htmlspecialchars( $match[1] ) . ';">' . $match[2] . '</span>', $message );
				}
			}

			// BBCode Align:
			if ( preg_match_all( self::$regexp['bbcode']['align'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$message		=	str_replace( $match[0], '<div class="pbBBCodeAlign text-' . htmlspecialchars( $match[1] ) . '">' . $match[2] . '</div>', $message );
				}
			}

			// BBCode URL (with url tag):
			if ( preg_match_all( self::$regexp['bbcode']['url'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$url			=	( $match[1] ? $match[1] : $match[2] );

					if ( ! preg_match( self::$regexp['link'], $url ) ) {
						continue;
					}

					$message		=	str_replace( $match[0], '<a href="' . htmlspecialchars( $url ) . '" rel="nofollow" target="_blank" class="pbBBCodeURL">' . $match[2] . '</a>', $message );
				}
			}

			// BBCode URL (with link tag):
			if ( preg_match_all( self::$regexp['bbcode']['link'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$url			=	( $match[1] ? $match[1] : $match[2] );

					if ( ! preg_match( self::$regexp['link'], $url ) ) {
						continue;
					}

					$message		=	str_replace( $match[0], '<a href="' . htmlspecialchars( $url ) . '" rel="nofollow" target="_blank" class="pbBBCodeURL">' . $match[2] . '</a>', $message );
				}
			}

			// BBCode Email:
			if ( preg_match_all( self::$regexp['bbcode']['email'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$email			=	( $match[1] ? $match[1] : $match[2] );

					if ( ! preg_match( self::$regexp['email'], $email ) ) {
						continue;
					}

					$message		=	str_replace( $match[0], '<a href="mailto:' . htmlspecialchars( $email ) . '" rel="nofollow" target="_blank" class="pbBBCodeEmail">' . $match[2] . '</a>', $message );
				}
			}

			// BBCode Bold:
			if ( preg_match_all( self::$regexp['bbcode']['bold'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$message		=	str_replace( $match[0], '<strong class="pbBBCodeBold">' . $match[1] . '</strong>', $message );
				}
			}

			// BBCode Italic:
			if ( preg_match_all( self::$regexp['bbcode']['italic'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$message		=	str_replace( $match[0], '<span class="pbBBCodeItalic" style="font-style: italic;">' . $match[1] . '</span>', $message );
				}
			}

			// BBCode Underline:
			if ( preg_match_all( self::$regexp['bbcode']['underline'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$message		=	str_replace( $match[0], '<span class="pbBBCodeUnderline" style="text-decoration: underline;">' . $match[1] . '</span>', $message );
				}
			}

			// BBCode Quote:
			if ( preg_match_all( self::$regexp['bbcode']['quote'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$message		=	str_replace( $match[0], '<blockquote class="pbBBCodeQuote"><p>' . $match[1] . '</p></blockquote>', $message );
				}
			}

			// BBCode List:
			if ( preg_match_all( self::$regexp['bbcode']['list'], $message, $matches, PREG_SET_ORDER ) ) {
				foreach ( $matches as $match ) {
					$items			=	array();

					if ( preg_match_all( self::$regexp['bbcode']['listItem'], $match[2], $listItems ) ) {
						$items		=	$listItems[1];
					}

					if ( ! $items ) {
						continue;
					}

					$message		=	str_replace( $match[0], '<' . $match[1] . ' class="pbBBCodeList"><li class="pbBBCodeListItem">' . implode( '</li><li class="pbBBCodeListItem">', $items ) . '</li></' . $match[1] . '>', $message );
				}
			}
		}

		$words						=	preg_split( '/\s/i', $message );

		// Replaces URLs with clickable html URLs:
		foreach ( $words as $word ) {
			if ( preg_match( self::$regexp['link'], $word, $match ) ) {
				$message			=	str_replace( $word, '<a href="' . htmlspecialchars( $match[0] ) . '" rel="nofollow"' . ( ! \JUri::isInternal( $match[0] ) ? ' target="_blank"' : null ) . '>' . htmlspecialchars( $match[0] ) . '</a>', $message );
			} elseif ( preg_match( self::$regexp['email'], $word, $match ) ) {
				$message			=	str_replace( $word, '<a href="mailto:' . htmlspecialchars( $match[0] ) . '" rel="nofollow" target="_blank">' . htmlspecialchars( $match[0] ) . '</a>', $message );
			}
		}

		// Remove duplicate spaces:
		$message					=	preg_replace( '/ {2,}/i', ' ', $message );

		// Remove duplicate tabs:
		$message					=	preg_replace( '/\t{2,}/i', "\t", $message );

		// Remove duplicate linebreaks:
		$message					=	preg_replace( '/(\r\n|\r|\n){2,}/i', '$1', $message );

		// Replaces linebre	aks with html breaks:
		$message					=	str_replace( array( "\n", "\r\n" ), '<br />', $message );

		return $message;
	}
}
