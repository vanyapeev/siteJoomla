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
use CB\Plugin\AutoActions\CBAutoActions;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class CodeAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return mixed
	 */
	public function execute( $user )
	{
		global $_CB_framework;

		$return							=	null;

		foreach ( $this->autoaction()->params()->subTree( 'code' ) as $row ) {
			/** @var ParamsInterface $row */
			$code						=	$this->string( $user, $row->get( 'code', null, GetterInterface::RAW ), false );
			$url						=	$this->string( $user, $row->get( 'url', null, GetterInterface::STRING ) );

			if ( ( ! $code ) && ( ! $url ) ) {
				continue;
			}

			$method						=	$row->get( 'method', 'HTML', GetterInterface::STRING );
			$content					=	null;

			switch ( $method ) {
				case 'PHP1':
				case 'PHP2':
				case 'PHP':
					$autoaction			=	$this->autoaction();
					$variables			=	$this->variables();
					$content			=	CBAutoActions::outputCode( $code, $autoaction, $user, $variables );
					break;
				case 'JS':
					$_CB_framework->document->addHeadScriptDeclaration( $code );
					break;
				case 'JS_URL':
					$_CB_framework->document->addHeadScriptUrl( $url );
					break;
				case 'JQUERY':
					$plugins			=	$row->get( 'plugins', null, GetterInterface::STRING );

					if ( $plugins ) {
						$plgs			=	explode( ',', $plugins );
					} else {
						$plgs			=	array();
					}

					$pluginUrls			=	$this->string( $user, $row->get( 'pluginurls', null, GetterInterface::STRING ) );

					if ( $pluginUrls ) {
						$plgUrls		=	explode( "\n", $pluginUrls );

						if ( $plgUrls ) foreach ( $plgUrls as $plgUrl ) {
							$plgName	=	pathinfo( $plgUrl, PATHINFO_FILENAME );

							$_CB_framework->addJQueryPlugin( $plgName, $plgUrl );

							$plgs[]		=	$plgName;
						}
					}

					$_CB_framework->outputCbJQuery( $code, ( empty( $plgs ) ? null : $plgs ) );
					break;
				case 'CSS':
					$_CB_framework->document->addHeadStyleInline( $code );
					break;
				case 'CSS_URL':
					$_CB_framework->document->addHeadStyleSheet( $url );
					break;
				case 'HEADER':
					$_CB_framework->document->addHeadCustomHtml( $code );
					break;
				case 'TITLE':
					$_CB_framework->setPageTitle( $code );
					break;
				case 'PATHWAY':
					$_CB_framework->appendPathWay( $code, $url );
					break;
				case 'MESSAGE':
					$messageType		=	$row->get( 'message_type', 'message', GetterInterface::STRING );

					if ( $messageType == 'custom' ) {
						$messageType	=	$this->string( $user, $row->get( 'message_type_custom', null, GetterInterface::STRING ) );
					}

					$_CB_framework->enqueueMessage( $code, ( $messageType ? $messageType : null ) );
					break;
				case 'HTML':
				default:
					ob_start();
					$content			=	$code;
					ob_end_clean();
					break;
			}

			if ( ( ! is_object( $content ) ) && ( ! is_array( $content ) ) && ( $content !== '' ) && ( $content !== null ) ) {
				if ( ( $return !== '' ) && ( $return !== null ) ) {
					$return				.=	$content;
				} else {
					$return				=	$content;
				}
			}
		}

		return $return;
	}
}