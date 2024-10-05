<?php
/**
 * The Civist plugin i18n class
 *
 * @package civist
 */

/**
 * The Civist plugin i18n class.
 */
class Civist_I18n {

	/**
	 * The plugin text domain.
	 *
	 * @var string
	 */
	private $plugin_text_domain;

	/**
	 * The Civist class constructor
	 *
	 * @param string $plugin_text_domain The text domain of the plugin.
	 */
	public function __construct( $plugin_text_domain ) {
		$this->plugin_text_domain = $plugin_text_domain;
	}

	/**
	 * Loads the plugin text domain for translations.
	 */
	public function load_textdomain() {
		$plugin_dir = dirname( plugin_basename( __FILE__ ) );
		load_plugin_textdomain( $this->plugin_text_domain, false, $plugin_dir );
	}
}
