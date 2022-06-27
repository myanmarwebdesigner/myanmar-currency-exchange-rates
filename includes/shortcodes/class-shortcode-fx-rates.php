<?php

/**
 * Exchange rates shortcode.
 * 
 * List currency exchange rates.
 * 
 * @since   2.0.0
 * @link https://myanmarwebdesigner.com
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes/shortcodes
 */

/**
 * Exchange rates shortcode.
 * 
 * List currency exchange rates.
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes/shortcodes
 * @author  Myanmar Web Designer (MWD) Co., Ltd.
 */

defined('ABSPATH') || exit;


if (!class_exists('Shortcode_FX_Rates')) {

    /**
     * Exchange rates shortcode.
     * 
     * Show foreign exchange rates on your webpage.
     * 
     * @since 2.0.0
     */
    class Shortcode_FX_Rates
    {

        /**
         * The [mm_fxrates] shortcode.
         * 
         * Accepts mode, orderby and order, and will display a table.
         * 
         * @since 2.0.0
         * 
         * @param array $atts {
         *      Optional. Shortcode attrubutes.
         *  
         *      @type string $mode Display mode. Default 'normal'. Accepts 'normal', 'compact'.
         *      @type string $orderby Order by. Default 'name'. Accepts 'name', 'rate'.
         *      @type string $order Output order. Default 'asc'. Accepts 'asc', 'desc'.
         * }
         * @return string Shortcode output.
         */
        function mm_fxrates_shortcode( $atts )
        {
            // normalize attribute keys, lowercase
            $atts = array_change_key_case( (array) $atts, CASE_LOWER );

            // normalize attribute value, lowercase
            $atts = array_map( 'strtolower', $atts );
            
            // Get plugin setting options
            $options = get_option( 'mwd_mcer_options' );
            $option_d_mode = ( !empty( $options['mwd_mcer_field_display_modes'] ) ) 
                ? $options['mwd_mcer_field_display_modes'] 
                : 'normal';

            // Attributes
            $atts = shortcode_atts(
                array(
                    'mode' => $option_d_mode,
                    'orderby' => 'name',
                    'order' => 'asc',
                ),
                $atts,
                'mm_fxrates'
            );

            // get plugin setting currencies option
            $option_currencies = ( !empty( $options['mwd_mcer_field_currencies'] ) )
                ? $options['mwd_mcer_field_currencies']
                : MWD_MCER()->cbm_exchange_rates()->get_default_currencies();

            // get exchange rates and timestanp
            $fxrates = array();
            $fxrates = MWD_MCER()->cbm_exchange_rates()->get_fxrates();
            $rate_timestamp = MWD_MCER()->cbm_exchange_rates()->get_fxtimestamp();

            // filter $fxrates with $option_currencies
            $rates = array();
            foreach ( $option_currencies as $option ) {
                if ( isset( $fxrates[$option] ) )
                    $rates[$option] = $fxrates[$option];
            }

            if ( count( $rates ) > 0 ) {
                if ( $atts['orderby'] === 'name' ) {
                    if ( $atts['order'] === 'asc' ) 
                        ksort( $rates ); // Sort an array by key.
                    elseif ( $atts['order'] === 'desc' ) 
                        krsort( $rates ); // Sort an array by key in reverse order.
                } elseif ( $atts['orderby'] === 'rate' ) {
                    // Remove thound separator form rates
                    foreach ( $rates as &$value ) {
                        $value = str_replace( ',', '', $value );
                    }

                    unset( $value ); // break the reference with the last element

                    // sorts $rates
                    if ( $atts['order'] === 'asc' ) 
                        asort( $rates, SORT_NUMERIC ); // sort $rates.
                    elseif ( $atts['order'] === 'desc' )
                        arsort( $rates, SORT_NUMERIC ); // reverse-sort $rates.

                    
                    // format the numbers with three decimal places
                    foreach ( $rates as &$value ) {
                        $value = $this->num_format( $value );
                    }

                    unset( $value ); // break the reference with the last element
                }
            }

            // start output buffering
            ob_start();

            if ( count( $rates ) > 0 ) :
            ?>

                <p class="description">
                    <small>
                        The Central Bank of Myanmar (CBM) updated this rates on MMT
                        <span class="text-danger">
                            <?php echo date( 'F j, Y - l g:i A', $rate_timestamp + 6.5 * 3600 ) ?>
                        </span>
                    </small>
                </p>
                <table style="border-collapse:collapse; width:100%; max-width: 400px;" class="table-striped">
                    <thead>
                        <tr style="border:0;">
                            <th style="padding:10px 16px;border:1px solid #ddd;line-height:normal;font-size:16px;font-weight:bold;">
                                Currency
                            </th>
                            <th style="padding:10px 16px;border:1px solid #ddd;line-height:normal;font-size:16px;font-weight:bold;">
                                MMK
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $d_mode = $atts['mode'];

                        if ( $d_mode === 'compact' ) :

                            foreach ( $rates as $key => $value ) :
                                ?>

                                <tr style="border:0;">
                                    <td style="padding:8px 16px;border:1px solid #ddd;line-height:normal;font-size:15px;font-weight:400;">
                                        <?php echo esc_html( '1 ' . $key ); ?>
                                    </td>
                                    <td style="padding:8px 16px;border:1px solid #ddd;line-height:normal;font-size:15px;font-weight:400;">
                                        <?php echo esc_html( $value ); ?>
                                    </td>
                                </tr>

                            <?php
                            endforeach;

                        elseif ( $d_mode === 'normal' ) :
                            // Get exchange currencies.
                            $fxcurrencies = MWD_MCER()->cbm_exchange_rates()->get_fxcurrencies();

                            foreach ( $rates as $key => $value ) :
                            ?>

                                <tr style="border:0;">
                                    <td style="padding:5px 16px;border:1px solid #ddd;line-height:normal;">
                                        <span style="display:inline-block;width: 100%;font-size:15px;font-weight:400;">
                                            <?php echo esc_html( '1 ' . $key ); ?>
                                        </span>
                                        <small style="font-size:12px;font-weight:400;">
                                            <?php echo esc_html( $fxcurrencies[$key] ); ?>
                                        </small>
                                    </td>
                                    <td style="padding:5px 16px;border:1px solid #ddd;line-height:normal;font-size:15px;font-weight:400;">
                                        <?php echo esc_html( $value ); ?>
                                    </td>
                                </tr>

                        <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            <?php
            else:
            ?>

                <p>Nothing to show!</p>

            <?php
            endif;

            // assign the contents of output buffer
            $output = ob_get_contents();
            
            // clean output buffer and turn off output buffering
            ob_end_clean();

            // return output
            return $output;
        }


        /**
         * Register the shortcode.
         * 
         * Use as callback function of init action hook.
         * 
         * @since 2.0.0
         */
        public function mm_fxrates_shortcodes_init()
        {
            add_shortcode( 'mm_fxrates', array( $this, 'mm_fxrates_shortcode' ) );
        }


        /**
         * Remove unnecessary zeros.
         * 
         * Same as number_format() but without unnecessary zeros.
         * 
         * @since 2.0.0
         * 
         * @param float $num Number to format.
         * @return float
         */
        public function num_format( $num )
        {
            $ret = number_format( $num, 10 );

            while( ( substr( $ret, -1 ) === "0" ) ) {
                // if number ends with a '0', remove '0' from end of string
                $ret = substr( $ret, 0, -1 );
            }

            if ( substr( $ret,-1 ) === '.' ) {
                $ret = substr($ret, 0, -1);
            }

            return $ret;
        }
    }
}
