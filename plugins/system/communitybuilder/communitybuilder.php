<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

jimport( 'joomla.plugin.plugin' );

class plgSystemCommunityBuilder extends JPlugin {

	/**
	 * @param  JForm         $form  Joomla XML form
	 * @param  array|object  $data  Form data (j2.5 is array, j3.0 is object, converted to array for easy usage between both)
	 */
	public function onContentPrepareForm( $form, $data ) {
		if ( ( $form instanceof JForm ) && ( $form->getName() == 'com_menus.item' ) ) {
			$data				=	(array) $data;

			if ( isset( $data['request']['option'] ) && ( $data['request']['option'] == 'com_comprofiler' ) && isset( $data['request']['view'] ) && ( $data['request']['view'] == 'pluginclass' ) ) {
				$element		=	( isset( $data['request']['plugin'] ) ? $data['request']['plugin'] : 'cb.core' );

				if ( $element ) {
					$db			=	JFactory::getDBO();

					$query		=	'SELECT ' . $db->quoteName( 'type' )
								.	', ' . $db->quoteName( 'folder' )
								.	"\n FROM " . $db->quoteName( '#__comprofiler_plugin' )
								.	"\n WHERE " . $db->quoteName( 'element' ) . " = " . $db->quote( $element );
					$db->setQuery( $query );
					$plugin		=	$db->loadAssoc();

					if ( $plugin ) {
						$path	=	JPATH_ROOT . '/components/com_comprofiler/plugin/' . $plugin['type'] . '/' . $plugin['folder'] . '/xml';

						if ( file_exists( $path ) ) {
							JForm::addFormPath( $path );

							$form->loadFile( 'metadata', false );
						}
					}
				}
			}
		}
	}

	public function onAfterRoute() {
		// Joomla doesn't populate GET so we'll do it below based off the menu item and routing variables:
		$app							=	JFactory::getApplication();

		if ( $app->isSite() && ( $app->input->get( 'option' ) == 'com_comprofiler' ) ) {
			// Map the current route variables to GET if missing:
			$route						=	$app->getRouter()->getVars();

			if ( $route && ( isset( $route['option'] ) ) && ( $route['option'] == 'com_comprofiler' ) ) {
				foreach( $route as $k => $v ) {
					if ( ! isset( $_GET[$k] ) ) {
						$_GET[$k]		=	$v;
					}

					if ( ! isset( $_REQUEST[$k] ) ) {
						$_REQUEST[$k]	=	$v;
					}
				}
			}

			// Map the current menu item variables to GET if missing:
			$menu						=	$app->getMenu()->getActive();

			if ( $menu && isset( $menu->query ) && ( isset( $menu->query['option'] ) ) && ( $menu->query['option'] == 'com_comprofiler' ) ) {
				foreach( $menu->query as $k => $v ) {
					if ( ! isset( $_GET[$k] ) ) {
						$_GET[$k]		=	$v;
					}

					if ( ! isset( $_REQUEST[$k] ) ) {
						$_REQUEST[$k]	=	$v;
					}
				}
			}

			// Adjust the reset password overrides so CB profile edit can be used to reset the password:
			if ( $this->params->get( 'rewrite_urls', 1 ) ) {
				$view						=	$app->input->get( 'view' );

				$app->set( 'site_reset_password_override', 1 );
				$app->set( 'site_reset_password_option', 'com_comprofiler' );
				$app->set( 'site_reset_password_view', 'userdetails' );

				if ( in_array( $view, array( 'saveuseredit', 'logout', 'fieldclass' ) ) ) {
					$app->set( 'site_reset_password_view', $view );
				}

				$app->set( 'site_reset_password_layout', '' );
				$app->set( 'site_reset_password_tasks', 'com_comprofiler/userdetails,com_comprofiler/saveuseredit,com_comprofiler/logout,com_comprofiler/fieldclass' );
			}
		}
	}

	public function onAfterInitialise() {
		$app								=	JFactory::getApplication();

		if ( $app->isSite() ) {
			if ( $app->input->get( 'option' ) == 'com_comprofiler' ) {
				// CB is dynamic and can't be page cached; so remove the cache:
				if ( JFactory::getConfig()->get( 'caching' ) ) {
					JFactory::getCache( 'page' )->remove( JUri::getInstance()->toString() );
				}
			}

			if ( $this->isRerouteSafe() ) {
				$view						=	$app->input->get( 'task' );

				if ( ! $view ) {
					$view					=	$app->input->get( 'view' );
				}

				if ( $this->params->get( 'redirect_urls', 1 ) && ( $app->input->get( 'option' ) == 'com_users' ) ) {
					switch ( $view ) {
						case 'profile':
							if ( $app->input->get( 'layout' ) == 'edit' ) {
								$userId		=	(int) $app->input->get( 'user_id' );
								$task		=	'userdetails';

								if ( $userId ) {
									$task	.=	'&user=' . $userId;
								}
							} else {
								$task		=	'userprofile';
							}
							break;
						case 'registration':
							$task			=	'registers';
							break;
						case 'reset':
						case 'remind':
							$task			=	'lostpassword';
							break;
						case 'user.logout':
						case 'logout':
							$task			=	'logout';
							break;
						case 'user.login':
						case 'login':
							$task								=	'login';

							if ( $_POST && JSession::checkToken( 'post' ) ) {
								$cbLoaded						=	true;

								if ( ( ! file_exists( JPATH_SITE . '/libraries/CBLib/CBLib/Core/CBLib.php' ) ) || ( ! file_exists( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' ) ) ) {
									$cbLoaded					=	false;
								}

								if ( $cbLoaded ) {
									include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );

									$cbSpoofField				=	cbSpoofField();
									$cbSpoofString				=	cbSpoofString( null, 'login' );

									// Change the request variables so it points to CBs login page before rendering in CB:
									$app->input->set( 'option', 'com_comprofiler' );
									$app->input->set( 'task', 'login' );
									$app->input->set( 'view', 'login' );
									$app->input->set( $cbSpoofField, $cbSpoofString );

									$_REQUEST['option']			=	'com_comprofiler';
									$_REQUEST['task']			=	'login';
									$_REQUEST['view']			=	'login';
									$_REQUEST[$cbSpoofField]	=	$cbSpoofString;

									$_GET['option']				=	'com_comprofiler';
									$_GET['task']				=	'login';
									$_GET['view']				=	'login';

									$_POST[$cbSpoofField]		=	$cbSpoofString;

									if ( isset( $_POST['return'] ) ) {
										// Make the return redirect compatible with CB:
										$_POST['return']		=	'B:' . $_POST['return'];
									}

									try {
										JComponentHelper::renderComponent( 'com_comprofiler' );
									} catch( Exception $e ) {
										// Just silently fail and do a normal redirect if CB didn't render
									}
								}
							}
							break;
						default:
							$task								=	'login';
							break;
					}

					$Itemid					=	$this->getItemid( $task );
					$url					=	'index.php?option=com_comprofiler' . ( $task ? '&view=' . $task : null ) . ( $Itemid ? '&Itemid=' . $Itemid : null );

					if ( in_array( $task, array( 'login', 'logout' ) ) ) {
						$return				=	$app->input->get( 'return', '', 'BASE64' );

						if ( $return ) {
							$url			.=	'&return=' . $return;
						}
					}

					$app->redirect( JRoute::_( $url, false ), null, null, true, true );
				}

				if ( $this->params->get( 'rewrite_urls', 1 ) ) {
					$router					=	$app->getRouter();

					$router->attachBuildRule( array( $this, 'buildRule' ) );
				}
			}
		}
	}

	/**
	 * @param plgSystemCommunityBuilder $router
	 * @param JUri $uri
	 */
	public function buildRule( &$router, &$uri ) {
		$app									=	JFactory::getApplication();

		if ( $app->isSite() && $this->isRerouteSafe() ) {
			if ( $uri->getVar( 'option' ) == 'com_users' ) {
				$uri->setVar( 'option', 'com_comprofiler' );

				$view							=	$uri->getVar( 'task' );

				if ( ! $view ) {
					$view						=	$uri->getVar( 'view' );
				}

				switch ( $view ) {
					case 'profile':
						if ( $uri->getVar( 'layout' ) == 'edit' ) {
							$userId				=	(int) $uri->getVar( 'user_id' );
							$task				=	'userdetails';

							if ( $userId ) {
								$task			.=	'&user=' . $userId;
							}
						} else {
							$task				=	'userprofile';
						}
						break;
					case 'registration':
						$task					=	'registers';
						break;
					case 'reset':
					case 'remind':
						$task					=	'lostpassword';
						break;
					case 'logout':
						$task					=	'logout';
						break;
					case 'login':
					default:
						$task					=	'login';
						break;
				}

				$uri->delVar( 'task' );
				$uri->delVar( 'view' );
				$uri->delVar( 'layout' );

				if ( $task ) {
					$uri->setVar( 'view', $task );
				}

				$Itemid							=	$this->getItemid( $task );

				$uri->delVar( 'Itemid' );

				if ( $Itemid ) {
					$uri->setVar( 'Itemid', $Itemid );
				}
			}
		}
	}

	/**
	 * Returns the task specific Itemid from Joomla CB menu items
	 *
	 * @param string $task
	 * @return null|int
	 */
	private function getItemid( $task ) {
		static $items			=	null;

		if ( ! isset( $items ) ) {
			$app				=	JFactory::getApplication();
			$menu				=	$app->getMenu();
			$items				=	$menu->getItems( 'component', 'com_comprofiler' );
		}

		$Itemid					=	null;

		if ( ( $task !== 'userprofile' ) && is_string( $task ) ) {
			if ( $items ) foreach ( $items as $item ) {
				if ( ( isset( $item->query['view'] ) && ( $item->query['view'] == $task ) ) || ( isset( $item->query['task'] ) && ( $item->query['task'] == $task ) ) ) {
					$Itemid		=	$item->id;
				}
			}
		}

		if ( ( $task === 'userprofile' ) || ( ( ! $Itemid ) && ( ! in_array( $task, array( 'login', 'logout', 'registers', 'lostpassword' ) ) ) ) ) {
			if ( $items ) foreach ( $items as $item ) {
				if ( ( ! isset( $item->query['view'] ) ) && ( ! isset( $item->query['task'] ) ) ) {
					$Itemid		=	$item->id;
				}
			}

			if ( ! $Itemid ) {
				if ( $items ) foreach ( $items as $item ) {
					if ( ( isset( $item->query['view'] ) && ( $item->query['view'] == 'userslist' ) ) || ( isset( $item->query['task'] ) && ( $item->query['task'] == 'userslist' ) ) ) {
						$Itemid	=	$item->id;
					}
				}
			}
		}

		return $Itemid;
	}

	/**
	 * Checks if the viewing user is a guest
	 *
	 * @return bool
	 */
	private function getUserIsGuest() {
		static $cache	=	null;

		if ( $cache === null ) {
			$cache		=	JFactory::getUser()->get( 'guest' );
		}

		return $cache;
	}

	/**
	 * Checks online status of the site and if the user is a guest
	 * Used to determine if URL rewriting is safe to perform
	 *
	 * @return bool
	 */
	private function isRerouteSafe() {
		return ( ( JFactory::getConfig()->get( 'offline' ) == 1 ) && $this->getUserIsGuest() ? false : true );
	}
}
