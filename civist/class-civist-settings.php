<?php
/**
 * Handles the plugin settings UI.
 *
 * @package civist
 */

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
		register_setting( $this->plugin_settings_name, $this->plugin_slug );
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
}

