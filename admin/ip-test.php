<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( esc_html__( 'Access denied', 'simple-country-blocker' ) );
}

$simple_country_blocker_current_ip      = simple_country_blocker_get_client_ip();
$simple_country_blocker_current_details = simple_country_blocker_lookup_ip( $simple_country_blocker_current_ip );
$simple_country_blocker_tested_details  = null;
$simple_country_blocker_test_error      = null;
$simple_country_blocker_test_ip         = '';

if ( isset( $_POST['test_ip_submit'] ) ) {
    $simple_country_blocker_nonce = isset( $_POST['_scb_test_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_scb_test_nonce'] ) ) : '';
    if ( ! wp_verify_nonce( $simple_country_blocker_nonce, 'simple_country_blocker_test_ip' ) ) {
        $simple_country_blocker_test_error = __( 'Your session has expired. Please reload the page and try again.', 'simple-country-blocker' );
    } else {
        $simple_country_blocker_test_ip        = isset( $_POST['ip_to_test'] ) ? sanitize_text_field( wp_unslash( $_POST['ip_to_test'] ) ) : '';
        $simple_country_blocker_tested_details = simple_country_blocker_lookup_ip( $simple_country_blocker_test_ip );
        if ( is_wp_error( $simple_country_blocker_tested_details ) ) {
            $simple_country_blocker_test_error     = $simple_country_blocker_tested_details->get_error_message();
            $simple_country_blocker_tested_details = null;
        }
    }
}

$simple_country_blocker_detail_fields = array(
    'ip'      => __( 'IP', 'simple-country-blocker' ),
    'city'    => __( 'City', 'simple-country-blocker' ),
    'region'  => __( 'Region', 'simple-country-blocker' ),
    'country' => __( 'Country', 'simple-country-blocker' ),
    'loc'     => __( 'Location', 'simple-country-blocker' ),
    'org'     => __( 'Organization', 'simple-country-blocker' ),
);
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Test IP Address', 'simple-country-blocker' ); ?></h1>
    <h2><?php esc_html_e( 'Your IP Details:', 'simple-country-blocker' ); ?></h2>
    <?php if ( is_wp_error( $simple_country_blocker_current_details ) ) : ?>
        <p><?php echo esc_html( $simple_country_blocker_current_details->get_error_message() ); ?></p>
    <?php else : ?>
        <dl>
            <?php foreach ( $simple_country_blocker_detail_fields as $simple_country_blocker_key => $simple_country_blocker_label ) : ?>
                <?php if ( isset( $simple_country_blocker_current_details[ $simple_country_blocker_key ] ) && is_scalar( $simple_country_blocker_current_details[ $simple_country_blocker_key ] ) ) : ?>
                    <dt><strong><?php echo esc_html( $simple_country_blocker_label ); ?></strong></dt>
                    <dd><?php echo esc_html( $simple_country_blocker_current_details[ $simple_country_blocker_key ] ); ?></dd>
                <?php endif; ?>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>

    <h2><?php esc_html_e( 'Test Another IP Address:', 'simple-country-blocker' ); ?></h2>
    <?php if ( ! empty( $simple_country_blocker_test_error ) ) : ?>
        <div class="notice notice-error"><p><?php echo esc_html( $simple_country_blocker_test_error ); ?></p></div>
    <?php endif; ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'simple_country_blocker_test_ip', '_scb_test_nonce' ); ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="ip_to_test"><?php esc_html_e( 'IP Address', 'simple-country-blocker' ); ?></label></th>
                <td>
                    <input type="text" id="ip_to_test" name="ip_to_test" value="<?php echo esc_attr( $simple_country_blocker_test_ip ); ?>" class="regular-text" required placeholder="8.8.8.8">
                </td>
            </tr>
        </table>
        <?php submit_button( __( 'Test IP', 'simple-country-blocker' ), 'primary', 'test_ip_submit' ); ?>
    </form>

    <?php if ( is_array( $simple_country_blocker_tested_details ) ) : ?>
        <h2><?php esc_html_e( 'Lookup Results:', 'simple-country-blocker' ); ?></h2>
        <dl>
            <?php foreach ( $simple_country_blocker_detail_fields as $simple_country_blocker_key => $simple_country_blocker_label ) : ?>
                <?php if ( isset( $simple_country_blocker_tested_details[ $simple_country_blocker_key ] ) && is_scalar( $simple_country_blocker_tested_details[ $simple_country_blocker_key ] ) ) : ?>
                    <dt><strong><?php echo esc_html( $simple_country_blocker_label ); ?></strong></dt>
                    <dd><?php echo esc_html( $simple_country_blocker_tested_details[ $simple_country_blocker_key ] ); ?></dd>
                <?php endif; ?>
            <?php endforeach; ?>
        </dl>
    <?php endif; ?>
</div>
