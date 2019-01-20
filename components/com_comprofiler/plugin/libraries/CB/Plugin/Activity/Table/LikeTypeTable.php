<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Activity\Table;

use CBLib\Database\Table\OrderedTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Application\Application;
use CB\Plugin\Activity\CBActivity;

defined('CBLIB') or die();

// Stored Language Strings:
// CBTxt::T( 'Like' )
// CBTxt::T( 'Love' )
// CBTxt::T( 'Funny' )
// CBTxt::T( 'Sad' )
// CBTxt::T( 'Angry' )

class LikeTypeTable extends OrderedTable
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $value			=	null;
	/** @var string  */
	public $icon			=	null;
	/** @var string  */
	public $class			=	null;
	/** @var int  */
	public $published		=	null;
	/** @var int  */
	public $ordering		=	null;
	/** @var string  */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_activity_like_types';

	/**
	 * Primary key(s) of table
	 *
	 * @var string
	 */
	protected $_tbl_key		=	'id';

	/**
	 * Ordering keys and for each their ordering groups.
	 * E.g.; array( 'ordering' => array( 'tab' ), 'ordering_registration' => array() )
	 * @var array
	 */
	protected $_orderings	=	array( 'ordering' => array() );

	/**
	 * @return bool
	 */
	public function check()
	{
		if ( $this->get( 'value', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

			return false;
		} elseif ( ( $this->get( 'icon', null, GetterInterface::STRING ) == '' ) && ( $this->get( 'class', null, GetterInterface::STRING ) == '' ) ) {
			$this->setError( CBTxt::T( 'Icon not specified!' ) );

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

			$_PLUGINS->trigger( 'activity_onBeforeUpdateLikeType', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateLikeType', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateLikeType', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateLikeType', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteLikeType', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteLikeType', array( $this ) );

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
	 * @return string
	 */
	public function icon()
	{
		global $_PLUGINS;

		if ( Application::Cms()->getClientId() ) {
			CBActivity::getTemplate( 'twemoji', false, true, false );
		}

		$icon					=	null;
		$likeClass				=	'streamIconLike' . ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $this->get( 'value', null, GetterInterface::STRING ) ) ) );

		if ( $this->get( 'icon', null, GetterInterface::STRING ) ) {
			static $plugin		=	null;

			if ( $plugin === null ) {
				$plugin			=	$_PLUGINS->getLoadedPlugin( 'user', 'cbactivity' );
			}

			if ( ! $plugin ) {
				return null;
			}

			$params				=	CBActivity::getGlobalParams();

			$icon				=	'<img src="' . $_PLUGINS->getPluginLivePath( $plugin ) . '/templates/' . htmlspecialchars( $params->get( 'general_template', 'default', GetterInterface::STRING ) ) . '/images/' . htmlspecialchars( $this->get( 'icon', null, GetterInterface::STRING ) ) . '" alt="' . htmlspecialchars( $this->get( 'value', null, GetterInterface::STRING ) ) . '" class="streamIconLike ' . htmlspecialchars( $likeClass ) . ' img-responsive-inline" />';
		} elseif ( $this->get( 'class' ) ) {
			$icon				=	'<span class="streamIconLike ' . htmlspecialchars( $likeClass ) . ' ' . htmlspecialchars( $this->get( 'class', null, GetterInterface::STRING ) ) . '"></span>';
		}

		return $icon;
	}
}