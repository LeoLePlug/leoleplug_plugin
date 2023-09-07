<?php
// Fonction pour ajouter le lien "Support" dans la barre supérieure (en haut)
function leoleplug_add_custom_link() {
    global $wp_admin_bar;

    // Ajouter le lien de support à la barre supérieure sans logo
    $wp_admin_bar->add_menu(array(
        'id' => 'leoleplug_support',
        'title' => 'Support',
        'href' => 'https://espace-membres.leoleplug.com/',
    ));
}

add_action('wp_before_admin_bar_render', 'leoleplug_add_custom_link');

// Ajouter le lien de support dans le menu à gauche
function leoleplug_add_support_menu() {
    add_menu_page(
        'LeoLePlug Support',
        'Support',
        'manage_options',
        'leoleplug-support',
        'leoleplug_redirect_to_support',
        plugin_dir_url(dirname(__FILE__)) . 'content/icon-llp.png', 
        26
    );
}

// Fonction pour rediriger vers l'URL du support
function leoleplug_redirect_to_support() {
    wp_redirect('https://espace-membres.leoleplug.com/');
    exit;
}

add_action('admin_menu', 'leoleplug_add_support_menu');

// Ajouter du CSS personnalisé pour ajuster la taille de l'icône du menu Support
function leoleplug_custom_menu_icon_css() {
    echo '<style>
        #adminmenu #toplevel_page_leoleplug-support .wp-menu-image img {
            width: 20px; /* Largeur de l\'icône */
            height: 20px; /* Hauteur de l\'icône */
        }
    </style>';
}

add_action('admin_head', 'leoleplug_custom_menu_icon_css');
