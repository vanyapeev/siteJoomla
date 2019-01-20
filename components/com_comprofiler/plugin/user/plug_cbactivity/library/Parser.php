<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Input\Get;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;

defined('CBLIB') or die();

class Parser
{
	/** @var array  */
	protected $regexp		=	array(	'hashtag'	=>	'/^#(\w+)$/i',
										'profile'	=>	'/^@(\w+)$/i',
										'link'		=>	'#^((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))$#i',
										'email'		=>	'/^[a-z0-9!#$%&\'*+\\\\\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\\\\\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/i'
									);
	/** @var null|Activity|Comments  */
	protected $stream		=	null;
	/** @var string  */
	protected $string		=	null;
	/** @var string  */
	protected $parsed		=	null;
	/** @var array  */
	protected $words		=	array();
	/** @var Registry[]  */
	protected $attachments	=	array();

	/**
	 * Constructor
	 *
	 * @param string                 $string
	 * @param null|Activity|Comments $stream
	 */
	public function __construct( $string, $stream = null )
	{
		global $_PLUGINS;

		$_PLUGINS->loadPluginGroup( 'user' );

		$this->stream	=	$stream;
		$this->string	=	$string;
		$this->parsed	=	$string;
		$this->words	=	preg_split( '/\s/i', $string );
	}

	/**
	 * Returns parser stream
	 *
	 * @return null|Activity|Comments
	 */
	public function stream()
	{
		return $this->stream;
	}

	/**
	 * Returns the original unmodified string
	 *
	 * @return string
	 */
	public function original()
	{
		return $this->string;
	}

	/**
	 * Replaces user substitutions
	 *
	 * @return string
	 */
	public function substitutions()
	{
		if ( $this->stream ) {
			$cbUser		=	\CBuser::getInstance( $this->stream->user()->get( 'id', 0, GetterInterface::INT ), false );
		} else {
			$cbUser		=	\CBuser::getMyInstance();
		}

		$this->parsed	=	$cbUser->replaceUserVars( $this->parsed, true, false, null, false );

		return $this->parsed;
	}

	/**
	 * Replaces :EMOTE: with emote icons
	 *
	 * @return string
	 */
	public function emotes()
	{
		$this->parsed	=	strtr( $this->parsed, CBActivity::loadEmoteOptions( true ) );

		return $this->parsed;
	}

	/**
	 * Replaces #HASHTAG with stream filter urls
	 *
	 * @return string
	 */
	public function hashtags()
	{
		global $_CB_framework;

		foreach ( $this->words as $word ) {
			if ( preg_match( $this->regexp['hashtag'], $word, $match ) ) {
				$this->parsed	=	str_replace( $word, '<a href="' . $_CB_framework->pluginClassUrl( 'cbactivity', true, array( 'action' => 'recentactivity', 'hashtag' => $match[1] ) ) . '">' . htmlspecialchars( $match[0] ) . '</a>', $this->parsed );
			}
		}

		return $this->parsed;
	}

	/**
	 * Replaces @MENTION with profile urls
	 *
	 * @return string
	 */
	public function profiles()
	{
		global $_CB_database, $_CB_framework;

		/** @var UserTable[] $users */
		static $users						=	array();

		foreach ( $this->words as $k => $word ) {
			if ( preg_match( $this->regexp['profile'], $word, $match ) ) {
				$cleanWord					=	Get::clean( $match[1], GetterInterface::STRING );

				if ( ! isset( $users[$cleanWord] ) ) {
					$user					=	new UserTable();

					if ( is_numeric( $match[1] ) ) {
						$user				=	\CBuser::getUserDataInstance( (int) $match[1] );
					}

					if ( ! $user->get( 'id', 0, GetterInterface::INT ) ) {
						$wordNext2			=	( isset( $this->words[$k+1] ) && ( ! preg_match( $this->regexp['profile'], $this->words[$k+1] ) ) ? $cleanWord . ' ' . Get::clean( $this->words[$k+1], GetterInterface::STRING ) : null );
						$wordNext3			=	( $wordNext2 && isset( $this->words[$k+2] ) && ( ! preg_match( $this->regexp['profile'], $this->words[$k+2] ) ) ? $wordNext2 . ' ' . Get::clean( $this->words[$k+2], GetterInterface::STRING ) : null );
						$wordNext4			=	( $wordNext3 && isset( $this->words[$k+3] ) && ( ! preg_match( $this->regexp['profile'], $this->words[$k+3] ) ) ? $wordNext3 . ' ' . Get::clean( $this->words[$k+3], GetterInterface::STRING ) : null );
						$wordNext5			=	( $wordNext4 && isset( $this->words[$k+4] ) && ( ! preg_match( $this->regexp['profile'], $this->words[$k+4] ) ) ? $wordNext4 . ' ' . Get::clean( $this->words[$k+4], GetterInterface::STRING ) : null );
						$wordNext6			=	( $wordNext5 && isset( $this->words[$k+5] ) && ( ! preg_match( $this->regexp['profile'], $this->words[$k+5] ) ) ? $wordNext5 . ' ' . Get::clean( $this->words[$k+5], GetterInterface::STRING ) : null );

						$query				=	'SELECT c.*, u.*'
											.	"\n FROM " . $_CB_database->NameQuote( '#__users' ) . " AS u"
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
											.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
											.	"\n WHERE ( u." . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $cleanWord )		// Match username exactly
											.	' OR u.' . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $cleanWord );					// Match name exactly

						if ( $wordNext2 ) { // 2 Words
							$query			.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $wordNext2 )				// Match username +1 word exactly
											.	' OR u.' . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $wordNext2 );					// Match name +1 word exactly
						}

						if ( $wordNext3 ) { // 3 Words
							$query			.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $wordNext3 )				// Match username +2 words exactly
											.	' OR u.' . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $wordNext3 );					// Match name +2 words exactly
						}

						if ( $wordNext4 ) { // 4 Words
							$query			.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $wordNext4 )				// Match username +3 words exactly
											.	' OR u.' . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $wordNext4 );					// Match name +3 words exactly
						}

						if ( $wordNext5 ) { // 5 Words
							$query			.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $wordNext5 )				// Match username +4 words exactly
											.	' OR u.' . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $wordNext5 );					// Match name +4 words exactly
						}

						if ( $wordNext6 ) { // 6 Words
							$query			.=	' OR u.' . $_CB_database->NameQuote( 'username' ) . ' = ' . $_CB_database->Quote( $wordNext6 )				// Match username +5 words exactly
											.	' OR u.' . $_CB_database->NameQuote( 'name' ) . ' = ' . $_CB_database->Quote( $wordNext6 );					// Match name +5 words exactly
						}

						$query				.=	' )'
											.	"\n ORDER BY u." . $_CB_database->NameQuote( 'username' ) . ", u." . $_CB_database->NameQuote( 'name' );
						$_CB_database->setQuery( $query );
						$_CB_database->loadObject( $user );
					}

					$users[$cleanWord]		=	$user;
				}

				$user						=	$users[$cleanWord];

				if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
					$this->parsed			=	preg_replace( '/@' . $user->get( 'id', 0, GetterInterface::INT ) . '\b|@' . preg_quote( $user->get( 'name', null, GetterInterface::STRING ), '/' ) . '\b|@' . preg_quote( $user->get( 'username', null, GetterInterface::STRING ), '/' ) . '\b|' . preg_quote( $word, '/' ) . '\b/i', '<a href="' . $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ) ) . '">' . htmlspecialchars( getNameFormat( $user->get( 'name', null, GetterInterface::STRING ), $user->get( 'username', null, GetterInterface::STRING ), Application::Config()->get( 'name_format' ) ) ) . '</a>', $this->parsed );
				}
			}
		}

		return $this->parsed;
	}

	/**
	 * Replaces URLs with clickable html URLs
	 *
	 * @return string
	 */
	public function links()
	{
		foreach ( $this->words as $word ) {
			if ( preg_match( $this->regexp['link'], $word, $match ) ) {
				$link				=	$match[0];

				if ( substr( $link, 0, 3 ) == 'www' ) {
					$link			=	'http://' . $link;
				}

				$newWindow			=	( ! \JUri::isInternal( $link ) );

				if ( ! $newWindow ) {
					$extension		=	strtolower( pathinfo( $link, PATHINFO_EXTENSION ) );

					if ( $extension && ( ! preg_match( '/^(php|asp|html)/', $extension ) ) ) {
						$newWindow	=	true;
					}
				}

				$this->parsed		=	str_replace( $word, '<a href="' . htmlspecialchars( $link ) . '"' . ( $newWindow ? ' target="_blank" rel="nofollow noopener"' : null ) . '>' . htmlspecialchars( $match[0] ) . '</a>', $this->parsed );
			} elseif ( preg_match( $this->regexp['email'], $word, $match ) ) {
				$this->parsed		=	str_replace( $word, '<a href="mailto:' . htmlspecialchars( $match[0] ) . '" rel="nofollow noopener" target="_blank">' . htmlspecialchars( $match[0] ) . '</a>', $this->parsed );
			}
		}

		return $this->parsed;
	}

	/**
	 * Parses for URLs
	 *
	 * @return array
	 */
	public function urls()
	{
		$urls				=	array();

		foreach ( $this->words as $word ) {
			if ( preg_match( $this->regexp['link'], $word, $match ) ) {
				$link		=	$match[0];

				if ( substr( $link, 0, 3 ) == 'www' ) {
					$link	=	'http://' . $link;
				}

				$urls[]		=	$link;
			}
		}

		return $urls;
	}

	/**
	 * Sends string through content.prepare
	 *
	 * @return string
	 */
	public function prepare()
	{
		$this->parsed	=	Application::Cms()->prepareHtmlContentPlugins( $this->parsed, 'activity.parser' );

		return $this->parsed;
	}

	/**
	 * Replaces linebreaks with html breaks
	 *
	 * @return string
	 */
	public function linebreaks()
	{
		$this->parsed	=	str_replace( array( "\r\n", "\r", "\n" ), '<br />', $this->parsed );

		return $this->parsed;
	}

	/**
	 * Replaces duplicate or bad content
	 *
	 * @return string
	 */
	public function clean()
	{
		// Remove duplicate spaces:
		$this->parsed		=	preg_replace( '/ {2,}/i', ' ', $this->parsed );

		// Remove duplicate tabs:
		$this->parsed		=	preg_replace( '/\t{2,}/i', "\t", $this->parsed );

		// Remove duplicate linebreaks:
		$this->parsed		=	preg_replace( '/((?:\r\n|\r|\n){2})(?:\r\n|\r|\n)*/i', '$1', $this->parsed );

		return $this->parsed;
	}

	/**
	 * Parsers urls for their media information
	 *
	 * @return Registry[]
	 */
	public function attachments()
	{
		foreach ( $this->words as $word ) {
			$this->attachment( $word );
		}

		return $this->attachments;
	}

	/**
	 * Parsers a url for media information
	 *
	 * @param string $url
	 * @return null|Registry
	 */
	public function attachment( $url )
	{
		global $_CB_framework;

		$attachment												=	null;

		if ( $url && preg_match( $this->regexp['link'], $url ) ) {
			$cachePath											=	$_CB_framework->getCfg( 'absolute_path' ) . '/cache/activity_links';
			$cache												=	$cachePath . '/' . md5( $url );

			if ( file_exists( $cache ) ) {
				if ( ( ( Application::Database()->getUtcDateTime() - filemtime( $cache ) ) / 3600 ) > 24 ) {
					$request									=	true;
				} else {
					$attachment									=	trim( file_get_contents( $cache ) );
					$request									=	false;
				}
			} else {
				$request										=	true;
			}

			if ( $request ) {
				$client											=	@new \GuzzleHttp\Client();

				try {
					$result										=	$client->get( $url );

					if ( $result->getStatusCode() == 200 ) {
						$attachment								=	array(	'type'			=>	'url',
																			'title'			=>	array(),
																			'description'	=>	array(),
																			'media'			=>	array( 'video' => array(), 'audio' => array(), 'image' => array(), 'file' => array() ),
																			'url'			=>	$url,
																			'internal'		=>	\JUri::isInternal( $url ),
																			'date'			=>	null
																		);

						$urlType								=	'url';
						$urlDomain								=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $url, PHP_URL_HOST ) );
						$urlExt									=	strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );
						$urlFilename							=	pathinfo( $url, PATHINFO_FILENAME ) . '.' . $urlExt;

						if ( in_array( $urlDomain, array( 'youtube', 'youtu' ) ) ) {
							$urlMimeType						=	'video/x-youtube';
						} else {
							if ( $urlExt == 'm4v' ) {
								$urlMimeType					=	'video/mp4';
							} elseif ( $urlExt == 'mp3' ) {
								$urlMimeType					=	'audio/mp3';
							} elseif ( $urlExt == 'm4a' ) {
								$urlMimeType					=	'audio/mp4';
							} else {
								$urlMimeType					=	cbGetMimeFromExt( $urlExt );

								if ( $urlMimeType == 'application/octet-stream' ) {
									$urlFilename				=	null;
									$urlExt						=	null;

									// We can't find the mimetype or extension from the URL so lets check the headers
									list( $urlMimeType )		=	explode( ';', $result->getHeader( 'Content-Type' ), 2 );

									foreach ( cbGetMimeMap() as $ext => $type ) {
										if ( is_array( $type ) ) {
											foreach ( $type as $subExt => $subType ) {
												if ( $urlMimeType == $subType ) {
													$urlExt		=	$subExt;

													break 2;
												}
											}
										} elseif ( $urlMimeType == $type ) {
											$urlExt				=	$ext;

											break;
										}
									}

									if ( preg_match( '/\s*filename\s?=\s?(.*)/', (string) $result->getHeader( 'Content-Disposition' ), $filenameMatches ) ) {
										$filenameParts			=	explode( ';', $filenameMatches[1] );
										$urlFilename			=	trim( $filenameParts[0], '"' );
									}
								}
							}
						}

						if ( $urlExt == 'exe' ) {
							// For security reject all exe files:
							return null;
						}

						$modified								=	(string) $result->getHeader( 'last-modified' );

						if ( $modified ) {
							$attachment['date']					=	Application::Date( $modified, 'UTC' )->getTimestamp();
						}

						$fileExtensions							=	array( 'zip', 'rar', 'doc', 'pdf', 'txt', 'xls' );

						if ( $this->stream ) {
							$streamExtensions					=	$this->stream->get( 'links_file_extensions', 'zip,rar,doc,pdf,txt,xls' );

							$fileExtensions						=	( $streamExtensions && ( $streamExtensions != 'none' ) ? explode( ',', $streamExtensions ) : array() );
						}

						if ( in_array( $urlDomain, array( 'youtube', 'youtu' ) ) || in_array( $urlExt, array( 'mp4', 'ogv', 'ogg', 'webm', 'm4v' ) ) ) {
							$urlType							=	'video';
						} elseif ( in_array( $urlExt, array( 'mp3', 'oga', 'ogg', 'weba', 'wav', 'm4a' ) ) ) {
							$urlType							=	'audio';
						} elseif ( in_array( $urlExt, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) {
							$urlType							=	'image';
						} elseif ( $fileExtensions && in_array( $urlExt, $fileExtensions ) ) {
							$urlType							=	'file';
						} else {
							// The URL is to an HTML page so lets parse it for media content:
							$document							=	@new \DOMDocument();

							$body								=	(string) $result->getBody();

							if ( function_exists( 'mb_convert_encoding' ) ) {
								$body							=	mb_convert_encoding( $body, 'HTML-ENTITIES', 'UTF-8' );
							} else {
								$body							=	'<?xml encoding="UTF-8">' . $body;
							}

							@$document->loadHTML( $body );

							$xpath								=	@new \DOMXPath( $document );

							$paths								=	array(	'title'			=>	array(	'//meta[@name="og:title"]/@content',
																										'//meta[@name="twitter:title"]/@content',
																										'//meta[@name="title"]/@content',
																										'//meta[@property="og:title"]/@content',
																										'//meta[@property="twitter:title"]/@content',
																										'//meta[@property="title"]/@content',
																										'//title'
																									),
																			'description'	=>	array(	'//meta[@name="og:description"]/@content',
																										'//meta[@name="twitter:description"]/@content',
																										'//meta[@name="description"]/@content',
																										'//meta[@property="og:description"]/@content',
																										'//meta[@property="twitter:description"]/@content',
																										'//meta[@property="description"]/@content'
																									),
																			'media'			=>	array(	'video'	=>	array(	'//meta[@name="og:video"]/@content',
																															'//meta[@name="og:video:url"]/@content',
																															'//meta[@name="twitter:player"]/@content',
																															'//meta[@property="og:video"]/@content',
																															'//meta[@property="og:video:url"]/@content',
																															'//meta[@property="twitter:player"]/@content',
																															'//video/@src'
																														),
																										'audio'	=>	array(	'//meta[@name="og:audio"]/@content',
																															'//meta[@name="og:audio:url"]/@content',
																															'//meta[@property="og:audio"]/@content',
																															'//meta[@property="og:audio:url"]/@content',
																															'//audio/@src'
																														),
																										'image'	=>	array(	'//meta[@name="og:image"]/@content',
																															'//meta[@name="og:image:url"]/@content',
																															'//meta[@name="twitter:image"]/@content',
																															'//meta[@name="image"]/@content',
																															'//meta[@property="og:image"]/@content',
																															'//meta[@property="og:image:url"]/@content',
																															'//meta[@property="twitter:image"]/@content',
																															'//meta[@property="image"]/@content',
																															'//img/@src'
																														)
																									)
																		);

							foreach ( $paths as $item => $itemPaths ) {
								$attachment[$item]										=	array();

								foreach ( $itemPaths as $subItem => $itemPath ) {
									if ( $item == 'media' ) {
										$attachment[$item][$subItem]					=	array();
										$existing										=	array();

										foreach ( $itemPath as $subItemPath ) {
											$nodes										=	@$xpath->query( $subItemPath );
											$limit										=	0;

											if ( ( $nodes !== false ) && $nodes->length ) {
												foreach ( $nodes as $node ) {
													$limit++;

													if ( $limit >= 10 ) {
														break;
													}

													$nodeUrl							=	$node->nodeValue;

													if ( isset( $node->baseURI ) ) {
														if ( $nodeUrl[0] === '/' ) {
															if ( substr( $node->baseURI, -1, 1 ) === '/' ) {
																$nodeUrl				=	$node->baseURI . substr( $nodeUrl, 1 );
															} else {
																$nodeUrl				=	$node->baseURI . $nodeUrl;
															}
														} elseif ( ! preg_match( $this->regexp['link'], $nodeUrl ) ) {
															$nodeUrl					=	$node->baseURI . $nodeUrl;
														}
													} else {
														if ( $nodeUrl[0] === '/' ) {
															if ( substr( $url, -1, 1 ) === '/' ) {
																$nodeUrl				=	$url . substr( $nodeUrl, 1 );
															} else {
																$nodeUrl				=	$url . $nodeUrl;
															}
														} elseif ( ! preg_match( $this->regexp['link'], $nodeUrl ) ) {
															$nodeUrl					=	$url . $nodeUrl;
														}
													}

													if ( preg_match( $this->regexp['link'], $nodeUrl ) ) {
														if ( in_array( $nodeUrl, $existing ) ) {
															continue;
														}

														$itemDomain						=	preg_replace( '/^(?:(?:\w+\.)*)?(\w+)\..+$/', '\1', parse_url( $nodeUrl, PHP_URL_HOST ) );
														$itemExt						=	strtolower( pathinfo( $nodeUrl, PATHINFO_EXTENSION ) );

														if ( in_array( $itemDomain, array( 'youtube', 'youtu' ) ) ) {
															$itemMimeType				=	'video/x-youtube';
														} else {
															if ( $itemExt == 'm4v' ) {
																$itemMimeType			=	'video/mp4';
															} elseif ( $itemExt == 'mp3' ) {
																$itemMimeType			=	'audio/mp3';
															} elseif ( $itemExt == 'm4a' ) {
																$itemMimeType			=	'audio/mp4';
															} else {
																$itemMimeType			=	cbGetMimeFromExt( $itemExt );

																if ( $itemMimeType == 'application/octet-stream' ) {
																	continue;
																}
															}
														}

														$attachment[$item][$subItem][]	=	array(	'type'		=>	$subItem,
																									'url'		=>	$nodeUrl,
																									'filename'	=>	( ! in_array( $itemDomain, array( 'youtube', 'youtu' ) ) ? pathinfo( $nodeUrl, PATHINFO_FILENAME ) . '.' . $itemExt : null ),
																									'mimetype'	=>	$itemMimeType,
																									'extension'	=>	$itemExt,
																									'filesize'	=>	0,
																									'internal'	=>	\JUri::isInternal( $nodeUrl )
																								);

														$existing[]						=	$nodeUrl;
													}
												}
											}
										}
									} else {
										$nodes											=	@$xpath->query( $itemPath );

										if ( ( $nodes !== false ) && $nodes->length ) {
											foreach ( $nodes as $node ) {
												if ( in_array( $node->nodeValue, $attachment[$item] ) ) {
													continue;
												}

												$attachment[$item][]					=	$node->nodeValue;
											}
										}
									}
								}
							}
						}

						if ( in_array( $urlType, array( 'video', 'audio', 'image', 'file' ) ) ) {
							$attachment['media'][$urlType][]	=	array(	'type'		=>	$urlType,
																			'url'		=>	$url,
																			'filename'	=>	( ! in_array( $urlDomain, array( 'youtube', 'youtu' ) ) ? $urlFilename : null ),
																			'mimetype'	=>	$urlMimeType,
																			'extension'	=>	$urlExt,
																			'filesize'	=>	( ! in_array( $urlDomain, array( 'youtube', 'youtu' ) ) ? (int) $result->getHeader( 'Content-Length' ) : 0 ),
																			'internal'	=>	\JUri::isInternal( $url )
																		);

							$attachment['type']					=	$urlType;
						}
					}
				} catch( \Exception $e ) {}

				$attachment										=	json_encode( $attachment );

				if ( ! is_dir( $cachePath ) ) {
					$oldMask									=	@umask( 0 );

					if ( @mkdir( $cachePath, 0755, true ) ) {
						@umask( $oldMask );
						@chmod( $cachePath, 0755 );
					} else {
						@umask( $oldMask );
					}
				}

				file_put_contents( $cache, $attachment );
			}

			if ( $attachment ) {
				$attachment										=	new Registry( $attachment );

				$this->attachments[]							=	$attachment;
			} else {
				$attachment										=	null;
			}
		}

		return $attachment;
	}

	/**
	 * Runs string through all parsers
	 *
	 * @param array $ignore
	 * @param bool  $html
	 * @return null|string
	 */
	public function parse( $ignore = array(), $html = true )
	{
		global $_PLUGINS;

		if ( ! $this->string ) {
			return null;
		}

		if ( $this->stream ) {
			if ( ! $this->stream->get( 'parser_substitutions', false, GetterInterface::BOOLEAN ) ) {
				$ignore[]	=	'substitutions';
			}

			if ( ! $this->stream->get( 'parser_emotes', true, GetterInterface::BOOLEAN ) ) {
				$ignore[]	=	'emotes';
			}

			if ( ! $this->stream->get( 'parser_hashtags', true, GetterInterface::BOOLEAN ) ) {
				$ignore[]	=	'hashtags';
			}

			if ( ! $this->stream->get( 'parser_profiles', true, GetterInterface::BOOLEAN ) ) {
				$ignore[]	=	'profiles';
			}

			if ( ! $this->stream->get( 'parser_links', true, GetterInterface::BOOLEAN ) ) {
				$ignore[]	=	'links';
			}

			if ( ! $this->stream->get( 'parser_prepare', false, GetterInterface::BOOLEAN ) ) {
				$ignore[]	=	'prepare';
			}
		}

		if ( ! in_array( 'substitutions', $ignore ) ) {
			$this->substitutions();
		}

		if ( ! in_array( 'emotes', $ignore ) ) {
			$this->emotes();
		}

		if ( ! in_array( 'hashtags', $ignore ) ) {
			$this->hashtags();
		}

		if ( ! in_array( 'profiles', $ignore ) ) {
			$this->profiles();
		}

		if ( ! in_array( 'links', $ignore ) ) {
			$this->links();
		}

		if ( ! in_array( 'prepare', $ignore ) ) {
			$this->prepare();
		}

		if ( ! in_array( 'clean', $ignore ) ) {
			$this->clean();
		}

		if ( ! in_array( 'linebreaks', $ignore ) ) {
			$this->linebreaks();
		}

		if ( ! $html ) {
			$this->parsed	=	Get::clean( $this->parsed, GetterInterface::STRING );
		}

		$_PLUGINS->trigger( 'activity_onParse', array( &$this, $ignore, $html ) );

		return $this->parsed;
	}
}