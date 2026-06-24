<?php
/**
 * The container for the javascript app
 *
 * @package civist
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/* translators: The loading text that shows in plugin pages while scripts are loading. */
$civist_loading = _x( 'Loading', 'wp.plugin.loading', 'civist' );

?>
<div id="civist-wrapper">
<h1><?php echo( esc_html( $civist_loading ) ); ?></h1>
</div>
<?php
