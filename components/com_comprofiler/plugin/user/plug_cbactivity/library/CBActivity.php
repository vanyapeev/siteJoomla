<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity;

use CB\Plugin\Activity\Table\ActivityTable;
use CB\Plugin\Activity\Table\NotificationTable;
use CB\Plugin\Activity\Table\CommentTable;
use CB\Plugin\Activity\Table\TagTable;
use CB\Plugin\Activity\Table\FollowTable;
use CB\Plugin\Activity\Table\LikeTable;
use CBLib\Application\Application;
use CBLib\Registry\ParamsInterface;
use CB\Database\Table\FieldTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Plugin\Activity\Table\ActionTable;
use CB\Plugin\Activity\Table\LocationTable;
use CB\Plugin\Activity\Table\EmoteTable;
use CB\Plugin\Activity\Table\LikeTypeTable;
use CB\Database\Table\UserTable;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class CBActivity
{

	/**
	 * @return Registry
	 */
	static public function getGlobalParams()
	{
		global $_PLUGINS;

		static $params	=	null;

		if ( ! $params ) {
			$plugin		=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );
			$params		=	new Registry();

			if ( $plugin ) {
				$params->load( $plugin->params );
			}
		}

		return $params;
	}

	/**
	 * Try to find the stream asset from object id
	 *
	 * @param string $type
	 * @param int    $id
	 * @return null|string
	 */
	static public function getAsset( $type, $id )
	{
		if ( ! $id ) {
			return null;
		}

		static $cache				=	array();

		if ( ! isset( $cache[$type][$id] ) ) {
			$asset					=	null;

			switch ( $type ) {
				case 'likes':
					$row			=	new LikeTable();

					$row->load( $id );

					$asset			=	$row->get( 'asset', null, GetterInterface::STRING );
					break;
				case 'following':
					$row			=	new FollowTable();

					$row->load( $id );

					$asset			=	$row->get( 'asset', null, GetterInterface::STRING );
					break;
				case 'tags':
					$row			=	new TagTable();

					$row->load( $id );

					$asset			=	$row->get( 'asset', null, GetterInterface::STRING );
					break;
				case 'comments':
					$row			=	new CommentTable();

					$row->load( $id );

					$asset			=	$row->get( 'asset', null, GetterInterface::STRING );
					break;
				case 'activity':
					$row			=	new ActivityTable();

					$row->load( $id );

					$asset			=	$row->get( 'asset', null, GetterInterface::STRING );
					break;
				case 'notification':
					$row			=	new NotificationTable();

					$row->load( $id );

					$asset			=	$row->get( 'asset', null, GetterInterface::STRING );
					break;
			}

			$cache[$type][$id]		=	$asset;
		}

		return $cache[$type][$id];
	}

	/**
	 * Try to build the asset source
	 * 
	 * @param string $asset
	 * @return mixed
	 */
	static public function getSource( $asset )
	{
		global $_PLUGINS;

		if ( ! $asset ) {
			return null;
		}

		static $cache				=	array();

		if ( ! isset( $cache[$asset] ) ) {
			$source					=	null;

			if ( preg_match( '/^profile\.(\d+)/', $asset, $matches ) ) {
				$source				=	\CBuser::getInstance( ( isset( $matches[1] ) ? (int) $matches[1] : 0 ) )->getUserData();
			} elseif ( preg_match( '/^activity\.(\d+)/', $asset, $matches ) ) {
				static $activity	=	array();

				$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				if ( ! isset( $activity[$id] ) ) {
					$row			=	new ActivityTable();

					$row->load( $id );

					$activity[$id]	=	$row;
				}

				$source				=	$activity[$id];
			} elseif ( preg_match( '/^comment\.(\d+)/', $asset, $matches ) ) {
				static $comments	=	array();

				$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				if ( ! isset( $comments[$id] ) ) {
					$row			=	new CommentTable();

					$row->load( $id );

					$comments[$id]	=	$row;
				}

				$source				=	$comments[$id];
			} elseif ( preg_match( '/^tag\.(\d+)/', $asset, $matches ) ) {
				static $tags		=	array();

				$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				if ( ! isset( $tags[$id] ) ) {
					$row			=	new TagTable();

					$row->load( $id );

					$tags[$id]		=	$row;
				}

				$source				=	$tags[$id];
			} elseif ( preg_match( '/^follow\.(\d+)/', $asset, $matches ) ) {
				static $follow		=	array();

				$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				if ( ! isset( $follow[$id] ) ) {
					$row			=	new FollowTable();

					$row->load( $id );

					$follow[$id]	=	$row;
				}

				$source				=	$follow[$id];
			} elseif ( preg_match( '/^like\.(\d+)/', $asset, $matches ) ) {
				static $like		=	array();

				$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				if ( ! isset( $like[$id] ) ) {
					$row			=	new LikeTable();

					$row->load( $id );

					$like[$id]		=	$row;
				}

				$source				=	$like[$id];
			} elseif ( preg_match( '/^notification\.(\d+)/', $asset, $matches ) ) {
				static $notif		=	array();

				$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				if ( ! isset( $notif[$id] ) ) {
					$row			=	new NotificationTable();

					$row->load( $id );

					$notif[$id]		=	$row;
				}

				$source				=	$notif[$id];
			}

			$_PLUGINS->trigger( 'activity_onAssetSource', array( $asset, &$source ) );

			$cache[$asset]			=	$source;
		}

		return $cache[$asset];
	}

	/**
	 * Utility function for grabbing a field while also ensuring proper display access to it
	 *
	 * @param int $fieldId
	 * @param int $profileId
	 * @return FieldTable|null
	 */
	static public function getField( $fieldId, $profileId )
	{
		if ( ! $fieldId ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		static $fields						=	array();

		if ( ! isset( $fields[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$fields[$profileId][$userId]	=	$profileUser->_getCbTabs( false )->_getTabFieldsDb( null, $profileUser->getUserData(), 'profile' );
		}

		if ( ! isset( $fields[$profileId][$userId][$fieldId] ) ) {
			return null;
		}

		$field								=	$fields[$profileId][$userId][$fieldId];

		if ( ! ( $field->params instanceof ParamsInterface ) ) {
			$field->params					=	new Registry( $field->params );
		}

		return $field;
	}

	/**
	 * Utility function for grabbing the activity tab while also ensuring proper display access to it
	 *
	 * @param int $tabId
	 * @param int $profileId
	 * @return TabTable|null
	 */
	static public function getTab( $tabId, $profileId )
	{
		static $profileTab					=	null;

		if ( ! $tabId ) {
			if ( $profileTab === null ) {
				$profileTab					=	new TabTable();

				$profileTab->load( array( 'pluginclass' => 'cbactivityTab' ) );
			}

			$tabId							=	$profileTab->get( 'tabid', 0, GetterInterface::INT );
		}

		if ( ! $tabId ) {
			return null;
		}

		$userId								=	Application::MyUser()->getUserId();

		static $tabs						=	array();

		if ( ! isset( $tabs[$profileId][$userId] ) ) {
			$profileUser					=	\CBuser::getInstance( $profileId, false );

			$tabs[$profileId][$userId]		=	$profileUser->_getCbTabs( false )->_getTabsDb( $profileUser->getUserData(), 'profile' );
		}

		if ( ! isset( $tabs[$profileId][$userId][$tabId] ) ) {
			return null;
		}

		$tab								=	$tabs[$profileId][$userId][$tabId];

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params					=	new Registry( $tab->params );
		}

		return $tab;
	}

	/**
	 * Returns an array of users connections
	 *
	 * @param int  $profileId
	 * @param bool $raw
	 * @return array
	 */
	static public function getConnections( $profileId, $raw = false )
	{
		if ( ( ! $profileId ) || ( ! Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) ) ) {
			return array();
		}

		static $cache				=	array();

		if ( ! isset( $cache[$profileId] ) ) {
			$cbConnection			=	new \cbConnection( $profileId );

			$cache[$profileId]		=	$cbConnection->getActiveConnections( $profileId );
		}

		if ( ! $raw ) {
			$connections			=	array();

			foreach ( $cache[$profileId] as $connection ) {
				$connections[]		=	(int) $connection->id;
			}

			return $connections;
		}

		return $cache[$profileId];
	}

	/**
	 * Returns an array of users following
	 *
	 * @param int  $profileId
	 * @param bool $raw
	 * @return array
	 */
	static public function getFollowing( $profileId, $raw = false )
	{
		global $_CB_database;

		if ( ! $profileId ) {
			return array();
		}

		static $cache				=	array();

		if ( ! isset( $cache[$profileId] ) ) {
			$query					=	"SELECT " . $_CB_database->NameQuote( 'id' )
									.	", " . $_CB_database->NameQuote( 'asset' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_following' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $profileId;
			$_CB_database->setQuery( $query );

			$cache[$profileId]		=	$_CB_database->loadAssocList( 'asset', 'id' );
		}

		if ( ! $raw ) {
			return array_keys( $cache[$profileId] );
		}

		return $cache[$profileId];
	}

	/**
	 * Returns an array of users likes
	 *
	 * @param int  $profileId
	 * @param bool $raw
	 * @return array
	 */
	static public function getLikes( $profileId, $raw = false )
	{
		global $_CB_database;

		if ( ! $profileId ) {
			return array();
		}

		static $cache				=	array();

		if ( ! isset( $cache[$profileId] ) ) {
			$query					=	"SELECT *"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_likes' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $profileId;
			$_CB_database->setQuery( $query );

			$cache[$profileId]		=	$_CB_database->loadAssocList( 'asset' );
		}

		if ( ! $raw ) {
			return array_keys( $cache[$profileId] );
		}

		return $cache[$profileId];
	}

	/**
	 * Finds the asset based off override parameters for a row
	 *
	 * @param string                     $type
	 * @param ActivityTable|CommentTable $row
	 * @param Activity|Comments          $stream
	 * @return string
	 */
	static public function findAssetOverride( $type, $row, $stream )
	{
		$asset								=	$row->get( 'asset', null, GetterInterface::STRING );

		if ( $type == 'comments' ) {
			if ( $row instanceof CommentTable ) {
				$asset						=	self::findParamOverrde( $row, 'replies_asset', 'comment' );

				if ( ! $asset ) {
					$asset					=	'comment';
				}

				switch ( $asset ) {
					case 'asset':
						$asset				=	$row->get( 'asset', null, GetterInterface::STRING );
						break;
					case 'stream':
						if ( $stream ) {
							$asset			=	$stream->asset();
						} else {
							$asset			=	'comment.' . $row->get( 'id', 0, GetterInterface::INT );
						}
						break;
					case 'comment':
						$asset				=	'comment.' . $row->get( 'id', 0, GetterInterface::INT );
						break;
				}
			} else {
				$asset						=	self::findParamOverrde( $row, 'comments_asset', 'activity' );

				if ( ! $asset ) {
					$asset					=	'activity';
				}

				switch ( $asset ) {
					case 'asset':
						$asset				=	$row->get( 'asset', null, GetterInterface::STRING );
						break;
					case 'stream':
						if ( $stream ) {
							$asset			=	$stream->asset();
						} else {
							$asset			=	'activity.' . $row->get( 'id', 0, GetterInterface::INT );
						}
						break;
					case 'activity':
						$asset				=	'activity.' . $row->get( 'id', 0, GetterInterface::INT );
						break;
				}
			}
		} elseif ( $type == 'tags' ) {
			if ( $row instanceof CommentTable ) {
				$asset						=	self::findParamOverrde( $row, 'tags_asset', 'comment' );

				if ( ! $asset ) {
					$asset					=	'comment';
				}

				switch ( $asset ) {
					case 'asset':
						$asset				=	$row->get( 'asset', null, GetterInterface::STRING );
						break;
					case 'stream':
						if ( $stream ) {
							$asset			=	$stream->asset();
						} else {
							$asset			=	'comment.' . $row->get( 'id', 0, GetterInterface::INT );
						}
						break;
					case 'comment':
						$asset				=	'comment.' . $row->get( 'id', 0, GetterInterface::INT );
						break;
				}
			} else {
				$asset						=	self::findParamOverrde( $row, 'tags_asset', 'activity' );

				if ( ! $asset ) {
					$asset					=	'activity';
				}

				switch ( $asset ) {
					case 'asset':
						$asset				=	$row->get( 'asset', null, GetterInterface::STRING );
						break;
					case 'stream':
						if ( $stream ) {
							$asset			=	$stream->asset();
						} else {
							$asset			=	'activity.' . $row->get( 'id', 0, GetterInterface::INT );
						}
						break;
					case 'activity':
						$asset				=	'activity.' . $row->get( 'id', 0, GetterInterface::INT );
						break;
				}
			}
		} elseif ( $type == 'likes' ) {
			if ( $row instanceof CommentTable ) {
				$asset						=	self::findParamOverrde( $row, 'likes_asset', 'comment' );

				if ( ! $asset ) {
					$asset					=	'comment';
				}

				switch ( $asset ) {
					case 'asset':
						$asset				=	$row->get( 'asset', null, GetterInterface::STRING );
						break;
					case 'stream':
						if ( $stream ) {
							$asset			=	$stream->asset();
						} else {
							$asset			=	'comment.' . $row->get( 'id', 0, GetterInterface::INT );
						}
						break;
					case 'comment':
						$asset				=	'comment.' . $row->get( 'id', 0, GetterInterface::INT );
						break;
				}
			} else {
				$asset						=	self::findParamOverrde( $row, 'likes_asset', 'activity' );

				if ( ! $asset ) {
					$asset					=	'activity';
				}

				switch ( $asset ) {
					case 'asset':
						$asset				=	$row->get( 'asset', null, GetterInterface::STRING );
						break;
					case 'stream':
						if ( $stream ) {
							$asset			=	$stream->asset();
						} else {
							$asset			=	'activity.' . $row->get( 'id', 0, GetterInterface::INT );
						}
						break;
					case 'activity':
						$asset				=	'activity.' . $row->get( 'id', 0, GetterInterface::INT );
						break;
				}
			}
		}

		return $asset;
	}

	/**
	 * Finds the parameter override value if available
	 *
	 * @param ActivityTable|CommentTable|TagTable|FollowTable|LikeTable|NotificationTable $row
	 * @param string                                                                      $param
	 * @param mixed                                                                       $default
	 * @param Activity|Comments|Tags|Following|Likes|Notifications                        $stream
	 * @return mixed
	 */
	static public function findParamOverrde( $row, $param, $default = null, $stream = null )
	{
		if ( $row instanceof ActivityTable ) {
			$override		=	$row->params()->get( 'defaults.' . $param, null, GetterInterface::STRING );
		} else {
			$override		=	null;
		}

		$value				=	$row->params()->get( 'overrides.' . $param, $override, GetterInterface::STRING );

		if ( ( $value === '' ) || ( $value === null ) || ( $value === '-1' ) ) {
			if ( ! $stream ) {
				$value		=	$default;
			} else {
				if ( is_bool( $default ) ) {
					$type	=	GetterInterface::BOOLEAN;
				} elseif ( is_int( $default ) ) {
					$type	=	GetterInterface::INT;
				} else {
					$type	=	GetterInterface::STRING;
				}

				$value		=	$stream->get( $param, $default, $type );
			}
		}

		return $value;
	}

	/**       
	 * Prefetch assets from rows based off type
	 * This primarily helps determine if a row has any comments, tags, etc..
	 *
	 * @param string                         $type
	 * @param ActivityTable[]|CommentTable[] $rows
	 * @param Activity|Comments              $stream
	 */
	static public function prefetchAssets( $type, $rows, $stream )
	{
		global $_CB_database;

		static $cache							=	array();

		if ( ! $rows ) {
			return;
		}

		if ( ! isset( $cache[$type] ) ) {
			$cache[$type]						=	array();
		}

		$assets									=	array();

		$queryAssets							=	array();
		$queryWildcards							=	array();

		foreach ( $rows as $row ) {
			$rowAssets							=	self::findAssetOverride( $type, $row, $stream );

			if ( ! is_array( $rowAssets ) ) {
				$rowAssets						=	explode( ',', $rowAssets );
			}

			foreach ( $rowAssets as $rowAsset ) {
				if ( ! isset( $cache[$type][$rowAsset] ) && ( ! in_array( $rowAsset, $assets ) ) ) {
					$assets[]					=	$rowAsset;

					if ( ( strpos( $rowAsset, '%' ) !== false ) || ( strpos( $rowAsset, '_' ) !== false ) ) {
						$queryWildcards[]		=	$rowAsset;
					} else {
						$queryAssets[]			=	$rowAsset;
					}
				}
			}
		}

		if ( $assets ) {
			if ( in_array( $type, array( 'comments', 'replies' ) ) ) {
				$table							=	'#__comprofiler_plugin_activity_comments';
			} elseif ( $type == 'tags' ) {
				$table							=	'#__comprofiler_plugin_activity_tags';
			} elseif ( $type == 'likes' ) {
				$table							=	'#__comprofiler_plugin_activity_likes';
			} else {
				return;
			}

			$queryAssets						=	array_unique( $queryAssets );
			$queryWildcards						=	array_unique( $queryWildcards );

			$assetsWhere						=	array();

			if ( $queryAssets ) {
				$assetsWhere[]					=	"a." . $_CB_database->NameQuote( 'asset' ) . ( count( $queryAssets ) > 1 ? " IN " . $_CB_database->safeArrayOfStrings( $queryAssets ) : " = " . $_CB_database->Quote( $queryAssets[0] ) );
			}

			if ( $queryWildcards ) {
				foreach ( $queryWildcards as $queryWildcard ) {
					$assetsWhere[]				=	"a." . $_CB_database->NameQuote( 'asset' ) . " LIKE " . $_CB_database->Quote( $queryWildcard );
				}
			}

			$query								=	"SELECT a." . $_CB_database->NameQuote( 'asset' )
												.	"\n FROM " . $_CB_database->NameQuote( $table ) . " AS a"
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
												.	" ON cb." . $_CB_database->NameQuote( 'id' ) . " = a." . $_CB_database->NameQuote( 'user_id' )
												.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
												.	" ON j." . $_CB_database->NameQuote( 'id' ) . " = cb." . $_CB_database->NameQuote( 'id' )
												.	"\n WHERE cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
												.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
												.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0"
												.	"\n AND " . ( count( $assetsWhere ) > 1 ? "( " . implode( " OR ", $assetsWhere ) . " )" : $assetsWhere[0] );
			$_CB_database->setQuery( $query );
			$prefetch							=	array_unique( $_CB_database->loadResultArray() );

			foreach ( $assets as $asset ) {
				if ( ( strpos( $asset, '%' ) !== false ) || ( strpos( $asset, '_' ) !== false ) ) {
					$assetExists				=	( preg_grep( '/^' . str_replace( '%', '.+', preg_quote( $asset, '/' ) ) . '/', $prefetch ) ? true : false );
				} else {
					$assetExists				=	in_array( $asset, $prefetch );
				}

				$cache[$type][$asset]			=	$assetExists;
			}
		}

		if ( $cache[$type] ) {
			foreach ( $rows as $row ) {
				$rowAssets						=	self::findAssetOverride( $type, $row, $stream );

				if ( ! is_array( $rowAssets ) ) {
					$rowAssets					=	explode( ',', $rowAssets );
				}

				foreach ( $rowAssets as $rowAsset ) {
					if ( isset( $cache[$type][$rowAsset] ) ) {
						$row->set( '_' . $type, $cache[$type][$rowAsset] );
					}
				}
			}
		}
	}

	/**
	 * Checks if a user can create posts in the supplied stream
	 *
	 * @param string                                               $type
	 * @param Activity|Comments|Tags|Following|Likes|Notifications $stream
	 * @param null|UserTable                                       $user
	 * @return bool
	 */
	static public function canCreate( $type, $stream, $user = null )
	{
		global $_PLUGINS;

		static $cache											=	array();

		if ( $stream instanceof NotificationsInterface ) {
			// Notifications can not be created from frontend operations and must be generated by the system:
			return false;
		}

		if ( ! $user ) {
			$user												=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user												=	\CBuser::getInstance( $user, false )->getUserData();
		}

		$userId													=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ( ! $userId ) || ( ! $user->get( 'approved', 1, GetterInterface::INT ) ) || ( ! $user->get( 'confirmed', 1, GetterInterface::INT ) ) || $user->get( 'block', 0, GetterInterface::INT ) ) {
			return false;
		}

		$streamId												=	$stream->id();

		if ( ! isset( $cache[$userId][$type][$streamId] ) ) {
			$checks												=	array(	'moderate'		=>	true,
																			'accesslevel'	=>	false,
																			'stream'		=>	array(	'connected'	=>	false,
																										'owner'		=>	false,
																										'notowner'	=>	false
																									),
																			'asset'			=>	array(	'connected'	=>	false,
																										'owner'		=>	false,
																										'notowner'	=>	false
																									),
																			'assetonly'		=>	array(	'connected'	=>	false,
																										'owner'		=>	false,
																										'notowner'	=>	false
																									)
																		);

			switch ( $type ) {
				case 'activity':
					if ( $stream instanceof ActivityInterface ) {
						if ( ! $stream->get( 'create', true, GetterInterface::BOOLEAN ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['accesslevel']					=	$stream->get( 'create_access', 2, GetterInterface::INT );
						$checks['asset']['connected']			=	$stream->get( 'create_connected', true, GetterInterface::BOOLEAN );
					} else {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
					break;
				case 'comment':
					if ( $stream instanceof ActivityInterface ) {
						if ( ( ! $stream->get( 'comments', true, GetterInterface::BOOLEAN ) ) || ( ! $stream->get( 'comments_create', true, GetterInterface::BOOLEAN ) ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['accesslevel']					=	$stream->get( 'comments_create_access', 2, GetterInterface::INT );
						$checks['asset']['connected']			=	$stream->get( 'comments_create_connected', true, GetterInterface::BOOLEAN );
					} elseif ( $stream instanceof CommentsInterface ) {
						if ( ! $stream->get( 'create', true, GetterInterface::BOOLEAN ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['accesslevel']					=	$stream->get( 'create_access', 2, GetterInterface::INT );
						$checks['asset']['connected']			=	$stream->get( 'create_connected', true, GetterInterface::BOOLEAN );
					} else {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
					break;
				case 'reply':
					if ( $stream instanceof ActivityInterface ) {
						if ( ( ! $stream->get( 'comments', true, GetterInterface::BOOLEAN ) ) || ( ! $stream->get( 'comments_replies', false, GetterInterface::BOOLEAN ) ) || ( ! $stream->get( 'comments_replies_create', true, GetterInterface::BOOLEAN ) ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['accesslevel']					=	$stream->get( 'comments_replies_create_access', 2, GetterInterface::INT );
						$checks['asset']['connected']			=	$stream->get( 'comments_replies_create_connected', true, GetterInterface::BOOLEAN );
					} elseif ( $stream instanceof CommentsInterface ) {
						if ( ( ! $stream->get( 'replies', false, GetterInterface::BOOLEAN ) ) || ( ! $stream->get( 'replies_create', true, GetterInterface::BOOLEAN ) ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['accesslevel']					=	$stream->get( 'comments_replies_create_access', 2, GetterInterface::INT );
						$checks['asset']['connected']			=	$stream->get( 'comments_replies_create_connected', true, GetterInterface::BOOLEAN );
					} else {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
					break;
				case 'tag':
					if ( $stream instanceof ActivityInterface ) {
						if ( ! $stream->get( 'tags', true, GetterInterface::BOOLEAN ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['stream']['owner']				=	true;
					} elseif ( $stream instanceof CommentsInterface ) {
						if ( ! $stream->get( 'tags', true, GetterInterface::BOOLEAN ) ) {
							$cache[$userId][$type][$streamId]	=	false;

							return false;
						}

						$checks['stream']['owner']				=	true;
					} elseif ( $stream instanceof TagsInterface ) {
						$checks['stream']['owner']				=	true;
					} else {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
					break;
				case 'follow':
					if ( $stream instanceof FollowingInterface ) {
						$checks['moderate']						=	false;
						$checks['assetonly']['connected']		=	$stream->get( 'connected', false, GetterInterface::BOOLEAN );
						$checks['assetonly']['notowner']		=	true;
					} else {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
					break;
				case 'like':
					if ( $stream instanceof LikesInterface ) {
						$checks['moderate']						=	false;
					} else {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
					break;
				default:
					$cache[$userId][$type][$streamId]			=	false;

					return false;
					break;
			}

			$canModerate										=	self::canModerate( $stream, $user );

			if ( $checks['moderate'] === true ) {
				if ( $canModerate ) {
					$cache[$userId][$type][$streamId]			=	true;

					return true;
				}
			}

			$streamOwner										=	$stream->user()->get( 'id', 0, GetterInterface::INT );

			if ( preg_match( '/^profile(?:\.(\d+)(?:\.field\.(\d+))?)?/', $stream->asset(), $matches ) ) {
				$assetOwner										=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

				$profileId										=	( isset( $matches[1] ) ? (int) $matches[1] : $streamOwner );
				$fieldId										=	( isset( $matches[2] ) ? (int) $matches[2] : $stream->get( 'field', 0, GetterInterface::INT ) );
				$tabId											=	$stream->get( 'tab', 0, GetterInterface::INT );

				if ( $fieldId ) {
					$field										=	self::getField( $fieldId, $profileId );

					if ( ! $field ) {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
				} else {
					$tab										=	self::getTab( $tabId, $profileId );

					if ( ! $tab ) {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
				}
			} else {
				$assetOwner										=	0;
			}

			foreach ( $checks as $type => $check ) {
				if ( ! is_array( $check ) ) {
					continue;
				}

				switch ( $type ) {
					case 'stream':
						$checkUser								=	$streamOwner;
						break;
					case 'asset':
						$checkUser								=	( $assetOwner ? $assetOwner : $streamOwner );
						break;
					case 'assetonly':
						$checkUser								=	$assetOwner;
						break;
					default:
						$checkUser								=	0;
						break;
				}

				foreach ( $check as $subType => $subCheck ) {
					if ( $subCheck === false ) {
						continue;
					}

					$denied										=	false;

					switch ( $subType ) {
						case 'owner':
							$denied								=	( $checkUser !== $userId );
							break;
						case 'notowner':
							$denied								=	( $checkUser === $userId );
							break;
						case 'connected':
							if ( Application::Config()->get( 'allowConnections', true, GetterInterface::BOOLEAN ) && ( ! $canModerate ) ) {
								$denied							=	( ( $checkUser !== $userId ) && ( ! in_array( $userId, self::getConnections( $checkUser ) ) ) );
							}
							break;
					}

					if ( $denied ) {
						$cache[$userId][$type][$streamId]		=	false;

						return false;
					}
				}
			}

			if ( $checks['accesslevel'] !== false ) {
				if ( ! Application::User( $userId )->canViewAccessLevel( $checks['accesslevel'] ) ) {
					$cache[$userId][$type][$streamId]			=	false;

					return false;
				}
			}

			$access												=	true;

			$_PLUGINS->trigger( 'activity_onStreamCreateAccess', array( &$access, $type, $user, $stream ) );

			$cache[$userId][$type][$streamId]					=	$access;
		}

		return $cache[$userId][$type][$streamId];
	}

	/**
	 * Checks if a user can moderate the stream
	 *
	 * @param Activity|Comments|Tags|Following|Notifications $stream
	 * @param null|UserTable                                 $user
	 * @return bool
	 */
	static public function canModerate( $stream, $user = null )
	{
		global $_PLUGINS;

		static $cache						=	array();

		if ( ! $user ) {
			$user							=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user							=	\CBuser::getInstance( $user, false )->getUserData();
		}

		$userId								=	$user->get( 'id', 0, GetterInterface::INT );

		if ( ( ! $userId ) || ( ! $user->get( 'approved', 1, GetterInterface::INT ) ) || ( ! $user->get( 'confirmed', 1, GetterInterface::INT ) ) || $user->get( 'block', 0, GetterInterface::INT ) ) {
			return false;
		}

		if ( Application::User( $userId )->isGlobalModerator() ) {
			return true;
		}

		if ( in_array( $userId, $stream->get( 'moderators', array(), GetterInterface::RAW ) ) ) {
			return true;
		}

		$streamId							=	$stream->id();

		if ( ! isset( $cache[$userId][$streamId] ) ) {
			$access							=	false;

			$_PLUGINS->trigger( 'activity_onStreamModerateAccess', array( &$access, $user, $stream ) );

			$cache[$userId][$streamId]		=	$access;
		}

		return $cache[$userId][$streamId];
	}

	/**
	 * Outputs a JSON ajax response
	 *
	 * @param null|string $message
	 * @param null|string $type
	 * @param null|string $output
	 * @param null|string $target
	 */
	static public function ajaxResponse( $message = null, $type = 'html', $output = null, $target = null )
	{
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-Type: application/json' );

		while ( @ob_end_clean() );

		if ( $message ) {
			switch ( $type ) {
				case 'notice':
					$message	=	'<div class="streamItemNotice">'
								.		'<div class="streamItemNoticeMessage">' . $message . '</div>'
								.		'<a href="javascript:void(0);" class="streamItemNoticeClose streamItemActionResponsesClose"><span class="streamIconClose fa fa-times"></span></a>'
								.	'</div>';
					break;
				case 'error':
				case 'warning':
				case 'info':
				case 'success':
					$message	=	'<div class="streamItemActionResponse streamItemAlert alert alert-' . htmlspecialchars( ( $type == 'error' ? 'danger' : $type ) ) . '">'
								.		'<div class="streamItemAlertMessage">' . $message . '</div>'
								.		'<a href="javascript:void(0);" class="streamItemAlertClose streamItemActionResponseClose"><span class="streamIconClose fa fa-times"></span></a>'
								.	'</div>';
					$output		=	'prepend';
					$target		=	'container';
					break;
			}
		}

		echo json_encode( array( 'message' => $message, 'type' => $type, 'output' => $output, 'target' => $target ) );

		exit();
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
				$plugin							=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );

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
	 * Reloads page headers for ajax responses
	 *
	 * @return null|string
	 */
	static public function reloadHeaders()
	{
		global $_CB_framework;

		if ( Application::Input()->get( 'format', null, GetterInterface::STRING ) != 'raw' ) {
			return null;
		}

		$_CB_framework->getAllJsPageCodes();

		// Reset meta headers as they can't be used inline anyway:
		$_CB_framework->document->_head['metaTags']		=	array();

		// Remove all non-jQuery scripts as they'll likely just cause errors due to redeclaration:
		foreach( $_CB_framework->document->_head['scriptsUrl'] as $url => $script ) {
			if ( ( strpos( $url, 'jquery.' ) === false ) || ( strpos( $url, 'migrate' ) !== false ) ) {
				unset( $_CB_framework->document->_head['scriptsUrl'][$url] );
			}
		}

		$header				=	$_CB_framework->document->outputToHead();

		if ( ! $header ) {
			return null;
		}

		$return				=	'<div class="streamItemHeaders" style="position: absolute; display: none; height: 0; width: 0; z-index: -999;">'
							.		'<script type="text/javascript">window.jQuery = cbjQuery; window.$ = cbjQuery;</script>'
							.		$header
							.	'</div>';

		return $return;
	}

	/**
	 * Returns file size formatted from bytes
	 *
	 * @param int $bytes
	 * @return string
	 */
	static public function getFormattedFileSize( $bytes )
	{
		if ( $bytes >= 1099511627776 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_TB', '%%COUNT%% TB|%%COUNT%% TBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1099511627776, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1073741824 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_GB', '%%COUNT%% GB|%%COUNT%% GBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1073741824, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1048576 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_MB', '%%COUNT%% MB|%%COUNT%% MBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1048576, 2, '.', '' ) ) );
		} elseif ( $bytes >= 1024 ) {
			return CBTxt::T( 'FILESIZE_FORMATTED_KB', '%%COUNT%% KB|%%COUNT%% KBs', array( '%%COUNT%%' => (float) number_format( $bytes / 1024, 2, '.', '' ) ) );
		}

		return CBTxt::T( 'FILESIZE_FORMATTED_B', '%%COUNT%% B|%%COUNT%% Bs', array( '%%COUNT%%' => (float) number_format( $bytes, 2, '.', '' ) ) );
	}

	/**
	 * Formats total using comma separation and reduce thousands
	 *
	 * @param int $total
	 * @return string
	 */
	static public function getFormattedTotal( $total )
	{
		if ( $total >= 1000 ) {
			$total	=	round( $total / 1000, 1 );

			return CBTxt::T( 'TOTAL_FORMATTED_SHORT', '[total]K', array( '[total]' => number_format( $total, ( floor( $total ) != $total ? 1 : 0 ) ) ) );
		}

		return number_format( $total );
	}

	/**
	 * Helper function for preparing post attachments
	 *
	 * @param ParamsInterface $attachments
	 * @return Registry
	 */
	static public function prepareAttachments( $attachments )
	{
		$links									=	new Registry();

		foreach ( $attachments as $i => $link ) {
			/** @var ParamsInterface $link */
			$type								=	$link->get( 'type', null, GetterInterface::STRING );

			if ( ! $type ) {
				$link->set( 'type', 'url' );
			}

			if ( $type != 'custom' ) {
				$url							=	$link->get( 'url', null, GetterInterface::STRING );

				if ( ! $url ) {
					continue;
				} elseif ( substr( $link['url'], 0, 3 ) == 'www' ) {
					$url						=	'http://' . $url;

					$link->set( 'url', $url );
				}

				if ( \JUri::isInternal( $url ) ) {
					$link->set( 'internal', true );
				} else {
					$link->set( 'internal', false );
				}
			}

			$media								=	$link->subTree( 'media' );

			switch ( $type ) {
				case 'custom':
					if ( ! $media->get( 'custom', null, GetterInterface::RAW ) ) {
						continue 2;
					}
					break;
				case 'video':
				case 'audio':
					if ( ( ! $media->get( 'url', null, GetterInterface::STRING ) ) || ( ! $media->get( 'mimetype', null, GetterInterface::STRING ) ) ) {
						continue 2;
					}
					break;
				case 'image':
				case 'file':
					if ( ! $media->get( 'url', null, GetterInterface::STRING ) ) {
						continue 2;
					}
					break;
				case 'url':
				default:
					if ( ( ! $media->get( 'url', null, GetterInterface::STRING ) ) || ( ! $link->get( 'thumbnail', true, GetterInterface::BOOLEAN ) ) ) {
						$link->set( 'media', false );
					}
					break;
			}

			if ( $type != 'custom' ) {
				if ( $link->get( 'media', null, GetterInterface::RAW ) !== false ) {
					$mediaUrl					=	$media->get( 'url', null, GetterInterface::STRING );

					if ( substr( $mediaUrl, 0, 3 ) == 'www' ) {
						$mediaUrl				=	'http://' . $mediaUrl;

						$media->set( 'url', $mediaUrl );
					}

					if ( \JUri::isInternal( $mediaUrl ) ) {
						$media->set( 'internal', true );
					} else {
						$media->set( 'internal', false );
					}

					if ( $type == 'url' ) {
						/** @var ParamsInterface $thumbnails */
						$thumbnails				=	$link->subTree( 'thumbnails' );

						foreach ( $thumbnails as $t => $thumbnail ) {
							/** @var ParamsInterface $thumbnail */
							$thumbnailUrl		=	$thumbnail->get( 'url', null, GetterInterface::STRING );

							if ( ! $thumbnailUrl ) {
								$thumbnails->unsetEntry( $t );
								continue;
							}

							if ( substr( $thumbnailUrl, 0, 3 ) == 'www' ) {
								$thumbnailUrl	=	'http://' . $thumbnailUrl;

								$thumbnail->set( 'url', $thumbnailUrl );
							}

							if ( \JUri::isInternal( $thumbnailUrl ) ) {
								$thumbnail->set( 'internal', true );
							} else {
								$thumbnail->set( 'internal', false );
							}
						}

						if ( $thumbnails ) {
							$link->set( 'thumbnails', $thumbnails->asArray() );
						} else {
							$link->set( 'thumbnails', false );
						}
					} else {
						$link->set( 'thumbnails', false );
					}
				} else {
					$link->set( 'thumbnails', false );
				}
			} else {
				$link->set( 'internal', true );
				$link->set( 'thumbnails', false );
			}

			$links->set( $i, $link->asArray() );
		}

		return $links;
	}

	/**
	 * Returns internal clean up urls
	 *
	 * @return string
	 */
	static public function loadCleanUpURL()
	{
		global $_CB_framework;

		return '<a href="' . $_CB_framework->pluginClassUrl( 'cbactivity', true, array( 'action' => 'cleanup', 'token' => md5( $_CB_framework->getCfg( 'secret' ) ) ), 'raw', 0, true ) . '" target="_blank">' . CBTxt::T( 'Click to Process' ) . '</a>';
	}

	/**
	 * Returns an options array of available actions
	 *
	 * @param bool              $raw
	 * @param Activity|Comments $stream
	 * @param int               $selected
	 * @return array|ActionTable[]
	 */
	static public function loadActionOptions( $raw = false, $stream = null, $selected = 0 )
	{
		global $_CB_database;

		if ( Application::Cms()->getClientId() ) {
			$raw								=	false;
			$stream								=	null;
			$selected							=	0;
		}

		if ( $stream ) {
			$streamId							=	$stream->id();
		} else {
			$streamId							=	0;
		}

		static $cache							=	array();
		static $actions							=	null;

		if ( $actions === null ) {
			$query								=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_actions' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$actions							=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\ActionTable', array( $_CB_database ) );
		}

		if ( ! isset( $cache[$streamId][$selected][$raw] ) ) {
			$include							=	array();
			$exclude							=	array();

			if ( $stream ) {
				$streamInclude					=	$stream->get( 'actions_include', 0, GetterInterface::STRING );

				if ( $streamInclude ) {
					if ( strpos( $streamInclude, '|*|' ) !== false ) {
						$include				=	cbToArrayOfInt( explode( '|*|', $streamInclude ) );
					} else {
						$include				=	cbToArrayOfInt( explode( ',', $streamInclude ) );
					}
				}

				$streamExclude					=	$stream->get( 'actions_exclude', 0, GetterInterface::STRING );

				if ( $streamExclude ) {
					if ( strpos( $streamExclude, '|*|' ) !== false ) {
						$exclude				=	cbToArrayOfInt( explode( '|*|', $streamExclude ) );
					} else {
						$exclude				=	cbToArrayOfInt( explode( ',', $streamExclude ) );
					}
				}
			}

			$options							=	array();

			if ( ( ! $raw ) && ( ! Application::Cms()->getClientId() ) ) {
				$options[]						=	\moscomprofilerHTML::makeOption( 0, '&nbsp;', 'value', 'text', null, null, 'data-cbactivity-option-icon="' . htmlspecialchars( '<span class="fa fa-times"></span>' ) . '"' );
			}

			/** @var ActionTable[] $actions */
			foreach ( $actions as $id => $action ) {
				if ( ( ( $exclude && in_array( $id, $exclude ) ) || ( $include && ( ! in_array( $id, $include ) ) ) ) && ( $id != $selected ) ) {
					continue;
				}

				if ( $raw ) {
					$options[$id]				=	$action;
				} else {
					$options[]					=	\moscomprofilerHTML::makeOption( $id, CBTxt::T( $action->get( 'value', null, GetterInterface::STRING ) ), 'value', 'text', null, null, ( $action->icon() ? ' data-cbactivity-option-icon="' . htmlspecialchars( $action->icon() ) . '"' : null ) . ( $action->get( 'description', null, GetterInterface::STRING ) ? ' data-cbactivity-toggle-placeholder="' . htmlspecialchars( $action->get( 'description', null, GetterInterface::STRING ) ) . '"' : null ) );
				}
			}

			$cache[$streamId][$selected][$raw]	=	$options;
		}

		return $cache[$streamId][$selected][$raw];
	}

	/**
	 * Returns an options array of available locations
	 *
	 * @param bool              $raw
	 * @param Activity|Comments $stream
	 * @param int               $selected
	 * @return array|ActionTable[]
	 */
	static public function loadLocationOptions( $raw = false, $stream = null, $selected = 0 )
	{
		global $_CB_database;

		if ( Application::Cms()->getClientId() ) {
			$raw								=	false;
			$stream								=	null;
			$selected							=	0;
		}

		if ( $stream ) {
			$streamId							=	$stream->id();
		} else {
			$streamId							=	0;
		}

		static $cache							=	array();
		static $locations						=	null;

		if ( $locations === null ) {
			$query								=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_locations' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$locations							=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\LocationTable', array( $_CB_database ) );
		}

		if ( ! isset( $cache[$streamId][$selected][$raw] ) ) {
			$include							=	array();
			$exclude							=	array();

			if ( $stream ) {
				$streamInclude					=	$stream->get( 'locations_include', 0, GetterInterface::STRING );

				if ( $streamInclude ) {
					if ( strpos( $streamInclude, '|*|' ) !== false ) {
						$include				=	cbToArrayOfInt( explode( '|*|', $streamInclude ) );
					} else {
						$include				=	cbToArrayOfInt( explode( ',', $streamInclude ) );
					}
				}

				$streamExclude					=	$stream->get( 'locations_exclude', 0, GetterInterface::STRING );

				if ( $streamExclude ) {
					if ( strpos( $streamExclude, '|*|' ) !== false ) {
						$exclude				=	cbToArrayOfInt( explode( '|*|', $streamExclude ) );
					} else {
						$exclude				=	cbToArrayOfInt( explode( ',', $streamExclude ) );
					}
				}
			}

			$options							=	array();

			if ( ( ! $raw ) && ( ! Application::Cms()->getClientId() ) ) {
				$options[]						=	\moscomprofilerHTML::makeOption( 0, '&nbsp;', 'value', 'text', null, null, 'data-cbactivity-option-icon="' . htmlspecialchars( '<span class="fa fa-times"></span>' ) . '"' );
			}

			/** @var LocationTable[] $locations */
			foreach ( $locations as $id => $location ) {
				if ( ( ( $exclude && in_array( $id, $exclude ) ) || ( $include && ( ! in_array( $id, $include ) ) ) ) && ( $id != $selected ) ) {
					continue;
				}

				if ( $raw ) {
					$options[$id]				=	$location;
				} else {
					$options[]					=	\moscomprofilerHTML::makeOption( $id, CBTxt::T( $location->get( 'value', null, GetterInterface::STRING ) ) );
				}
			}

			$cache[$streamId][$selected][$raw]	=	$options;
		}

		return $cache[$streamId][$selected][$raw];
	}

	/**
	 * Returns an options array of available user tags
	 *
	 * @param Tags           $stream
	 * @param null|UserTable $user
	 * @return array
	 */
	static public function loadTagOptions( $stream = null, $user = null )
	{
		$streamId								=	0;

		if ( $stream ) {
			if ( $stream->get( 'query', true, GetterInterface::BOOLEAN ) ) {
				$streamId						=	$stream->id();
			}

			if ( ! $user ) {
				$user							=	$stream->user();
			}
		}

		if ( ! $user ) {
			$user								=	\CBuser::getMyUserDataInstance();
		} elseif ( is_int( $user ) ) {
			$user								=	\CBuser::getInstance( $user, false )->getUserData();
		}

		$userId									=	$user->get( 'id', 0, GetterInterface::INT );

		static $options							=	array();
		static $connections						=	array();
		static $custom							=	array();

		if ( ! isset( $options[$userId][$streamId] ) ) {
			if ( ! isset( $connections[$userId] ) ) {
				$connectionOptions				=	array();

				foreach( self::getConnections( $userId, true ) as $connection ) {
					$connectionOptions[]		=	\moscomprofilerHTML::makeOption( (string) $connection->id, getNameFormat( $connection->name, $connection->username, Application::Config()->get( 'name_format', 3, GetterInterface::INT ) ) );
				}

				$connections[$userId]			=	$connectionOptions;
			}

			if ( ! isset( $custom[$streamId] ) ) {
				$streamOptions					=	array();

				if ( $streamId ) {
					$exclude					=	array();

					foreach ( $connections[$userId] as $connection ) {
						$exclude[]				=	$connection->value;
					}

					foreach ( $stream->reset()->rows( 'all' ) as $tag ) {
						$existingTag			=	$tag->get( 'tag', null, GetterInterface::STRING );

						if ( in_array( $existingTag, $exclude ) ) {
							continue;
						} elseif ( is_numeric( $existingTag ) && ( ! in_array( (int) $existingTag, self::getConnections( $userId ) ) ) ) {
							continue;
						}

						$streamOptions[]		=	\moscomprofilerHTML::makeOption( $existingTag, $existingTag );
					}
				}

				$custom[$streamId]				=	$streamOptions;
			}

			$options[$userId][$streamId]		=	array_merge( $connections[$userId], $custom[$streamId] );
		}

		return $options[$userId][$streamId];
	}

	/**
	 * Returns an options array of available emotes
	 *
	 * @param bool $substitutions
	 * @param bool $raw
	 * @return array|EmoteTable[]
	 */
	static public function loadEmoteOptions( $substitutions = false, $raw = false )
	{
		global $_CB_database;

		if ( Application::Cms()->getClientId() ) {
			$substitutions			=	false;
			$raw					=	false;
		}

		static $cache				=	array();
		static $emotes				=	null;

		if ( $emotes === null ) {
			$query					=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_emotes' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$emotes					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\EmoteTable', array( $_CB_database ) );
		}

		if ( $raw ) {
			return $emotes;
		}

		if ( ! isset( $cache[$substitutions] ) ) {
			$options				=	array();

			if ( ( $substitutions !== true ) && ( ! Application::Cms()->getClientId() ) ) {
				$options[]			=	\moscomprofilerHTML::makeOption( 0, '&nbsp;', 'value', 'text', null, null, 'data-cbactivity-option-icon="' . htmlspecialchars( '<span class="fa fa-smile-o fa-lg"></span>' ) . '"' );
			}

			/** @var EmoteTable[] $emotes */
			foreach ( $emotes as $id => $emote ) {
				if ( $substitutions === true ) {
					$key			=	':' . $emote->get( 'value', null, GetterInterface::STRING ) . ':';

					$options[$key]	=	$emote->icon();
				} else {
					$options[]		=	\moscomprofilerHTML::makeOption( $id, '&nbsp;', 'value', 'text', null, null, ' data-cbactivity-option-icon="' . htmlspecialchars( $emote->icon() ) . '"' );
				}
			}

			$cache[$substitutions]	=	$options;
		}

		return $cache[$substitutions];
	}

	/**
	 * Returns an options array of available like types
	 *
	 * @param bool  $raw
	 * @param Likes $stream
	 * @param int   $selected
	 * @return array|LikeTypeTable[]
	 */
	static public function loadLikeOptions( $raw = false, $stream = null, $selected = 0 )
	{
		global $_CB_database;

		if ( Application::Cms()->getClientId() ) {
			$raw								=	false;
			$stream								=	null;
			$selected							=	0;
		}

		if ( $stream ) {
			$streamId							=	$stream->id();
		} else {
			$streamId							=	0;
		}

		static $cache							=	array();
		static $types							=	null;

		if ( $types === null ) {
			$query								=	'SELECT *'
												.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_activity_like_types' )
												.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
												.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' );
			$_CB_database->setQuery( $query );
			$types								=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Activity\Table\LikeTypeTable', array( $_CB_database ) );
		}

		if ( ! isset( $cache[$streamId][$selected][$raw] ) ) {
			$include							=	array();
			$exclude							=	array();

			if ( $stream ) {
				$streamInclude					=	$stream->get( 'include', 0, GetterInterface::STRING );

				if ( $streamInclude ) {
					if ( strpos( $streamInclude, '|*|' ) !== false ) {
						$include				=	cbToArrayOfInt( explode( '|*|', $streamInclude ) );
					} else {
						$include				=	cbToArrayOfInt( explode( ',', $streamInclude ) );
					}
				}

				$streamExclude					=	$stream->get( 'exclude', 0, GetterInterface::STRING );

				if ( $streamExclude ) {
					if ( strpos( $streamExclude, '|*|' ) !== false ) {
						$exclude				=	cbToArrayOfInt( explode( '|*|', $streamExclude ) );
					} else {
						$exclude				=	cbToArrayOfInt( explode( ',', $streamExclude ) );
					}
				}
			}

			$options							=	array();

			/** @var LikeTypeTable[] $types */
			foreach ( $types as $id => $type ) {
				if ( ( ( $exclude && in_array( $id, $exclude ) ) || ( $include && ( ! in_array( $id, $include ) ) ) ) && ( $id != $selected ) ) {
					continue;
				}

				if ( $raw ) {
					$options[$id]				=	$type;
				} else {
					$options[]					=	\moscomprofilerHTML::makeOption( $id, CBTxt::T( $type->get( 'value', null, GetterInterface::STRING ) ), 'value', 'text', null, null, ' data-cbactivity-option-icon="' . htmlspecialchars( $type->icon() ) . '"' );
				}
			}

			$cache[$streamId][$selected][$raw]	=	$options;
		}

		return $cache[$streamId][$selected][$raw];
	}
}
