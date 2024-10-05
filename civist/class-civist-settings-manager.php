<?php
/**
 * Handle the plugin settings.
 *
 * @package civist
 */

/**
 * Handles the plugin settings.
 */
class Civist_Settings_Manager {
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;
	/**
	 * The nonce action.
	 *
	 * @var string
	 */
	private $nonce_action;
	/**
	 * The client settings action name.
	 *
	 * @var string
	 */
	private $client_action_name;
	/**
	 * The Civist_Settings_Manager class constructor.
	 *
	 * @param string $plugin_slug The slug of the plugin.
	 */
	public function __construct( $plugin_slug ) {
		$this->plugin_slug        = $plugin_slug;
		$this->client_action_name = $plugin_slug . '_settings';
		$this->nonce_action       = $plugin_slug . '_settings_nonce';
	}

	/**
	 * Returns the action to be used with wp_create_nonce
	 *
	 * @return string
	 */
	public function get_nonce_action() {
		return $this->nonce_action;
	}

	/**
	 * Returns the action to be used by the client's form
	 *
	 * @return string
	 */
	public function get_client_action_name() {
		return $this->client_action_name;
	}

	/**
	 * Returns true if the plugin is connected with civist.com
	 *
	 * @return boolean
	 */
	public function is_connected() {
		return $this->is_configured( array( 'api_key', 'api_key_id', 'api_url', 'widget_url', 'oembed_url' ) );
	}

	/**
	 * Returns true if the option is overriden by global define
	 *
	 * @param string $name The name of the option.
	 * @return boolean
	 */
	public function is_overriden( $name ) {
		if ( 'registration_url' === $name && defined( 'CIVIST_REGISTRATION_URL' ) ) {
			return true;
		}
		if ( 'geoip_url' === $name && defined( 'CIVIST_GEOIP_URL' ) ) {
			return true;
		}
		if ( 'widget_url' === $name && defined( 'CIVIST_WIDGET_URL' ) ) {
			return true;
		}
	}

	/**
	 * Handle and persist the plugin registration http call.
	 */
	public function handle_settings_from_client() {
		if ( isset( $_POST['apiKey'], $_POST['apiKeyId'], $_POST['apiUrl'], $_POST['widgetUrl'], $_POST['oEmbedUrl'], $_POST['nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['nonce'] ), $this->nonce_action ) ) { // Input var okay.
			$api_key    = $this->sanitize_key( wp_unslash( $_POST['apiKey'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$api_key_id = sanitize_text_field( wp_unslash( $_POST['apiKeyId'] ) );
			$api_url    = sanitize_text_field( wp_unslash( $_POST['apiUrl'] ) );
			$widget_url = sanitize_text_field( wp_unslash( $_POST['widgetUrl'] ) );
			$oembed_url = sanitize_text_field( wp_unslash( $_POST['oEmbedUrl'] ) );
		} else {
			status_header( 400 );
			wp_die(); // this is required to terminate immediately and return a proper response.
		}

		$options = array(
			'api_key'    => $api_key,
			'api_key_id' => $api_key_id,
			'api_url'    => $api_url,
			'widget_url' => $widget_url,
			'oembed_url' => $oembed_url,
		);
		$this->update( $options );

		status_header( 200 );
	}

	/**
	 * Initializes the plugin options to an array.
	 *
	 * @param array $default_options An array of key value pairs mapping to plugin options.
	 */
	public function initialize_plugin_options( $default_options ) {
		$this->migrate_plugin_options_from_name_to_slug();

		if ( ! is_array( $this->get_all() ) ) {
			$this->set( $default_options );
		}
	}

	/**
	 * Migrates the plugin options that were incorrectly saved using the plugin's name
	 * when it was `Civist` to the plugin slug.
	 */
	private function migrate_plugin_options_from_name_to_slug() {
		$options = $this->get_all();
		if ( is_array( $options ) ) {
			// In case-insensitive config this would always pass wether
			// the settings are kept under `Civist` or `civist` so we should return
			// without changing anything.
			// In case-sensitive config this will fail if the settings were saved under `Civist`.
			return;
		}

		$plugin_name_options = get_option( 'Civist' );
		if ( is_array( $plugin_name_options ) ) {
			// In case-insensitive config this will fail because first test also failed.
			// In case-sensitive config this might pass and if so we should migrate the settings.
			$this->set( $plugin_name_options );
			delete_option( 'Civist' );
		}
	}



	/**
	 * Returns the plugin options.
	 *
	 * @return array
	 */
	public function get_all() {
		$db_options = get_option( $this->plugin_slug );
		if ( defined( 'CIVIST_REGISTRATION_URL' ) ) {
			$db_options['registration_url'] = CIVIST_REGISTRATION_URL;
		}
		if ( defined( 'CIVIST_GEOIP_URL' ) ) {
			$db_options['geoip_url'] = CIVIST_GEOIP_URL;
		}
		if ( defined( 'CIVIST_WIDGET_URL' ) ) {
			$db_options['widget_url'] = CIVIST_WIDGET_URL;
		}
		return $db_options;
	}


	/**
	 * Returns a plugin option by name.
	 *
	 * @param string $name The name of the option.
	 * @return string
	 */
	public function get_option_by_name( $name ) {
		$options = $this->get_all();

		return array_key_exists( $name, $options ) ? $options[ $name ] : '';
	}

	/**
	 * Updates the plugin options.
	 *
	 * @param array $new_options An array of key value pairs mapping to plugin options.
	 */
	public function update( $new_options ) {
		$updated_plugin_options = array_merge( $this->get_all(), $new_options );
		$this->set( $updated_plugin_options );
	}

	/**
	 * Sets the plugin options
	 *
	 * @param array $new_options An array of key value pairs mapping to plugin options.
	 */
	public function set( $new_options ) {
		update_option( $this->plugin_slug, $new_options );
	}

	/**
	 * Takes a list of options and returns true if all options are set and not empty.
	 *
	 * @param array $setting_keys An array of strings representing each option that should be tested.
	 * @return bool
	 */
	private function is_configured( $setting_keys ) {
		$plugin_options = $this->get_all();
		foreach ( $setting_keys as $option_key ) {
			$value = array_key_exists( $option_key, $plugin_options ) ? $plugin_options[ $option_key ] : '';
			if ( ! isset( $value ) || empty( $value ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Sanitize an API key
	 *
	 * @param string $key The key to be sanitized.
	 */
	private function sanitize_key( $key ) {
		$raw_key = $key;
		$key     = preg_replace( '/[^0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*\-+]/', '', $key );
		return apply_filters( 'sanitize_key', $key, $raw_key );
	}
}
