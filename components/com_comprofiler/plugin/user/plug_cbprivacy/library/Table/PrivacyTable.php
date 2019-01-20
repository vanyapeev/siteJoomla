<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Privacy\Table;

use CB\Plugin\Privacy\CBPrivacy;
use CBLib\Application\Application;
use CBLib\Database\Table\Table;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;

defined('CBLIB') or die();

class PrivacyTable extends Table
{
	/** @var int  */
	public $id				=	null;
	/** @var int  */
	public $user_id			=	null;
	/** @var string  */
	public $asset			=	null;
	/** @var string  */
	public $rule			=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_privacy';

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
		} elseif ( $this->get( 'asset', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Asset not specified!' ) );

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

		if ( ! $new ) {
			$old->load( $this->get( 'id', 0, GetterInterface::INT ) );

			$_PLUGINS->trigger( 'privacy_onBeforeUpdatePrivacy', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'privacy_onBeforeCreatePrivacy', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'privacy_onAfterUpdatePrivacy', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'privacy_onAfterCreatePrivacy', array( $this ) );
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

		$_PLUGINS->trigger( 'privacy_onBeforeDeletePrivacy', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'privacy_onAfterDeletePrivacy', array( $this ) );

		return true;
	}

	/**
	 * @return Registry
	 */
	public function params()
	{
		if ( ! ( $this->get( '_params', null, GetterInterface::RAW ) instanceof Registry ) ) {
			$this->set( '_params', new Registry( $this->get( 'params', null, GetterInterface::RAW ) ) );
		}

		return $this->get( '_params' );
	}

	/**
	 * @param null|int|UserTable $userId
	 * @return bool
	 */
	public function authorized( $userId = null )
	{
		global $_PLUGINS;

		static $cache						=	array();

		if ( $userId === null ) {
			$userId							=	Application::MyUser()->getUserId();
		} elseif ( $userId instanceof UserTable ) {
			$userId							=	$userId->get( 'id', 0, GetterInterface::INT );
		}

		$id									=	$this->get( 'id', 0, GetterInterface::INT );
		$owner								=	$this->get( 'user_id', 0, GetterInterface::INT );
		$rule								=	$this->get( 'rule', null, GetterInterface::STRING );

		if ( ( ! $rule ) || ( ( $userId == $owner ) && ( $rule != 999 ) ) || Application::User( $userId )->isGlobalModerator() ) {
			return true;
		}

		if ( ( ! $id ) || ( ! isset( $cache[$id][$userId] ) ) ) {
			$access							=	false;

			if ( $rule == 99 ) {
				$access						=	false;
			} elseif ( $rule == 999 ) {
				if ( Application::User( $userId )->isGlobalModerator() ) {
					$access					=	true;
				}
			} elseif ( $rule == 1 ) {
				if ( $userId > 0 ) {
					$access					=	true;
				}
			} elseif ( $rule == 2 ) {
				if ( in_array( $userId, CBPrivacy::getConnections( $owner ) ) ) {
					$access					=	true;
				}
			} elseif ( substr( $rule, 0, 5 ) == 'CONN-' ) {
				$connectionType				=	str_replace( 'CONN-', '', $rule );

				foreach ( CBPrivacy::getConnections( $owner, true ) as $connection ) {
					if ( ( $connection->id != $userId ) || ( ! $connection->type ) ) {
						continue;
					}

					$connectionTypes		=	explode( '|*|', $connection->type );

					foreach ( $connectionTypes as $type ) {
						if ( htmlspecialchars( trim( $type ) ) == $connectionType ) {
							$access			=	true;
						}
					}
				}
			} elseif ( $rule == 3 ) {
				if ( CBPrivacy::getConnectionOfConnection( $owner, $userId ) ) {
					$access					=	true;
				}
			} elseif ( substr( $rule, 0, 7 ) == 'ACCESS-' ) {
				if ( in_array( str_replace( 'ACCESS-', '', $rule ), Application::User( $userId )->getAuthorisedViewLevels() ) ) {
					$access					=	true;
				}
			} elseif ( substr( $rule, 0, 6 ) == 'GROUP-' ) {
				if ( in_array( str_replace( 'GROUP-', '', $rule ), Application::User( $userId )->getAuthorisedGroups() ) ) {
					$access					=	true;
				}
			}

			$_PLUGINS->trigger( 'privacy_onAuthorized', array( &$access, $userId, $this ) );

			$cache[$id][$userId]			=	$access;
		}

		return $cache[$id][$userId];
	}
}