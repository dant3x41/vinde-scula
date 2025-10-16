<?php
/**
 * Custom post type registration for Vinde-ți scula plugin.
 *
 * @package VindeScula
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the custom post type for tool sale requests.
 */
function vinde_scula_register_post_type(): void {
    $labels = [
        'name'               => __( 'Anunțuri de vânzare scule', 'vinde-scula' ),
        'singular_name'      => __( 'Anunț vânzare scule', 'vinde-scula' ),
        'menu_name'          => __( 'Anunțuri de vânzare scule', 'vinde-scula' ),
        'name_admin_bar'     => __( 'Anunț vânzare scule', 'vinde-scula' ),
        'add_new'            => __( 'Adaugă nou', 'vinde-scula' ),
        'add_new_item'       => __( 'Adaugă anunț nou', 'vinde-scula' ),
        'new_item'           => __( 'Anunț nou', 'vinde-scula' ),
        'edit_item'          => __( 'Editează anunț', 'vinde-scula' ),
        'view_item'          => __( 'Vezi anunț', 'vinde-scula' ),
        'all_items'          => __( 'Toate anunțurile', 'vinde-scula' ),
        'search_items'       => __( 'Caută anunțuri', 'vinde-scula' ),
        'parent_item_colon'  => __( 'Anunț părinte:', 'vinde-scula' ),
        'not_found'          => __( 'Nu au fost găsite anunțuri.', 'vinde-scula' ),
        'not_found_in_trash' => __( 'Nu au fost găsite anunțuri în coș.', 'vinde-scula' ),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => false,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => [ 'title', 'editor' ],
    ];

    register_post_type( 'vinde_scula', $args );
}
