<?php
/**
 * Class for registering a new settings page under Settings.
 */
class MWD_MFR_Option_Page {
 
    /**
     * Constructor.
     */
    function __construct() {
        add_action( 'admin_menu', [$this, 'mwd_mcer_option_page'] );
    }
 
    /**
     * Registers a new settings page under Settings.
     */
    function mwd_mcer_option_page() {
        // Add plugin option page
        $hookname = add_options_page(
            __('Myanmar Currency Exchange Rates Options', 'myanmar-exchange-rates'),
            __('Myanmar Exchange Rates', 'myanmar-exchange-rates'),
            'manage_options',
            'mwd_mcer',
            [$this, 'option_page']
        );

        // Handle form submit
        add_action( 'load-' . $hookname, 'mwd_mcer_options_page_submit' );

        // Form submit function
        function mwd_mcer_options_page_submit()
        {
            // print_r($_POST);
            if ('POST' === $_SERVER['REQUEST_METHOD'] && isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], $_POST['action'])) {
                return "Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi esse facilis iusto aspernatur animi sit quidem voluptatibus quod commodi ratione qui aut error quas, nam totam reprehenderit dolorem consectetur quisquam?";
            } else {
                return 'failed';
            }
        }
    }
 
    /**
     * Settings page display callback.
     */
    function option_page() {
    ?>

        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <?php if (current_user_can('manage_options')) : ?>
                <form action="<?php menu_page_url( 'mwd_mcer' ) ?>" method="post">
                    <?php
                    // output security fields for the registered setting "mwd_mcer_options"
                    settings_fields( 'mwd_mcer' );
                    // output setting sections and their fields
                    // (sections are registered for "mwd_mcer", each field is registered to a specific section)
                    do_settings_sections( 'mwd_mcer' );
                    // output save settings button
                    submit_button( __( 'Save Settings', 'textdomain' ) );
                    ?>
                </form>
            <?php endif; ?>
        </div>
    
    <?php
    }
}

new MWD_MFR_Option_Page;