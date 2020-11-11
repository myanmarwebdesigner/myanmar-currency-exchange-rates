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

if ( ! class_exists( 'mm_fx_rates' ) ) {
	/**
	 * Class mm_fx_rates to register Widget
	 */
   class mm_fx_rates extends WP_Widget
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
	
			// echo $before_widget;
			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}
			// echo $after_widget;
			
         $response = wp_remote_get( 'http://forex.cbm.gov.mm/api/latest' );
			$body = wp_remote_retrieve_body( $response );
			$fxrates = json_decode($body);

			if ($fxrates != Null) {
			?>
				<p class="text-danger">Updated on <strong><?php echo date('j, F, Y', 1596096000) ?></strong></p>
				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">Currency</th>
							<th scope="col">MMK</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo '1 USD'; ?></td>
							<td><?php echo $fxrates->rates->USD; ?></td>
						</tr>
						<tr>
							<td><?php echo '1 EUR'; ?></td>
							<td><?php echo $fxrates->rates->EUR; ?></td>
						</tr>
						<tr>
							<td><?php echo '1 SGD'; ?></td>
							<td><?php echo $fxrates->rates->SGD; ?></td>
						</tr>
						<tr>
							<td><?php echo '1 JPY'; ?></td>
							<td><?php echo $fxrates->rates->JPY; ?></td>
						</tr>
						<tr>
							<td><?php echo '1 THB'; ?></td>
							<td><?php echo $fxrates->rates->THB; ?></td>
						</tr>
						<tr>
							<td><?php echo '1 MYR'; ?></td>
							<td><?php echo $fxrates->rates->MYR; ?></td>
						</tr>
						<tr>
							<td><?php echo '1 AUD'; ?></td>
							<td><?php echo $fxrates->rates->AUD; ?></td>
						</tr>
					</tbody>
				</table>

			<?php
			}
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
		function update( $new_instance, $old_instance ) {
			return $new_instance;
		}
	
		/**
		 * Output the admin widget options form HTML.
		 *
		 * @param array $instance The current widget settings.
		 * @return string The HTML markup for the form.
		 */
		function form( $instance ) {
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			}
			else {
				$title = __( 'Daily Exchange Rates', 'myanmar-exchange-rates' );
			}
			?>
				<p>
					<label for="<?php echo $this->get_field_name( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
			<?php
		}
   }
   
   // Register the new widget.
   function mwd_mcer_register_widgets()
   {
      register_widget( 'mm_fx_rates' );
   }
   add_action( 'widgets_init', 'mwd_mcer_register_widgets' );
}