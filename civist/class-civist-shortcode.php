<?php
/**
 * Register the plugin shortcode provider.
 *
 * @package civist
 */

/**
 * Register the plugin shortcode provider.
 */
class Civist_Shortcode {
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * The Civist_Shortcode class constructor.
	 *
	 * @param string $plugin_slug The slug of the plugin.
	 */
	public function __construct( $plugin_slug ) {
		$this->plugin_slug = $plugin_slug;
	}

	/**
	 * Registers the plugin shortcode provider.
	 */
	public function register_shortcode() {
		add_shortcode( $this->plugin_slug, array( $this, 'shortcode_provider' ) );
	}

	/**
	 * Handle shortcode.
	 *
	 * @param string $atts The shortcode attributes.
	 */
	public function shortcode_provider( $atts ) {
		global $wp_embed;
		$atts = shortcode_atts(
			array(
				'src'  => '',
				'mode' => 'default',
			),
			$atts
		);
		return $wp_embed->shortcode(
			array(
				'mode' => $atts['mode'],
			),
			$atts['src']
		);
	}
}
