<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CBLib\Application\Application;
use CBLib\Registry\Registry;
use CBLib\Registry\ParamsInterface;
use CBLib\Registry\GetterInterface;
use CB\Plugin\ProfileBook\CBProfileBook;
use CB\Plugin\ProfileBook\Table\EntryTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

global $_PLUGINS;

$_PLUGINS->loadPluginGroup( 'user' );

$_PLUGINS->registerFunction( 'onAfterDeleteUser', 'deleteEntries', '\CB\Plugin\ProfileBook\Trigger\UserTrigger' );
$_PLUGINS->registerFunction( 'onAfterLogoutForm', 'getEntriesNotification', '\CB\Plugin\ProfileBook\Trigger\UserTrigger' );

$_PLUGINS->registerFunction( 'mod_onCBAdminMenu', 'adminMenu', '\CB\Plugin\ProfileBook\Trigger\AdminTrigger' );

$_PLUGINS->registerUserFieldParams();
$_PLUGINS->registerUserFieldTypes( array( 'pb_profile_rating' => '\CB\Plugin\ProfileBook\Field\ProfileRatingField' ) );

class getprofilebookTab extends cbTabHandler
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

		if ( $user->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) {
			return null;
		}

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params			=	new Registry( $tab->params );
		}

		$viewer						=	CBuser::getMyUserDataInstance();
		$isModerator				=	Application::MyUser()->isGlobalModerator();
		$isOwner					=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) );

		$tabPrefix					=	'tab_' . $tab->get( 'tabid', 0, GetterInterface::INT ) . '_';
		$limit						=	$tab->params->get( 'pbEntriesPerPage', 10, GetterInterface::INT );
		$limitstart					=	$_CB_framework->getUserStateFromRequest( $tabPrefix . 'limitstart{com_comprofiler}', $tabPrefix . 'limitstart' );

		$query						=	"SELECT COUNT(*)"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'g' )
									.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );

		if ( ( ! $isOwner ) && ( ! $isModerator ) ) {
			$query					.=	"\n AND ( " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	" OR " . $_CB_database->NameQuote( 'posterid' ) . " = " . $viewer->get( 'id', 0, GetterInterface::INT ) . " )";
		}

		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		if ( ( ! $total ) && ( $isOwner || ( ( ! $isModerator ) && ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) && ( ! $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) ) ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
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
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'g' )
									.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );

			if ( ( ! $isOwner ) && ( ! $isModerator ) ) {
				$query				.=	"\n AND ( " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	" OR " . $_CB_database->NameQuote( 'posterid' ) . " = " . $viewer->get( 'id', 0, GetterInterface::INT ) . " )";
			}

			$query					.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . ( $tab->params->get( 'pbSortDirection', 'DESC', GetterInterface::STRING ) == 'DESC' ? " DESC" : " ASC" );
			if ( $tab->params->get( 'pbPagingEngabbled', true, GetterInterface::BOOLEAN ) ) {
				$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			/** @noinspection PhpUnusedLocalVariableInspection */
			$rows					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\ProfileBook\Table\EntryTable', array( $_CB_database ) );

			$userIds				=	array();

			if ( $isOwner ) {
				$unread				=	array();

				/** @var EntryTable[] $rows */
				foreach ( $rows as $row ) {
					if ( $row->get( 'status', 0, GetterInterface::INT ) == 0 ) {
						$unread[]	=	$row->get( 'id', 0, GetterInterface::INT );
					}

					$posterId		=	$row->get( 'posterid', 0, GetterInterface::INT );

					if ( $posterId ) {
						$userIds[]	=	$posterId;
					}
				}

				if ( $unread ) {
					$query			=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
									.	"\n SET " . $_CB_database->NameQuote( 'status' ) . " = 1"
									.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $unread );
					$_CB_database->setQuery( $query );
					$_CB_database->query();
				}
			} else {
				/** @var EntryTable[] $rows */
				foreach ( $rows as $row ) {
					$posterId		=	$row->get( 'posterid', 0, GetterInterface::INT );

					if ( $posterId ) {
						$userIds[]	=	$posterId;
					}
				}
			}

			if ( $userIds ) {
				\CBuser::advanceNoticeOfUsersNeeded( $userIds );
			}
		}

		ob_start();
		require CBProfileBook::getTemplate( $tab->params->get( 'template', '-1', GetterInterface::STRING ), 'guestbook' );
		$html						=	ob_get_contents();
		ob_end_clean();

		$class						=	$this->params->get( 'general_class', null );

		$return						=	'<div class="cbProfileBook' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
									.		$html
									.	'</div>';

		return $return;
	}
}

class getprofilebookblogTab extends cbTabHandler
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

		if ( $user->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) {
			return null;
		}

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params	=	new Registry( $tab->params );
		}

		$viewer				=	CBuser::getMyUserDataInstance();
		$isModerator		=	Application::MyUser()->isGlobalModerator();
		$isOwner			=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) );

		$tabPrefix			=	'tab_' . $tab->get( 'tabid', 0, GetterInterface::INT ) . '_';
		$limit				=	$tab->params->get( 'pbEntriesPerPage', 10, GetterInterface::INT );
		$limitstart			=	$_CB_framework->getUserStateFromRequest( $tabPrefix . 'limitstart{com_comprofiler}', $tabPrefix . 'limitstart' );

		$query				=	"SELECT COUNT(*)"
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'b' )
							.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );

		if ( ( ! $isOwner ) && ( ! $isModerator ) ) {
			$query			.=	"\n AND ( " . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	" OR " . $_CB_database->NameQuote( 'posterid' ) . " = " . $viewer->get( 'id', 0, GetterInterface::INT ) . " )";
		}

		$_CB_database->setQuery( $query );
		$total				=	$_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $isOwner ) && ( ! $isModerator ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		if ( $total <= $limitstart ) {
			$limitstart		=	0;
		}

		$pageNav			=	new cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $tabPrefix );
		$pageNav->setBaseURL( $_CB_framework->userProfileUrl( $user->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ) );

		/** @noinspection PhpUnusedLocalVariableInspection */
		$rows				=	array();

		if ( $total ) {
			$query			=	"SELECT *"
							.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
							.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'b' )
							.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );

			if ( ( ! $isOwner ) && ( ! $isModerator ) ) {
				$query		.=	"\n AND ( " . $_CB_database->NameQuote( 'published' ) . " = 1"
							.	" OR " . $_CB_database->NameQuote( 'posterid' ) . " = " . $viewer->get( 'id', 0, GetterInterface::INT ) . " )";
			}

			$query			.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . ( $tab->params->get( 'pbSortDirection', 'DESC', GetterInterface::STRING ) == 'DESC' ? " DESC" : " ASC" );
			if ( $tab->params->get( 'pbPagingEngabbled', true, GetterInterface::BOOLEAN ) ) {
				$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			/** @noinspection PhpUnusedLocalVariableInspection */
			$rows			=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\ProfileBook\Table\EntryTable', array( $_CB_database ) );
		}

		ob_start();
		require CBProfileBook::getTemplate( $tab->params->get( 'template', '-1', GetterInterface::STRING ), 'blogs' );
		$html				=	ob_get_contents();
		ob_end_clean();

		$class				=	$this->params->get( 'general_class', null );

		$return				=	'<div class="cbProfileBook' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
							.		$html
							.	'</div>';

		return $return;
	}
}

class getprofilebookwallTab extends cbTabHandler
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

		if ( $user->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
			return null;
		}

		if ( ! ( $tab->params instanceof ParamsInterface ) ) {
			$tab->params			=	new Registry( $tab->params );
		}

		$viewer						=	CBuser::getMyUserDataInstance();
		$isModerator				=	Application::MyUser()->isGlobalModerator();
		$isOwner					=	( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) );

		$tabPrefix					=	'tab_' . $tab->get( 'tabid', 0, GetterInterface::INT ) . '_';
		$limit						=	$tab->params->get( 'pbEntriesPerPage', 10, GetterInterface::INT );
		$limitstart					=	$_CB_framework->getUserStateFromRequest( $tabPrefix . 'limitstart{com_comprofiler}', $tabPrefix . 'limitstart' );

		$query						=	"SELECT COUNT(*)"
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'w' )
									.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );

		if ( ( ! $isOwner ) && ( ! $isModerator ) ) {
			$query					.=	"\n AND ( " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	" OR " . $_CB_database->NameQuote( 'posterid' ) . " = " . $viewer->get( 'id', 0, GetterInterface::INT ) . " )";
		}

		$_CB_database->setQuery( $query );
		$total						=	$_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $isOwner ) && ( ! $isModerator ) && ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) && ( ! $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) && ( ! Application::Config()->get( 'showEmptyTabs', true, GetterInterface::BOOLEAN ) ) ) {
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
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
									.	"\n WHERE " . $_CB_database->NameQuote( 'mode' ) . " = " . $_CB_database->Quote( 'w' )
									.	"\n AND " . $_CB_database->NameQuote( 'userid' ) . " = " . $user->get( 'id', 0, GetterInterface::INT );

			if ( ( ! $isOwner ) && ( ! $isModerator ) ) {
				$query				.=	"\n AND ( " . $_CB_database->NameQuote( 'published' ) . " = 1"
									.	" OR " . $_CB_database->NameQuote( 'posterid' ) . " = " . $viewer->get( 'id', 0, GetterInterface::INT ) . " )";
			}

			$query					.=	"\n ORDER BY " . $_CB_database->NameQuote( 'date' ) . ( $tab->params->get( 'pbSortDirection', 'DESC', GetterInterface::STRING ) == 'DESC' ? " DESC" : " ASC" );
			if ( $tab->params->get( 'pbPagingEngabbled', true, GetterInterface::BOOLEAN ) ) {
				$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
			} else {
				$_CB_database->setQuery( $query );
			}
			/** @noinspection PhpUnusedLocalVariableInspection */
			$rows					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\ProfileBook\Table\EntryTable', array( $_CB_database ) );

			$userIds				=	array();

			if ( $isOwner ) {
				$unread				=	array();

				/** @var EntryTable[] $rows */
				foreach ( $rows as $row ) {
					if ( $row->get( 'status', 0, GetterInterface::INT ) == 0 ) {
						$unread[]	=	$row->get( 'id', 0, GetterInterface::INT );
					}

					$posterId		=	$row->get( 'posterid', 0, GetterInterface::INT );

					if ( $posterId ) {
						$userIds[]	=	$posterId;
					}
				}

				if ( $unread ) {
					$query			=	'UPDATE '. $_CB_database->NameQuote( '#__comprofiler_plug_profilebook' )
									.	"\n SET " . $_CB_database->NameQuote( 'status' ) . " = 1"
									.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $unread );
					$_CB_database->setQuery( $query );
					$_CB_database->query();
				}
			} else {
				/** @var EntryTable[] $rows */
				foreach ( $rows as $row ) {
					$posterId		=	$row->get( 'posterid', 0, GetterInterface::INT );

					if ( $posterId ) {
						$userIds[]	=	$posterId;
					}
				}
			}

			if ( $userIds ) {
				\CBuser::advanceNoticeOfUsersNeeded( $userIds );
			}
		}

		ob_start();
		require CBProfileBook::getTemplate( $tab->params->get( 'template', '-1', GetterInterface::STRING ), 'wall' );
		$html						=	ob_get_contents();
		ob_end_clean();

		$class						=	$this->params->get( 'general_class', null );

		$return						=	'<div class="cbProfileBook' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
									.		$html
									.	'</div>';

		return $return;
	}
}