<?php
/**
 * Last Realease Widget
 *
 * @package WolfDiscography
 * @subpackage Widgets
 * @since 2.0.0
 */

namespace WolfDiscography\Widgets;

defined( 'ABSPATH' ) || exit;

class LastReleaseWidget extends \WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Widget settings
		$ops = array(
			'classname'   => 'widget_last_release',
			'description' => esc_html__( 'Display your last release', 'wolf-discography' ),
		);

		// Create the widget
		parent::__construct( 'widget_last_release', esc_html__( 'Last Release', 'wolf-discography' ), $ops );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {

		extract( $args );

		$title = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title = apply_filters( 'widget_title', $title );

		$desc = ( isset( $instance['desc'] ) ) ? sanitize_text_field( $instance['desc'] ) : '';

		echo $before_widget;
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		if ( ! empty( $desc ) ) {
			echo '<p>';
			echo $desc;
			echo '</p>';
		}
		$this->widget_last_release();
		echo $after_widget;
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['desc']  = $new_instance['desc'];
		$instance['count'] = absint( $new_instance['count'] );
		// $instance['hide_release_title'] = $new_instance['hide_release_title'];
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		// Set up some default widget settings
		$defaults = array(
			'title' => esc_html__( 'Last Release', 'wolf-discography' ),
			'desc'  => '',
			// 'hide_release_title' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title', 'wolf-discography' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php _e( 'Optional Text', 'wolf-discography' ); ?>:</label>
			<textarea class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc' ) ); ?>" ><?php echo $instance['desc']; ?></textarea>
		</p>
		<?php
	}



	/**
	 * Last Release Widget function
	 *
	 * Displays the last release widget
	 *
	 * @return string
	 */
	public function widget_last_release() {
		global $wpdb;
		$query = new \WP_Query(
			array(
				'post_type'      => 'release',
				'posts_per_page' => 1,
			),
		);

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$post_id        = get_the_ID();
				$thumbnail_size = get_post_meta( $post_id, '_wolf_release_type', true ) == 'DVD' || get_post_meta( $post_id, '_wolf_release_type', true ) == 'K7' ? 'DVD' : 'CD';
				?>
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $thumbnail_size ); ?></a>
				<h4 class="entry-title"><a title="<?php esc_html_e( 'View Details', 'wolf-discography' ); ?>" class="entry-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
				<?php
			}
		} else {
			echo '<p>';
			esc_html_e( 'No release to display yet.', 'wolf-discography' );
			echo '</p>';
		}
		wp_reset_postdata();
	}
}