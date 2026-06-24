<?php
/**
 * Handles the plugin settings UI.
 *
 * @package civist
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Imports
 */
require_once 'class-civist-registration.php';

/**
 * Handles the plugin settings UI.
 */
class Civist_Settings {
	/**
	 * The plugin settings name.
	 *
	 * @var string
	 */
	private $plugin_settings_name;
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;
	/**
	 * The plugin settings manager.
	 *
	 * @var Civist_Settings_Manager
	 */
	private $settings_manager;
	/**
	 * The plugin connected flag.
	 *
	 * @var bool
	 */
	private $is_plugin_connected;

	/**
	 * The Civist_Settings class constructor.
	 *
	 * @param string                  $plugin_slug The slug of the plugin.
	 * @param Civist_Settings_Manager $settings_manager The plugin settings manager.
	 */
	public function __construct( $plugin_slug, Civist_Settings_Manager $settings_manager ) {
		$this->plugin_slug          = $plugin_slug;
		$this->plugin_settings_name = $this->plugin_slug . '_settings';
		$this->settings_manager     = $settings_manager;
		$this->is_plugin_connected  = $this->settings_manager->is_connected();
	}

	/**
	 * Renders the options page.
	 */
	public function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( "You don't have sufficient permissions to access this page." );
		}
		require_once 'civist-container.php';
	}

	/**
	 * Register the settings link in the plugins page.
	 *
	 * @param array $links The list of links.
	 * @return array
	 */
	public function register_settings_plugin_action_link( $links ) {
		$is_connected = $this->is_plugin_connected;
		// translators: Settings link text.
		$text    = _x( 'Settings', 'wp.plugin.link.settings', 'civist' );
		$page    = $is_connected ? $this->plugin_slug . '-settings' : $this->plugin_slug;
		$url     = $is_connected ? get_admin_url( null, 'options-general.php?page=' . $page ) : get_admin_url( null, 'admin.php?page=' . $page );
		$links[] = '<a href="' . esc_url( $url ) . '">' . $text . '</a>';
		return $links;
	}

	/**
	 * Register the plugin settings using the WordPress settings API.
	 */
	public function register_settings() {
		register_setting(
			$this->plugin_settings_name,
			$this->plugin_slug,
			array(
				'sanitize_callback' => array( $this, 'sanitize_settings_callback' ),
			)
		);
		add_settings_section(
			$this->plugin_settings_name . '_settings_section',
			'Advanced Settings',
			'',
			$this->plugin_settings_name
		);

		add_settings_field(
			'api_key_id',
			'API Key ID',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'api_key_id'
		);

		add_settings_field(
			'api_key',
			'API Key',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'api_key'
		);

		add_settings_field(
			'api_url',
			'API URL',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'api_url'
		);

		add_settings_field(
			'widget_url',
			'Widget URL',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'widget_url'
		);

		add_settings_field(
			'registration_url',
			'Registration URL',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'registration_url'
		);

		add_settings_field(
			'oembed_url',
			'oEmbed URL',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'oembed_url'
		);

		add_settings_field(
			'geoip_url',
			'GeoIP URL',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'geoip_url'
		);

		add_settings_field(
			'version',
			'Version',
			array( $this, 'render_text_field' ),
			$this->plugin_settings_name,
			$this->plugin_settings_name . '_settings_section',
			'version'
		);
	}

	/**
	 * Renders a text field in the settings form.
	 *
	 * @param string $option_key The plugin option field identifier.
	 */
	public function render_text_field( $option_key ) {
		$options = $this->settings_manager->get_all();
		?>
		<input
			type="text"
			id="<?php echo esc_attr( $option_key ); ?>"
			name="<?php echo esc_attr( $this->plugin_slug . '[' . $option_key . ']' ); ?>"
			value="<?php echo array_key_exists( $option_key, $options ) ? esc_html( $options[ $option_key ] ) : ''; ?>"
			<?php
			if ( $this->settings_manager->is_overriden( $option_key ) ) {
				echo 'disabled';
			}
			?>
		>
		<?php
	}

	/**
	 * Renders the advanced options page.
	 *
	 * For internal/testing purposes only.
	 * TODO: Remove when advanced settings are no longer necessary.
	 */
	public function advanced_options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( "You don't have sufficient permissions to access this page." );
		}
		?>
		<form id="civist-settings-form" action='options.php' method='post'>
		<h2>Civist</h2>
		<?php
		settings_fields( $this->plugin_settings_name );
		do_settings_sections( $this->plugin_settings_name );
		submit_button();
		?>
		</form>
		<?php
	}

	/**
	 * Sanitizes the settings array before it is saved to the database.
	 *
	 * @param array $input The raw settings input submitted from the form.
	 * @return array The sanitized settings.
	 */
	public function sanitize_settings_callback( $input ) {
		// Ensure we are working with an array.
		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array();

		// Safely sanitize standard text fields.
		if ( isset( $input['api_key_id'] ) ) {
			$sanitized['api_key_id'] = sanitize_text_field( $input['api_key_id'] );
		}
		if ( isset( $input['version'] ) ) {
			$sanitized['version'] = sanitize_text_field( $input['version'] );
		}

		// Safely sanitize URLs.
		$url_fields = array( 'api_url', 'widget_url', 'registration_url', 'oembed_url', 'geoip_url' );
		foreach ( $url_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized[ $field ] = sanitize_url( $input[ $field ] );
			}
		}

		// Custom sanitizer for API key.
		if ( isset( $input['api_key'] ) ) {
			$sanitized['api_key'] = Civist_Settings_Manager::sanitize_key( $input['api_key'] );
		}

		return $sanitized;
	}
}

