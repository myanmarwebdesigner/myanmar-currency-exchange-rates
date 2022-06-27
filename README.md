# myanmar-fx-rates

Myanmar daily foreign exchange rates WordPress plugin. This plugin gets daily exchange rates from Central Bank of Myanmar (CBM).

## How to use?

- Install the plugin [Myanmar Currency Exchange Rates](https://wordpress.org/plugins/myanmar-currency-exchange-rates/)
- Add widget to sidebar
- Choose Display mode and Currencies on setting page. ( _Settings > Myanmar Exchange Rates_ )

## Ways to show fxrates?

1. Via Plugin's Widget
2. Via Shortcode
3. Via Template tag

## Shortcode

_Example without parameter_

```
[mm_fxrates]
```

_Example with parameters_

```
/**
 * mode: Default from setting page. Accepts normal, compact.
 * orderby: Default name. Accepts name, rate.
 * order: Default asc. Accepts asc, desc.
 */

[mm_fxrates mode="compact" orderby="rate" order="desc"]
```

## Template tag

```PHP
<?php
/**
 * $atts Optional.
 *
 * mode: Default normal. Accepts normal, compact.
 * orderby: Default name. Accepts name, rate.
 * order: Default asc. Accepts asc, desc.
 */

// Default values
$atts = array(
    'mode' => 'normal',
    'orderby' => 'name',
    'order' => 'asc'
);

// Use in wordpress template
mwd_mm_fxrates( $atts );
?>
```
