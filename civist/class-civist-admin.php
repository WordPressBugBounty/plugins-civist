<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package civist
 */

/**
 * Configures Civist menus, pages and scripts in WordPress administration.
 */
class Civist_Admin {
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
	 * The id of the reconnect link in the plugins page.
	 *
	 * @var string
	 */
	private $reconnect_link_id;
	/**
	 * The plugin settings manager.
	 *
	 * @var Civist_Settings_Manager
	 */
	private $settings_manager;
	/**
	 * The plugin settings UI.
	 *
	 * @var Civist_Settings
	 */
	private $settings;
	/**
	 * The plugin scripts.
	 *
	 * @var Civist_Scripts
	 */
	private $scripts;
	/**
	 * The plugin editor functionality.
	 *
	 * @var Civist_Editor
	 */
	private $editor;

	/**
	 * The Civist_Admin class constructor
	 *
	 * @param string                  $plugin_name The name of the plugin.
	 * @param string                  $plugin_file The file of the plugin.
	 * @param string                  $plugin_slug The slug of the plugin.
	 * @param Civist_Settings_Manager $settings_manager The plugin settings manager.
	 * @param Civist_Settings         $settings The plugin settings UI.
	 * @param Civist_Scripts          $scripts The plugin scripts.
	 * @param Civist_Editor           $editor The plugin editor functionality.
	 */
	public function __construct( $plugin_name, $plugin_file, $plugin_slug, Civist_Settings_Manager $settings_manager, Civist_Settings $settings, Civist_Scripts $scripts, Civist_Editor $editor ) {
		$this->plugin_name         = $plugin_name;
		$this->plugin_file         = $plugin_file;
		$this->plugin_slug         = $plugin_slug;
		$this->reconnect_link_id   = $plugin_slug . '_reconnect_kink';
		$this->settings_manager    = $settings_manager;
		$this->settings            = $settings;
		$this->scripts             = $scripts;
		$this->editor              = $editor;
		$this->is_plugin_connected = $this->settings_manager->is_connected();
	}

	/**
	 * Build the plugin's menu entries.
	 */
	public function add_admin_menu() {
		if ( current_user_can( 'edit_posts' ) || current_user_can( 'manage_options' ) ) {
			global $submenu;
			$plugin_page_url = admin_url( 'admin.php' ) . '?page=' . $this->plugin_slug;
			add_menu_page( $this->plugin_name, $this->plugin_name, 'edit_posts', $this->plugin_slug, array( $this, 'render_manager' ), 'dashicons-civist_symbol_wp', 22 );
			if ( ! $this->is_plugin_connected ) {
				$submenu['options-general.php'][] = array( $this->plugin_name, 'manage_options', $plugin_page_url ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			} else {
				/* translators: The menu link to show the plugin dashboard page. */
				$submenu[ $this->plugin_slug ][] = array( _x( 'Dashboard', 'wp.plugin.manager.menu.dashboard', 'civist' ), 'edit_posts', $plugin_page_url . '#/' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				/* translators: The menu link to show the plugin all petitions page. */
				$submenu[ $this->plugin_slug ][] = array( _x( 'Petitions', 'wp.plugin.manager.menu.all_petitions', 'civist' ), 'edit_posts', $plugin_page_url . '#/petitions' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				/* translators: The menu link to show the plugin all donation forms page. */
				$submenu[ $this->plugin_slug ][] = array( _x( 'Donation Forms', 'wp.plugin.manager.menu.all_donation_forms', 'civist' ), 'edit_posts', $plugin_page_url . '#/donation_forms' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				/* translators: The menu link to show the plugin all supporters page. */
				$submenu[ $this->plugin_slug ][] = array( _x( 'Supporters', 'wp.plugin.manager.menu.all_supporters', 'civist' ), 'edit_posts', $plugin_page_url . '#/supporters' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				/* translators: The menu link to show the settings page. */
				$submenu[ $this->plugin_slug ][] = array( _x( 'Settings', 'wp.plugin.manager.menu.settings', 'civist' ), 'manage_options', admin_url( 'options-general.php' ) . '?page=' . $this->plugin_slug . '-settings' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				add_options_page( $this->plugin_slug, $this->plugin_name, 'manage_options', $this->plugin_slug . '-settings', array( $this->settings, 'options_page' ) );
			}
			// TODO: Remove me when advanced settings are no longer necessary.
			add_submenu_page( null, $this->plugin_name, $this->plugin_name, 'manage_options', $this->plugin_slug . '-advanced-settings', array( $this->settings, 'advanced_options_page' ) );
		}
	}

	/**
	 * Render the plugin manager page.
	 */
	public function render_manager() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( "You don't have sufficient permissions to access this page." );
		}
		require_once 'civist-container.php';
	}

	/**
	 * Sets async attr in script.
	 *
	 * @param string $url The url of the script.
	 */
	public function add_async( $url ) {
		if ( strpos( $url, '#asyncload' ) === false ) {
			return $url;
		}
		return str_replace( '#asyncload', '', $url ) . ' async';
	}

	/**
	 * Register and enqueue plugin's scripts and stylesheets.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_scripts( $hook_suffix ) {
		include 'civist-scripts.php'; // exposes $webpack-files variable.

		$this->scripts->enqueue_civist_icon_font( $webpack_files );
		// advanced settings page.
		if ( strpos( $hook_suffix, $this->plugin_slug . '-advanced-settings' ) !== false ) {
			return;
		}

		if ( strpos( $hook_suffix, 'post' ) !== false || strpos( $hook_suffix, $this->plugin_slug ) !== false || strpos( $hook_suffix, 'plugins' ) !== false ) {
			// any plugin page.
			$this->scripts->enqueue_freshdesk_widget_scripts();
		}

		if ( ! $this->is_plugin_connected ) {
			if ( strpos( $hook_suffix, $this->plugin_slug ) !== false ) {
				// registration page.
				$this->scripts->enqueue_registration_scripts( $webpack_files );
			}
		} elseif ( strpos( $hook_suffix, 'plugins' ) !== false ) {
			// plugins page.
			$this->scripts->enqueue_plugins_scripts( $webpack_files, $this->reconnect_link_id );

		} elseif ( strpos( $hook_suffix, $this->plugin_slug ) !== false && strpos( $hook_suffix, 'settings' ) === false ) {
			// manager page.
			$this->scripts->enqueue_manager_scripts( $webpack_files );

		} elseif ( strpos( $hook_suffix, $this->plugin_slug . '-settings' ) !== false ) {
			// settings page.
			$this->scripts->enqueue_settings_scripts( $webpack_files );
		} elseif ( strpos( $hook_suffix, 'post' ) !== false ) {
			// post/page editor page.
			$this->editor->enqueue_editor_scripts();
		}
	}


	/**
	 * Renders the plugin reconnect link in the plugin entry .
	 *
	 * @param string $links The links array.
	 * @return array The links modified by the function.
	 */
	public function add_reconnect_link( $links ) {
		if ( ! $this->is_plugin_connected ) {
			return $links;
		}
		$new_links = array(
			/* translators: The link to reconect Civist in the WordPress admin plugins page under the Civist plugin entry. */
			'<a href="#/" id="' . esc_html( $this->reconnect_link_id ) . '">' . _x( 'Reconnect', 'wp.plugin.plugins.link.reconnect', 'civist' ) . '</a>',
		);

		return array_merge( $links, $new_links );
	}
}
