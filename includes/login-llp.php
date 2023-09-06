<?php
// Personnaliser le logo de la page de connexion
function leoleplug_custom_login_logo() {
    echo '<style>
        body.login #login h1 a {
            background-image: url(' . plugin_dir_url(__FILE__) . 'content/logo-llp.png);
            background-size: contain;
            width: 100px; // Largeur du logo
            height: 100px; // Hauteur du logo
        }
    </style>';
}

add_action('login_head', 'leoleplug_custom_login_logo');