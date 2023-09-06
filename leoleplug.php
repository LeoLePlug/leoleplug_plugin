<?php
/*
Plugin Name: LeoLePlug Agency 
Description: Ce plugin sert à faire fonctionner les sites développé par Leoleplug Agency
Version: 1.3
Author: LeoLePlug Agency
Author URI: https://leoleplug.com/
*/

// Inclure le fichier de mise à jour
require_once(plugin_dir_path(__FILE__) . 'includes/maj-llp.php');
require_once(plugin_dir_path(__FILE__) . 'includes/support-llp.php');
require_once(plugin_dir_path(__FILE__) . 'includes/adminpanel-llp.php');
require_once(plugin_dir_path(__FILE__) . 'includes/login-llp.php');

// Mettre à jour la version actuelle dans les options
if (!get_option('leoleplug_plugin_version')) {
    update_option('leoleplug_plugin_version', '1.2');
}
