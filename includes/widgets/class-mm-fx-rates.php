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

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'MM_FX_Rates' ) ) {
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
		public function __construct() {
			// Instantiate the parent object.
			parent::__construct(
				'mfr',
				'Myanmar Currency Exchange Rates',
				['description' => __( 'Myanmar Currency Exchange Rates', 'myanmar-exchange-rates' )],
         );
		}
	
		/**
		 * The widget's HTML output.
		 *
		 * @see WP_Widget::widget()
		 *
		 * @param array $args     Display arguments including before_title, after_title,
		 *                        before_widget, and after_widget.
		 * @param array $instance The settings for the particular instance of the widget.
		 */
      public function widget( $args, $instance )
      {
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'] );
	
			echo $before_widget;
			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}
         
         // Get stored options
         $options = get_option( 'mwd_mcer_options' );
         $currency_options = ( empty( $options['mwd_mcer_field_currencies'] ) ) ? MWD_MCER()->cbm_exchange_rates()->get_default_currencies() : $options['mwd_mcer_field_currencies'];
         
         $rates = array();
         $fxrates_body = MWD_MCER()->cbm_exchange_rates()->get_fxrates_body();
         $rates = $fxrates_body['rates'];
         $rate_timestamp = $fxrates_body['timestamp'];

         // filter $rates with $currency_options
         // and add to $fxrates
         $fxrates = array();
         foreach ( $currency_options as $option ) {
            if ( isset( $rates[$option] ) )
               $fxrates[$option] = $rates[$option];
         }

			if ( count( $fxrates ) > 0 ) {
            // sort the $fxrates
            if ( $instance['order'] === 'name_asc' || $instance['order'] === '--' ) {
               // Sort an array by key.
               ksort( $fxrates );
            } elseif ( $instance['order'] === 'name_desc' ) {
               // Sort an array by key in reverse order.
               krsort( $fxrates );
            } elseif ( ( $instance['order'] === 'rates_asc' ) || ( $instance['order'] === 'rates_desc' ) ) {
               // Remove the `,`.
               foreach( $fxrates as &$value ) {
                  $value = str_replace( ',', '', $value );
               }

               if ( $instance['order'] === 'rates_asc' ) {
                  // sort $fxrates.
                  asort( $fxrates, SORT_NUMERIC );
               } else {                  
                  // reverse-sort $fxrates.
                  arsort( $fxrates, SORT_NUMERIC );
               }

               // format the numbers
               foreach( $fxrates as &$value ) {
                  $value = number_format( $value, 4 );
               }
            }
            ?>

				<p class="description">
               <small>The Central Bank of Myanmar (CBM) updated this rates on GMT <span class="text-danger"><?php echo date( 'F j, Y - l g:i a', $rate_timestamp ) ?></span></small>
            </p>
				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">Currency</th>
							<th scope="col">MMK</th>
						</tr>
					</thead>
					<tbody>
                  <?php
                  $d_mode = empty( get_option( 'mwd_mcer_options')['mwd_mcer_field_display_modes'] )
                     ? 'compact'
                     : get_option( 'mwd_mcer_options')['mwd_mcer_field_display_modes'];

                  if ( $d_mode === 'compact' ) {
                     foreach ( $fxrates as $key => $value ) :
                     ?>
   
                        <tr>
                           <td style="padding:.5rem;"><?php echo esc_html( '1 ' . $key ); ?></td>
                           <td style="padding:.5rem;"><?php echo esc_html( $value ); ?></td>
                        </tr>
   
                     <?php
                     endforeach;
                  } elseif ( $d_mode === 'normal' ) {
                     // Get fxcurrencies.
                     $fxcurrencies = MWD_MCER()->cbm_exchange_rates()->get_fxcurrencies();

                     foreach ( $fxrates as $key => $value ) :
                     ?>

                        <tr>
                           <td>
                              <span><?php echo esc_html( '1 ' . $key ); ?></span><br>
                              <small><?php echo esc_html( $fxcurrencies[$key] ); ?></small>
                           </td>
                           <td><?php echo esc_html( $value ); ?></td>
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
		public function update( $new_instance, $old_instance ) {
         $instance = array();
         $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title']) : '';
         $instance['order'] = ( ! empty( $new_instance['order'] ) ) ? strip_tags( $new_instance['order']) : '';

			return $instance;
		}
	
		/**
		 * Output the admin widget options form HTML.
		 *
		 * @param array $instance The current widget settings.
		 * @return string The HTML markup for the form.
		 */
		public function form( $instance ) {
         $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : esc_html__( 'Daily Exchange Rates', 'myanmar-exchange-rates' );
         $order = ( ! empty( $instance['order'] ) ) ? $instance['order'] : esc_html__( '--', 'myanmar-exchange-rates' );
			?>
         <p>
            <label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
         </p>
         <p>
            <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order by: ', 'myanmar-exchange-rates' ); ?></label>
            <select name="<?php echo $this->get_field_name( 'order' ); ?>" id="<?php echo $this->get_field_id( 'order' ); ?>" class="widefat">
               <option value="--" <?php echo ( $order === 'name_asc' ) ? ' selected ' : ''; ?>><?php esc_html_e( '---' ); ?></option>
               <option value="name_asc" <?php echo ( $order === 'name_asc' ) ? ' selected ' : ''; ?>><?php esc_html_e( 'Name ASC', 'myanmar-exchange-rates' ); ?></option>
               <option value="name_desc" <?php echo ( $order === 'name_desc' ) ? ' selected ' : ''; ?>><?php esc_html_e( 'Name DESC', 'myanmar-exchange-rates' ); ?></option>
               <option value="rates_asc" <?php echo ( $order === 'rates_asc' ) ? ' selected ' : ''; ?>><?php esc_html_e( 'Rates ASC', 'myanmar-exchange-rates' ); ?></option>
               <option value="rates_desc" <?php echo ( $order === 'rates_desc' ) ? ' selected ' : ''; ?>><?php esc_html_e( 'Rates DESC', 'myanmar-exchange-rates' ); ?></option>
            </select>
         </p>
         <p>
            <a href="<?php menu_page_url( 'mwd_mcer' ); ?>" title="Setting options"><?php _e('Configure', 'myanmar-exchange-rates' ); ?></a>
         </p>
			<?php
		}
   }
}