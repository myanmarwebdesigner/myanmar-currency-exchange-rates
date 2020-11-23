<?php
/**
 * The admin-specific functionality of the plugin
 * 
 * @since   1.0
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/admin
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for registering a new settings page under Settings.
 * 
 * @since   1.0
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/admin
 * @author  Myanmar Web Designer (MWD) Co., Ltd. 
 */
class Myanmar_Exchange_Rates_Admin
{
   /**
    * The ID of the plugin.
    * 
    * @since 1.0
    * @access private
    * @var  string $plugin_name The ID of this plugin.
    */
   private $plugin_name;

   /**
    * The version of the plugin
    * 
    * @since   1.0
    * @access  private
    * @var string $version The current version of this plugin.
    */
   private $version;
 
   /**
    * Initialize the class and set its properties
    * 
    * @since  1.0
    * @param  string $plugin_name  The name of this plugin.
    * @param  string   $version The version of this plugin.
    */
   public function __construct( $plugin_name, $version )
   {
      $this->plugin_name = $plugin_name;
      $this->version = $version;
   }
 
   /**
    * Registers a new settings page under Settings.
    */
   function mwd_mcer_option_page() {
      // Add plugin option page
      $hookname = add_options_page(
         __('Myanmar Currency Exchange Rates by MWD', 'myanmar-exchange-rates'),
         __('Myanmar Currency Exchange Rates', 'myanmar-exchange-rates'),
         'manage_options',
         'mwd_mcer',
         [$this, 'mwd_mcer_options_page_html']  
      );
   }
 
   /**
    * Settings page display callback.
    * 
    * @since  1.0
    */
   function mwd_mcer_options_page_html()
   {
      // check user capabilities
      if ( ! current_user_can( 'manage_options' ) ) {
         return;
      }

      // add error/update messages
      // check if the user have submitted the settings
      // Wordpress will add the 'settings-updated' $_GET parameter to the url
      if ( isset( $_GET['settings-updated'] ) ) {
         // add settings saved message with the class of `updated`
         add_settings_error( 'mwd_mcer_messages', 'mwd_mcer_message', __( 'Settings Saved', 'myanmar-exchange-rates' ), 'updated' );
      }
      ?>

      <div class="wrap">
         <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
         <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "mwd_mcer_options"
            settings_fields( 'mwd_mcer' );
            // output setting sections and their fields
            // (sections are registered for "mwd_mcer", each field is registered to a specific section)
            do_settings_sections( 'mwd_mcer' );
            // output save settings button
            submit_button( __( 'Save Settings', 'myanmar-exchange-rates' ) );
            ?>
         </form>
      </div>
   
   <?php
   }

   /**
    * Custom setting and options
    * 
    * @since   1.0
    */
   public function mwd_mcer_settings_init()
   {
      // register a new setting for 'mwd_mcer' page.
      register_setting( 'mwd_mcer', 'mwd_mcer_options' );

      // Register a new section in the 'mwd_mcer' page
      // Choose display mode section.
      // add_settings_section(
      //    'mwd_mcer_section_display_modes',
      //    __( 'Choose display mode.', 'myanmar-exchange-rates' ), [ $this, 'mwd_mcer_section_display_modes_callback' ], 'mwd_mcer');
      add_settings_section( 'mwd_mcer_section_display_modes', __( '', 'myanmar-exchange-rates' ), [ $this, 'mwd_mcer_section_display_modes_callback' ], 'mwd_mcer');

      // Register a new section in the 'mwd_mcer' page
      // Select currencies to show section.
      add_settings_section( 'mwd_mcer_section_choose_currency', __( '', 'myanmar-exchange-rates'), [ $this, 'mwd_mcer_section_choose_currency_callback' ], 'mwd_mcer');

      // Add new field to the section of 'mwd_mcer' page
      // Select currencies field.
      add_settings_field(
         'mwd_mcer_field_currencies',
         __( 'Select Currencies to Display', 'myanmar-exchange-rates' ),
         [ $this, 'mwd_mcer_field_currencies_callback' ],
         'mwd_mcer',
         'mwd_mcer_section_choose_currency',
         array(
            'name'   => 'mwd_mcer_field_currencies',
            'class'  => 'mwd-mcer-field-currencies',
         ),
      );

      // Add new field to the section of 'mwd_mcer' page
      // Display modes field.
      add_settings_field(
         'mwd_mcer_field_display_modes',
         __( 'Display Mode', 'myanmar-exchange-rates' ),
         [ $this, 'mwd_mcer_field_display_modes_callback' ],
         'mwd_mcer',
         'mwd_mcer_section_display_modes',
         array(
            'name'   => 'mwd_mcer_field_display_modes',
            'class'  => 'mwd-mcer-field-display-modes',
         )
      );
   }

   /**
    * Currencies field callback of `mwd_mcer` page 
    *
    * @since   1.0
    */
   public function mwd_mcer_field_currencies_callback( $args )
   {
      // Latest exchange-rates.
      $fxrates = MWD_MCER()->cbm_exchange_rates()->get_fxrates();

      // Sort $fxrates ASC order by Name.
      ksort( $fxrates );

      // Get the value of the setting we've registered with register_setting().
      $options = get_option( 'mwd_mcer_options' );
      $currency_options = ( ! isset( $options[$args['name']] ) || empty( $options[$args['name']] ) ) ? MWD_MCER()->cbm_exchange_rates()->get_default_currencies() : $options[$args['name']];
      ?>

      <p style="margin-bottom:1rem;">
         <input type="checkbox" 
            name="" 
            id="all-currencies"
            <?php echo ( count( $fxrates ) == count( $currency_options ) ) ? ' checked ' : '' ?>
         >
         <label for="all-currencies"><?php _e( 'All', 'myanmar-exchange-rates' ); ?></label>
      </p>
      
      <fieldset>         

        <?php foreach ( $fxrates as $currency => $value ) : ?>

            <div class="form-group" style="display: inline-block;margin-right: 10px;">
               <input type="checkbox" 
                  name="mwd_mcer_options[<?php esc_attr_e( $args['name'] ) ?>][]" id="<?php esc_attr_e( strtolower( $currency ) ); ?>"
                  value="<?php esc_attr_e( $currency ); ?>"
                  <?php echo ( in_array( $currency, $currency_options, TRUE ) ) ? ' checked ' : ''; ?>
               >
               <label for="<?php esc_attr_e( strtolower( $currency ) ); ?>"><?php esc_html_e( $currency ); ?></label>
            </div>

         <?php endforeach; ?>

      </fieldset>
      
      <?php
   }

   /**
    * Display-modes field callback of `mwd_mcer` page.
    *
    * @since   1.0
    */
   public function mwd_mcer_field_display_modes_callback( $args )
   {
      $options = get_option( 'mwd_mcer_options' );
      $modes = ( ! empty( $options[$args['name']] ) ) ? $options[$args['name']] : 'normal';
      ?>

      <fieldset>
         <div class="form-group" style="display: inline-block;margin-right: 16px">
            <input type="radio" 
               name="mwd_mcer_options[<?php echo esc_attr( $args['name'] ); ?>]" 
               id="normal" 
               value="normal"
               <?php echo ( $modes === 'normal' ) ? ' checked ' : '' ; ?>
            >
            <label for="normal"><?php _e( 'Normal', 'myanmar-exchange-rates' ); ?></label>
         </div>
         <div class="form-group" style="display: inline-block;margin-right: 16px">
            <input type="radio" 
               name="mwd_mcer_options[<?php echo esc_attr( $args['name'] ); ?>]" 
               id="compact" 
               value="compact"
               <?php echo ( $modes === 'compact' ) ? ' checked ' : '' ; ?>
            >
            <label for="compact"><?php _e( 'Compact', 'myanmar-exchange-rates' ); ?></label>
         </div>
      </fieldset>

      <?php
   }

   /**
    * Setting section callback of 'mwd_mcer' page
    *
    * @since   1.0
    */
   public function mwd_mcer_section_choose_currency_callback( $arg )
   {
      echo '';
   }

   /**
    * Choose display mode setting-section. 
    *
    * @since   1.0
    */
   public function mwd_mcer_section_display_modes_callback( $args )
   {
      echo '';
   }

   /**
    * Register the JavaScript for the admin area.
    *
    * @since   1.0
    */
   public function enqueue_scripts()
   {
      wp_enqueue_script( 'mwd-mcer-admin-scripts', plugin_dir_url( __FILE__ ) . 'js/mwd-mcer-admin.js', array( 'jquery' ), $this->version, true );
   }
}