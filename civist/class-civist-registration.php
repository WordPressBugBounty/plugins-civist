<?php
/**
 * The registration-specific UI elements of the plugin
 *
 * @package civist
 */

/**
 * Handles the render of registration related UI elements of the plugin
 */
class Civist_Registration {
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	private $plugin_slug;

	/**
	 * The Civist_Registration class constructor
	 *
	 * @param string $plugin_slug The slug of the plugin.
	 */
	public function __construct( $plugin_slug ) {
		$this->plugin_slug = $plugin_slug;
	}

	/**
	 * Add action to render the plugin's connect notice.
	 */
	public function show_connect_notice() {
		add_action( 'admin_notices', array( $this, 'civist_connect_notice' ) );
		add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_civist_connect_widget' ) );
	}

	/**
	 * Render the plugin's connect notice.
	 */
	public function civist_connect_notice() {
		$current = get_current_screen();
		if ( 'plugins' !== $current->parent_base ) {
			return;
		}
		?>
		<div class="notice notice-info is-dismissible">
			<div style="padding: 16px 0;">
				<h2 style="font-size: 18px; font-weight: 400; margin-top: 0;">
					<?php
					/* translators: the title of the connect to civist notice shown in the plugins page */
					echo( esc_html_x( 'Civist is almost ready!', 'wp.plugin.notice.connect.title', 'civist' ) );
					?>
				</h2>
				<p style="font-size: 14px; margin: 0;">
					<?php
					/* translators: the text of the connect to civist notice shown in the plugins page */
					echo ( esc_html_x( 'Please connect to or create a Civist account to enable the Civist plugin.', 'wp.plugin.notice.connect.text', 'civist' ) );
					?>
				</p>
				<p style="padding: 12px 0 0;">
					<a
						href="<?php echo( esc_url( admin_url( 'admin.php' ) . '?page=' . $this->plugin_slug ) ); ?>"
						class="button button-primary"
					>
						<?php
						/* translators: the text of the button that connects to Civist */
						echo( esc_html_x( 'Connect Civist', 'wp.plugin.notice.connect.link.connect', 'civist' ) );
						?>
					<a>
					<a
						target="_blank"
						href="<?php echo( esc_url( 'https://civist.com' ) ); ?>"
						class="button"
					>
						<?php
						/* translators: the text of the button to learn more about Civist */
						echo( esc_html_x( 'Learn more', 'wp.plugin.notice.connect.link.learn_more', 'civist' ) );
						?>
					<a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Setup the plugin's integration in the administration dashboard.
	 */
	public function wp_dashboard_civist_connect_widget() {
		wp_add_dashboard_widget(
			'civist_widget',
			_x( 'Please connect Civist', 'wp.plugin.dashboard.widget.box.title', 'civist' ),
			array( $this, 'dashboard_widget' )
		);

		global $wp_meta_boxes;
		$dashboard                                    = $wp_meta_boxes['dashboard']['normal']['core'];
		$wp_meta_boxes['dashboard']['normal']['core'] = array_merge( // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			array(
				'civist_widget' => $dashboard['civist_widget'],
			),
			$dashboard
		);
	}

	/**
	 * Render the plugin's widget in the administration dashboard.
	 */
	public function dashboard_widget() {
		?>
		<div style="text-align: center; padding: .75em; box-sizing: border-box;">
			<a style="text-decoration: none;">
				<div>
					<svg width="18%" height="18%" viewBox="0 0 800 800" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;">
						<clipPath id="_clip1">
							<rect id="Artboard1" x="0" y="0" width="800" height="800"/>
						</clipPath>
						<g clip-path="url(#_clip1)"><path d="M174.969,397.768c1.202,-122.49 101.017,-221.563 223.789,-221.563c60.889,0 116.131,24.369 156.497,63.877l44.592,-44.591c-51.785,-50.924 -122.795,-82.357 -201.089,-82.357c-158.329,0 -286.872,128.543 -286.872,286.872c0,158.329 128.543,286.872 286.872,286.872c90.261,0 170.841,-41.776 223.443,-107.03l40.361,40.36c-63.066,75.454 -157.87,123.485 -263.804,123.485c-189.686,0 -343.687,-154.001 -343.687,-343.687c0,-189.686 154.001,-343.687 343.687,-343.687c114.391,0 215.804,56.006 278.291,142.07l-40.776,40.776l-0.022,-0.034l-45.641,45.641l0.021,0.035l-41.393,41.393c-27.312,-55.531 -84.464,-93.784 -150.48,-93.784c-91.748,0 -166.375,73.885 -167.575,165.352l-0.016,0l0,2.238l-56.198,0l0,-2.238Z" style="fill:#000;"/></g>
					</svg>
				</div>
				<h3 style="font-size: 1.25em; font-weight: 400;">
					<?php
					/* translators: the title of the connect to civist widget in the WordPress administration dashboard */
					echo( esc_html_x( 'Please Connect Civist', 'wp.plugin.dashboard.widget.title', 'civist' ) );
					?>
				</h3>
				<p style="color: #555d66; margin-top: 0; padding: 0 15px; width: 100%; box-sizing: border-box;">
					<?php
					/* translators: the text of the connect to civist widget in the WordPress administration dashboard */
					echo ( esc_html_x( 'Connecting to Civist will allow you to start campaigning using your own WordPress', 'wp.plugin.dashboard.widget.text', 'civist' ) );
					?>
				</p>
			</a>
			<div style="text-align: center; padding: 15px 0 10px 0;">
				<a
					href="<?php echo( esc_attr( admin_url( 'admin.php' ) . '?page=' . $this->plugin_slug ) ); ?>"
					class="button button-primary"
				>
					<?php
					echo( esc_html_x( 'Connect Civist', 'wp.plugin.notice.connect.link.connect', 'civist' ) );
					?>
				<a>
			</div>
		</div>
		<?php
	}
}

