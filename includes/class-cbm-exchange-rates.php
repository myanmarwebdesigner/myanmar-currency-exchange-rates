<?php
/**
 * The CBMs' exchange rates for the plugin.
 * 
 * @link http://myanmarwebdesigner.com
 * @since   1.0
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes
 */

defined( 'ABSPATH' ) || exit;

/**
 * The CBMs' exchange rates for the plugin.
 * 
 * This is used to get latest exchage rates from Central Bank of Myanmar
 * and update if new currency is found form CBM.
 * 
 * @since   1.0
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes
 * @author  Myanmar Web Designer (MWD) Co., Ltd. 
 */
class CBM_Exchange_Rates
{
   /**
    * The single instance of the class.
    *
    * @since   1.0
    * @access  protected
    * @static
    * @var  CBM_Exchange_Rates
    */
   protected static $_instance = null;
   
   /**
    * Exchange rates response body.
    *
    * @since   1.0
    * @access  protected
    * @var  array  $fxrates_body Latest rates repsonse body.
    */
   protected $fxrates_body;
   
   /**
    * Exchange-currencies.
    *
    * @since   1.0
    * @access  protected
    * @var  array  $fxcurrencies Latest-currencies.
    */
   protected $fxcurrencies;
   
   /**
   * The currencies list
   *
   * @since   1.0
   * @access  protected
   * @var  array $currencies All available currencies
   */
   protected $currencies;

   /**
    * The default currencies to show.
    *
    * @since   1.0
    * @access  protected
    * @var  array $default_currencies  Default currencies to show on public-side.
    */
   protected $default_currencies;

   /**
    * Initialize the class and get latest exchange-rates
    *
    * @since   1.0
    */
   public function __construct()
   {
      $this->fxrates_body = $this->get_latest_fxrates_body();
      $this->fxcurrencies = $this->get_latest_fxcurrencies();

      // All available currencies
      $this->currencies = array(
         "USD",
         "KES",
         "THB",
         "PKR",
         "CZK",
         "JPY",
         "SAR",
         "LAK",
         "HKD",
         "BRL",
         "LKR",
         "NZD",
         "CAD",
         "GBP",
         "PHP",
         "KRW",
         "VND",
         "DKK",
         "AUD",
         "RSD",
         "MYR",
         "INR",
         "BND",
         "EUR",
         "SEK",
         "NOK",
         "ILS",
         "CNY",
         "CHF",
         "RUB",
         "KWD",
         "BDT",
         "EGP",
         "ZAR",
         "NPR",
         "IDR",
         "KHR",
         "SGD",
      );

      // Default currencies to display.
      $this->default_currencies = array(
         'USD',
         "EUR",
         "THB",
         "SGD",
         "JPY",
         "MYR",
         'AUD',
      );
   }

   /**
    * CBM_Exchange_Rates instance.

    * @since   1.0
    * @static
    * @return  CBM_Exchange_Rates
    */
   public static function instance()
   {
      if ( is_null( self::$_instance ) ) {
         self::$_instance = new self();
      }

      return self::$_instance;
   }

   /**
    * Get latest exchange-rates
    * from `Central Banks of Myanmar`.
    *
    * @since   1.0
    * @access  private
    * @return  array If ok Retrun response body array, else empty array.
    */
   private function get_latest_fxrates_body()
   {
      // latest exchange-rates.
      $fxrates_body = array();

      // Get latest exchange-rates
      $response = wp_remote_get( 'http://forex.cbm.gov.mm/api/latest' );
      $body = wp_remote_retrieve_body( $response );

      if ( ! empty( $body ) ) {
         // Decode to PHP associative array.
         $fxrates_body = json_decode( $body, true );
         
         // Check and update if $rates_body is update.
         $option_latest_fxrates = get_option( 'mwd_mcer_latest_fxrates', '' ) ;

         if ( empty( $option_latest_fxrates ) ) {
            add_option( 'mwd_mcer_latest_fxrates', $fxrates_body );
         } elseif ( $fxrates_body['timestamp'] > $option_latest_fxrates['timestamp'] ) {
            update_option( 'mwd_mcer_latest_fxrates', $fxrates_body );
         }
      } else {
         // Get option-response-body.
         $fxrates_body = get_option( 'mwd_mcer_latest_fxrates', array() ) ;
      }

      return $fxrates_body;
   }

   /**
    * Get the exchange-currencies
    * from the `Central Banks of Myanmar`.
    *
    * @since   1.0
    * @access  private
    * @return  array If exist return the exchange-currencies else empty array.
    */
   private function get_latest_fxcurrencies()
   {
      // Exchange currencies.
      $fxcurrencies = array();

      $response = wp_remote_get( 'https://forex.cbm.gov.mm/api/currencies' );
      $body = wp_remote_retrieve_body( $response );
      
      if ( ! empty( $body ) ) {
         // Decode to PHP associative array.
         $fxcurrencies = json_decode( $body, true )['currencies'];

         // Retrieve stored `fxcurrencies`.
         $option_fxcurrencies = get_option( 'mwd_mcer_fxcurrencies', '' );

         if ( empty( $option_fxcurrencies ) ) {
            add_option( 'mwd_mcer_fxcurrencies', $fxcurrencies );
         } elseif ( count( array_diff_assoc( $fxcurrencies, $option_fxcurrencies ) ) > 0 ) {
            update_option( 'mwd_mcer_fxcurrencies', $fxcurrencies );
         }
      } else {
         // Retrieve stored `fxcurrencies`.
         $fxcurrencies = get_option( 'mwd_mcer_fxcurrencies', array() );
      }
      
      return $fxcurrencies;
   }

   /**
    * Retrieve fxrates.
    *
    * @since   1.0
    * @return  array Latest fxrates.
    */
   public function get_fxrates_body()
   {
      return $this->fxrates_body;
   }

   /**
    * Retrieve fxcurrencies.
    *
    * @since   1.0
    * @see  CBM_Exchange_Rates::get_latest_fxcurrencies()
    * @return  array The fxcurrencies array.
    */
   public function get_fxcurrencies()
   {
      return $this->fxcurrencies;
   }

   /**
    * Retrieve currencies.
    *
    * @since   1.0
    * @return  array Available currencies.
    */
   public function get_currencies()
   {
      return $this->currencies;
   }

   /**
    * Retrieve the default currencies.
    *
    * @since   1.0
    * @return  array Default currencies.
    */
   public function get_default_currencies()
   {
      return $this->default_currencies;
   }
}