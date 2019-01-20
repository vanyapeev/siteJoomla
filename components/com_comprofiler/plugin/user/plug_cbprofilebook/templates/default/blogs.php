<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\ProfileBook\Table\EntryTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\ProfileBook\CBProfileBook;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var bool         $isOwner
 * @var bool         $isModerator
 * @var UserTable    $user
 * @var UserTable    $viewer
 * @var TabTable     $tab
 * @var EntryTable[] $rows
 * @var cbPageNav    $pageNav
 */

global $_CB_framework, $_PLUGINS;

initToolTip();

$js		=	"$( '.pbBlog .pbBlogIntroRows .cbMoreLessOpen' ).on( 'click', function() {"
		.		"$( this ).closest( '.pbBlogIntroRow' ).removeClass( 'col-sm-6' ).addClass( 'col-sm-12' );"
		.	"});"
		.	"$( '.pbBlog .cbMoreLess' ).cbmoreless();";

$_CB_framework->outputCbJQuery( $js, 'cbmoreless' );

$i		=	0;
?>
<div class="pbBlog">
	<?php echo implode( '', $_PLUGINS->trigger( 'pb_onBeforeDisplayBlog', array( &$rows, &$pageNav, $viewer, $user, $tab ) ) ); ?>
	<?php if ( $isOwner || $isModerator ) { ?>
	<div class="pbBlogHeader" style="margin-bottom: 10px;">
		<button type="button" onclick="location.href='<?php echo $_CB_framework->pluginClassUrl( $this->element, false, array( 'action' => 'entry', 'func' => 'new', 'mode' => 'b', 'userid' => $user->get( 'id', 0, GetterInterface::INT ) ) ); ?>';" class="pbButton pbButtonNew btn btn-success"><span class="fa fa-plus-circle"></span> <?php echo CBTxt::T( 'New Blog' ); ?></button>
	</div>
	<?php } ?>
	<div class="pbBlogRows">
		<?php
		if ( $rows ) foreach ( $rows as $row ) {
			$_PLUGINS->trigger( 'pb_onDisplayBlog', array( &$row, $viewer, $user, $tab ) );

			$i++;

			$menu					=	null;

			if ( $isModerator || $isOwner ) {
				$menuItems			=	'<ul class="pbBlogMenuItems dropdown-menu" style="display: block; position: relative; margin: 0;">'
									.		'<li class="pbBlogMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'edit', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-edit"></span> ' . CBTxt::T( 'Edit' ) . '</a></li>';

				if ( $row->get( 'published' ) == 1 ) {
					$menuItems		.=		'<li class="pbBlogMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to unpublish this blog?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'unpublish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-times-circle"></span> ' . CBTxt::T( 'Unpublish' ) . '</a></li>';
				} else {
					$menuItems		.=		'<li class="pbBlogMenuItem"><a href="' . $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'publish', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) . '"><span class="fa fa-check"></span> ' . CBTxt::T( 'Publish' ) . '</a></li>';
				}

				$menuItems			.=		'<li class="pbBlogMenuItem"><a href="javascript: void(0);" onclick="cbjQuery.cbconfirm( \'' . addslashes( CBTxt::T( 'Are you sure you want to delete this blog?' ) ) . '\' ).done( function() { window.location.href = \'' . addslashes( $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'func' => 'delete', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ) ) . '\'; })"><span class="fa fa-trash-o"></span> ' . CBTxt::T( 'Delete' ) . '</a></li>'
									.	'</ul>';

				$menu				=	cbTooltip( 1, $menuItems, null, 'auto', null, null, null, 'class="btn btn-default btn-xs" data-cbtooltip-menu="true" data-cbtooltip-classes="qtip-nostyle"' );
			}

			$odd					=	( $i & 1 );
		?>
		<?php if ( $i != 1 ) { ?>
		<?php if ( ! $odd ) { ?>
		<div class="pbBlogIntroRows row">
		<?php } ?>
		<div class="pbBlogIntroRow col-sm-6">
		<?php } ?>
		<div class="pbBlogRow<?php echo ( $i == 1 ? ' pbBlogFeaturedRow' : null ); ?> panel panel-default">
			<div class="pbBlogRowContainer panel-body">
				<div class="pbBlogRowHeader">
					<div class="pbBlogRowTitle text-center<?php echo ( $i == 1 ? ' text-large' : null ); ?>">
						<strong><a href="<?php echo $_CB_framework->pluginClassUrl( $this->element, true, array( 'action' => 'entry', 'id' => $row->get( 'id', 0, GetterInterface::INT ) ) ); ?>"><?php echo $row->get( 'postertitle', null, GetterInterface::STRING ); ?></a></strong>
					</div>
					<div class="pbBlogRowDate text-center text-small text-muted">
						<?php echo cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'PROFILEBLOG_DATE_FORMAT', 'M j, Y' ) ); ?>
						<?php if ( $row->get( 'editdate', null, GetterInterface::STRING ) && ( $row->get( 'editdate', null, GetterInterface::STRING ) != '0000-00-00 00:00:00' ) ) { ?>
							<span class="pbBlogRowNewEdited fa fa-edit" title="<?php echo htmlspecialchars( cbFormatDate( $row->get( 'editdate', null, GetterInterface::STRING ) ) ); ?>"></span>
						<?php } ?>
					</div>
				</div>
				<div class="pbBlogRowBlog cbMoreLess">
					<div class="cbMoreLessContent">
						<?php echo CBProfileBook::parseMessage( $row->get( 'postercomment', null, GetterInterface::HTML ), $tab ); ?>
					</div>
					<div class="cbMoreLessOpen fade-edge hidden">
						<a href="javascript: void(0);" class="cbMoreLessButton"><?php echo CBTxt::T( 'Read More' ); ?></a>
					</div>
				</div>
			</div>
			<?php if ( $menu ) { ?>
			<div class="pbBlogMenu btn-group">
				<button type="button" <?php echo $menu; ?>><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>
			</div>
			<?php } ?>
		</div>
		<?php if ( $i != 1 ) { ?>
		</div>
		<?php if ( $odd || ( $i == count( $rows ) ) ) { ?>
		</div>
		<?php } ?>
		<?php } ?>
		<?php if ( ( $i == 1 ) && ( count( $rows ) > 1 ) ) { ?>
		<div class="pbBlogDivider border-default"></div>
		<?php } ?>
		<?php } else { ?>
		<div class="pbBlogRow pbBlogEmpty">
			<?php echo ( $isOwner ? CBTxt::T( 'You currently have no blogs.' ) : CBTxt::T( 'This user currently has no blogs.' ) ); ?>
		</div>
		<?php } ?>
	</div>
	<?php if ( $tab->params->get( 'pbPagingEngabbled', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) { ?>
	<div class="pbBlogPaging text-center">
		<?php echo $pageNav->getListLinks(); ?>
	</div>
	<?php } ?>
	<?php echo implode( '', $_PLUGINS->trigger( 'pb_onAfterDisplayBlog', array( $rows, $pageNav, $viewer, $user, $tab ) ) ); ?>
</div>