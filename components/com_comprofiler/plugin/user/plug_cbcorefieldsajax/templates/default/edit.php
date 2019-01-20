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
use CBLib\Language\CBTxt;
use CBLib\Registry\GetterInterface;

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var int        $loaded
 * @var string     $reason
 * @var string     $formatted
 * @var UserTable  $user
 * @var FieldTable $field
 * @var string     $saveUrl
 */

global $_CB_framework;

cbValidator::loadValidation();
initToolTip();

$mode	=	( $reason == 'list' ? $field->params->get( 'ajax_list_output', 2, GetterInterface::INT ) : $field->params->get( 'ajax_profile_output', 1, GetterInterface::INT ) );
?>
<div class="cbAjaxDefault cbAjaxContainer cbAjaxContainerEdit cb_template cb_template_<?php echo selectTemplate( 'dir' ); ?> cbClicksInside">
	<form action="<?php echo $saveUrl; ?>" name="cbAjaxForm" enctype="multipart/form-data" method="post" class="cbAjaxForm cbValidation cb_form form-auto">
		<div class="cbAjaxInput form-group cb_form_line clearfix">
			<div class="cb_field">
				<?php echo $formatted; ?>
			</div>
		</div>
		<div class="cbAjaxButtons form-group cb_form_line clearfix">
			<input type="submit" class="cbAjaxSubmit btn btn-primary" value="<?php echo htmlspecialchars( CBTxt::T( 'Update' ) ); ?>" />
			<input type="button" class="cbAjaxCancel btn btn-default<?php echo ( ( $reason == 'list' ) || ( $mode > 1 ) ? ' cbTooltipClose' : null ); ?>" value="<?php echo htmlspecialchars( CBTxt::T( 'Cancel' ) ); ?>" />
		</div>
		<?php echo cbGetSpoofInputTag( 'fieldclass' ) . cbGetRegAntiSpamInputTag(); ?>
	</form>
	<?php echo $this->reloadHeaders(); ?>
</div>