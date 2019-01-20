<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Table;

use CBLib\Application\Application;
use CBLib\Database\Table\OrderedTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\Registry;
use CBLib\Registry\GetterInterface;
use CB\Plugin\AutoActions\Action\Action;
use CB\Plugin\AutoActions\Action\ActionInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\AutoActions\CBAutoActions;
use CBLib\Database\Table\TableInterface;

defined('CBLIB') or die();

class AutoActionTable extends OrderedTable
{
	/** @var int */
	public $id				=	null;
	/** @var string */
	public $system			=	null;
	/** @var string */
	public $title			=	null;
	/** @var string */
	public $description		=	null;
	/** @var string */
	public $type			=	null;
	/** @var string */
	public $trigger			=	null;
	/** @var int */
	public $object			=	null;
	/** @var int */
	public $variable		=	null;
	/** @var string */
	public $access			=	null;
	/** @var string */
	public $conditions		=	null;
	/** @var int */
	public $published		=	null;
	/** @var int */
	public $ordering		=	null;
	/** @var string */
	public $params			=	null;

	/** @var Registry  */
	protected $_params		=	null;
	/** @var Registry  */
	protected $_conditions	=	null;

	/**
	 * Table name in database
	 *
	 * @var string
	 */
	protected $_tbl			=	'#__comprofiler_plugin_autoactions';

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
		if ( $this->get( 'type', null, GetterInterface::STRING ) == '' ) {
			$this->setError( CBTxt::T( 'Type not specified!' ) );

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
		if ( ! $this->get( 'system', null, GetterInterface::STRING ) ) {
			$this->set( 'system', '' );
		}

		return parent::store( $updateNulls );
	}

	/**
	 * Generic check for whether dependencies exist for this object in the db schema
	 * Should be overridden if checks need to be done before delete()
	 *
	 * @param  int  $oid  key index (only int supported here)
	 * @return boolean
	 */
	public function canDelete( $oid = null )
	{
		if ( $this->get( 'system', null, GetterInterface::STRING ) ) {
			$this->setError( CBTxt::T( 'System actions can not be deleted' ) );

			return false;
		}

		return true;
	}

	/**
	 * Copies this record (no checks)
	 * canCopy should be called first to check if a copy is possible.
	 *
	 * @param  null|TableInterface|self  $object  The object being copied otherwise create new object and add $this
	 * @return self|boolean                       OBJECT: The new object copied successfully, FALSE: Failed to copy
	 */
	public function copy( $object = null )
	{
		if ( $object === null ) {
			$object		=	clone $this;
		}

		if ( $object->get( 'system', null, GetterInterface::STRING ) ) {
			$object->set( 'system', '' );
		}

		return parent::copy( $object );
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
	 * @return Registry
	 */
	public function conditions()
	{
		if ( ! ( $this->get( '_conditions', null, GetterInterface::RAW ) instanceof Registry ) ) {
			$this->set( '_conditions', new Registry( $this->get( 'conditions', null, GetterInterface::RAW ) ) );
		}

		return $this->get( '_conditions' );
	}

	/**
	 * Returns an action with auto action specific information
	 *
	 * @param UserTable $user
	 * @param array     $variables
	 * @param array     $substitutions
	 * @return ActionInterface
	 */
	public function action( $user = null, $variables = array(), $substitutions = array() )
	{
		if ( ! $user ) {
			$user				=	\CBuser::getMyInstance()->getUserData();
		}

		$class					=	'\CB\Plugin\AutoActions\Action\\' . ucfirst( $this->get( 'type', null, GetterInterface::STRING ) ) . 'Action';

		if ( ! class_exists( $class ) ) {
			$action				=	new Action( $this );
		} else {
			$action				=	new $class( $this );
		}

		if ( isset( $variables['self'] ) ) {
			unset( $variables['self'] );
		}

		if ( isset( $variables['user'] ) ) {
			unset( $variables['user'] );
		}

		$substitutions			=	array_merge( $substitutions, CBAutoActions::getExtras( $variables ) );

		$variables['self']		=	$this;
		$variables['user']		=	$user;

		$action->substitutions( $substitutions );
		$action->variables( $variables );

		return $action;
	}

	/**
	 * Triggers the action checking access and conditions then returning its results
	 *
	 * @param UserTable $user
	 * @param array     $variables
	 * @param array     $substitutions
	 * @return mixed
	 */
	public function trigger( $user = null, $variables = array(), $substitutions = array() )
	{
		if ( ! $user ) {
			$user				=	\CBuser::getMyInstance()->getUserData();
		}

		$action					=	$this->action( $user, $variables, $substitutions );
		$return					=	null;

		try {
			$return				=	$action->trigger( $user );

			if ( $action->error() ) {
				$this->setError( implode( "\n", $action->error() ) );
			}
		} catch ( \Exception $e ) {
			$this->setError( $e->getMessage() );
		}

		return $return;
	}

	/**
	 * Executes the action directly and returns its results (this skips access and condition checks)
	 *
	 * @param UserTable   $user
	 * @return mixed
	 */
	public function execute( $user = null )
	{
		if ( ! $user ) {
			$user		=	\CBuser::getMyInstance()->getUserData();
		}

		$action			=	$this->action( $user );
		$return			=	null;

		try {
			$return		=	$action->execute( $user );

			if ( $action->error() ) {
				$this->setError( implode( "\n", $action->error() ) );
			}
		} catch ( \Exception $e ) {
			$this->setError( $e->getMessage() );
		}

		return $return;
	}

	/**
	 * Runs the action with user parsing, variables, conditions, and access checks
	 *
	 * @param array $variables
	 * @return mixed|null|string
	 */
	public function run( &$variables = array() )
	{
		global $_CB_database, $_CB_framework;

		static $connections					=	array();

		$userIds							=	array();
		$users								=	array();
		$loop								=	array();

		if ( $this->get( 'object', 0, GetterInterface::INT ) == 3 ) {
			$objectUsers					=	cbToArrayOfInt( explode( ',', $this->get( 'variable', null, GetterInterface::STRING ) ) );

			if ( $objectUsers ) {
				$userIds					=	array_merge( $userIds, $objectUsers );
			}
		} elseif ( $this->get( 'object', 0, GetterInterface::INT ) == 2 ) {
			$users[]						=	\CBuser::getMyUserDataInstance();
		} elseif ( $this->get( 'object', 0, GetterInterface::INT ) == 1 ) {
			$variable						=	'var' . $this->get( 'variable', 0, GetterInterface::INT );

			if ( isset( $variables[$variable] ) ) {
				$variable					=	$variables[$variable];

				if ( is_array( $variable ) || ( $variable instanceof \Traversable ) ) {
					foreach ( $variable as $varUser ) {
						$user				=	CBAutoActions::prepareUser( $varUser, array( 'id' ) );

						if ( ! $user instanceof UserTable ) {
							$userIds[]		=	$user;
						} elseif ( $user->get( 'id', 0, GetterInterface::INT ) ) {
							$users[]		=	$user;
						}
					}
				} else {
					$user					=	CBAutoActions::prepareUser( $variable );

					if ( ! $user instanceof UserTable ) {
						$userIds[]			=	$user;
					} else {
						if ( $user->get( 'id', 0, GetterInterface::INT ) && $this->params()->get( 'reload', false, GetterInterface::BOOLEAN ) ) {
							$user->load( $user->get( 'id', 0, GetterInterface::INT ) );
						}

						$users[]			=	$user;
					}
				}
			}
		} elseif ( $this->get( 'object', 0, GetterInterface::INT ) == 4 ) {
			$user							=	CBAutoActions::getUser( $variables );
			$code							=	$this->action( $user, $variables )->string( $user, $this->params()->get( 'object_custom', null, GetterInterface::RAW ), false );

			if ( $code ) {
				$variable					=	CBAutoActions::outputCode( $code, $this, $user, $variables );

				if ( is_array( $variable ) || ( $variable instanceof \Traversable ) ) {
					foreach ( $variable as $varUser ) {
						$user				=	CBAutoActions::prepareUser( $varUser );

						if ( ! $user instanceof UserTable ) {
							$userIds[]		=	$user;
						} elseif ( $user->get( 'id', 0, GetterInterface::INT ) ) {
							$users[]		=	$user;
						}
					}
				} else {
					$user					=	CBAutoActions::prepareUser( $variable );

					if ( ! $user instanceof UserTable ) {
						$userIds[]			=	$user;
					} else {
						if ( $user->get( 'id', 0, GetterInterface::INT ) && $this->params()->get( 'reload', false, GetterInterface::BOOLEAN ) ) {
							$user->load( $user->get( 'id', 0, GetterInterface::INT ) );
						}

						$users[]			=	$user;
					}
				}
			}
		} elseif ( $this->get( 'object', 0, GetterInterface::INT ) == 6 ) {
			$moderatorViewAccessLevels		=	Application::CmsPermissions()->getGroupsOfViewAccessLevel( Application::Config()->get( 'moderator_viewaccesslevel', 3, GetterInterface::INT ), true );

			if ( $moderatorViewAccessLevels ) {
				static $moderators			=	null;

				if ( $moderators == null ) {
					$query					=	'SELECT DISTINCT u.' . $_CB_database->NameQuote( 'id' )
											.	"\n FROM " . $_CB_database->NameQuote( '#__users' ) . " AS u"
											.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
											.	' ON c.' . $_CB_database->NameQuote( 'id' ) . ' = u.' . $_CB_database->NameQuote( 'id' )
											.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__user_usergroup_map' ) . " AS g"
											.	' ON g.' . $_CB_database->NameQuote( 'user_id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
											.	"\n WHERE g." . $_CB_database->NameQuote( 'group_id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $moderatorViewAccessLevels )
											.	"\n AND u." . $_CB_database->NameQuote( 'block' ) . " = 0"
											.	"\n AND c." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
											.	"\n AND c." . $_CB_database->NameQuote( 'approved' ) . " = 1";

					$_CB_database->setQuery( $query );
					$moderators				=	$_CB_database->loadResultArray();
				}

				if ( $moderators ) {
					$userIds				=	array_merge( $userIds, $moderators );
				}
			}
		} else {
			$user							=	CBAutoActions::getUser( $variables );

			if ( $this->get( 'object', 0, GetterInterface::INT ) == 5 ) {
				$userId						=	$user->get( 'id', 0, GetterInterface::INT );

				if ( ! isset( $connections[$userId] ) ) {
					$cbConnection			=	new \cbConnection( $userId );

					$connections[$userId]	=	$cbConnection->getActiveConnections( $userId );
				}

				foreach ( $connections[$userId] as $connection ) {
					$userIds[]				=	$connection->id;
				}
			} else {
				if ( $user->get( 'id', 0, GetterInterface::INT ) && $this->params()->get( 'reload', false, GetterInterface::BOOLEAN ) ) {
					$user->load( $user->get( 'id', 0, GetterInterface::INT ) );
				}

				$users[]					=	$user;
			}
		}

		if ( $this->params()->get( 'loop', 0, GetterInterface::INT ) ) {
			$loopVariable					=	'var' . $this->params()->get( 'loop', 0, GetterInterface::INT );

			if ( isset( $variables[$loopVariable] ) ) {
				$loopVariable				=	$variables[$loopVariable];

				if ( is_array( $loopVariable ) || ( $loopVariable instanceof \Traversable ) ) {
					$loop					=	$loopVariable;
				}
			}
		}

		if ( $userIds ) {
			$userIds						=	array_unique( $userIds );

			\CBuser::advanceNoticeOfUsersNeeded( $userIds );

			foreach ( $userIds as $userId ) {
				$user						=	\CBuser::getUserDataInstance( $userId );

				if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
					$users[]				=	$user;
				}
			}
		}

		$return								=	null;

		foreach ( $users as &$user ) {
			if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
				$password					=	Application::Input()->get( 'post/passwd', null, GetterInterface::STRING );

				if ( ! $password ) {
					$password				=	Application::Input()->get( 'post/password', null, GetterInterface::STRING );
				}
			} else {
				$password					=	null;
			}

			if ( $loop ) {
				foreach ( $loop as $loopKey => &$loopVar ) {
					$variables['loop_key']	=	$loopKey;
					$variables['loop']		=	&$loopVar;

					$content				=	$this->trigger( $user, $variables, array( 'password' => $password ) );

					if ( ( $return !== '' ) && ( $return !== null ) ) {
						$return				.=	$content;
					} else {
						$return				=	$content;
					}
				}
			} else {
				$content					=	$this->trigger( $user, $variables, array( 'password' => $password ) );

				if ( ( $return !== '' ) && ( $return !== null ) ) {
					$return					.=	$content;
				} else {
					$return					=	$content;
				}
			}

			if ( $this->getError() && $this->params()->get( 'debug', false, GetterInterface::BOOLEAN ) ) {
				$_CB_framework->enqueueMessage( $this->getError(), 'error' );
			}
		}

		return $return;
	}

	/**
	 * Checks if the action associated with this auto action is even installed
	 *
	 * @return bool
	 */
	public function installed()
	{
		return $this->action()->installed();
	}
}