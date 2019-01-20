<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Registry\GetterInterface;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\ProfileUpdateLogger\CBProfileUpdateLogger;
use CB\Plugin\ProfileUpdateLogger\Table\UpdateLogTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteLog', '\CB\Plugin\ProfileUpdateLogger\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterUserUpdate', 'logUpdates', '\CB\Plugin\ProfileUpdateLogger\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterUpdateUser', 'logUpdates', '\CB\Plugin\ProfileUpdateLogger\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\ProfileUpdateLogger\Trigger\AdminTrigger' );

class getcbpuloggerTab extends cbTabHandler
{

	/**
	 * prepare frontend tab render
	 *
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @return null|string
	 */
	public function getDisplayTab( $tab, $user, $ui )
	{
		global $_CB_framework, $_CB_database;

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params			=	new Registry( $tab->params );
		}

		$viewer						=	CBuser::getMyUserDataInstance();

		if ( ( ! Application::MyUser()->isGlobalModerator() ) && ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $user->get( 'id', 0, GetterInterface::INT ) ) || ( ! $tab->params->get( 'pulEnableTabUserView', false, GetterInterface::BOOLEAN ) ) ) ) {
			return null;
		}

		$tabPrefix					=	'tab_' . $tab->get( 'tabid', 0, GetterInterface::INT ) . '_';
		$limit						=	$tab->params->get( 'pulEntriesPerPageFE', 20, GetterInterface::INT );
		$limitstart					=	$_CB_framework->getUserStateFromRequest( $tabPrefix . 'limitstart{com_comprofiler}', $tabPrefix . 'limitstart' );

		$query						=	"SELECT COUNT(*)"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_pulogger' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'profileid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );
		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		if ( ( ! $total ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		if ( $total <= $limitstart ) {
			$limitstart				=	0;
		}

		$pageNav					=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $tabPrefix );
		$pageNav->setBaseURL( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ) );

		/** @noinspection PhpUnusedLocalVariableInspection */
		$rows						=	array();

		if ( $total ) {
			$query					=	"SELECT *"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_pulogger' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'profileid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT )
									.	"\n ORDER BY " . $_CB_database->NameQuote( 'changedate' ) . " DESC";
			if ( $tab->params->get( 'pulEnablePagingFE', true, GetterInterface::BOOLEAN ) ) {
				$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			/** @noinspection PhpUnusedLocalVariableInspection */
			$rows					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\ProfileUpdateLogger\Table\UpdateLogTable', array( $_CB_database ) );

			$userIds				=	array();

			/** @var UpdateLogTable[] $rows */
			foreach ( $rows as $row ) {
				$editedBy			=	$row->get( 'editedbyid', 0, GetterInterface::INT );

				if ( $editedBy ) {
					$userIds[]		=	$editedBy;
				}
			}

			if ( $userIds ) {
				\CBuser::advanceNoticeOfUsersNeeded( $userIds );
			}
		}

		ob_start();
		require CBProfileUpdateLogger::getTemplate( null, 'tab' );
		$html						=	ob_get_contents();
		ob_end_clean();

		$class						=	$this->params->get( 'general_class', null );

		$return						=	'<div class="cbProfileUpdateLogger' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
									.		$html
									.	'</div>';

		return $return;
	}
}