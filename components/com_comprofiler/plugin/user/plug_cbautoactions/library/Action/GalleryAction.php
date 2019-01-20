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
use CB\Plugin\Gallery\Table\ItemTable;
use CB\Plugin\Gallery\Table\FolderTable;
use CB\Plugin\Gallery\Gallery;
use CB\Plugin\Gallery\CBGallery;
use CBLib\Registry\Registry;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class GalleryAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null|string
	 */
	public function execute( $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NOT_INSTALLED', ':: Action [action] :: CB Gallery is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$return								=	null;

		foreach ( $this->autoaction()->params()->subTree( 'gallery' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner							=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner						=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner						=	(int) $this->string( $user, $owner );
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionUser					=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionUser					=	$user;
			}

			$mode							=	$row->get( 'mode', null, GetterInterface::STRING );
			$asset							=	$this->string( $actionUser, $row->get( 'asset', null, GetterInterface::STRING ) );
			$value							=	$this->string( $actionUser, $row->get( 'value', null, GetterInterface::STRING ) );

			if ( $mode == 'gallery' ) {
				$location					=	$this->string( $actionUser, $row->get( 'location', null, GetterInterface::STRING ), false );

				if ( ! $location ) {
					$location				=	'current';
				}

				$gallery					=	new Gallery( $asset, $actionUser );

				$gallery->set( 'location', $location );
				$gallery->set( 'moderators', explode( ',', $this->string( $actionUser, $row->get( 'moderators', null, GetterInterface::STRING ) ) ) );
				$gallery->set( 'notify', explode( ',', $this->string( $actionUser, $row->get( 'notify', null, GetterInterface::STRING ) ) ) );
				$gallery->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

				$gallery->parse( $row->subTree( 'gallery_gallery' ), 'gallery_' );

				if ( ( ! $gallery->folders( 'count' ) ) && ( ! $gallery->items( 'count' ) ) && ( ! CBGallery::canCreateFolders( $gallery ) ) && ( ! CBGallery::canCreateItems( 'all', 'both', $gallery ) ) ) {
					continue;
				}

				$return						.=	$gallery->gallery();
			} elseif ( $row->get( 'method', 'create', GetterInterface::STRING ) == 'delete' ) {
				switch ( $mode ) {
					case 'folder':
						$table				=	'#__comprofiler_plugin_gallery_folders';
						$class				=	'\CB\Plugin\Gallery\Table\FolderTable';
						break;
					case 'item':
					default:
						$table				=	'#__comprofiler_plugin_gallery_items';
						$class				=	'\CB\Plugin\Gallery\Table\ItemTable';
						break;
				}

				if ( ! $asset ) {
					$asset					=	'profile.' . $actionUser->get( 'id', 0, GetterInterface::INT );
				}

				$where						=	array();

				switch ( $row->get( 'delete_by', 'asset', GetterInterface::STRING ) ) {
					case 'link':
						if ( ! $value ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_VALUE', ':: Action [action] :: CB Gallery skipped due to missing link', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$where[]			=	$_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $value );
						break;
					case 'asset':
						$where[]			=	$_CB_database->NameQuote( 'asset' ) . ( strpos( $asset, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $asset );
						break;
					case 'owner':
						if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_OWNER', ':: Action [action] :: CB Gallery skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$where[]			=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
						break;
					case 'asset_link':
						if ( ! $value ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_VALUE', ':: Action [action] :: CB Gallery skipped due to missing link', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$where[]			=	$_CB_database->NameQuote( 'asset' ) . ( strpos( $asset, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $asset );
						$where[]			=	$_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $value );
						break;
					case 'asset_owner':
						if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_OWNER', ':: Action [action] :: CB Gallery skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$where[]			=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
						$where[]			=	$_CB_database->NameQuote( 'asset' ) . ( strpos( $asset, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $asset );
						break;
					case 'link_owner':
						if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_OWNER', ':: Action [action] :: CB Gallery skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						if ( ! $value ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_VALUE', ':: Action [action] :: CB Gallery skipped due to missing link', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$where[]			=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
						$where[]			=	$_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $value );
						break;
					case 'asset_link_owner':
					default:
						if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_OWNER', ':: Action [action] :: CB Gallery skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						if ( ! $value ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_VALUE', ':: Action [action] :: CB Gallery skipped due to missing link', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
							continue;
						}

						$where[]			=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
						$where[]			=	$_CB_database->NameQuote( 'asset' ) . ( strpos( $asset, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $asset );
						$where[]			=	$_CB_database->NameQuote( 'value' ) . ' = ' . $_CB_database->Quote( $value );
						break;
				}

				$query						=	'SELECT *'
											.	"\n FROM " . $_CB_database->NameQuote( $table )
											.	"\n WHERE " . implode( "\n AND ", $where );
				$_CB_database->setQuery( $query );
				$objects					=	$_CB_database->loadObjectList( null, $class, array( $_CB_database ) );

				/** @var FolderTable[]|ItemTable[] $objects */
				foreach ( $objects as $object ) {
					$object->delete();
				}
			} else {
				if ( ! $actionUser->get( 'id', 0, GetterInterface::INT ) ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_OWNER', ':: Action [action] :: CB Gallery skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				if ( ! $asset ) {
					$asset					=	'profile.' . $actionUser->get( 'id', 0, GetterInterface::INT );
				}

				$title						=	$this->string( $actionUser, $row->get( 'title', null, GetterInterface::STRING ) );
				$description				=	$this->string( $actionUser, $row->get( 'description', null, GetterInterface::STRING ) );

				switch ( $mode ) {
					case 'folder':
						$object				=	new FolderTable();
						break;
					case 'item':
					default:
						$object				=	new ItemTable();
						break;
				}

				switch ( $row->get( 'create_by', 'asset', GetterInterface::STRING ) ) {
					case 'link':
						$object->load( array( 'value' => $value ) );
						break;
					case 'asset':
						$object->load( array( 'asset' => $asset ) );
						break;
					case 'owner':
						$object->load( array( 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ) ) );
						break;
					case 'asset_link':
						$object->load( array( 'asset' => $asset, 'value' => $value ) );
						break;
					case 'asset_owner':
						$object->load( array( 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );
						break;
					case 'link_owner':
						$object->load( array( 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ), 'value' => $value ) );
						break;
					case 'asset_link_owner':
						$object->load( array( 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset, 'value' => $value ) );
						break;
				}

				$object->set( 'user_id', $actionUser->get( 'id', 0, GetterInterface::INT ) );

				if ( $title ) {
					$object->set( 'title', $title );
				}

				if ( $description ) {
					$object->set( 'description', $description );
				}

				$published					=	$row->get( 'published', null, GetterInterface::STRING );

				if ( ( $published === null ) || ( $published === '' ) ) {
					$object->set( 'published', 1 );
				} else {
					$object->set( 'published', ( (int) $this->string( $actionUser, $published ) ? 1 : 0 ) );
				}

				if ( $mode == 'item' ) {
					if ( ! $value ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_NO_VALUE', ':: Action [action] :: CB Gallery skipped due to missing value', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					$object->set( '_input', new Registry( array( 'value' => $value ) ) );
					$object->set( 'value', $value );
				}

				if ( ! $object->store() ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_GALLERY_FAILED', ':: Action [action] :: CB Gallery failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $object->getError() ) ) );
				}
			}
		}

		return $return;
	}

	/**
	 * @return bool
	 */
	public function installed()
	{
		global $_PLUGINS;

		$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbgallery' );

		if ( ! $plugin ) {
			return false;
		}

		$pluginVersion		=	str_replace( '+build.', '+', $_PLUGINS->getPluginVersion( $plugin, true ) );

		if ( version_compare( $pluginVersion, '2.0.0', '>=' ) && version_compare( $pluginVersion, '3.0.0', '<' ) ) {
			return true;
		}

		return false;
	}
}