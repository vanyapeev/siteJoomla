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
use CB\Plugin\Privacy\Table\PrivacyTable;
use CB\Plugin\Privacy\Privacy;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class PrivacyAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null|string|bool
	 */
	public function execute( $user )
	{
		global $_CB_database;

		if ( ! $this->installed() ) {
			$this->error( CBTxt::T( 'AUTO_ACTION_PRIVACY_NOT_INSTALLED', ':: Action [action] :: CB Privacy is not installed', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
			return null;
		}

		$return								=	null;

		foreach ( $this->autoaction()->params()->subTree( 'privacy' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner							=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner						=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner						=	(int) $this->string( $user, $owner );
			}

			if ( ! $owner ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PRIVACY_NO_OWNER', ':: Action [action] :: CB Privacy skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionUser					=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionUser					=	$user;
			}

			$asset							=	$this->string( $actionUser, $row->get( 'asset', null, GetterInterface::STRING ) );

			if ( ! $asset ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_PRIVACY_NO_ASSET', ':: Action [action] :: CB Privacy skipped due to missing asset', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			$mode							=	$row->get( 'mode', null, GetterInterface::STRING );

			if ( $mode == 'privacy' ) {
				$method						=	$row->get( 'method', 'display', GetterInterface::STRING );

				$privacy					=	new Privacy( $asset, $actionUser );

				$privacy->set( 'autoaction', $this->autoaction()->get( 'id', 0, GetterInterface::INT ) );

				$privacy->parse( $row->subTree( 'privacy_privacy' ), 'privacy_' );

				if ( $method == 'authorized' ) {
					$return					=	$privacy->authorized();

					if ( ! $return ) {
						$loop				=	$this->autoaction()->params()->get( 'loop', 0, GetterInterface::INT );

						if ( $loop ) {
							$variables		=	$this->variables();
							$loopKey		=	(int) $variables['loop_key'];

							if ( isset( $variables['var' . $loop][$loopKey] ) ) {
								unset( $variables['var' . $loop][$loopKey] );
							}
						}
					}
				} elseif ( $method == 'save' ) {
					$privacy->privacy( 'save' );
				} else {
					$return					.=	$privacy->privacy( 'edit' );
				}
			} else {
				$method						=	$row->get( 'method', 'create', GetterInterface::STRING );

				if ( $method == 'delete' ) {
					$where					=	array();

					switch ( $row->get( 'asset_owner', 'asset', GetterInterface::STRING ) ) {
						case 'owner':
							$where[]		=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
							break;
						case 'asset_owner':
							$where[]		=	$_CB_database->NameQuote( 'user_id' ) . ' = ' . $actionUser->get( 'id', 0, GetterInterface::INT );
							$where[]		=	$_CB_database->NameQuote( 'asset' ) . ( strpos( $asset, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $asset );
							break;
						case 'asset':
						default:
							$where[]		=	$_CB_database->NameQuote( 'asset' ) . ( strpos( $asset, '%' ) !== false ? ' LIKE ' : ' = ' ) . $_CB_database->Quote( $asset );
							break;
					}

					$query					=	'SELECT *'
											.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler_plugin_privacy' )
											.	"\n WHERE " . implode( "\n AND ", $where );
					$_CB_database->setQuery( $query );
					$rules					=	$_CB_database->loadObjectList( 'id', '\CB\Plugin\Privacy\Table\PrivacyTable', array( $_CB_database ) );

					/** @var PrivacyTable[] $rules */
					foreach ( $rules as $rule ) {
						$rule->delete();
					}
				} else {
					$rules					=	explode( '|*|', $row->get( 'rules', null, GetterInterface::STRING ) );

					if ( ! $rules ) {
						$this->error( CBTxt::T( 'AUTO_ACTION_PRIVACY_NO_RULES', ':: Action [action] :: CB Privacy skipped due to missing rules', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
						continue;
					}

					/** @var array $rules */
					foreach ( $rules as $rule ) {
						$privacy			=	new PrivacyTable();

						$privacy->load( array( 'user_id' => $actionUser->get( 'id', 0, GetterInterface::INT ), 'asset' => $asset ) );

						$privacy->set( 'user_id', $actionUser->get( 'id', 0, GetterInterface::INT ) );
						$privacy->set( 'asset', $asset );
						$privacy->set( 'rule', $rule );

						if ( ! $privacy->store() ) {
							$this->error( CBTxt::T( 'AUTO_ACTION_PRIVACY_FAILED', ':: Action [action] :: CB Privacy failed to save. Error: [error]', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ), '[error]' => $privacy->getError() ) ) );
						}
					}
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

		$plugin				=	$_PLUGINS->getLoadedPlugin( 'user', 'cbprivacy' );

		if ( ! $plugin ) {
			return false;
		}

		$pluginVersion		=	str_replace( '+build.', '+', $_PLUGINS->getPluginVersion( $plugin, true ) );

		if ( version_compare( $pluginVersion, '5.0.0', '>=' ) && version_compare( $pluginVersion, '6.0.0', '<' ) ) {
			return true;
		}

		return false;
	}
}