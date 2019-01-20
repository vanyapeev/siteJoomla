<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Trigger;

use CB\Plugin\Gallery\CBGallery;
use CB\Plugin\Gallery\Gallery;
use CBLib\Application\Application;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

defined('CBLIB') or die();

class WorkflowTrigger extends \cbPluginHandler
{

	/**
	 * Displays link to folder and item pending approval page
	 *
	 * @param bool $horizontal
	 * @return array|null
	 */
	public function approvalLink( $horizontal )
	{
		global $_CB_framework;

		if ( ( ! Application::MyUser()->isGlobalModerator() ) || ( ! CBGallery::getGlobalParams()->get( 'general_workflows', true, GetterInterface::BOOLEAN ) ) ) {
			return null;
		}

		$gallery		=	new Gallery( 'all' );

		$gallery->set( 'published', -1 );

		$folders		=	$gallery->folders( 'count' );
		$items			=	$gallery->items( 'count' );
		$return			=	null;

		if ( $folders ) {
			$return		.=	'<' . ( $horizontal ? 'span' : 'div' ) . ' class="cbModeratorLink cbModeratorLinkGalleryFolders">'
						.		'<a href="' . $_CB_framework->pluginClassUrl( 'cbgallery', true, array( 'action' => 'approval' ) ) . '">' . CBTxt::T( 'GALLERY_ALBUM_APPROVALS', '%%COUNT%% Album Approval|%%COUNT%% Album Approvals', array( '%%COUNT%%' => $folders ) ) . '</a>'
						.	'</' . ( $horizontal ? 'span' : 'div' ) . '>'
						.	( $items && $horizontal ? '&nbsp;' : null );
		}

		if ( $items ) {
			$return		.=	'<' . ( $horizontal ? 'span' : 'div' ) . ' class="cbModeratorLink cbModeratorLinkGalleryItems">'
						.		'<a href="' . $_CB_framework->pluginClassUrl( 'cbgallery', true, array( 'action' => 'approval' ) ) . '">' . CBTxt::T( 'GALLERY_MEDIA_APPROVALS', '%%COUNT%% Media Approval|%%COUNT%% Media Approvals', array( '%%COUNT%%' => $items ) ) . '</a>'
						.	'</' . ( $horizontal ? 'span' : 'div' ) . '>';
		}

		if ( ! $return ) {
			return null;
		}

		return array( 'afterLinks' => $return );
	}
}