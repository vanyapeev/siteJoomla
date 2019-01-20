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

if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }

/**
 * @var int        $loaded
 * @var string     $reason
 * @var string     $formatting
 * @var string     $formatted
 * @var UserTable  $user
 * @var FieldTable $field
 * @var string     $editUrl
 */

global $_CB_framework;

include_once CBfield_ajaxfields::getTemplate( $field->params->get( 'ajax_template', null, GetterInterface::STRING ), 'display_jquery', false );

$mode			=	( $reason == 'list' ? $field->params->get( 'ajax_list_output', 2, GetterInterface::INT ) : $field->params->get( 'ajax_profile_output', 1, GetterInterface::INT ) );
$ajaxOutput		=	( ( $reason == 'list' ) || ( $mode > 1 ) ? ' data-cbajaxfield-mode="' . ( $mode == 3 ? 'modal' : 'tooltip' ) . '" data-cbajaxfield-classes="cbAjaxDefault"' : null );
?>
<div class="cbAjaxDefault cbAjaxContainer cbAjaxContainerDisplay<?php echo ( in_array( $formatting, array( 'span', 'none' ) ) ? ' cbAjaxContainerInline' : null ); ?> cbClicksInside" data-cbajaxfield-url="<?php echo $editUrl; ?>"<?php echo $ajaxOutput; ?>>
	<div class="cbAjaxValue cbAjaxToggle fa-before fa-pencil">
		<?php echo $formatted; ?>
	</div>
	<?php echo $this->reloadHeaders(); ?>
</div>