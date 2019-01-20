<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions;

use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;
use CBLib\Input\Get;
use CB\Plugin\AutoActions\Table\AutoActionTable;

defined('CBLIB') or die();

class CBAutoActions
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbautoactions' );
			$params		=	new Registry();

			if ( $plugin ) {
				$params->load( $plugin->params );
			}
		}

		return $params;
	}

	/**
	 * Attempts to execute code from a string
	 *
	 * @param string               $code
	 * @param AutoActionTable|null $autoaction
	 * @param UserTable|null       $user
	 * @param array|null           $variables
	 * @param mixed|null           $content
	 * @return string|null
	 */
	static public function outputCode( $code, &$autoaction = null, &$user = null, &$variables = null, &$content = null )
	{
		if ( ! $code ) {
			return null;
		}

		static $canCreate		=	null;

		if ( $canCreate === null ) {
			$canCreate			=	true;

			if ( version_compare( phpversion(), '7.2.0', '>=' ) || ( ! function_exists( 'create_function' ) ) ) {
				$canCreate		=	false;
			}
		}

		// For B/C:
		/** @noinspection PhpUnusedLocalVariableInspection */
		$trigger				=	$autoaction;
		/** @noinspection PhpUnusedLocalVariableInspection */
		$vars					=	$variables;

		ob_start();
		if ( $canCreate ) {
			$function			=	create_function( '$autoaction,$user,$variables,$content,$trigger,$vars', $code );
			$return				=	$function( $autoaction, $user, $variables, $content, $trigger, $vars );
		} else {
			$temp				=	tmpfile();

			fwrite( $temp, "<?php \n" . $code  );

			$tempData			=	stream_get_meta_data( $temp );

			$return				=	include $tempData['uri'];

			fclose( $temp );

			if ( ! is_string( $return ) ) {
				$return			=	null;
			}
		}
		$echos					=	ob_get_contents();
		ob_end_clean();

		if ( $echos && ( is_string( $return ) || ( $return === '' ) || ( $return === null ) ) ) {
			$return				=	$echos . $return;
		}

		return $return;
	}

	/**
	 * Trys to finder a user object in an array of variables
	 *
	 * @param array $variables
	 * @return UserTable
	 */
	static public function getUser( $variables )
	{
		$user						=	null;

		// Lets first try to find a user object in the variables directly:
		foreach ( $variables as $variable ) {
			if ( is_object( $variable ) && ( $variable instanceof UserTable ) ) {
				$user				=	$variable;
				break;
			}
		}

		// We failed to find a user object so lets try to parse for one from the variables:
		if ( ! $user ) {
			foreach ( $variables as $variable ) {
				$variable			=	self::prepareUser( $variable );

				if ( ! $variable instanceof UserTable ) {
					$variableUser	=	\CBuser::getUserDataInstance( $variable );

					if ( $variableUser->get( 'id', 0, GetterInterface::INT ) ) {
						$user		=	$variableUser;
						break;
					}
				} elseif ( $variable->get( 'id', 0, GetterInterface::INT ) ) {
					$user			=	$variable;
					break;
				}
			}
		}

		// Still can't find a user so lets just fallback to self:
		if ( ! $user ) {
			$user					=	\CBuser::getMyUserDataInstance();
		}

		return $user;
	}

	/**
	 * Trys to load a user object from a variable
	 *
	 * @param object|int $userVariable
	 * @param array      $skip
	 * @return UserTable|int
	 */
	static public function prepareUser( $userVariable, $skip = array() )
	{
		$user				=	\CBuser::getUserDataInstance( 0 );

		if ( is_object( $userVariable ) ) {
			if ( $userVariable instanceof UserTable ) {
				$user		=	$userVariable;
			} elseif ( isset( $userVariable->user_id ) && ( ! in_array( 'user_id', $skip ) ) ) {
				$user		=	(int) $userVariable->user_id;
			} elseif ( isset( $userVariable->user ) && ( ! in_array( 'user', $skip ) ) ) {
				$user		=	(int) $userVariable->user;
			} elseif ( isset( $userVariable->id ) && ( ! in_array( 'id', $skip ) ) ) {
				$user		=	(int) $userVariable->id;
			}
		} elseif ( is_int( $userVariable ) && ( ! in_array( 'int', $skip ) ) ) {
			$user			=	$userVariable;
		}

		return $user;
	}

	/**
	 * Parses substitution extras array from available variables
	 *
	 * @param array $variables
	 * @return array
	 */
	static public function getExtras( $variables = array() )
	{
		$extras							=	array();

		foreach ( $variables as $key => $variable ) {
			if ( is_object( $variable ) || is_array( $variable ) ) {
				/** @var array|object $variable */
				if ( is_object( $variable ) ) {
					if ( $variable instanceof ParamsInterface ) {
						$paramsArray	=	$variable->asArray();
					} else {
						$paramsArray	=	get_object_vars( $variable );
					}
				} else {
					$paramsArray		=	$variable;
				}

				self::prepareExtras( $key, $paramsArray, $extras );
			} else {
				$extras[$key]			=	$variable;
			}
		}

		$get							=	Application::Input()->getNamespaceRegistry( 'get' );

		if ( $get ) {
			self::prepareExtras( 'get', $get->asArray(), $extras );
		}

		$post							=	Application::Input()->getNamespaceRegistry( 'post' );

		if ( $post ) {
			self::prepareExtras( 'post', $post->asArray(), $extras );
		}

		$files							=	Application::Input()->getNamespaceRegistry( 'files' );

		if ( $files ) {
			self::prepareExtras( 'files', $files->asArray(), $extras );
		}

		$cookie							=	Application::Input()->getNamespaceRegistry( 'cookie' );

		if ( $cookie ) {
			self::prepareExtras( 'cookie', $cookie->asArray(), $extras );
		}

		$server							=	Application::Input()->getNamespaceRegistry( 'server' );

		if ( $server ) {
			self::prepareExtras( 'server', $server->asArray(), $extras );
		}

		$env							=	Application::Input()->getNamespaceRegistry( 'env' );

		if ( $env ) {
			self::prepareExtras( 'env', $env->asArray(), $extras );
		}

		$session						=	Application::Session();

		if ( $session ) {
			self::prepareExtras( 'session', $session->asArray(), $extras );
		}

		return $extras;
	}

	/**
	 * Converts array or object into pathed extras substitutions
	 *
	 * @param string       $prefix
	 * @param array|object $items
	 * @param array        $extras
	 * @param int          $depth
	 */
	static public function prepareExtras( $prefix, $items, &$extras, $depth = 0 )
	{
		if ( $depth > 5 ) {
			return;
		}

		foreach ( $items as $k => $v ) {
			if ( $k[0] == '_' ) {
				continue;
			}

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

			if ( ( $k == 'params' ) && ( ! is_object( $v ) ) && ( ! is_array( $v ) ) ) {
				$params					=	new Registry( $v );

				$v						=	$params->asArray();
			}

			$k							=	'_' . ltrim( str_replace( ' ', '_', trim( strtolower( $k ) ) ), '_' );

			if ( ( ! is_object( $v ) ) && ( ! is_array( $v ) ) ) {
				$extras[$prefix . $k]	=	$v;
			} elseif ( $v ) {
				$depth++;

				if ( is_object( $v ) ) {
					/** @var object $v */
					$subItems			=	get_object_vars( $v );
				} else {
					$subItems			=	$v;
				}

				self::prepareExtras( $prefix . $k, $subItems, $extras, $depth );
			}
		}
	}

	/**
	 * Parses a string for PHP functions
	 *
	 * @param string         $input
	 * @param array          $vars
	 * @param array          $extraStrings
	 * @param UserTable|null $user
	 * @param \CBuser|null   $cbUser
	 * @return string
	 */
	static public function formatFunction( $input, $vars = array(), $extraStrings = array(), $user = null, $cbUser = null )
	{
		if ( ( ! $input ) || strpos( $input, '[cb:parse' ) === false ) {
			return $input;
		}

		$regex		=	'%\[cb:parse(?: +function="([^"/\[\] ]+)")?( +(?: ?[a-zA-Z-_]+="(?:[^"]|\\\\")+")+)?(?:(?:\s*/])|(?:]((?:[^\[]+|\[(?!/?cb:parse[^\]]*])|(?R))+)?\[/cb:parse]))%i';

		$input		=	preg_replace_callback( $regex, function( array $matches ) use ( $vars, $extraStrings, $user, $cbUser )
							{
								$function								=	( isset( $matches[1] ) ? $matches[1] : null );
								$value									=	( isset( $matches[3] ) ? self::formatFunction( $matches[3], $vars, $extraStrings, $user, $cbUser ) : null );

								if ( ! $function ) {
									return $value;
								}

								$options								=	new Registry();

								if ( isset( $matches[2] ) ) {
									if ( preg_match_all( '/(?:([a-zA-Z-_]+)="((?:[^"]|\\\\\\\\")+)")+/i', $matches[2], $optionResults, PREG_SET_ORDER ) ) {
										foreach( $optionResults as $option ) {
											$k							=	( isset( $option[1] ) ? $option[1] : null );
											$v							=	( isset( $option[2] ) ? self::formatFunction( $option[2], $vars, $extraStrings, $user, $cbUser ) : null );

											if ( $k ) {
												$options->set( $k, $v );
											}
										}
									}
								}

								$method									=	$options->get( 'method', null, GetterInterface::STRING );

								$options->unsetEntry( 'method' );

								$return									=	null;

								switch ( $function ) {
									case 'prepare':
										$return							=	Application::Cms()->prepareHtmlContentPlugins( $value, 'autoaction', ( $user ? $user->get( 'id', 0, GetterInterface::INT ) : 0 ) );
										break;
									case 'substitutions':
										if ( $cbUser ) {
											$return						=	$cbUser->replaceUserVars( $value, false, false, $extraStrings, false );
										} elseif ( $user ) {
											$return						=	\CBuser::getInstance( $user->get( 'id', 0, GetterInterface::INT ), false )->replaceUserVars( $value, false, false, $extraStrings, false );
										} else {
											$return						=	\CBuser::getMyInstance()->replaceUserVars( $value, false, false, $extraStrings, false );
										}
										break;
									case 'translate':
										$return							=	CBTxt::T( $value );
										break;
									case 'clean':
										switch( $method ) {
											case 'cmd':
												$return					=	Get::clean( $value, GetterInterface::COMMAND );
												break;
											case 'numeric':
												$return					=	Get::clean( $value, GetterInterface::NUMERIC );
												break;
											case 'unit':
												$return					=	Get::clean( $value, GetterInterface::UINT );
												break;
											case 'int':
											case 'integer':
												$return					=	Get::clean( $value, GetterInterface::INT );
												break;
											case 'bool':
											case 'boolean':
												$return					=	Get::clean( $value, GetterInterface::BOOLEAN );
												break;
											case 'str':
											case 'string':
												$return					=	Get::clean( $value, GetterInterface::STRING );
												break;
											case 'html':
												$return					=	Get::clean( $value, GetterInterface::HTML );
												break;
											case 'float':
												$return					=	Get::clean( $value, GetterInterface::FLOAT );
												break;
											case 'base64':
												$return					=	Get::clean( $value, GetterInterface::BASE64 );
												break;
											case 'tags':
												$return					=	strip_tags( $value );
												break;
										}
										break;
									case 'convert':
										switch( $method ) {
											case 'uppercase':
												$return					=	strtoupper( $value );
												break;
											case 'uppercasewords':
												$return					=	ucwords( $value );
												break;
											case 'uppercasefirst':
												$return					=	ucfirst( $value );
												break;
											case 'lowercase':
												$return					=	strtolower( $value );
												break;
											case 'lowercasefirst':
												$return					=	lcfirst( $value );
												break;
										}
										break;
									case 'math':
										$return							=	self::formatMath( $value );

										switch( $method ) {
											case 'round':
												$return					=	round( $value, $options->get( 'decimal', 0, GetterInterface::INT ) );
												break;
											case 'ceil':
												$return					=	ceil( $value );
												break;
											case 'floor':
												$return					=	floor( $value );
												break;
											case 'abs':
												$return					=	abs( $value );
												break;
											case 'number':
												$return					=	number_format( $value, $options->get( 'decimals', 0, GetterInterface::INT ), $options->get( 'decimal', '.', GetterInterface::STRING ), $options->get( 'separator', ',', GetterInterface::STRING ) );
												break;
										}
										break;
									case 'time':
										if ( $options->has( 'time' ) ) {
											$return						=	Application::Date( ( is_numeric( $value ) ? (int) $value : $value ), 'UTC' )->modify( $options->get( 'time', null, GetterInterface::STRING ) )->getTimestamp();
										} else {
											$return						=	Application::Date( ( is_numeric( $value ) ? (int) $value : $value ), 'UTC' )->getTimestamp();
										}
										break;
									case 'date':
										$offset							=	$options->get( 'offset', null, GetterInterface::STRING );
										$return							=	cbFormatDate( ( is_numeric( $value ) ? (int) $value : $value ), ( $offset ? true : false ), ( $options->get( 'time', 'true', GetterInterface::STRING ) == 'false' ? false : true ), $options->get( 'date-format', null, GetterInterface::STRING ), $options->get( 'time-format', null, GetterInterface::STRING ), ( $offset != 'true' ? $offset : null ) );
										break;
									case 'length':
										$return							=	strlen( $value );
										break;
									case 'replace':
										$count							=	$options->get( 'count', 0, GetterInterface::INT );

										if ( $options->has( 'pattern' ) ) {
											$return						=	( $options->has( 'count' ) ? preg_replace( $options->get( 'pattern', null, GetterInterface::RAW ), $options->get( 'replace', null, GetterInterface::RAW ), $value, $count ) : preg_replace( $options->get( 'pattern', null, GetterInterface::RAW ), $options->get( 'replace', null, GetterInterface::RAW ), $value ) );
										} else {
											$return						=	( $options->has( 'count' ) ? str_replace( $options->get( 'search', null, GetterInterface::RAW ), $options->get( 'replace', null, GetterInterface::RAW ), $value, $count ) : str_replace( $options->get( 'search', null, GetterInterface::RAW ), $options->get( 'replace', null, GetterInterface::RAW ), $value ) );
										}
										break;
									case 'position':
										switch( $options->get( 'occurrence', null, GetterInterface::STRING ) ) {
											case 'last':
												$return					=	strrpos( $value, $options->get( 'search', null, GetterInterface::RAW ) );
												break;
											case 'first':
											default:
												$return					=	strpos( $value, $options->get( 'search', null, GetterInterface::RAW ) );
												break;
										}
										break;
									case 'occurrence':
										$return							=	strstr( $value, $options->get( 'search', null, GetterInterface::RAW ) );
										break;
									case 'repeat':
										$return							=	str_repeat( $value, $options->get( 'count', 0, GetterInterface::INT ) );
										break;
									case 'extract':
										$return							=	( $options->has( 'length' ) ? substr( $value, $options->get( 'start', 0, GetterInterface::INT ), $options->get( 'length', 0, GetterInterface::INT ) ) : substr( $value, $options->get( 'start', 0, GetterInterface::INT ) ) );
										break;
									case 'trim':
										switch( $options->get( 'direction', null, GetterInterface::STRING ) ) {
											case 'left':
												$return					=	( $options->has( 'characters' ) ? ltrim( $value, $options->get( 'characters', null, GetterInterface::STRING ) ) : ltrim( $value ) );
												break;
											case 'right':
												$return					=	( $options->has( 'characters' ) ? rtrim( $value, $options->get( 'characters', null, GetterInterface::STRING ) ) : rtrim( $value ) );
												break;
											default:
												$return					=	( $options->has( 'characters' ) ? trim( $value, $options->get( 'characters', null, GetterInterface::STRING ) ) : trim( $value ) );
												break;
										}
										break;
									case 'encode':
										switch( $method ) {
											case 'cslashes':
												$return					=	addcslashes( $value, $options->get( 'characters', null, GetterInterface::STRING ) );
												break;
											case 'slashes':
												$return					=	addslashes( $value );
												break;
											case 'entity':
												$return					=	htmlentities( $value );
												break;
											case 'html':
												$return					=	htmlspecialchars( $value );
												break;
											case 'url':
												$return					=	urlencode( $value );
												break;
											case 'base64':
												$return					=	base64_encode( $value );
												break;
											case 'md5':
												$return					=	md5( $value );
												break;
											case 'sha1':
												$return					=	sha1( $value );
												break;
											case 'password':
												$user					=	new UserTable();

												$return					=	$user->hashAndSaltPassword( $value );
												break;
										}
										break;
									case 'decode':
										switch( $method ) {
											case 'cslashes':
												$return					=	stripcslashes( $value );
												break;
											case 'slashes':
												$return					=	stripslashes( $value );
												break;
											case 'entity':
												$return					=	html_entity_decode( $value );
												break;
											case 'html':
												$return					=	htmlspecialchars_decode( $value );
												break;
											case 'url':
												$return					=	urldecode( $value );
												break;
											case 'base64':
												$return					=	base64_decode( $value );
												break;
										}
										break;
									default:
										$class							=	$options->get( 'class', null, GetterInterface::STRING );
										$subFunction					=	null;
										$static							=	false;
										$result							=	null;

										if ( strpos( $function, '::' ) !== false ) {
											list( $class, $function )	=	explode( '::', $function, 2 );

											$static						=	true;
										} elseif ( strpos( $class, '::' ) !== false ) {
											$subFunction				=	$function;

											list( $class, $function )	=	explode( '::', $class, 2 );

											$static						=	true;
										}

										if ( $class ) {
											$object						=	null;

											$options->unsetEntry( 'class' );

											if ( isset( $vars[$class] ) && is_object( $vars[$class] ) ) {
												$object					=	$vars[$class];
												$class					=	get_class( $object );
											}

											if ( $static ) {
												if ( $subFunction ) {
													if ( is_callable( array( $class, $function ) ) ) {
														$object			=	call_user_func_array( array( $class, $function ), array() );

														if ( method_exists( $object, $subFunction ) ) {
															$result		=	call_user_func_array( array( $object, $subFunction ), $options->asArray() );
														}
													}
												} else {
													if ( is_callable( array( $class, $function ) ) ) {
														$result			=	call_user_func_array( array( $class, $function ), $options->asArray() );
													}
												}
											} else {
												if ( $object || class_exists( $class ) ) {
													if ( ! $object ) {
														$object			=	new $class();

														if ( $value && method_exists( $object, 'load' ) ) {
															$object->load( $value );
														}
													}

													if ( method_exists( $object, $function ) ) {
														$result			=	call_user_func_array( array( $object, $function ), $options->asArray() );
													}
												}
											}
										} else {
											if ( function_exists( $function ) ) {
												$result					=	call_user_func_array( $function, $options->asArray() );
											}
										}

										if ( $method && is_object( $result ) && method_exists( $result, $method ) ) {
											$result						=	call_user_func_array( array( $result, $method ), $options->asArray() );
										}

										if ( ( ! is_array( $result ) ) && ( ! is_object( $result ) ) ) {
											$return						=	$result;
										}
										break;
								}

								return $return;
							},
							$input );

		return self::formatFunction( $input, $vars, $extraStrings, $user, $cbUser );
	}

	/**
	 * Parses a string for math expressions
	 *
	 * @param string $value
	 * @return string
	 */
	static public function formatMath( $value )
	{
		if ( preg_match( '/(?:\(\s*)([^(]+?)(?:\s*\))/i', $value, $expression ) ) {
			// Sub-Expression
			$value					=	str_replace( $expression[0], self::formatMath( $expression[1] ), $value );

			return self::formatMath( $value );
		} elseif ( preg_match( '/([+-]?\d*\.?\d+)\s*\*\s*([+-]?\d*\.?\d+)/i', $value, $expression ) ) {
			// Multiply
			$left					=	( isset( $expression[1] ) ? trim( $expression[1] ) : null );
			$right					=	( isset( $expression[2] ) ? trim( $expression[2] ) : null );
			$value					=	str_replace( $expression[0], ( $left * $right ), $value );

			return self::formatMath( $value );
		} elseif ( preg_match( '%([+-]?\d*\.?\d+)\s*/\s*([+-]?\d*\.?\d+)%i', $value, $expression ) ) {
			// Divide:
			$left					=	( isset( $expression[1] ) ? trim( $expression[1] ) : null );
			$right					=	( isset( $expression[2] ) ? trim( $expression[2] ) : null );
			$value					=	str_replace( $expression[0], ( $left / $right ), $value );

			return self::formatMath( $value );
		} elseif ( preg_match( '/([+-]?\d*\.?\d+)\s*([+%-])\s*([+-]?\d*\.?\d+)/i', $value, $expression ) ) {
			// Add, Subtract, Modulus:
			$left					=	( isset( $expression[1] ) ? trim( $expression[1] ) : null );
			$operator				=	( isset( $expression[2] ) ? trim( $expression[2] ) : null );
			$right					=	( isset( $expression[3] ) ? trim( $expression[3] ) : null );

			if ( $operator ) {
				switch( $operator ) {
					case '+':
						$value		=	str_replace( $expression[0], ( $left + $right ), $value );
						break;
					case '-':
						$value		=	str_replace( $expression[0], ( $left - $right ), $value );
						break;
					case '%':
						$value		=	str_replace( $expression[0], ( $left % $right ), $value );
						break;
				}
			}

			return self::formatMath( $value );
		}

		return $value;
	}

	/**
	 * Compares two values to see if they're a match based off the supplied operator
	 *
	 * @param string $field
	 * @param string $operator
	 * @param string $value
	 * @param array  $vars
	 * @return bool|int|string
	 */
	static public function getFieldMatch( $field, $operator, $value, $vars = array() )
	{
		if ( $operator === '' ) {
			return true;
		}

		$field			=	trim( $field );
		$value			=	trim( $value );

		switch ( (int) $operator ) {
			case 1:
				$match	=	( $field != $value );
				break;
			case 2:
				$match	=	( $field > $value );
				break;
			case 3:
				$match	=	( $field < $value );
				break;
			case 4:
				$match	=	( $field >= $value );
				break;
			case 5:
				$match	=	( $field <= $value );
				break;
			case 6:
				$match	=	( ! $field );
				break;
			case 7:
				$match	=	( $field );
				break;
			case 8:
				$match	=	( stristr( $field, $value ) );
				break;
			case 9:
				$match	=	( ! stristr( $field, $value ) );
				break;
			case 10:
				$match	=	( preg_match( $value, $field ) );
				break;
			case 11:
				$match	=	( ! preg_match( $value, $field ) );
				break;
			case 0:
			default:
				$match	=	( $field == $value );
				break;
		}

		return $match;
	}

	/**
	 * Returns string name of an operator from int
	 *
	 * @param int|string $operator
	 * @return string
	 */
	static public function getOperatorTitle( $operator )
	{
		switch ( (int) $operator ) {
			case 1:
				$title	=	CBTxt::T( 'Not Equal To' );
				break;
			case 2:
				$title	=	CBTxt::T( 'Greater Than' );
				break;
			case 3:
				$title	=	CBTxt::T( 'Less Than' );
				break;
			case 4:
				$title	=	CBTxt::T( 'Greater Than or Equal To' );
				break;
			case 5:
				$title	=	CBTxt::T( 'Less Than or Equal To' );
				break;
			case 6:
				$title	=	CBTxt::T( 'Empty' );
				break;
			case 7:
				$title	=	CBTxt::T( 'Not Empty' );
				break;
			case 8:
				$title	=	CBTxt::T( 'Does Contain' );
				break;
			case 9:
				$title	=	CBTxt::T( 'Does Not Contain' );
				break;
			case 10:
				$title	=	CBTxt::T( 'Is REGEX' );
				break;
			case 11:
				$title	=	CBTxt::T( 'Is Not REGEX' );
				break;
			case 0:
				$title	=	CBTxt::T( 'Equal To' );
				break;
			default:
				$title	=	CBTxt::T( 'Unknown' );
				break;
		}

		return $title;
	}

	/**
	 * Returns the current URL
	 * 
	 * @return string
	 */
	static public function getCurrentURL()
	{
		$isHttps		=	( isset( $_SERVER['HTTPS'] ) && ( ! empty( $_SERVER['HTTPS'] ) ) && ( $_SERVER['HTTPS'] != 'off' ) );
		$url			=	'http' . ( $isHttps ? 's' : '' ) . '://' . $_SERVER['HTTP_HOST'];

		if ( ( ! empty( $_SERVER['PHP_SELF'] ) ) && ( ! empty( $_SERVER['REQUEST_URI'] ) ) ) {
			$url		.=	$_SERVER['REQUEST_URI'];
		} else {
			$url		.=	$_SERVER['SCRIPT_NAME'];

			if ( isset( $_SERVER['QUERY_STRING'] ) && ( ! empty( $_SERVER['QUERY_STRING'] ) ) ) {
				$url	.=	'?' . $_SERVER['QUERY_STRING'];
			}
		}

		return cbUnHtmlspecialchars( preg_replace( '/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', preg_replace( '/eval\((.*)\)/', '', htmlspecialchars( urldecode( $url ) ) ) ) );
	}

	/**
	 * Encodes a string to URL safe
	 *
	 * @param string $str
	 * @return string
	 */
	static public function escapeURL( $str )
	{
		return urlencode( trim( $str ) );
	}

	/**
	 * Encodes a string to XML safe
	 *
	 * @param string $str
	 * @return string
	 */
	static public function escapeXML( $str )
	{
		return htmlspecialchars( trim( $str ), ENT_COMPAT, 'UTF-8' );
	}

	/**
	 * Encodes a string to SQL safe
	 *
	 * @param string $str
	 * @return string
	 */
	static public function escapeSQL( $str )
	{
		global $_CB_database;

		return $_CB_database->getEscaped( $str );
	}

	/**
	 * Returns the internal general URL for firing internal general actions
	 *
	 * @return string
	 */
	static public function loadInternalGeneralURL()
	{
		global $_CB_framework;

		return '<a href="' . $_CB_framework->pluginClassUrl( 'cbautoactions', true, array( 'action' => 'general', 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '" target="_blank" class="text-wrapall">' . $_CB_framework->pluginClassUrl( 'cbautoactions', false, array( 'action' => 'general', 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '</a>';
	}

	/**
	 * Returns the internal users URL for firing internal users actions
	 *
	 * @return string
	 */
	static public function loadInternalUsersURL()
	{
		global $_CB_framework;

		return '<a href="' . $_CB_framework->pluginClassUrl( 'cbautoactions', true, array( 'action' => 'users', 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '" target="_blank" class="text-wrapall">' . $_CB_framework->pluginClassUrl( 'cbautoactions', false, array( 'action' => 'users', 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '</a>';
	}

	/**
	 * Returns the internal action URL for firing an action
	 *
	 * @param int $id
	 * @return string
	 */
	static public function loadInternalActionURL( $id )
	{
		global $_CB_framework;

		if ( ! $id ) {
			return null;
		}

		return '<a href="' . $_CB_framework->pluginClassUrl( 'cbautoactions', true, array( 'action' => 'action', 'actions' => (int) $id ), 'html', 0, true ) . '" target="_blank" class="text-wrapall">' . $_CB_framework->pluginClassUrl( 'cbautoactions', false, array( 'action' => 'action', 'actions' => (int) $id ), 'html', 0, true ) . '</a>';
	}

	/**
	 * Installs system actions from plugins edit.autoactions.system.xml file
	 *
	 * @return string
	 */
	static public function installSystemActions()
	{
		global $_CB_database, $_PLUGINS;

		$_PLUGINS->loadPluginGroup( null, null, 0 );

		$loadedPlugins							=	$_PLUGINS->getLoadedPluginGroup( null );
		$order									=	1;
		$exist									=	array();
		$return									=	null;

		foreach ( $loadedPlugins as $plugin ) {
			$element							=	$_PLUGINS->loadPluginXML( 'autoactions', 'system', $plugin->id );

			/** @var \SimpleXMLElement[] $viewModels */
			$autoactions						=	$element->xpath( '/autoactions/autoaction' );

			if ( $autoactions && count( $autoactions ) ) {
				foreach ( $autoactions as $autoaction ) {
					$name						=	(string) $autoaction->attributes( 'name' );
					$type						=	(string) $autoaction->attributes( 'type' );

					if ( ( ! $name ) || ( ! $type ) ) {
						continue;
					}

					$label						=	(string) $autoaction->attributes( 'label' );

					if ( ! $label ) {
						$label					=	$name;
					}

					$action						=	new AutoActionTable( $_CB_database, '#__comprofiler_plugin_autoactions', 'id' );

					$action->load( array( 'system' => (string) $autoaction->attributes( 'name' ) ) );

					if ( ! $action->get( 'id', 0, GetterInterface::INT ) ) {
						$action->set( 'published', (int) $autoaction->attributes( 'published' ) );
					}

					$action->set( 'system', $name );
					$action->set( 'title', $label );
					$action->set( 'type', $type );
					$action->set( 'ordering', $order );

					$variables					=	$autoaction->xpath( 'param' );

					if ( $variables && count( $variables ) ) {
						foreach ( $variables as $variable ) {
							$varName			=	(string) $variable->attributes( 'name' );

							if ( ! $varName ) {
								continue;
							}

							$action->set( $varName, (string) $variable->attributes( 'value' ) );
						}
					}

					if ( $action->get( 'object', null, GetterInterface::INT ) === null ) {
						$action->set( 'object', 0 );
					}

					if ( $action->get( 'variable', null, GetterInterface::INT ) === null ) {
						$action->set( 'variable', 1 );
					}

					if ( $action->get( 'access', null, GetterInterface::INT ) === null ) {
						$action->set( 'access', -1 );
					}

					$actionConditions			=	new Registry();
					/** @var \SimpleXMLElement[] $conditions */
					$conditions					=	$autoaction->xpath( 'conditions/condition' );

					if ( $conditions && count( $conditions ) ) {
						foreach ( $conditions as $i => $condition ) {
							$params				=	$condition->xpath( 'param' );

							if ( $params && count( $params ) ) {
								foreach ( $params as $param ) {
									$paramName	=	(string) $param->attributes( 'name' );

									if ( ! $paramName ) {
										continue;
									}

									$actionConditions->set( $i . '.' . $paramName, (string) $param->attributes( 'value' ) );
								}
							}
						}
					}

					$action->set( 'conditions', $actionConditions->asJson() );

					$actionParams				=	new Registry();
					$repeatActions				=	$autoaction->xpath( 'actions/action' );

					if ( $repeatActions && count( $repeatActions ) ) {
						$actionsName			=	(string) $autoaction->xpath( 'actions' )[0]->attributes( 'name' );

						if ( ! $actionsName ) {
							$actionsName		=	$type;
						}

						foreach ( $repeatActions as $i => $repeatAction ) {
							$params				=	$repeatAction->xpath( 'param' );

							if ( $params && count( $params ) ) {
								foreach ( $params as $param ) {
									$paramName	=	(string) $param->attributes( 'name' );

									if ( ! $paramName ) {
										continue;
									}

									$actionParams->set( $actionsName . '.' . $i . '.' . $paramName, (string) $param->attributes( 'value' ) );
								}
							}
						}
					}

					$singleAction				=	$autoaction->xpath( 'action/param' );

					if ( $singleAction && count( $singleAction ) ) {
						$actionsName			=	(string) $autoaction->xpath( 'action' )[0]->attributes( 'name' );

						if ( ! $actionsName ) {
							$actionsName		=	$type;
						}

						foreach ( $singleAction as $param ) {
							$paramName			=	(string) $param->attributes( 'name' );

							if ( ! $paramName ) {
								continue;
							}

							$actionParams->set( $actionsName . '.' . $paramName, (string) $param->attributes( 'value' ) );
						}
					}

					$params						=	$autoaction->xpath( 'params/param' );

					if ( $params && count( $params ) ) {
						foreach ( $params as $param ) {
							$paramName			=	(string) $param->attributes( 'name' );

							if ( ! $paramName ) {
								continue;
							}

							$actionParams->set( $paramName, (string) $param->attributes( 'value' ) );
						}
					}

					$action->set( 'params', $actionParams->asJson() );

					if ( $action->store() ) {
						$return					.=	'<p>'
												.		'<strong>' . $order . '. ' . $action->get( 'title', null, GetterInterface::STRING ) . '</strong>'
												.		'<br />' . $action->get( 'description', null, GetterInterface::STRING )
												.	'</p>';

						$order++;

						$exist[]				=	$action->get( 'system', null, GetterInterface::STRING );
					}
				}
			}
		}

		// Delete system actions that no longer exist:
		if ( $exist ) {
			$query								=	"DELETE"
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'system' ) . " NOT IN " . $_CB_database->safeArrayOfStrings( $exist )
												.	"\n AND " . $_CB_database->NameQuote( 'system' ) . " != ''";
			$_CB_database->setQuery( $query );
			$_CB_database->query();

			$return								=	'<strong>' . CBTxt::T( 'COUNT_SYSTEM_ACTIONS_INSTALLED', '%%COUNT%% system action installed.|%%COUNT%% system actions installed.', array( '%%COUNT%%' => count(  $exist) ) ) . '</strong><hr />'
												.	$return;
		} else {
			$return								=	CBTxt::T( 'No system actions installed!' );
		}

		return $return;
	}
}
