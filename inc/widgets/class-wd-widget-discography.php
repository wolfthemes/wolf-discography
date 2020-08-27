<?php
/**
 * Discography Widget
 *
 * Displays Discography widget
 *
 * @author WolfThemes
 * @category Widgets
 * @package WolfDiscography/Widgets
 * @version 1.5.1
 * @extends WP_Widget
 */

defined( 'ABSPATH' ) || exit;

class WD_Widget_Discography extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Widget settings
		$ops = array( 'classname' => 'widget_discography', 'description' => esc_html__( 'Display your discography', 'wolf-discography' ) );

		// Create the widget
		parent::__construct( 'widget_discography', esc_html__( 'Discography', 'wolf-discography' ), $ops );
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
	function widget( $args, $instance ) {

		extract( $args );
		$title = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title = apply_filters( 'widget_title', $title );

		$desc = ( isset( $instance['desc'] ) ) ? sanitize_text_field( $instance['desc'] ) : '';
		$count = isset( $instance['count'] ) ? absint( $instance['count'] ) : 3;

		echo $before_widget;

		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		if ( ! empty( $desc ) ) {
			echo '<p>';
			echo $desc;
			echo '</p>';
		}
		wolf_widget_discography( $count );
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
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['desc'] = sanitize_text_field( $new_instance['desc'] );
		$instance['count'] = absint( $new_instance['count'] );
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
			'title' => esc_html__( 'Releases', 'wolf-discography' ),
			'desc' => '',
			'count' => 3,
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ));  ?>"><?php esc_html_e(  'Title' , 'wolf-discography' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ));  ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php esc_html_e( 'Optional Text', 'wolf-discography' ); ?>:</label>
			<textarea class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc' ) ); ?>" ><?php echo $instance['desc']; ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Count', 'wolf-discography' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" value="<?php echo absint( $instance['count'] ); ?>">
		</p>
		<?php
	}

}