<?php

/**
 * The file that defines the core plugin class
 * 
 * @link https://myanmarwebdesigner.com
 * @since   1.0
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes
 */

defined('ABSPATH') || exit;

/**
 * The core plugin class
 * 
 * This is used to define internationalization, admin-specific hooks and 
 * public-facing site hooks.
 * 
 * Also maintains the unique identifier of this plugin as well as
 * the current version of the plugin.
 * 
 * @since   1.0
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes
 * @author  Myanmar Web Designer (MWD) Co., Ltd. 
 */
final class Myanmar_Exchange_Rates
{
   /**
    * The loader that is responsible for maintaining and registering all hooks that 
    * powers the plugin.

    * @since   1.0
    * @access  protected
    * @var  MyanmarExchangeRatesLoader   $loader   Maintains and registers all hooks for the plugin.
    */
   protected $loader;

   /**
    * The single instance of the class.
    * 
    * @since   1.0
    * @var  Myanmar_Exchange_Rates
    * @static
    * @access protected
    */
   protected static $_instance = null;

   /**
    * The unique identifier of the plugin.
    *
    * @since   1.0
    * @access  protected
    * @var  string   $plugin_name   The string used to uniquely identify the plugin
    */
   public $plugin_name;

   /**
    * The current version of the plugin.
    * 
    * @since   1.0
    * @access  protected
    * @var string $version The current version of the plugin.
    */
   public $version;

   /**
    * Define the core functionality of the plugin.
    *
    * @since   1.0
    * @since 2.0.0 Added define_public_hooks().
    */
   public function __construct()
   {
      if (defined('MYANMAR_EXCHANGE_RATES_VERSION'))
         $this->version = MYANMAR_EXCHANGE_RATES_VERSION;
      else
         $this->version = '1.0';

      $this->plugin_name = 'myanmar-currency-exchange-rates';

      $this->load_dependencies();
      $this->define_admin_hooks();
      $this->define_public_hooks();
   }

   /**
    * Load the required dependencies for the plugin
    *
    * Include the following files that makeup the plugin:
    *
    * @since   1.0
    * @since 2.0.0 Added shortcode class file.
    * @access  private
    */
   private function load_dependencies()
   {
      /**
       * The class responsible for orchestrating the actions and filters of
       * the core plugin.
       */
      require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-myanmar-exchange-rates-loader.php';

      // The class responsible for defining all actions that occurs in the admin area.
      require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-myanmar-exchange-rates-admin.php';

      // The class responsible for defining widget
      require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/class-mm-fx-rates.php';

      // The class responsible for defining widget
      require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cbm-exchange-rates.php';

      // The class responsible for defining shortcode.
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shortcodes/class-shortcode-fx-rates.php';

      $this->loader = new Myanmar_Exchange_Rates_Loader();
   }

   /**
    * Register all of the hooks related to the admin area functionality
    * of the plugin
    *
    * @since   1.0
    * @access  private
    */
   private function define_admin_hooks()
   {
      $plugin_admin = new Myanmar_Exchange_Rates_Admin($this->get_plugin_name(), $this->get_version());

      $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
      $this->loader->add_action('admin_menu', $plugin_admin, 'mwd_mcer_option_page');
      $this->loader->add_action('admin_init', $plugin_admin, 'mwd_mcer_settings_init');
   }
   

   /**
    * Register public hooks
    *
    * Public hooks related to the public area functionality of the plugin.
    *
    * @since 2.0.0
    * @access  private
    */
   private function define_public_hooks()
   {
      $this->loader->add_action( 'init', new Shortcode_FX_Rates(), 'mm_fxrates_shortcodes_init' );
   }


   /**
    * Return the instance of CBM_Exchange_Rates.
    *
    * @since   1.0
    * @access  private
    * @return  CBM_Exchange_Rates  The instance of CBM_Exchange_Rates class.
    */
   public function cbm_exchange_rates()
   {
      return CBM_Exchange_Rates::instance();
   }

   /**
    * Run the loader to execute all of the hooks with Wordpress.
    *
    * @since   1.0
    */
   public function run()
   {
      $this->loader->run();
   }

   /**
    * Main Myanmar_Exchange_Rates instance.
    *
    * Ensure only one instance of Myanmar_Exchange_Rates is loaded and can be loaded.
    * 
    * @since   1.0
    * @static
    * @see  MWD_MCER()
    * @return  Myanmar_Exchange_Rates  Min instance.
    */
   public static function instance()
   {
      if (is_null(self::$_instance))
         self::$_instance = new self();

      return self::$_instance;
   }

   /**
    * The name of the plugin used to uniquely identify it with the context of
    * Wordpress and to define internatiionalization functionality.
    *
    * @since   1.0
    * @return  string   The name of the plugin. 
    */
   public function get_plugin_name()
   {
      return $this->plugin_name;
   }

   /**
    * Retrieve the version number of the plugin.

    * @since   1.0
    * @return  string   The version number of the plugin.
    */
   public function get_version()
   {
      return $this->version;
   }

   /**
    * The reference to the class that orchestrates the hooks with the plugin.
    *
    * @since   1.0
    * @return  MyanmarExchangeRatesLoader Orchestraes the hooks of the plugin.
    */
   public function get_loader()
   {
      return $this->loader;
   }
}
