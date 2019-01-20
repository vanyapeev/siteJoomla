<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AntiSpam;

use CBLib\Application\Application;
use CBLib\Registry\ParametersStore;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class Captcha extends ParametersStore
{
	/** @var array $asset */
	protected $asset					=	null;
	/** @var string $code */
	protected $code						=	null;
	/** @var string $answer */
	protected $answer					=	null;
	/** @var string $error */
	protected $error					=	null;

	/** @var array $defaults */
	protected $defaults					=	array(	'mode'								=>	'internal',
													'honeypot'							=>	true,
													'honeypot_name'						=>	'full_address',
													'internal_format'					=>	'png',
													'internal_audio'					=>	true,
													'internal_refresh'					=>	true,
													'internal_ajax'						=>	false,
													'internal_height'					=>	50,
													'internal_color_range'				=>	0,
													'internal_bg_color'					=>	'255,255,255',
													'internal_txt_color'				=>	'20,40,100',
													'internal_bg_noise'					=>	true,
													'internal_bg_noise_color'			=>	'20,40,100',
													'internal_fg_noise'					=>	true,
													'internal_fg_noise_color'			=>	'20,40,100',
													'internal_font'						=>	'EARWIGFA.ttf|*|monofont.ttf',
													'internal_rotation'					=>	13,
													'internal_offset'					=>	3,
													'internal_range'					=>	5,
													'internal_size'						=>	20,
													'internal_length'					=>	6,
													'internal_characters'				=>	'abcdefhijklmnopqrstuvwxyz',
													'internal_questions'				=>	"What is 2 plus 2?=4\nWhat is 1 times 6?=6\nWhat is 9 divide 3?=3\nAre you a Human?=Yes\nAre you a Bot?=No\nHow many words is this?=5\nHow many fingers on a hand?=5\nHow many toes on a foot?=5\nWhat is 10 add 10?=20\nWhat is 0 multiply 100?=0\nWhat is 5 minus 1?=4\nWhat is 2 add 2?=4\n4th letter of Test is?=t\n20, 81, 3; which is smallest?=3\n12, 31, 9; which is greatest?=31\nPurple, car, dog; which is a color?=Purple\nCat, plane, rock; which is an animal?=Cat\nIf tomorrow is Monday; what day is today?=Sunday\nTim, cat, dog; which is human?=Tim"
												);

	/**
	 * Constructor for captcha object
	 *
	 * @param null|string $asset
	 * @param null|string $mode
	 */
	public function __construct( $asset = null, $mode = null )
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup( 'user' );

		$this->asset		=	$asset;

		if ( ( $mode !== '' ) && ( $mode !== null ) && ( $mode !== '-1' ) ) {
			$this->set( 'mode', $mode );
		}

		$pluginParams		=	CBAntiSpam::getGlobalParams();

		foreach ( $this->defaults as $param => $default ) {
			if ( ( $param == 'mode' ) && $this->get( 'mode', null, GetterInterface::STRING ) ) {
				continue;
			}

			$value			=	$pluginParams->get( 'captcha_' . $param, $default, GetterInterface::STRING );

			if ( is_int( $default ) ) {
				$value		=	(int) $value;
			} elseif ( is_bool( $default ) ) {
				$value		=	(bool) $value;
			}

			$this->set( $param, $value );
		}
	}

	/**
	 * Reloads the captcha from session by id optionally exclude asset, code, or answer (for question captchas)
	 *
	 * @param null|string $id
	 * @param array       $exclude
	 * @return bool
	 */
	public function load( $id = null, $exclude = array() )
	{
		if ( ! $id ) {
			$id					=	$this->id();
		}

		$session				=	Application::Session()->subTree( 'captcha.' . $id );

		if ( $session->count() == 1 ) {
			$inherit			=	Application::Session()->get( 'captcha.' . $id, null, GetterInterface::STRING );

			if ( $inherit ) {
				return $this->load( $inherit, $exclude );
			}

			return false;
		}

		if ( $session->count() ) {
			if ( ! in_array( 'asset', $exclude ) ) {
				$this->asset	=	$session->get( 'asset', null, GetterInterface::STRING );
			}

			if ( ! in_array( 'code', $exclude ) ) {
				$this->code		=	$session->get( 'code', null, GetterInterface::STRING );
			}

			if ( ! in_array( 'answer', $exclude ) ) {
				$this->answer	=	$session->get( 'answer', null, GetterInterface::STRING );
			}

			parent::load( $session );

			return true;
		}

		return false;
	}

	/**
	 * Parses parameters into the captcha
	 *
	 * @param ParamsInterface|array|string $params
	 * @param null|string                  $prefix
	 * @return self
	 */
	public function parse( $params, $prefix = null )
	{
		if ( $params instanceof self ) {
			$this->asset		=	$params->asset();
			$this->code			=	$params->code();
			$this->answer		=	$params->answer();

			parent::load( $params->asArray() );
		} else {
			if ( is_array( $params ) ) {
				$params			=	new Registry( $params );
			}

			foreach ( $this->defaults as $param => $default ) {
				$value			=	$params->get( $prefix . $param, null, GetterInterface::STRING );

				if ( ( $value !== '' ) && ( $value !== null ) && ( $value !== '-1' ) ) {
					if ( is_int( $default ) ) {
						$value	=	(int) $value;
					} elseif ( is_bool( $default ) ) {
						$value	=	(bool) $value;
					}

					$this->set( $param, $value );
				}
			}
		}

		return $this;
	}

	/**
	 * Gets the captcha id
	 *
	 * @return null|string
	 */
	public function id()
	{
		if ( ! $this->asset() ) {
			return null;
		}

		return md5( 'captcha' . $this->asset() );
	}

	/**
	 * Gets the captcha asset
	 *
	 * @return null|string
	 */
	public function asset()
	{
		if ( ! $this->asset ) {
			return null;
		}

		return strtolower( trim( preg_replace( '/[^a-zA-Z0-9.]/i', '', $this->asset ) ) );
	}

	/**
	 * Gets the captcha code (for question based captcha this is the question)
	 *
	 * @return string
	 */
	public function code()
	{
		if ( $this->code === null ) {
			$code							=	null;
			$answer							=	null;

			switch( $this->get( 'mode', 'internal', GetterInterface::STRING ) ) {
				case 'joomla':
				case 'recaptcha':
				case 'recaptcha_invisible':
				case 'honeypot':
					$code					=	'';
					break;
				case 'question':
					$captchaQuestions		=	"What is 2 plus 2?=4\n"
											.	"What is 1 times 6?=6\n"
											.	"What is 9 divide 3?=3\n"
											.	"Are you a Human?=Yes\n"
											.	"Are you a Bot?=No\n"
											.	"How many words is this?=5\n"
											.	"How many fingers on a hand?=5\n"
											.	"How many toes on a foot?=5\n"
											.	"What is 10 add 10?=20\n"
											.	"What is 0 multiply 100?=0\n"
											.	"What is 5 minus 1?=4\n"
											.	"What is 2 add 2?=4\n"
											.	"4th letter of Test is?=t\n"
											.	"20, 81, 3; which is smallest?=3\n"
											.	"12, 31, 9; which is greatest?=31\n"
											.	"Purple, car, dog; which is a color?=Purple\n"
											.	"Cat, plane, rock; which is an animal?=Cat\n"
											.	"If tomorrow is Monday; what day is today?=Sunday\n"
											.	"Tim, cat, dog; which is human?=Tim";

					$questions				=	$this->get( 'internal_questions', $captchaQuestions, GetterInterface::STRING );

					if ( ! $questions ) {
						$questions			=	$captchaQuestions;
					}

					$questions				=	explode( "\n", $questions );
					$codes					=	array();

					foreach ( $questions as $question ) {
						$question			=	explode( '=', $question );
						$key				=	( isset( $question[0] ) ? trim( CBTxt::T( $question[0] ) ) : null );
						$value				=	( isset( $question[1] ) ? trim( CBTxt::T( $question[1] ) ) : null );

						if ( $key && $value ) {
							$codes[$key]	=	$value;
						}
					}

					if ( $codes ) {
						$code				=	array_rand( $codes, 1 );
						$answer				=	$codes[$code];
					}
					break;
				case 'math':
					$leftMin				=	$this->get( 'internal_math_left_min', 1, GetterInterface::INT );
					$leftMax				=	$this->get( 'internal_math_left_max', 10, GetterInterface::INT );
					$rightMin				=	$this->get( 'internal_math_left_min', 1, GetterInterface::INT );
					$rightMax				=	$this->get( 'internal_math_right_max', 10, GetterInterface::INT );

					$code					=	CBAntiSpam::getMathEquation( $answer, $leftMin, $leftMax, $rightMin, $rightMax );
					break;
				case 'internal':
				default:
					$length					=	$this->get( 'internal_length', 6, GetterInterface::INT );

					if ( ! $length ) {
						$length				=	6;
					}

					$characters				=	$this->get( 'internal_characters', 'abcdefhijklmnopqrstuvwxyz', GetterInterface::STRING );

					if ( ! $characters ) {
						$characters			=	'abcdefhijklmnopqrstuvwxyz';
					}

					for ( $i = 0, $n = (int) $length; $i < $n; $i++ ) {
						$code				.=	cbIsoUtf_substr( $characters, mt_rand( 0, cbIsoUtf_strlen( $characters ) -1 ), 1 );
					}
					break;
			}

			$this->code						=	$code;
			$this->answer					=	$answer;

			$this->cache();
		}

		return $this->code;
	}

	/**
	 * Gets the captcha answer (for question based captcha)
	 *
	 * @return string
	 */
	public function answer()
	{
		return $this->answer;
	}

	/**
	 * Gets the captcha validation code
	 *
	 * @return null|string
	 */
	public function error()
	{
		return $this->error;
	}

	/**
	 * Resets the captcha code
	 *
	 * @return self
	 */
	public function reset()
	{
		$this->code		=	null;
		$this->answer	=	null;
		$this->error	=	null;

		$this->cache();

		return $this;
	}

	/**
	 * Returns the user supplied captcha value
	 *
	 * @return string
	 */
	public function value()
	{
		switch( $this->get( 'mode', 'internal', GetterInterface::STRING ) ) {
			case 'recaptcha':
			case 'recaptcha_invisible':
				$value		=	Application::Input()->get( 'g-recaptcha-response', null, GetterInterface::STRING );
				break;
			case 'honeypot':
				$value		=	null;
				break;
			case 'joomla':
			case 'internal':
			case 'question':
			case 'math':
			default:
				$value		=	Application::Input()->get( $this->id(), null, GetterInterface::STRING );
				break;
		}

		return $value;
	}

	/**
	 * Validates the captcha from POST
	 *
	 * @param null $value
	 * @return bool
	 */
	public function validate( $value = null )
	{
		global $_CB_framework;

		$reset											=	true;
		$valid											=	false;

		if ( $value !== null ) {
			$reset										=	false;
		} else {
			$value										=	$this->value();
		}

		$params											=	CBAntiSpam::getGlobalParams();
		$ipAddress										=	CBAntiSpam::getCurrentIP();
		$useHoneypot									=	$this->get( 'honeypot', true, GetterInterface::BOOLEAN );
		$mode											=	$this->get( 'mode', 'internal', GetterInterface::STRING );

		switch( $mode ) {
			case 'joomla':
				$jPlugin								=	$params->get( 'captcha_joomla_plugin', null, GetterInterface::STRING );

				if ( ! $jPlugin ) {
					$jPlugin							=	$_CB_framework->getCfg( 'captcha' );
				}

				if ( $jPlugin ) {
					$jCaptcha							=	\JCaptcha::getInstance( $jPlugin, array( 'namespace' => $this->asset() ) );

					if ( $jCaptcha ) {
						$valid							=	$jCaptcha->checkAnswer( $value );
					}
				}
				break;
			case 'recaptcha':
			case 'recaptcha_invisible':
				$recaptchaKey							=	$params->get( 'captcha_recaptcha_site_key', null, GetterInterface::STRING );
				$recaptchaSecret						=	$params->get( 'captcha_recaptcha_secret_key', null, GetterInterface::STRING );
				$recaptchaInvisibleKey					=	$params->get( 'captcha_recaptcha_invisible_site_key', null, GetterInterface::STRING );
				$recaptchaInvisibleSecret				=	$params->get( 'captcha_recaptcha_invisible_secret_key', null, GetterInterface::STRING );

				if ( ( ( $mode == 'recaptcha' ) && $recaptchaKey && $recaptchaSecret ) || ( ( $mode == 'recaptcha_invisible' ) && $recaptchaInvisibleKey && $recaptchaInvisibleSecret ) ) {
					$client								=	new \GuzzleHttp\Client();

					try {
						$body							=	array(	'secret'	=>	( $mode == 'recaptcha_invisible' ? $recaptchaInvisibleSecret : $recaptchaSecret ),
																	'response'	=>	$value
																);

						if ( $ipAddress ) {
							$body['remoteip']			=	$ipAddress;
						}

						$result							=	$client->get( 'https://www.google.com/recaptcha/api/siteverify', array( 'query' => $body ) );

						if ( $result->getStatusCode() == 200 ) {
							$response					=	$result->json();

							if ( isset( $response['success'] ) && ( $response['success'] == true ) ) {
								$valid					=	true;
							} elseif ( isset( $response['error-codes'] ) ) {
								$this->error			=	implode( ', ', $response['error-codes'] );
							}
						} else {
							$this->error				=	CBTxt::T( 'Failed to reach Google reCaptcha verify server.' );
						}
					} catch ( \Exception $e ) {
						$this->error					=	$e->getMessage();
					}
				} else {
					$valid								=	true;
				}
				break;
			case 'honeypot':
				$useHoneypot							=	true;
				$valid									=	true;
				break;
			case 'question':
			case 'math':
				$answer									=	$this->answer();

				if ( $answer && ( cbutf8_strtolower( $answer ) == cbutf8_strtolower( $value ) ) ) {
					$valid								=	true;
				}
				break;
			case 'internal':
			default:
				$code									=	$this->code();

				if ( $code && ( cbutf8_strtolower( $code ) == cbutf8_strtolower( $value ) ) ) {
					$valid								=	true;
				}
				break;
		}

		if ( $useHoneypot ) {
			$honeypot									=	$this->get( 'honeypot_name', 'full_address', GetterInterface::STRING );

			if ( ! $honeypot ) {
				$honeypot								=	'full_address';
			}

			if ( Application::Input()->get( $honeypot, null, GetterInterface::STRING ) ) {
				$valid									=	false;
			}
		}

		if ( $reset ) {
			$this->reset();

			$blocked									=	CBAntiSpam::getBlock( null, $ipAddress );
			$message									=	$params->get( 'captcha_autoblock_msg', 'Your captcha attempt has been blocked. Reason: [reason]' );

			if ( $blocked ) {
				if ( $message ) {
					$extras								=	array(	'[duration]'	=>	ucwords( strtolower( str_replace( array( '+', '-' ), '', $blocked->get( 'duration', null, GetterInterface::STRING ) ) ) ),
																	'[date]'		=>	$blocked->get( 'date', null, GetterInterface::STRING ) . ' UTC',
																	'[expire]'		=>	$blocked->expiry() . ( $blocked->get( 'duration', null, GetterInterface::STRING ) ? ' UTC' : null )
																);

					$extras								=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'CAPTCHA_BLOCK_REASON', ( $blocked->get( 'reason', null, GetterInterface::STRING ) ? $blocked->get( 'reason', null, GetterInterface::STRING ) : 'Spam.' ), $extras ) ) );

					$this->error						=	CBTxt::T( 'CAPTCHA_BLOCK_MESSAGE', $message, $extras );
				}

				$valid									=	false;
			} else {
				if ( ! $valid ) {
					$blocked							=	CBAntiSpam::logAttempt( 'captcha' );

					if ( $blocked ) {
						if ( $message ) {
							$extras						=	array(	'[duration]'	=>	ucwords( strtolower( str_replace( array( '+', '-' ), '', $blocked->get( 'duration', null, GetterInterface::STRING ) ) ) ),
																	'[date]'		=>	$blocked->get( 'date', null, GetterInterface::STRING ) . ' UTC',
																	'[expire]'		=>	$blocked->expiry() . ( $blocked->get( 'duration', null, GetterInterface::STRING ) ? ' UTC' : null )
																);

							$extras						=	array_merge( $extras, array( '[reason]' => CBTxt::T( 'CAPTCHA_BLOCK_REASON', ( $blocked->get( 'reason', null, GetterInterface::STRING ) ? $blocked->get( 'reason', null, GetterInterface::STRING ) : 'Spam.' ), $extras ) ) );

							$this->error				=	CBTxt::T( 'CAPTCHA_BLOCK_MESSAGE', $message, $extras );
						}

						$valid							=	false;
					}
				} else {
					CBAntiSpam::logIPAddress( 0, 'captcha' );
				}
			}
		}

		return $valid;
	}

	/**
	 * Returns the captcha generated image
	 *
	 * @return null|string
	 */
	public function image()
	{
		global $_CB_framework, $_PLUGINS;

		CBAntiSpam::getTemplate( 'captcha', false, true, false );

		static $absPath				=	null;

		if ( ! $absPath ) {
			$plugin					=	$_PLUGINS->getLoadedPlugin( 'user', 'cbantispam' );
			$absPath				=	$_PLUGINS->getPluginPath( $plugin );
		}

		$image						=	null;

		switch( $this->get( 'mode', 'internal', GetterInterface::STRING ) ) {
			case 'joomla':
			case 'recaptcha':
			case 'recaptcha_invisible':
			case 'honeypot':
				$image				=	null;
				break;
			case 'internal':
			case 'question':
			case 'math':
			default:
				if ( $this->get( 'internal_ajax', false, GetterInterface::BOOLEAN ) && ( ! $this->get( 'loaded', false, GetterInterface::BOOLEAN ) ) ) {
					$image			=	'<span class="cbantispamCaptchaImage" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"></span>';
				} else {
					$height			=	$this->get( 'internal_height', 50, GetterInterface::INT );

					if ( ! $height ) {
						$height		=	50;
					}

					$image			=	'<img src="' . $_CB_framework->pluginClassUrl( 'cbantispam', true, array( 'action' => 'captcha', 'func' => 'image', 'id' => $this->id() ), 'raw', 0, true ) . '" alt="' . htmlspecialchars( CBTxt::T( 'Captcha' ) ) . '"' . ( $height ? ' style="height: ' . (int) $height . 'px;"' : null ) . ' class="cbantispamCaptchaImage cbantispamCaptchaImage' . htmlspecialchars( $this->id() ) . '" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '" />';
				}
				break;
		}

		return $image;
	}

	/**
	 * Returns the captcha generated audio
	 *
	 * @return null|string
	 */
	public function audio()
	{
		global $_CB_framework;

		CBAntiSpam::getTemplate( 'captcha', false, true, false );

		$audio				=	null;

		switch( $this->get( 'mode', 'internal', GetterInterface::STRING ) ) {
			case 'joomla':
			case 'recaptcha':
			case 'recaptcha_invisible':
			case 'honeypot':
				$audio		=	null;
				break;
			case 'internal':
			case 'question':
			case 'math':
			default:
				if ( $this->get( 'internal_ajax', false, GetterInterface::BOOLEAN ) && ( ! $this->get( 'loaded', false, GetterInterface::BOOLEAN ) ) ) {
					$audio	=	'<span class="cbantispamCaptchaAudioFile" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"></span>';
				} else {
					$audio	=	'<audio src="' . $_CB_framework->pluginClassUrl( 'cbantispam', true, array( 'action' => 'captcha', 'func' => 'audio', 'id' => $this->id() ), 'raw', 0, true ) . '" type="audio/mpeg" class="cbantispamCaptchaAudioFile cbantispamCaptchaAudioFile' . htmlspecialchars( $this->id() ) . ' hidden" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"></audio>';
				}
				break;
		}

		return $audio;
	}

	/**
	 * Returns the captcha form input
	 *
	 * @param string $attributes
	 * @param string $classes
	 * @return null|string
	 */
	public function input( $attributes = null, $classes = 'form-control' )
	{
		CBAntiSpam::getTemplate( 'captcha', false, true, false );

		$useHoneypot					=	$this->get( 'honeypot', true, GetterInterface::BOOLEAN );

		switch( $this->get( 'mode', 'internal', GetterInterface::STRING ) ) {
			case 'joomla':
			case 'recaptcha':
			case 'recaptcha_invisible':
				$input					=	null;
				break;
			case 'honeypot':
				$useHoneypot			=	true;
				$input					=	null;
				break;
			case 'internal':
			case 'question':
			case 'math':
			default:
				if ( $this->get( 'internal_ajax', false, GetterInterface::BOOLEAN ) && ( ! $this->get( 'loaded', false, GetterInterface::BOOLEAN ) ) ) {
					$input			=	'<span class="cbantispamCaptchaInput" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"' . ( $classes ? ' data-cbantispam-classes="' . htmlspecialchars( $classes ) . '"' : null ) . ( $attributes ? ' ' . trim( $attributes ) : null ) . '></span>';
				} else {
					if ( $this->get( 'loaded', false, GetterInterface::BOOLEAN ) ) {
						$attributes		=	null;
						$classes		=	null;
					}

					$size				=	$this->get( 'internal_size', 20, GetterInterface::INT );

					$input				=	'<input type="text" name="' . htmlspecialchars( $this->id() ) . '" value=""' . ( $size ? ' size="' . (int) $size . '"' : null ) . ' class="cbantispamCaptchaInput cbantispamCaptchaInput' . htmlspecialchars( $this->id() ) . ' required' . ( $classes ? ' ' . htmlspecialchars( $classes ) : null ) . '" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"' . $attributes . '>';
				}
				break;
		}

		if ( $useHoneypot && ( ! $this->get( 'loaded', false, GetterInterface::BOOLEAN ) ) ) {
			$honeypot					=	$this->get( 'honeypot_name', 'full_address', GetterInterface::STRING );

			if ( ! $honeypot ) {
				$honeypot				=	'full_address';
			}

			$input						.=	'<div class="cbantispamCaptchaInput">'
										.		'<input type="text" name="' . htmlspecialchars( $honeypot ) . '" value="" class="form-control">'
										.	'</div>';
		}

		return $input;
	}

	/**
	 * Displays the captcha
	 *
	 * @return null|string
	 */
	public function captcha()
	{
		global $_CB_framework;

		CBAntiSpam::getTemplate( 'captcha', false, true, false );

		$this->code();

		$params										=	CBAntiSpam::getGlobalParams();
		$mode										=	$this->get( 'mode', 'internal', GetterInterface::STRING );
		$return										=	null;

		switch( $mode ) {
			case 'joomla':
				$jPlugin							=	$params->get( 'captcha_joomla_plugin', null, GetterInterface::STRING );

				if ( ! $jPlugin ) {
					$jPlugin						=	$_CB_framework->getCfg( 'captcha' );
				}

				if ( $jPlugin ) {
					$jCaptcha						=	\JCaptcha::getInstance( $jPlugin, array( 'namespace' => $this->asset() ) );

					if ( $jCaptcha ) {
						$return						=	$jCaptcha->display( $this->id(), $this->id(), 'class="required"' );
					}
				}
				break;
			case 'recaptcha_invisible':
			case 'recaptcha':
				$recaptchaKey						=	$params->get( 'captcha_recaptcha_site_key', null, GetterInterface::STRING );
				$recaptchaSecret					=	$params->get( 'captcha_recaptcha_secret_key', null, GetterInterface::STRING );
				$recaptchaInvisibleKey				=	$params->get( 'captcha_recaptcha_invisible_site_key', null, GetterInterface::STRING );
				$recaptchaInvisibleSecret			=	$params->get( 'captcha_recaptcha_invisible_secret_key', null, GetterInterface::STRING );

				if ( $mode == 'recaptcha_invisible' ) {
					if ( ( ! $recaptchaInvisibleKey ) || ( ! $recaptchaInvisibleSecret ) ) {
						return null;
					}
				} else {
					if ( ( ! $recaptchaKey ) || ( ! $recaptchaSecret ) ) {
						return null;
					}
				}

				static $JS_loaded					=	0;

				if ( ! $JS_loaded++ ) {
					$languages						=	array(	'ar', 'af', 'am', 'hy', 'az', 'eu', 'bn', 'bg', 'ca', 'zh-HK', 'zh-CN', 'zh-TW',
																'hr', 'cs', 'da', 'nl', 'en-GB', 'en', 'et', 'fil', 'fi', 'fr', 'fr-CA', 'gl', 'ka',
																'de', 'de-AT', 'de-CH', 'el', 'gu', 'iw', 'hi', 'hu', 'is', 'id', 'it', 'ja', 'kn',
																'ko', 'lo', 'lv', 'lt', 'ms', 'ml', 'mr', 'mn', 'no', 'fa', 'pl', 'pt', 'pt-BR', 'pt-PT',
																'ro', 'ru', 'sr', 'si', 'sk', 'sl', 'es', 'es-419', 'sw', 'sv', 'ta', 'te', 'th', 'tr', 'uk', 'ur', 'vi', 'zu'
															);
					$language						=	$params->get( 'captcha_recaptcha_lang', 'en', GetterInterface::STRING );

					if ( $language == 'auto' ) {
						$languageTag				=	Application::Cms()->getLanguageTag();

						if ( in_array( $languageTag, $languages ) ) {
							$language				=	$languageTag;
						} else {
							list( $languageCode )	=	explode( '-', Application::Cms()->getLanguageTag() );

							if ( in_array( $languageCode, $languages ) ) {
								$language			=	$languageCode;
							}
						}

						if ( $language == 'auto' ) {
							$language				=	null;
						}
					}

					$scheme							=	( ( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) ) ? 'https' : 'http' );

					$jQuery							=	"$.loadCBAntiSpamRecaptcha = function() {";

					if ( $recaptchaKey && $recaptchaSecret ) {
						$jQuery						.=	"$( '.cbantispamRecaptcha' ).each( function() {"
													.		"grecaptcha.render( $( this ).get( 0 ), {"
													.			"sitekey: '" . addslashes( $recaptchaKey ) . "',"
													.			"theme: '" . addslashes( $params->get( 'captcha_recaptcha_theme', 'light', GetterInterface::STRING ) ) . "',"
													.			"size: '" . addslashes( $params->get( 'captcha_recaptcha_size', 'normal', GetterInterface::STRING ) ) . "'"
													.		"});"
													.	"});";
					}

					if ( $recaptchaInvisibleKey && $recaptchaInvisibleSecret ) {
						$jQuery						.=	"$( '.cbantispamRecaptchaInvisible' ).each( function() {"
													.		"var captcha = $( this );"
													.		"var form = captcha.closest( 'form' );"
													.		"if ( ! form.length ) {"
													.			"return;"
													.		"}"
													.		"var widget = grecaptcha.render( captcha.get( 0 ), {"
													.			"sitekey: '" . addslashes( $recaptchaInvisibleKey ) . "',"
													.			"badge: '" . addslashes( $params->get( 'captcha_recaptcha_invisible_badge', 'inline', GetterInterface::STRING ) ) . "',"
													.			"size: 'invisible',"
													.			"callback: function( token ) {"
													.				"if ( ! captcha.hasClass( 'preview' ) ) {"
													.					"captcha.addClass( 'done' );"
													.					"form.submit();"
													.				"}"
													.			"}"
													.		"});"
													.		"if ( ! captcha.hasClass( 'preview' ) ) {"
													.			"if ( form.hasClass( 'cbValidation' ) ) {"
													.				"form.on( 'cbvalidate.validate', function( e, cbvalidate, valid ) {"
													.					"if ( valid ) {"
													.						"grecaptcha.execute( widget );"
													.					"}"
													.				"}).on( 'submit', function( e ) {"
													.					"if ( ! captcha.hasClass( 'done' ) ) {"
													.						"e.preventDefault();"
													.					"}"
													.				"});"
													.			"} else {"
													.				"form.on( 'submit', function( e ) {"
													.					"if ( ! captcha.hasClass( 'done' ) ) {"
													.						"e.preventDefault();"
													.						"grecaptcha.execute( widget );"
													.					"}"
													.				"});"
													.			"}"
													.		"}"
													.	"});";
					}

					$jQuery							.=	"};";

					$_CB_framework->outputCbJQuery( $jQuery );

					$js								=	"function loadCBAntiSpamRecaptcha() {"
													.		"cbjQuery.loadCBAntiSpamRecaptcha();"
													.	"};";

					$_CB_framework->document->addHeadScriptUrl( $scheme . '://www.google.com/recaptcha/api.js?onload=loadCBAntiSpamRecaptcha&render=explicit' . ( $language ? '&hl=' . $language : null ), false, $js );
				}

				if ( $mode == 'recaptcha_invisible' ) {
					$class							=	'cbantispamRecaptchaInvisible';
				} else {
					$class							=	'cbantispamRecaptcha';
				}

				$return								=	'<div class="' . htmlspecialchars( $class ). ' ' . htmlspecialchars( $class . $this->id() ) . ( $this->asset() == 'preview' ? ' preview' : null ) . '" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '" style="display: inline-block;"></div>';
				break;
			case 'honeypot':
				$return								=	null;
				break;
			case 'internal':
			case 'question':
			case 'math':
			default:
				static $JS_loaded					=	0;

				if ( ! $JS_loaded++ ) {
					$js								=	"var captchaRefreshHandler = function() {"
													.		"var id = $( this ).data( 'cbantispam-captcha' );"
													.		"var img = $( '.cbantispamCaptchaImage' + id ).first();"
													.		"var url = img.attr( 'src' ).replace( /(&ver=[0-9]+)*/ig, '' );"
													.		"var ver = Math.floor( Math.random() * 10000 );"
													.		"$( '.cbantispamCaptchaImage' + id ).attr( 'src', url + '&ver=' + ver ).on( 'load', function() {"
													.			"var audio = $( '.cbantispamCaptchaAudioFile' + id );"
													.			"if ( audio.length ) {"
													.				"audio.first().get( 0 ).pause();"
													.				"audio.attr( 'src', audio.attr( 'src' ).replace( /(&ver=[0-9]+)*/ig, '' ) + '&ver=' + ver );"
													.			"}"
													.			"$( '.cbantispamCaptchaInput' + id ).val( '' );"
													.		"});"
													.	"};"
													.	"var captchaAudioHandler = function() {"
													.		"var icon = $( this );"
													.		"var id = $( this ).data( 'cbantispam-captcha' );"
													.		"var audio = $( '.cbantispamCaptchaAudioFile' + id );"
													.		"if ( audio.length ) {"
													.			"audio = audio.first().get( 0 );"
													.			"if ( audio.paused ) {"
													.				"audio.currentTime = 0;"
													.				"audio.play();"
													.				"icon.removeClass( 'fa-volume-up' ).addClass( 'fa-pause' );"
													.			"} else {"
													.				"audio.pause();"
													.				"icon.removeClass( 'fa-pause' ).addClass( 'fa-volume-up' );"
													.			"}"
													.			"$( audio ).off( 'pause.antispam' ).on( 'pause.antispam', function() {"
													.				"icon.removeClass( 'fa-pause' ).addClass( 'fa-volume-up' );"
													.			"});"
													.		"}"
													.	"};"
													.	"$( 'span.cbantispamCaptcha[data-cbantispam-captcha]' ).each( function() {"
													.		"var id = $( this ).data( 'cbantispam-captcha' );"
													.		"var captcha = $( this );"
													.		"$.ajax({"
													.			"url: '" . $_CB_framework->pluginClassUrl( 'cbantispam', false, array( 'action' => 'captcha', 'func' => 'load' ), 'raw', 0, true ) . "',"
													.			"data: { id: id },"
													.			"type: 'GET',"
													.			"dataType: 'json',"
													.			"cache: false"
													.		"}).fail( function( jqXHR, textStatus, errorThrown ) {"
													.			"captcha.find( '.cbantispamCaptchaLoading' ).removeClass( 'fa-spinner fa-pulse' ).addClass( 'fa-warning text-danger' );"
													.		"}).done( function( data, textStatus, jqXHR ) {"
													.			"if ( ( typeof data.input !== 'undefined' ) && data.input ) {"
													.				"var inputPlaceholder = $( 'span.cbantispamCaptchaInput[data-cbantispam-captcha=\"' + id + '\"]' );"
													.				"inputPlaceholder.removeData( 'cbantispam-captcha' );"
													.				"var classes = inputPlaceholder.data( 'cbantispam-classes' );"
													.				"inputPlaceholder.removeData( 'cbantispam-classes' );"
													.				"var newInput = $( data.input );"
													.				"inputPlaceholder.replaceWith( newInput );"
													.				"newInput.data( inputPlaceholder.data() );"
													.				"newInput.addClass( classes );"
													.			"}"
													.			"if ( ( typeof data.image !== 'undefined' ) && data.image ) {"
													.				"$( 'span.cbantispamCaptchaImage[data-cbantispam-captcha=\"' + id + '\"]' ).replaceWith( data.image );"
													.			"}"
													.			"if ( ( typeof data.audio !== 'undefined' ) && data.audio ) {"
													.				"$( 'span.cbantispamCaptchaAudioFile[data-cbantispam-captcha=\"' + id + '\"]' ).replaceWith( data.audio );"
													.			"}"
													.			"if ( ( typeof data.captcha !== 'undefined' ) && data.captcha ) {"
													.				"var newCaptcha = $( data.captcha );"
													.				"captcha.replaceWith( newCaptcha );"
													.				"newCaptcha.on( 'click', '.cbantispamCaptchaRefresh', captchaRefreshHandler );"
													.				"newCaptcha.on( 'click', '.cbantispamCaptchaAudio', captchaAudioHandler );"
													.			"}"
													.		"});"
													.	"});"
													.	"$( '.cbantispamCaptchaRefresh' ).on( 'click', captchaRefreshHandler );"
													.	"$( '.cbantispamCaptchaAudio' ).on( 'click', captchaAudioHandler );";

					$_CB_framework->outputCbJQuery( $js );
				}

				if ( $this->get( 'internal_ajax', false, GetterInterface::BOOLEAN ) && ( ! $this->get( 'loaded', false, GetterInterface::BOOLEAN ) ) ) {
					$return							=	'<span class="cbantispamCaptcha" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"><span class="cbantispamCaptchaLoading fa fa-spinner fa-pulse"></span></span>';
				} else {
					if ( $this->asset() == 'preview' ) {
						$audio						=	false;
						$refresh					=	false;
					} else {
						$audio						=	( in_array( $mode, array( 'question', 'math' ) ) ? false : $this->get( 'internal_audio', true, GetterInterface::BOOLEAN ) );
						$refresh					=	$this->get( 'internal_refresh', true, GetterInterface::BOOLEAN );
					}

					$return							=	'<div class="cbantispamCaptcha cbantispamCaptcha' . htmlspecialchars( $this->id() ) . ' clearfix" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '" style="' . ( $this->asset() == 'preview' ? 'display: inline-block;' : 'margin-bottom: 5px;' ) . '">';

					if ( $audio || $refresh ) {
						$return						.=		'<div class="cbantispamCaptchaImageContaner pull-left" style="margin-right: 5px; max-width: 90%;">' . $this->image() . '</div>'
													.		'<div class="cbantispamCaptchaButtons pull-left" style="max-width: 10%;">';

						if ( $refresh ) {
							$return					.=			'<div class="cbantispamCaptchaRefreshButton">'
													.				'<a href="javascript:void(0);" class="cbantispamCaptchaRefresh cbantispamCaptchaRefresh' . htmlspecialchars( $this->id() ) . ' fa fa-refresh" style="vertical-align: top; cursor: pointer;" title="' . htmlspecialchars( CBTxt::T( 'Refresh Captcha' ) ) . '" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"></a>'
													.			'</div>';
						}

						if ( $audio ) {
							$return					.=			'<div class="cbantispamCaptchaAudioButton">'
													.				'<a href="javascript:void(0);" class="cbantispamCaptchaAudio cbantispamCaptchaAudio' . htmlspecialchars( $this->id() ) . ' fa fa-volume-up" style="vertical-align: bottom; cursor: pointer;" title="' . htmlspecialchars( CBTxt::T( 'Listen to Captcha' ) ) . '" data-cbantispam-captcha="' . htmlspecialchars( $this->id() ) . '"></a>'
													.				$this->audio()
													.			'</div>';
						}

						$return						.=		'</div>';
					} else {
						$return						.=		$this->image();
					}

					$return							.=	'</div>';
				}
				break;
		}

		return $return;
	}

	/**
	 * Returns an array of the captcha variables
	 *
	 * @return array
	 */
	public function asArray()
	{
		$params				=	parent::asArray();
		$params['asset']	=	$this->asset();
		$params['code']		=	$this->code();
		$params['answer']	=	$this->answer();

		return $params;
	}

	/**
	 * Caches the captcha into session; this is normally only done on creation or parse to preserve parameters between loads
	 * It is not advised to call this manually unless captcha parameters have changed after creation and desired result is for them to persist
	 *
	 * @return self
	 */
	public function cache()
	{
		if ( ( ! $this->id() ) || ( $this->asset() == 'preview' ) ) {
			return $this;
		}

		$session	=	Application::Session();
		$captcha	=	$session->subTree( 'captcha' );

		$captcha->set( $this->id(), $this->asArray() );

		$session->set( 'captcha', $captcha->asArray() );

		return $this;
	}
}