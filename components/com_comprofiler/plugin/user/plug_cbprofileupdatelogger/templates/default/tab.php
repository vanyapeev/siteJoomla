<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C)2005-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\TabTable;
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;
use CB\Plugin\ProfileUpdateLogger\Table\UpdateLogTable;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var UserTable        $user
 * @var UserTable        $viewer
 * @var TabTable         $tab
 * @var UpdateLogTable[] $rows
 * @var cbPageNav        $pageNav
 */

initToolTip();
?>
<div class="puLogFrontend">
	<table class="puLogRows table table-hover table-responsive">
		<thead>
			<tr>
				<th style="width: 25%;"><?php echo CBTxt::T( 'Field' ); ?></th>
				<th style="min-width: 25%;"><?php echo CBTxt::T( 'Old Value' ); ?></th>
				<th style="min-width: 25%;"><?php echo CBTxt::T( 'New Value' ); ?></th>
				<th class="text-center hidden-xs" style="width: 15%;"><?php echo CBTxt::T( 'Date' ); ?></th>
				<th class="text-center hidden-xs" style="width: 5%;"><?php echo CBTxt::T( 'By' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ( $rows ) foreach ( $rows as $row ) {
				$editByUser		=	( $row->get( 'editedbyid', 0, GetterInterface::INT ) == $row->get( 'profileid', 0, GetterInterface::INT ) ? CBTxt::T( 'Self' ) : CBuser::getInstance( $row->get( 'editedbyid', 0, GetterInterface::INT ), false )->getField( 'formatname', null, 'html', 'none', 'profile', 0, true ) );
				$editedBy		=	cbTooltip( null, ( $row->get( 'mode', 0, GetterInterface::INT ) ? CBTxt::T( 'BACKEND_USER', 'Backend: [user]', array( '[user]' => $editByUser ) ) : CBTxt::T( 'FRONTEND_USER', 'Frontend: [user]', array( '[user]' => $editByUser ) ) ), null, 'auto', null, null, null, 'data-hascbtooltip="true" data-cbtooltip-position-my="bottom center" data-cbtooltip-position-at="top center" data-cbtooltip-classes="qtip-simple"' );
				$oldValue		=	htmlspecialchars( $row->get( 'oldvalue', null, GetterInterface::RAW ) );

				if ( ( $oldValue === null ) || ( $oldValue === '' ) ) {
					$oldValue	=	CBTxt::T( '(empty)' );
				}

				$newValue		=	htmlspecialchars( $row->get( 'newvalue', null, GetterInterface::RAW ) );

				if ( ( $newValue === null ) || ( $newValue === '' ) ) {
					$newValue	=	CBTxt::T( '(empty)' );
				}
			?>
			<tr class="puLogRow">
				<td class="text-wrapall" style="width: 25%;"><span class="puLogRaw"><?php echo $row->get( 'fieldname', null, GetterInterface::STRING ); ?></span></td>
				<td class="text-wrapall" style="min-width: 25%;"><span class="puLogRaw"><?php echo $oldValue; ?></span></td>
				<td class="text-wrapall" style="min-width: 25%;"><span class="puLogRaw"><?php echo $newValue; ?></span></td>
				<td class="text-center hidden-xs" style="width: 15%;"><?php echo cbFormatDate( $row->get( 'changedate', null, GetterInterface::STRING ), true, false ); ?></td>
				<td class="text-center hidden-xs" style="width: 5%;"><span class="fa fa-<?php echo ( $row->get( 'mode', 0, GetterInterface::INT ) ? 'user-secret' : 'user' ) . ( $row->get( 'editedbyid', 0, GetterInterface::INT ) == $row->get( 'profileid', 0, GetterInterface::INT ) ? ' text-muted' : null ); ?>"<?php echo $editedBy; ?>></span></td>
			</tr>
			<?php } else { ?>
			<tr class="puLogRow puLogRowEmpty">
				<td colspan="5"><?php echo ( $viewer->get( 'id', 0, GetterInterface::INT ) == $user->get( 'id', 0, GetterInterface::INT ) ? CBTxt::T( 'You currently have no changes.' ) : CBTxt::T( 'This user currently has no changes.' ) ); ?></td>
			</tr>
			<?php } ?>
		</tbody>
		<?php if ( $tab->params->get( 'pulEnablePagingFE', true, GetterInterface::BOOLEAN ) && ( $pageNav->total > $pageNav->limit ) ) { ?>
		<tfoot>
			<tr>
				<td colspan="5" class="text-center"><?php echo $pageNav->getListLinks(); ?></td>
			</tr>
		</tfoot>
		<?php } ?>
	</table>
</div>