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
use CB\Plugin\Activity\Table\NotificationTable;

defined('CBLIB') or die();

/**
 * @method string getAsset()
 * @method Notifications setAsset( $asset )
 * @method array getAssets()
 * @method Notifications setAssets( $assets )
 * @method UserTable getUser()
 * @method Notifications setUser( $user )
 * @method int|array getId()
 * @method Notifications setId( $id )
 * @method array getModerators()
 * @method Notifications setModerators( $moderators )
 * @method int|array getUserId()
 * @method Notifications setUserId( $userId )
 * @method string|array getDate()
 * @method Notifications setDate( $datetime )
 * @method string getSearch()
 * @method Notifications setSearch( $search )
 * @method string getTitle()
 * @method Notifications setTitle( $title )
 * @method string getMessage()
 * @method Notifications setMessage( $message )
 * @method string getRead()
 * @method Notifications setRead( $read )
 * @method string getHidden()
 * @method Notifications setHidden( $hidden )
 * @method bool getInline()
 * @method Notifications setInline( $inline )
 * @method int getPublished()
 * @method Notifications setPublished( $published )
 */
class Notifications extends Stream implements NotificationsInterface
{
	/** @var array $defaults */
	protected $defaults					=	array(	'layout'					=>	'button',
													'direction'					=>	'down',
													'auto_update'				=>	false,
													'auto_load'					=>	false,
													'pinned'					=>	false,
													'paging'					=>	true,
													'paging_first_limit'		=>	15,
													'paging_limit'				=>	15,
													'parser_substitutions'		=>	true,
													'parser_emotes'				=>	true,
													'parser_hashtags'			=>	true,
													'parser_profiles'			=>	true,
													'parser_links'				=>	true,
													'parser_prepare'			=>	false
												);

	/** @var NotificationTable[] $loadedRows */
	protected static $loadedRows		=	array();

	/**
	 * Constructor for notifications object
	 *
	 * @param null|string|array  $assets
	 * @param null|int|UserTable $user
	 */
	public function __construct( $assets = null, $user = null )
	{
		if ( ! $assets ) {
			$assets		=	array( 'all', 'global' );
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
			$variables							=	array( 'asset', 'assets', 'user', 'id', 'moderators', 'user_id', 'date', 'search', 'title', 'message', 'read', 'hidden', 'inline', 'published' );
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
	 * Gets the primary notifications asset
	 *
	 * @return string
	 */
	public function asset()
	{
		return 'notification.' . $this->user()->get( 'id', 0, GetterInterface::INT );
	}

	/**
	 * Retrieves notifications rows or row count
	 *
	 * @param string $output
	 * @return NotificationTable[]|int
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

		if ( ! ( $myId && ( $this->get( 'hidden', 'visibile', GetterInterface::STRING ) == 'hidden' ) ) ) {
			if ( in_array( 'global', $this->assets() ) )  {
				$where[]					=	"( a." . $_CB_database->NameQuote( 'user' ) . " = " . $this->user()->get( 'id', 0, GetterInterface::INT )
											.	" OR a." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( 'global' ) . " )";
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'user' ) . " = " . $this->user()->get( 'id', 0, GetterInterface::INT );
			}
		}

		if ( $this->assets() && ( ! in_array( 'all', $this->assets() ) ) ) {
			$assets							=	array();
			$wildcards						=	array();

			$this->queryAssets( $this, $assets, $wildcards );

			if ( $assets|| $wildcards ) {
				$assetsWhere				=	array();

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
											.	" AND b." . $_CB_database->NameQuote( 'type' ) . " = " . $_CB_database->Quote( 'notification' )
											.	" AND b." . $_CB_database->NameQuote( 'item' ) . " = a." . $_CB_database->NameQuote( 'id' );

					if ( in_array( $hidden, array( 'status', 'hidden' ) ) ) {
						if ( $output != 'count' ) {
							$select[]		=	"b." . $_CB_database->NameQuote( 'type' ) . " AS _hidden";
						}
					} else {
						$where[]			=	"b." . $_CB_database->NameQuote( 'id' ) . " IS NULL";
					}
				}

				$read						=	$this->get( 'read', null, GetterInterface::STRING );

				if ( in_array( $read, array( 'status', 'read', 'unread', 'readonly', 'unreadonly' ) ) ) {
					$join[]					=	( in_array( $read, array( 'readonly', 'unreadonly' ) ) ? "INNER JOIN " : "LEFT JOIN " ) . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_read' ) . " AS c"
											.	" ON c." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $myId
											.	" AND c." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset() )
											.	" AND c." . $_CB_database->NameQuote( 'date' ) . ( $read == 'unreadonly' ? " < " : " >= " ) . "a." . $_CB_database->NameQuote( 'date' );

					if ( $output != 'count' ) {
						$select[]			=	"c." . $_CB_database->NameQuote( 'id' ) . " AS _read";
					}
				}
			}
		}

		if ( ( $this->get( 'published', null, GetterInterface::RAW ) !== '' ) && ( $this->get( 'published', null, GetterInterface::RAW ) !== null ) ) {
			$where[]						=	"a." . $_CB_database->NameQuote( 'published' ) . " = " . $this->get( 'published', null, GetterInterface::INT );
		}

		$_PLUGINS->trigger( 'activity_onQueryNotificationsStream', array( $output, &$select, &$join, &$where, &$this ) );

		$query								=	"SELECT " . implode( ", ", $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_notifications' ) . " AS a"
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

			if ( $this->get( 'pinned', false, GetterInterface::BOOLEAN ) ) {
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

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\NotificationTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );
				$userIds					=	array( $this->user()->get( 'id', 0, GetterInterface::INT ) );

				/** @var NotificationTable[] $rows */
				foreach ( $rows as $row ) {
					if ( preg_match( '/^profile\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
						$userIds[]			=	(int) $matches[1];
					}

					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'activity_onLoadNotificationsStream', array( &$rows, $this ) );

				if ( $rows ) {
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
	 * Retrieves notifications row
	 *
	 * @param int $id
	 * @return NotificationTable
	 */
	public function row( $id )
	{
		if ( ! $id ) {
			return new NotificationTable();
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
				$row		=	new NotificationTable();
			}

			$cache[$id]		=	$row;
		}

		return $cache[$id];
	}

	/**
	 * Outputs notifications HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function notifications( $view = null )
	{
		return $this->display( $view );
	}
}