<?php

class Pretix_Api {
	public function __construct( private readonly string $apiUrl, private readonly string $apiToken ) {
	}

	private function api_call( $path ) {
		if ( str_starts_with( $path, $this->apiUrl ) ) {
			$url = $path;
		} else {
			$url = $this->apiUrl . $path;
		}

		$headers  = [
			'Authorization' => 'Token ' . $this->apiToken,
		];
		$args     = [
			'headers' => $headers,
		];
		$response = wp_remote_request( $url, $args );

		if ( ! is_wp_error( $response ) && $response['response']['code'] === 200 ) {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );
			if ( $data !== null ) {
				return $data;
			} else {
				return new WP_Error( 'json_decode_error', 'Error decoding JSON data' );
			}
		} else {
			return $response;
		}
	}

	public function get_subevents( $organizer, $event, $subevent = null ) {
		if ( empty( $subevent ) ) {
			$path = "organizers/$organizer/events/$event/subevents";
		} else {
			$path = "organizers/$organizer/events/$event/subevents/$subevent";
		}

		return $this->api_call( $path );
	}

	public function get_orders( $organizer, $event, $subevent = null ) {
		$params = [
			'status' => 'p', // only get paid orders
		];

		if ( ! empty( $subevent ) ) {
			$params['subevent'] = $subevent; // query for subevent, when provided
		}

		$path = "organizers/$organizer/events/$event/orders?" . http_build_query( $params );

		return $this->api_call( $path );
	}
}
