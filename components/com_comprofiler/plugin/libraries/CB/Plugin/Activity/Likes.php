<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\LikeTypeTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\LikeTable;

defined('CBLIB') or die();

/**
 * @method string getAsset()
 * @method Likes setAsset( $asset )
 * @method array getAssets()
 * @method Likes setAssets( $assets )
 * @method UserTable getUser()
 * @method Likes setUser( $user )
 * @method int|array getId()
 * @method Likes setId( $id )
 * @method array getModerators()
 * @method Likes setModerators( $moderators )
 * @method int|array getType()
 * @method Likes setType( $type )
 * @method int|array getUserId()
 * @method Likes setUserId( $userId )
 * @method bool getInline()
 * @method Likes setInline( $inline )
 */
class Likes extends Stream implements LikesInterface
{
	/** @var array $defaults */
	protected $defaults					=	array(	'layout'				=>	'button',
													'count'					=>	true,
													'include'				=>	0,
													'exclude'				=>	0,
													'paging'				=>	true,
													'paging_first_limit'	=>	15,
													'paging_limit'			=>	15
												);

	/** @var LikeTable[] $loadedRows */
	protected static $loadedRows		=	array();

	/**
	 * @param string $name
	 * @param array  $arguments
	 * @return self|string|int|array|null
	 */
	public function __call( $name, $arguments )
	{
		$method									=	substr( $name, 0, 3 );

		if ( in_array( $method, array( 'get', 'set' ) ) ) {
			$variables							=	array( 'asset', 'assets', 'user', 'id', 'moderators', 'user_id', 'type', 'inline' );
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
							case 'type':
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
	 * Returns an array of types for likes
	 *
	 * @return LikeTypeTable[]
	 */
	public function types()
	{
		return CBActivity::loadLikeOptions( true, $this );
	}

	/**
	 * Retrieves likes rows or row count
	 *
	 * @param string $output
	 * @return LikeTable[]|int
	 */
	public function rows( $output = null )
	{
		global $_CB_database, $_PLUGINS;

		static $cache						=	array();

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

		if ( $this->asset() && ( ! in_array( 'all', $this->assets() ) ) ) {
			if ( ( strpos( $this->asset(), '%' ) !== false ) || ( strpos( $this->asset(), '_' ) !== false ) ) {
				$where[]					=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $this->asset() );
			} else {
				$where[]					=	"a." . $_CB_database->NameQuote( 'asset' ) . " = " . $_CB_database->Quote( $this->asset() );
			}
		}

		if ( ! $hasId ) {
			$type							=	$this->get( 'type', null, GetterInterface::RAW );

			if ( ( ( $type !== '' ) && ( $type !== null ) ) || ( is_array( $type ) && $type ) ) {
				if ( is_array( $type ) ) {
					$where[]				=	"a." . $_CB_database->NameQuote( 'type' ) . " IN " . $_CB_database->safeArrayOfIntegers( $type );
				} else {
					$where[]				=	"a." . $_CB_database->NameQuote( 'type' ) . " = " . (int) $type;
				}
			}
		}

		$_PLUGINS->trigger( 'activity_onQueryLikesStream', array( $output, &$select, &$join, &$where, &$this ) );

		$query								=	"SELECT " . implode( ", ", $select )
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_likes' ) . " AS a"
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
											.	" ON cb." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'user_id' )
											.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
											.	" ON j." . $_CB_database->NameQuote( 'id' ) . " = cb." . $_CB_database->NameQuote( 'id' )
											.	( $join ? "\n " . implode( "\n ", $join ) : null )
											.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
											.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
											.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
											.	( $where ? "\n AND " . implode( "\n AND ", $where ) : null );

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

				$rows						=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\LikeTable', array( $_CB_database ) );
				$rowsCount					=	count( $rows );
				$userIds					=	array();

				/** @var LikeTable[] $rows */
				foreach ( $rows as $row ) {
					if ( preg_match( '/^profile\.(\d+)/', $row->get( 'asset', null, GetterInterface::STRING ), $matches ) ) {
						$userIds[]			=	(int) $matches[1];
					}

					$userIds[]				=	$row->get( 'user_id', 0, GetterInterface::INT );
				}

				if ( $userIds ) {
					\CBuser::advanceNoticeOfUsersNeeded( $userIds );
				}

				$_PLUGINS->trigger( 'activity_onLoadLikesStream', array( &$rows, $this ) );

				if ( $rows ) {
					self::$loadedRows		=	( self::$loadedRows + $rows );
				}

				if ( $paging && $rowsCount && ( count( $rows ) < $rowsCount ) ) {
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
	 * Retrieves likes row
	 *
	 * @param int $id
	 * @return LikeTable
	 */
	public function row( $id )
	{
		if ( ! $id ) {
			return new LikeTable();
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
				$row		=	new LikeTable();
			}

			$cache[$id]		=	$row;
		}

		return $cache[$id];
	}

	/**
	 * Outputs likes HTML
	 *
	 * @param null|string $view
	 * @return string
	 */
	public function likes( $view = null )
	{
		return $this->display( $view );
	}
}