<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;
use CB\Plugin\Privacy\CBPrivacy;
use CB\Plugin\Privacy\Privacy;
use CB\Plugin\Privacy\PrivacyInterface;
use CB\Plugin\Privacy\Table\PrivacyTable;
use CB\Plugin\Privacy\Table\ClosedTable;
use CBLib\Input\Get;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

class CBplug_cbprivacy extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		$action				=	$this->input( 'action', null, GetterInterface::STRING );

		if ( $action == 'privacy' ) {
			$this->getPrivacy();
			return;
		}

		$raw				=	( $this->input( 'format', null, GetterInterface::STRING ) == 'raw' );
		$function			=	$this->input( 'func', null, GetterInterface::STRING );
		$id					=	$this->input( 'id', null, GetterInterface::INT );
		$user				=	CBuser::getMyUserDataInstance();

		if ( ! $raw ) {
			outputCbJs();
			outputCbTemplate();

			ob_start();
		}

		switch ( $action ) {
			case 'disable':
				switch ( $function ) {
					case 'enable':
						$this->saveEnable( $id, $user );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveDisable( $id, $user );
						break;
					case 'show':
					default:
						$this->showDisableEdit( $id, $user );
						break;
				}
				break;
			case 'delete':
				switch ( $function ) {
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveDelete( $id, $user );
						break;
					case 'show':
					default:
						$this->showDeleteEdit( $id, $user );
						break;
				}
				break;
			default:
				CBRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
				break;
		}

		if ( ! $raw ) {
			$html			=	ob_get_contents();
			ob_end_clean();

			if ( ! $html ) {
				return;
			}

			$class			=	$this->params->get( 'general_class', null, GetterInterface::STRING );

			$return			=	'<div class="cbPrivacy' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
							.		$html
							.	'</div>';

			echo $return;
		}
	}

	/**
	 * Loads in a privacy directly or by URL
	 *
	 * @param null|Privacy $privacy
	 * @param null|string  $view
	 */
	public function getPrivacy( $privacy = null, $view = null )
	{
		global $_PLUGINS;

		$viewer							=	CBuser::getMyUserDataInstance();
		$raw							=	false;
		$inline							=	false;
		$access							=	true;

		$privacyLoaded					=	false;

		if ( $privacy ) {
			if ( ! ( $privacy instanceof PrivacyInterface ) ) {
				return;
			}

			$function					=	( $view ? $view : 'edit' );
			$inline						=	true;

			$privacyLoaded				=	true;
		} else {
			$raw						=	( $this->input( 'format', null, GetterInterface::STRING ) == 'raw' );
			$function					=	$this->input( 'func', null, GetterInterface::STRING );
			$privacyId					=	$this->input( 'privacy', null, GetterInterface::STRING );

			$privacy					=	new Privacy( null, $viewer );

			if ( $privacyId ) {
				if ( $privacy->load( $privacyId ) ) {
					$privacyLoaded		=	true;
				} else {
					$access				=	false;
				}
			} else {
				$access					=	false;
			}
		}

		if ( ! $privacy->asset() ) {
			$access						=	false;
		}

		$_PLUGINS->trigger( 'privacy_onPrivacy', array( &$privacy, &$access, $privacyLoaded ) );

		if ( ! $access ) {
			if ( $inline ) {
				return;
			} elseif ( $raw ) {
				header( 'HTTP/1.0 401 Unauthorized' );
				exit();
			} else {
				cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		if ( ! $privacy->id() ) {
			$privacy->cache();
		}

		if ( ! $raw ) {
			outputCbJs();
			outputCbTemplate();

			ob_start();
		}

		switch ( $function ) {
			case 'edit':
				$this->showPrivacyEdit( $viewer, $privacy );
				break;
			case 'save':
				$this->savePrivacy( $viewer, $privacy, $raw );
				break;
		}

		if ( ! $raw ) {
			$html						=	ob_get_contents();
			ob_end_clean();

			if ( ! $html ) {
				return;
			}

			$class						=	$this->params->get( 'general_class', null, GetterInterface::STRING );

			$return						=	'<span class="cbPrivacy' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
										.		$html
										.	'</span>';

			echo $return;
		}
	}

	/**
	 * Checks if the disable account field is accessible to a user
	 * 
	 * @param int       $userId
	 * @param UserTable $user
	 * @return bool
	 */
	private function getDisableField( $userId, $user )
	{
		if ( ( ! $userId ) || ( ( $userId != $user->get( 'id', 0, GetterInterface::INT ) ) && ( ! Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) ) || Application::User( $userId )->isGlobalModerator() ) {
			return false;
		}

		$fields		=	CBuser::getInstance( $userId, false )->_getCbTabs( false )->_getTabFieldsDb( null, $user, 'edit', 'privacy_disable_me' );

		return ( $fields ? true : false );
	}

	/**
	 * Displays account disable form
	 *
	 * @param int       $userId
	 * @param UserTable $user
	 */
	private function showDisableEdit( $userId, $user )
	{
		if ( ! $userId ) {
			$userId		=	$user->get( 'id', 0, GetterInterface::INT );
		}

		if ( ! $this->getDisableField( $userId, $user ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		require CBPrivacy::getTemplate( null, 'disable' );
	}

	/**
	 * Disables a users account
	 *
	 * @param int       $userId
	 * @param UserTable $user
	 */
	private function saveDisable( $userId, $user )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $userId ) {
			$userId				=	$user->get( 'id', 0, GetterInterface::INT );
		}

		if ( ! $this->getDisableField( $userId, $user ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$cbUser					=	CBuser::getInstance( $userId, false );
		$disableUser			=	$cbUser->getUserData();

		if ( ! $disableUser->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$_PLUGINS->trigger( 'privacy_onBeforeAccountDisable', array( &$disableUser, $user ) );

		$disableUser->set( 'block', 1 );

		if ( $disableUser->storeBlock() ) {
			$closed				=	new ClosedTable();

			$closed->set( 'user_id', $disableUser->get( 'id', 0, GetterInterface::INT ) );
			$closed->set( 'username', $disableUser->get( 'username', null, GetterInterface::STRING ) );
			$closed->set( 'name', $disableUser->get( 'name', null, GetterInterface::STRING ) );
			$closed->set( 'email', $disableUser->get( 'email', null, GetterInterface::STRING ) );
			$closed->set( 'type', 'disable' );
			$closed->set( 'reason', $this->input( 'reason', null, GetterInterface::STRING ) );

			$closed->store();

			$notification		=	new cbNotification();

			$extra				=	array(	'ip_address'	=>	cbGetIPlist(),
											'reason'		=>	$closed->get( 'reason', null, GetterInterface::STRING ),
											'date'			=>	cbFormatDate( $closed->get( 'date', null, GetterInterface::STRING ) )
										);

			if ( $this->params->get( 'disable_notify', true, GetterInterface::BOOLEAN ) ) {
				$subject		=	$cbUser->replaceUserVars( CBTxt::T( 'User Account Disabled' ), true, false, $extra, false );
				$body			=	$cbUser->replaceUserVars( CBTxt::T( 'Name: [name]<br />Username: [username]<br />Email: [email]<br />IP Address: [ip_address]<br />Date: [date]<br /><br />[reason]<br /><br />' ), false, false, $extra, false );

				if ( $subject && $body ) {
					$notification->sendToModerators( $subject, $body, false, 1 );
				}
			}

			$savedLanguage		=	CBTxt::setLanguage( $disableUser->getUserLanguage() );

			$subject			=	CBTxt::T( 'Your Account has been Disabled' );
			$body				=	CBTxt::T( 'This is a notice that your account [username] on [siteurl] has been disabled.' );

			CBTxt::setLanguage( $savedLanguage );

			if ( $subject && $body ) {
				$notification->sendFromSystem( $disableUser, $subject, $body, true, 1, null, null, null, $extra );
			}

			$_PLUGINS->trigger( 'privacy_onAfterAccountDisable', array( $disableUser, $user ) );

			$_CB_framework->logout();

			cbRedirect( 'index.php', CBTxt::T( 'Account disabled successfully!' ) );
		}

		cbRedirect( $_CB_framework->userProfileUrl( $userId, false ), CBTxt::T( 'ACCOUNT_FAILED_TO_DISABLE', 'Account failed to disable! Error: [error]', array( '[error]' => $disableUser->getError() ) ), 'error' );
	}

	/**
	 * Displays account enable form
	 *
	 * @param int       $userId
	 * @param UserTable $user
	 */
	private function saveEnable( $userId, $user )
	{
		global $_CB_framework;

		if ( ! $userId ) {
			$userId			=	$user->get( 'id', 0, GetterInterface::INT );
		}

		if ( ( ! $userId ) || $user->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$cbUser				=	CBuser::getInstance( $userId, false );
		$disabledUser		=	$cbUser->getUserData();

		if ( ( ! $disabledUser->get( 'block', 0, GetterInterface::INT ) ) || ( ! $disabledUser->get( 'approved', 1, GetterInterface::INT ) ) || ( ! $disabledUser->get( 'confirmed', 1, GetterInterface::INT ) ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$closed				=	new ClosedTable();

		$closed->load( array( 'user_id' => $disabledUser->get( 'id', 0, GetterInterface::INT ), 'type' => 'disable' ) );

		if ( ! $closed->get( 'id', 0, GetterInterface::INT ) ) {
			$closed->load( array( 'user_id' => $disabledUser->get( 'id', 0, GetterInterface::INT ), 'type' => 'pending' ) );

			if ( $closed->get( 'id', 0, GetterInterface::INT ) ) {
				cbRedirect( 'index.php', CBTxt::T( 'Your account is already awaiting confirmation to re-enable. Please check your email for your confirmation.' ) );
			}

			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$closed->set( 'type', 'pending' );
		$closed->set( 'date', Application::Database()->getUtcDateTime() );

		$closed->store();

		cbRedirect( $_CB_framework->viewUrl( 'login' ), CBTxt::T( 'Please login to re-enable your account.' ) );
	}

	/**
	 * Checks if the delete account field is accessible to a user
	 *
	 * @param int       $userId
	 * @param UserTable $user
	 * @return bool
	 */
	private function getDeleteField( $userId, $user )
	{
		if ( ( ! $userId ) || ( ( $userId != $user->get( 'id', 0, GetterInterface::INT ) ) && ( ! Application::User( $user->get( 'id', 0, GetterInterface::INT ) )->isGlobalModerator() ) ) || Application::User( $userId )->isGlobalModerator() ) {
			return false;
		}

		$fields		=	CBuser::getInstance( $userId, false )->_getCbTabs( false )->_getTabFieldsDb( null, $user, 'edit', 'privacy_delete_me' );

		return ( $fields ? true : false );
	}

	/**
	 * Displays account delete form
	 *
	 * @param int       $userId
	 * @param UserTable $user
	 */
	private function showDeleteEdit( $userId, $user )
	{
		if ( ! $userId ) {
			$userId		=	$user->get( 'id', 0, GetterInterface::INT );
		}

		if ( ! $this->getDeleteField( $userId, $user ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		require CBPrivacy::getTemplate( null, 'delete' );
	}

	/**
	 * Deletes a users account
	 *
	 * @param int       $userId
	 * @param UserTable $user
	 */
	private function saveDelete( $userId, $user )
	{
		global $_CB_framework, $_PLUGINS;

		if ( ! $userId ) {
			$userId				=	$user->get( 'id', 0, GetterInterface::INT );
		}

		if ( ! $this->getDeleteField( $userId, $user ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$cbUser					=	CBuser::getInstance( $userId, false );
		$deleteUser				=	$cbUser->getUserData();

		if ( ! $deleteUser->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$_PLUGINS->trigger( 'privacy_onBeforeAccountDelete', array( &$deleteUser, $user ) );

		if ( $deleteUser->delete( $userId ) ) {
			$closed				=	new ClosedTable();

			$closed->set( 'user_id', $deleteUser->get( 'id', 0, GetterInterface::INT ) );
			$closed->set( 'username', $deleteUser->get( 'username', null, GetterInterface::STRING ) );
			$closed->set( 'name', $deleteUser->get( 'name', null, GetterInterface::STRING ) );
			$closed->set( 'email', $deleteUser->get( 'email', null, GetterInterface::STRING ) );
			$closed->set( 'type', 'delete' );
			$closed->set( 'reason', $this->input( 'reason', null, GetterInterface::STRING ) );

			$closed->store();

			$notification		=	new cbNotification();

			$extra				=	array(	'ip_address'	=>	cbGetIPlist(),
											'reason'		=>	$closed->get( 'reason', null, GetterInterface::STRING ),
											'date'			=>	cbFormatDate( $closed->get( 'date', null, GetterInterface::STRING ) )
										);

			if ( $this->params->get( 'delete_notify', true, GetterInterface::BOOLEAN ) ) {
				$subject		=	$cbUser->replaceUserVars( CBTxt::T( 'User Account Deleted' ), true, false, $extra, false );
				$body			=	$cbUser->replaceUserVars( CBTxt::T( 'Name: [name]<br />Username: [username]<br />Email: [email]<br />IP Address: [ip_address]<br />Date: [date]<br /><br />[reason]<br /><br />' ), false, false, $extra, false );

				if ( $subject && $body ) {
					$notification->sendToModerators( $subject, $body, false, 1 );
				}
			}

			$savedLanguage		=	CBTxt::setLanguage( $deleteUser->getUserLanguage() );

			$subject			=	CBTxt::T( 'Your Account has been Deleted' );
			$body				=	CBTxt::T( 'This is a notice that your account [username] on [siteurl] has been deleted.' );

			CBTxt::setLanguage( $savedLanguage );

			if ( $subject && $body ) {
				$notification->sendFromSystem( $deleteUser, $subject, $body, true, 1, null, null, null, $extra );
			}

			$_PLUGINS->trigger( 'privacy_onAfterAccountDelete', array( $deleteUser, $user ) );

			cbRedirect( 'index.php', CBTxt::T( 'Account deleted successfully!' ) );
		}

		cbRedirect( $_CB_framework->userProfileUrl( $userId, false ), CBTxt::T( 'ACCOUNT_FAILED_TO_DELETE', 'Account failed to delete! Error: [error]', array( '[error]' => $deleteUser->getError() ) ), 'error' );
	}

	/**
	 * Displays privacy edit
	 *
	 * @param UserTable $viewer
	 * @param Privacy   $privacy
	 */
	private function showPrivacyEdit( $viewer, $privacy )
	{
		global $_CB_framework;

		if ( $privacy->get( 'ajax', false, GetterInterface::BOOLEAN ) && ( ( ! $privacy->user()->get( 'id', 0, GetterInterface::INT ) ) || preg_match( '/\.0$/', $privacy->asset() ) ) ) {
			$privacy->set( 'ajax', false );
		}

		/** @noinspection PhpUnusedLocalVariableInspection */
		$rows						=	array();
		$selected					=	array();

		if ( $privacy->get( 'query', true, GetterInterface::BOOLEAN ) ) {
			$rows					=	CBPrivacy::getPrivacy( $privacy->user() );

			if ( ! isset( $rows[$privacy->asset()] ) ) {
				/** @noinspection PhpUnusedLocalVariableInspection */
				$rows				=	array();
			} else {
				$rows				=	$rows[$privacy->asset()];

				/** @var PrivacyTable[] $rows */
				foreach ( $rows as $row ) {
					$rule			=	$row->get( 'rule', null, GetterInterface::STRING );

					if ( $rule === null ) {
						continue;
					}

					$selected[]		=	$rule;
				}
			}
		}

		/** @noinspection PhpUnusedLocalVariableInspection */
		$ajaxUrl					=	$_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'privacy', 'func' => 'save', 'privacy' => $privacy->id() ), 'raw', 0, true );

		require CBPrivacy::getTemplate( $privacy->get( 'template', null, GetterInterface::STRING ), 'edit' );
	}

	/**
	 * Save privacy
	 *
	 * @param UserTable $viewer
	 * @param Privacy   $privacy
	 * @param bool      $ajax
	 */
	private function savePrivacy( $viewer, $privacy, $ajax = false )
	{
		global $_PLUGINS;

		if ( $ajax && ( ! $privacy->get( 'ajax', false, GetterInterface::BOOLEAN ) ) ) {
			header( 'HTTP/1.0 401 Unauthorized' );
			exit();
		}

		if ( ! $privacy->get( 'guest', false, GetterInterface::BOOLEAN ) ) {
			if ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( $viewer->get( 'id', 0, GetterInterface::INT ) != $privacy->user()->get( 'id', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) {
				if ( $ajax ) {
					header( 'HTTP/1.0 401 Unauthorized' );
					exit();
				}

				return;
			}
		}

		if ( ! $privacy->user()->get( 'id', 0, GetterInterface::INT ) ) {
			$privacy->user( $viewer );
		}

		if ( ! $privacy->user()->get( 'id', 0, GetterInterface::INT ) ) {
			if ( $ajax ) {
				header( 'HTTP/1.0 401 Unauthorized' );
				exit();
			}

			return;
		}

		$rules				=	$this->input( md5( 'privacy_' . $privacy->asset() ), array(), GetterInterface::RAW );

		if ( ! $rules ) {
			// Check if for post values if this is a new entry:
			$rules			=	$this->input( md5( 'privacy_' . preg_replace( '/\.(\d+)$/', '.0', $privacy->asset() ) ), array(), GetterInterface::RAW );
		}

		// Ensure the selected rules are allowed in this privacy usage:
		if ( ! Application::Cms()->getClientId() ) {
			foreach ( $rules as $k => $v ) {
				if ( ! array_key_exists( $v, $privacy->rules( true ) ) ) {
					unset( $rules[$k] );
				}
			}
		}

		foreach ( $privacy->reset()->rows( 'all' ) as $rule ) {
			/** @var PrivacyTable $rule */
			if ( ! in_array( $rule->get( 'rule', null, GetterInterface::STRING ), $rules ) ) {
				$_PLUGINS->trigger( 'privacy_onBeforeDeletePrivacyRule', array( $privacy, &$rule ) );

				if ( $rule->getError() || ( ! $rule->canDelete() ) || ( ! $rule->delete() ) ) {
					continue;
				}

				$_PLUGINS->trigger( 'privacy_onAfterDeletePrivacyRule', array( $privacy, $rule ) );
			} else {
				$key		=	array_search( $rule->get( 'rule', null, GetterInterface::STRING ), $rules );

				if ( $key !== false ) {
					unset( $rules[$key] );
				}
			}
		}

		foreach ( $rules as $privacyRule ) {
			$rule			=	new PrivacyTable();

			$rule->set( 'user_id', $privacy->user()->get( 'id', 0, GetterInterface::INT ) );
			$rule->set( 'asset', $privacy->asset() );
			$rule->set( 'rule', Get::clean( $privacyRule, GetterInterface::STRING ) );

			$_PLUGINS->trigger( 'privacy_onBeforeCreatePrivacyRule', array( $privacy, &$rule ) );

			if ( $rule->getError() || ( ! $rule->check() ) || ( ! $rule->store() ) ) {
				continue;
			}

			$_PLUGINS->trigger( 'privacy_onAfterCreatePrivacyRule', array( $privacy, $rule ) );
		}

		$privacy->clear();

		if ( $ajax ) {
			header( 'HTTP/1.0 200 OK' );
			exit();
		}
	}
}