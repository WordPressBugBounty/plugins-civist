<?php
/**
 * The Civist plugin class.
 *
 * @package civist
 */

/**
 * The Civist plugin.
 */
class Civist {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	private $plugin_name;
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;
	/**
	 * The plugin text domain.
	 *
	 * @var string
	 */
	private $plugin_text_domain;
	/**
	 * The plugin file.
	 *
	 * @var string
	 */
	private $plugin_file;
	/**
	 * The plugin registration url.
	 *
	 * @var string
	 */
	private $registration_url;
	/**
	 * The url of the geoip service.
	 *
	 * @var string
	 */
	private $geoip_url;
	/**
	 * The widget supported languages.
	 *
	 * @var array
	 */
	private $widget_supported_languages;
	/**
	 * The plugin settings manager.
	 *
	 * @var Civist_Settings_Manager
	 */
	private $settings_manager;
	/**
	 * Enforces HTTPS
	 *
	 * @var bool
	 */
	private $enforce_https;

	/**
	 * The Civist class constructor
	 *
	 * @param string $version The version of the plugin.
	 * @param string $plugin_name The name of the plugin.
	 * @param string $plugin_slug The slug of the plugin.
	 * @param string $plugin_text_domain The text domain of the plugin.
	 * @param string $plugin_file The file of the plugin.
	 * @param string $registration_url The default registration URL for Civist.
	 * @param string $geoip_url The url of the GeoIP service.
	 * @param array  $widget_supported_languages The widget supported languages.
	 * @param bool   $enforce_https Enforces HTTPS.
	 */
	public function __construct( $version, $plugin_name, $plugin_slug, $plugin_text_domain, $plugin_file, $registration_url, $geoip_url, $widget_supported_languages, $enforce_https ) {
		$this->version                    = $version;
		$this->plugin_name                = $plugin_name;
		$this->plugin_slug                = $plugin_slug;
		$this->plugin_text_domain         = $plugin_text_domain;
		$this->plugin_file                = $plugin_file;
		$this->registration_url           = $registration_url;
		$this->geoip_url                  = $geoip_url;
		$this->widget_supported_languages = $widget_supported_languages;
		$this->enforce_https              = $enforce_https;
		$this->load_dependencies();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->settings_manager = new Civist_Settings_Manager( $this->plugin_slug );
		$this->oembed_provider  = new Civist_OEmbed( $this->plugin_file, $this->plugin_slug, $this->settings_manager );
		$this->settings_manager->initialize_plugin_options(
			array(
				'version'          => $this->version,
				'registration_url' => $this->registration_url,
				'geoip_url'        => $this->geoip_url,
			)
		);
		if ( is_admin() ) {
			$this->plugin_jwt = new Civist_Jwt( $this->plugin_slug, $this->settings_manager );
		} else {
			$this->plugin_jwt = null;
		}
		$this->plugin_scripts = new Civist_Scripts( $this->plugin_name, $this->plugin_file, $this->plugin_slug, $this->settings_manager, $this->widget_supported_languages, $this->enforce_https, $this->plugin_jwt );

		$this->handle_version_change();
		$this->define_non_admin_hooks();
		$this->define_admin_hooks();
	}

	/**
	 * Updates the default settings when there is a version change.
	 */
	private function handle_version_change() {
		$current_version = $this->settings_manager->get_option_by_name( 'version' );
		if ( $current_version !== $this->version ) {
			$this->settings_manager->update(
				array(
					'version'          => $this->version,
					'registration_url' => $this->registration_url,
					'geoip_url'        => $this->geoip_url,
				)
			);

			$this->oembed_provider->clear_cache();
		}
	}

	/**
	 * Loads the plugin dependencies.
	 */
	private function load_dependencies() {
		require_once 'class-civist-settings-manager.php';
		require_once 'class-civist-oembed.php';
		require_once 'class-civist-i18n.php';
		require_once 'class-civist-shortcode.php';
		require_once 'class-civist-settings.php';
		require_once 'class-civist-scripts.php';
		require_once 'class-civist-editor.php';
		if ( is_admin() ) {
			require_once 'class-civist-jwt.php';
			require_once 'class-civist-admin.php';
			require_once 'class-civist-registration.php';
		}
	}

	/**
	 * Registers all of the hoks specific to the admin area.
	 */
	private function define_admin_hooks() {
		if ( is_admin() ) {
			add_action( 'wp_ajax_' . $this->settings_manager->get_client_action_name(), array( $this->settings_manager, 'handle_settings_from_client' ) );

			add_action( 'wp_ajax_' . $this->plugin_jwt->get_client_action_name(), array( $this->plugin_jwt, 'serve_jwt_token' ) );

			$plugin_settings = new Civist_Settings( $this->plugin_slug, $this->settings_manager );
			add_action( 'admin_init', array( $plugin_settings, 'register_settings' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $plugin_settings, 'register_settings_plugin_action_link' ) );

			add_filter( 'script_loader_tag', array( $this->plugin_scripts, 'add_async' ), 11, 2 );
			$plugin_editor = new Civist_Editor( $this->plugin_name, $this->plugin_file, $this->plugin_slug, $this->plugin_scripts );
			$plugin_admin  = new Civist_Admin( $this->plugin_name, $this->plugin_file, $this->plugin_slug, $this->settings_manager, $plugin_settings, $this->plugin_scripts, $plugin_editor );
			add_action( 'admin_menu', array( $plugin_admin, 'add_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $plugin_admin, 'add_reconnect_link' ), 10, 2 );

			if ( ! $this->settings_manager->is_connected() ) {
				$plugin_registration = new Civist_Registration( $this->plugin_slug );
				add_action( 'load-plugins.php', array( $plugin_registration, 'show_connect_notice' ) );
				add_action( 'load-index.php', array( $plugin_registration, 'show_connect_notice' ) );
			} else {
				add_action( 'media_buttons', array( $plugin_editor, 'add_petition_media_button' ) );
			}
		}
	}

	/**
	 * Define oembed hook.
	 */
	public function define_oembed_hook() {
		add_filter( 'embed_oembed_html', array( $this->oembed_provider, 'wrap_widget' ), 10, 4 );
	}

	/**
	 * Registers all of the hooks not specific to the admin area.
	 */
	private function define_non_admin_hooks() {
		$plugin_i18n = new Civist_I18n( $this->plugin_text_domain );
		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_textdomain' ) );

		if ( $this->settings_manager->is_connected() ) {
			add_action( 'init', array( $this->oembed_provider, 'oembed_provider' ) );
			add_filter( 'http_request_host_is_external', array( $this->oembed_provider, 'allow_localhost' ), 10, 3 );

			$this->define_oembed_hook();
			// This is necessary because some plugins (like "Instant Articles for WP") remove all `embed_oembed_html` filters.
			add_action( 'plugins_loaded', array( $this, 'define_oembed_hook' ) );

			$plugin_shortcode = new Civist_Shortcode( $this->plugin_slug );
			add_action( 'init', array( $plugin_shortcode, 'register_shortcode' ) );
			$plugin_editor = new Civist_Editor( $this->plugin_name, $this->plugin_file, $this->plugin_slug, $this->plugin_scripts );
			add_action( 'init', array( $plugin_editor, 'register_plugin_blocks' ) );
		}
	}
}
