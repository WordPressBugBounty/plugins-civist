<?php
/**
 * Register the plugin oEmbed provider.
 *
 * @package civist
 */

/**
 * Register the plugin oEmbed provider.
 */
class Civist_OEmbed {
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
	 * The plugin settings manager.
	 *
	 * @var Civist_Settings_Manager
	 */
	private $settings_manager;

	/**
	 * The Civist_OEmbed class constructor.
	 *
	 * @param string                  $plugin_file The file of the plugin.
	 * @param string                  $plugin_slug The slug of the plugin.
	 * @param Civist_Settings_Manager $settings_manager The plugin settings manager.
	 */
	public function __construct( $plugin_file, $plugin_slug, Civist_Settings_Manager $settings_manager ) {
		$this->plugin_slug      = $plugin_slug;
		$this->settings_manager = $settings_manager;
	}

	/**
	 * Handle oEmbed provider registration.
	 */
	public function oembed_provider() {
		$widget_url = $this->settings_manager->get_option_by_name( 'widget_url' );
		$oembed_url = $this->settings_manager->get_option_by_name( 'oembed_url' );
		$pattern    = $widget_url . '*';
		wp_oembed_add_provider( $pattern, $oembed_url, false );
	}

	/**
	 * Allow localhost as origin for the oEmbed provider.
	 *
	 * @param bool   $is_external Is request host external.
	 * @param string $host The host.
	 * @param string $url The request url.
	 */
	public function allow_localhost( $is_external, $host, $url ) {
		if ( ! $is_external ) {
			$oembed_url  = $this->settings_manager->get_option_by_name( 'oembed_url' );
			$is_external = ( substr( $url, 0, strlen( $oembed_url ) ) === $oembed_url );
		}
		return $is_external;
	}

	/**
	 * Wrap the widget with a div containing a custom class so that it can be used in the script
	 * that communicates with the embedded resource to select the iframe element inside a post.
	 *
	 * @param string $data The oembed response.
	 * @param string $url The oembed request url.
	 * @param string $args The oembed request args.
	 * @param string $post_id The post id.
	 */
	public function wrap_widget( $data, $url, $args, $post_id ) {
		$widget_url = $this->settings_manager->get_option_by_name( 'widget_url' );
		if ( strpos( $url, $widget_url ) !== false ) {
			$permalink           = get_permalink( $post_id );
			$html_safe_permalink = esc_html( $permalink );
			$wrapper_class       = $this->wrap_widget_html_class( $post_id );
			$mode                = esc_js( wp_parse_args( $args, array( 'mode' => 'default' ) )['mode'] );

			$content = "<div class=\"{$wrapper_class}\">$data</div>";

			$content .= "<script type='text/javascript'>";
			$content .= "  var origin = '" . esc_js( $widget_url ) . "';";
			$content .= '  origin = origin.slice(0, -1);';
			$content .= '  var handleEmbedPostMessage = function(embedContainer) {';
			$content .= "    window.addEventListener('message', function(msg) {";
			$content .= "      if(msg.origin !== origin || msg.data !== 'ready') {";
			$content .= '        return;';
			$content .= '      }';
			$content .= "      embedContainer.querySelector('iframe').contentWindow.postMessage({";
			$content .= "        permalink:'" . esc_js( $permalink ) . "',";
			$content .= '        referrer: window.location.href';
			$content .= "      },'" . esc_js( $widget_url ) . "')";
			$content .= '    });';
			$content .= '  };';

			$content .= '  var handleEmbed = function(embedContainer) {';
			$content .= "    embedContainer.setAttribute('data-permalink', '{$html_safe_permalink}');";
			$content .= "    embedContainer.setAttribute('data-mode', '{$mode}');";
			$content .= '  };';

			$content .= "  var embedsThemeId = document.querySelectorAll('#post-" . esc_js( $post_id ) . " [id^=civist]');";
			$content .= "  var embedsInternalClass = document.querySelectorAll('." . esc_js( $wrapper_class ) . " [id^=civist]');";

			$content .= '  [].forEach.call(embedsThemeId, handleEmbedPostMessage);';
			$content .= '  [].forEach.call(embedsInternalClass, handleEmbedPostMessage);';
			$content .= '  [].forEach.call(embedsThemeId, handleEmbed);';
			$content .= '  [].forEach.call(embedsInternalClass, handleEmbed);';
			$content .= '</script>';
			return $content;
		}
		return $data;
	}

	/**
	 * Clears the plugin's oembed cache.
	 */
	public function clear_cache() {
		global $wpdb;
			// @codingStandardsIgnoreLine
			$wpdb->query( "DELETE $wpdb->postmeta
				FROM
					$wpdb->postmeta,
					(
						SELECT `meta_key` FROM $wpdb->postmeta
						WHERE
							`meta_key` LIKE '\_oembed\_%'
							AND `meta_key` NOT LIKE '\_oembed\_time\_%'
							AND `meta_value` LIKE '%id=\"civist-%'
					) pm
				WHERE $wpdb->postmeta.`meta_key` IN (`pm`.`meta_key`, CONCAT('_oembed_time_', SUBSTRING(`pm`.`meta_key` FROM 9)))
			"
			);
	}

	/**
	 * Build the class used in the widget wrapper.
	 *
	 * @param int $id The post id.
	 */
	private function wrap_widget_html_class( $id ) {
		return "{$this->plugin_slug}-oembed-wrapper-{$id}";
	}
}
