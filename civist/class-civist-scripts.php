<?php
/**
 * The plugin scripts related functionality
 *
 * @package civist
 */

/**
 * Handles the plugin scripts' registration.
 */
class Civist_Scripts {
	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	private $plugin_name;
	/**
	 * The plugin file.
	 *
	 * @var string
	 */
	private $plugin_file;
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;
	/**
	 * The id of the media button in the post/page editor.
	 *
	 * @var string
	 */
	private $editor_button_id;
	/**
	 * The plugin jwt manager.
	 *
	 * @var Civist_Jwt_Manager
	 */
	/**
	 * The plugin settings manager.
	 *
	 * @var Civist_Settings_Manager
	 */
	private $settings_manager;
	/**
	 * The widget supported languages.
	 *
	 * @var array
	 */
	private $widget_supported_languages;
	/**
	 * Enforces HTTPS
	 *
	 * @var bool
	 */
	private $enforce_https;

	/**
	 * The Civist_Admin class constructor
	 *
	 * @param string                  $plugin_name The name of the plugin.
	 * @param string                  $plugin_file The file of the plugin.
	 * @param string                  $plugin_slug The slug of the plugin.
	 * @param Civist_Settings_Manager $settings_manager The plugin settings manager.
	 * @param array                   $widget_supported_languages The widget supported languages.
	 * @param bool                    $enforce_https Enforces HTTPS.
	 * @param Civist_Jwt              $jwt The plugin jwt manager.
	 */
	public function __construct( $plugin_name, $plugin_file, $plugin_slug, Civist_Settings_Manager $settings_manager, $widget_supported_languages, $enforce_https, Civist_Jwt $jwt = null ) {
		$this->plugin_name                = $plugin_name;
		$this->plugin_file                = $plugin_file;
		$this->plugin_slug                = $plugin_slug;
		$this->settings_manager           = $settings_manager;
		$this->jwt                        = $jwt;
		$this->widget_supported_languages = $widget_supported_languages;
		$this->enforce_https              = $enforce_https;
	}

	/**
	 * Register and enqueue the civist icon font styles
	 *
	 * @param string $webpack_files The files to inject.
	 */
	public function enqueue_civist_icon_font( $webpack_files ) {
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		wp_enqueue_style( 'civist_font', $plugin_dir_url . $webpack_files->civist2017->css[0], array(), $webpack_files->civist2017->hash );
	}

	/**
	 * Register and enqueue the plugin scripts for the plugin registration page
	 *
	 * @param string $webpack_files The files to inject.
	 */
	public function enqueue_registration_scripts( $webpack_files ) {
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$name           = 'civist_script';
		wp_register_script( $name, $plugin_dir_url . $webpack_files->registration->entry, array(), $webpack_files->registration->hash, true );
		$params = array_merge(
			$this->get_management_configuration(),
			array(
				'registrationNonce'  => wp_create_nonce( $this->settings_manager->get_nonce_action() ),
				'registrationUrl'    => $this->settings_manager->get_option_by_name( 'registration_url' ),
				'settingsAjaxAction' => $this->settings_manager->get_client_action_name(),

			)
		);
		wp_localize_script( $name, 'civist', $params );
		$this->inject_vendor( $plugin_dir_url, $webpack_files );
		wp_enqueue_script( $name );
	}

	/**
	 * Register and enqueue the plugin scripts for the plugins page
	 *
	 * @param string $webpack_files The files to inject.
	 * @param string $reconnect_link_id The id of the reconnect plugin action link element.
	 */
	public function enqueue_plugins_scripts( $webpack_files, $reconnect_link_id ) {
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$name           = 'civist_script';
		wp_enqueue_media();
		wp_register_script( $name, $plugin_dir_url . $webpack_files->plugins->entry, array(), $webpack_files->plugins->hash, true );
		$params = array_merge(
			$this->get_management_configuration(),
			array(
				'apiKeyId'           => $this->settings_manager->get_option_by_name( 'api_key_id' ),
				'reconnectLinkId'    => $reconnect_link_id,
				'reconnectNonce'     => wp_create_nonce( $this->settings_manager->get_nonce_action() ),
				'settingsAjaxAction' => $this->settings_manager->get_client_action_name(),

			)
		);
		wp_localize_script( $name, 'civist', $params );
		$this->inject_vendor( $plugin_dir_url, $webpack_files );
		wp_enqueue_script( $name );
	}

	/**
	 * Register and enqueue the plugin scripts for the management pages
	 *
	 * @param string $webpack_files The files to inject.
	 */
	public function enqueue_manager_scripts( $webpack_files ) {
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$name           = 'civist_script';
		wp_enqueue_media();
			wp_register_script( $name, $plugin_dir_url . $webpack_files->manager->entry, array(), $webpack_files->manager->hash, true );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_editor(
				'',
				'civist',
				array(
					'editor_height' => 0,
					'quicktags'     => false,
					'media_buttons' => false,
					'tinymce'       => false,
					'editor_class'  => 'hidden',
				)
			);
			$this->inject_vendor( $plugin_dir_url, $webpack_files );
			$params = $this->get_management_configuration();
		wp_localize_script( $name, 'civist', $params );
		wp_enqueue_script( $name );
	}

	/**
	 * Register and enqueue the plugin scripts for the plugin settings page
	 *
	 * @param string $webpack_files The files to inject.
	 */
	public function enqueue_settings_scripts( $webpack_files ) {
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$name           = 'civist-script';
		$params         = $this->get_management_configuration();
		wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'media-views' );
			wp_register_script( $name, $plugin_dir_url . $webpack_files->settings->entry, array(), $webpack_files->settings->hash, true );
			$this->inject_vendor( $plugin_dir_url, $webpack_files );
			wp_localize_script( $name, 'civist', $params );
			wp_enqueue_script( $name );
	}

	/**
	 * Register and enqueue the plugin scripts for the post and page editor
	 *
	 * @param string $webpack_files The files to inject.
	 * @param string $open_editor_modal_callback The name of the callback registered on window that opens the embed modal.
	 * @param string $embed_block_name The name of the embed block.
	 * @param string $form_block_name The name of the form block.
	 * @param string $progress_block_name The name of the progress block.
	 * @param string $civist_block_name The name of the civist block.
	 */
	public function enqueue_editor_scripts( $webpack_files, $open_editor_modal_callback, $embed_block_name, $form_block_name, $progress_block_name, $civist_block_name ) {
		$name           = 'civist-script';
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$params         = array_merge(
			$this->get_management_configuration(),
			array(
				'openEditorModalCallback' => $open_editor_modal_callback,
				'embedBlockName'          => $embed_block_name,
				'formBlockName'           => $form_block_name,
				'progressBlockName'       => $progress_block_name,
				'civistBlockName'         => $civist_block_name,
			)
		);

		wp_enqueue_media();
		wp_register_script( $name, $plugin_dir_url . $webpack_files->editor->entry, array(), $webpack_files->editor->hash, true );
		$this->inject_vendor( $plugin_dir_url, $webpack_files );
		wp_localize_script( $name, 'civist', $params );
		wp_enqueue_script( $name );
	}

	/**
	 * Register and enqueue the Freshdesk Widget scripts
	 */
	public function enqueue_freshdesk_widget_scripts() {
		wp_enqueue_script( 'freshdesk_widget', 'https://s3.amazonaws.com/assets.freshdesk.com/widget/freshwidget.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}

	/**
	 * Sets async attr in script.
	 *
	 * @param string $tag The script tag.
	 * @param string $handle The handle or id of the enqueued script.
	 */
	public function add_async( $tag, $handle ) {
		if ( 'freshdesk_widget' !== $handle ) {
			return $tag;
		}
		return str_replace( '></script>', ' async></script>', $tag );
	}

	/**
	 * Builds the params that should be injected into js files under `civist_public` global
	 *
	 * @return array
	 */
	public function get_public_configuration() {
		global $content_width;

		return array(
			'apiUrl'                   => $this->settings_manager->get_option_by_name( 'api_url' ),
			'version'                  => $this->settings_manager->get_option_by_name( 'version' ),
			'contentWidth'             => $content_width,
			'widgetSupportedLanguages' => $this->widget_supported_languages,
		);
	}

	/**
	 * Builds the params that should be injected into js files under `civist` global
	 *
	 * @return array
	 */
	public function get_management_configuration() {
		if ( ! is_admin() ) {
			return $this->get_public_configuration();
		}
		global $wp_version;
		global $content_width;

		$locale         = get_locale();
		$admin_email    = get_option( 'admin_email' );
		$plugin_dir_url = plugin_dir_url( __FILE__ );
		$params         = array(
			'adminAjaxUrl'             => admin_url( 'admin-ajax.php' ),
			'adminEmail'               => $admin_email,
			'adminUrl'                 => admin_url( 'admin.php' ),
			'apiUrl'                   => $this->settings_manager->get_option_by_name( 'api_url' ),
			'apiKeyId'                 => $this->settings_manager->get_option_by_name( 'api_key_id' ),
			'contentWidth'             => $content_width,
			'enforceHttps'             => $this->enforce_https ? 'enforce' : 'allow',
			'locale'                   => $locale,
			'pluginUrl'                => $plugin_dir_url,
			'pluginDashboardUrl'       => admin_url( 'admin.php' ) . '?page=' . $this->plugin_slug,
			'pluginSettingsUrl'        => admin_url( 'options-general.php' ) . '?page=' . $this->plugin_slug . '-settings',
			'pluginSlug'               => $this->plugin_slug,
			'siteUrl'                  => get_site_url(),
			'version'                  => $this->settings_manager->get_option_by_name( 'version' ),
			'widgetUrl'                => $this->settings_manager->get_option_by_name( 'widget_url' ),
			'geoipUrl'                 => $this->settings_manager->get_option_by_name( 'geoip_url' ),
			'widgetSupportedLanguages' => $this->widget_supported_languages,
			'wpVersion'                => $wp_version,
		);
		if ( null !== $this->jwt ) {
			$params = array_merge(
				$params,
				array(
					'jwtAjaxAction' => $this->jwt->get_client_action_name(),
				)
			);
		}
		return $params;
	}

	/**
	 * Injects vendor scripts.
	 *
	 * @param string $plugin_dir_url The plugin dir url.
	 * @param string $webpack_files The files to inject.
	 */
	private function inject_vendor( $plugin_dir_url, $webpack_files ) {
		wp_enqueue_script( 'civist_manifest', $plugin_dir_url . $webpack_files->manifest->entry, array(), $webpack_files->manifest->hash, true );
		wp_enqueue_script( 'civist_vendor', $plugin_dir_url . $webpack_files->vendor->entry, array(), $webpack_files->vendor->hash, true );
		wp_enqueue_script( 'civist_common', $plugin_dir_url . $webpack_files->common->entry, array(), $webpack_files->common->hash, true );
	}
}
