<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\AutoActions\Action;

use CB\Database\Table\UserTable;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class InviteAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_INVITE_NOT_INSTALLED', ':: Action [action] :: CB Invites is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		foreach ( $this->autoaction()->params()->subTree( 'invite' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner					=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner				=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner				=	(int) $this->string( $user, $owner );
			}

			if ( ! $owner ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_INVITE_NO_OWNER', ':: Action [action] :: CB Invites skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionUser			=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionUser			=	$user;
			}

			switch ( $row->get( 'mode', 1, GetterInterface::INT ) ) {
				case 1:
					$invite			=	new \cbinvitesInviteTable();

					$toArray		=	explode( ',', $this->string( $actionUser, $row->get( 'to', null, GetterInterface::STRING ) ) );

					foreach ( $toArray as $to ) {
						$invite->set( 'id', null );
						$invite->set( 'to', $to );
						$invite->set( 'subject', $this->string( $actionUser, $row->get( 'subject', null, GetterInterface::STRING ) ) );
						$invite->set( 'body', $this->string( $actionUser, $row->get( 'body', null, GetterInterface::RAW ), false ) );
						$invite->set( 'user_id', $actionUser->get( 'id', 0, GetterInterface::INT ) );
						$invite->set( 'code', md5( uniqid() ) );

						if ( ! $invite->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_INVITE_FAILED', ':: Action [action] :: CB Invites failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $invite->getError() ) ) );
							continue;
						}

						if ( ! $invite->send() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_INVITE_SEND_FAILED', ':: Action [action] :: CB Invites failed to send. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $invite->getError() ) ) );
							continue;
						}
					}
					break;
				case 2:
					$query			=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'to' ) . " = " . $_CB_database->Quote( $actionUser->get( 'email', null, GetterInterface::STRING ) );
					$_CB_database->setQuery( $query );
					$invites		=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

					/** @var \cbinvitesInviteTable[] $invites */
					foreach ( $invites as $invite ) {
						$invite->accept( $actionUser );
					}
					break;
				case 3:
					$query			=	'SELECT *'
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_invites' )
									.	"\n WHERE ( " . $_CB_database->NameQuote( 'user_id' ) . " = " . $actionUser->get( 'id', 0, GetterInterface::INT )
									.	' OR ' . $_CB_database->NameQuote( 'user' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT ) . ' )';
					$_CB_database->setQuery( $query );
					$invites		=	$_CB_database->loadObjectList( null, 'cbinvitesInviteTable', array( $_CB_database ) );

					/** @var \cbinvitesInviteTable[] $invites */
					foreach ( $invites as $invite ) {
						$invite->delete();
					}
					break;
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		if ( $_PLUGINS->getLoadedPlugin( 'user', 'cbinvites' ) ) {
			return true;
		}

		return false;
	}
}