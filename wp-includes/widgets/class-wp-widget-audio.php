<?php
/**
 * Widget API: WP_Widget_Audio class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.8.0
 */

/**
 * Core class that implements an audio widget.
 *
 * @since 4.8.0
 *
 * @todo Refactor this for latest WP_Widget_Media and remove codeCoverageIgnore
 * @codeCoverageIgnore
 * @see WP_Widget
 */
class WP_Widget_Audio extends WP_Widget_Media {

	/**
	 * Constructor.
	 *
	 * @since  4.8.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct( 'media_audio', __( 'Audio' ), array(
			'description' => __( 'Displays an audio file.' ),
			'mime_type'   => 'audio',
		) );

		if ( $this->is_preview() ) {
			$this->enqueue_mediaelement_script();
		}
	}

	/**
	 * Render the media on the frontend.
	 *
	 * @since  4.8.0
	 * @access public
	 *
	 * @param array $instance Widget instance props.
	 * @return void
	 */
	public function render_media( $instance ) {

		// @todo Support external audio defined by 'url' only.
		if ( empty( $instance['attachment_id'] ) ) {
			return;
		}

		$attachment = get_post( $instance['attachment_id'] );
		if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
			return;
		}

		if ( in_array( $instance['link'], array( 'file', 'post' ), true ) ) {
			echo $this->create_link_for( $attachment, $instance['link'] );
		} else {
			echo wp_audio_shortcode( array(
				'src' => wp_get_attachment_url( $attachment->ID ),
			) );
		}
	}

	/**
	 * Render form template scripts.
	 *
	 * @since 4.8.0
	 * @access public
	 */
	public function render_control_template_scripts() {
		parent::render_control_template_scripts();

		?>
		<script type="text/html" id="tmpl-wp-media-widget-audio-preview">
			<?php wp_underscore_audio_template(); ?>
		</script>
		<?php
	}

	/**
	 * Enqueue media element script and style if in need.
	 *
	 * This ensures the first instance of the audio widget can properly handle audio elements.
	 *
	 * @since 4.8.0
	 * @access private
	 */
	private function enqueue_mediaelement_script() {
		/** This filter is documented in wp-includes/media.php */
		if ( 'mediaelement' === apply_filters( 'wp_audio_shortcode_library', 'mediaelement' ) ) {
			wp_enqueue_style( 'wp-mediaelement' );
			wp_enqueue_script( 'wp-mediaelement' );
		}
	}
}
