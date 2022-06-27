<?php

/**
 * Exchange rates widget.
 * 
 * @link https://myanmarwebdesigner.com
 * @since   1.0
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes/widgets
 */

/**
 * Exchange rates widget.
 * 
 * List currency exchange rates.
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes/widgets
 * @author  Myanmar Web Designer (MWD) Co., Ltd.
 */

defined('ABSPATH') || exit;

if (!class_exists('MM_FX_Rates')) {
   /**
    * Class MM_FX_Rates to register Widget
    */
   class MM_FX_Rates extends WP_Widget
   {
      /**
       * Constructs the new widget.
       *
       * @see WP_Widget::__construct()
       */
      public function __construct()
      {
         // Instantiate the parent object.
         parent::__construct(
            'mfr',
            'Myanmar Currency Exchange Rates',
            ['description' => __('Myanmar Currency Exchange Rates', 'myanmar-exchange-rates')],
         );
      }

      /**
       * The widget's HTML output.
       * 
       * @since 1.0.0
       * @since 2.0.0 Upgraded number format from fixed to dynamic decimals but without unnecessary zeros. 
       *
       * @see WP_Widget::widget()
       *
       * @param array $args     Display arguments including before_title, after_title,
       *                        before_widget, and after_widget.
       * @param array $instance The settings for the particular instance of the widget.
       */
      public function widget($args, $instance)
      {
         extract($args);
         $title = apply_filters('widget_title', $instance['title']);

         echo $before_widget;
         if (!empty($title)) {
            echo $before_title . $title . $after_title;
         }

         // Get stored options
         $options = get_option('mwd_mcer_options');
         $currency_options = (!empty($options['mwd_mcer_field_currencies']))
            ? $options['mwd_mcer_field_currencies']
            : MWD_MCER()->cbm_exchange_rates()->get_default_currencies();

         $fxrates = array();
         $fxrates = MWD_MCER()->cbm_exchange_rates()->get_fxrates();
         $rate_timestamp = MWD_MCER()->cbm_exchange_rates()->get_fxtimestamp();

         // filter $rates with $currency_options
         // and add to $rates
         $rates = array();
         foreach ($currency_options as $option) {
            if (isset($fxrates[$option]))
               $rates[$option] = $fxrates[$option];
         }

         if (count($rates) > 0) {
            // sort the $rates
            if ($instance['order'] === 'name_asc') {
               // Sort an array by key.
               ksort($rates);
            } elseif ($instance['order'] === 'name_desc') {
               // Sort an array by key in reverse order.
               krsort($rates);
            } elseif (($instance['order'] === 'rates_asc') || ($instance['order'] === 'rates_desc') || $instance['order'] === '--') {
               // Remove the `,`.
               foreach ($rates as &$value) {
                  $value = str_replace(',', '', $value);
               }

               unset( $value ); // break the reference with the last element

               if ($instance['order'] === 'rates_asc') {
                  // sort $rates.
                  asort($rates, SORT_NUMERIC);
               } else {
                  // reverse-sort $rates.
                  arsort($rates, SORT_NUMERIC);
               }

               // format the numbers
               foreach ($rates as &$value) {
                  $value = $this->num_format( $value );
               }

               unset( $value ); // break the reference with the last element
            }
?>

            <p class="description">
               <small>The Central Bank of Myanmar (CBM) updated this rates on MMT <span class="text-danger"><?php echo date('F j, Y - l g:i A', $rate_timestamp + 6.5 * 3600) ?></span></small>
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
                  $d_mode = (!empty($options['mwd_mcer_field_display_modes']))
                     ? $options['mwd_mcer_field_display_modes']
                     : 'normal';

                  if ($d_mode === 'compact') {
                     foreach ($rates as $key => $value) :
                  ?>

                        <tr style="border:0;">
                           <td style="padding:8px 16px;border:1px solid #ddd;line-height:normal;font-size:15px;font-weight:400;">
                              <?php echo esc_html('1 ' . $key); ?>
                           </td>
                           <td style="padding:8px 16px;border:1px solid #ddd;line-height:normal;font-size:15px;font-weight:400;">
                              <?php echo esc_html($value); ?>
                           </td>
                        </tr>

                     <?php
                     endforeach;
                  } elseif ($d_mode === 'normal') {
                     // Get fxcurrencies.
                     $fxcurrencies = MWD_MCER()->cbm_exchange_rates()->get_fxcurrencies();

                     foreach ($rates as $key => $value) :
                     ?>

                        <tr style="border:0;">
                           <td style="padding:5px 16px;border:1px solid #ddd;line-height:normal;">
                              <span style="display:inline-block;width: 100%;font-size:15px;font-weight:400;">
                                 <?php echo esc_html('1 ' . $key); ?>
                              </span>
                              <small style="font-size:12px;font-weight:400;"><?php echo esc_html($fxcurrencies[$key]); ?></small>
                           </td>
                           <td style="padding:5px 16px;border:1px solid #ddd;line-height:normal;font-size:15px;font-weight:400;">
                              <?php echo esc_html($value); ?>
                           </td>
                        </tr>

                  <?php
                     endforeach;
                  }
                  ?>
               </tbody>
            </table>

         <?php
         } else {
            echo '<p>Something went wrong!</p>';
         }
         echo $after_widget;
      }

      /**
       * The widget update handler.
       *
       * @see WP_Widget::update()
       *
       * @param array $new_instance The new instance of the widget.
       * @param array $old_instance The old instance of the widget.
       * @return array The updated instance of the widget.
       */
      public function update($new_instance, $old_instance)
      {
         $instance = array();
         $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
         $instance['order'] = (!empty($new_instance['order'])) ? strip_tags($new_instance['order']) : '';

         return $instance;
      }

      /**
       * Output the admin widget options form HTML.
       *
       * @param array $instance The current widget settings.
       * @return string The HTML markup for the form.
       */
      public function form($instance)
      {
         $title = (!empty($instance['title'])) ? $instance['title'] : esc_html__('Daily Exchange Rates', 'myanmar-exchange-rates');
         $order = (!empty($instance['order'])) ? $instance['order'] : esc_html__('--', 'myanmar-exchange-rates');
         ?>
         <p>
            <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
         </p>
         <p>
            <label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order by: ', 'myanmar-exchange-rates'); ?></label>
            <select name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>" class="widefat">
               <!-- <option value="--" <?php //echo ($order === 'name_asc') ? ' selected ' : ''; 
                                       ?>><?php //esc_html_e('---'); 
                                          ?></option> -->
               <option value="rates_desc" <?php echo (empty($order) || $order === 'rates_desc') ? ' selected ' : ''; ?>><?php esc_html_e('Rates: DESC', 'myanmar-exchange-rates'); ?></option>
               <option value="rates_asc" <?php echo ($order === 'rates_asc') ? ' selected ' : ''; ?>><?php esc_html_e('Rates: ASC', 'myanmar-exchange-rates'); ?></option>
               <option value="name_desc" <?php echo ($order === 'name_desc') ? ' selected ' : ''; ?>><?php esc_html_e('Name: Z » A', 'myanmar-exchange-rates'); ?></option>
               <option value="name_asc" <?php echo ($order === 'name_asc') ? ' selected ' : ''; ?>><?php esc_html_e('Name: A » Z', 'myanmar-exchange-rates'); ?></option>
            </select>
         </p>
         <p>
            <a style="text-decoration: none;" href="<?php echo admin_url('options-general.php?page=mwd_mcer'); ?>" title="Setting options"><span class="dashicons dashicons-admin-settings"></span> <?php _e('Configure', 'myanmar-exchange-rates'); ?></a>
         </p>
<?php
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

         while( substr( $ret, -1 ) === "0" ) {
               // if number ends with a '0', remove '0' from end of string
               $ret = substr( $ret, 0, -1 );
         }

         if ( substr( $ret,-1 ) === '.' ) {
               $ret = substr( $ret, 0, -1 );
         }

         return $ret;
      }
   }
}
