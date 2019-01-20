<?php
/**
* Community Builder (TM) cbprivacy French (France) language file Administration
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
'DISABLE_ME_e34c7e'	=>	'Désactivez moi',
'DELETE_ME_311f9c'	=>	'Supprimez moi',
'TAB_PRIVACY_PREFERENCES_eb5b1f'	=>	'Préférence de confidentialité de l\'onglet',
'FIELD_PRIVACY_PREFERENCES_fb019b'	=>	'Préférence de confidentialité du champ',
// 6 language strings from file plug_cbprivacy/xml/models/model.privacy.xml
'SAME_AS_GLOBAL_540a68'	=>	'Identique à la configuration globale',
'ENABLE_2faec1'	=>	'Activé',
'DISABLE_bcfacc'	=>	'Désactivé',
'BUTTON_87b776'	=>	'Bouton',
'ICON_817434'	=>	'Icône',
'TAGS_189f63'	=>	'Etiquettes',
// 11 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.cbprivacyoverrides.xml
'SELECT_TEMPLATE_TO_BE_USED_FOR_THIS_PRIVACY_SELECT_3737e0'	=>	'Sélectionnez le modèle à utiliser pour l\'ensemble de la confidentialité de CB. Si le modèle est incomplet, les fichiers manquants seront utilisés à partir du modèle par défaut. Les fichiers modèles sont situés à le répertoire suivant : components/com_comprofiler/plugin/user/plug_cbprivacy/templates/.',
'SELECT_THE_DEFAULT_PRIVACY_RULES_FOR_THIS_PRIVACY__3d9ac6'	=>	'Sélectionnez les règles de confidentialité par défaut pour les sélecteurs de confidentialité. La valeur par défaut s\'appliquera même si aucune confidentialité n\'existe pour un utilisateur.',
'ENABLE_OR_DISABLE_PUBLIC_PRIVACY_OPTION_FOR_THIS_P_6b2c96'	=>	'Activer ou désactiver l\'option de confidentialité publique. La confidentialité "Public" permet à quiconque de voir le contenu protégé si les profils sont affichés sur le site. Notez que le propriétaire du contenu et les modérateurs sont exemptés de cette règle de confidentialité.',
'ENABLE_OR_DISABLE_USERS_PRIVACY_OPTION_FOR_THIS_PR_a299b5'	=>	'Activer ou désactiver l\'option de confidentialité des utilisateurs. La confidentialité des utilisateurs permet uniquement aux utilisateurs enregistrés et connectés de voir le contenu protégé. Notez que le propriétaire du contenu et les modérateurs sont exemptés de cette règle de confidentialité.',
'ENABLE_OR_DISABLE_PRIVATE_PRIVACY_OPTION_FOR_THIS__9e72be'	=>	'Activer ou désactiver l\'option Privé de confidentialité. La confidentialité privée permet uniquement au propriétaire du contenu de le voir. Les modérateurs de note sont exemptés de cette règle de confidentialité.',
'ENABLE_OR_DISABLE_CONNECTIONS_PRIVACY_OPTION_FOR_T_ae1b1b'	=>	'Activer ou désactiver l\'option de confidentialité de Connexions. La confidentialité des connexions permet uniquement aux utilisateurs en relation avec le propriétaire de voir le contenu protégé. Notez que le propriétaire du contenu et les modérateurs sont exemptés de cette règle de confidentialité.',
'ENABLE_OR_DISABLE_CONNECTIONS_OF_CONNECTIONS_PRIVA_65fe27'	=>	'Activer ou désactiver l\'option de confidentialité Connections of Connections. La connectivité Connections of Connections permet uniquement utilisateurs en relation (Connexions) avec des propriétaires de voir le contenu protégé. Cela peut être combiné avec d\'autres règles de confidentialité telles que Connexions. Les modérateurs sont exemptés de cette règle de confidentialité.',
'ENABLE_OR_DISABLE_SELECTION_OF_CONNECTION_TYPE_PRI_aca287'	=>	'Activer ou désactiver la sélection des options de confidentialité du type de connexion. La confidentialité du type de connexion permet uniquement aux utilisateurs auxquels le propriétaire est connecté et d\'un type spécifique de voir le contenu protégé. Notez que le propriétaire du contenu et les modérateurs sont exemptés de cette règle de confidentialité.',
'SELECT_THE_CONNETION_TYPES_AVAILABLE_FOR_PRIVACY_C_6cbc16'	=>	'Sélectionnez les types de connexion disponibles pour la sélection.',
'ENABLE_OR_DISABLE_SELECTION_OF_VIEW_ACCESS_LEVEL_P_63b63c'	=>	'Activer ou désactiver la sélection des options de confidentialité du niveau d\'accès de visualisation. Afficher la confidentialité du niveau d\'accès permet uniquement aux utilisateurs ayant le niveau d\'accès de visualisation spécifié d\'afficher le contenu protégé. Notez que le propriétaire du contenu et les modérateurs sont exemptés de cette règle de confidentialité.',
'ENABLE_OR_DISABLE_SELECTION_OF_USERGROUP_PRIVACY_O_0d0263'	=>	'Activer ou désactiver la sélection des options de confidentialité du groupe d\'utilisateurs. La confidentialité du groupe d\'utilisateurs permet uniquement aux utilisateurs du groupe d\'utilisateurs spécifié d\'afficher du contenu protégé. Cela vérifie également les groupes d\'utilisateurs hérités (par exemple, Auteur est un enfant de Enregistré, donc si Enregistré est sélectionné, Auteur a également accès). Notez que le propriétaire du contenu et les modérateurs sont exemptés de cette règle de confidentialité.',
// 5 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.editprivacyrow.xml
'INPUT_THE_ASSET_FOR_THIS_PRIVACY_ASSET_DETERMINES__151fff'	=>	'Entrez l\'actif pour cette confidentialité. L\'actif détermine ce qui est protégé (par exemple, profile, profile.field.FIELD_ID).',
'ASSET_26e905'	=>	'Actif',
'SELECT_THE_PRIVACY_RULE_THIS_DETERMINES_WHO_CAN_AC_b79e2f'	=>	'Sélectionnez la règle de confidentialité. Cela détermine qui peut accéder au contenu protégé en fonction de la règle sélectionnée.',
'RULE_ab7a48'	=>	'Règle',
'INPUT_OWNER_OF_THIS_PRIVACY_AS_SINGLE_INTEGER_USER_e909bc'	=>	'Saisissez le propriétaire de cette confidentialité sous la forme du nombre entier Id_utilisateur',
// 1 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.showclosedrow.xml
'USER_ID_1edcda'	=>	'ID de l\'utilisateur',
// 6 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.showclosedrows.xml
'VIEW_4351cf'	=>	'Affichage',
'SEARCH_CLOSED_3ed1aa'	=>	'Recherche fermée...',
'ID_ASCENDING_ee74eb'	=>	'ID croissant',
'ID_DESCENDING_8ab4db'	=>	'ID décroissant',
'NAME_ASCENDING_2981b2'	=>	'Nom croissant',
'NAME_DESCENDING_209413'	=>	'Nom décroissant',
// 2 language strings from file plug_cbprivacy/xml/views/view.com_comprofiler.showprivacyrows.xml
'SEARCH_PRIVACY_6c5271'	=>	'Recherche confidentielle...',
'SELECT_RULE_452ff1'	=>	'- Sélectionnez une règle -',
);
