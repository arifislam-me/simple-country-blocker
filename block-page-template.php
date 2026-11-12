<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php esc_html_e( 'Access Restricted', 'simple-country-blocker' ); ?></title>
    <?php wp_print_styles( 'simple-country-blocker-style' ); ?>
</head>
<body>
    <div id="page" class="site">
        <div class="site-content">
            <h1 class="site-title"><?php esc_html_e( 'Access Restricted', 'simple-country-blocker' ); ?></h1>
            <p><?php esc_html_e( 'Access to this website is restricted from your country.', 'simple-country-blocker' ); ?></p>
        </div>
    </div>
</body>
</html>
