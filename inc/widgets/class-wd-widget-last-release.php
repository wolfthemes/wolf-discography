<?php
/**
 * Last Release Widget
 *
 * Displays last release widget
 *
 * @author WolfThemes
 * @category Widgets
 * @package WolfDiscography/Widgets
 * @version 1.5.1
 * @extends WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WD_Widget_Last_Release extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {

		// Widget settings
		$ops = array( 'classname' => 'widget_last_release', 'description' => esc_html__( 'Display your last release', 'wolf-discography' ) );

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
	function widget( $args, $instance ) {

		extract( $args );
		
		$title = ( isset( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : '';
		$title = apply_filters( 'widget_title', $title );

		$desc = ( isset( $instance['desc'] ) ) ? sanitize_text_field( $instance['desc'] ) : '';
		
		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		if ( ! empty( $desc ) ) {
			echo '<p>';
			echo $desc;
			echo '</p>';
		}
		wolf_widget_last_release();
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
		$instance['title'] = $new_instance['title'];
		$instance['desc'] = $new_instance['desc'];
		$instance['count'] = absint( $new_instance['count'] );
		//$instance['hide_release_title'] = $new_instance['hide_release_title'];
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
			'desc' => '',
			//'hide_release_title' => '',
		);
		$instance = wp_parse_args( ( array ) $instance, $defaults);
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ));  ?>"><?php _e(  'Title' , 'wolf-discography' ); ?>:</label>
			<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ));  ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>"><?php _e( 'Optional Text', 'wolf-discography' ); ?>:</label>
			<textarea class="widefat"  id="<?php echo esc_attr( $this->get_field_id( 'desc' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'desc' ) ); ?>" ><?php echo $instance['desc']; ?></textarea>
		</p>
		<?php
	}

}