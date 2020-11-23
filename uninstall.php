<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

/**
 * Delete options.
 * 
 * @since   1.0
 */
delete_option( 'mwd_mcer_latest_fxrates' );
delete_option( 'mwd_mcer_fxcurrencies' );
delete_option( 'mwd_mcer_options' );
delete_option( 'widget_mfr' );