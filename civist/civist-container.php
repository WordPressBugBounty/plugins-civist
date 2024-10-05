<?php
/**
 * The container for the javascript app
 *
 * @package civist
 */

/* translators: The loading text that shows in plugin pages while scripts are loading. */
$loading = _x( 'Loading', 'wp.plugin.loading', 'civist' );

?>
<div id="civist-wrapper">
<h1><?php echo( esc_html( $loading ) ); ?></h1>
</div>
<?php
