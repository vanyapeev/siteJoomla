<?php
/**
* Community Builder (TM) cb.pulog Greek (Greece) language file Frontend
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
'SELECT_TEMPLATE_TO_BE_USED_FOR_ALL_OF_CB_PROFILE_U_05b692'	=>	'Επιλέξτε το πρότυπο που θα χρησιμοποιηθεί για όλο το CB Profile Update Logger. Εάν το πρότυπο είναι ελλιπές τότε τα αρχεία που λείπουν θα συμπληρωθούν από αυτά του προεπιλεγμένου πρότυπου. Μπορείτε να βρείτε τα αρχεία πρότυπου στο: components/com_comprofiler/plugin/user/plug_cbprofileupdatelogger/templates/.',
'OPTIONALLY_ADD_A_CLASS_SUFFIX_TO_SURROUNDING_DIV_E_559f2f'	=>	'Προαιρετικά συμπληρώστε ένα πρόθεμα κλάσης στην ενότητα DIV που περικλείει όλα τα CB Profile Update Logger.',
'ENABLE_OR_DISABLE_AUTOMATIC_DELETION_OF_LOGS_WHEN__c527b5'	=>	'Ενεργοποίηση ή απενεργοποίηση της αυτόματης διαγραφής των logs (καταγραφών) όταν ένας χρήστης διαγραφεί.',
'ENABLE_OR_DISABLE_USAGE_OF_BACKEND_ADMINISTRATOR_M_1dbdc5'	=>	'Ενεργοποίηση ή απενεργοποίηση της χρήσης μενού διαχειριστή στην περιοχή διαχείρισης.',
'ADMIN_MENU_3d31a7'	=>	'Μενού Διαχειριστή',
'ENABLE_OR_DISABLE_LOGGING_OF_BACKEND_PROFILE_CHANG_b8e524'	=>	'Ενεργοποίηση ή απενεργοποίηση καταγραφής των αλλαγών προφίλ της περιοχής διαχείρισης.',
'BACKEND_2e427c'	=>	'Περιοχή Διαχειριστών',
'OPTIONALLY_INPUT_A_COMMA_SEPARATED_LIST_OF_USER_ID_340263'	=>	'Προαιρετική εισαγωγή λίστας id χρηστών, διαχωρισμένων με κόμμα, που θα αγνοηθούν όταν γίνεται έλεγχος για αλλαγές.',
'EXCLUDE_USERS_f9804a'	=>	'Εξαίρεση Χρηστών',
'OPTIONALLY_SELECT_FIELDS_TO_IGNORE_WHEN_CHECKING_F_05f34d'	=>	'Προαιρετική επιλογή πεδίων που θα αγνοηθούν όταν γίνεται έλεγχος για αλλαγές. Σημειώστε ότι το πεδίο του κωδικού αγνοείται πάντα.',
'EXCLUDE_FIELDS_922895'	=>	'Εξαίρεση Πεδίων',
'SELECT_FIELDS_b7951c'	=>	'- Επιλογή Πεδίων -',
'OPTIONALLY_SELECT_TYPES_OF_FIELDS_TO_IGNORE_WHEN_C_720812'	=>	'Προαιρετική επιλογή τύπου πεδίων που θα αγνοηθούν όταν γίνεται έλεγχος για αλλαγές. Σημειώστε ότι οι τύποι πεδίου κωδικού αγνοούνται πάντα.',
'EXCLUDE_FIELD_TYPES_43180b'	=>	'Εξαίρεση Τύπου Πεδίων',
'SELECT_FIELD_TYPES_21878c'	=>	'- Επιλογή Τύπου Πεδίων -',
'ENABLE_OR_DISABLE_NOTIFYING_MODERATORS_OF_FRONTEND_685ca6'	=>	'Ενεγοποίηση ή απενεργοποίηση της ειδοποίησης των συντονιστών για αλλαγές προφίλ στην περιοχή χρηστών.',
'THIS_TAB_CONTAINS_A_LOG_OF_PROFILE_UPDATES_MADE_BY_483741'	=>	'Αυτή η καρτέλα περιέχει καταγραφή των ενημερώσεων προφίλ που έχουν γίνει από χρήστες ή συντονιστές',
'UPDATE_LOG_cbc070'	=>	'Καταγραφή Ενημερώσεων',
'ENABLE_OR_DISABLE_DISPLAY_OF_THE_PROFILE_UPDATE_LO_2681f5'	=>	'Ενεργοποίηση ή απενεργοποίηση της εμφάνισης καταγραφής ενημερώσεων προφίλ στον ιδιοκτήτη του προφίλ εκτός από τους συντονιστές.',
'PROFILE_OWNER_06447f'	=>	'Ιδιοκτήτης Προφίλ',
'ENABLE_OR_DISABLE_USAGE_OF_PAGING_5b27ec'	=>	'Ενεργοποίηση ή απενεργοποίηση της χρήσης σελιδοποίησης.',
'INPUT_PAGE_LIMIT_PAGE_LIMIT_DETERMINES_HOW_MANY_CH_fe62d5'	=>	'Εισάγετε το όριο σελίδων. Το όριο σελίδων καθορίζει το πόσες αλλαγές θα εμφανίζονται ανά σελίδα.',
'LIMIT_80d267'	=>	'Όριο',
// 2 language strings from file plug_cbprofileupdatelog/library/Table/UpdateLogTable.php
'FIELD_NOT_SPECIFIED_f8ddb4'	=>	'Δεν καθορίστηκε πεδίο!',
'VALUE_IS_UNCHANGED_9d5852'	=>	'Η τιμή δεν άλλαξε!',
// 3 language strings from file plug_cbprofileupdatelog/library/Trigger/AdminTrigger.php
'PROFILE_UPDATE_LOG_46898e'	=>	'Καταγραφή Ενημέρωσης Προφίλ',
'LOG_ce0be7'	=>	'Καταγραφή',
'CONFIGURATION_254f64'	=>	'Διαμόρφωση',
// 5 language strings from file plug_cbprofileupdatelog/library/Trigger/UserTrigger.php
'EMPTY_9e65b5'	=>	'(κενό)',
'FIELD_CHANGED_OLD_TO_NEW'	=>	'<p><strong>[field]:</strong> "[old]" σε "[new]"</p>',
'A_PROFILE_HAS_BEEN_UPDATED_86d910'	=>	'Ένα προφίλ ενημερώθηκε!',
'USERNAME_HAS_UPDATED_THEIR_PROFILE_CHANGED_CHANGED_0b1ef6'	=>	'<a href="[url]">[username]</a> ενημέρωσε το προφίλ. Αλλαγές: [changed]. Εκκρεμείς Αλλαγές: [pending].<br /><br />[changes]',
'USER_HAS_UPDATED_THE_PROFILE_OF_USERNAME_CHANGED_C_d64443'	=>	'[user] ενημέρωσε το προφίλ του <a href="[url]">[username]</a>. Αλλαγές: [changed]. Εκκρεμείς Αλλαγές: [pending].<br /><br />[changes]',
// 9 language strings from file plug_cbprofileupdatelog/templates/default/tab.php
'FIELD_6f16a5'	=>	'Πεδίο',
'OLD_VALUE_56f05f'	=>	'Παλαιά Τιμή',
'NEW_VALUE_943f33'	=>	'Νέα Τιμή',
'BY_53e5aa'	=>	'Από',
'SELF_ad6e76'	=>	'Ίδιος',
'BACKEND_USER'	=>	'Περιοχή διαχείρισης: [user]',
'FRONTEND_USER'	=>	'Περιοχή χρηστών: [user]',
'YOU_CURRENTLY_HAVE_NO_CHANGES_c7ea23'	=>	'Προς το παρόν δεν έχετε αλλαγές.',
'THIS_USER_CURRENTLY_HAS_NO_CHANGES_6f157c'	=>	'Αυτός ο χρήστης δεν έχει αλλαγές προς το παρόν.',
);
