<?php
/**
 * Admin menu registration for Vinde-ți scula plugin.
 *
 * @package VindeScula
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the admin menu entries for the plugin.
 */
function vinde_scula_register_admin_menu(): void {
    add_menu_page(
        __( 'Vinde-ți scula', 'vinde-scula' ),
        __( 'Vinde-ți scula', 'vinde-scula' ),
        'edit_posts',
        'vinde_scula_overview',
        'vinde_scula_render_overview_page',
        'dashicons-hammer',
        26
    );

    add_submenu_page(
        'vinde_scula_overview',
        __( 'Anunțuri primite', 'vinde-scula' ),
        __( 'Anunțuri primite', 'vinde-scula' ),
        'edit_posts',
        'edit.php?post_type=vinde_scula'
    );
}

/**
 * Render the overview page content.
 */
function vinde_scula_render_overview_page(): void {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'Nu ai permisiunea de a accesa această pagină.', 'vinde-scula' ) );
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Vinde-ți scula', 'vinde-scula' ); ?></h1>
        <p><?php esc_html_e( 'Folosește sub-meniul „Anunțuri primite” pentru a gestiona cererile trimise.', 'vinde-scula' ); ?></p>
    </div>
    <?php
}
