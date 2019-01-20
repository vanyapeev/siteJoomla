<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\GroupJiveFile\Trigger;

use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Plugin\GroupJive\CBGroupJive;
use CB\Plugin\GroupJive\Table\GroupTable;
use CB\Plugin\GroupJive\Table\CategoryTable;
use CB\Plugin\GroupJive\Table\NotificationTable;
use CB\Plugin\GroupJiveFile\Table\FileTable;
use CB\Plugin\GroupJiveFile\CBGroupJiveFile;

defined('CBLIB') or die();

class FileTrigger extends \cbPluginHandler
{

	/**
	 * check file create limit
	 *
	 * @param bool       $access
	 * @param string     $param
	 * @param GroupTable $group
	 * @param UserTable  $user
	 */
	public function canCreate( &$access, $param, $group, $user )
	{
		global $_CB_database;

		if ( $param == 'file' ) {
			$createLimit				=	(int) $this->params->get( 'groups_file_create_limit', 0 );

			if ( $createLimit ) {
				static $count			=	array();

				$countId				=	md5( $user->get( 'id' ) . $group->get( 'id' ) );

				if ( ! isset( $count[$countId] ) ) {
					$query				=	'SELECT COUNT(*)'
										.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_file' )
										.	"\n WHERE " . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
										.	"\n AND " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' );
					$_CB_database->setQuery( $query );
					$count[$countId]	=	(int) $_CB_database->loadResult();
				}

				if ( $count[$countId] >= $createLimit ) {
					$access				=	false;
				}
			}
		}
	}

	/**
	 * render frontend file group edit params
	 *
	 * @param string        $return
	 * @param GroupTable    $row
	 * @param array         $input
	 * @param CategoryTable $category
	 * @param UserTable     $user
	 * @return string
	 */
	public function editGroup( &$return, &$row, &$input, $category, $user )
	{
		CBGroupJive::getTemplate( 'group_edit', true, true, $this->element );

		$listEnable			=	array();
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 0, CBTxt::T( 'Disable' ) );
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 1, CBTxt::T( 'Enable' ) );
		$listEnable[]		=	\moscomprofilerHTML::makeOption( 2, CBTxt::T( 'Enable, with Approval' ) );

		$enableTooltip		=	cbTooltip( null, CBTxt::T( 'Optionally enable or disable usage of files. Group owner and group administrators are exempt from this configuration and can always upload files. Note existing files will still be accessible.' ), null, null, null, null, null, 'data-hascbtooltip="true"' );

		$input['file']		=	\moscomprofilerHTML::selectList( $listEnable, 'params[file]', 'class="form-control"' . $enableTooltip, 'value', 'text', (int) $this->input( 'post/params.file', $row->params()->get( 'file', 1 ), GetterInterface::INT ), 1, false, false );

		return \HTML_groupjiveFileParams::showFileParams( $row, $input, $category, $user, $this );
	}

	/**
	 * delete the local files and the directory when a category is deleted
	 *
	 * @param CategoryTable $category
	 */
	public function deleteCategory( $category )
	{
		global $_CB_framework;

		CBGroupJive::deleteDirectory( $_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivefile/' . (int) $category->get( 'id' ) );
	}

	/**
	 * delete all the files for the group that was deleted
	 *
	 * @param GroupTable $group
	 */
	public function deleteGroup( $group )
	{
		global $_CB_framework, $_CB_database;

		$query			=	'SELECT *'
						.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_file' )
						.	"\n WHERE " . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' );
		$_CB_database->setQuery( $query );
		$files			=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveFile\Table\FileTable', array( $_CB_database ) );

		/** @var FileTable[] $files */
		foreach ( $files as $file ) {
			$file->delete();
		}

		CBGroupJive::deleteDirectory( $_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/cbgroupjivefile/' . (int) $group->get( 'category' ) . '/' . (int) $group->get( 'id' ) );
	}

	/**
	 * render frontend file group notifications edit params
	 *
	 * @param string            $return
	 * @param NotificationTable $row
	 * @param array             $input
	 * @param GroupTable        $group
	 * @param UserTable         $user
	 * @return string
	 */
	public function editNotifications( &$return, &$row, &$input, $group, $user )
	{
		CBGroupJive::getTemplate( 'notifications', true, true, $this->element );

		$listToggle					=	array();
		$listToggle[]				=	\moscomprofilerHTML::makeOption( '0', CBTxt::T( "Don't Notify" ) );
		$listToggle[]				=	\moscomprofilerHTML::makeOption( '1', CBTxt::T( 'Notify' ) );

		$input['file_new']			=	\moscomprofilerHTML::yesnoSelectList( 'params[file_new]', 'class="form-control"', (int) $this->input( 'post/params.file_new', $row->params()->get( 'file_new', $this->params->get( 'notifications_default_file_new', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );
		$input['file_approve']		=	\moscomprofilerHTML::yesnoSelectList( 'params[file_approve]', 'class="form-control"', (int) $this->input( 'post/params.file_approve', $row->params()->get( 'file_approve', $this->params->get( 'notifications_default_file_approve', 0 ) ), GetterInterface::INT ), CBTxt::T( 'Notify' ), CBTxt::T( "Don't Notify" ), false );

		return \HTML_groupjiveFileNotifications::showFileNotifications( $row, $input, $group, $user, $this );
	}

	/**
	 * store default notifications
	 *
	 * @param \CB\Plugin\GroupJive\Table\UserTable $row
	 * @param Registry                             $notifications
	 */
	public function storeNotifications( $row, &$notifications )
	{
		$notifications->set( 'file_new', $this->params->get( 'notifications_default_file_new', 0 ) );
		$notifications->set( 'file_approve', $this->params->get( 'notifications_default_file_approve', 0 ) );
	}

	/**
	 * prepare frontend files render
	 *
	 * @param string     $return
	 * @param GroupTable $group
	 * @param string     $users
	 * @param string     $invites
	 * @param array      $counters
	 * @param array      $buttons
	 * @param array      $menu
	 * @param \cbTabs    $tabs
	 * @param UserTable  $user
	 * @return array|null
	 */
	public function showFiles( &$return, &$group, &$users, &$invites, &$counters, &$buttons, &$menu, &$tabs, $user )
	{
		global $_CB_framework, $_CB_database;

		CBGroupJive::getTemplate( 'files', true, true, $this->element );

		$canModerate			=	( CBGroupJive::isModerator( $user->get( 'id' ) ) || ( CBGroupJive::getGroupStatus( $user, $group ) >= 2 ) );
		$prefix					=	'gj_group_' . $group->get( 'id', 0, GetterInterface::INT ) . '_files_';
		$limit					=	(int) $this->params->get( 'groups_file_limit', 15 );
		$limitstart				=	$_CB_framework->getUserStateFromRequest( $prefix . 'limitstart{com_comprofiler}', $prefix . 'limitstart' );
		$search					=	$_CB_framework->getUserStateFromRequest( $prefix . 'search{com_comprofiler}', $prefix . 'search' );
		$where					=	null;

		if ( $search && $this->params->get( 'groups_file_search', 1 ) ) {
			$where				.=	"\n AND ( f." . $_CB_database->NameQuote( 'title' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR f." . $_CB_database->NameQuote( 'filename' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false )
								.	" OR f." . $_CB_database->NameQuote( 'description' ) . " LIKE " . $_CB_database->Quote( '%' . $_CB_database->getEscaped( $search, true ) . '%', false ) . " )";
		}

		$searching				=	( $where ? true : false );

		$query					=	'SELECT COUNT(*)'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_file' ) . " AS f"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = f.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE f." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( f." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR f.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	$where;
		$_CB_database->setQuery( $query );
		$total					=	(int) $_CB_database->loadResult();

		if ( ( ! $total ) && ( ! $searching ) && ( ! CBGroupJive::canCreateGroupContent( $user, $group, 'file' ) ) ) {
			return null;
		}

		$pageNav				=	new \cbPageNav( $total, $limitstart, $limit );

		$pageNav->setInputNamePrefix( $prefix );

		switch( (int) $this->params->get( 'groups_file_orderby', 2 ) ) {
			case 1:
				$orderBy		=	'f.' . $_CB_database->NameQuote( 'date' ) . ' ASC';
				break;
			case 3:
				$orderBy		=	'f.' . $_CB_database->NameQuote( 'filename' ) . ' ASC';
				break;
			case 4:
				$orderBy		=	'f.' . $_CB_database->NameQuote( 'filename' ) . ' DESC';
				break;
			case 2:
			default:
				$orderBy		=	'f.' . $_CB_database->NameQuote( 'date' ) . ' DESC';
				break;
		}

		$query					=	'SELECT f.*'
								.	"\n FROM " . $_CB_database->NameQuote( '#__groupjive_plugin_file' ) . " AS f"
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS cb"
								.	' ON cb.' . $_CB_database->NameQuote( 'id' ) . ' = f.' . $_CB_database->NameQuote( 'user_id' )
								.	"\n LEFT JOIN " . $_CB_database->NameQuote( '#__users' ) . " AS j"
								.	' ON j.' . $_CB_database->NameQuote( 'id' ) . ' = cb.' . $_CB_database->NameQuote( 'id' )
								.	"\n WHERE f." . $_CB_database->NameQuote( 'group' ) . " = " . (int) $group->get( 'id' )
								.	"\n AND cb." . $_CB_database->NameQuote( 'approved' ) . " = 1"
								.	"\n AND cb." . $_CB_database->NameQuote( 'confirmed' ) . " = 1"
								.	"\n AND j." . $_CB_database->NameQuote( 'block' ) . " = 0";

		if ( ! $canModerate ) {
			$query				.=	"\n AND ( f." . $_CB_database->NameQuote( 'user_id' ) . " = " . (int) $user->get( 'id' )
								.		' OR f.' . $_CB_database->NameQuote( 'published' ) . ' = 1 )';
		}

		$query					.=	$where
								.	"\n ORDER BY " . $orderBy;
		if ( $this->params->get( 'groups_file_paging', 1 ) ) {
			$_CB_database->setQuery( $query, $pageNav->limitstart, $pageNav->limit );
		} else {
			$_CB_database->setQuery( $query );
		}
		$rows					=	$_CB_database->loadObjectList( null, '\CB\Plugin\GroupJiveFile\Table\FileTable', array( $_CB_database ) );

		$input					=	array();

		$input['search']		=	'<input type="text" name="' . htmlspecialchars( $prefix ) . 'search" value="' . htmlspecialchars( $search ) . '" onchange="document.gjGroupFileForm.submit();" placeholder="' . htmlspecialchars( CBTxt::T( 'Search Files...' ) ) . '" class="form-control" />';

		CBGroupJiveFile::getFile( $rows );
		CBGroupJive::preFetchUsers( $rows );

		$group->set( '_files', $pageNav->total );

		return array(	'id'		=>	'file',
						'title'		=>	CBTxt::T( 'Files' ),
						'content'	=>	\HTML_groupjiveFile::showFiles( $rows, $pageNav, $searching, $input, $counters, $group, $user, $this )
					);
	}
}