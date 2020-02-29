/* exported L10N */
const L10N = {
    'general_camera_mode': 'Camera facing mode',
    'previewCamFlipHorizontal': 'Flip image from device cam horizontally',
    'general_videoHeight': 'Device cam picture height',
    'general_videoWidth': 'Device cam picture width',
    'user_interface_background_chroma': 'Chroma keying panel background image path',
    'user_interface_background_admin': 'Chemin de l\'image d\'arrière-plan du panneau d\'administration',
    'user_interface_background_image': 'Chemin de l\'image de fond',
    'send': 'Envoyer',
    'show_error_messages': 'Afficher les messages d\'erreur',
    'general_default_imagefilter': 'Filtre d\'image par défaut',
    'default_imagefilter': 'Choisissez le filtre d\'image',
    'general_disabled_filters': 'Filtres désactivés',
    'allow_delete': 'Autoriser la suppression de l\'image',
    'general_collage_cntdwn_time': 'Montage photo compte à rebours en secondes',
    'continuous_collage': 'Prendre un photo montage sans interruption',
    'delete': 'Effacer',
    'using_latest_version': 'Vous utilisez la dernière version de photobooth',
    'update_available': 'Il y a une mise à jour disponible',
    'test_update_available': 'Il y a une mise à jour de test disponible',
    'check_version': 'Vérifier la version',
    'current_version': 'Version actuelle',
    'available_version': 'Version disponible',
    'force_buzzer': 'masquer le déclencheur',
    'use_button': 'Utilisez le bouton matériel pour prendre une photo',
    'general_photo_key': 'Code clé qui déclenche une photo',
    'general_collage_key': 'Code clé qui déclenche une collage',
    'cups_button': 'Afficher le bouton CUPS',
    'gallery': 'Galerie',
    'startScreen': '<h1>Photobooth</h1><h2>Interface Web</h2> by Andr\u00e9 Rinas',
    'general_start_screen_title': 'Page d’accueil (Titre)',
    'general_start_screen_subtitle': 'Page d’accueil (Sous-titre)',
    'takePhoto': 'Prendre une photo!',
    'takeCollage': 'Prendre un photo montage!',
    'general_pictureRotation': 'Rotate photo after taking (in degrees)',
    'home': 'Accueil',
    'qr': 'QR Code',
    'mail': 'e-mail',
    'insertMail': 'Entrez votre adresse e-mail pour recevoir la photo.',
    'sendAllMail': 'Envoyez moi le lien de toutes les photos, dans les prochains jours',
    'newPhoto': 'Une autre',
    'newCollage': 'Une autre photo montage',
    'nextPhoto': 'Photo suivante',
    'busy': 'En cours de traitement',
    'busyCollage': 'En cours de traitement',
    'cheese': 'Cheeeeeeeese!',
    'cheeseCollage': 'Cheeeeeeeese 4 fois !',
    'qrHelp': 'Se connecter au wifi du <strong>photobooth</strong>, pour télécharger la photo sur votre smartphone.',
    'error': 'Il y a eu un problème. Merci de réessayer.',
    'reload': 'Recharger la page',
    'print': 'Imprimer',
    'printing': 'I\'impression a commencé',
    'save': 'Sauvegarder',
    'saving': 'En cours de sauvegarde',
    'success': 'Enregistré avec succès',
    'saveerror': 'Erreur',
    'close': 'Fermer',
    'general': 'Général',
    'folders': 'Dossiers',
    'commands': 'Les commandes',
    'reset': 'Réinitialiser',
    'remove_images': 'Delete images',
    'remove_mailtxt': 'Delete "mail-addresses.txt"',
    'remove_config': 'Delete personal configuration (my.config.inc.php)',
    'language': 'Choisissez la langue',
    'dev': 'Mode développeur',
    'file_format_date': 'Utilisez images au format date',
    'chroma_keying': 'Activez le fond vert',
    'keyingerror': 'L\'incrustation sur fond vert n\'est pas possible!',
    'use_print': 'Utiliser l\'impression',
    'use_qr': 'Utiliser le QR code',
    'general_webserver_ip': 'IP address of the Photobooth web server',
    'use_download': 'Autoriser les téléchargements',
    'print_qrcode': 'QR-Code sur l\'image en cours d\'impression',
    'show_gallery': 'Montrez la galerie',
    'scrollbar': 'Affichez la barre de défilement dans la galerie',
    'show_date': 'Montrez la date sous les images (fonctionne uniquement avec images au format de date)',
    'gallery_date_format': 'Format de date',
    'gallery_no_image': 'La galerie est vide. Faites des photos!',
    'use_mail': 'Utiliser le courrier électronique',
    'show_fork': 'Affichez le badge de la page Github',
    'general_cntdwn_time': 'Compte à rebours en secondes',
    'general_cheese_time': 'Définir le temps pour le Cheeeeeeeese! en millisecondes',
    'general_time_to_live': 'Show image after capture in milliseconds',
    'previewFromCam': 'Voir l\'aperçu par caméra',
    'previewCamTakesPic': 'Device cam takes picture',
    'newest_first': 'Affichez les dernières images en premier',
    'image_preview_before_processing': 'Preload image before processing',
    'folders_images': 'Dossier d\'images',
    'folders_thumbs': 'Dossier de vignettes',
    'folders_qrcodes': 'Dossier QR code',
    'folders_print': 'Dossier d\'impression',
    'folders_tmp': 'Dossier tmp',
    'folders_data': 'Dossier data',
    'folders_keying': 'Dossier font vert',
    'general_language': 'La langue',
    'keep_images': 'Keep original images in tmp folder',
    'send_all_later': 'Activez la case à cocher pour ajouter l\'adresse saisie à un fichier (par exemple, pour envoyer un courrier avec toutes les images ultérieurement)',
    'mail_host': 'Adresse mail de l\'hôte',
    'mail_username': 'Nom d\'utilisateur du compte de messagerie',
    'mail_password': 'Mot de passe',
    'mail_secure': 'Sécurité (tls ou ssl)',
    'mail_port': 'Port',
    'mail_fromAddress': 'Adresse mail de l\'expéditeur',
    'mail_fromName': 'Nom de l\'expéditeur',
    'mail_subject': 'objet du mail',
    'mail_text': 'Texte',
    'mailSent': 'E-mail envoyé',
    'mailSaved': 'E-mail address saved successfully',
    'mailError': 'Erreur d\'envoi d\'email',
    'commands_take_picture_cmd': 'Prendre une photo',
    'commands_take_picture_msg': 'Message de réussite pour prendre une photo',
    'commands_print_cmd': 'Commande d\'impression',
    'commands_print_msg': 'Message de réussite pour impression',
    'commands_exiftool_cmd': 'EXIFtool command',
    'commands_exiftool_msg': 'Success message for EXIF preservation',
    'really_delete': 'Vraiment supprimer toutes les images? Ça ne peut pas être annulé!',
    'event': 'Événement',
    'is_event': 'Événement',
    'event_textLeft': 'Texte à gauche',
    'event_textRight': 'Texte droit',
    'event_symbol': 'Icône',
    'symbol': 'Sélectionnez le symbole',
    'selectFilter': 'Filtre d\'image',
    'use_filter': 'Autorisez le filtre d\'image',
    'polaroid_effect': 'Effet Polaroid',
    'general_polaroid_rotation': 'Rotation de la photo polaroid',
    'use_collage': 'Autorisez la fonction du montage photo',
    'take_frame': 'Prendre une photo avec cadre',
    'general_take_frame_path': 'Frame',
    'print_frame': 'Imprimez le contour sur la photo',
    'print_frame_path': 'Frame',
    'is_textonprint': 'Imprimez un texte sur la photo',
    'print_line1': 'Texte sur la première ligne',
    'print_line2': 'Texte sur la seconde ligne',
    'print_line3': 'Texte sur la troisième ligne',
    'print_locationx': 'Coordonnée X',
    'print_locationy': 'Coordonnée Y',
    'print_rotation': 'Rotation du texte',
    'print_linespace': 'Interligne',
    'print_font_path': 'Font',
    'print_fontsize': 'Taille de la police',
    'user_interface': 'Interface utilisateur',
    'crop_onprint': 'Recadrer la photo à imprimer',
    'print_crop_width': 'Nouvelle largeur à l\'impression',
    'print_crop_height': 'Nouvelle hauteur à l\'impression',
    'jpeg_quality': 'JPEG qualité',
    'jpeg_quality_jpeg_quality_image': 'JPEG qualité des images (-1 ... 100)',
    'jpeg_quality_jpeg_quality_chroma': 'JPEG qualité pour chroma-keying (-1 ... 100)',
    'jpeg_quality_jpeg_quality_thumb': 'JPEG qualité des thumbnails (-1 ... 100)',
    'abort': 'Avorter',
    'user_interface_colors_primary': 'Primary color',
    'user_interface_colors_secondary': 'Secondary color',
    'user_interface_colors_font': 'Font color',
    'user_interface_colors_button_font': 'Buttons font color',
    'user_interface_colors_start_font': 'Start screen font color',
    'user_interface_colors_panel': 'Admin- and login panel color',
    'user_interface_colors_hover_panel': 'Admin- and login panel hover color',
    'user_interface_colors_border': 'Border color',
    'user_interface_colors_box': 'Field color',
    'user_interface_colors_gallery_button': 'Gallery button color',
    'user_interface_colors_countdown': 'Countdown color',
    'user_interface_colors_background_countdown': 'Countdown background color',
    'user_interface_colors_cheese': 'Cheeeeeeeese! color',
    'user_interface_font_size': 'Default font size',
    'rounded_corners': 'Rounded corners',
    'login': 'Login',
    'login_enabled': 'Login enabled',
    'login_username': 'Username',
    'login_password': 'Password',
    'login_invalid': 'Username or password is invalid!',
    'logout': 'Logout',
    'admin_panel': 'Admin panel',
    'protect_admin': 'Protect admin panel',
    'protect_index': 'Protect start screen',
    'slideshow': 'Diaporama',
    'slideshow_refreshTime': 'Actualiser la page après X secondes',
    'slideshow_pictureTime': 'Millisecondes une image est affichée',
    'randomPicture': 'Afficher des images aléatoires',
    'use_thumbs': 'Use thumbnails for slideshow',
    'preserve_exif_data': 'Preserve EXIF data'
}
