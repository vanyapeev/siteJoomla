<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2004-2018 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Registry\GetterInterface;
use CB\Database\Table\TabTable;
use CB\Database\Table\UserTable;
use CB\Plugin\AutoActions\Table\AutoActionTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbautoactions extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_CB_framework, $_CB_database;

		$action										=	$this->input( 'action', null, GetterInterface::STRING );
		$token										=	$this->input( 'token', null, GetterInterface::STRING );
		$userIds									=	$this->input( 'users', null, GetterInterface::STRING );
		$actionIds									=	$this->input( 'actions', null, GetterInterface::STRING );
		$output										=	( $this->input( 'format', null, GetterInterface::STRING ) != 'raw' );
		$user										=	CBuser::getMyUserDataInstance();
		$return										=	null;

		if ( ! is_array( $userIds ) ) {
			if ( $userIds ) {
				$userIds							=	explode( ',', $userIds );
			} else {
				$userIds							=	array();
			}
		}

		if ( ! is_array( $actionIds ) ) {
			if ( $actionIds ) {
				$actionIds							=	explode( ',', $actionIds );
			} else {
				$actionIds							=	array();
			}
		}

		if ( ( $token == md5( $_CB_framework->getCfg( 'secret' ) ) ) || ( ( $action == 'action' ) && $actionIds ) ) {
			if ( $action == 'general' ) {
				$query								=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
													.	"\n AND " . $_CB_database->NameQuote( 'trigger' ) . " LIKE " . $_CB_database->Quote( '%internalGeneral%', false )
													.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' ) . " ASC";
				$_CB_database->setQuery( $query );
				$rows								=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AutoActions\Table\AutoActionTable', array( $_CB_database ) );

				/** @var $rows AutoActionTable[] */
				foreach ( $rows as $row ) {
					$variables						=	array( 'trigger' => 'internalGeneral', 'var1' => $user );

					$return							.=	$row->run( $variables );
				}
			} elseif ( $action == 'users' ) {
				$query								=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
													.	"\n AND " . $_CB_database->NameQuote( 'trigger' ) . " LIKE " . $_CB_database->Quote( '%internalUsers%', false )
													.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' ) . " ASC";
				$_CB_database->setQuery( $query );
				$rows								=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AutoActions\Table\AutoActionTable', array( $_CB_database ) );

				/** @var $rows AutoActionTable[] */
				if ( $rows ) {
					$query							=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
													.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . ' AS u'
													.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' );
					$_CB_database->setQuery( $query );
					$users							=	$_CB_database->loadObjectList( null, '\CB\Database\Table\UserTable', array( $_CB_database ) );

					if ( $users ) {
						foreach ( $users as $u ) {
							foreach ( $rows as $row ) {
								$variables			=	array( 'trigger' => 'internalUsers', 'var1' => $u );

								$return				.=	$row->run( $variables );
							}
						}
					}
				}
			} elseif ( $action == 'action' ) {
				$query								=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_autoactions' )
													.	"\n WHERE " . $_CB_database->NameQuote( 'published' ) . " = 1"
													.	"\n AND " . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $actionIds )
													.	"\n AND " . $_CB_database->NameQuote( 'trigger' ) . " = " . $_CB_database->Quote( '' )
													.	"\n ORDER BY " . $_CB_database->NameQuote( 'ordering' ) . " ASC";
				$_CB_database->setQuery( $query );
				$rows								=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\AutoActions\Table\AutoActionTable', array( $_CB_database ) );

				/** @var $rows AutoActionTable[] */
				if ( $rows ) {
					if ( $userIds ) {
						$query						=	'SELECT *'
													.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' ) . " AS c"
													.	"\n INNER JOIN " . $_CB_database->NameQuote( '#__users' ) . ' AS u'
													.	' ON u.' . $_CB_database->NameQuote( 'id' ) . ' = c.' . $_CB_database->NameQuote( 'id' )
													.	"\n WHERE c." . $_CB_database->NameQuote( 'id' ) . " IN " . $_CB_database->safeArrayOfIntegers( $userIds );
						$_CB_database->setQuery( $query );
						$users						=	$_CB_database->loadObjectList( null, '\CB\Database\Table\UserTable', array( $_CB_database ) );

						if ( $users ) {
							foreach ( $users as $u ) {
								foreach ( $rows as $row ) {
									$variables	=	array( 'trigger' => 'internalAction', 'var1' => $u );

									$return		.=	$row->run( $variables );
								}
							}
						}
					} else {
						foreach ( $rows as $row ) {
							$variables			=	array( 'trigger' => 'internalAction', 'var1' => $user );

							$return				.=	$row->run( $variables );
						}
					}
				}
			} else {
				if ( ! $output ) {
					header( 'HTTP/1.0 405 Method Not Allowed' );
					exit();
				} else {
					cbNotAuth();
				}
			}
		} else {
			if ( ! $output ) {
				header( 'HTTP/1.0 403 Forbidden' );
				exit();
			} else {
				cbNotAuth();
			}
		}

		echo $return;
	}
}