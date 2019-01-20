<?php
/**
* Community Builder (TM) cb.pulog French (France) language file Frontend
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
// 23 language strings from file plug_cbprofileupdatelog/cb.pulog.xml
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Sélectionner le template à utiliser pour tous les profils de CB. Si le template est incomplet alors les fichiers manquants seront remplacés par ceux du template par défaut. Les fichiers du template peuvent être trouvés à l\'emplacement suivant: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'Ajoutez éventuellement un suffixe de classe pour DIV enfermant tous éléments CB relatifs à CB Profile Update Logger.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Activer ou désactiver la suppression automatique des journaux lorsqu\'un utilisateur est supprimé.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Activer ou désactiver l\'utilisation du menu administrateur backend.',
'ADMIN_MENU_3d31a7'	=>	'Menu Admin',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Activer ou désactiver la journalisation des modifications du profil en backend.',
'BACKEND_2e427c'	=>	'Backend',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Entrez éventuellement une liste d\'ID utilisateurs à ignorer, séparés par des virgules,  lors de la vérification des modifications.',
'EXCLUDE_USERS_f9804a'	=>	'Exclure le Utilisateurs',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Vous pouvez éventuellement sélectionner les champs dont les valeurs sont masquées dans les notifications de modification de profil. Notez que le champ de mot de passe est toujours masqué.',
'EXCLUDE_FIELDS_922895'	=>	'Exclure les Champs',
'SELECT_FIELDS_b7951c'	=>	'-Sélectionnez les champs-',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Vous pouvez éventuellement sélectionner les champs à ignorer dans les notifications de modifications. Notez que le champ de mot de passe est toujours ignoré.',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Type de Champs à exclure',
'SELECT_FIELD_TYPES_21878c'	=>	'-Sélectionner les Types de Champs',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Activer ou désactiver la notification des modérateurs de modifications de profil réalisée en Frontend',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Cet onglet contient le journal des modifications de profil exécutées par l\'utilisateur ou un modérateur/administrateur',
'UPDATE_LOG_cbc070'	=>	'Journal de modification de profil',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Activer ou désactiver l\'affichage des modifications de profil faites par le propriétaire en plus de celles faites par un modérateur',
'PROFILE_OWNER_06447f'	=>	'Propriétaire du profil',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Activer ou désactiver l\'utilisation de la pagination.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Nb d\'entrées max par page. Limite le nombre d\'entrée par page. ',
'LIMIT_80d267'	=>	'Nb d\'entrées max par page',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Champ non spécifié',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Vous n\'avez pas modifié le contenu du champ !',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Journal des modification de profils',
'LOG_ce0be7'	=>	'Journal',
'CONFIGURATION_254f64'	=>	'Configuration',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(vide)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong>"[old]" à "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'Un profil a été modifié !',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'<a href="[url]">[username]</a> a modifié son profil. Modifié: [changed]. En Attente de modification: [pending].<br /><br />[changes]\'',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'[user] a modifié le profil de <a href="[url]">[username]</a>. Modifié: [changed]. En Attente de modification: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Champ',
'OLD_VALUE_56f05f'	=>	'Ancienne Valeur',
'NEW_VALUE_943f33'	=>	'Nouvelle Valeur',
'BY_53e5aa'	=>	'Par',
'SELF_ad6e76'	=>	'auto',
'BACKEND_USER'	=>	'Backend : [user]',
'FRONTEND_USER'	=>	'Frontend : [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'Vous n\'avez apporté aucune modification au profil',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Cet utilisateur n\'a apporté aucune modification au profil.',
);
