<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( esc_html__( 'Access denied', 'simple-country-blocker' ) );
}

$simple_country_blocker_blocked_countries = simple_country_blocker_sanitize_countries( get_option( 'simple_country_blocker_blocked_countries', array() ) );
$simple_country_blocker_country_list      = require __DIR__ . '/countries.php';
?>
<div class="wrap">
    <h1><?php esc_html_e( 'Country Blocker Settings', 'simple-country-blocker' ); ?></h1>
    <?php if ( ! simple_country_blocker_is_public_ip( simple_country_blocker_get_client_ip() ) ) : ?>
        <div class="notice notice-warning"><p><?php esc_html_e( 'Your server reports a private or invalid visitor IP. Country blocking cannot identify this connection. If your site uses a proxy, configure your web server to restore visitor IPs from trusted proxies.', 'simple-country-blocker' ); ?></p></div>
    <?php endif; ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'simple_country_blocker_settings_group' ); ?>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e( 'Blocked Countries', 'simple-country-blocker' ); ?></th>
                <td>
                    <input type="hidden" name="simple_country_blocker_blocked_countries[]" value="">
                    <fieldset style="columns: 3 14em;">
                        <legend class="screen-reader-text"><?php esc_html_e( 'Blocked Countries', 'simple-country-blocker' ); ?></legend>
                        <?php foreach ( $simple_country_blocker_country_list as $simple_country_blocker_code => $simple_country_blocker_name ) : ?>
                            <label style="display: block; break-inside: avoid;">
                                <input type="checkbox" name="simple_country_blocker_blocked_countries[]" value="<?php echo esc_attr( $simple_country_blocker_code ); ?>"<?php checked( in_array( $simple_country_blocker_code, $simple_country_blocker_blocked_countries, true ) ); ?>>
                                <?php echo esc_html( $simple_country_blocker_name ); ?>
                            </label>
                        <?php endforeach; ?>
                    </fieldset>
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <p>
        <?php
        /* translators: %d: Total number of countries and territories */
        echo esc_html( sprintf( __( 'Total countries and territories: %d', 'simple-country-blocker' ), count( $simple_country_blocker_country_list ) ) );
        ?>
    </p>
</div>
