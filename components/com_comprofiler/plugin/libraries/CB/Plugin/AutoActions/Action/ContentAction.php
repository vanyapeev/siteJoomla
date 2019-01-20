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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

class ContentAction extends Action
{

	/**
	 * @param UserTable $user
	 * @return null
	 */
	public function execute( $user )
	{
		global $_CB_framework;

		foreach ( $this->autoaction()->params()->subTree( 'content' ) as $row ) {
			/** @var ParamsInterface $row */
			$owner					=	$row->get( 'owner', null, GetterInterface::STRING );

			if ( ! $owner ) {
				$owner				=	$user->get( 'id', 0, GetterInterface::INT );
			} else {
				$owner				=	(int) $this->string( $user, $owner );
			}

			if ( ! $owner ) {
				$this->error( CBTxt::T( 'AUTO_ACTION_CONTENT_NO_OWNER', ':: Action [action] :: Content skipped due to missing owner', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
				continue;
			}

			if ( $user->get( 'id', 0, GetterInterface::INT ) != $owner ) {
				$actionUser			=	\CBuser::getUserDataInstance( $owner );
			} else {
				$actionUser			=	$user;
			}

			$mode					=	$row->get( 'mode', 1, GetterInterface::INT );
			$title					=	$this->string( $actionUser, $row->get( 'title', null, GetterInterface::STRING ) );
			$alias					=	$row->get( 'alias', null, GetterInterface::STRING );

			if ( ! $alias ) {
				$alias				=	$title;
			} else {
				$alias				=	$this->string( $actionUser, $alias );
			}

			$alias					=	$this->cleanAlias( $alias );
			$introText				=	$this->string( $actionUser, $row->get( 'introtext', null, GetterInterface::RAW ), false );
			$fullText				=	$this->string( $actionUser, $row->get( 'fulltext', null, GetterInterface::RAW ), false );
			$metaDesc				=	$this->string( $actionUser, $row->get( 'metadesc', null, GetterInterface::RAW ), false );
			$metaKey				=	$this->string( $actionUser, $row->get( 'metakey', null, GetterInterface::RAW ), false );
			$access					=	$row->get( 'access', 1, GetterInterface::INT );
			$published				=	$row->get( 'published', 1, GetterInterface::INT );
			$featured				=	$row->get( 'featured', 0, GetterInterface::INT );
			$language				=	$row->get( 'language', '*', GetterInterface::STRING );

			if ( $mode == 1 ) {
				\JTable::addIncludePath( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_content/tables' );

				$category			=	$row->get( 'category_j', null, GetterInterface::INT );
				$table				=	\JTable::getInstance( 'content' );

				while ( $table->load( array( 'alias' => $alias, 'catid' => $category ) ) ) {
					$matches		=	null;

					if ( preg_match( '#-(\d+)$#', $alias, $matches ) ) {
						$alias		=	preg_replace( '#-(\d+)$#', '-' . ( $matches[1] + 1 ) . '', $alias );
					} else {
						$alias		.=	'-2';
					}
				}

				/** @var \JTableContent $article */
				$article			=	\JTable::getInstance( 'content' );

				$article->set( 'created_by', $actionUser->get( 'id', 0, GetterInterface::INT ) );
				$article->set( 'title', $title );
				$article->set( 'alias', $alias );
				$article->set( 'introtext', $introText );
				$article->set( 'fulltext', $fullText );
				$article->set( 'metadesc', $metaDesc );
				$article->set( 'metakey', $metaKey );
				$article->set( 'catid', $category );
				$article->set( 'access', $access );
				$article->set( 'state', $published );
				$article->set( 'featured', $featured );
				$article->set( 'ordering', 1 );
				$article->set( 'created', $_CB_framework->getUTCDate() );
				$article->set( 'language', $language );

				$article->set( 'images', '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}' );
				$article->set( 'urls', '{"urla":null,"urlatext":"","targeta":"","urlb":null,"urlbtext":"","targetb":"","urlc":null,"urlctext":"","targetc":""}' );
				$article->set( 'attribs', '{"show_title":"","link_titles":"","show_tags":"","show_intro":"","info_block_position":"","show_category":"","link_category":"","show_parent_category":"","link_parent_category":"","show_author":"","link_author":"","show_create_date":"","show_modify_date":"","show_publish_date":"","show_item_navigation":"","show_icons":"","show_print_icon":"","show_email_icon":"","show_vote":"","show_hits":"","show_noauth":"","urls_position":"","alternative_readmore":"","article_layout":"","show_publishing_options":"","show_article_options":"","show_urls_images_backend":"","show_urls_images_frontend":""}' );
				$article->set( 'metadata', '{"robots":"","author":"","rights":"","xreference":"","tags":""}' );

				if ( ! $article->store() ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CONTENT_FAILED', ':: Action [action] :: Content failed to save', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				$article->reorder( '`catid` = ' . $category );

				if ( $article->get( 'featured' ) ) {
					$feature	=	\JTable::getInstance( 'Featured', 'ContentTable' );

					$feature->set( 'content_id', (int) $article->get( 'id' ) );
					$feature->set( 'ordering', 0 );

					if ( $feature->store() ) {
						$feature->reorder();
					}
				}
			} elseif ( ( $mode == 2 ) && $this->isK2Installed() ) {
				$category			=	$row->get( 'category_k', null, GetterInterface::INT );

				/** @var \TableK2Item $article */
				$article			=	\JTable::getInstance( 'K2Item', 'Table' );

				$article->set( 'created_by', $actionUser->get( 'id', 0, GetterInterface::INT ) );
				$article->set( 'title', $title );
				$article->set( 'alias', $alias );
				$article->set( 'introtext', $introText );
				$article->set( 'fulltext', $fullText );
				$article->set( 'metadesc', $metaDesc );
				$article->set( 'metakey', $metaKey );
				$article->set( 'catid', $category );
				$article->set( 'access', $access );
				$article->set( 'published', $published );
				$article->set( 'featured', $featured );
				$article->set( 'ordering', 1 );
				$article->set( 'created', $_CB_framework->getUTCDate() );
				$article->set( 'language', $language );

				if ( ! $article->store() ) {
					$this->error( CBTxt::T( 'AUTO_ACTION_CONTENT_FAILED', ':: Action [action] :: Content failed to save', array( '[action]' => $this->autoaction()->get( 'id', 0, GetterInterface::INT ) ) ) );
					continue;
				}

				$article->reorder( '`catid` = ' . $category );
			}
		}

		return null;
	}

	/**
	 * @return array
	 */
	public function k2Categories()
	{
		global $_CB_framework;

		$listCategories				=	array();

		if ( $this->isK2Installed() ) {
			require_once( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2/models/categories.php' );

			$categoryModel			=	new \K2ModelCategories();

			$categories				=	$categoryModel->categoriesTree( null, true, true );

			if ( $categories ) foreach ( $categories as $category ) {
				$listCategories[]	=	\moscomprofilerHTML::makeOption( (string) $category->value, $category->text );
			}
		}

		return $listCategories;
	}

	/**
	 * @return bool
	 */
	private function isK2Installed()
	{
		global $_CB_framework;

		if ( is_dir( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2' ) && class_exists( 'K2Model' ) ) {
			\JTable::addIncludePath( $_CB_framework->getCfg( 'absolute_path' ) . '/administrator/components/com_k2/tables' );

			return true;
		}

		return false;
	}

	/**
	 * @param string $title
	 * @return string
	 */
	private function cleanAlias( $title )
	{
		$alias	=	str_replace( '-', ' ', $title );
		$alias	=	trim( cbIsoUtf_strtolower( $alias ) );
		$alias	=	preg_replace( '/(\s|[^A-Za-z0-9\-])+/', '-', $alias );
		$alias	=	trim( $alias, '-' );

		return $alias;
	}
}