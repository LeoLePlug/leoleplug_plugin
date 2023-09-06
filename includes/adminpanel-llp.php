<?php
// Fonction pour remplacer le logo WordPress dans l'admin bar
function leoleplug_remove_wp_nodes($wp_admin_bar) {
    // Supprimer les sous-menus indésirables
    $wp_admin_bar->remove_node('about');
    $wp_admin_bar->remove_node('wporg');
    $wp_admin_bar->remove_node('documentation');
    $wp_admin_bar->remove_node('support-forums');
    $wp_admin_bar->remove_node('feedback');
    $wp_admin_bar->remove_node('contribute');
}

add_action('admin_bar_menu', 'leoleplug_remove_wp_nodes', 999);

function leoleplug_replace_wp_logo($wp_admin_bar) {
    // Mettez à jour le nœud principal pour qu'il redirige vers leoleplug.com
    $args = array(
        'id'    => 'wp-logo',
        'title' => '<img src="' . plugin_dir_url(dirname(__FILE__)) . 'content/icon-llp.png" style="height: 32px; width: 32px; vertical-align: middle;">',
        'href'  => 'https://leoleplug.com/',
        'meta'  => array('target' => '_blank'),
    );
    $wp_admin_bar->add_node($args);
    
    // Ajouter le sous-menu "Visiter leoleplug.com"
    $wp_admin_bar->add_node(array(
        'parent' => 'wp-logo',
        'id'     => 'leoleplug-visit',
        'title'  => 'Visiter leoleplug.com',
        'href'   => 'https://leoleplug.com/',
        'meta'   => array('target' => '_blank')
    ));
}

add_action('admin_bar_menu', 'leoleplug_replace_wp_logo', 1000);
