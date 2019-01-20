<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\ActivityTable;

defined('CBLIB') or die();

/**
 * @method string getAsset()
 * @method Activity setAsset( $asset )
 * @method array getAssets()
 * @method Activity setAssets( $assets )
 * @method UserTable getUser()
 * @method Activity setUser( $user )
 * @method int|array getId()
 * @method Activity setId( $id )
 * @method array getModerators()
 * @method Activity setModerators( $moderators )
 * @method int|array getUserId()
 * @method Activity setUserId( $userId )
 * @method string|array getDate()
 * @method Activity setDate( $datetime )
 * @method string getSearch()
 * @method Activity setSearch( $search )
 * @method string getTitle()
 * @method Activity setTitle( $title )
 * @method string getMessage()
 * @method Activity setMessage( $message )
 * @method string getHidden()
 * @method Activity setHidden( $hidden )
 * @method bool getInline()
 * @method Activity setInline( $inline )
 * @method int getPublished()
 * @method Activity setPublished( $published )
 */
class Activity extends Stream implements ActivityInterface
{
	/** @var array $defaults */
	protected $defaults					=	array(	'layout'									=>	'stream',
													'direction'									=>	'down',
													'auto_update'								=>	false,
													'auto_load'									=>	false,
													'pinned'									=>	true,
													'create'									=>	true,
													'create_access'								=>	2,
													'create_connected'							=>	true,
													'message_limit'								=>	400,
													'collapsed'									=>	true,
													'paging'									=>	true,
													'paging_first_limit'						=>	15,
													'paging_limit'								=>	15,
													'parser_substitutions'						=>	false,
													'parser_emotes'								=>	true,
													'parser_hashtags'							=>	true,
													'parser_profiles'							=>	true,
													'parser_links'								=>	true,
													'parser_prepare'							=>	false,
													'actions'									=>	true,
													'actions_message_limit'						=>	100,
													'actions_include'							=>	0,
													'actions_exclude'							=>	0,
													'locations'									=>	true,
													'locations_address_limit'					=>	200,
													'locations_include'							=>	0,
													'locations_exclude'							=>	0,
													'links'										=>	true,
													'links_file_extensions'						=>	'zip,rar,doc,pdf,txt,xls',
													'links_embedded'							=>	false,
													'links_link_limit'							=>	5,
													'tags'										=>	true,
													'likes'										=>	true,
													'likes_include'								=>	0,
													'likes_exclude'								=>	0,
													'comments'									=>	true,
													'comments_direction'						=>	'up',
													'comments_auto_update'						=>	false,
													'comments_auto_load'						=>	false,
													'comments_pinned'							=>	false,
													'comments_create'							=>	true,
													'comments_create_access'					=>	2,
													'comments_create_connected'					=>	true,
													'comments_message_limit'					=>	400,
													'comments_collapsed'						=>	true,
													'comments_paging'							=>	true,
													'comments_paging_first_limit'				=>	5,
													'comments_paging_limit'						=>	5,
													'comments_parser_substitutions'				=>	false,
													'comments_parser_emotes'					=>	true,
													'comments_parser_profiles'					=>	true,
													'comments_parser_links'						=>	true,
													'comments_parser_prepare'					=>	false,
													'comments_actions'							=>	false,
													'comments_actions_message_limit'			=>	100,
													'comments_actions_include'					=>	0,
													'comments_actions_exclude'					=>	0,
													'comments_locations'						=>	false,
													'comments_locations_address_limit'			=>	200,
													'comments_locations_include'				=>	0,
													'comments_locations_exclude'				=>	0,
													'comments_links'							=>	false,
													'comments_links_file_extensions'			=>	'zip,rar,doc,pdf,txt,xls',
													'comments_links_embedded'					=>	false,
													'comments_links_link_limit'					=>	5,
													'comments_tags'								=>	false,
													'comments_likes'							=>	false,
													'comments_likes_include'					=>	0,
													'comments_likes_exclude'					=>	0,
													'comments_replies'							=>	false,
													'comments_replies_direction'				=>	'up',
													'comments_replies_auto_update'				=>	false,
													'comments_replies_auto_load'				=>	false,
													'comments_replies_pinned'					=>	false,
													'comments_replies_create'					=>	true,
													'comments_replies_create_access'			=>	2,
													'comments_replies_create_connected'			=>	true,
													'comments_replies_message_limit'			=>	400,
													'comments_replies_collapsed'				=>	false,
													'comments_replies_paging'					=>	true,
													'comments_replies_paging_first_limit'		=>	5,
													'comments_replies_paging_limit'				=>	5,
													'comments_replies_parser_substitutions'		=>	false,
													'comments_replies_parser_emotes'			=>	true,
													'comments_replies_parser_profiles'			=>	true,
													'comments_replies_parser_links'				=>	true,
													'comments_replies_parser_prepare'			=>	false,
													'comments_replies_actions'					=>	false,
													'comments_replies_actions_message_limit'	=>	100,
													'comments_replies_actions_include'			=>	0,
													'comments_replies_actions_exclude'			=>	0,
													'comments_replies_locations'				=>	false,
													'comments_replies_locations_address_limit'	=>	200,
													'comments_replies_locations_include'		=>	0,
													'comments_replies_locations_exclude'		=>	0,
													'comments_replies_links'					=>	false,
													'comments_replies_links_file_extensions'	=>	'zip,rar,doc,pdf,txt,xls',
													'comments_replies_links_embedded'			=>	false,
													'comments_replies_links_link_limit'			=>	5,
													'comments_replies_tags'						=>	false,
													'comments_replies_likes'					=>	false,
													'comments_replies_likes_include'			=>	0,
													'comments_replies_likes_exclude'			=>	0
												);

	/** @var ActivityTable[] $loadedRows */
	protected static $loadedRows		=	array();

	/**
	 * Constructor for activity object
	 *
	 * @param null|string|array  $assets
	 * @param null|int|UserTable $user
	 */
	public function __construct( $assets = null, $user = null )
	{
		if ( ! $assets ) {
			$assets		=	array( 'user.connections', 'following', 'global' );
		}

		parent::__construct( $assets, $user );
	}

	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return self|string|int|array|null
	 */
	public function __call( $name, $arguments )
	{
		$method									=	substr( $name, 0, 3 );

		if ( in_array( $method, array( 'get', 'set' ) ) ) {
			$variables							=	array( 'asset', 'assets', 'user', 'id', 'moderators', 'user_id', 'date', 'search', 'title', 'message', 'hidden', 'inline', 'published' );
			$variable							=	strtolower( substr( $name, 3 ) );

			switch ( $variable ) {
				case 'userid':
					$variable					=	'user_id';
					break;
			}

			if ( in_array( $variable, $variables ) ) {
				switch ( $method ) {
					case 'get':
						switch ( $variable ) {
							case 'asset':
								return $this->asset();
								break;
							case 'assets':
								return $this->assets();
								break;
							case 'user':
								return $this->user();
								break;
							case 'id':
							case 'user_id':
								if ( is_array( $this->get( $variable, null, GetterInterface::RAW ) ) ) {
									$default	=	array();
									$type		=	GetterInterface::RAW;
								} else {
									$default	=	0;
									$type		=	GetterInterface::INT;
								}
								break;
							case 'moderators':
								$default		=	array();
								$type			=	GetterInterface::RAW;
								break;
							case 'inline':
								$default		=	false;
								$type			=	GetterInterface::BOOLEAN;
								break;
							case 'published':
								$default		=	0;
								$type			=	GetterInterface::INT;
								break;
							default:
								if ( is_array( $this->get( $variable, null, GetterInterface::RAW ) ) ) {
									$default	=	array();
									$type		=	GetterInterface::RAW;
								} else {
									$default	=	null;
									$type		=	GetterInterface::STRING;
								}
								break;
						}

						return $this->get( $variable, $default, $type );
						break;
					case 'set':
						switch ( $variable ) {
							case 'asset':
							case 'assets':
								$this->assets( ( $arguments ? $arguments[0] : null ) );
								break;
							case 'user':
								$this->user( ( $arguments ? $arguments[0] : null ) );
								break;
							default:
								$this->set( $variable, ( $arguments ? $arguments[0] : null ) );
								break;
						}

						return $this;
						break;
				}
			}
		}

		trigger_error( 'Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR );

		return null;
	}

	/**
	 * Retrieves activity rows or row count
	 *
	 * @param string $output
	 * @return ActivityTable[]|int
	 */
	public function rows( $output = null )
	{
		global $_CB_database, $_PLUGINS;

		static $cache						=	array();

		$myId								=	(int) Application::MyUser()->getUserId();
		$id									=	$this->get( 'id', null, GetterInterface::RAW );
		$hasId								=	( ( ( $id !== '' ) && ( $id !== null ) ) || ( is_array( $id ) && $id ) );

		$select								=	array();
		$join								=	array();
		$where								=	array();

		if ( $output == 'count' ) {
			$select[]						=	'COUNT( a.' . $_CB_database->NameQuote( 'id' ) . ' )';
		} else {
			$select[]						=	'a.*';
		}

		if ( $hasId ) {
			if ( is_array( $this->get( 'id', null, GetterInterface::RAW ) ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $id );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
			}
		}

		$userId								=	$this->get( 'user_id', null, GetterInterface::RAW );

		if ( ( ( $userId !== '' ) && ( $userId !== null ) ) || ( is_array( $userId ) && $userId ) ) {
			if ( is_array( $userId ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userId );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $userId;
			}
		}

		if ( $this->assets() && ( ! in_array( 'all', $this->assets() ) ) )  {
			$assets							=	array();
			$wildcards						=	array();
			$orUsers						=	array();

			$this->queryAssets( $assets, $wildcards, $orUsers );

			if ( $assets || ( ( ! $userId ) && $orUsers ) || $wildcards ) {
				$assetsWhere				=	array();

				if ( ( ( ! $userId ) && $orUsers ) ) {
					$assetsWhere[]			=	"a." . $_CB_database->NameQuote( 'user_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $orUsers );
				}

				if ( $assets ) {
					$assetsWhere[]			=	"a." . $_CB_database->NameQuote( 'asset' ) . ( count( $assets ) > 1 ? " IN " . $_CB_database->safeArrayOfStrings( $assets ) : " = " . $_CB_database->Quote( $assets[0] ) );
				}

				if ( $wildcards ) {
					foreach ( $wildcards as $wildcard ) {
						$assetsWhere[]		=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $wildcard );
					}
				}

				$where[]					=	( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
			} else {
				if ( $output == 'count' ) {
					return 0;
				} else {
					return array();
				}
			}
		}

		if ( ! $hasId ) {
			$date							=	$this->get( 'date', null, GetterInterface::RAW );

			if ( is_array( $date ) ) {
				if ( count( $date ) > 1 ) {
					if ( ! in_array( $date[1], array( '=', '<>', '<', '>', '<=', '>=', 'REGEXP', 'NOT REGEXP', 'LIKE', 'NOT LIKE' ) ) ) {
						$date[1]			=	'>';
					}

					$where[]				=	"a." . $_CB_database->NameQuote( 'date' ) . " " . $date[1] . " " . $_CB_database->Quote( ( is_int( $date[0] ) ? Application::Database()->getUtcDateTime( (int) $date[0] ) : $date[0] ) );
				} else {
					$where[]				=	"a." . $_CB_database->NameQuote( 'date' ) . " > " . $_CB_database->Quote( ( is_int( $date[0] ) ? Application::Database()->getUtcDateTime( (int) $date[0] ) : $date[0] ) );
				}
			} elseif ( $date != '' ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'date' ) . " = " . $_CB_database->Quote( ( is_int( $date ) ? Application::Database()->getUtcDateTime( (int) $date ) : $date ) );
			}

			if ( $this->get( 'title', null, GetterInterface::STRING ) != '' ) {
				if ( strpos( $this->get( 'title', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
				} else {
					$where[]				=	"a." . $_CB_database->NameQuote( 'title' ) . " = " . $_CB_database->Quote( $this->get( 'title', null, GetterInterface::STRING ) );
				}
			}

			if ( $this->get( 'message', null, GetterInterface::STRING ) != '' ) {
				if ( strpos( $this->get( 'message', null, GetterInterface::STRING ), '%' ) !== false ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'message' ) . " LIKE " . $_CB_database->Quote( $this->get( 'message', null, GetterInterface::STRING ) );
				} else {
					$where[]				=	"a." . $_CB_database->NameQuote( 'message' ) . " = " . $_CB_database->Quote( $this->get( 'message', null, GetterInterface::STRING ) );
				}
			}

			if ( $this->get( 'search', null, GetterInterface::STRING ) != '' ) {
				$where[]					=	"( a." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
											.	" OR a." . $_CB_database->NameQuote( 'message' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false )
											.	" OR a." . $_CB_database->NameQuote( 'date' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $this->get( 'search', null, GetterInterface::STRING ), true ) . '%', false ) . " )";
			}

			if ( $myId ) {
				$hidden						=	$this->get( 'hidden', 'visibile', GetterInterface::STRING );

				if ( in_array( $hidden, array( 'status', 'visibile', 'hidden' ) ) ) {
					$join[]					=	( $hidden == 'hidden' ? "INNER JOIN " : "LEFT JOIN " ) . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_hidden' ) . " AS b"
											.	" ON b." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $myId
											.	" AND ( ( b." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'activity' )
											.	" AND b." . $_CB_database->NameQuote( 'item' ) . " = a." . $_CB_database->NameQuote( 'id' ) . " )"
											.	" OR ( b." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'activity.asset' )
											.	" AND b." . $_CB_database->NameQuote( 'item' ) . " = a." . $_CB_database->NameQuote( 'asset' ) . " )"
											.	" OR ( b." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'activity.user' )
											.	" AND b." . $_CB_database->NameQuote( 'item' ) . " = a." . $_CB_database->NameQuote( 'user_id' ) . " ) )";

					if ( in_array( $hidden, array( 'status', 'hidden' ) ) ) {
						if ( $output != 'count' ) {
							$select[]		=	"b." . $_CB_database->NameQuote( 'type' ) . " AS _hidden";
						}
					} else {
						$where[]			=	"b." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
					}
				}
			}
		}

		if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
			$where[]						=	"a." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
		}

		$_PLUGINS->trigger( 'activity_onQueryActivityStream', array( $output, &$select, &$join, &$where, &$this ) );

		$query								=	"SELECT " . implode( ", ", $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity' ) . " AS a"
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
											.	" ON cb." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'user_id' )
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
											.	" ON j." . $_CB_database->NameQuote( 'id' ) . " = cb." . $_CB_database->NameQuote( 'id' )
											.	( $join ? "\n " . implode( "\n ", $join ) : null )
											.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
											.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
											.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
											.	( $where ? "\n AND " . implode( "\n AND ", $where ) : null );

		if ( $output != 'count' ) {
			$query							.=	"\n ORDER BY ";

			if ( $this->get( 'pinned', true, GetterInterface::BOOLEAN ) ) {
				$query						.=	"a." . $_CB_database->NameQuote( 'pinned' ) . " DESC, ";
			}

			$query							.=	"a." . $_CB_database->NameQuote( 'date' ) . " DESC";
		}

		if ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) == 0 ) {
			$pageLimit						=	$this->get( 'paging_first_limit', 15, GetterInterface::INT );
		} else {
			$pageLimit						=	$this->get( 'paging_limit', 15, GetterInterface::INT );
		}

		$paging								=	( ( ! $hasId ) && $pageLimit && ( $output != 'all' ) );
		$cacheId							=	md5( $query . ( $output ? $output : ( $paging ? $this->get( 'paging_limitstart', 0, GetterInterface::INT ) . $pageLimit : null ) ) );

		if ( ( ! isset( $cache[$cacheId] ) ) || ( ( ( $output == 'count' ) && $this->clearRowCount ) || $this->clearRowSelect ) ) {
			if ( $output == 'count' ) {
				$this->clearRowCount		=	false;

				$_CB_database->setQuery( $query );

				$cache[$cacheId]			=	(int) $_CB_database->loadResult();
			} else {
				$this->clearRowSelect		=	false;

				if ( $paging ) {
					$_CB_database->setQuery( $query, $this->get( 'paging_limitstart', 0, GetterInterface::INT ), $pageLimit );
				} else {
					$_CB_database->setQuery( $query );
				}

				$this->set( 'paging_limitstart', ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) + $pageLimit ) );

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\ActivityTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );
				$userIds					=	array();

				/** @var ActivityTable[] $rows */
				foreach ( $rows as $row ) {
					if ( preg_match( '/^profile\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
						$userIds[]			=	(int) $matches[1];
					}

					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'activity_onLoadActivityStream', array( &$rows, $this ) );

				if ( $rows ) {
					if ( $this->get( 'comments', true, GetterInterface::BOOLEAN ) ) {
						CBActivity::prefetchAssets( 'comments', $rows, $this );
					}

					if ( $this->get( 'tags', true, GetterInterface::BOOLEAN ) ) {
						CBActivity::prefetchAssets( 'tags', $rows, $this );
					}

					if ( $this->get( 'likes', true, GetterInterface::BOOLEAN ) ) {
						CBActivity::prefetchAssets( 'likes', $rows, $this );
					}

					self::$loadedRows		=	( self::$loadedRows + $rows );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < round( $rowsCount / 1.25 ) ) ) {
					$nextLimit				=	( $pageLimit - count( $rows ) );

					if ( $nextLimit <= 0 ) {
						$nextLimit			=	1;
					}

					$this->set( 'paging_limit', $nextLimit );

					$cache[$cacheId]		=	( $rows + $this->rows( $output ) );

					$this->set( 'paging_limit', $pageLimit );
				} else {
					$cache[$cacheId]		=	$rows;
				}
			}
		} elseif ( $output != 'count' ) {
			$this->set( 'paging_limitstart', ( $this->get( 'paging_limitstart', 0, GetterInterface::INT ) + count( $cache[$cacheId] ) ) );
		}

		return $cache[$cacheId];
	}

	/**
	 * Retrieves activity row
	 *
	 * @param int $id
	 * @return ActivityTable
	 */
	public function row( $id )
	{
		if ( ! $id ) {
			return new ActivityTable();
		}

		if ( isset( self::$loadedRows[$id] ) ) {
			return self::$loadedRows[$id];
		}

		static $cache		=	array();

		if ( ! isset( $cache[$id] ) ) {
			$rows			=	$this->reset()->setId( $id )->rows();

			if ( isset( $rows[$id] ) ) {
				$row		=	$rows[$id];
			} else {
				$row		=	new ActivityTable();
			}

			$cache[$id]		=	$row;
		}

		return $cache[$id];
	}

	/**
	 * Outputs activity HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function activity( $view = null )
	{
		return $this->display( $view );
	}
}