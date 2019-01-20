<?php
/**
* Community Builder (TM) cbprivacy Dutch (Netherlands) language file Administration
* @version $Id:$
* @copyright (C) 2004-2017 www.joomlapolis.com / Lightning MultiCom SA - and its licensors, all rights reserved
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

/**
* WARNING:
* Do not make changes to this file as it will be over-written when you upgrade CB.
* To localize you need to create your own CB language plugin and make changes there.
*/

defined('CBLIB') or die();

return	array(
// 4 language strings from file plug_cbprivacy/cbprivacy.xml
'DISABLE_ME_e34c7e'	=>	'Schakel mij uit',
'DELETE_ME_311f9c'	=>	'Verwijder mij',
'TAB_PRIVACY_PREFERENCES_eb5b1f'	=>	'Tab privacy instellingen',
'FIELD_PRIVACY_PREFERENCES_fb019b'	=>	'Veld privacy voorkeuren',
// 6 language strings from file plug_cbprivacy/xml/models/model.privacy.xml
'SAME_AS_GLOBAL_540a68'	=>	'Hetzelfde als standaard',
'ENABLE_2faec1'	=>	'Inschakelen',
'DISABLE_bcfacc'	=>	'Uitschakelen',
'BUTTON_87b776'	=>	'Button',
'ICON_817434'	=>	'Icoon',
'TAGS_189f63'	=>	'Tags',
// 11 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.cbprivacyoverrides.xml
'SELECT_TEMPLATE_TO_BE_USED_FOR_THIS_PRIVACY_SELECT_3737e0'	=>	'Select template to be used for this privacy selector. If template is incomplete then missing files will be used from the default template. Template files can be located at the following location: components/com_comprofiler/plugin/user/plug_cbprivacy/templates/.',
'SELECT_THE_DEFAULT_PRIVACY_RULES_FOR_THIS_PRIVACY__3d9ac6'	=>	'Select the default privacy rules for this privacy selector. The default will apply even if no privacy exists for a user.',
'ENABLE_OR_DISABLE_PUBLIC_PRIVACY_OPTION_FOR_THIS_P_6b2c96'	=>	'Enable or disable Public privacy option for this privacy selector. Public privacy allows anyone to see the protected content. Note the content owner and moderators are exempt from this privacy rule.',
'ENABLE_OR_DISABLE_USERS_PRIVACY_OPTION_FOR_THIS_PR_a299b5'	=>	'Enable or disable Users privacy option for this privacy selector. Users privacy allows only registered and logged in users to see the protected content. Note the content owner and moderators are exempt from this privacy rule.',
'ENABLE_OR_DISABLE_PRIVATE_PRIVACY_OPTION_FOR_THIS__9e72be'	=>	'Enable or disable Private privacy option for this privacy selector. Private privacy allows only the owner of the content to see it. Note moderators are exempt from this privacy rule.',
'ENABLE_OR_DISABLE_CONNECTIONS_PRIVACY_OPTION_FOR_T_ae1b1b'	=>	'Enable or disable Connections privacy option for this privacy selector. Connections privacy allows only users the owner is connected to to see the protected content. Note the content owner and moderators are exempt from this privacy rule.',
'ENABLE_OR_DISABLE_CONNECTIONS_OF_CONNECTIONS_PRIVA_65fe27'	=>	'Enable or disable Connections of Connections privacy option for this privacy selector. Connections of Connections privacy allows only connections of the owners connections to see the protected content. This can be combined with other privacy rules like Connections. Note moderators are exempt from this privacy rule.',
'ENABLE_OR_DISABLE_SELECTION_OF_CONNECTION_TYPE_PRI_aca287'	=>	'Enable or disable selection of connection type privacy options for this privacy selector. Connection Type privacy allows only users the owner is connected to and of a specific type to see the protected content. Note the content owner and moderators are exempt from this privacy rule.',
'SELECT_THE_CONNETION_TYPES_AVAILABLE_FOR_PRIVACY_C_6cbc16'	=>	'Select the connetion types available for privacy control.',
'ENABLE_OR_DISABLE_SELECTION_OF_VIEW_ACCESS_LEVEL_P_63b63c'	=>	'Enable or disable selection of view access level privacy options for this privacy selector. View Access Level privacy allows only users with the specified view access level to see protected content. Note the content owner and moderators are exempt from this privacy rule.',
'ENABLE_OR_DISABLE_SELECTION_OF_USERGROUP_PRIVACY_O_0d0263'	=>	'Enable or disable selection of usergroup privacy options for this privacy selector. Usergroup privacy allows only users with the specified usergroup to see protected content. This will also check inherited usergroups (e.g. Author is child of Registered so if Registered is selected Author also has access). Note the content owner and moderators are exempt from this privacy rule.',
// 5 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.editprivacyrow.xml
'INPUT_THE_ASSET_FOR_THIS_PRIVACY_ASSET_DETERMINES__151fff'	=>	'Input the asset for this privacy. Asset determines what is being protected (e.g. profile, profile.field.FIELD_ID).',
'ASSET_26e905'	=>	'Schakel het gebruik van het backend-beheerdersmenu in of uit.',
'SELECT_THE_PRIVACY_RULE_THIS_DETERMINES_WHO_CAN_AC_b79e2f'	=>	'Select the privacy rule. This determines who can access the protected content based off the rule selected.',
'RULE_ab7a48'	=>	'regel',
'INPUT_OWNER_OF_THIS_PRIVACY_AS_SINGLE_INTEGER_USER_e909bc'	=>	'Input owner of this privacy as single integer user_id.',
// 1 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.showclosedrow.xml
'USER_ID_1edcda'	=>	'Gebruikers ID',
// 6 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.showclosedrows.xml
'VIEW_4351cf'	=>	'Weergeven',
'SEARCH_CLOSED_3ed1aa'	=>	'Zoeken afgesloten...',
'ID_ASCENDING_ee74eb'	=>	'ID oplopend',
'ID_DESCENDING_8ab4db'	=>	'ID aflopend ',
'NAME_ASCENDING_2981b2'	=>	'Naam aflopend ',
'NAME_DESCENDING_209413'	=>	'Naam oplopend',
// 2 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.showprivacyrows.xml
'SEARCH_PRIVACY_6c5271'	=>	'Zoek privacy',
'SELECT_RULE_452ff1'	=>	'- Selecteer regel -',
);
