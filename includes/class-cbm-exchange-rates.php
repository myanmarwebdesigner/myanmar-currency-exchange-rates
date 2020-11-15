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
    * latest exchange rates response body.
    *
    * @since   1.0
    * @access  protected
    * @var  array  $fxrates Latest rates repsonse body.
    */
   protected $fxrates;
   
   /**
    * Exchange rates response body.
    *
    * @since   1.0
    * @access  protected
    * @var  object  $fxrates_body Latest rates repsonse body.
    */
   protected $fxrates_body;
   
   /**
   * The currencies list
   *
   * @since   1.0
   * @access  protected
   * @var  array $currencies All available currencies
   */
   protected $currencies;

   /**
    * Initialize the class and get latest exchange-rates
    *
    * @since   1.0
    */
   public function __construct()
   {
      // Get rates
      $response = wp_remote_get( 'http://forex.cbm.gov.mm/api/latest' );
      $body = wp_remote_retrieve_body( $response );

      $this->fxrates_body = json_decode( $body );

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
    * Retrieve currencies.
    *
    * @since   1.0
    * @return  array Available currencies.
    */
   public function get_currencies()
   {
      return $this->currencies;
   }
}