<?php
/**
 * The editor integration of the plugin
 *
 * @package civist
 */

/**
 * Configures Civist integration with classic and block editor
 */
class Civist_Editor {
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
	 * The plugin scripts.
	 *
	 * @var Civist_Script
	 */
	private $scripts;

	const EMBED_BLOCK_NAME    = 'civist/embed';
	const FORM_BLOCK_NAME     = 'civist/form';
	const PROGRESS_BLOCK_NAME = 'civist/progress';
	const CIVIST_BLOCK_NAME   = 'civist/civist';

	/**
	 * The Civist_Admin class constructor
	 *
	 * @param string         $plugin_name The name of the plugin.
	 * @param string         $plugin_file The file of the plugin.
	 * @param string         $plugin_slug The slug of the plugin.
	 * @param Civist_Scripts $scripts The plugin scripts.
	 */
	public function __construct( $plugin_name, $plugin_file, $plugin_slug, Civist_Scripts $scripts ) {
		$this->plugin_name                      = $plugin_name;
		$this->plugin_file                      = $plugin_file;
		$this->plugin_slug                      = $plugin_slug;
		$this->scripts                          = $scripts;
		$this->editor_button_id                 = $plugin_slug . '_editor_button';
		$this->editor_button_click_handler_name = $plugin_slug . '_handle_media_button_click';
	}

	/**
	 * Renders the plugin media button for embedding content into posts/pages.
	 */
	public function add_petition_media_button() {
		/* translators: The button to open the modal to add a petition to a post/page. */
		$text = _x( 'Civist', 'wp.plugin.editor.button', 'civist' );
		?>
		<button type="button" class="button" style="padding-left: 5px;"
			id="<?php echo( esc_html( $this->editor_button_id ) ); ?>"
			onclick="<?php echo( esc_js( $this->editor_button_click_handler_name ) . '()' ); ?>"
		>
			<span class="wp-media-buttons-icon dashicons-civist_symbol_wp" style="font-size: 18px; vertical-align: sub;"></span><?php echo( esc_html( $text ) ); ?>
		</button>
		<?php
	}

	/**
	 * Register and enqueue the plugin scripts for the classic editor
	 */
	public function enqueue_editor_scripts() {
		include 'civist-scripts.php'; // exposes $webpack-files variable.
		$this->scripts->enqueue_editor_scripts( $webpack_files, $this->editor_button_click_handler_name, $this::EMBED_BLOCK_NAME, $this::FORM_BLOCK_NAME, $this::PROGRESS_BLOCK_NAME, $this::CIVIST_BLOCK_NAME );
	}

	/**
	 * Register the plugin blocks for the block editor
	 */
	public function register_plugin_blocks() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$this->register_plugin_embed_block();
		$this->register_plugin_form_block();
		$this->register_plugin_progress_block();
		$this->register_plugin_civist_block();
	}

	/**
	 * Register the (deprecated) plugin embed block for the block editor
	 */
	private function register_plugin_embed_block() {
		/* translators: The title of the deprecated Civist block. */
		$title = _x( 'Civist (deprecated)', 'wp.plugin.block.civist_embed.title', 'civist' );
		/* translators: The description of the deprecated Civist block. */
		$description = _x( 'This block is deprecated. Please change the block type to the new Civist block.', 'wp.plugin.block.civist_embed.description', 'civist' );
		register_block_type(
			$this::EMBED_BLOCK_NAME,
			array(
				'title'       => $title,
				'description' => $description,
				'category'    => 'embed',
				'keywords'    => array( 'petition', 'donation form', 'embed' ),
			)
		);
	}
	/**
	 * Register the plugin form block for the block editor
	 */
	private function register_plugin_form_block() {
		/* translators: The title of the Civist block that contains a petition or donation form using the block editor. */
		$title = _x( 'Civist form', 'wp.plugin.block.civist_form.title', 'civist' );
		/* translators: The description of the Civist block that contins a petition or donation form using the block editor. */
		$description = _x( 'The form', 'wp.plugin.block.civist_form.description', 'civist' );

		register_block_type(
			$this::FORM_BLOCK_NAME,
			array(
				'title'       => $title,
				'description' => $description,
				'category'    => 'embed',
				'keywords'    => array( 'petition', 'donation form', 'embed' ),
			)
		);
	}


	/**
	 * Register the plugin progress block for the block editor
	 */
	private function register_plugin_progress_block() {
		include 'civist-blocks-scripts.php'; // exposes $webpack-files variable.

		/* translators: The title of the Civist block that contains a petition's progress using the block editor. */
		$title = _x( 'Civist progress', 'wp.plugin.block.civist_progress.title', 'civist' );
		/* translators: The description of the Civist block for that contins a petition's progress using the block editor. */
		$description = _x( 'The progress', 'wp.plugin.block.civist_progress.description', 'civist' );

		register_block_type(
			$this::PROGRESS_BLOCK_NAME,
			array(
				'title'       => $title,
				'description' => $description,
				'category'    => 'embed',
				'keywords'    => array( 'petition', 'progress', 'embed' ),
			)
		);
	}

	/**
	 * Register the plugin main block for the block editor
	 */
	private function register_plugin_civist_block() {
		include 'civist-blocks-scripts.php'; // exposes $webpack-files variable.
		$script        = 'civist_blocks_embed';
		$editor_script = 'civist_blocks';
		$style         = 'civist_blocks_style';
		wp_register_script( $editor_script, plugin_dir_url( __FILE__ ) . $webpack_files->editor->entry, array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-url' ), $webpack_files->editor->hash, true );
		wp_localize_script(
			$editor_script,
			'civist',
			array_merge(
				$this->scripts->get_management_configuration(),
				array(
					'openEditorModalCallback' => $this->editor_button_click_handler_name,
					'embedBlockName'          => $this::EMBED_BLOCK_NAME,
					'formBlockName'           => $this::FORM_BLOCK_NAME,
					'progressBlockName'       => $this::PROGRESS_BLOCK_NAME,
					'civistBlockName'         => $this::CIVIST_BLOCK_NAME,
				)
			)
		);
		wp_register_script( $script, plugin_dir_url( __FILE__ ) . $webpack_files->embed->entry, array( 'wp-dom-ready' ), $webpack_files->embed->hash, true );
		wp_localize_script( $script, 'civist_public', $this->scripts->get_public_configuration() );
		wp_register_style( $style, plugin_dir_url( __FILE__ ) . $webpack_files->embed->css[0], array(), $webpack_files->embed->hash );

		/* translators: The title of the Civist block for embedding a petition or donation form using the block editor. */
		$title = _x( 'Civist', 'wp.plugin.block.civist_civist.title', 'civist' );
		/* translators: The description of the Civist block for embedding a petition or donation form using the block editor. */
		$description = _x( 'Embed a Petition or a Donation Form', 'wp.plugin.block.civist_civist.description', 'civist' );

		register_block_type(
			$this::CIVIST_BLOCK_NAME,
			array(
				'style'         => $style,
				'editor_script' => $editor_script,
				'script'        => $script,
				'title'         => $title,
				'description'   => $description,
				'category'      => 'embed',
				'keywords'      => array( 'petition', 'donation form', 'embed' ),
			)
		);
	}
}
