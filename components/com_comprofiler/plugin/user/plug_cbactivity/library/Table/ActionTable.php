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
// CBTxt::T( 'Feeling' )
// CBTxt::T( 'How are you feeling?' )
// CBTxt::T( 'Watching' )
// CBTxt::T( 'What are you watching?' )
// CBTxt::T( 'Reading' )
// CBTxt::T( 'What are you reading?' )
// CBTxt::T( 'Listening To' )
// CBTxt::T( 'What are you listening to?' )
// CBTxt::T( 'Drinking' )
// CBTxt::T( 'What are you drinking?' )
// CBTxt::T( 'Eating' )
// CBTxt::T( 'What are you eating?' )
// CBTxt::T( 'Playing' )
// CBTxt::T( 'What are you playing?' )
// CBTxt::T( 'Traveling To' )
// CBTxt::T( 'Where are you going?' )
// CBTxt::T( 'Looking For' )
// CBTxt::T( 'What are you looking for?' )
// CBTxt::T( 'Celebrating' )
// CBTxt::T( 'What are you celebrating?' )

class ActionTable extends OrderedTable
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $value			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $description		=	null;
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
	protected $_tbl			=	'#__comprofiler_plugin_activity_actions';

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
			$this->setError( CBTxt::T( 'Action not specified!' ) );

			return false;
		} else {
			$row	=	new ActionTable();

			$row->load( array( 'value' => $this->get( 'value', null, GetterInterface::STRING ) ) );

			if ( $row->get( 'id', 0, GetterInterface::INT ) && ( $this->get( 'id', 0, GetterInterface::INT ) != $row->get( 'id', 0, GetterInterface::INT ) ) ) {
				$this->setError( CBTxt::T( 'Action already exists!' ) );

				return false;
			}
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

			$_PLUGINS->trigger( 'activity_onBeforeUpdateAction', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateAction', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateAction', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateAction', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteAction', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteAction', array( $this ) );

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
		global $_CB_framework;

		if ( Application::Cms()->getClientId() ) {
			CBActivity::getTemplate( 'twemoji', false, true, false );
		}

		$icon			=	null;
		$actionClass	=	'streamIconAction' . ucfirst( strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', $this->get( 'value', null, GetterInterface::STRING ) ) ) );

		if ( $this->get( 'icon', null, GetterInterface::STRING ) ) {
			$icon		=	'<img src="' . $_CB_framework->getCfg( 'live_site' ) . '/images/' . htmlspecialchars( $this->get( 'icon', null, GetterInterface::STRING ) ) . '" alt="' . htmlspecialchars( $this->get( 'value', null, GetterInterface::STRING ) ) . '" class="streamIconAction ' . htmlspecialchars( $actionClass ) . ' img-responsive-inline" />';
		} elseif ( $this->get( 'class', null, GetterInterface::STRING ) ) {
			$icon		=	'<span class="streamIconAction ' . htmlspecialchars( $actionClass ) . ' ' . htmlspecialchars( $this->get( 'class', null, GetterInterface::STRING ) ) . '"></span>';
		}

		return $icon;
	}
}