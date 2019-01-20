<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CB\Plugin\Activity\CBActivity;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\Activity\Activity;
use CB\Plugin\Activity\Comments;
use CB\Plugin\Activity\Tags;
use CB\Plugin\Activity\Likes;

defined('CBLIB') or die();

class ActivityTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $asset			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $message			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $pinned			=	null;
	/** @var string  */
	public $date			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( ! $this->get( 'user_id', 0, GetterInterface::INT ) ) {
			$this->setError( CBTxt::T( 'Owner not specified!' ) );

			return false;
		}

		return true;
	}

	/**
	 * @param bool $updateNulls
	 * @return bool
	 */
	public function store( $updateNulls = false )
	{
		global $_PLUGINS;

		$new	=	( $this->get( 'id', 0, GetterInterface::INT ) ? false : true );
		$old	=	new self();

		if ( ! $this->get( 'asset', null, GetterInterface::STRING ) ) {
			$this->set( 'asset', 'profile.' . $this->get( 'user_id', 0, GetterInterface::INT ) );
		}

		$this->set( 'published', $this->get( 'published', 1, GetterInterface::INT ) );
		$this->set( 'date', $this->get( 'date', Application::Database()->getUtcDateTime(), GetterInterface::STRING ) );

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'activity_onBeforeUpdateActivity', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateActivity', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateActivity', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateActivity', array( $this ) );
		}

		return true;
	}

	/**
	 * @param null|int $id
	 * @return bool
	 */
	public function delete( $id = null )
	{
		global $_PLUGINS;

		$_PLUGINS->trigger( 'activity_onBeforeDeleteActivity', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		// Deletes activity about this activity:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity' )
						.	"\n WHERE ( " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) )
						.	" OR " . $this->getDbo()->NameQuote( 'asset' ) . " LIKE " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) . '.%' ) . " )";
		$this->getDbo()->setQuery( $query );
		$activities		=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\ActivityTable', array( $this->getDbo() ) );

		/** @var ActivityTable[] $activities */
		foreach ( $activities as $activity ) {
			$activity->delete();
		}

		// Deletes hidden entries for this activity:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_hidden' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'type' ) . " = " . $this->getDbo()->Quote( 'activity' )
						.	"\n AND " . $this->getDbo()->NameQuote( 'item' ) . " = " . $this->get( 'id', 0, GetterInterface::INT );
		$this->getDbo()->setQuery( $query );
		$hidden			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\HiddenTable', array( $this->getDbo() ) );

		/** @var HiddenTable[] $hidden */
		foreach ( $hidden as $hide ) {
			$hide->delete();
		}

		// Deletes activity specific notifications:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_notifications' )
						.	"\n WHERE ( " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) )
						.	" OR " . $this->getDbo()->NameQuote( 'asset' ) . " LIKE " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) . '.%' ) . " )";
		$this->getDbo()->setQuery( $query );
		$notifications	=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\NotificationTable', array( $this->getDbo() ) );

		/** @var NotificationTable[] $notifications */
		foreach ( $notifications as $notification ) {
			$notification->delete();
		}

		// Deletes activity specific comments:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_comments' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) );
		$this->getDbo()->setQuery( $query );
		$comments		=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\CommentTable', array( $this->getDbo() ) );

		/** @var CommentTable[] $comments */
		foreach ( $comments as $comment ) {
			$comment->delete();
		}

		// Deletes activity specific tags:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_tags' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) );
		$this->getDbo()->setQuery( $query );
		$tags			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\TagTable', array( $this->getDbo() ) );

		/** @var TagTable[] $tags */
		foreach ( $tags as $tag ) {
			$tag->delete();
		}

		// Deletes activity specific likes:
		$query			=	'SELECT *'
						.	"\n FROM " . $this->getDbo()->NameQuote( '#__comprofiler_plugin_activity_likes' )
						.	"\n WHERE " . $this->getDbo()->NameQuote( 'asset' ) . " = " . $this->getDbo()->Quote( 'activity.' . $this->get( 'id', 0, GetterInterface::INT ) );
		$this->getDbo()->setQuery( $query );
		$likes			=	$this->getDbo()->loadObjectList( null, '\CB\Plugin\Activity\Table\LikeTable', array( $this->getDbo() ) );

		/** @var LikeTable[] $likes */
		foreach ( $likes as $like ) {
			$like->delete();
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteActivity', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params' ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params' ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @return null|string
	 */
	public function action()
	{
		static $cache						=	array();

		$actions							=	CBActivity::loadActionOptions( true );
		$emotes								=	CBActivity::loadEmoteOptions( false, true );
		$id									=	$this->get( 'id', 0, GetterInterface::INT  );

		if ( ! isset( $cache[$id] ) ) {
			$action							=	$this->params()->subTree( 'action' );
			$actionId						=	$action->get( 'id', 0, GetterInterface::INT );
			$actionTitle					=	null;
			$actionMessage					=	null;
			$actionEmote					=	null;
			$return							=	null;

			if ( $actionId && isset( $actions[$actionId] ) ) {
				if ( $actions[$actionId]->get( 'published', 1, GetterInterface::INT  ) ) {
					$actionTitle			=	( $actions[$actionId]->get( 'title', null, GetterInterface::HTML ) != '' ? trim( CBTxt::T( $actions[$actionId]->get( 'title', null, GetterInterface::HTML ) ) ) : null );

					$message				=	$action->get( 'message', null, GetterInterface::STRING );

					if ( $message != '' ) {
						$actionMessage		=	'<span class="activityActionMessage">' . trim( htmlspecialchars( $message ) ) . '</span>';
					}

					$emoteId				=	$action->get( 'emote', 0, GetterInterface::INT );

					if ( $emoteId && isset( $emotes[$emoteId] ) && $emotes[$emoteId]->get( 'published', 1, GetterInterface::INT ) ) {
						$actionEmote		=	( $emotes[$emoteId]->icon() ? $emotes[$emoteId]->icon() : null );
					} else {
						$actionEmote		=	( $actions[$actionId]->icon() ? $actions[$actionId]->icon() : null );
					}
				}

				if ( $actionMessage ) {
					$return					=	CBTxt::T( 'ACTIVITY_ACTION', '[title] [message] [emote]', array( '[title]' => $actionTitle, '[message]' => $actionMessage, '[emote]' => $actionEmote ) );
				}
			}

			$cache[$id]						=	$return;
		}

		return $cache[$id];
	}

	/**
	 * @return null|string
	 */
	public function location()
	{
		static $cache							=	array();

		$locations								=	CBActivity::loadLocationOptions( true );
		$id										=	$this->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $cache[$id] ) ) {
			$location							=	$this->params()->subTree( 'location' );
			$locationId							=	$location->get( 'id', 0, GetterInterface::INT );
			$locationTitle						=	null;
			$locationAddress					=	null;
			$return								=	null;

			if ( $locationId && isset( $locations[$locationId] ) ) {
				if ( $locations[$locationId]->get( 'published', 1, GetterInterface::INT ) ) {
					$locationTitle				=	( $locations[$locationId]->get( 'title', null, GetterInterface::HTML ) != '' ? trim( CBTxt::T( $locations[$locationId]->get( 'title', null, GetterInterface::HTML ) ) ) : null );

					$place						=	$location->get( 'place', null, GetterInterface::STRING );

					if ( $place != '' ) {
						$address				=	$location->get( 'address', null, GetterInterface::STRING );
						$url					=	$locations[$locationId]->get( 'url', null, GetterInterface::STRING );
						$addressUrl				=	null;

						if ( ! $url ) {
							if ( $address != '' ) {
								$addressUrl		=	'https://www.google.com/maps/place/' . urlencode( $address );
							} else {
								$addressUrl		=	'https://www.google.com/maps/search/' . urlencode( $place );
							}
						} elseif ( $url != 'none' ) {
							$addressUrl			=	str_replace( array( '[place]', '[address]' ), array( urlencode( $place ), urlencode( ( $address != '' ? $address : $place ) ) ), $url );
						}

						if ( $addressUrl ) {
							$addressUrl			=	'<a href="' . htmlspecialchars( $addressUrl ) . '" target="_blank" rel="nofollow noopener">' . trim( htmlspecialchars( $place ) ) . '</a>';
						} else {
							$addressUrl			=	trim( htmlspecialchars( $place ) );
						}

						$locationAddress		=	'<span class="activityLocation">' . $addressUrl . '</span>';
					}
				}

				if ( $locationAddress ) {
					$return						=	CBTxt::T( 'ACTIVITY_LOCATION', '[title] [location]', array( '[title]' => $locationTitle, '[location]' => $locationAddress ) );
				}
			}

			$cache[$id]							=	$return;
		}

		return $cache[$id];
	}

	/**
	 * @return ParamsInterface
	 */
	public function attachments()
	{
		static $cache		=	array();

		$id					=	$this->get( 'id', 0, GetterInterface::INT );

		if ( ! isset( $cache[$id] ) ) {
			$cache[$id]		=	CBActivity::prepareAttachments( $this->params()->subTree( 'links' ) );
		}

		return $cache[$id];
	}

	/**
	 * @return UserTable|ActivityTable|CommentTable|TagTable|FollowTable|LikeTable|NotificationTable|null
	 */
	public function source()
	{
		global $_PLUGINS;

		static $cache		=	array();

		$id					=	$this->get( 'asset', null, GetterInterface::STRING );

		if ( ! isset( $cache[$id] ) ) {
			$source			=	CBActivity::getSource( $id );

			$_PLUGINS->trigger( 'activity_onActivitySource', array( $this, &$source ) );

			$cache[$id]		=	$source;
		}

		return $cache[$id];
	}

	/**
	 * @param Activity $stream
	 * @return Comments
	 */
	public function comments( $stream = null )
	{
		static $cache						=	array();

		$id									=	$this->get( 'id', 0, GetterInterface::INT );

		if ( $stream ) {
			$streamId						=	$stream->id();
		} else {
			$streamId						=	0;
		}

		if ( ! isset( $cache[$id][$streamId] ) ) {
			$commentsUser					=	CBActivity::findParamOverrde( $this, 'comments_user', 'activity' );

			if ( ! $commentsUser instanceof UserTable ) {
				if ( $commentsUser == '' ) {
					$commentsUser			=	'activity';
				}

				switch ( $commentsUser ) {
					case 'user':
						$tagsUser			=	null;
						break;
					case 'stream':
						if ( $stream ) {
							$commentsUser	=	$stream->user();
						} else {
							$commentsUser	=	$this->get( 'user_id', 0, GetterInterface::INT );
						}
						break;
					case 'asset':
					case 'activity':
						$commentsUser		=	$this->get( 'user_id', 0, GetterInterface::INT );
						break;
				}
			}

			$commentsStream					=	new Comments( CBActivity::findAssetOverride( 'comments', $this, $stream ), $commentsUser );

			if ( $stream ) {
				$commentsStream->set( 'direction', 'up' );

				$commentsStream->parse( $stream->asArray(), 'comments_' );

				if ( $this->get( 'asset', null, GetterInterface::STRING ) == 'global' ) {
					$commentsStream->set( 'create_connected', false );
					$commentsStream->set( 'replies_create_connected', false );
				}

				$commentsStream->set( 'moderators', $stream->get( 'moderators', array(), GetterInterface::RAW ) );
			}

			$commentsStream->set( 'inline', true );
			$commentsStream->set( 'auto_update', false );
			$commentsStream->set( 'auto_load', false );
			$commentsStream->set( 'replies_auto_update', false );
			$commentsStream->set( 'replies_auto_load', false );

			$commentsStream->set( 'activity', $this->get( 'id', 0, GetterInterface::INT ) );

			$cache[$id][$streamId]			=	$commentsStream;
		}

		return $cache[$id][$streamId];
	}

	/**
	 * @param Activity $stream
	 * @return Tags
	 */
	public function tags( $stream = null )
	{
		static $cache					=	array();

		$id								=	$this->get( 'id', 0, GetterInterface::INT );

		if ( $stream ) {
			$streamId					=	$stream->id();
		} else {
			$streamId					=	0;
		}

		if ( ! isset( $cache[$id][$streamId] ) ) {
			$tagsUser					=	CBActivity::findParamOverrde( $this, 'tags_user', 'activity' );

			if ( ! $tagsUser instanceof UserTable ) {
				if ( $tagsUser == '' ) {
					$tagsUser			=	'activity';
				}

				switch ( $tagsUser ) {
					case 'user':
						$tagsUser		=	null;
						break;
					case 'stream':
						if ( $stream ) {
							$tagsUser	=	$stream->user();
						} else {
							$tagsUser	=	$this->get( 'user_id', 0, GetterInterface::INT );
						}
						break;
					case 'asset':
					case 'activity':
						$tagsUser		=	$this->get( 'user_id', 0, GetterInterface::INT );
						break;
				}
			}

			$tagsStream					=	new Tags( CBActivity::findAssetOverride( 'tags', $this, $stream ), $tagsUser );

			if ( $stream ) {
				$tagsStream->parse( $stream->asArray(), 'tags_' );

				$tagsStream->set( 'moderators', $stream->get( 'moderators', array(), GetterInterface::RAW ) );
			}

			$tagsStream->set( 'inline', true );
			$tagsStream->set( 'placeholder', CBTxt::T( 'Who are you with?' ) );

			$tagsStream->set( 'activity', $this->get( 'id', 0, GetterInterface::INT ) );

			$cache[$id][$streamId]		=	$tagsStream;
		}

		return $cache[$id][$streamId];
	}

	/**
	 * @param Activity $stream
	 * @return Likes
	 */
	public function likes( $stream = null )
	{
		static $cache					=	array();

		$id								=	$this->get( 'id', 0, GetterInterface::INT );

		if ( $stream ) {
			$streamId					=	$stream->id();
		} else {
			$streamId					=	0;
		}

		if ( ! isset( $cache[$id][$streamId] ) ) {
			$likesUser					=	CBActivity::findParamOverrde( $this, 'likes_user', 'activity' );

			if ( ! $likesUser instanceof UserTable ) {
				if ( $likesUser == '' ) {
					$likesUser			=	'activity';
				}

				switch ( $likesUser ) {
					case 'user':
						$likesUser		=	null;
						break;
					case 'stream':
						if ( $stream ) {
							$likesUser	=	$stream->user();
						} else {
							$likesUser	=	$this->get( 'user_id', 0, GetterInterface::INT );
						}
						break;
					case 'asset':
					case 'activity':
						$likesUser		=	$this->get( 'user_id', 0, GetterInterface::INT );
						break;
				}
			}

			$likesStream				=	new Likes( CBActivity::findAssetOverride( 'likes', $this, $stream ), $likesUser );

			if ( $stream ) {
				$likesStream->parse( $stream->asArray(), 'likes_' );

				$likesStream->set( 'moderators', $stream->get( 'moderators', array(), GetterInterface::RAW ) );
			}

			$likesStream->set( 'inline', true );
			$likesStream->set( 'count', true );
			$likesStream->set( 'layout', 'extended' );

			$likesStream->set( 'activity', $this->get( 'id', 0, GetterInterface::INT ) );

			$cache[$id][$streamId]		=	$likesStream;
		}

		return $cache[$id][$streamId];
	}
}