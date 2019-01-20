<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\Registry;
use CBLib\Language\CBTxt;
use CBLib\Registry\ParamsInterface;
use CB\Plugin\Connect\CBConnect;
use CB\Plugin\Connect\Connect;
use CB\Plugin\Connect\Profile;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbconnect extends cbPluginHandler
{
	/** @var Connect  */
	private $connect	=	null;

	/**
	 * @param TabTable  $tab
	 * @param UserTable $user
	 * @param int       $ui
	 * @param array     $postdata
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		global $_PLUGINS, $_CB_database;

		$_PLUGINS->loadPluginGroup( 'user' );

		$providerId					=	$this->input( 'provider', null, GetterInterface::STRING );

		// HybridAuth B/C:
		if ( ! $providerId ) {
			$providerId				=	$this->input( 'hauth_start', null, GetterInterface::STRING );

			if ( ! $providerId ) {
				$providerId			=	$this->input( 'hauth_done', null, GetterInterface::STRING );
			}
		}

		// Callback doesn't allow parameters so lets try to find it in state
		if ( ! $providerId ) {
			$providerState			=	$this->input( 'state', null, GetterInterface::STRING );

			if ( $providerState ) {
				$stateParts			=	explode( '.', $providerState );

				if ( count( $stateParts ) === 2 ) {
					$providerId		=	$stateParts[0];
				}
			}
		}

		$providerId					=	trim( strtolower( $providerId ) );

		if ( ! $providerId ) {
			CBConnect::returnRedirect( null, 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$this->connect				=	new Connect( $providerId );

		if ( ! $this->connect->provider() ) {
			if ( $this->connect->id ) {
				// CBTxt::T( 'PROVIDER_NOT_AVAILABLE', '[provider] is not available.', array( '[provider]' => $providerId ) )
				CBConnect::returnRedirect( null, 'index.php', CBTxt::T( strtoupper( $this->connect->id ) . '_NOT_AVAILABLE PROVIDER_NOT_AVAILABLE', '[provider] is not available.', array( '[provider]' => $providerId ) ), 'error' );
			} else {
				CBConnect::returnRedirect( null, 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		if ( CBConnect::getReturn( true ) ) {
			$this->connect->provider()->session()->set( $this->connect->id . '.return', CBConnect::getReturn( true ) );
		}

		$error						=	$this->input( 'error_description', null, GetterInterface::STRING );

		if ( ! $error ) {
			$error					=	$this->input( 'error', null, GetterInterface::STRING );
		}

		if ( $error ) {
			// CBTxt::T( 'PROVIDER_FAILED_TO_AUTHENTICATE', '[provider] failed to authenticate. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $error ) )
			CBConnect::returnRedirect( $this->connect->id, 'index.php', CBTxt::T( strtoupper( $this->connect->id ) . '_FAILED_TO_AUTHENTICATE PROVIDER_FAILED_TO_AUTHENTICATE', '[provider] failed to authenticate. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $error ) ), 'error' );
		}

		try {
			$this->connect->provider()->authenticate();

			if ( $this->connect->provider()->authorized() ) {
				$profile			=	$this->connect->provider()->profile();

				if ( ! $profile->get( 'id', null, GetterInterface::STRING ) ) {
					// CBTxt::T( 'PROVIDER_PROFILE_MISSING', '[provider] profile could not be found.', array( '[provider]' => $this->connect->name() ) )
					throw new Exception( CBTxt::T( strtoupper( $this->connect->id ) . '_PROFILE_MISSING PROVIDER_PROFILE_MISSING', '[provider] profile could not be found.', array( '[provider]' => $this->connect->name() ) ) );
				}

				$query				=	'SELECT ' . $_CB_database->NameQuote( 'id' )
									.	"\n FROM " . $_CB_database->NameQuote( '#__comprofiler' )
									.	"\n WHERE " . $_CB_database->NameQuote( $this->connect->field() ) . " = " . $_CB_database->Quote( $profile->get( 'id', null, GetterInterface::STRING ) );
				$_CB_database->setQuery( $query );
				$userId				=	(int) $_CB_database->loadResult();

				$myUser				=	CBuser::getMyUserDataInstance();
				$user				=	CBuser::getUserDataInstance( $userId );

				if ( $myUser->get( 'id', 0, GetterInterface::INT ) ) {
					if ( ( ! $this->connect->params()->get( 'link', true, GetterInterface::BOOLEAN ) ) && ( ! $myUser->get( $this->connect->field(), null, GetterInterface::STRING ) ) ) {
						// CBTxt::T( 'LINKING_FOR_PROVIDER_NOT_PERMITTED', 'Linking for [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) )
						throw new Exception( CBTxt::T( 'LINKING_FOR_' . strtoupper( $this->connect->id ) . '_NOT_PERMITTED LINKING_FOR_PROVIDER_NOT_PERMITTED', 'Linking for [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) ) );
					}

					if ( ! $myUser->get( $this->connect->field(), null, GetterInterface::STRING ) ) {
						if ( $user->get( 'id', 0, GetterInterface::INT ) && ( $myUser->get( 'id', 0, GetterInterface::INT ) != $user->get( 'id', 0, GetterInterface::INT ) ) ) {
							// CBTxt::T( 'PROVIDER_ALREADY_LINKED', '[provider] account already linked to another user.', array( '[provider]' => $this->connect->name() ) )
							throw new Exception( CBTxt::T( strtoupper( $this->connect->id ) . '_ALREADY_LINKED PROVIDER_ALREADY_LINKED', '[provider] account already linked to another user.', array( '[provider]' => $this->connect->name() ) ) );
						}

						if ( ! $myUser->storeDatabaseValue( $this->connect->field(), $profile->get( 'id', 0, GetterInterface::INT ) ) ) {
							// CBTxt::T( 'PROVIDER_FAILED_TO_LINK', '[provider] account failed to link. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $myUser->getError() ) )
							throw new Exception( CBTxt::T( strtoupper( $this->connect->id ) . '_FAILED_TO_LINK PROVIDER_FAILED_TO_LINK', '[provider] account failed to link. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $myUser->getError() ) ) );
						}

						$resync		=	$this->connect->params()->get( 'link_resynchronize', 0, GetterInterface::INT );

						if ( $resync ) {
							$this->update( $user, $profile, ( $resync === 1 ) );
						}

						// CBTxt::T( 'PROVIDER_LINKED_SUCCESSFULLY', '[provider] account linked successfully!', array( '[provider]' => $this->connect->name() ) )
						CBConnect::returnRedirect( $this->connect->id, 'index.php', CBTxt::T( strtoupper( $this->connect->id ) . '_LINKED_SUCCESSFULLY PROVIDER_LINKED_SUCCESSFULLY', '[provider] account linked successfully!', array( '[provider]' => $this->connect->name() ) ) );
					}

					// CBTxt::T( 'ALREADY_LINKED_TO_PROVIDER', 'You are already linked to a [provider] account.', array( '[provider]' => $this->connect->name() ) )
					throw new Exception( CBTxt::T( 'ALREADY_LINKED_TO_' . strtoupper( $this->connect->id ) . ' ALREADY_LINKED_TO_PROVIDER', 'You are already linked to a [provider] account.', array( '[provider]' => $this->connect->name() ) ) );
				} else {
					if ( ( ! $this->connect->params()->get( 'register', true, GetterInterface::BOOLEAN ) ) && ( ! $user->get( 'id', 0, GetterInterface::INT ) ) ) {
						// CBTxt::T( 'SIGN_UP_WITH_PROVIDER_NOT_PERMITTED', 'Sign up with [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) )
						throw new Exception( CBTxt::T( 'SIGN_UP_WITH_' . strtoupper( $this->connect->id ) . '_NOT_PERMITTED SIGN_UP_WITH_PROVIDER_NOT_PERMITTED', 'Sign up with [provider] is not permitted.', array( '[provider]' => $this->connect->name() ) ) );
					}

					$login			=	true;
					$resync			=	$this->connect->params()->get( 'login_resynchronize', 0, GetterInterface::INT );

					if ( ! $user->get( 'id', 0, GetterInterface::INT ) ) {
						$login		=	$this->register( $user, $profile );
					} elseif ( $resync ) {
						$this->update( $user, $profile, ( $resync === 1 ) );
					}

					if ( $login ) {
						$this->login( $user );
					}
				}
			}
		} catch ( Exception $e ) {
			CBConnect::returnRedirect( $this->connect->id, 'index.php', $e->getMessage(), 'error' );
		}
	}

	/**
	 * Registers a new user
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return bool
	 * @throws Exception
	 */
	private function register( &$user, $profile )
	{
		global $_CB_framework, $_PLUGINS, $ueConfig;

		$mode						=	$this->connect->params()->get( 'mode', 1, GetterInterface::INT );
		$approve					=	$this->connect->params()->get( 'approve', 0, GetterInterface::INT );
		$confirm					=	$this->connect->params()->get( 'confirm', 0, GetterInterface::INT );
		$usergroup					=	$this->connect->params()->get( 'usergroup', null, GetterInterface::STRING );
		$approval					=	( $approve == 2 ? $ueConfig['reg_admin_approval'] : $approve );
		$confirmation				=	( $confirm == 2 ? $ueConfig['reg_confirmation'] : $confirm );
		$username					=	$this->username( $user, $profile );
		$dummyUser					=	new UserTable();

		// Username fallback to Username:
		if ( $profile->get( 'username', null, GetterInterface::STRING ) && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) ) ) ) {
			$username				=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( $profile->get( 'username', null, GetterInterface::STRING ) ) );
		}

		// Username fallback to Name:
		$name						=	$this->name( $profile );

		if ( $name && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) ) ) ) {
			$username				=	preg_replace( '/[<>\\\\"%();&\']+/', '', $name );
		}

		// Username fallback to ID:
		if ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) ) ) {
			$username				=	(string) $profile->get( 'id', null, GetterInterface::STRING );
		}

		$password					=	null;

		if ( $mode == 2 ) {
			$user->set( 'email', $profile->get( 'email', null, GetterInterface::STRING ) );
		} else {
			$linkUrl				=	base64_encode( $_CB_framework->pluginClassUrl( 'cbconnect', false, array( 'provider' => $this->connect->id ) ) );

			if ( $dummyUser->loadByUsername( $username ) ) {
				if ( ! $this->connect->params()->get( 'link', true, GetterInterface::BOOLEAN ) ) {
					throw new Exception( CBTxt::T( 'UE_USERNAME_NOT_AVAILABLE', "The username '[username]' is already in use.", array( '[username]' =>  htmlspecialchars( $username ) ) ) );
				} else {
					cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => $linkUrl ) ), CBTxt::T( 'USERNAME_IN_USE_LOGIN_LINK', "The username '[username]' is already in use. Please login to link your account.", array( '[username]' =>  htmlspecialchars( $username ) ) ), 'message' );
				}
			}

			if ( ! $this->email( $user, $profile ) ) {
				return false;
			}

			if ( $dummyUser->loadByEmail( $user->get( 'email', null, GetterInterface::STRING ) ) ) {
				if ( ! $this->connect->params()->get( 'link', true, GetterInterface::BOOLEAN ) ) {
					throw new Exception( CBTxt::T( 'UE_EMAIL_NOT_AVAILABLE', "The email '[email]' is already in use.", array( '[email]' =>  htmlspecialchars( $user->get( 'email', null, GetterInterface::STRING ) ) ) ) );
				} else {
					cbRedirect( $_CB_framework->viewUrl( 'login', false, array( 'return' => $linkUrl ) ), CBTxt::T( 'EMAIL_IN_USE_LOGIN_LINK', "The email '[email]' is already in use. Please login to link your account.", array( '[email]' =>  htmlspecialchars( $user->get( 'email', null, GetterInterface::STRING ) ) ) ), 'message' );
				}
			}

			$this->image( $user, 'avatar', $profile->get( 'avatar', null, GetterInterface::STRING ) );
			$this->image( $user, 'canvas', $profile->get( 'canvas', null, GetterInterface::STRING ) );

			if ( ! $usergroup ) {
				$gids				=	array( (int) $_CB_framework->getCfg( 'new_usertype' ) );
			} else {
				$gids				=	cbToArrayOfInt( explode( '|*|', $usergroup ) );
			}

			$password				=	$user->getRandomPassword();

			$user->set( 'gids', $gids );
			$user->set( 'sendEmail', 0 );
			$user->set( 'registerDate', Application::Database()->getUtcDateTime() );
			$user->set( 'password', $user->hashAndSaltPassword( $password ) );
			$user->set( 'registeripaddr', cbGetIPlist() );

			if ( $approval == 0 ) {
				$user->set( 'approved', 1 );
			} else {
				$user->set( 'approved', 0 );
			}

			if ( $confirmation == 0 ) {
				$user->set( 'confirmed', 1 );
			} else {
				$user->set( 'confirmed', 0 );
			}

			if ( ( $user->get( 'confirmed', 0, GetterInterface::INT ) == 1 ) && ( $user->get( 'approved', 0, GetterInterface::INT ) == 1 ) ) {
				$user->set( 'block', 0 );
			} else {
				$user->set( 'block', 1 );
			}
		}

		if ( $name ) {
			$user->set( 'name', $name );
		} else {
			$user->set( 'name', $username );
		}

		switch ( $ueConfig['name_style'] ) {
			case 2:
				$lastName			=	strrpos( $user->get( 'name', null, GetterInterface::STRING ), ' ' );

				if ( $lastName !== false ) {
					$user->set( 'firstname', substr( $user->get( 'name', null, GetterInterface::STRING ), 0, $lastName ) );
					$user->set( 'lastname', substr( $user->get( 'name', null, GetterInterface::STRING ), ( $lastName + 1 ) ) );
				} else {
					$user->set( 'firstname', '' );
					$user->set( 'lastname', $user->get( 'name', null, GetterInterface::STRING ) );
				}
				break;
			case 3:
				$middleName			=	strpos( $user->get( 'name', null, GetterInterface::STRING ), ' ' );
				$lastName			=	strrpos( $user->get( 'name', null, GetterInterface::STRING ), ' ' );

				if ( $lastName !== false ) {
					$user->set( 'firstname', substr( $user->get( 'name', null, GetterInterface::STRING ), 0, $middleName ) );
					$user->set( 'lastname', substr( $user->get( 'name', null, GetterInterface::STRING ), ( $lastName + 1 ) ) );

					if ( $middleName !== $lastName ) {
						$user->set( 'middlename', substr( $user->get( 'name', null, GetterInterface::STRING ), ( $middleName + 1 ), ( $lastName - $middleName - 1 ) ) );
					} else {
						$user->set( 'middlename', '' );
					}
				} else {
					$user->set( 'firstname', '' );
					$user->set( 'lastname', $user->get( 'name', null, GetterInterface::STRING ) );
				}
				break;
		}

		$user->set( 'username', $username );
		$user->set( $this->connect->field(), $profile->get( 'id', null, GetterInterface::STRING ) );

		$this->fields( $user, $profile );

		if ( $mode == 2 ) {
			$cbTabs					=	new cbTabs( 0, 1, null, false );
			$fields					=	$cbTabs->_getTabFieldsDb( null, $user, 'register', null, true );

			if ( is_array( $fields ) ) {
				foreach ( $fields as $field ) {
					$k				=	$field->get( 'name', null, GetterInterface::STRING );

					if ( $user->get( $k, null, GetterInterface::STRING ) === null ) {
						$user->set( $k, $field->get( 'default', null, GetterInterface::STRING ) );
					}
				}
			}

			$data					=	array();

			foreach ( $user as $k => $v ) {
				$data[$k]			=	$v;
			}

			$emailPass				=	( isset( $ueConfig['emailpass'] ) ? $ueConfig['emailpass'] : '0' );
			$regErrorMSG			=	null;

			if ( ( ( $_CB_framework->getCfg( 'allowUserRegistration' ) == '0' ) && ( ( ! isset( $ueConfig['reg_admin_allowcbregistration'] ) ) || $ueConfig['reg_admin_allowcbregistration'] != '1' ) ) ) {
				$msg				=	CBTxt::T( 'UE_NOT_AUTHORIZED', 'You are not authorized to view this page!' );
			} else {
				$msg				=	null;
			}

			$_PLUGINS->trigger( 'onBeforeRegisterFormRequest', array( &$msg, $emailPass, &$regErrorMSG ) );

			if ( $msg ) {
				$_CB_framework->enqueueMessage( $msg, 'error' );
				return false;
			}

			$fieldsQuery			=	null;
			$results				=	$_PLUGINS->trigger( 'onBeforeRegisterForm', array( 'com_comprofiler', $emailPass, &$regErrorMSG, $fieldsQuery ) );

			if ( $_PLUGINS->is_errors() ) {
				$_CB_framework->enqueueMessage( $_PLUGINS->getErrorMSG( '<br />' ), 'error' );
				return false;
			}

			if ( implode( '', $results ) != '' ) {
				$return				=		'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
									.			'<div>' . implode( '</div><div>', $results ) . '</div>'
									.		'</div>';

				echo $return;
				return false;
			}

			// CBTxt::T( 'PROVIDER_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) )
			$_CB_framework->enqueueMessage( CBTxt::T( strtoupper( $this->connect->id ) . '_SIGN_UP_INCOMPLETE PROVIDER_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) ) );

			HTML_comprofiler::registerForm( 'com_comprofiler', $emailPass, $user, $data, $regErrorMSG );
			return false;
		} else {
			$_PLUGINS->trigger( 'onBeforeUserRegistration', array( &$user, &$user ) );

			if ( $user->store() ) {
				if ( $user->get( 'confirmed', 0, GetterInterface::INT ) == 0 ) {
					$user->store();
				}

				$passwordCache		=	$user->get( 'password', null, GetterInterface::STRING );

				$user->set( 'password', $password );

				$messagesToUser		=	activateUser( $user, 1, 'UserRegistration' );

				$_PLUGINS->trigger( 'onAfterUserRegistration', array( &$user, &$user, true ) );

				$user->set( 'password', $passwordCache );

				if ( $user->get( 'block', 0, GetterInterface::INT ) == 1 ) {
					$return			=		'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
									.			'<div>' . implode( '</div><div>', $messagesToUser ) . '</div>'
									.		'</div>';

					echo $return;

					return false;
				} else {
					return true;
				}
			}

			// CBTxt::T( 'SIGN_UP_WITH_PROVIDER_FAILED', 'Sign up with [provider] failed. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $user->getError() ) )
			throw new Exception( CBTxt::T( 'SIGN_UP_WITH_' . strtoupper( $this->connect->id ) . '_FAILED SIGN_UP_WITH_PROVIDER_FAILED', 'Sign up with [provider] failed. Error: [error]', array( '[provider]' => $this->connect->name(), '[error]' => $user->getError() ) ) );
		}
	}

	/**
	 * Updates a user
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @param bool      $fullProfile
	 */
	private function update( &$user, $profile, $fullProfile = true )
	{
		global $ueConfig;

		if ( $fullProfile ) {
			$username						=	$this->username( $user, $profile );
			$dummyUser						=	new UserTable();

			// Username fallback to Username:
			if ( $profile->get( 'username', null, GetterInterface::STRING ) && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) && ( $dummyUser->get( 'id', 0, GetterInterface::INT ) != $user->get( 'id', 0, GetterInterface::INT ) ) ) ) ) {
				$username					=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( $profile->get( 'username', null, GetterInterface::STRING ) ) );
			}

			// Username fallback to Name:
			$name							=	$this->name( $profile );

			if ( $name && ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) && ( $dummyUser->get( 'id', 0, GetterInterface::INT ) != $user->get( 'id', 0, GetterInterface::INT ) ) ) ) ) {
				$username					=	preg_replace( '/[<>\\\\"%();&\']+/', '', $name );
			}

			// Username fallback to ID:
			if ( ( ! $username ) || ( $username && $dummyUser->loadByUsername( $username ) && ( $dummyUser->get( 'id', 0, GetterInterface::INT ) != $user->get( 'id', 0, GetterInterface::INT ) ) ) ) {
				$username					=	(string) $profile->get( 'id', null, GetterInterface::STRING );
			}

			// If username exists, doesn't match, and doesn't belong to another user then remap it:
			if ( $username && ( $username != $user->get( 'username', null, GetterInterface::STRING ) ) && ( ( ! $dummyUser->loadByUsername( $username ) ) || ( $dummyUser->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) ) ) {
				$user->set( 'username', $username );
			}

			// If email exists, doesn't match, and doesn't belong to another user then remap it:
			if ( $profile->get( 'email', null, GetterInterface::STRING ) && ( $profile->get( 'email', null, GetterInterface::STRING ) != $user->get( 'email', null, GetterInterface::STRING ) ) && ( ( ! $dummyUser->loadByEmail( $profile->get( 'email' ) ) ) || ( $dummyUser->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ) ) ) {
				$user->set( 'email', $profile->get( 'email', null, GetterInterface::STRING ) );
			}

			if ( $name ) {
				if ( $name != $user->get( 'name', null, GetterInterface::STRING ) ) {
					$user->set( 'name', $name );
				}
			} elseif ( $username ) {
				if ( $username != $user->get( 'name', null, GetterInterface::STRING ) ) {
					$user->set( 'name', $username );
				}
			}

			switch ( $ueConfig['name_style'] ) {
				case 2:
					$lastNamePos			=	strrpos( $user->get( 'name', null, GetterInterface::STRING ), ' ' );
					$middleName				=	'';

					if ( $lastNamePos !== false ) {
						$firstName			=	substr( $user->get( 'name', null, GetterInterface::STRING ), 0, $lastNamePos );
						$lastName			=	substr( $user->get( 'name', null, GetterInterface::STRING ), ( $lastNamePos + 1 ) );
					} else {
						$firstName			=	'';
						$lastName			=	$user->get( 'name', null, GetterInterface::STRING );
					}
					break;
				case 3:
					$middleNamePos			=	strpos( $user->get( 'name', null, GetterInterface::STRING ), ' ' );
					$lastNamePos			=	strrpos( $user->get( 'name', null, GetterInterface::STRING ), ' ' );

					if ( $lastNamePos !== false ) {
						$firstName			=	substr( $user->get( 'name', null, GetterInterface::STRING ), 0, $middleNamePos );
						$lastName			=	substr( $user->get( 'name', null, GetterInterface::STRING ), ( $lastNamePos + 1 ) );

						if ( $middleNamePos !== $lastNamePos ) {
							$middleName		=	substr( $user->get( 'name', null, GetterInterface::STRING ), ( $middleNamePos + 1 ), ( $lastNamePos - $middleNamePos - 1 ) );
						} else {
							$middleName		=	'';
						}
					} else {
						$firstName			=	'';
						$middleName			=	'';
						$lastName			=	$user->get( 'name', null, GetterInterface::STRING );
					}
					break;
				default:
					$firstName				=	'';
					$middleName				=	'';
					$lastName				=	'';
					break;
			}

			if ( $firstName != $user->get( 'firstname', null, GetterInterface::STRING ) ) {
				$user->set( 'firstname', $firstName );
			}

			if ( $middleName != $user->get( 'middlename', null, GetterInterface::STRING ) ) {
				$user->set( 'middlename', $middleName );
			}

			if ( $lastName != $user->get( 'lastname', null, GetterInterface::STRING ) ) {
				$user->set( 'lastname', $lastName );
			}

			$this->image( $user, 'avatar', $profile->get( 'avatar', null, GetterInterface::STRING ) );
			$this->image( $user, 'canvas', $profile->get( 'canvas', null, GetterInterface::STRING ) );
		}

		$this->fields( $user, $profile );

		$user->store();
	}

	/**
	 * Returns formatted name
	 *
	 * @param Profile $profile
	 * @return string
	 */
	private function name( $profile )
	{
		if ( $profile->get( 'name', null, GetterInterface::STRING ) ) {
			$name		=	trim( $profile->get( 'name', null, GetterInterface::STRING ) );
		} elseif ( $profile->get( 'firstname', null, GetterInterface::STRING ) || $profile->get( 'middlename', null, GetterInterface::STRING ) || $profile->get( 'lastname', null, GetterInterface::STRING ) ) {
			$name		=	$profile->get( 'firstname', null, GetterInterface::STRING );

			if ( $profile->get( 'middlename', null, GetterInterface::STRING ) )  {
				$name	.=	' ' . $profile->get( 'middlename', null, GetterInterface::STRING );
			}

			if ( $profile->get( 'lastname', null, GetterInterface::STRING ) )  {
				$name	.=	' ' . $profile->get( 'lastname', null, GetterInterface::STRING );
			}

			$name		=	trim( $name );
		} else {
			$name		=	null;
		}

		return $name;
	}

	/**
	 * Returns formatted username
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return string
	 */
	private function username( &$user, $profile )
	{
		$providers				=	CBConnect::getProviders();
		$usernameFormat			=	$this->connect->params()->get( 'username', null, GetterInterface::STRING );
		$username				=	null;

		if ( $usernameFormat ) {
			$extras				=	array(	'provider'				=>	$this->connect->id,
											'profile_id'			=>	$profile->get( 'id', null, GetterInterface::STRING ),
											'profile_username'		=>	$profile->get( 'username', null, GetterInterface::STRING ),
											'profile_name'			=>	$profile->get( 'name', null, GetterInterface::STRING ),
											'profile_firstname'		=>	$profile->get( 'firstname', null, GetterInterface::STRING ),
											'profile_middlename'	=>	$profile->get( 'middlename', null, GetterInterface::STRING ),
											'profile_lastname'		=>	$profile->get( 'lastname', null, GetterInterface::STRING ),
											'profile_email'			=>	$profile->get( 'email', null, GetterInterface::STRING ) );

			foreach ( $providers[$this->connect->id]['fields'] as $field ) {
				$k				=	$this->connect->id . '_' . trim( strtolower( str_replace( array( '.', '-' ), '_', $field ) ) );

				$extras[$k]		=	$profile->profile()->get( $field, null, GetterInterface::STRING );
			}

			$username			=	preg_replace( '/[<>\\\\"%();&\']+/', '', trim( cbReplaceVars( $usernameFormat, $user, true, false, $extras, false ) ) );
		}

		return $username;
	}

	/**
	 * Checks if an email address has been supplied by the provider or if email form needs to render
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 * @return bool
	 */
	private function email( &$user, $profile )
	{
		global $_CB_framework;

		$email						=	$this->input( 'email', null, GetterInterface::STRING );
		$emailVerify				=	$this->input( 'email__verify', null, GetterInterface::STRING );

		if ( $email ) {
			if ( ! cbIsValidEmail( $email ) ) {
				$_CB_framework->enqueueMessage( sprintf( CBTxt::T( 'UE_EMAIL_NOVALID', 'This is not a valid email address.' ), htmlspecialchars( $email ) ), 'error' );

				$email				=	null;
			} else {
				$field				=	new FieldTable();

				$field->load( array( 'name' => 'email' ) );

				$field->set( 'params', new Registry( $field->get( 'params', null, GetterInterface::RAW ) ) );

				if ( $field->params->get( 'fieldVerifyInput', 0, GetterInterface::INT ) && ( $email != $emailVerify ) ) {
					$_CB_framework->enqueueMessage( CBTxt::T( 'Email and verification do not match, please try again.' ), 'error' );

					$email			=	null;
				}
			}
		}

		if ( ! $email ) {
			$email					=	$profile->get( 'email', null, GetterInterface::STRING );
		}

		if ( ! $email ) {
			$regAntiSpamValues		=	cbGetRegAntiSpams();

			outputCbTemplate();
			outputCbJs();
			cbValidator::loadValidation();

			$cbUser					=	CBuser::getInstance( null );

			// CBTxt::T( 'PROVIDER_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) )
			$_CB_framework->enqueueMessage( CBTxt::T( strtoupper( $this->connect->id ) . '_SIGN_UP_INCOMPLETE PROVIDER_SIGN_UP_INCOMPLETE', 'Your [provider] sign up is incomplete. Please complete the following.', array( '[provider]' => $this->connect->name() ) ) );

			$return					=	'<form action="' . $_CB_framework->pluginClassUrl( $this->element, false, array( 'provider' => $this->connect->id ) ) . '" method="post" enctype="multipart/form-data" name="adminForm" id="cbcheckedadminForm" class="cb_form form-auto cbValidation">'
									.		'<div class="cbRegistrationTitle page-header">'
									.			'<h3>' . CBTxt::T( 'Sign up incomplete' ) . '</h3>'
									.		'</div>'
									.		$cbUser->getField( 'email', null, 'htmledit', 'div', 'register', 0, true, array( 'required' => 1, 'edit' => 1, 'registration' => 1 ) )
									.		'<div class="form-group cb_form_line clearfix">'
									.			'<div class="col-sm-offset-3 col-sm-9">'
									.				'<input type="submit" value="Sign up" class="btn btn-primary cbRegistrationSubmit" data-submit-text="Loading...">'
									.			'</div>'
									.		'</div>'
									.		cbGetSpoofInputTag( 'plugin' )
									.		cbGetRegAntiSpamInputTag( $regAntiSpamValues )
									.	'</form>';

			echo $return;

			return false;
		}

		$user->set( 'email', $email );

		return true;
	}

	/**
	 * Parses profile data for an image and uploads it
	 *
	 * @param UserTable $user
	 * @param string    $fieldName
	 * @param string    $imageUrl
	 * @throws Exception
	 */
	private function image( &$user, $fieldName = 'avatar', $imageUrl )
	{
		global $_CB_framework, $ueConfig;

		if ( ( ! $imageUrl ) || ( ! $fieldName ) ) {
			return;
		}

		$tmpPath						=	$_CB_framework->getCfg( 'absolute_path' ) . '/tmp/';
		$imagePath						=	$_CB_framework->getCfg( 'absolute_path' ) . '/images/comprofiler/';
		$hash							=	substr( md5( $imageUrl ), 0, 6 );

		if ( $user->get( $fieldName, null, GetterInterface::STRING ) && ( $hash == substr( $user->get( $fieldName, null, GetterInterface::STRING ), 0, 6 ) ) ) {
			// The hashes are the same. Now check if the file exists. If it does then skip image processing:
			if ( file_exists( $imagePath . $user->get( $fieldName, null, GetterInterface::STRING ) ) ) {
				return;
			}
		}

		try {
			$field						=	new FieldTable();

			$field->load( array( 'name' => $fieldName ) );

			$field->set( 'params', new Registry( $field->get( 'params', null, GetterInterface::RAW ) ) );

			$conversionType				=	(int) ( isset( $ueConfig['conversiontype'] ) ? $ueConfig['conversiontype'] : 0 );
			$imageSoftware				=	( $conversionType == 5 ? 'gmagick' : ( $conversionType == 1 ? 'imagick' : ( $conversionType == 4 ? 'gd' : 'auto' ) ) );
			$fileName					=	uniqid( $hash . '_' );
			$resize						=	$field->params->get( 'avatarResizeAlways', '', GetterInterface::STRING );

			if ( $resize == '' ) {
				if ( isset( $ueConfig['avatarResizeAlways'] ) ) {
					$resize				=	$ueConfig['avatarResizeAlways'];
				} else {
					$resize				=	1;
				}
			}

			$aspectRatio				=	$field->params->get( 'avatarMaintainRatio', '', GetterInterface::STRING );

			if ( $aspectRatio == '' ) {
				if ( isset( $ueConfig['avatarMaintainRatio'] ) ) {
					$aspectRatio		=	$ueConfig['avatarMaintainRatio'];
				} else {
					$aspectRatio		=	1;
				}
			}

			$image						=	new \CBLib\Image\Image( $imageSoftware, $resize, $aspectRatio );
			/** @var GuzzleHttp\ClientInterface $client */
			$client						=	new GuzzleHttp\Client();
			/** @var GuzzleHttp\Message\Response $result */
			$result						=	$client->get( $imageUrl );

			if ( $result->getStatusCode() != 200 ) {
				return;
			}

			$photo						=	$image->getImagine()->load( $result->getBody() );

			if ( ! $photo ) {
				return;
			}

			$ext						=	strtolower( preg_replace( '/[^-a-zA-Z0-9_]/', '', pathinfo( $imageUrl, PATHINFO_EXTENSION ) ) );

			if ( ( ! $ext ) || ( ! in_array( $ext, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) ) {
				$mime					=	$result->getHeader( 'Content-Type' );

				switch ( $mime ) {
					case 'image/jpeg':
						$ext			=	'jpg';
						break;
					case 'image/png':
						$ext			=	'png';
						break;
					case 'image/gif':
						$ext			=	'gif';
						break;
				}
			}

			if ( ! in_array( $ext, array( 'jpg', 'jpeg', 'gif', 'png' ) ) ) {
				return;
			}

			$tmpImage					=	$tmpPath . $fileName . '.' . $ext;

			$photo->save( $tmpImage );

			$image->setImage( $photo );
			$image->setName( $fileName );
			$image->setSource( $tmpImage );
			$image->setDestination( $imagePath );

			$width						=	$field->params->get( 'avatarWidth', ( $fieldName == 'canvas' ? 1280 : '' ), GetterInterface::STRING );

			if ( $width == '' ) {
				if ( isset( $ueConfig['avatarWidth'] ) ) {
					$width				=	$ueConfig['avatarWidth'];
				} else {
					$width				=	200;
				}
			}

			$height						=	$field->params->get( 'avatarHeight', ( $fieldName == 'canvas' ? 640 : '' ), GetterInterface::STRING );

			if ( $height == '' ) {
				if ( isset( $ueConfig['avatarHeight'] ) ) {
					$height				=	$ueConfig['avatarHeight'];
				} else {
					$height				=	500;
				}
			}

			$image->processImage( $width, $height );

			if ( $user->get( $fieldName, null, GetterInterface::STRING ) ) {
				if ( file_exists( $imagePath . $user->get( $fieldName, null, GetterInterface::STRING ) ) ) {
					@unlink( $imagePath . $user->get( $fieldName, null, GetterInterface::STRING ) );
				}

				if ( file_exists( $imagePath . 'tn' . $user->get( $fieldName, null, GetterInterface::STRING ) ) ) {
					@unlink( $imagePath . 'tn' . $user->get( $fieldName, null, GetterInterface::STRING ) );
				}
			}

			$user->set( $fieldName, $image->getCleanFilename() );

			$image->setName( 'tn' . $fileName );

			$thumbWidth					=	$field->params->get( 'thumbWidth', ( $fieldName == 'canvas' ? 640 : '' ), GetterInterface::STRING );

			if ( $thumbWidth == '' ) {
				if ( isset( $ueConfig['thumbWidth'] ) ) {
					$thumbWidth			=	$ueConfig['thumbWidth'];
				} else {
					$thumbWidth			=	60;
				}
			}

			$thumbHeight				=	$field->params->get( 'thumbHeight', ( $fieldName == 'canvas' ? 320 : '' ), GetterInterface::STRING );

			if ( $thumbHeight == '' ) {
				if ( isset( $ueConfig['thumbHeight'] ) ) {
					$thumbHeight		=	$ueConfig['thumbHeight'];
				} else {
					$thumbHeight		=	86;
				}
			}

			$image->processImage( $thumbWidth, $thumbHeight );

			unlink( $tmpImage );

			if ( in_array( $fieldName, array( 'canvas', 'avatar' ) ) ) {
				$approval				=	$this->connect->params()->get( $fieldName . '_approve', 2, GetterInterface::INT );
			} else {
				$approval				=	2;
			}

			if ( $approval == 2 ) {
				$approval				=	$field->params->get( 'avatarUploadApproval', '', GetterInterface::STRING );

				if ( $approval == '' ) {
					if ( isset( $ueConfig['avatarUploadApproval'] ) ) {
						$approval		=	$ueConfig['avatarUploadApproval'];
					} else {
						$approval		=	1;
					}
				}
			}

			$user->set( $fieldName . 'approved', ( $approval ? 0 : 1 ) );
		} catch ( Exception $e ) {
			if ( $_CB_framework->getCfg( 'debug' ) ) {
				throw new Exception( $e->getMessage() );
			}
		}
	}

	/**
	 * Maps profile fields to the user
	 *
	 * @param UserTable $user
	 * @param Profile   $profile
	 */
	private function fields( &$user, $profile )
	{
		global $_CB_database;

		$providers					=	CBConnect::getProviders();
		$allowed					=	$providers[$this->connect->id]['fields'];
		$exclude					=	array( 'id', 'username', 'name', 'firstname', 'middlename', 'lastname', 'email', 'avatar', $providers[$this->connect->id]['field'] );
		$types						=	null;

		if ( $types == null ) {
			$query					=	'SELECT *'
									.	"\n FROM " .  $_CB_database->NameQuote( '#__comprofiler_fields' );
			$_CB_database->setQuery( $query );
			$types					=	$_CB_database->loadAssocList( 'name', 'type' );
		}

		foreach ( $this->connect->params()->subTree( 'fields' ) as $field ) {
			/** @var ParamsInterface $field */
			$fromField				=	$field->get( 'from', null, GetterInterface::STRING );
			$toField				=	$field->get( 'to', null, GetterInterface::STRING );

			if ( ( ! $fromField ) || ( ! $toField ) || in_array( $toField, $exclude ) || ( ! in_array( $fromField, $allowed ) ) ) {
				continue;
			}

			// Check for overrides first encase we needed to reformat something after requesting from provider:
			$value					=	$profile->get( $fromField, null, GetterInterface::RAW );

			if ( $value === null ) {
				// No override found so lets check the request response profile:
				$value				=	$profile->profile()->get( $fromField, null, GetterInterface::RAW );
			}

			if ( is_object( $value ) ) {
				$value				=	get_object_vars( $value );
			}

			if ( is_array( $value ) ) {
				if ( isset( $types[$toField] ) ) {
					$values			=	array();

					foreach ( $value as $v ) {
						if ( is_array( $v ) || is_object( $v ) ) {
							continue;
						}

						$values[]	=	$v;
					}

					if ( preg_match( '/multicheckbox|multiselect/', $types[$toField] ) ) {
						$value		=	implode( '|*|', $values );
					} elseif ( $types[$toField] == 'textarea' ) {
						$value		=	implode( "\n", $values );
					} elseif ( $types[$toField] == 'editorta' ) {
						$value		=	'<p>' . implode( '</p><p>', $values ) . '</p>';
					} else {
						$value		=	implode( ', ', $values );
					}
				} else {
					continue;
				}
			}

			if ( isset( $types[$toField] ) ) {
				switch ( $types[$toField] ) {
					case 'checkbox':
						$value		=	( ( $value === true ) || ( $value === 'true' ) || ( $value === 1 ) || ( $value === '1' ) ? 1 : 0 );
						break;
					case 'date':
						$value		=	Application::Date( $value, 'UTC' )->format( 'Y-m-d' );
						break;
					case 'datetime':
						$value		=	Application::Date( $value, 'UTC' )->format( 'Y-m-d H:i:s' );
						break;
					case 'image':
						if ( $this->connect->params()->get( 'mode', 1, GetterInterface::INT ) == 1 ) {
							$this->image( $user, $toField, $value );

							continue 2;
						}
						break;
				}
			}

			$user->set( $toField, $value );
		}
	}

	/**
	 * Logs in a user
	 *
	 * @param UserTable $user
	 * @return bool
	 * @throws Exception
	 */
	private function login( $user )
	{
		global $_CB_framework;

		$cbAuthenticate			=	new CBAuthentication();
		$messagesToUser			=	array();
		$alertMessages			=	array();
		$redirectUrl			=	null;
		$resultError			=	$cbAuthenticate->login( $user->get( 'username', null, GetterInterface::STRING ), false, true, true, $redirectUrl, $messagesToUser, $alertMessages, 1 );

		if ( count( $messagesToUser ) > 0 ) {
			if ( $resultError ) {
				$_CB_framework->enqueueMessage( $resultError, 'error' );
			}

			$return				=		'<div class="cb_template cb_template_' . selectTemplate( 'dir' ) . '">'
								.			'<div>' . implode( '</div><div>', $messagesToUser ) . '</div>'
								.		'</div>';

			echo $return;

			return false;
		} elseif ( $resultError ) {
			throw new Exception( $resultError );
		} else {
			$redirect			=	null;

			if ( ( ! $user->get( 'lastvisitDate', null, GetterInterface::STRING ) ) || ( $user->get( 'lastvisitDate', null, GetterInterface::STRING ) == '0000-00-00 00:00:00' ) ) {
				$redirect		=	$this->connect->params()->get( 'firstlogin', true, GetterInterface::STRING );
			}

			if ( ! $redirect ) {
				$redirect		=	$this->connect->params()->get( 'login', true, GetterInterface::STRING );
			}

			if ( ! $redirect ) {
				$redirect		=	base64_decode( $this->connect->provider()->session()->get( $this->connect->id . '.return', null, GetterInterface::STRING ) );
			}

			if ( ! $redirect ) {
				$redirect		=	CBConnect::getReturn( true, true );
			}

			if ( ! $redirect ) {
				$redirect		=	'index.php';
			}

			$message			=	( count( $alertMessages ) > 0 ? stripslashes( implode( '<br />', $alertMessages ) ) : null );

			cbRedirect( $redirect, $message, 'message' );
		}

		return true;
	}
}