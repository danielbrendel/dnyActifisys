<?php

/*
    Actifisys (dnyActifisys) developed by Daniel Brendel

    (C) 2019 - 2023 by Daniel Brendel

    Version: 1.0
    Contact: dbrendel1988<at>gmail<dot>com
    GitHub: https://github.com/danielbrendel/

    Released under the MIT license
*/

return [
    'login' => 'Login',
    'register' => 'Registrieren',
    'news' => 'News',
    'tos' => 'ToS',
    'imprint' => 'Impressum',
    'contact' => 'Kontakt',
    'faq' => 'FAQ',
    'email' => 'E-Mail',
    'enteremail' => 'name@domain.tld',
    'password' => 'Passwort',
    'enterpassword' => 'Geben Sie Ihr Passwort ein',
    'recover_password' => 'Passwort wiederherstellen',
    'register_name' => 'Name',
    'register_email' => 'E-Mail',
    'register_password' => 'Passwort',
    'register_password_confirmation' => 'Passwort Bestätigung',
    'register_email_in_use' => 'Es existiert bereits ein Konto mit der angegebenen E-Mail Adresse',
    'messages' => 'Unterhaltungen',
    'notifications' => 'Benachrichtigungen',
    'create_activity' => 'Aktivität erstellen',
    'logout' => 'Logout',
    'settings' => 'Einstellungen',
    'cookie_consent_close' => 'Schließen',
    'location' => 'Ort',
    'title' => 'Titel',
    'description' => 'Beschreibung',
    'date' => 'Datum',
    'date_from' => 'Von',
    'date_till' => 'Bis',
    'limit' => 'Begrenzung',
    'create' => 'Erstellen',
    'close' => 'Schließen',
    'date_is_in_past' => 'Das angegebene Datum liegt in der Vergangenheit',
    'error' => 'Fehler',
    'success' => 'Erfolg',
    'no_information_given' => 'Keine Informationen angegeben',
    'actual_participants' => ':count Teilnehmer*Innen',
    'potential_participants' => ':count interessiert',
    'mail_user_participated_title' => 'Neue/r/* Teilnehmer*in!',
    'mail_salutation' => 'Liebe/r/* :name',
    'mail_user_participated_body' => '<a href="' . url('/') . '/user/:id">:name</a> ist jetzt ein/e/* Teilnehmer*in.',
    'mail_activity_open' => 'Aktivität öffnen',
    'mail_email_changed_title' => 'E-Mail wurde geändert',
    'mail_email_changed_body' => 'Deine E-Mail Adresse wurde zu :email geändert.',
    'mail_message_received_info' => 'Neue Nachricht',
    'mail_message_received_title' => 'Neue Nachricht',
    'mail_message_received_body' => 'Die folgende Nachricht wurde an dich gesandt:',
    'mail_message_open' => 'Nachricht öffnen',
    'mail_pw_changed_title' => 'Passwort geändert',
    'mail_pw_changed_body' => 'Dein Passwort wurde erfolgreich geändert.',
    'mail_password_reset_title' => 'Password zurücksetzen',
    'mail_password_reset_body' => 'Klicke auf den Link unten, um dein Passwort zurückzusetzen.',
    'mail_password_reset' => 'Zurücksetzen',
    'mail_registered_title' => 'Willkommen!',
    'mail_registered_body' => 'Es ist schön, dich als neues Mitglied begrüßen zu dürfen. Jetzt musst du nur noch deinen Account bestätigen. Dafür klicke einfach auf den Link unten.',
    'mail_registered_confirm' => 'Bestätigen',
    'activity_created' => 'Neue Aktivität wurde erstellt',
    'activity_not_found_or_locked' => 'Aktivit existiert nicht oder wurde gesperrt.',
    'user_commented_short' => 'Neuer Kommentar',
    'user_commented_long' => '<a href=":profile">:sender</a> hat deine <a href=":item">Aktivität</a> kommentiert: :message',
    'mail_subject_register' => 'Registrierung',
    'comment_added' => 'Kommentar hinzugefügt',
    'parent_post_not_found' => 'Eltern-Kommentar nicht gefunden',
    'user_participated_short' => 'Neue/r/* Teilnehmer*in',
    'user_participated_long' => '<a href=":profile">:name</a> nimmt nun an deiner <a href=":item">Aktivität</a> teil',
    'mail_user_participated' => 'Neue/r/* Teilnehmer*in',
    'added_as_participant' => 'Du nimmst nun an der Aktivität teil',
    'activity_limit_reached' => 'Sorry, aber die maximale Anzahl an Teilnehmer*Innen wurde erreicht',
    'removed_as_participant' => 'Du nimmst nicht mehr teil',
    'added_as_potential' => 'Du bist nun als Interessent markiert',
    'removed_as_potential' => 'Du bist jetzt kein Interessent mehr',
    'activity_locked' => 'Aktivität gesperrt',
    'insufficient_permissions' => 'Fehler: Unzureichende Zugrifffsrechte',
    'activity_canceled' => 'Aktivität wurde abgesagt',
    'activity_reported' => 'Aktivität wurde gemeldet',
    'comment_not_found' => 'Kommentar nicht gefunden',
    'comment_locked' => 'Kommentar wurde gesperrt',
    'comment_reported' => 'Kommentar wurde gemeldet',
    'comment_edited' => 'Kommentar wurde aktualisiert',
    'favorite_added' => 'Zu Favoriten hinzugefügt',
    'favorite_removed' => 'Von Favoriten entfernt',
    'product_installed' => 'Das Produkt wurde erfolgreich installiert. Dein aktuelles Passwort: :password. Bitte gehe nun zu den Profileinstellungen und ändere dein Passwort!',
    'contact_success' => 'Deine Anfrage wurde aufgenommen. Sie wird so schnell wie möglich bearbeitet.',
    'account_not_yet_confirmed' => 'Du musst erst deinen Account bestätigen',
    'account_deactivated' => 'Account deaktiviert',
    'login_welcome_msg' => 'Hi :name. Du wurdest erfolgreich angemeldet!',
    'login_failed' => 'Login fehlgeschlagen. Überprüfe deine Benutzerdaten oder setze dein Passwort zurück',
    'login_already_logged_in' => 'Du bist bereits eingeloggt',
    'logout_success' => 'Tschüss, bis bald!',
    'not_logged_in' => 'Du bist nicht eingeloggt',
    'pw_recovery_ok' => 'Eine Wiederherstellungs E-Mail wurde an dein Postfach verschickt. Bitte befolge die dortigen Anweisungen',
    'password_reset_ok' => 'Dein Passwort wurde aktualisiert. Du kannst dich jetzt mit dem neuen Passwort anmelden.',
    'register_confirm_email' => 'Willkommen! Bitte bestätige noch deinen Account. Dafür wurde dir eine E-Mail an dein angegebenes E-Mail Konto geschickt. Danach kannst du dich dann anmelden. <a href=":link">Erneut senden</a>',
    'register_confirmed_ok' => 'Dein Account wurde bestätigt. Du kannst dich jetzt einloggen.',
    'settings_saved' => 'Einstellungen erfolgreich gespeichert.',
    'faq_saved' => 'FAQ Eintrag gespeichert',
    'faq_removed' => 'FAQ Eintrag gelöscht',
    'env_saved' => 'Einstellungen der Umgebung gespeichert',
    'user_not_found' => 'Benutzer*in nicht gefunden',
    'saved' => 'Erfolgreich gespeichert!',
    'newsletter_in_progress' => 'Newsletter wird verschickt',
    'theme_created' => 'Theme wurde erstellt',
    'theme_default_saved' => 'Standard Theme wurde gesetzt!',
    'theme_edited' => 'Theme wurde aktualisiert',
    'theme_deleted' => 'Theme wurde gelöscht',
    'not_a_png_file' => 'Die Datei ist kein gültiges PNG Bild',
    'entity_locked' => 'Entität wurde gesperrt',
    'entity_deleted' => 'Entität wurde gelöscht',
    'entity_set_safe' => 'Entität wurde als sicher gesetzt',
    'formatted_project_name_saved' => 'Benutzerdefinierter Projektname gespeichert',
    'gender_male' => 'Männlich',
    'gender_female' => 'Weiblich',
    'gender_diverse' => 'Divers',
    'gender_unspecified' => 'Nicht spezifiziert',
    'user_locked' => 'Benutzer*in wurde gesperrt',
    'user_reported' => 'Benutzer*in wurde gemeldet',
    'user_ignored' => 'Benutzer*in wurde auf Ignorieren gesetzt',
    'user_not_ignored' => 'Benutzer*in wird nun nicht mehr ignoriert',
    'settings_avatar_invalid_image_type' => 'Ungültiges Bildformat',
    'password_saved' => 'Passwort wurde gespeichert',
    'email_saved' => 'E-Mail Adresse wurde aktualisiert',
    'notifications_saved' => 'Abonnements wurden aktualisiert',
    'account_deleted' => 'Account wurde gelöscht',
    'message_sent' => 'Nachricht wurde verschickt',
    'view_activities' => 'Aktivitäten',
    'do_filter' => 'Filter',
    'filter_by_location' => 'Filter per Ort',
    'filter_by_tag' => 'Filter per Hashtag',
    'filter_by_text' => 'Filter per Text',
    'collapse_filter' => 'Einklappen',
    'load_more' => 'Mehr laden',
    'no_more_activities' => 'Keine weiteren Aktivitäten',
    'no_participants' => 'Noch keine Teilnehmer*Innen',
    'no_one_interested' => 'Noch keine Interessenten',
    'share_whatsapp' => 'Teilen via WhatsApp',
    'share_twitter' => 'Teilen via Twitter',
    'share_facebook' => 'Teilen via Facebook',
    'share_sms' => 'Teilen via SMS',
    'share_email' => 'Teilen via E-Mail',
    'share_clipboard' => 'Zwischenablage',
    'cancel' => 'Abbrechen',
    'participate' => 'Teilnehmen',
    'not_participate' => 'Teilnahme absagen',
    'interested' => 'Interessiert',
    'not_interested' => 'Nicht interessiert',
    'type_a_message' => 'Gib eine Nachricht ein',
    'send' => 'Senden',
    'no_more_comments' => 'Keine weiteren Kommentare',
    'no_comments_yet' => 'Noch keine Kommentare',
    'profile_of' => 'Profil von :name',
    'age' => 'Alter',
    'gender' => 'Geschlecht',
    'activities' => 'Aktivitäten',
    'profile_actions' => 'Aktionen',
    'remove_favorite' => 'Von Favoriten entfernen',
    'add_favorite' => 'Zu Favoriten hinzufügen',
    'send_message' => 'Nachricht senden',
    'report' => 'Melden',
    'remove_from_ignore' => 'Nicht mehr ignorieren',
    'add_to_ignore' => 'Ignorieren',
    'profile' => 'Profil',
    'security' => 'Sicherheit',
    'membership' => 'Mitgliedschaft',
    'view_profile' => 'Profil ansehen',
    'name' => 'Name',
    'bio' => 'Bio',
    'avatar' => 'Avatar',
    'password_confirmation' => 'Passwort Bestätigung',
    'newsletter_notice' => 'Newsletter empfangen',
    'email_on_message_notice' => 'Benachrichtigung bei neuen Nachrichten',
    'email_on_comment_notice' => 'Benachrichtigung bei neuen Kommentaren',
    'email_on_participated_notice' => 'Benachrichtigung bei neuen Teilnehmer*Innen',
    'email_on_fav_created_notice' => 'Mich informieren, wenn ein Favorit eine neue Aktivität erstellt hat',
    'delete_account_notice' => 'Hier kannst du deinen Account löschen. Achtung: Diese Aktion kann nicht Rückgängig gemacht werden!',
    'message_create' => 'Nachricht erstellen',
    'no_messages' => 'Keine Nachrichten vorhanden',
    'message_thread' => 'Unterhaltung mit :name',
    'type_something' => 'Schreibe etwas',
    'text' => 'Text',
    'favorites' => 'Favoriten',
    'no_favorites_yet' => 'Favoriten werden hier angezeigt.',
    'birthday' => 'Geburtstag',
    'contact_name' => 'Dein Name',
    'contact_email' => 'Deine E-Mail',
    'contact_subject' => 'Betreff',
    'contact_body' => 'Nachricht',
    'submit' => 'Absenden',
    'time' => 'Uhrzeit',
    'lock' => 'Sperren',
    'edit' => 'Ändern',
    'participant_limit_reached_short' => 'Begrenzung wurde erreicht',
    'activity_edited' => 'Aktivität wurde aktualisiert',
    'user_not_existing_or_deactivated' => 'User existiert nicht oder ist deaktiviert',
    'edit_activity' => 'Aktivität ändern',
    'save' => 'Speichern',
    'cookie_consent' => 'Cookie consent',
    'about' => 'Über',
    'reg_info' => 'Registrierungs Info',
    'maintainer_area' => 'Maintainer area',
    'users' => 'Benutzer*innen',
    'newsletter' => 'Newsletter',
    'themes' => 'Themes',
    'logo' => 'Logo',
    'background' => 'Background',
    'bg_info' => 'Background',
    'bg_alpha' => 'Alpha value',
    'reports' => 'Meldungen',
    'project_name_formatted' => 'Formatted project name',
    'faq_remove' => 'Entfernen',
    'refresh' => 'Aktualisieren',
    'table_search' => 'Suchen',
    'table_show_entries' => 'Zeige Einträge',
    'faq_id' => 'ID',
    'faq_question' => 'Frage',
    'faq_answer' => 'Antwort',
    'faq_last_updated' => 'Zuletzt aktualisiert',
    'table_row_info' => '',
    'table_pagination_prev' => 'Vorherige',
    'table_pagination_next' => 'Nächste',
    'remove' => 'Entfernen',
    'environment' => 'Environment',
    'project_description' => 'Beschreibung',
    'project_name' => 'Projekt Name',
    'project_tags' => 'Tags',
    'project_smtp_host' => 'SMTP Host',
    'project_smtp_user' => 'SMTP User',
    'project_smtp_pw' => 'SMTP Passwort',
    'project_smtp_fromname' => 'SMTP From name',
    'project_smtp_fromaddress' => 'SMTP From address',
    'project_ga' => 'Google Analytics token',
    'project_lang' => 'Sprache',
    'project_twitter_news' => 'Twitter news handle',
    'project_helprealm_workspace' => 'HelpRealm workspace',
    'project_helprealm_token' => 'API Token',
    'project_helprealm_tickettypeid' => 'Ticket type ID',
    'get_user_details' => 'Details anzeigen',
    'deactivated' => 'Deaktiviert',
    'admin' => 'Admin',
    'maintainer' => 'Maintainer',
    'subject' => 'Betreff',
    'theme_create' => 'Erstellen',
    'theme_reset_default' => 'Standard wiederherstellen',
    'theme_name' => 'Name',
    'theme_default' => 'Als Standard setzen',
    'theme_edit' => 'Editieren',
    'theme_delete' => 'Löschen',
    'logo_info' => 'Hier kannst du das Logo setzen. Zur Zeit werden nur PNG Bilder unterstützt.',
    'headline_top' => 'Top headline',
    'headline_sub' => 'Sub headline',
    'report_id' => 'ID',
    'report_entity' => 'Entität',
    'report_type' => 'Typ',
    'report_count' => 'Anzahl',
    'report_lock' => 'Sperren',
    'report_delete' => 'Löschen',
    'report_safe' => 'Sicher',
    'report_confirm_lock' => 'Möchtest du diese Entität wirklich sperren?',
    'report_confirm_delete' => 'Möchtest du diese Entität wirklich löschen?',
    'report_confirm_safe' => 'Möchtest du diese Entität wirklich als sicher markieren?',
    'mail_footer' => 'Mit freundlichen Grüßen',
    'mail_user_participated_info' => 'Neuer Teilnehmer*In!',
    'message_received' => 'Neue Nachricht erhalten',
    'cancel_activity' => 'Aktivität absagen',
    'confirm_cancel_activity' => 'Möchtest du die Aktivität wirklich absagen?',
    'cancel_activity_reason' => 'Optionaler Grund',
    'mail_activity_created_title' => 'Neue Aktivität',
    'mail_activity_created_info' => 'Neue Aktivität',
    'mail_activity_created_body' => ':name hat eine neue Aktivität erstellt.',
    'mail_activity_canceled_title' => 'Aktivität wurde abgesagt',
    'mail_activity_canceled_info' => 'Aktivität wurde abgesagt',
    'mail_activity_canceled_body' => 'Die Aktivität ":title" von :name wurde abgesagt.',
    'activity_canceled_long' => ':name hat die Aktivität ":title" abgesagt',
    'activity_canceled_title' => 'Aktivität abgesagt',
    'no_limit' => 'Keine Beschränkung',
    'limit_count' => 'Für maximal :count Teilnehmer*Innen',
    'activity_canceled_message' => 'Diese Aktivität wurde abgesagt.',
    'no_reason_specified' => 'Es wurde kein Grund angegeben',
    'reason' => 'Grund',
    'activity_expired' => 'Aktivität abgelaufen',
    'activity_expired_message' => 'Diese Aktivität ist bereits abgelaufen.',
    'activity_running' => 'Aktivität läuft gerade',
    'mail_user_commented_title' => 'Neuer Kommentar hinzugefügt',
    'mail_user_commented_info' => 'Neuer Kommentar hinzugefügt',
    'mail_user_commented_body' => 'Du hast einen neuen Kommentar in deiner Aktivität erhalten.',
    'activity_not_found_or_canceled' => 'Aktivität exist entweder nicht oder wurde abgesagt',
    'user_not_found_or_deactivated' => 'Benutzer*in nicht gefunden oder deaktiviert',
    'file_uploaded' => 'Die Datei wurde hinzugefügt',
    'file_not_found_or_insufficient_permissions' => 'Datei nicht gefunden oder unzureichende Rechte',
    'file_deleted' => 'Datei gelöscht',
    'confirm_file_delete' => 'Bist du sicher, dass du die Datei löschen willst?',
    'images' => 'Bilder',
    'upload' => 'Hochladen',
    'image_upload' => 'Bilder Upload',
    'upload_image' => 'Lade ein Bild hoch',
    'email_on_act_canceled_notice' => 'Nachricht an mich, wenn eine Aktivität an der ich teilnehme abgesagt wird',
    'email_not_found' => 'E-Mail Adresse in unseren Datenbeständen nicht gefunden',
    'date_from_smaller_than_now' => 'Von-Datum liegt in der Vergangenheit!',
    'date_till_smaller_than_now' => 'Bis-Datum liegt in der Vergangenheit!',
    'till_date_must_not_be_less_than_from_date' => 'Bis-Datum darf nicht kleiner als Von-Datum sein!',
    'from' => 'Von',
    'till' => 'Bis',
    'only_gender' => 'Nur dieses Geschlecht',
    'your_gender_excluded' => 'Sorry, aber dein Geschlecht ist vom Ersteller ausgeschlossen worden',
    'all' => 'Alle',
    'mail_fav_created_title' => 'Erstellte Aktivität',
    'mail_fav_created_info' => 'Einer deiner Favoriten hat eine Aktivität erstellt',
    'mail_fav_created_body' => 'Die folgende Aktivität wurde von :creator erstellt',
    'gender_restricted' => 'Nicht für alle Geschlechter',
    'user_no_messages' => 'Die Nachricht konnte nicht übermittelt werden',
	'no_notifications_yet' => 'Benachrichtigungen werden hier angezeigt.',
	'password_reset' => 'Passwort zurücksetzen',
	'reset' => 'Zurücksetzen',
	'new_message' => '<a href=":profile">:name</a> hat dir eine Nachricht geschickt: :subject',
	'new_message_short' => ':name hat dir eine Nachricht geschickt',
	'added_to_favorites_short' => 'Jemand mag deine Aktivitäten',
	'added_to_favorites' => '<a href=":profile">:name</a> hat dich zu den eigenen Favoriten hinzugefügt',
	'user_replied_comment' => '<a href=":profile">:name</a> hat auf deinen <a href=":item">Kommentar</a> geantwortet.',
	'user_replied_comment_short' => 'Antwort auf Kommentar',
    'activity_created_short' => 'Neue Aktivität erstellt',
	'activity_created_long' => '<a href=":profile">:name</a> hat eine an <a href=":item">Aktivität</a> erstellt: :title',
	'activity_created' => 'Aktivität wurde erstellt',
    'verify_account' => 'Konto verifizieren',
    'identity_card_front' => 'Personalausweis Vorderseite',
    'identity_card_back' => 'Personalausweis Rückseite',
    'confirm_verify_permission' => 'Ich bestätige, dass ich das Recht habe, diese Daten zu übermitteln',
    'verify_account_ok' => 'Deine Anfrage wurde aufgenommen. Sie wird so schnell wie möglich bearbeitet.',
    'verify_permission_unconfirmed' => 'Du musst bestätigen, dass du das Recht hast, diese Daten zu übermitteln',
    'verification_in_progress' => 'Deine Verifizierung wird noch bearbeitet',
    'verification_succeeded' => 'Dein Account ist verifiziert!',
    'delete' => 'Löschen',
    'account_verification' => 'Account Bestätigung',
    'verify_id' => 'ID',
    'verify_user' => 'Benutzer*in',
    'verify_idcard_front' => 'Personalausweis Vorderseite',
    'idcard_front' => 'Vorderseite',
    'verify_idcard_back' => 'Personalausweis Rückseite',
    'idcard_back' => 'Rückseite',
    'verify_approve' => 'Bestätigen',
    'verify_decline' => 'Ablehnen',
    'decline_reason' => 'Ablehnungsgrund',
    'cookie_consent_description' => 'Der Text des Cookie Consents',
    'account_verified' => 'Benutzeraccount verifiziert!',
    'account_verification_declined' => 'Verifizierung der Benutzer*in abgelehnt',
    'mail_acc_verify_title' => 'Account Verifizierung',
    'mail_acc_verify_info' => 'Ergebnis der Account Verifizierung',
    'mail_acc_verify_body' => 'Das Ergebnis lautet wie folgt: :state - Begründung: :reason',
    'settings_category_invalid_image_type' => 'Ungültiges Bildformat',
    'categories' => 'Kategorien',
    'category_id' => 'ID',
    'category_name' => 'Name',
    'category_edit' => 'Editieren',
    'set_category_active' => 'Aktivieren',
    'set_category_inactive' => 'Deaktivieren',
    'category_inactive' => 'Inaktivitäts-Status',
    'category_added' => 'Kategorie hinzugefügt',
    'category_edited' => 'Kategorie aktualisiert',
    'category_status_changed' => 'Status wurde geändert',
    'category_create' => 'Erstellen',
    'category_description' => 'Beschreibung',
    'category_image' => 'Bild',
    'create_category' => 'Kategorie erstellen',
    'edit_category' => 'Kategorie ändern',
    'category' => 'Kategorie',
    'filter_by_category' => 'Per Kategorie filtern',
    'category_all' => '- Alle Kategorien -',
    'filter_options' => 'Filteroptionen anzeigen',
    'category_zero' => '- Keine -',
    'head_code' => 'Head code',
    'head_code_description' => 'Hier kann benutzerdefinierter Code für den Head Bereich spezifiziert werden.',
    'adcode' => 'Ad code',
    'adcode_description' => 'Here kann Code für Ads spezifiziert werden. Ads werden dann im Aktivitäten-Feed angezeigt.',
    'date_format' => 'd.m.Y H:i:s',
    'only_for_verified_users' => 'Diese Funktion steht nur verifizierten Benutzer*innen zur Verfügung.',
    'activity_verified_only' => 'Diese Aktivität ist nur für verifizierte Benutzer*innen',
    'only_verified' => 'Nur für verifizierte Benutzer*innen',
    'only_verified_long' => 'Diese Aktivität soll nur für verifizierte Nutzer zugänglich sein',
    'participating' => 'Teilnehmend',
    'not_yet_participating' => 'Noch nirgends teilnehmend',
    'not_yet_interested' => 'Noch nirgends als interessiert markiert',
    'message_list_phrase' => 'von',
    'email_changed' => 'E-Mail Adresse geändert',
    'password_changed' => 'Passwort geändert',
    'mail_password_reset_subject' => 'Passwort zurücksetzen',
    'copiedToClipboard' => 'Link in die Zwischenablage kopiert!',
    'expandThread' => 'Antworten anzeigen',
    'reply' => 'Antworten',
    'view' => 'Ansehen',
    'verifiedUser' => 'Identität verifiziert',
    'account_already_confirmed' => 'Das angegebene Konto wurde bereits aktiviert.',
    'register_confirm_resend' => 'Die Bestätigungs E-Mail wurde erneut gesendet. Bitte überprüfe dein Postfach. <a href=":link">Erneut senden</a>',
    'about_description' => 'Dieser Text wird unterhalb des Aktivitäten-Feeds für Gäste angezeigt.',
    'reply_thread' => 'Antwort hinzufügen',
    'date_format_display' => 'd.m.Y',
    'time_format_display' => 'H:i',
    'load_older_posts' => 'Ältere Posts laden',
    'imprint_description' => 'Hier kannst du den Inhalt des Impressums festlegen',
    'tos_description' => 'Hier kannst du den Inhalt der Nutzungsbedingungen festlegen',
    'reg_info_description' => 'Dieser formatierte Text wird im Registrierungsformular angezeigt',
    'register_confirm_token_not_found' => 'Der angegebene Hash konnte nicht gefunden werden',
    'add_location' => 'Ort hinzufügen',
    'location_name' => 'Name',
    'edit_location' => 'Ort bearbeiten',
    'location_id' => 'ID',
    'location_name' => 'Name',
    'location_edit' => 'Bearbeiten',
    'location_active' => 'Aktiv',
    'set_location_active' => 'Aktivieren',
    'set_location_inactive' => 'Deaktivieren',
    'locations' => 'Orte',
    'location_add' => 'Hinzufügen',
    'add' => 'Hinzufügen',
    'location_added' => 'Ort wurde hinzugefügt',
    'location_edited' => 'Ort wurde bearbeitet',
    'location_status_changed' => 'Aktivitätsstatus wurde aktualisiert',
    'locations_all' => '- Alle Orte -',
    'purchase_pro_mode' => 'Pro-Mode kaufen',
    'purchase_pro_mode_title' => 'Pro-Mode kaufen',
    'purchase_pro_mode_info' => 'Hier kannst du für :costs den Pro-Mode kaufen. Auf diese Art kannst du unseren Service unterstützen. Ausserdem wird dir danach keine Werbung mehr angezeigt.',
    'credit_or_debit_card' => 'Kredit-oder Debitkarte',
    'submit_payment' => 'Kaufen',
    'payment_service_deactivated' => 'Payment service ist deaktiviert',
    'user_not_found_or_locked_or_already_pro' => 'User nicht gefunden, gesperrt oder hat bereits Pro-Mode',
    'payment_failed' => 'Zahlung fehlgeschlagen',
    'payment_succeeded' => 'Die Zahlung war erfolgreich',
    'public_profile_label' => 'Gästen erlauben, mein Profil zu sehen',
    'allow_messages_label' => 'Private Nachrichten erhalten',
    'user_disabled_private_messaging' => 'Diese*r Nutzer*in hat private Nachrichten für sich abgeschaltet',
    'no_account_yet' => 'Noch kein Konto? Jetzt registrieren',
    'no_more_messages' => 'Keine weiteren Nachrichten',
    'running_activities' => 'Laufende Aktivitäten',
    'past_activities' => 'Vergangene Aktivitäten',
    'announcements' => 'Ankündigungen',
    'content' => 'Inhalt',
    'until' => 'Anzeigen bis',
    'announcement_created' => 'Die Ankündigung wurde erstellt',
    'additional_options' => 'Weitere Optionen',
    'clear_filter' => 'Filter zurücksetzen',
    'forum' => 'Forum',
    'forum_title' => 'Forum',
    'forum_subtitle' => 'Nimm an Diskussionen teil. Jede Diskussion bezieht sich auf ein bestimmtes Themengebiet',
    'search_for_name' => 'Name suchen',
    'search' => 'Suchen',
    'forums_no_forums_found' => 'Keine weitere Foren gefunden',
    'forum_not_found_or_locked' => 'Das angegebene Forum wurde entweder nicht gefunden oder ist gesperrt',
    'forums_no_threads_found' => 'Keine weiteren Beiträge gefunden',
    'thread_not_found_or_locked' => 'Der angegebene Beitrag wurde entweder nicht gefunden oder ist gesperrt',
    'thread' => 'Beitrag',
    'forums_no_posts_found' => 'Keine weiteren Postings gefunden',
    'thread_created' => 'Neuer Beitrag wurde erfolgreich erstellt',
    'create_thread' => 'Beitrag erstellen',
    'title' => 'Titel',
    'enter_title' => 'Gib einen Titel ein',
    'message' => 'Nachricht',
    'enter_message' => 'Gib hier deine Nachricht ein',
    'thread_replied' => 'Antwort erstellt',
    'set_thread_sticky' => 'Den Beitrag anheften',
    'thread_edited' => 'Beitrag wurde aktualisiert',
    'edit_thread' => 'Beitrag bearbeiten',
    'set_thread_locked' => 'Beitrag sperren',
    'thread_not_found_or_locked' => 'Beitrag wurde nicht gefunden oder ist gesperrt',
    'no_reply_to_locked_thread' => 'Dieser Beitrag wurde gesperrt',
    'search_for_thread' => 'Suche nach Beitrag',
    'forum_post_reported' => 'Forum-Posting wurde gemeldet',
    'forum_post_not_found_or_locked' => 'Forum-Posting nicht gefunden oder gesperrt',
    'single_post' => 'Einzelnen Beitrag ansehen',
    'confirmLockForumPost' => 'Möchtest du dieses Posting sperren?',
    'forum_post_locked' => 'Dieser Beitrag wurde gesperrt',
    'forum_post_locked_ok' => 'Der Post wurde gesperrt',
    'forum_post_edited' => 'Posting wurde aktualisiert',
    'forum_post_edited_info' => 'Aktualisiert',
    'forums' => 'Foren',
    'forum_id' => 'ID',
    'forum_name' => 'Name',
    'forum_description' => 'Beschreibung',
    'forum_lock' => 'Sperren',
    'forum_remove' => 'Löschen',
    'forum_created' => 'Forum wurde erstellt',
    'forum_create' => 'Forum erstellen',
    'forum_edited' => 'Forum wurde aktualisiert',
    'forum_locked' => 'Forum wurde gesperrt',
    'forum_removed' => 'Forum wurde gelöscht',
    'forum_edit' => 'Forum bearbeiten',
    'forum_reply_short' => 'Neue Antwort im Forum',
    'forum_reply_long' => ':name hat auf einen Forenbeitrag geantwortet: <a href=":url">:thread</a>',
    'go_back' => 'Zurück',
    'forum_remove_confirm' => 'Soll diese Forenrubrik wirklich gelöscht werden?',
    'marketplace' => 'Markthalle',
    'marketplace_subtitle' => 'Finde interessante Anbieter',
    'category_all' => '- Alle -',
    'marketplace_advert_by' => 'Von :name',
    'choose_market_category' => 'Hier Kategorie auswählen',
    'marketplace_no_adverts_found' => 'Keine weiteren Einträge',
    'marketplace_create_banner_hint' => 'Der Banner sollte eine Auflösung von 425x155 Pixel haben',
    'marketplace_create_advert_title' => 'Anzeige erstellen',
    'marketplace_create_title' => 'Bitte gib einen Titel ein',
    'marketplace_create_description' => 'Bitte gib eine Beschreibung davon ein, was du anbietest',
    'marketplace_create_link' => 'Bitte gib hier einen Link zu deiner Webpräsenz ein',
    'marketplace_advert_created' => 'Deine Anzeige wurde erfolgreich erstellt',
    'marketplace_list_adverts_title' => 'Deine Anzeigen',
    'choose_category' => '- Kategorie auswählen -',
    'marketplace_edit_advert' => 'Anzeige bearbeiten',
    'marketplace_advert_edited' => 'Deine Anzeige wurde erfolgreich aktualisiert',
    'confirm_delete_marketadvert' => 'Möchtest du diese Anzeige wirklich löschen?',
    'marketplace_advert_deleted' => 'Deine Anzeige wurde erfolgreich entfernt',
    'marketplace_advert_reported' => 'Die Anzeige wurde gemeldet',
    'linkfilter_title' => 'Besuchen von :url',
    'linkfilter_hint' => 'Du bist gerade dabei, die Präsenz :url zu besuchen. :project ist nicht verantwortlich für dessen Inhalt. Möchtest du fortfahren?',
    'linkfilter_visit' => 'Verstanden und weiter',
    'marketplace_text' => 'Inhalt der Beschreibung der Markthalle',
    'send_image' => 'Bild senden',
    'image_posted' => 'Bild wurde gesendet',
    'post_upload_size_exceeded' => 'Die Datei ist zu groß',
    'image_invalid_file_type' => 'Ungültige Bilddatei',
    'gallery' => 'Galerie',
    'gallery_subtitle' => 'Die neusten Bilder aus der Community',
    'gallery_submit' => 'Hochladen',
    'gallery_upload' => 'In die Galerie hochladen',
    'gallery_no_items_found' => 'Keine weiteren Einträge',
    'gallery_item_added' => 'Bild wurde der Galerie hinzugefügt',
    'gallery_item_deleted' => 'Bild wurde aus der Galerie entfernt',
    'gallery_item_reported' => 'Bild wurde gemeldet',
    'gallery_item_by' => 'Von :name',
    'gallery_text' => 'Inhalt der Beschreibung der Galerie',
    'community' => 'Community',
    'confirm_delete_gallery_item' => 'Möchtest du dieses Bild wirklich löschen?',
    'image' => 'Bild',
    'mail_activity_upcoming_title' => 'Erinnerung zur anstehenden Aktivität',
    'mail_activity_upcoming_info' => 'Morgen steht eine Aktivität an',
    'mail_activity_upcoming_body' => 'Du bist als Teilnehmer*in bei :title von :name, welche morgen stattfindet. Du kannst über untenstehenden Button zur Aktivität navigieren.',
    'activity_upcoming' => 'Bevorstehende Aktivität',
    'activity_upcoming_long' => 'Die Aktivität <a href=":item">:title</a> findet morgen statt.',
    'email_on_act_upcoming_notice' => 'Ich möchte an bevorstehende Aktivitäten erinnert werden',
    'invalid_marketplace_banner_image' => 'Die angegebene Datei ist kein gültiges Bild',
    'tags' => 'Tags',
    'tags_example_placeholder' => 'Beispiel: tagbsp1 tag-bsp2 tag-langes-bsp3',
    'no_tags_specified' => 'Keine Tags angegeben',
    'clear_tag' => '#:tag leeren',
    'image_sent' => 'Bild gesendet',
    'display_date_format' => 'd.m.Y',
    'display_time_format' => 'H:i:s',
    'gallery_thread_added' => 'Kommentar wurde hinzugefügt',
    'gallery_no_items_found' => 'Keine weiteren Einträge',
    'gallery_thread_item_reported' => 'Der Kommentar wurde gemeldet',
    'confirmLocGalleryThreadItem' => 'Möchtest Du den Kommentar wirklich sperren?',
    'gallery_thread_item_locked' => 'Der Kommentar wurde gesperrt',
    'gallery_thread_item_edited' => 'Der Kommentar wurde aktualisiert',
    'user_gallery_item_commented_short' => 'Neuer Kommentar',
    'user_gallery_item_commented_long' => '<a href=":profile">:name</a> hat deinen <a href=":item">Beitrag</a> in der Galerie kommentiert',
    'edit_comment' => 'Kommentar bearbeiten',
    'view_visits' => 'Besucher-Statistiken',
    'visits' => 'Besucher',
    'last_week' => 'Letzte Woche',
    'last_two_weeks' => 'Letzte zwei Wochen',
    'last_month' => 'Letzter Monat',
    'last_three_months' => 'Letztes Quartal',
    'last_year' => 'Letztes Jahr',
    'currently_online' => 'Jetzt online:',
    'range' => 'Bereich:',
    'select_range' => '- Bereich auswählen - ',
    'sum' => 'Summe:',
    'avg_per_day' => 'Durchschnitt am Tag:',
    'avg_per_hour' => 'Durchschnitt pro Stunde:',
    'custom_page_not_found' => 'Die Seite konnte nicht gefunden werden',
    'upcoming_activities' => 'Anstehende Aktivitäten',
    'add_as_participant' => 'Mich als Teilnehmer*in hinzufügen'
];
