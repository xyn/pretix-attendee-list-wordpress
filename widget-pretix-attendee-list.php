<?php


class Pretix_Attendee_List_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'pretix_attendee_list_widget',
			'Pretix Attendee List',
			array( 'description' => 'Display a list of attendees.' )
		);
	}

	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		$sona_name_question_identifier  = isset( $instance['sona_name_question_identifier'] ) ? esc_attr( $instance['sona_name_question_identifier'] ) : '';
		$permission_question_identifier = isset( $instance['permission_question_identifier'] ) ? esc_attr( $instance['permission_question_identifier'] ) : '';
		$event                          = isset( $instance['event'] ) ? esc_attr( $instance['event'] ) : '';
		$subevent                       = isset( $instance['subevent'] ) ? esc_attr( $instance['subevent'] ) : '';
		$is_singular_event              = isset( $instance['is_singular_event'] ) ? esc_attr( $instance['is_singular_event'] ) : false;

		$pretix_api_url   = get_option( "pretix_api_url" );
		$pretix_api_token = get_option( "pretix_api_token" );
		$pretix_organizer = get_option( "pretix_organizer" );

		$pretix_api = new Pretix_Api( $pretix_api_url, $pretix_api_token );
		$tools      = new Pretix_Attendee_List_Tools();

		try {
			if ( ! $is_singular_event ) {
				if ( empty( $subevent ) ) {
					$subevents        = $pretix_api->get_subevents( $pretix_organizer, $event );
					$closest_subevent = $tools->get_closest_subevent( $subevents );
					$orders           = $pretix_api->get_orders( $pretix_organizer, $event, $closest_subevent['id'] );
				} else {
					$orders = $pretix_api->get_orders( $pretix_organizer, $event, $subevent );
				}
			} else {
				$orders = $pretix_api->get_orders( $pretix_organizer, $event );
			}

			$people          = $tools->get_all_attendee_names( $orders, $sona_name_question_identifier );
			$approved_people = $tools->get_approved_attendee_names( $orders, $permission_question_identifier, $sona_name_question_identifier );

			echo sprintf( "<p>Nose counter: %s</p>", count( $people ) );
			echo "<ul>\n";
			foreach ( $approved_people as $person ) {
				echo sprintf( "\t<li>%s</li>\n", htmlspecialchars( $person ) );
			}
			echo "</ul>\n";

		} catch ( AttendeeListException $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
				echo '<p>An exception occurred when rendering the widget:</p>';
				echo sprintf( "<pre>%s</pre>", htmlspecialchars( $e->getMessage() ) );
			}
		}

		echo $args['after_widget'];

	}

	public function form( $instance ) {
	}

	public function update( $new_instance, $old_instance ) {
		$instance                                   = array();
		$instance['sona_name_question_identifier']  = ! empty( $new_instance['sona_name_question_identifier'] ) ? sanitize_text_field( $new_instance['sona_name_question_identifier'] ) : '';
		$instance['permission_question_identifier'] = ! empty( $new_instance['permission_question_identifier'] ) ? sanitize_text_field( $new_instance['permission_question_identifier'] ) : '';
		$instance['event']                          = ! empty( $new_instance['event'] ) ? sanitize_text_field( $new_instance['event'] ) : '';
		$instance['subevent']                       = ! empty( $new_instance['subevent'] ) ? sanitize_text_field( $new_instance['subevent'] ) : '';
		$instance['is_singular_event']              = ! empty( $new_instance['is_singular_event'] ) ? sanitize_text_field( $new_instance['is_singular_event'] ) : false;

		return $instance;
	}
}

function register_pretix_attendee_list_widget() {
	register_widget( 'Pretix_Attendee_List_Widget' );
}

add_action( 'widgets_init', 'register_pretix_attendee_list_widget' );

// Shortcode to embed the widget with parameters
function pretix_attendee_list_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'sona_name_question_identifier'  => '',
			'permission_question_identifier' => '',
			'event'                          => '',
			'subevent'                       => '',
			'is_singular_event'              => false,
		),
		$atts,
		'pretix_attendee_list'
	);

	ob_start();
	the_widget( 'Pretix_Attendee_List_Widget', $atts );

	return ob_get_clean();
}

add_shortcode( 'pretix_attendee_list', 'pretix_attendee_list_shortcode' );
