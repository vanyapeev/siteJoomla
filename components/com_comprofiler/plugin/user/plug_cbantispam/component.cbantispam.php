<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Application\Application;
use CB\Plugin\AntiSpam\Captcha;
use CB\Plugin\AntiSpam\CBAntiSpam;
use CB\Plugin\AntiSpam\Table\BlockTable;
use CB\Plugin\AntiSpam\Table\WhitelistTable;
use CB\Plugin\AntiSpam\Table\AttemptTable;
use CB\Plugin\AntiSpam\Table\LogTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbantispam extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		$format							=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action							=	$this->input( 'action', null, GetterInterface::STRING );
		$function						=	$this->input( 'func', null, GetterInterface::STRING );
		$id								=	$this->input( 'id', null, GetterInterface::STRING );
		$user							=	CBuser::getMyUserDataInstance();

		if ( $format != 'raw' ) {
			ob_start();
		}

		switch ( $action ) {
			case 'prune':
				switch ( $function ) {
					case 'block':
					case 'log':
					case 'attempts':
						$this->pruneItems( $function, false );
						break;
					case 'all':
					default:
						$this->pruneAll( false );
						break;
				}
				break;
			case 'captcha':
				switch ( $function ) {
					case 'image':
						$this->captchaImage( $id );
						break;
					case 'audio':
						$this->captchaAudio( $id );
						break;
					case 'load':
						$this->captchaLoad( $id );
						break;
				}
				break;
			case 'block':
				if ( ( ! $this->params->get( 'general_block', true, GetterInterface::BOOLEAN ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
					CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'account':
						$this->showBlock( $id, 'account', $user );
						break;
					case 'user':
						$this->showBlock( $id, 'user', $user );
						break;
					case 'ip':
						$this->showBlock( $id, 'ip', $user );
						break;
					case 'ip_range':
						$this->showBlock( $id, 'ip_range', $user );
						break;
					case 'email':
						$this->showBlock( $id, 'email', $user );
						break;
					case 'domain':
						$this->showBlock( $id, 'domain', $user );
						break;
					case 'edit':
						$this->showBlock( $id, null, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveBlock( $id, $user );
						break;
					case 'delete':
						$this->deleteBlock( $id, $user );
						break;
					case 'new':
					default:
						$this->showBlock( null, null, $user );
						break;
				}
				break;
			case 'whitelist':
				if ( ( ! $this->params->get( 'general_whitelist', true, GetterInterface::BOOLEAN ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
					CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'user':
						$this->showWhitelist( $id, 'user', $user );
						break;
					case 'ip':
						$this->showWhitelist( $id, 'ip', $user );
						break;
					case 'email':
						$this->showWhitelist( $id, 'email', $user );
						break;
					case 'domain':
						$this->showWhitelist( $id, 'domain', $user );
						break;
					case 'edit':
						$this->showWhitelist( $id, null, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveWhitelist( $id, $user );
						break;
					case 'delete':
						$this->deleteWhitelist( $id, $user );
						break;
					case 'new':
					default:
						$this->showWhitelist( null, null, $user );
						break;
				}
				break;
			case 'attempt':
				if ( ( ! $this->params->get( 'general_attempts', true, GetterInterface::BOOLEAN ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
					CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'delete':
						$this->deleteAttempt( $id, $user );
						break;
				}
				break;
			case 'log':
				if ( ( ! $this->params->get( 'general_log', true, GetterInterface::BOOLEAN ) ) || ( ! Application::MyUser()->isGlobalModerator() ) ) {
					CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				}

				switch ( $function ) {
					case 'delete':
						$this->deleteLog( $id, $user );
						break;
				}
				break;
		}

		if ( $format != 'raw' ) {
			$html						=	ob_get_contents();
			ob_end_clean();

			$class						=	$this->params->get( 'general_class', null );

			$return						=	'<div class="cbAntiSpam' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
										.		$html
										.	'</div>';

			echo $return;
		}
	}

	/**
	 * Prunes old items of $type
	 *
	 * @param string $type
	 * @param bool   $force
	 */
	private function pruneItems( $type, $force )
	{
		global $_CB_framework, $_CB_database;

		$clean					=	false;

		if ( $force ) {
			$clean				=	true;
		} else {
			$token				=	$this->input( 'token', null, GetterInterface::STRING );

			if ( $token == md5( $_CB_framework->getCfg( 'secret' ) ) ) {
				$clean			=	true;
			}
		}

		if ( $clean ) {
			switch ( $type ) {
				case 'block':
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_block' );
					$_CB_database->setQuery( $query );
					$blocks		=	$_CB_database->loadObjectList( null, '\CB\Plugin\AntiSpam\Table\BlockTable', array( $_CB_database ) );

					/** @var BlockTable[] $blocks */
					foreach ( $blocks as $block ) {
						if ( $block->expired() ) {
							$block->delete();
						}
					}
					break;
				case 'log':
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_log' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( strtoupper( $this->params->get( 'cleanup_log_dur', '-1 YEAR', GetterInterface::STRING ) ) )->format( 'Y-m-d H:i:s' ) );
					$_CB_database->setQuery( $query );
					$logs		=	$_CB_database->loadObjectList( null, '\CB\Plugin\AntiSpam\Table\LogTable', array( $_CB_database ) );

					/** @var LogTable[] $logs */
					foreach ( $logs as $log ) {
						$log->delete();
					}
					break;
				case 'attempts':
					$query		=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_antispam_attempts' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'date' ) . " <= " . $_CB_database->Quote( Application::Date( 'now', 'UTC' )->modify( strtoupper( $this->params->get( 'cleanup_attempts_dur', '-1 YEAR', GetterInterface::STRING ) ) )->format( 'Y-m-d H:i:s' ) );
					$_CB_database->setQuery( $query );
					$attempts	=	$_CB_database->loadObjectList( null, '\CB\Plugin\AntiSpam\Table\AttemptTable', array( $_CB_database ) );

					/** @var AttemptTable[] $attempts */
					foreach ( $attempts as $attempt ) {
						$attempt->delete();
					}
					break;
			}

			if ( ! $force ) {
				header( 'HTTP/1.0 200 OK' );
				exit();
			}
		} else {
			if ( ! $force ) {
				header( 'HTTP/1.0 403 Forbidden' );
				exit();
			}
		}
	}

	/**
	 * Prunes all old items
	 *
	 * @param bool $force
	 */
	private function pruneAll( $force )
	{
		global $_CB_framework;

		$clean			=	false;

		if ( $force ) {
			$clean		=	true;
		} else {
			$token		=	$this->input( 'token', null, GetterInterface::STRING );

			if ( $token == md5( $_CB_framework->getCfg( 'secret' ) ) ) {
				$clean	=	true;
			}
		}

		if ( $clean ) {
			$this->pruneItems( 'block', true );
			$this->pruneItems( 'log', true );
			$this->pruneItems( 'attempts', true );

			if ( ! $force ) {
				header( 'HTTP/1.0 200 OK' );
				exit();
			}
		} else {
			if ( ! $force ) {
				header( 'HTTP/1.0 403 Forbidden' );
				exit();
			}
		}
	}

	/**
	 * Generates a captcha image
	 *
	 * @param string $id
	 */
	public function captchaImage( $id )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $id ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		while ( @ob_end_clean() );

		$absPath						=	$_PLUGINS->getPluginPath( $this->getPluginId() );
		$captcha						=	new Captcha();

		$captcha->load( $id );

		if ( $this->input( 'ver', null, GetterInterface::STRING ) ) {
			$captcha->reset();
		}

		$code							=	$captcha->code();

		if ( ! $code ) {
			exit( CBTxt::T( 'failed to generate captcha code' ) );
		}

		$height							=	$captcha->get( 'internal_height', 50, GetterInterface::INT );

		if ( ! $height ) {
			$height						=	50;
		}

		$bgColor						=	$captcha->get( 'internal_bg_color', '255,255,255', GetterInterface::STRING );

		if ( ! $bgColor ) {
			$bgColor					=	'255,255,255';
		}

		$bgColors						=	explode( ',', $bgColor );
		$imgBgColors					=	array();

		if ( $bgColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $bgColors[$i] ) ) {
				$color					=	(int) trim( $bgColors[$i] );
				$imgBgColors[]			=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgBgColors ) {
			$imgBgColors				=	array( 255, 255, 255 );
		} elseif ( count( $imgBgColors ) < 3 ) {
			if ( count( $imgBgColors ) < 2 ) {
				$imgBgColors[]			=	255;
				$imgBgColors[]			=	255;
			} else {
				$imgBgColors[]			=	255;
			}
		}

		$txtColor						=	$captcha->get( 'internal_txt_color', '20,40,100', GetterInterface::STRING );

		if ( ! $txtColor ) {
			$txtColor					=	'20,40,100';
		}

		$txtColors						=	explode( ',', $txtColor );
		$imgTxtColors					=	array();

		if ( $txtColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $txtColors[$i] ) ) {
				$color					=	(int) trim( $txtColors[$i] );
				$imgTxtColors[]			=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgTxtColors ) {
			$imgTxtColors				=	array( 20, 40, 100 );
		} elseif ( count( $imgTxtColors ) < 3 ) {
			if ( count( $imgTxtColors ) < 2 ) {
				$imgTxtColors[]			=	40;
				$imgTxtColors[]			=	100;
			} else {
				$imgTxtColors[]			=	100;
			}
		}

		$nBgColor						=	$captcha->get( 'internal_bg_noise_color', '100,120,180', GetterInterface::STRING );

		if ( ! $nBgColor ) {
			$nBgColor					=	'100,120,180';
		}

		$nBgColors						=	explode( ',', $nBgColor );
		$imgNoiseBgColors				=	array();

		if ( $nBgColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $nBgColors[$i] ) ) {
				$color					=	(int) trim( $nBgColors[$i] );
				$imgNoiseBgColors[]		=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgNoiseBgColors ) {
			$imgNoiseBgColors			=	array( 100, 120, 180 );
		} elseif ( count( $imgNoiseBgColors ) < 3 ) {
			if ( count( $imgNoiseBgColors ) < 2 ) {
				$imgNoiseBgColors[]		=	120;
				$imgNoiseBgColors[]		=	180;
			} else {
				$imgNoiseBgColors[]		=	180;
			}
		}

		$nFgColor						=	$captcha->get( 'internal_fg_noise_color', '100,120,120', GetterInterface::STRING );

		if ( ! $nFgColor ) {
			$nFgColor					=	'100,120,120';
		}

		$nFgColors						=	explode( ',', $nFgColor );
		$imgNoiseFgColors				=	array();

		if ( $nFgColors ) for( $i = 0; $i < 3; $i++ ) {
			if ( isset( $nFgColors[$i] ) ) {
				$color					=	(int) trim( $nFgColors[$i] );
				$imgNoiseFgColors[]		=	( $color > 255 ? 255 : ( $color < 0 ? 0 : $color ) );
			}
		}

		if ( ! $imgNoiseFgColors ) {
			$imgNoiseFgColors			=	array( 100, 120, 120 );
		} elseif ( count( $imgNoiseFgColors ) < 3 ) {
			if ( count( $imgNoiseFgColors ) < 2 ) {
				$imgNoiseFgColors[]		=	120;
				$imgNoiseFgColors[]		=	120;
			} else {
				$imgNoiseFgColors[]		=	120;
			}
		}

		$fonts							=	explode( '|*|', $captcha->get( 'internal_font', 'EARWIGFA.ttf|*|monofont.ttf', GetterInterface::STRING ) );

		if ( ! $fonts ) {
			$fonts						=	array( 'EARWIGFA.ttf', 'monofont.ttf' );
		}

		$imgFonts						=	array();

		foreach ( $fonts as $font ) {
			$imgFont					=	$absPath . '/fonts/' . $font;

			if ( ! file_exists( $imgFont ) ) {
				continue;
			}

			$imgFonts[]					=	$imgFont;
		}

		if ( ! $imgFonts ) {
			exit( CBTxt::T( 'failed to locate font file' ) );
		}

		$charRotation					=	$captcha->get( 'internal_rotation', 13, GetterInterface::INT );

		if ( $charRotation < 0 ) {
			$charRotation				=	0;
		}

		$charOffset						=	$captcha->get( 'internal_offset', 3, GetterInterface::INT );

		if ( $charOffset < 0 ) {
			$charOffset					=	0;
		}

		$charRange						=	$captcha->get( 'internal_range', 5, GetterInterface::INT );

		if ( $charRange < 0 ) {
			$charRange					=	0;
		}

		$colorRange						=	$captcha->get( 'internal_color_range', 0, GetterInterface::INT );

		if ( $colorRange < 0 ) {
			$colorRange					=	0;
		}

		$fontSize						=	( $height * 0.75 );
		$textBox						=	imagettfbbox( ( $charRange ? ( $fontSize + $charRange ) : $fontSize ), 0, $imgFonts[0], $code );

		if ( $textBox === false ) {
			exit( CBTxt::T( 'imagettfbbox failed to establish image size' ) );
		}

		$width							=	( $textBox[4] + 20 );
		$image							=	imagecreatetruecolor( $width, $height );

		if ( $image === false ) {
			exit( CBTxt::T( 'imagecreate failed to create new image' ) );
		}

		if ( $colorRange ) {
			$bgColorR					=	mt_rand( ( ( $imgBgColors[0] - $colorRange ) < 0 ? 0 : ( $imgBgColors[0] - $colorRange ) ), ( ( $imgBgColors[0] + $colorRange ) > 255 ? 255 : ( $imgBgColors[0] + $colorRange ) ) );
			$bgColorG					=	mt_rand( ( ( $imgBgColors[1] - $colorRange ) < 0 ? 0 : ( $imgBgColors[1] - $colorRange ) ), ( ( $imgBgColors[1] + $colorRange ) > 255 ? 255 : ( $imgBgColors[1] + $colorRange ) ) );
			$bgColorB					=	mt_rand( ( ( $imgBgColors[2] - $colorRange ) < 0 ? 0 : ( $imgBgColors[2] - $colorRange ) ), ( ( $imgBgColors[2] + $colorRange ) > 255 ? 255 : ( $imgBgColors[2] + $colorRange ) ) );

			$backgroundColor			=	imagecolorallocate( $image, $bgColorR, $bgColorG, $bgColorB );

			if ( $backgroundColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_COLOR_FAILED', 'imagecolorallocate failed to set BG colors [r], [g], [b]', array( '[r]' => $bgColorR, '[g]' => $bgColorG, '[b]' => $bgColorB ) ) );
			}

			$textColorR					=	mt_rand( ( ( $imgTxtColors[0] - $colorRange ) < 0 ? 0 : ( $imgTxtColors[0] - $colorRange ) ), ( ( $imgTxtColors[0] + $colorRange ) > 255 ? 255 : ( $imgTxtColors[0] + $colorRange ) ) );
			$textColorG					=	mt_rand( ( ( $imgTxtColors[1] - $colorRange ) < 0 ? 0 : ( $imgTxtColors[1] - $colorRange ) ), ( ( $imgTxtColors[1] + $colorRange ) > 255 ? 255 : ( $imgTxtColors[1] + $colorRange ) ) );
			$textColorB					=	mt_rand( ( ( $imgTxtColors[2] - $colorRange ) < 0 ? 0 : ( $imgTxtColors[2] - $colorRange ) ), ( ( $imgTxtColors[2] + $colorRange ) > 255 ? 255 : ( $imgTxtColors[2] + $colorRange ) ) );

			$textColor					=	imagecolorallocate( $image, $textColorR, $textColorG, $textColorB );

			if ( $textColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_TEXT_COLOR_FAILED', 'imagecolorallocate failed to set text colors [r], [g], [b]', array( '[r]' => $textColorR, '[g]' => $textColorG, '[b]' => $textColorB ) ) );
			}

			$noiseBgColorR				=	mt_rand( ( ( $imgNoiseBgColors[0] - $colorRange ) < 0 ? 0 : ( $imgNoiseBgColors[0] - $colorRange ) ), ( ( $imgNoiseBgColors[0] + $colorRange ) > 255 ? 255 : ( $imgNoiseBgColors[0] + $colorRange ) ) );
			$noiseBgColorG				=	mt_rand( ( ( $imgNoiseBgColors[1] - $colorRange ) < 0 ? 0 : ( $imgNoiseBgColors[1] - $colorRange ) ), ( ( $imgNoiseBgColors[1] + $colorRange ) > 255 ? 255 : ( $imgNoiseBgColors[1] + $colorRange ) ) );
			$noiseBgColorB				=	mt_rand( ( ( $imgNoiseBgColors[2] - $colorRange ) < 0 ? 0 : ( $imgNoiseBgColors[2] - $colorRange ) ), ( ( $imgNoiseBgColors[2] + $colorRange ) > 255 ? 255 : ( $imgNoiseBgColors[2] + $colorRange ) ) );

			$noiseBgColor				=	imagecolorallocate( $image, $noiseBgColorR, $noiseBgColorG, $noiseBgColorB );

			if ( $noiseBgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise BG colors [r], [g], [b]', array( '[r]' => $noiseBgColorR, '[g]' => $noiseBgColorG, '[b]' => $noiseBgColorB ) ) );
			}

			$noiseFgColorR				=	mt_rand( ( ( $imgNoiseFgColors[0] - $colorRange ) < 0 ? 0 : ( $imgNoiseFgColors[0] - $colorRange ) ), ( ( $imgNoiseFgColors[0] + $colorRange ) > 255 ? 255 : ( $imgNoiseFgColors[0] + $colorRange ) ) );
			$noiseFgColorG				=	mt_rand( ( ( $imgNoiseFgColors[1] - $colorRange ) < 0 ? 0 : ( $imgNoiseFgColors[1] - $colorRange ) ), ( ( $imgNoiseFgColors[1] + $colorRange ) > 255 ? 255 : ( $imgNoiseFgColors[1] + $colorRange ) ) );
			$noiseFgColorB				=	mt_rand( ( ( $imgNoiseFgColors[2] - $colorRange ) < 0 ? 0 : ( $imgNoiseFgColors[2] - $colorRange ) ), ( ( $imgNoiseFgColors[2] + $colorRange ) > 255 ? 255 : ( $imgNoiseFgColors[2] + $colorRange ) ) );

			$noiseFgColor				=	imagecolorallocate( $image, $noiseFgColorR, $noiseFgColorG, $noiseFgColorB );

			if ( $noiseFgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_FG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise FG colors [r], [g], [b]', array( '[r]' => $noiseFgColorR, '[g]' => $noiseFgColorG, '[b]' => $noiseFgColorB ) ) );
			}
		} else {
			$backgroundColor			=	imagecolorallocate( $image, $imgBgColors[0], $imgBgColors[1], $imgBgColors[2] );

			if ( $backgroundColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_COLOR_FAILED', 'imagecolorallocate failed to set BG colors [r], [g], [b]', array( '[r]' => $imgBgColors[0], '[g]' => $imgBgColors[1], '[b]' => $imgBgColors[2] ) ) );
			}

			$textColor					=	imagecolorallocate( $image, $imgTxtColors[0], $imgTxtColors[1], $imgTxtColors[2] );

			if ( $textColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_TEXT_COLOR_FAILED', 'imagecolorallocate failed to set text colors [r], [g], [b]', array( '[r]' => $imgTxtColors[0], '[g]' => $imgTxtColors[1], '[b]' => $imgTxtColors[2] ) ) );
			}

			$noiseBgColor				=	imagecolorallocate( $image, $imgNoiseBgColors[0], $imgNoiseBgColors[1], $imgNoiseBgColors[2] );

			if ( $noiseBgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_BG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise BG colors [r], [g], [b]', array( '[r]' => $imgNoiseBgColors[0], '[g]' => $imgNoiseBgColors[1], '[b]' => $imgNoiseBgColors[2] ) ) );
			}

			$noiseFgColor				=	imagecolorallocate( $image, $imgNoiseFgColors[0], $imgNoiseFgColors[1], $imgNoiseFgColors[2] );

			if ( $noiseFgColor === false ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_FG_NOISE_COLOR_FAILED', 'imagecolorallocate failed to set noise FG colors [r], [g], [b]', array( '[r]' => $imgNoiseFgColors[0], '[g]' => $imgNoiseFgColors[1], '[b]' => $imgNoiseFgColors[2] ) ) );
			}
		}

		if ( imagefill( $image, 0, 0, $backgroundColor ) === false ) {
			exit( CBTxt::T( 'imagefill failed to add BG color to the image' ) );
		}

		if ( $captcha->get( 'internal_bg_noise', true, GetterInterface::BOOLEAN ) ) {
			for ( $i = 0; $i < ( ( $width * $height ) / 10 ); $i++ ) {
				if ( imagefilledellipse( $image, mt_rand( 0, $width ), mt_rand( 0, $height ), 1, 1, $noiseBgColor ) === false ) {
					exit( CBTxt::T( 'imagefilledellipse failed to add BG noise to the image' ) );
				}
			}

			for( $i = 1; ( $i < ( $width / 20 ) ); $i++ ) {
				if ( imageline( $image, ( ( $i * 10 ) - mt_rand( -20, 20 ) ), 0, ( ( $i * 10 ) + mt_rand( -20, 20 ) ), $height, $noiseBgColor ) === false ) {
					exit( CBTxt::T( 'imageline failed to add first pass BG noise to the image' ) );
				}
			}

			for( $i = 1; ( $i < ( $height / 20 ) ); $i++ ) {
				if ( imageline( $image, 0, ( ( $i * 10 ) - mt_rand( -20, 20 ) ), $width, ( ( $i * 10 ) + mt_rand( -20, 20 ) ), $noiseBgColor ) === false ) {
					exit( CBTxt::T( 'imageline failed to add second pass BG noise to the image' ) );
				}
			}
		}

		if ( $_CB_framework->outputCharset() == 'UTF-8' ) {
			$codeSplit					=	preg_split( '//u', $code, -1, PREG_SPLIT_NO_EMPTY );
		} else {
			$codeSplit					=	str_split( $code, 1 );
		}

		$x								=	( ( $width - $textBox[4] ) / 2 );
		$y								=	( ( $height - $textBox[5] ) / 2 );
		$i								=	0;

		if ( $codeSplit ) foreach ( $codeSplit as $c ) {
			$charFont					=	( count( $imgFonts ) > 1 ? array_rand( $imgFonts, 1 ) : 0 );

			if ( $charRange ) {
				$charSize				=	mt_rand( ( $fontSize - $charRange ), ( $fontSize + $charRange ) );

				if ( ( $charSize < 0 ) || ( $charSize > $height ) ) {
					$charSize			=	$fontSize;
				}
			} else {
				$charSize				=	$fontSize;
			}

			$result						=	imagettftext( $image, $charSize, ( $charRotation ? mt_rand( -$charRotation, $charRotation ) : 0 ), ( $x + $i ), ( $charOffset ? ( $y + mt_rand( -$charOffset, $charOffset ) ) : $y ), -$textColor, $imgFonts[$charFont], $c );

			if ( ! $result ) {
				exit( CBTxt::T( 'CAPTCHA_IMAGE_TEXT_FAILED', 'imagettftext failed to add "[text]" to the image', array( '[text]' => $c ) ) );
			}

			$i							+=	( $textBox[4] / count( $codeSplit ) );
		}

		if ( $captcha->get( 'internal_fg_noise', true, GetterInterface::BOOLEAN ) ) for ( $i = 0; $i < 3; $i++ ) {
			if ( imageline( $image, mt_rand( 0, $width ), mt_rand( 0, $height ), mt_rand( 0, $width ), mt_rand( 0, $height ), $noiseFgColor ) === false ) {
				exit( CBTxt::T( 'imageline failed to add FG noise to the image' ) );
			}
		}

		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		switch ( $captcha->get( 'internal_format', 'png', GetterInterface::STRING ) ) {
			case 'gif':
				header( 'Content-Type: image/gif; charset=utf-8' );

				if ( imagegif( $image ) === false ) {
					exit( CBTxt::T( 'imagegif failed to generate image' ) );
				}
				break;
			case 'png':
				header( 'Content-Type: image/png; charset=utf-8' );

				if ( imagepng( $image, null, 0 ) === false ) {
					exit( CBTxt::T( 'imagepng failed to generate image' ) );
				}
				break;
			case 'jpeg':
			default:
				header( 'Content-Type: image/jpeg; charset=utf-8' );

				if ( imagejpeg( $image, null, 100 ) === false ) {
					exit( CBTxt::T( 'imagejpeg failed to generate image' ) );
				}
				break;
		}

		if ( imagedestroy( $image ) === false ) {
			exit( CBTxt::T( 'imagedestroy failed to destroy the image' ) );
		}

		@ob_flush();
		flush();

		exit();
	}

	/**
	 * Generates and returns captcha input, image, and audio HTML
	 *
	 * @param string $id
	 */
	public function captchaLoad( $id )
	{
		$captcha	=	new Captcha();

		if ( ! $captcha->load( $id ) ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		if ( $captcha->get( 'loaded', false, GetterInterface::BOOLEAN ) || ( ! in_array( $captcha->get( 'mode', null, GetterInterface::STRING ), array( 'internal', 'question', 'math' ) ) ) ) {
			header( 'HTTP/1.0 403 Forbidden' );
			exit();
		}

		$captcha->set( 'loaded', true );

		$captcha->cache();

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-Type: application/json' );

		while ( @ob_end_clean() );

		echo json_encode( array( 'input' => $captcha->input(), 'image' => $captcha->image(), 'audio' => $captcha->audio(), 'captcha' => $captcha->captcha() ) );

		exit();
	}

	/**
	 * Generates a captcha audio file
	 *
	 * @param string $id
	 */
	public function captchaAudio( $id )
	{
		global $_PLUGINS;

		if ( ! $id ) {
			header( 'HTTP/1.0 404 Not Found' );
			exit();
		}

		while ( @ob_end_clean() );

		$absPath			=	$_PLUGINS->getPluginPath( $this->getPluginId() );
		$captcha			=	new Captcha();

		$captcha->load( $id );

		$code				=	$captcha->code();

		if ( ! $code ) {
			exit( CBTxt::T( 'failed to generate captcha code' ) );
		}

		$sounds				=	array();

		for( $i = 0; $i < cbIsoUtf_strlen( $code ); $i++ ) {
			$file			=	$absPath . '/audio/' . $code{$i} . '.mp3';

			if ( ! file_exists( $file ) ) {
				exit( CBTxt::T( 'CAPTCHA_AUDIO_FILE_FAILED', 'failed to locate "[file]" audio file', array( '[file]' => $file ) ) );
			}

			$sounds[]		=	$file;
		}

		header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );
		header( 'Content-Type: audio/mpeg' );
		header( 'Content-Disposition: inline; filename=cbcaptcha.mp3;' );
		header( 'Content-Transfer-Encoding: binary' );

		$out				=	'';
		$count				=	count( $sounds );
		$i					=	0;

		foreach ( $sounds as $sound ) {
			$i++;

			if ( $i != $count ) {
				$offset		=	128;
			} else {
				$offset		=	0;
			}

			$fh				=	fopen( $sound, 'rb' );
			$size			=	filesize( $sound );

			$out			.=	fread( $fh, ( $size - $offset ) );

			fclose( $fh );
		}

		header( 'Content-Length: ' . cbIsoUtf_strlen( $out ) );

		echo $out;

		@ob_flush();
		flush();

		exit();
	}

	/**
	 * Displays block user page
	 *
	 * @param int         $id
	 * @param string      $type
	 * @param UserTable   $user
	 */
	public function showBlock( $id, $type, $user )
	{
		$blockUser				=	CBuser::getUserDataInstance( $this->input( 'user', 0, GetterInterface::INT ) );

		if ( Application::User( $blockUser->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$value					=	null;

		switch ( $type ) {
			case 'account':
			case 'user':
				$value			=	$blockUser->get( 'id', 0, GetterInterface::INT );
				break;
			case 'ip':
				$value			=	CBAntiSpam::getUserIP( $blockUser );
				break;
			case 'ip_range':
				$value			=	CBAntiSpam::getUserIP( $blockUser ) . ':' . CBAntiSpam::getUserIP( $blockUser );
				break;
			case 'email':
				$value			=	$blockUser->get( 'email', null, GetterInterface::STRING );
				break;
			case 'domain':
				$value			=	CBAntiSpam::getEmailDomain( $blockUser );
				break;
		}

		$row					=	new BlockTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		CBAntiSpam::getTemplate( 'block' );

		$input					=	array();

		$listType				=	array();
		$listType[]				=	moscomprofilerHTML::makeOption( 'account', CBTxt::T( 'Account' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'user', CBTxt::T( 'User' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'ip', CBTxt::T( 'IP Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'ip_range', CBTxt::T( 'IP Address Range' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'email', CBTxt::T( 'Email Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'domain', CBTxt::T( 'Email Domain' ) );

		$type					=	$this->input( 'post/type', $row->get( 'type', $type, GetterInterface::STRING ), GetterInterface::STRING );
		$typeTooltip			=	cbTooltip( null, CBTxt::T( 'Select the block type. Type determines what value should be supplied.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['type']			=	moscomprofilerHTML::selectList( $listType, 'type', 'class="form-control required"' . $typeTooltip, 'value', 'text', $type, 1, true, false, false );

		$valueTooltip			=	cbTooltip( null, CBTxt::T( 'Input block value in relation to the type. User type use the users user_id (e.g. 42). IP Address type use a full valid IP Address (e.g. 192.168.0.1). IP Address Range type use two full valid IP Addresses separated by a colon (e.g. 192.168.0.1:192.168.0.100). Email type use a fill valid email address (e.g. invalid@cb.invalid). Email Domain type use a full email address domain after @ (e.g. example.com). Additionally IP Address, Email Address, and Email Domain types support % wildcard.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['value']			=	'<input type="text" id="value" name="value" value="' . htmlspecialchars( $this->input( 'post/value', $row->get( 'value', $value, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control required" size="25"' . $valueTooltip . ' />';

		$calendar				=	new cbCalendars( 1 );
		$dateTooltip			=	cbTooltip( null, CBTxt::T( 'Select the date and time the block should go in affect. Note date and time always functions in UTC.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['date']			=	$calendar->cbAddCalendar( 'date', null, true, $this->input( 'post/date', $row->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ), GetterInterface::STRING ), false, true, null, null, $dateTooltip );

		$durationTooltip		=	cbTooltip( null, CBTxt::T( 'Input the strtotime relative date (e.g. +1 Day). This duration will be added to the datetime specified above. Leave blank for a forever duration.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['duration']		=	'<input type="text" id="duration" name="duration" value="' . htmlspecialchars( $this->input( 'post/duration', $row->get( 'duration', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control" size="25"' . $durationTooltip . ' />';

		$listDurations			=	array();
		$listDurations[]		=	moscomprofilerHTML::makeOption( '', CBTxt::T( '- Select Duration -' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'MIDNIGHT', CBTxt::T( 'Midnight' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NOON', CBTxt::T( 'Noon' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'TOMORROW', CBTxt::T( 'Tomorrow' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NEXT WEEK', CBTxt::T( 'Next Week' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NEXT MONTH', CBTxt::T( 'Next Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'NEXT YEAR', CBTxt::T( 'Next Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF THIS MONTH', CBTxt::T( 'Last Day of This Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF NEXT MONTH', CBTxt::T( 'First Day of Next Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF NEXT MONTH', CBTxt::T( 'Last Day of Next Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF THIS YEAR', CBTxt::T( 'Last Day of This Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF NEXT YEAR', CBTxt::T( 'First Day of Next Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF NEXT YEAR', CBTxt::T( 'Last Day of Next Year' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF JANUARY', CBTxt::T( 'First Day of January' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF JANUARY', CBTxt::T( 'Last Day of January' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF FEBRUARY', CBTxt::T( 'First Day of February' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF FEBRUARY', CBTxt::T( 'Last Day of February' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF MARCH', CBTxt::T( 'First Day of March' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF MARCH', CBTxt::T( 'Last Day of March' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF APRIL', CBTxt::T( 'First Day of Apil' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF APRIL', CBTxt::T( 'Last Day of Apil' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF MAY', CBTxt::T( 'First Day of May' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF MAY', CBTxt::T( 'Last Day of May' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF JUNE', CBTxt::T( 'First Day of June' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF JUNE', CBTxt::T( 'Last Day of June' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF JULY', CBTxt::T( 'First Day of July' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF JULY', CBTxt::T( 'Last Day of July' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF AUGUST', CBTxt::T( 'First Day of August' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF AUGUST', CBTxt::T( 'Last Day of August' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF SEPTEMBER', CBTxt::T( 'First Day of September' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF SEPTEMBER', CBTxt::T( 'Last Day of September' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF OCTOBER', CBTxt::T( 'First Day of October' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF OCTOBER', CBTxt::T( 'Last Day of October' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF NOVEMBER', CBTxt::T( 'First Day of November' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF NOVEMBER', CBTxt::T( 'Last Day of November' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'FIRST DAY OF DECEMBER', CBTxt::T( 'First Day of December' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( 'LAST DAY OF DECEMBER', CBTxt::T( 'Last Day of December' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+30 MINUTES', CBTxt::T( '30 Minutes' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 HOUR', CBTxt::T( '1 Hour' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 HOURS', CBTxt::T( '3 Hours' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+6 HOURS', CBTxt::T( '6 Hours' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+12 HOURS', CBTxt::T( '12 Hours' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 DAY', CBTxt::T( '1 Day' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 DAYS', CBTxt::T( '3 Days' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+6 DAYS', CBTxt::T( '6 Days' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+12 DAYS', CBTxt::T( '12 Days' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 WEEK', CBTxt::T( '1 Week' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+2 WEEKS', CBTxt::T( '2 Weeks' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 WEEKS', CBTxt::T( '3 Weeks' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 MONTH', CBTxt::T( '1 Month' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+3 MONTHS', CBTxt::T( '3 Months' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+6 MONTHS', CBTxt::T( '6 Months' ) );
		$listDurations[]		=	moscomprofilerHTML::makeOption( '+1 YEAR', CBTxt::T( '1 Year' ) );
		$input['durations']		=	moscomprofilerHTML::selectList( $listDurations, 'durations', 'class="form-control"', 'value', 'text', null, 0, true, false, false );

		$reasonTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input block reason. If left blank will default to spam.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['reason']		=	'<textarea id="reason" name="reason" class="form-control" cols="40" rows="5"' . $reasonTooltip . '>' . htmlspecialchars( $this->input( 'post/reason', $row->get( 'reason', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '</textarea>';

		$banUserTooltip			=	cbTooltip( null, CBTxt::T( 'Ban the users profile using Community Builder moderator ban feature. Note normal ban notification will be sent with the ban.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['ban_user']		=	moscomprofilerHTML::yesnoSelectList( 'ban_user', 'class="form-control"' . $banUserTooltip, $this->input( 'post/ban_user', 0, GetterInterface::INT ) );

		$banReasonTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally input reason for profile ban.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['ban_reason']	=	'<textarea id="ban_reason" name="ban_reason" class="form-control" cols="40" rows="5"' . $banReasonTooltip . '>' . htmlspecialchars( $this->input( 'post/ban_reason', null, GetterInterface::STRING ) ) . '</textarea>';

		$blockUserTooltip		=	cbTooltip( null, CBTxt::T( 'Block the users profile using Joomla block state.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['block_user']	=	moscomprofilerHTML::yesnoSelectList( 'block_user', 'class="form-control"' . $blockUserTooltip, $this->input( 'post/block_user', 0, GetterInterface::INT ) );

		HTML_cbantispamBlock::showBlock( $row, $input, $type, $user, $blockUser, $this );
	}

	/**
	 * Saves a user block
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveBlock( $id, $user )
	{
		global $_CB_framework, $ueConfig;

		$type						=	$this->input( 'post/type', null, GetterInterface::STRING );

		$row						=	new BlockTable();

		if ( $type == 'account' ) {
			$blockUser				=	CBuser::getUserDataInstance( $this->input( 'post/value', 0, GetterInterface::INT ) );

			if ( ! $blockUser->get( 'id', 0, GetterInterface::INT ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'User does not exist.' ), 'error' );

				$this->showBlock( $id, $type, $user );
				return;
			}

			if ( Application::User( $blockUser->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
				CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}

			$new					=	true;
		} else {
			$blockUser				=	CBuser::getUserDataInstance( $this->input( 'post/user', 0, GetterInterface::INT ) );

			if ( $id ) {
				$row->load( (int) $id );
			}

			$type					=	$this->input( 'post/type', $row->get( 'type', null, GetterInterface::STRING ), GetterInterface::STRING );

			$row->set( 'type', $type );
			$row->set( 'value', $this->input( 'post/value', $row->get( 'value', null, GetterInterface::STRING ), GetterInterface::STRING ) );

			if ( in_array( $type, array( 'account', 'user' ) ) ) {
				$blockUser			=	CBuser::getUserDataInstance( $row->get( 'value', 0, GetterInterface::INT ) );

				if ( ! $blockUser->get( 'id', 0, GetterInterface::INT ) ) {
					$_CB_framework->enqueueMessage( CBTxt::T( 'User does not exist.' ), 'error' );

					$this->showBlock( $id, $type, $user );
					return;
				}

				if ( Application::User( $blockUser->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
					CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				}
			} elseif ( ( $type == 'email' ) && ( strpos( $row->get( 'value', null, GetterInterface::STRING ), '%' ) === false ) && ( ! cbIsValidEmail( $row->get( 'value', null, GetterInterface::STRING ) ) ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'Not a valid email address.' ), 'error' );

				$this->showWhitelist( $id, $type, $user );
				return;
			}

			$row->set( 'reason', $this->input( 'post/reason', $row->get( 'reason', null, GetterInterface::STRING ), GetterInterface::STRING ) );
			$row->set( 'date', $this->input( 'post/date', $row->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ), GetterInterface::STRING ) );
			$row->set( 'duration', $this->input( 'post/duration', $row->get( 'duration', null, GetterInterface::STRING ), GetterInterface::STRING ) );

			$new					=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );

			if ( $row->getError() || ( ! $row->check() ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'BLOCK_SAVE_FAILED', 'Block failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

				$this->showBlock( $id, $type, $user );
				return;
			}

			if ( $row->getError() || ( ! $row->store() ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'BLOCK_SAVE_FAILED', 'Block failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

				$this->showBlock( $id, $type, $user );
				return;
			}
		}

		$accountBlocked				=	false;

		if ( in_array( $type, array( 'account', 'user' ) ) ) {
			if ( isset( $ueConfig['allowUserBanning'] ) && $ueConfig['allowUserBanning'] ) {
				if ( $this->input( 'post/ban_user', 0, GetterInterface::INT ) && ( ! $blockUser->get( 'banned', 0, GetterInterface::INT ) ) ) {
					if ( ! $blockUser->banUser( 1, $user, $this->input( 'post/ban_reason', null, GetterInterface::STRING ) ) ) {
						if ( $type == 'account' ) {
							$_CB_framework->enqueueMessage( CBTxt::T( 'BLOCK_ACCOUNT_BAN_FAILED', 'Profile Ban failed to save! Error: [error]', array( '[error]' => $blockUser->getError() ) ), 'error' );
						} else {
							$_CB_framework->enqueueMessage( CBTxt::T( 'BLOCK_PROFILE_BAN_FAILED', 'Block saved successfully, but Profile Ban failed to save! Error: [error]', array( '[error]' => $blockUser->getError() ) ), 'error' );
						}

						$this->showBlock( $row->get( 'id', 0, GetterInterface::INT ), $type, $user );
						return;
					}
				}
			}

			if ( ( ( $type == 'account' ) || $this->input( 'post/block_user', 0, GetterInterface::INT ) ) && ( ! $blockUser->get( 'block', 0, GetterInterface::INT ) ) ) {
				$blockUser->set( 'block', 1 );

				if ( ! $blockUser->storeBlock() ) {
					if ( $type == 'account' ) {
						$_CB_framework->enqueueMessage( CBTxt::T( 'BLOCK_ACCOUNT_BLOCK_FAILED', 'Profile Block failed to save! Error: [error]', array( '[error]' => $blockUser->getError() ) ), 'error' );
					} else {
						$_CB_framework->enqueueMessage( CBTxt::T( 'BLOCK_PROFILE_BLOCK_FAILED', 'Block saved successfully, but Profile Block failed to save! Error: [error]', array( '[error]' => $blockUser->getError() ) ), 'error' );
					}

					$this->showBlock( $row->get( 'id', 0, GetterInterface::INT ), $type, $user );
					return;
				}

				$accountBlocked		=	true;
			}
		}

		if ( $new ) {
			if ( $accountBlocked ) {
				cbRedirect( 'index.php', CBTxt::T( 'Block created successfully!' ) );
			} else {
				CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Block created successfully!' ) );
			}
		} else {
			if ( $accountBlocked ) {
				cbRedirect( 'index.php', CBTxt::T( 'Block saved successfully!' ) );
			} else {
				CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Block saved successfully!' ) );
			}
		}
	}

	/**
	 * Deletes a user block
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteBlock( $id, $user )
	{
		$row	=	new BlockTable();

		$row->load( (int) $id );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'BLOCK_DELETE_FAILED', 'Block failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Block deleted successfully!' ) );
	}

	/**
	 * Displays whitelist user page
	 *
	 * @param int       $id
	 * @param string    $type
	 * @param UserTable $user
	 */
	public function showWhitelist( $id, $type, $user )
	{
		$whitelistUser			=	CBuser::getUserDataInstance( $this->input( 'user', 0, GetterInterface::INT ) );

		if ( Application::User( $whitelistUser->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$value					=	null;

		switch ( $type ) {
			case 'account':
			case 'user':
				$value			=	$whitelistUser->get( 'id', 0, GetterInterface::INT );
				break;
			case 'ip':
				$value			=	CBAntiSpam::getUserIP( $whitelistUser );
				break;
			case 'ip_range':
				$value			=	CBAntiSpam::getUserIP( $whitelistUser ) . ':' . CBAntiSpam::getUserIP( $whitelistUser );
				break;
			case 'email':
				$value			=	$whitelistUser->get( 'email', null, GetterInterface::STRING );
				break;
			case 'domain':
				$value			=	CBAntiSpam::getEmailDomain( $whitelistUser );
				break;
		}

		$row					=	new WhitelistTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		CBAntiSpam::getTemplate( 'whitelist' );

		$input					=	array();

		$listType				=	array();
		$listType[]				=	moscomprofilerHTML::makeOption( 'user', CBTxt::T( 'User' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'ip', CBTxt::T( 'IP Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'ip_range', CBTxt::T( 'IP Address Range' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'email', CBTxt::T( 'Email Address' ) );
		$listType[]				=	moscomprofilerHTML::makeOption( 'domain', CBTxt::T( 'Email Domain' ) );

		$type					=	$this->input( 'post/type', $row->get( 'type', $type, GetterInterface::STRING ), GetterInterface::STRING );
		$typeTooltip			=	cbTooltip( null, CBTxt::T( 'Select whitelist block type. Type determines what value should be supplied.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['type']			=	moscomprofilerHTML::selectList( $listType, 'type', 'class="form-control required"' . $typeTooltip, 'value', 'text', $type, 1, true, false, false );

		$valueTooltip			=	cbTooltip( null, CBTxt::T( 'Input whitelist value in relation to the type. User type use the users user_id (e.g. 42). IP Address type use a full valid IP Address (e.g. 192.168.0.1). IP Address Range type use two full valid IP Addresses separated by a colon (e.g. 192.168.0.1:192.168.0.100). Email type use a fill valid email address (e.g. invalid@cb.invalid). Email Domain type use a full email address domain after @ (e.g. example.com). Additionally IP Address, Email Address, and Email Domain types support % wildcard.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['value']			=	'<input type="text" id="value" name="value" value="' . htmlspecialchars( $this->input( 'post/value', $row->get( 'value', $value, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control required" size="25"' . $valueTooltip . ' />';

		$reasonTooltip			=	cbTooltip( null, CBTxt::T( 'Optionally input whitelist reason. Note this is for administrative purposes only.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['reason']		=	'<textarea id="reason" name="reason" class="form-control" cols="40" rows="5"' . $reasonTooltip . '>' . htmlspecialchars( $this->input( 'post/reason', $row->get( 'reason', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '</textarea>';

		HTML_cbantispamWhitelist::showWhitelist( $row, $input, $type, $user, $whitelistUser, $this );
	}

	/**
	 * Saves a user whitelist
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function saveWhitelist( $id, $user )
	{
		global $_CB_framework;

		$row					=	new WhitelistTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		$type					=	$this->input( 'post/type', $row->get( 'type', null, GetterInterface::STRING ), GetterInterface::STRING );

		$row->set( 'type', $type );
		$row->set( 'value', $this->input( 'post/value', $row->get( 'value', null, GetterInterface::STRING ), GetterInterface::STRING ) );

		if ( in_array( $type, array( 'account', 'user' ) ) ) {
			$whitelistUser		=	CBuser::getUserDataInstance( $row->get( 'value', 0, GetterInterface::INT ) );

			if ( ! $whitelistUser->get( 'id', 0, GetterInterface::INT ) ) {
				$_CB_framework->enqueueMessage( CBTxt::T( 'User does not exist.' ), 'error' );

				$this->showWhitelist( $id, $type, $user );
				return;
			}

			if ( Application::User( $whitelistUser->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
				CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}
		} elseif ( ( $type == 'email' ) && ( strpos( $row->get( 'value', null, GetterInterface::STRING ), '%' ) === false ) && ( ! cbIsValidEmail( $row->get( 'value', null, GetterInterface::STRING ) ) ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'Not a valid email address.' ), 'error' );

			$this->showWhitelist( $id, $type, $user );
			return;
		}

		$row->set( 'reason', $this->input( 'post/reason', $row->get( 'reason', null, GetterInterface::STRING ), GetterInterface::STRING ) );

		$new					=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'WHITELIST_SAVE_FAILED', 'Whitelist failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showWhitelist( $id, $type, $user );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'WHITELIST_SAVE_FAILED', 'Whitelist failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showWhitelist( $id, $type, $user );
			return;
		}

		if ( $new ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Whitelist created successfully!' ) );
		} else {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Whitelist saved successfully!' ) );
		}
	}

	/**
	 * Deletes a user whitelist
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteWhitelist( $id, $user )
	{
		$row	=	new WhitelistTable();

		$row->load( (int) $id );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'WHITELIST_DELETE_FAILED', 'Whitelist failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Whitelist deleted successfully!' ) );
	}

	/**
	 * Deletes a user attempt
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteAttempt( $id, $user )
	{
		$row	=	new AttemptTable();

		$row->load( (int) $id );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'ATTEMPT_DELETE_FAILED', 'Attempt failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Attempt deleted successfully!' ) );
	}

	/**
	 * Deletes a user log
	 *
	 * @param int       $id
	 * @param UserTable $user
	 */
	private function deleteLog( $id, $user )
	{
		$row	=	new LogTable();

		$row->load( (int) $id );

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ! $row->delete() ) {
			CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'LOG_DELETE_FAILED', 'Log failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		CBAntiSpam::returnRedirect( 'index.php', CBTxt::T( 'Log deleted successfully!' ) );
	}
}