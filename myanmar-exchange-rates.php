<?php
/**
 * Plugin Name:       Myanmar Currency Exchange Rates by MWD
 * Plugin URI:        https://github.com/gtu-myowin/myanmar-exchange-rates
 * Description:       Myanmar daily foreign exchange (forex) rates WordPress plugin. This plugin gets daily exchange rates from Central Bank of Myanmar (CBM).
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       myanmar-exchange-rates
 * Author:            Myanmar Web Designer (MWD) Co., Ltd.
 * Author URI:        https://www.myanmarwebdesigner.com
 */

 /*
Myanmar Currency Exchange Rates is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Myanmar Currency Exchange Rates is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Myanmar Currency Exchange Rates. If not, see {URI to Plugin License}.
*/

include(plugin_dir_path(__FILE__) . 'admin//option-page.php');

if (!class_exists('mm_fx_rates')) {
	/**
	 * Class mm_fx_rates to register Widget
	 */
	class mm_fx_rates extends WP_Widget {
	
		/**
		 * Constructs the new widget.
		 *
		 * @see WP_Widget::__construct()
		 */
		function __construct() {
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
		function widget( $args, $instance ) {
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
}

// Register the new widget.
function mwd_mcer_register_widgets() {
    register_widget( 'mm_fx_rates' );
}
add_action( 'widgets_init', 'mwd_mcer_register_widgets' );


// When Activate the plugin
function mwd_mcer_activate()
{
	mwd_mcer_register_widgets();
}
register_activation_hook(__FILE__, 'mwd_mcer_activate');

// When Deactivate the plugin
function mwd_mcer_deactivate()
{
	unregister_widget('mm_fx_rates');
}
register_activation_hook(__FILE__, 'mwd_mcer_deactivate');