<?php
/**
 * Plugin Name: Vinde-ți scula
 * Plugin URI: https://github.com/dant3x41/vinde-scula
 * Description: Formular multi-step pentru colectarea anunțurilor de vânzare scule electrice.
 * Version: 1.0.0
 * Author: Codex
 * Requires PHP: 8.0
 * Requires at least: 6.0
 * Text Domain: vinde-scula
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'VINDE_SCULA_PLUGIN_VERSION', '1.0.0' );
define( 'VINDE_SCULA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VINDE_SCULA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once VINDE_SCULA_PLUGIN_DIR . 'includes/cpt.php';
require_once VINDE_SCULA_PLUGIN_DIR . 'includes/form.php';
require_once VINDE_SCULA_PLUGIN_DIR . 'includes/admin-menu.php';

register_activation_hook( __FILE__, 'vinde_scula_activate' );

/**
 * Handle plugin activation tasks.
 */
function vinde_scula_activate(): void {
    vinde_scula_register_post_type();
    flush_rewrite_rules();
}

add_action( 'init', 'vinde_scula_register_post_type' );
add_action( 'init', 'vinde_scula_handle_form_submission' );
add_action( 'wp_enqueue_scripts', 'vinde_scula_enqueue_assets' );
add_action( 'admin_menu', 'vinde_scula_register_admin_menu' );
