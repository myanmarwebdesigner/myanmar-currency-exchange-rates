<?php
/**
 * Register all the actions and filters for the plugin
 * 
 * @link https://myanmarwebdesigner.com
 * @since   1.0
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes
 */

/**
 * Register all the actions and filters for the plugin.
 * 
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the Wordpress API.
 * Call the run function to execute the list of actions and filters.
 * 
 * @package Myanmar_Exchange_Rates
 * @subpackage Myanmar_Exchange_Rates/includes
 * @author  Myanmar Web Designer (MWD) Co., Ltd. 
 */

defined( 'ABSPATH' ) || exit;

class Myanmar_Exchange_Rates_Loader
{
   /**
    * The array of actions registered with the Wordpress.
    *
    * @since   1.0
    * @access  protected
    * @var  array $actions The actions registered with Wordpress to fire when the plugin loads.
    */
   protected $actions;

   /**
    * The array of filters registered with the Wordpress.

    * @since   1.0
    * @access  protected
    * @var  array $filters The filters registered with Wordpress to fire when the plugin loads.
    */
   protected $filters;

   /**
    * Initialize the collections used to maintain the actions and filters.
    *
    * @since   1.0
    */
   public function __construct()
   {
      $this->actions = array();
      $this->filters = array();
   }

   /**
    * Add a new action to the collection to be registered with Wordpress.
    *
    * @since   1.0
    * @param   string   $hook    The name of the Wordpress action that is being registered.
    * @param   object   $component  A reference to the instance of the object on which the action is defined.
    * @param   string   $callback   The name of the function definition on the $component.
    * @param   int      $priority   Optional. The priority at which the function should be fired. Default is 10.
    * @param   int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
    */
   public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 )
   {
      $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
   }   

   /**
    * Add a new filter to the collection to be registered with Wordpress.
    *
    * @since   1.0
    * @param   string   $hook    The name of the Wordpress filter that is being registered.
    * @param   object   $component  A reference to the instance of the object on which the action is defined.
    * @param   string   $callback   The name of the function definition on the $component.
    * @param   int      $priority   Optional. The priority at which the function should be fired. Default is 10.
    * @param   int      $accepted_args Optional. The number of arguments that should be passed to the $callback. Default is 1.
    */
   public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 )
   {
      $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
   }

   /**
    * A utility function that is used to register actions and filters into
    * a single collecion.
    *
    * @since   1.0
    * @access  private
    * @param   array    $hooks         The collection of hooks that is befing registered (that is, actions or filters).
    * @param   string   $hook          The name of the Wordpress hook that is being registered.
    * @param   object   $component     A reference to the instance of the object on whick the filter is defined.
    * @param   string   $callback      The name of the function definition on the $component.
    * @param   int      $priority      The priority at which the function should be fired.
    * @param   int      $accepted_args The number of arguments that should be passed to the $callback.
    * @return  array                   The collection of actions and filters registered with Wordpress.
    */
   private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args )
   {
      $hooks[] = array(
         'hook'   => $hook,
         'component' => $component,
         'callback'  => $callback,
         'priority'  => $priority,
         'accepted_args'   => $accepted_args,
      );

      return $hooks;
   }

   /**
    * Register all the actions and filters with Wordpress.
    *
    * @since   1.0
    */
   public function run()
   {
      // Add all the action hooks
      foreach( $this->actions as $hook ) {
         add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
      }

      // Add all the filter hooks
      foreach( $this->filters as $hook ) {
         add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
      }
   }
}