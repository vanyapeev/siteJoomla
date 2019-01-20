<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Application\Application;
use CB\Plugin\ProfileBook\CBProfileBook;
use CB\Plugin\ProfileBook\Table\EntryTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CBplug_cbprofilebook extends cbPluginHandler
{

	/**
	 * @param  TabTable   $tab       Current tab
	 * @param  UserTable  $user      Current user
	 * @param  int        $ui        1 front, 2 admin UI
	 * @param  array      $postdata  Raw unfiltred POST data
	 */
	public function getCBpluginComponent( $tab, $user, $ui, $postdata )
	{
		$format			=	$this->input( 'format', null, GetterInterface::STRING );

		if ( $format != 'raw' ) {
			outputCbJs();
			outputCbTemplate();
		}

		$action			=	$this->input( 'action', null, GetterInterface::STRING );
		$function		=	$this->input( 'func', null, GetterInterface::STRING );
		$id				=	$this->input( 'id', null, GetterInterface::STRING );
		$viewer			=	CBuser::getMyUserDataInstance();

		if ( $format != 'raw' ) {
			ob_start();
		}

		switch ( $action ) {
			case 'entry':
				switch ( $function ) {
					case 'edit':
						$this->showEntryEdit( $id, $viewer );
						break;
					case 'new':
						$this->showEntryEdit( null, $viewer );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveEntry( $id, $viewer );
						break;
					case 'publish':
						$this->stateEntry( 1, $id, $viewer );
						break;
					case 'unpublish':
						$this->stateEntry( 0, $id, $viewer );
						break;
					case 'delete':
						$this->deleteEntry( $id, $viewer );
						break;
					case 'show':
					default:
						$this->showEntry( $id, $viewer );
						break;
				}
				break;
			case 'feedback':
				switch ( $function ) {
					case 'edit':
						$this->showFeedbackEdit( $id, $viewer );
						break;
					case 'save':
						cbSpoofCheck( 'plugin' );
						$this->saveFeedback( $id, $viewer );
						break;
					case 'delete':
						$this->deleteFeedback( $id, $viewer );
						break;
				}
				break;
		}

		if ( $format != 'raw' ) {
			$html		=	ob_get_contents();
			ob_end_clean();

			$class		=	$this->params->get( 'general_class', null );

			$return		=	'<div class="cbProfileBook' . ( $class ? ' ' . htmlspecialchars( $class ) : null ) . '">'
						.		$html
						.	'</div>';

			echo $return;
		}
	}

	/**
	 * Displays a profilebook entry (blog only)
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function showEntry( $id, $viewer )
	{
		global $_CB_framework;

		$row			=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || ( ( ! $row->get( 'published', 0, GetterInterface::INT ) ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'posterid', 0, GetterInterface::INT ) ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$recipient		=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		} elseif ( ( $row->get( 'mode', null, GetterInterface::STRING ) != 'b' ) || ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$tab			=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		require CBProfileBook::getTemplate( $tab->params->get( 'template', '-1', GetterInterface::STRING ), 'blog' );
	}

	/**
	 * Displays a profilebook entry edit
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function showEntryEdit( $id, $viewer )
	{
		global $_CB_framework;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'posterid', 0, GetterInterface::INT ) ) )
				 && ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) )
				 && ( ! Application::MyUser()->isGlobalModerator() )
			) {
				cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		} else {
			$row->set( 'mode', $this->input( 'mode', 'b', GetterInterface::STRING ) );
			$row->set( 'userid', $this->input( 'userid', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) );
		}

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) || ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) == $recipient->get( 'id', 0, GetterInterface::INT ) ) ) ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'b':
				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $recipient->get( 'id', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) && ( ! $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$input						=	array();

		$input['published']			=	moscomprofilerHTML::yesnoSelectList( 'published', 'class="form-control"', $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );

		$input['postertitle']		=	'<input type="postertitle" id="postertitle" name="postertitle" value="' . htmlspecialchars( $this->input( 'post/postertitle', $row->get( 'postertitle', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control required" size="50" />';

		if ( $row->get( 'mode', null, GetterInterface::STRING ) == 'b' ) {
			$input['postercomment']	=	$_CB_framework->displayCmsEditor( 'postercomment', $this->input( 'post/postercomment', $row->get( 'postercomment', null, GetterInterface::HTML ), GetterInterface::HTML ), 400, 200, 40, 7 );
		} else {
			$input['postercomment']	=	'<textarea id="postercomment" name="postercomment" class="form-control required" cols="55" rows="6">' . $this->input( 'post/postercomment', $row->get( 'postercomment', null, GetterInterface::HTML ), GetterInterface::HTML ) . '</textarea>';
		}

		$input['postername']		=	'<input type="postername" id="postername" name="postername" value="' . htmlspecialchars( $this->input( 'post/postername', $row->get( 'postername', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control required" size="50" />';
		$input['posteremail']		=	'<input type="posteremail" id="posteremail" name="posteremail" value="' . htmlspecialchars( $this->input( 'post/posteremail', $row->get( 'posteremail', null, GetterInterface::STRING ), GetterInterface::STRING ) ) . '" class="form-control required" size="50" />';

		require CBProfileBook::getTemplate( $tab->params->get( 'template', '-1', GetterInterface::STRING ), 'edit' );
	}

	/**
	 * Saves a profilebook entry
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function saveEntry( $id, $viewer )
	{
		global $_CB_framework, $_PLUGINS;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			if ( ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'posterid', 0, GetterInterface::INT ) ) )
				 && ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) )
				 && ( ! Application::MyUser()->isGlobalModerator() )
			) {
				cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
			}
		}

		$ipAddresses				=	cbGetIParray();

		$row->set( 'mode', $row->get( 'mode', $this->input( 'post/mode', 'b', GetterInterface::STRING ), GetterInterface::STRING ) );
		$row->set( 'posterid', $row->get( 'posterid', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ) );
		$row->set( 'posterip', $row->get( 'posterip', trim( array_shift( $ipAddresses ) ), GetterInterface::STRING ) );
		$row->set( 'postercomment', $this->input( 'post/postercomment', $row->get( 'postercomment', null, GetterInterface::HTML ), GetterInterface::HTML ) );
		$row->set( 'userid', $row->get( 'userid', $this->input( 'post/userid', $viewer->get( 'id', 0, GetterInterface::INT ), GetterInterface::INT ), GetterInterface::INT ) );

		if ( $row->get( 'id', 0, GetterInterface::INT ) ) {
			$row->set( 'editdate', Application::Database()->getUtcDateTime() );
			$row->set( 'editedbyid', $viewer->get( 'id', 0, GetterInterface::INT ) );
		}

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;
		$ratingDefaults				=	0;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) || ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) == $recipient->get( 'id', 0, GetterInterface::INT ) ) ) ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}

				if ( Application::MyUser()->isGlobalModerator() ) {
					$row->set( 'published', $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );
				} else {
					$row->set( 'published', ( $recipient->get( 'cb_pb_autopublish', null, GetterInterface::STRING ) == '_UE_NO' ? -1 : $row->get( 'published', 1, GetterInterface::INT ) ) );
				}

				$row->set( 'status', $row->get( 'status', 0, GetterInterface::INT ) );
				break;
			case 'b':
				$ratingDefaults		=	2;

				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) || ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $recipient->get( 'id', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}

				$row->set( 'published', $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );
				$row->set( 'status', 1 );
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}

				if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) == $recipient->get( 'id', 0, GetterInterface::INT ) ) || Application::MyUser()->isGlobalModerator() ) {
					$row->set( 'published', $this->input( 'post/published', $row->get( 'published', 1, GetterInterface::INT ), GetterInterface::INT ) );
				} else {
					$row->set( 'published', ( $recipient->get( 'cb_pb_autopublish_wall', null, GetterInterface::STRING ) == '_UE_NO' ? -1 : $row->get( 'published', 1, GetterInterface::INT ) ) );
				}

				$row->set( 'status', $row->get( 'status', ( $viewer->get( 'id', 0, GetterInterface::INT ) == $recipient->get( 'id', 0, GetterInterface::INT ) ? 1 : 0 ), GetterInterface::INT ) );
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( $row->get( 'mode', null, GetterInterface::STRING ) == 'b' ) {
			$row->set( 'postertitle', $this->input( 'post/postertitle', $row->get( 'postertitle', null, GetterInterface::STRING ), GetterInterface::STRING ) );
		}

		if ( ( ! $row->get( 'posterid', 0, GetterInterface::INT ) ) && $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) {
			$row->set( 'postername', $this->input( 'post/postername', $row->get( 'postername', null, GetterInterface::STRING ), GetterInterface::STRING ) );

			if ( ( ! $row->get( 'id', 0, GetterInterface::INT ) ) || Application::MyUser()->isGlobalModerator() ) {
				$row->set( 'posteremail', $this->input( 'post/posteremail', $row->get( 'posteremail', null, GetterInterface::STRING ), GetterInterface::STRING ) );
			}
		}

		if ( $tab->params->get( 'pbEnableRating', $ratingDefaults, GetterInterface::INT ) ) {
			$rating					=	$this->input( 'post/postervote', $row->get( 'postervote', 0, GetterInterface::INT ), GetterInterface::INT );

			if ( $rating < 0 ) {
				$rating				=	0;
			} elseif ( $rating > 5 ) {
				$rating				=	5;
			}

			$row->set( 'postervote', $rating );
		}

		if ( ( ! $row->get( 'posterid', 0, GetterInterface::INT ) ) && ( ! $tab->params->get( 'pbAllowAnony', false, GetterInterface::BOOLEAN ) ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), CBTxt::T( 'Not authorized.' ), 'error' );
		} elseif ( ( ! $row->get( 'postervote', 0, GetterInterface::INT ) ) && ( $tab->params->get( 'pbEnableRating', $ratingDefaults, GetterInterface::INT ) == 3 ) ) {
			$row->setError( CBTxt::T( 'Rating not specified!' ) );
		}

		if ( $row->get( 'mode', null, GetterInterface::STRING ) != 'b' ) {
			$showCaptcha			=	$tab->params->get( 'pbCaptcha', 1, GetterInterface::INT );

			if ( Application::MyUser()->isGlobalModerator() || ( ( $showCaptcha == 1 ) && $viewer->get( 'id', 0, GetterInterface::INT ) ) ) {
				$showCaptcha		=	0;
			}

			if ( $showCaptcha ) {
				$_PLUGINS->trigger( 'onCheckCaptchaHtmlElements', array() );

				if ( $_PLUGINS->is_errors() ) {
					$row->setError( CBTxt::T( $_PLUGINS->getErrorMSG() ) );
				}
			}
		}

		$new						=	( $row->get( 'id', 0, GetterInterface::INT ) ? false : true );

		if ( $row->getError() || ( ! $row->check() ) ) {
			switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
				case 'g':
					$error			=	CBTxt::T( 'GUESTBOOK_SAVE_FAILED', 'Guestbook signature failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'b':
					$error			=	CBTxt::T( 'BLOG_SAVE_FAILED', 'Blog failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'w':
				default:
					$error			=	CBTxt::T( 'POST_SAVE_FAILED', 'Post failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
			}

			$_CB_framework->enqueueMessage( $error, 'error' );

			$this->showEntryEdit( $id, $viewer );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
				case 'g':
					$error			=	CBTxt::T( 'GUESTBOOK_SAVE_FAILED', 'Guestbook signature failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'b':
					$error			=	CBTxt::T( 'BLOG_SAVE_FAILED', 'Blog failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'w':
				default:
					$error			=	CBTxt::T( 'POST_SAVE_FAILED', 'Post failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
			}

			$_CB_framework->enqueueMessage( $error, 'error' );

			$this->showEntryEdit( $id, $viewer );
			return;
		}

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				if ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) {
					if ( $new ) {
						$message	=	CBTxt::T( 'Guestbook signed successfully and awaiting approval!' );
					} else {
						$message	=	CBTxt::T( 'Guestbook signature saved successfully and awaiting approval!' );
					}

					$subject		=	CBTxt::T( 'A new signature on your guestbook is awaiting approval!' );
					$body			=	CBTxt::T( '[user] has signed your <a href="[url]">guestbook</a> and requires your approval.' );
				} else {
					if ( $new ) {
						$message	=	CBTxt::T( 'Guestbook signed successfully!' );
					} else {
						$message	=	CBTxt::T( 'Guestbook signature saved successfully!' );
					}

					$subject		=	CBTxt::T( 'A new signature has been made on your guestbook!' );
					$body			=	CBTxt::T( '[user] has signed your <a href="[url]">guestbook</a>.' );
				}

				if ( $new && ( $recipient->get( 'cb_pb_notifyme', null, GetterInterface::STRING ) != '_UE_NO' ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) != $recipient->get( 'id', 0, GetterInterface::INT ) ) ) {
					$cbUser			=	CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false );

					$extras			=	array(	'user'		=>	( ! $row->get( 'posterid', 0, GetterInterface::INT ) ? $row->get( 'postername', CBTxt::T( 'Anonymous' ), GetterInterface::STRING ) : CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ),
												'signature'	=>	$_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ),
												'post'		=>	CBProfileBook::parseMessage( $row->get( 'postercomment', null, GetterInterface::HTML ), $tab ),
												'date'		=>	cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) )
											);

					$cbNotification	=	new cbNotification( );

					$cbNotification->sendFromSystem( $recipient, $cbUser->replaceUserVars( $subject, false, false, $extras, false ), $cbUser->replaceUserVars( $body, false, false, $extras, false ), false, true );
				}
				break;
			case 'b':
				if ( $new ) {
					$message		=	CBTxt::T( 'Blog created successfully!' );
				} else {
					$message		=	CBTxt::T( 'Blog saved successfully!' );
				}
				break;
			case 'w':
			default:
				if ( $row->get( 'published', 1, GetterInterface::INT ) == -1 ) {
					if ( $new ) {
						$message	=	CBTxt::T( 'Post created successfully and awaiting approval!' );
					} else {
						$message	=	CBTxt::T( 'Post saved successfully and awaiting approval!' );
					}

					$subject		=	CBTxt::T( 'A new post on your wall is awaiting approval!' );
					$body			=	CBTxt::T( '[user] has posted on your <a href="[url]">wall</a> and requires your approval.' );
				} else {
					if ( $new ) {
						$message	=	CBTxt::T( 'Post created successfully!' );
					} else {
						$message	=	CBTxt::T( 'Post saved successfully!' );
					}

					$subject		=	CBTxt::T( 'A new post has been made on your wall!' );
					$body			=	CBTxt::T( '[user] has posted on your <a href="[url]">wall</a>.' );
				}

				if ( $new && ( $recipient->get( 'cb_pb_notifyme_wall', null, GetterInterface::STRING ) != '_UE_NO' ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) != $recipient->get( 'id', 0, GetterInterface::INT ) ) ) {
					$cbUser			=	CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false );

					$extras			=	array(	'user'	=>	( ! $row->get( 'posterid', 0, GetterInterface::INT ) ? $row->get( 'postername', CBTxt::T( 'Anonymous' ), GetterInterface::STRING ) : CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ) ),
												'url'	=>	$_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ),
												'post'	=>	CBProfileBook::parseMessage( $row->get( 'postercomment', null, GetterInterface::HTML ), $tab ),
												'date'	=>	cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ) )
											);

					$cbNotification	=	new cbNotification( );

					$cbNotification->sendFromSystem( $recipient, $cbUser->replaceUserVars( $subject, false, false, $extras, false ), $cbUser->replaceUserVars( $body, false, false, $extras, false ), false, true, null, null, null, $extras );
				}
				break;
		}

		cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), $message );
	}

	/**
	 * Saves published state of a profilebook entry
	 *
	 * @param int       $state
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function stateEntry( $state, $id, $viewer )
	{
		global $_CB_framework;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		} elseif ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row->set( 'published', (int) $state );

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'b':
				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( $row->getError() || ( ! $row->check() ) ) {
			switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
				case 'g':
					$error			=	CBTxt::T( 'GUESTBOOK_STATE_SAVE_FAILED', 'Guestbook signature state failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'b':
					$error			=	CBTxt::T( 'BLOG_STATE_SAVE_FAILED', 'Blog state failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'w':
				default:
					$error			=	CBTxt::T( 'POST_STATE_SAVE_FAILED', 'Post state failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
			}

			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), $error, 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
				case 'g':
					$error			=	CBTxt::T( 'GUESTBOOK_STATE_SAVE_FAILED', 'Guestbook signature state failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'b':
					$error			=	CBTxt::T( 'BLOG_STATE_SAVE_FAILED', 'Blog state failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'w':
				default:
					$error			=	CBTxt::T( 'POST_STATE_SAVE_FAILED', 'Post state failed to save! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
			}

			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), $error, 'error' );
		}

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$message			=	CBTxt::T( 'Guestbook signature state saved successfully!' );
				break;
			case 'b':
				$message			=	CBTxt::T( 'Blog state saved successfully!' );
				break;
			case 'w':
			default:
				$message			=	CBTxt::T( 'Post state saved successfully!' );
				break;
		}

		cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), $message );
	}

	/**
	 * Deletes a profilebook entry
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function deleteEntry( $id, $viewer )
	{
		global $_CB_framework;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		} elseif ( ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'posterid', 0, GetterInterface::INT ) ) )
			 && ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) )
			 && ( ! Application::MyUser()->isGlobalModerator() )
		) {
			cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'b':
				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( ( ! $row->canDelete() ) || ( ! $row->delete() ) ) {
			switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
				case 'g':
					$error			=	CBTxt::T( 'GUESTBOOK_DELETE_FAILED', 'Guestbook signature failed to delete! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'b':
					$error			=	CBTxt::T( 'BLOG_DELETE_FAILED', 'Blog failed to delete! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
				case 'w':
				default:
					$error			=	CBTxt::T( 'POST_DELETE_FAILED', 'Post failed to delete! Error: [error]', array( '[error]' => $row->getError() ) );
					break;
			}

			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), $error, 'error' );
		}

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$message			=	CBTxt::T( 'Guestbook signature deleted successfully!' );
				break;
			case 'b':
				$message			=	CBTxt::T( 'Blog deleted successfully!' );
				break;
			case 'w':
			default:
				$message			=	CBTxt::T( 'Post deleted successfully!' );
				break;
		}

		cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), $message );
	}

	/**
	 * Displays a profilebook entry feedback edit
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function showFeedbackEdit( $id, $viewer )
	{
		global $_CB_framework;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		} elseif ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'b':
				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$input						=	array();

		$input['feedback']			=	'<textarea id="feedback" name="feedback" class="form-control required" cols="55" rows="6">' . $this->input( 'post/feedback', $row->get( 'feedback', null, GetterInterface::HTML ), GetterInterface::HTML ) . '</textarea>';

		require CBProfileBook::getTemplate( $tab->params->get( 'template', '-1', GetterInterface::STRING ), 'edit_feedback' );
	}

	/**
	 * Saves a profilebook entry feedback
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function saveFeedback( $id, $viewer )
	{
		global $_CB_framework;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		} elseif ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row->set( 'feedback', $this->input( 'post/feedback', $row->get( 'feedback', null, GetterInterface::HTML ), GetterInterface::HTML ) );

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'b':
				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( $row->getError() || ( ! $row->check() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'FEEDBACK_SAVE_FAILED', 'Feedback failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showFeedbackEdit( $id, $viewer );
			return;
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			$_CB_framework->enqueueMessage( CBTxt::T( 'FEEDBACK_SAVE_FAILED', 'Feedback failed to save! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );

			$this->showFeedbackEdit( $id, $viewer );
			return;
		}

		cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), CBTxt::T( 'Feedback saved successfully!' ) );
	}

	/**
	 * Deletes a profilebook entry feedback
	 *
	 * @param int       $id
	 * @param UserTable $viewer
	 */
	private function deleteFeedback( $id, $viewer )
	{
		global $_CB_framework;

		$row						=	new EntryTable();

		if ( $id ) {
			$row->load( (int) $id );
		}

		if ( ! $row->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Not authorized.' ), 'error' );
		} elseif ( ( ! $viewer->get( 'id', 0, GetterInterface::INT ) ) || ( ( $viewer->get( 'id', 0, GetterInterface::INT ) != $row->get( 'userid', 0, GetterInterface::INT ) ) && ( ! Application::MyUser()->isGlobalModerator() ) ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $row->get( 'userid', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		$row->set( 'feedback', '' );

		$recipient					=	CBuser::getUserDataInstance( $row->get( 'userid', 0, GetterInterface::INT ) );

		if ( ! $recipient->get( 'id', 0, GetterInterface::INT ) ) {
			cbRedirect( 'index.php', CBTxt::T( 'Profile does not exist.' ), 'error' );
		}

		$tab						=	null;

		switch ( $row->get( 'mode', null, GetterInterface::STRING ) ) {
			case 'g':
				$tab				=	CBProfileBook::getTab( 'getprofilebookTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'b':
				$tab				=	CBProfileBook::getTab( 'getprofilebookblogTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_blog', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
			case 'w':
				$tab				=	CBProfileBook::getTab( 'getprofilebookwallTab', $recipient->get( 'id', 0, GetterInterface::INT ) );

				if ( $recipient->get( 'cb_pb_enable_wall', null, GetterInterface::STRING ) == '_UE_NO' ) {
					cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
				}
				break;
		}

		if ( ! $tab ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false ), CBTxt::T( 'Not authorized.' ), 'error' );
		}

		if ( $row->getError() || ( ! $row->check() ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), CBTxt::T( 'FEEDBACK_DELETE_FAILED', 'Feedback failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		if ( $row->getError() || ( ! $row->store() ) ) {
			cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), CBTxt::T( 'FEEDBACK_DELETE_FAILED', 'Feedback failed to delete! Error: [error]', array( '[error]' => $row->getError() ) ), 'error' );
		}

		cbRedirect( $_CB_framework->userProfileUrl( $recipient->get( 'id', 0, GetterInterface::INT ), false, $tab->get( 'tabid', 0, GetterInterface::INT ) ), CBTxt::T( 'Feedback deleted successfully!' ) );
	}
}