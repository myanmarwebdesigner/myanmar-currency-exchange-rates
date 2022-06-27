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
    * An associative array key of three-letter currency code and array value of long country name.
    *
    * @since   1.0
    * @access  protected
    * @var  array  $fxcurrencies Latest-currencies.
    */
   protected $fxcurrencies;

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
    *
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
    * @since   2.0.0 Used wp transient instead of wp options
    * @access  private
    * @return  array Retrun exchange rates, or empty array.
    */
   private function get_latest_fxrates_body()
   {
      // Get any existing copy of our transient data ( exchange rates )
      if ( false === ( $fxrates = get_transient( 'mwd_mcer_fxrates' ) ) ) {
         // It wasn't there, so regenerate the data and save the transient
         // Get latest exchange-rates from the Central Banks of Myanmar.
         $response = wp_remote_get( 'http://forex.cbm.gov.mm/api/latest' );
         $response_code = wp_remote_retrieve_response_code( $response );      
         $response_body = wp_remote_retrieve_body( $response );

         if ( $response_code === 200 ) {
            // Decode to PHP's associative array.
            $fxrates = json_decode( $response_body, true );
            
            set_transient( 'mwd_mcer_fxrates', $fxrates, 12 * HOUR_IN_SECONDS );
         } else {
            $fxrates = array();
         }
      }

      return $fxrates;
   }

   /**
    * Get the exchange-currencies
    * from the `Central Banks of Myanmar`.
    *
    * @since   1.0
    * @since   2.0.0 Used wp transient instead of wp options
    * @access  private
    * @return  array Return exchange currencies, or empty array.
    */
   private function get_latest_fxcurrencies()
   {
      // Get any existing copy of our transient data ( exchange currencies )
      if ( false === ( $fxcurrencies = get_transient( 'mwd_mcer_fxcurrencies' ) ) ) {
         // It wasn't there, so regenerate the data and save the transient
         // Get the latest currencies from Central Banks of Myanmar.
         $response = wp_remote_get( 'https://forex.cbm.gov.mm/api/currencies' );
         $response_code = wp_remote_retrieve_response_code( $response );      
         $response_body = wp_remote_retrieve_body( $response );

         if ( $response_code === 200 ) {
            // Decode to PHP associative array.
            $fxcurrencies = isset( json_decode( $response_body, true )['currencies'] ) ? json_decode( $response_body, true )['currencies'] : array();
   
            set_transient( 'mwd_mcer_fxcurrencies', $fxcurrencies, 1 * MONTH_IN_SECONDS );
         } else {
            $fxcurrencies = array();
         }
      }
      
      return $fxcurrencies;
   }

   /**
    * Retrieve latest exchange-rates response body.
    *
    * @since   1.0
    * @return  array An PHP's associative array of fxrates responsed body.
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
    * Retrieve latest exchange-rates.
    *
    * @since   1.0
    * @return  array An PHP's associative array of fxrates, or empty array.
    */
   public function get_fxrates()
   {
      return ( ! empty( $this->fxrates_body['rates'] ) ) ? $this->fxrates_body['rates'] : array();
   }

   /**
    * Retrieve latest exchange-rates timestamp.
    *
    * @since   1.0
    * @return  string The latest timestamp, or empty string.
    */
   public function get_fxtimestamp()
   {
      return ( ! empty( $this->fxrates_body['timestamp'] ) ) ? $this->fxrates_body['timestamp'] : '';
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