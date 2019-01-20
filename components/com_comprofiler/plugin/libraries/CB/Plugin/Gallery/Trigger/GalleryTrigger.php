<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

namespace CB\Plugin\Gallery\Trigger;

use CBLib\Database\Table\Table;

defined('CBLIB') or die();

class GalleryTrigger extends \cbFieldHandler
{

	/**
	 * Handles loading source for non-core CB Gallery objects
	 *
	 * @param string $asset
	 * @param mixed  $source
	 */
	public function assetSource( $asset, &$source )
	{
		global $_CB_database, $_PLUGINS;

		if ( preg_match( '/^article\.(\d+)/', $asset, $matches ) ) {
			static $article		=	array();

			$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

			if ( ! isset( $article[$id] ) ) {
				$query			=	'SELECT *'
								.	"\n FROM " . $_CB_database->NameQuote( '#__content' )
								.	"\n WHERE " . $_CB_database->NameQuote( 'id' ) . " = " . (int) $id;
				$_CB_database->setQuery( $query, 0, 1 );
				$details		=	$_CB_database->loadAssoc();

				$row			=	new Table( null, '#__content', 'id' );

				foreach ( $details as $k => $v ) {
					$row->set( $k, $v );
				}

				$article[$id]	=	$row;
			}

			$source				=	$article[$id];
		} elseif ( preg_match( '/^blog\.(\d+)/', $asset, $matches ) ) {
			if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbblogs' ) ) {
				return;
			}

			$model				=	\cbblogsClass::getModel();

			if ( ! $model->file ) {
				return;
			}

			static $blog		=	array();

			$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

			if ( ! isset( $blog[$id] ) ) {
				$row			=	new \cbblogsBlogTable();

				$row->load( $id );

				$article[$id]	=	$row;
			}

			$source				=	$blog[$id];
		} elseif ( preg_match( '/^kunena\.(\d+)/', $asset, $matches ) ) {
			if ( ! $_PLUGINS->getLoadedPlugin( 'user', 'cbforums' ) ) {
				return;
			}

			$model				=	\cbforumsClass::getModel();

			if ( ! $model->file ) {
				return;
			}

			if ( ! class_exists( 'KunenaForumMessageHelper' ) ) {
				return;
			}

			static $post		=	array();

			$id					=	( isset( $matches[1] ) ? (int) $matches[1] : 0 );

			if ( ! isset( $post[$id] ) ) {
				$post[$id]		=	\KunenaForumMessageHelper::get( $id );
			}

			$source				=	$post[$id];
		}
	}
}