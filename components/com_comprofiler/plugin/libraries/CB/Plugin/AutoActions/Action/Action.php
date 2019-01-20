<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Plugin\AutoActions\Table\AutoActionTable;
use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CBLib\Input\Get;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\AutoActions\CBAutoActions;

defined('CBLIB') or die();

class Action implements ActionInterface
{
	/** @var null|AutoActionTable  */
	private $autoaction		=	null;
	/** @var array  */
	private $substitutions	=	array();
	/** @var array  */
	private $variables		=	array();
	/** @var array  */
	private $errors			=	array();

	/**
	 * Constructor for action object
	 *
	 * @param null|AutoActionTable  $autoaction
	 */
	public function __construct( $autoaction = null )
	{
		if ( $autoaction !== null ) {
			$this->autoaction	=	$autoaction;
		}
	}

	/**
	 * Triggers the action checking access and conditions then returning its results
	 *
	 * @param UserTable $user
	 * @return mixed
	 */
	public function trigger( $user )
	{
		global $_PLUGINS;

		// Exclude:
		$excludeGlobal			=	explode( ',', CBAutoActions::getGlobalParams()->get( 'exclude', null, GetterInterface::STRING ) );
		$excludeTrigger			=	explode( ',', $this->autoaction()->params()->get( 'exclude', null, GetterInterface::STRING ) );
		$exclude				=	array_filter( array_merge( $excludeGlobal, $excludeTrigger ) );

		if ( $exclude ) {
			cbArrayToInts( $exclude );

			$exclude			=	array_unique( $exclude );

			if ( in_array( $user->get( 'id', 0, GetterInterface::INT ), $exclude ) ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_USER_EXCLUDED', ':: Action [action] :: User [user_id] excluded', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[user_id]' => $user->get( 'id', 0, GetterInterface::INT ) ) ) );
				return null;
			}
		}

		// Access:
		if ( $user->get( 'id', 0, GetterInterface::INT ) ) {
			$gids				=	Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->getAuthorisedGroups( false );

			array_unshift( $gids, -3 );

			if ( Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) {
				array_unshift( $gids, -5 );
			} else {
				array_unshift( $gids, -4 );
			}
		} else {
			$gids				=	$user->get( 'gids', array(), GetterInterface::RAW );

			array_unshift( $gids, -2 );
			array_unshift( $gids, -4 );
		}

		if ( $user->get( 'id', 0, GetterInterface::INT ) == Application::MyUser()->getUserId() ) {
			array_unshift( $gids, -7 );
		} else {
			array_unshift( $gids, -6 );
		}

		array_unshift( $gids, -1 );

		$access					=	explode( '|*|', $this->autoaction()->get( 'access', null, GetterInterface::STRING ) );

		if ( ! array_intersect( $access, $gids ) ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ACCESS_FAILED', ':: Action [action] :: Access check for user [user_id] failed: looking for [access] in [groups]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[user_id]' => $user->get( 'id', 0, GetterInterface::INT ), '[access]' => implode( ', ', $access ), '[groups]' => implode( ', ', $gids ) ) ) );
			return null;
		}

		// Conditions:
		foreach ( $this->autoaction()->conditions() as $i => $conditional ) {
			/** @var ParamsInterface $conditional */
			$condTranslate		=	$conditional->get( 'translate', false, GetterInterface::BOOLEAN );
			$condFormat			=	$conditional->get( 'format', false, GetterInterface::BOOLEAN );
			$condPrepare		=	$conditional->get( 'content_plugins', false, GetterInterface::BOOLEAN );

			$condField			=	$this->string( $user, $conditional->get( 'field', null, GetterInterface::HTML ), true, $condTranslate, $condFormat, $condPrepare, true );
			$condOperator		=	$conditional->get( 'operator', '0', GetterInterface::STRING );
			$condValue			=	$this->string( $user, $conditional->get( 'value', null, GetterInterface::HTML ), true, $condTranslate, $condFormat, $condPrepare, true );

			if ( ! CBAutoActions::getFieldMatch( $condField, $condOperator, $condValue, $this->variables() ) ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_CONDITIONAL_FAILED', ':: Action [action] :: Conditional [cond] failed for user [user_id]: [field] [operator] [value]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[cond]' => ( $i + 1 ), '[user_id]' => $user->get( 'id', 0, GetterInterface::INT ), '[field]' => $condField, '[operator]' => CBAutoActions::getOperatorTitle( $condOperator ), '[value]' => $condValue ) ) );
				return null;
			}
		}

		// Action:
		$passwordCache			=	$user->get( 'password', null, GetterInterface::STRING );

		$user->set( 'password', null );

		$content				=	$this->execute( $user );

		$user->set( 'password', $passwordCache );

		$autoaction				=	$this->autoaction();
		$variables				=	$this->variables();
		$substitutions			=	$this->substitutions();

		$_PLUGINS->trigger( 'autoactions_onAction', array( &$content, &$autoaction, &$user, &$variables, &$substitutions ) );

		// Layout
		$display				=	$this->autoaction()->params()->get( 'display', 'none', GetterInterface::STRING );
		$layout					=	$this->autoaction()->params()->get( 'display_layout', null, GetterInterface::RAW );

		if ( ( $display != 'none' ) && ( ( ( $content !== '' ) && ( $content !== null ) ) || $layout ) ) {
			if ( $layout ) {
				$translate		=	$this->autoaction()->params()->get( 'display_translate', false, GetterInterface::BOOLEAN );
				$substitute		=	$this->autoaction()->params()->get( 'display_substitutions', false, GetterInterface::BOOLEAN );
				$format			=	$this->autoaction()->params()->get( 'display_format', false, GetterInterface::BOOLEAN );
				$prepare		=	$this->autoaction()->params()->get( 'display_content_plugins', false, GetterInterface::BOOLEAN );

				$return			=	str_replace( '[content]', $content, $this->string( $user, $layout, false, $translate, $format, $prepare, $substitute ) );

				if ( $this->autoaction()->params()->get( 'display_method', 'html', GetterInterface::STRING ) == 'php' ) {
					$return		=	CBAutoActions::outputCode( $return, $autoaction, $user, $variables, $content );
				}
			} else {
				$return			=	$content;
			}

			switch ( $display ) {
				case 'silent':
					break;
				case 'echo':
					echo $return;
					break;
				case 'var_dump':
					var_dump( $return );
					break;
				case 'print':
					print $return;
					break;
				case 'exit':
					exit( $return );
					break;
				case 'return':
					return $return;
					break;
			}
		}

		return null;
	}

	/**
	 * Executes the action directly and returns its results
	 *
	 * @param UserTable $user
	 * @return mixed
	 */
	public function execute( $user )
	{
		$params				=	$this->autoaction()->params()->subTree( 'action' );
		$actions			=	$params->get( 'actions', null, GetterInterface::STRING );

		if ( ! $actions ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_ACTIN_NO_ACTIONS', ':: Action [action] :: Action skipped due to missing actions', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$userId				=	$params->get( 'user', null, GetterInterface::STRING );

		if ( ! $userId ) {
			$userId			=	$user->get( 'id', 0, GetterInterface::INT );
		} else {
			$userId			=	(int) $this->string( $user, $userId );
		}

		if ( $user->get( 'id', 0, GetterInterface::INT ) != $userId ) {
			$actionUser		=	\CBuser::getUserDataInstance( $userId );
		} else {
			$actionUser		=	$user;
		}

		$variables			=	$this->variables();
		$substitutions		=	$this->substitutions();
		$actions			=	cbToArrayOfInt( explode( '|*|', $actions ) );
		$return				=	null;

		foreach ( $actions as $actionId ) {
			$action			=	new AutoActionTable();

			if ( ! $action->load( $actionId ) ) {
				continue;
			}

			if ( ( ! $action->get( 'id', 0 , GetterInterface::INT ) ) || ( ! $action->get( 'published', 1 , GetterInterface::INT ) ) ) {
				continue;
			}

			$return			.=	$action->trigger( $actionUser, $variables, $substitutions );

			if ( $action->getError() ) {
				$this->error( $action->getError() );
			}
		}

		return $return;
	}

	/**
	 * Gets the auto action associated with this action
	 *
	 * @return AutoActionTable
	 */
	public function autoaction()
	{
		if ( ! $this->autoaction ) {
			$this->autoaction	=	new AutoActionTable();
		}

		return $this->autoaction;
	}

	/**
	 * Gets or sets substitution variables for this action
	 *
	 * @param null|array $substitutions
	 * @return array
	 */
	public function substitutions( $substitutions = null )
	{
		if ( $substitutions === null ) {
			return $this->substitutions;
		}

		$this->substitutions	=	$substitutions;

		return $this->substitutions;
	}

	/**
	 * Gets or sets action variables
	 *
	 * @param null|array $variables
	 * @return array
	 */
	public function variables( $variables = null )
	{
		if ( $variables === null ) {
			return $this->variables;
		}

		$this->variables	=	$variables;

		return $this->variables;
	}

	/**
	 * Parses a string through action substitutions
	 *
	 * @param null|UserTable  $user
	 * @param string          $string
	 * @param bool            $htmlspecialchars
	 * @param null|array|bool $translate
	 * @param null|bool       $format
	 * @param null|bool       $prepare
	 * @param null|bool       $substitutions
	 * @return string
	 */
	public function string( $user, $string, $htmlspecialchars = true, $translate = null, $format = null, $prepare = null, $substitutions = null )
	{
		if ( ( $this->autoaction()->params()->get( 'translate', true, GetterInterface::BOOLEAN ) && ( $translate === null ) ) || ( $translate === true ) ) {
			$string						=	CBTxt::T( $string );
		}

		if ( ( ( ! $this->autoaction()->params()->get( 'substitutions', true, GetterInterface::BOOLEAN ) ) && ( $substitutions === null ) ) || ( $substitutions === false ) ) {
			return $string;
		}

		if ( ! $user ) {
			$user						=	\CBuser::getMyInstance()->getUserData();
		}

		$cbUser							=	new \CBuser();
		$cbUser->_cbuser				=	$user;

		$substitutions					=	$this->substitutions();
		$variables						=	$this->variables();

		$substitutions['action_id']		=	$this->autoaction()->get( 'id', 0, GetterInterface::INT );

		$password						=	( isset( $substitutions['password'] ) ? Get::clean( $substitutions['password'], GetterInterface::STRING ) : null );
		$actionUser						=	( isset( $variables['user'] ) ? $variables['user']->get( 'id', 0, GetterInterface::INT ) : 0 );

		$ignore							=	array();
		$ignoreId						=	0;

		$string							=	preg_replace_callback( '%\[cbautoactions:ignore\](.*?)\[/cbautoactions:ignore\]%si', function( array $matches ) use ( &$ignore, &$ignoreId )
												{
													$ignoreId++;

													$ignore[$ignoreId]		=	$matches[1];

													return '[cbautoactions:ignored ' . (int) $ignoreId . ']';
												},
												$string );

		if ( $password !== null ) {
			$string						=	str_ireplace( '[password]', $password, $string );
		}

		$string							=	str_ireplace( '[action_user]', $actionUser, $string );

		if ( ( $this->autoaction()->params()->get( 'content_plugins', false, GetterInterface::BOOLEAN ) && ( $prepare === null ) ) || ( $prepare === true ) ) {
			$string						=	Application::Cms()->prepareHtmlContentPlugins( $string, 'autoaction', ( $user ? $user->get( 'id', 0, GetterInterface::INT ) : 0 ) );
		}

		if ( $cbUser ) {
			$string						=	$cbUser->replaceUserVars( $string, $htmlspecialchars, false, $substitutions, false );
		} else {
			$string						=	( $htmlspecialchars ? htmlspecialchars( $string ) : $string );
		}

		if ( ( $this->autoaction()->params()->get( 'format', false, GetterInterface::BOOLEAN ) && ( $format === null ) ) || ( $format === true ) ) {
			$string						=	CBAutoActions::formatFunction( $string, $variables, $substitutions, $user, $cbUser );
		}

		foreach ( $ignore as $id => $ignored ) {
			$string						=	str_replace( '[cbautoactions:ignored ' . (int) $id . ']', $ignored, $string );
		}

		return $string;
	}

	/**
	 * Checks if the actions dependency is installed
	 *
	 * @return bool
	 */
	public function installed()
	{
		return true;
	}

	/**
	 * Gets or sets action errors
	 *
	 * @param string $error
	 * @return array
	 */
	public function error( $error = null )
	{
		if ( $error ) {
			$this->errors[]		=	$error;
		}

		return $this->errors;
	}
}