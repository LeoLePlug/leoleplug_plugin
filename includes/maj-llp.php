<?php
// Définition de la constante pour l'URL de mise à jour
define('LEOLEPLUG_UPDATE_JSON_URL', 'https://raw.githubusercontent.com/LeoLePlug/leoleplug_plugin/main/leoleplug.json');

/**
 * Vérifie s'il y a une mise à jour disponible.
 */
function leoleplug_check_for_updates() {
    $current_version = get_option('leoleplug_plugin_version');
    $json_data = wp_remote_get(LEOLEPLUG_UPDATE_JSON_URL);

    if (is_wp_error($json_data)) {
        return;
    }

    $body = wp_remote_retrieve_body($json_data);
    $data = json_decode($body);

    if ($data && version_compare($data->version, $current_version, '>')) {
        // Stocke les données de mise à jour pour une utilisation ultérieure
        set_transient('leoleplug_update_data', $data, DAY_IN_SECONDS);
        // Ajoute une notification pour informer de la mise à jour
        add_action('admin_notices', 'leoleplug_display_update_notice');
    }
}

/**
 * Affiche une notification de mise à jour.
 */
function leoleplug_display_update_notice() {
    $data = get_transient('leoleplug_update_data');
    
    if ($data) {
        $update_url = esc_url($data->download_url);
        echo '<div class="notice notice-info is-dismissible">
            <p>Une mise à jour pour le plugin LeoLePlug Agency est disponible. <a href="' . $update_url . '">Mettre à jour maintenant</a></p>
        </div>';
    }
}

/**
 * Gère la mise à jour du plugin.
 */
function leoleplug_update_plugin() {
    $json_data = wp_remote_get(LEOLEPLUG_UPDATE_JSON_URL);

    if (is_wp_error($json_data)) {
        return;
    }

    $body = wp_remote_retrieve_body($json_data);
    $data = json_decode($body);

    if ($data) {
        $zip_url = esc_url($data->download_url);
        $response = wp_safe_remote_get($zip_url);

        if (!is_wp_error($response)) {
            $tmp_file = download_url($zip_url);
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
        update_option('leoleplug_plugin_version', $data->version);
    }
}

// Hook pour vérifier les mises à jour lors de l'initialisation de l'admin
add_action('admin_init', 'leoleplug_check_for_updates');

// Écoute pour la mise à jour du plugin via une requête GET
if (isset($_GET['leoleplug_update']) && $_GET['leoleplug_update'] == 'true') {
    leoleplug_update_plugin();
}
