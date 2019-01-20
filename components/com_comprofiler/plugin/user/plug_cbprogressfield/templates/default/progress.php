<?php
/**
* Community Builder (TM)
* @version $Id: $
* @package CommunityBuilder
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

use CB\Database\Table\UserTable;
use CB\Database\Table\FieldTable;
use CBLib\Registry\GetterInterface;
use CBLib\Language\CBTxt;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var int        $complete
 * @var UserTable  $user
 * @var FieldTable $field
 * @var array      $userFields
 */

$bar				=	$field->params->get( 'prg_bar', 1, GetterInterface::STRING );
$barColor			=	null;

switch ( $bar ) {
	case 'blue':
		$barColor	=	' progress-bar-info';
		break;
	case 'red':
		$barColor	=	' progress-bar-danger';
		break;
	case 'green':
		$barColor	=	' progress-bar-success';
		break;
	case 'orange':
		$barColor	=	' progress-bar-warning';
		break;
}

$checklist			=	$field->params->get( 'prg_checklist', 3, GetterInterface::INT );

if ( $bar || $checklist ) { ?>
<div class="cbProgress">
	<?php if ( $bar ) { ?>
	<div class="cbProgressBar progress">
		<div class="progress-bar<?php echo $barColor; ?>" role="progressbar" aria-valuenow="<?php echo $complete; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $complete; ?>%;">
			<?php echo ( $field->params->get( 'prg_completeness', true, GetterInterface::BOOLEAN  ) ? CBTxt::T( 'PROFILE_COMPLETE_PERCENT', '[complete]%', array( '[complete]' => $complete ) ) : null ); ?>
		</div>
	</div>
	<?php } ?>
	<?php if ( $checklist ) { ?>
	<div class="cbProgressChecklist well well-sm">
		<?php
		foreach( $userFields as $userField ) {
			switch ( $checklist ) {
				case 1:
					if ( $userField->complete ) {
						?><div class="cbProgressChecklistComplete"><span class="fa fa-check text-success"></span> <?php echo $userField->title; ?></div><?php
					}
					break;
				case 2:
					if ( ! $userField->complete ) {
						?><div class="cbProgressChecklistInComplete"><span class="fa fa-times text-danger"></span> <?php echo $userField->title; ?></div><?php
					}
					break;
				case 3:
					?><div class="<?php echo ( $userField->complete ? 'cbProgressChecklistComplete' : 'cbProgressChecklistInComplete' ); ?>"><span class="fa <?php echo ( $userField->complete ? 'fa-check text-success' : 'fa-times text-danger' ); ?>"></span> <?php echo $userField->title; ?></div><?php
					break;
			}
		}
		?>
	</div>
	<?php } ?>
</div>
<?php } else {
	echo CBTxt::T( 'PROFILE_COMPLETE_PERCENT', '[complete]%', array( '[complete]' => $complete ) );
}
