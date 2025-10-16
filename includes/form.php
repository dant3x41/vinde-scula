<?php
/**
 * Front-end form handling for Vinde-ți scula plugin.
 *
 * @package VindeScula
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Holds the latest submission message for display in the shortcode output.
 *
 * @var string|null
 */
$GLOBALS['vinde_scula_form_message'] = null;

/**
 * Process the front-end form submission.
 */
function vinde_scula_handle_form_submission(): void {
    if ( ! isset( $_POST['vinde_scula_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vinde_scula_nonce'] ) ), 'vinde_scula_submit' ) ) {
        $GLOBALS['vinde_scula_form_message'] = __( 'A apărut o problemă cu trimiterea formularului. Te rugăm să reîncarci pagina și să încerci din nou.', 'vinde-scula' );
        return;
    }

    $power_source = isset( $_POST['vinde_scula_power_source'] ) ? sanitize_text_field( wp_unslash( $_POST['vinde_scula_power_source'] ) ) : '';
    $brand        = isset( $_POST['vinde_scula_brand'] ) ? sanitize_text_field( wp_unslash( $_POST['vinde_scula_brand'] ) ) : '';
    $condition    = isset( $_POST['vinde_scula_condition'] ) ? sanitize_text_field( wp_unslash( $_POST['vinde_scula_condition'] ) ) : '';

    $allowed_power_sources = [ 'cu-acumulator', 'cu-fir', 'pneumatic' ];
    $allowed_brands        = [ 'bosch', 'dewalt', 'makita', 'hilti', 'festool', 'metabo', 'milwaukee', 'hikoki', 'stanley', 'skil' ];
    $allowed_conditions    = [ 'noua', 'ca-noua', 'foarte-buna', 'buna' ];

    if ( ! in_array( $power_source, $allowed_power_sources, true ) || ! in_array( $brand, $allowed_brands, true ) || ! in_array( $condition, $allowed_conditions, true ) ) {
        $GLOBALS['vinde_scula_form_message'] = __( 'Selecțiile furnizate nu sunt valide. Te rugăm să verifici și să reîncerci.', 'vinde-scula' );
        return;
    }

    $post_title   = sprintf(
        /* translators: %1$s: brand, %2$s: submission datetime */
        __( 'Anunț %1$s - %2$s', 'vinde-scula' ),
        ucfirst( $brand ),
        wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) )
    );
    $post_content = sprintf(
        "%s: %s\n%s: %s\n%s: %s",
        __( 'Tip alimentare', 'vinde-scula' ),
        vinde_scula_get_power_source_label( $power_source ),
        __( 'Brand', 'vinde-scula' ),
        vinde_scula_get_brand_label( $brand ),
        __( 'Stare', 'vinde-scula' ),
        vinde_scula_get_condition_label( $condition )
    );

    $post_id = wp_insert_post(
        [
            'post_type'   => 'vinde_scula',
            'post_title'  => $post_title,
            'post_status' => 'pending',
            'post_content'=> $post_content,
        ],
        true
    );

    if ( is_wp_error( $post_id ) ) {
        $GLOBALS['vinde_scula_form_message'] = __( 'A apărut o eroare la salvarea cererii. Te rugăm să încerci din nou mai târziu.', 'vinde-scula' );
        return;
    }

    update_post_meta( $post_id, '_vinde_scula_power_source', $power_source );
    update_post_meta( $post_id, '_vinde_scula_brand', $brand );
    update_post_meta( $post_id, '_vinde_scula_condition', $condition );

    $GLOBALS['vinde_scula_form_message'] = __( 'Mulțumim! Cererea ta a fost trimisă și va fi analizată.', 'vinde-scula' );
}

/**
 * Register the shortcode and render the multi-step form.
 *
 * @return void
 */
function vinde_scula_register_shortcode(): void {
    add_shortcode( 'vinde_scula_form', 'vinde_scula_render_form' );
}
add_action( 'init', 'vinde_scula_register_shortcode' );

/**
 * Enqueue assets required for the multi-step form.
 */
function vinde_scula_enqueue_assets(): void {
    if ( ! is_singular() && ! is_front_page() ) {
        return;
    }

    $post_id = get_queried_object_id();

    if ( ! $post_id ) {
        return;
    }

    $post = get_post( $post_id );

    if ( ! $post ) {
        return;
    }

    if ( ! has_shortcode( $post->post_content, 'vinde_scula_form' ) ) {
        return;
    }

    wp_enqueue_style(
        'vinde-scula-form',
        VINDE_SCULA_PLUGIN_URL . 'assets/css/form.css',
        [],
        VINDE_SCULA_PLUGIN_VERSION
    );

    wp_enqueue_script(
        'vinde-scula-form',
        VINDE_SCULA_PLUGIN_URL . 'assets/js/form.js',
        [],
        VINDE_SCULA_PLUGIN_VERSION,
        true
    );
}

/**
 * Render the multi-step form.
 *
 * @return string
 */
function vinde_scula_render_form(): string {
    if ( ! empty( $GLOBALS['vinde_scula_form_message'] ) ) {
        return sprintf( '<div class="vinde-scula-form-message">%s</div>', esc_html( $GLOBALS['vinde_scula_form_message'] ) );
    }

    ob_start();
    ?>
    <form class="vinde-scula-form" method="post">
        <div class="vinde-scula-progress" aria-hidden="true">
            <span class="vinde-scula-progress-step is-active" data-step="1"><?php esc_html_e( 'Pasul 1 din 3', 'vinde-scula' ); ?></span>
            <span class="vinde-scula-progress-step" data-step="2"><?php esc_html_e( 'Pasul 2 din 3', 'vinde-scula' ); ?></span>
            <span class="vinde-scula-progress-step" data-step="3"><?php esc_html_e( 'Pasul 3 din 3', 'vinde-scula' ); ?></span>
        </div>

        <div class="vinde-scula-form-step is-active" data-step="1">
            <h3><?php esc_html_e( 'Tip alimentare', 'vinde-scula' ); ?></h3>
            <div class="vinde-scula-options">
                <?php foreach ( vinde_scula_get_power_sources() as $value => $label ) : ?>
                    <label class="vinde-scula-option">
                        <input type="radio" name="vinde_scula_power_source" value="<?php echo esc_attr( $value ); ?>" required>
                        <span><?php echo esc_html( $label ); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <button type="button" class="vinde-scula-button vinde-scula-next" data-next="2"><?php esc_html_e( 'Continuă', 'vinde-scula' ); ?></button>
        </div>

        <div class="vinde-scula-form-step" data-step="2">
            <h3><?php esc_html_e( 'Brand', 'vinde-scula' ); ?></h3>
            <div class="vinde-scula-options">
                <?php foreach ( vinde_scula_get_brands() as $value => $label ) : ?>
                    <label class="vinde-scula-option">
                        <input type="radio" name="vinde_scula_brand" value="<?php echo esc_attr( $value ); ?>" required>
                        <span><?php echo esc_html( $label ); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="vinde-scula-navigation">
                <button type="button" class="vinde-scula-button vinde-scula-prev" data-prev="1"><?php esc_html_e( 'Înapoi', 'vinde-scula' ); ?></button>
                <button type="button" class="vinde-scula-button vinde-scula-next" data-next="3"><?php esc_html_e( 'Continuă', 'vinde-scula' ); ?></button>
            </div>
        </div>

        <div class="vinde-scula-form-step" data-step="3">
            <h3><?php esc_html_e( 'Stare', 'vinde-scula' ); ?></h3>
            <div class="vinde-scula-options">
                <?php foreach ( vinde_scula_get_conditions() as $value => $label ) : ?>
                    <label class="vinde-scula-option">
                        <input type="radio" name="vinde_scula_condition" value="<?php echo esc_attr( $value ); ?>" required>
                        <span><?php echo esc_html( $label ); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="vinde-scula-navigation">
                <button type="button" class="vinde-scula-button vinde-scula-prev" data-prev="2"><?php esc_html_e( 'Înapoi', 'vinde-scula' ); ?></button>
                <button type="submit" class="vinde-scula-button vinde-scula-submit"><?php esc_html_e( 'Trimite', 'vinde-scula' ); ?></button>
            </div>
        </div>

        <input type="hidden" name="vinde_scula_nonce" value="<?php echo esc_attr( wp_create_nonce( 'vinde_scula_submit' ) ); ?>">
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Return power source options.
 *
 * @return array<string, string>
 */
function vinde_scula_get_power_sources(): array {
    return [
        'cu-acumulator' => __( 'Cu acumulator (fără fir)', 'vinde-scula' ),
        'cu-fir'        => __( 'Cu fir / rețea (electrice clasice)', 'vinde-scula' ),
        'pneumatic'     => __( 'Pneumatice (cu aer comprimat)', 'vinde-scula' ),
    ];
}

/**
 * Return brand options.
 *
 * @return array<string, string>
 */
function vinde_scula_get_brands(): array {
    return [
        'bosch'     => __( 'Bosch', 'vinde-scula' ),
        'dewalt'    => __( 'DeWalt', 'vinde-scula' ),
        'makita'    => __( 'Makita', 'vinde-scula' ),
        'hilti'     => __( 'Hilti', 'vinde-scula' ),
        'festool'   => __( 'Festool', 'vinde-scula' ),
        'metabo'    => __( 'Metabo', 'vinde-scula' ),
        'milwaukee' => __( 'Milwaukee', 'vinde-scula' ),
        'hikoki'    => __( 'Hikoki', 'vinde-scula' ),
        'stanley'   => __( 'Stanley', 'vinde-scula' ),
        'skil'      => __( 'Skil', 'vinde-scula' ),
    ];
}

/**
 * Return condition options.
 *
 * @return array<string, string>
 */
function vinde_scula_get_conditions(): array {
    return [
        'noua'        => __( 'Nouă', 'vinde-scula' ),
        'ca-noua'     => __( 'Ca nouă', 'vinde-scula' ),
        'foarte-buna' => __( 'Foarte bună', 'vinde-scula' ),
        'buna'        => __( 'Bună', 'vinde-scula' ),
    ];
}

/**
 * Get the label for a power source key.
 */
function vinde_scula_get_power_source_label( string $value ): string {
    $options = vinde_scula_get_power_sources();
    return $options[ $value ] ?? $value;
}

/**
 * Get the label for a brand key.
 */
function vinde_scula_get_brand_label( string $value ): string {
    $options = vinde_scula_get_brands();
    return $options[ $value ] ?? $value;
}

/**
 * Get the label for a condition key.
 */
function vinde_scula_get_condition_label( string $value ): string {
    $options = vinde_scula_get_conditions();
    return $options[ $value ] ?? $value;
}
