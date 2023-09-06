<?php
// Définition de la constante pour l'URL du fichier .txt de version
define('LEOLEPLUG_VERSION_TXT_URL', 'https://raw.githubusercontent.com/LeoLePlug/leoleplug_plugin/main/plugin-version.txt');

/**
 * Vérifie s'il y a une mise à jour disponible.
 */
function leoleplug_check_for_updates() {
    $current_version = get_option('leoleplug_plugin_version');
    $txt_response = wp_remote_get(LEOLEPLUG_VERSION_TXT_URL);

    if (is_wp_error($txt_response)) {
        return;
    }

    $remote_version = trim(wp_remote_retrieve_body($txt_response));

    if (version_compare($remote_version, $current_version, '>')) {
        // Stocke les données de mise à jour pour une utilisation ultérieure
        set_transient('leoleplug_update_data', $remote_version, DAY_IN_SECONDS);

        $json_url = 'https://raw.githubusercontent.com/LeoLePlug/leoleplug_plugin/main/update-llp.json';
        // Stocke l'URL de téléchargement dans une transitoire pour une utilisation ultérieure
        set_transient('leoleplug_download_url', $json_url, DAY_IN_SECONDS);

        // Ajoute une notification pour informer de la mise à jour
        add_action('admin_notices', 'leoleplug_display_update_notice');

        // Ajoute une notification de mise à jour à côté du plugin dans la liste des plugins
        add_action('after_plugin_row_leoleplug/leoleplug.php', 'leoleplug_add_update_notification');
    }
}

/**
 * Affiche une notification de mise à jour.
 */
function leoleplug_display_update_notice() {
    $data = get_transient('leoleplug_update_data');
    
    if ($data) {
        $download_url = get_transient('leoleplug_download_url');
        echo '<div class="notice notice-info is-dismissible">
            <p>Une mise à jour pour le plugin LeoLePlug Agency est disponible. <a href="' . esc_url($download_url) . '">Mettre à jour maintenant</a></p>
        </div>';
    }
}

/**
 * Ajoute une notification de mise à jour à côté du plugin dans la liste des plugins.
 */
function leoleplug_add_update_notification() {
    $current_version = get_option('leoleplug_plugin_version');
    $txt_response = wp_remote_get(LEOLEPLUG_VERSION_TXT_URL);

    if (is_wp_error($txt_response)) {
        return;
    }

    $remote_version = trim(wp_remote_retrieve_body($txt_response));

    if (version_compare($remote_version, $current_version, '>')) {
        // Affiche la notification de mise à jour
        echo '<tr class="plugin-update-tr active" id="leoleplug-update-notification">
            <td colspan="3" class="plugin-update colspanchange">
                <div class="update-message notice inline notice-warning notice-alt"><p>';
        
        $download_url = get_transient('leoleplug_download_url');
        echo 'Une mise à jour pour le plugin LeoLePlug Agency est disponible. <a href="' . esc_url($download_url) . '">Mettre à jour maintenant</a>';

        echo '</p></div></td></tr>';
    }
}

/**
 * Gère la mise à jour du plugin.
 */
function leoleplug_update_plugin() {
    $download_url = get_transient('leoleplug_download_url');

    if ($download_url) {
        $response = wp_safe_remote_get($download_url);

        if (!is_wp_error($response)) {
            $tmp_file = download_url($download_url);
            if (!is_wp_error($tmp_file)) {
                $plugin_dir = plugin_dir_path(__FILE__);
                $dest_file = $plugin_dir . 'leoleplug.zip';

                if (rename($tmp_file, $dest_file)) {
                    WP_Filesystem();
                    $unzipfile = unzip_file($dest_file, $plugin_dir);
                    if (is_wp_error($unzipfile)) {
                        unlink($dest_file);
                    }
                }
            }
        }

        // Met à jour la version actuelle du plugin dans les options
        $update_version = get_transient('leoleplug_update_data');
        if ($update_version) {
            update_option('leoleplug_plugin_version', $update_version);
        }
    }
}

// Hook pour vérifier les mises à jour lors de l'initialisation de l'admin
add_action('admin_init', 'leoleplug_check_for_updates');

// Écoute pour la mise à jour du plugin via une requête GET
if (isset($_GET['leoleplug_update']) && $_GET['leoleplug_update'] == 'true') {
    leoleplug_update_plugin();
}
