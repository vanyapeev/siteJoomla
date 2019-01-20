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

defined('CBLIB') or die();

// Stored Language Strings:
// CBTxt::T( 'At' )
// CBTxt::T( 'In' )
// CBTxt::T( 'Near' )
// CBTxt::T( 'Going To' )
// CBTxt::T( 'Leaving From' )

class LocationTable extends OrderedTable
{
	/** @var int  */
	public $id				=	null;
	/** @var string  */
	public $value			=	null;
	/** @var string  */
	public $title			=	null;
	/** @var string  */
	public $url				=	null;
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
	protected $_tbl			=	'#__comprofiler_plugin_activity_locations';

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
			$this->setError( CBTxt::T( 'Location not specified!' ) );

			return false;
		} else {
			$row	=	new LocationTable();

			$row->load( array( 'value' => $this->get( 'value', null, GetterInterface::STRING ) ) );

			if ( $row->get( 'id', 0, GetterInterface::INT ) && ( $this->get( 'id', 0, GetterInterface::INT ) != $row->get( 'id', 0, GetterInterface::INT ) ) ) {
				$this->setError( CBTxt::T( 'Location already exists!' ) );

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

			$_PLUGINS->trigger( 'activity_onBeforeUpdateLocation', array( &$this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onBeforeCreateLocation', array( &$this ) );
		}

		if ( ! parent::store( $updateNulls ) ) {
			return false;
		}

		if ( ! $new ) {
			$_PLUGINS->trigger( 'activity_onAfterUpdateLocation', array( $this, $old ) );
		} else {
			$_PLUGINS->trigger( 'activity_onAfterCreateLocation', array( $this ) );
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

		$_PLUGINS->trigger( 'activity_onBeforeDeleteLocation', array( &$this ) );

		if ( ! parent::delete( $id ) ) {
			return false;
		}

		$_PLUGINS->trigger( 'activity_onAfterDeleteLocation', array( $this ) );

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
}