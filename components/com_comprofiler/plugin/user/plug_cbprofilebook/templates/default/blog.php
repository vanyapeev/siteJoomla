<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2015 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CBLib\Application\Application;
use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CB\Plugin\ProfileBook\Table\EntryTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\ProfileBook\CBProfileBook;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var UserTable  $viewer
 * @var TabTable   $tab
 * @var EntryTable $row
 */

global $_CB_framework, $_PLUGINS;

initToolTip();

$integrations			=	implode( '', $_PLUGINS->trigger( 'pb_onViewBlog', array( &$row, $viewer, $tab ) ) );
$cbUser					=	CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false );
$menu					=	null;

if ( ( $viewer->get( 'id', 0, GetterInterface::INT ) && ( $viewer->get( 'id', 0, GetterInterface::INT ) == $row->get( 'posterid', 0, GetterInterface::INT ) ) ) || Application::MyUser()->isGlobalModerator() ) {
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
?>
<div class="pbBlog">
	<div class="pbBlogRows">
		<div class="pbBlogRow pbBlogDirectRow">
			<div class="pbBlogRowContainer">
				<div class="pbBlogRowHeader">
					<div class="pbBlogRowTitle text-center text-large">
						<strong><?php echo $row->get( 'postertitle', null, GetterInterface::STRING ); ?></strong>
					</div>
					<div class="pbBlogRowDate text-center text-small text-muted">
						<?php echo CBuser::getInstance( $row->get( 'posterid', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'list', 0, true ); ?>
						-
						<?php echo cbFormatDate( $row->get( 'date', null, GetterInterface::STRING ), true, false, CBTxt::T( 'PROFILEBLOG_DATE_FORMAT', 'M j, Y' ) ); ?>
						<?php if ( $row->get( 'editdate', null, GetterInterface::STRING ) && ( $row->get( 'editdate', null, GetterInterface::STRING ) != '0000-00-00 00:00:00' ) ) { ?>
							<span class="pbBlogRowNewEdited fa fa-edit" title="<?php echo htmlspecialchars( cbFormatDate( $row->get( 'editdate', null, GetterInterface::STRING ) ) ); ?>"></span>
						<?php } ?>
					</div>
				</div>
				<div class="pbBlogRowBlog">
					<?php echo CBProfileBook::parseMessage( $row->get( 'postercomment', null, GetterInterface::HTML ), $tab ); ?>
				</div>
			</div>
			<?php if ( $menu ) { ?>
			<div class="pbBlogMenu btn-group">
				<button type="button" <?php echo $menu; ?>><span class="fa fa-cog"></span> <span class="fa fa-caret-down"></span></button>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="pbBack text-right">
		<button type="button" onclick="location.href='<?php echo $_CB_framework->userProfileUrl( $row->get( 'posterid', 0, GetterInterface::INT ), true, $tab->get( 'tabid', 0, GetterInterface::INT ) ); ?>';" class="pbButton pbButtonBack btn btn-sm btn-default"><?php echo CBTxt::T( 'Back' ); ?></button>
	</div>
	<?php if ( $integrations ) { ?>
	<div class="pbBlogIntegrations border-default bg-muted">
		<?php echo $integrations; ?>
	</div>
	<?php } ?>
</div>