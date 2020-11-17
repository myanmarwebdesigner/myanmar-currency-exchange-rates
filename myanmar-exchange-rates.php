<?php
/**
 * Plugin Name:       Myanmar Currency Exchange Rates
 * Plugin URI:        https://github.com/myanmarwebdesigner/myanmar-currency-exchange-rates
 * Description:       Myanmar daily foreign exchange (forex) rates WordPress plugin. This plugin gets daily exchange rates from Central Bank of Myanmar (CBM).
 * Version:           1.0
 * Requires at least: 2.7.0
 * Requires PHP:      5.4
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       myanmar-currency-exchange-rates
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

// if the file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Currently plugin version
 */
define( 'MYANMAR_EXCHANGE_RATES_VERSION', '1.0' );

/**
 * The core plugin class that is used to define internationalization, 
 * admin-specific hooks, and public-facing site hooks.
 * 
 * @since   1.0
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-myanmar-exchange-rates.php';

// When Activate the plugin
function mwd_mcer_activate()
{
	mwd_mcer_register_widgets();
}
register_activation_hook(__FILE__, 'mwd_mcer_activate');
   
/**
 * Register the new widget.
 *
 * @since   1.0
 */
function mwd_mcer_register_widgets()
{
   register_widget( 'MM_FX_Rates' );
}
add_action( 'widgets_init', 'mwd_mcer_register_widgets' );

// When Deactivate the plugin
function mwd_mcer_deactivate()
{
	unregister_widget('mm_fx_rates');
}
register_activation_hook(__FILE__, 'mwd_mcer_deactivate');

/**
 * Begin execution of the plugin.
 * 
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 * 
 * @since   1.0
 */
function run_myanmar_exchange_rates()
{
   $MCER = new Myanmar_Exchange_Rates();
   $MCER->run();
}
run_myanmar_exchange_rates();

/**
 * Return the main instance of Myanmar_Exchange_Rates
 * 
 * @since   1.0
 * @return  Myanmar_Exchange_Rates
 */
function MWD_MCER()
{
   return Myanmar_Exchange_Rates::instance();
}