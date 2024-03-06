<?php

class Pretix_Api {
	public function __construct( private readonly string $apiUrl, private readonly string $apiToken ) {
	}

	/**
	 * @throws AttendeeListException
	 */
	private function api_call( string $path ): array {
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

		if ( is_wp_error( $response ) || $response['response']['code'] !== 200 ) {
			throw new AttendeeListException( 'The remote request to the Pretix API failed', 'wp_remote_request_failed' );
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );
		if ( $data === null ) {
			throw new AttendeeListException( 'Error decoding JSON data', 'json_decode_error' );
		}

		return $data;
	}

	/**
	 * @throws AttendeeListException
	 */
	public function get_subevents( string $organizer, string $event, ?string $subevent = null ): array {
		if ( empty( $subevent ) ) {
			$path = "organizers/$organizer/events/$event/subevents";
		} else {
			$path = "organizers/$organizer/events/$event/subevents/$subevent";
		}

		return $this->api_call( $path );
	}

	/**
	 * @throws AttendeeListException
	 */
	public function get_orders( string $organizer, string $event, ?string $subevent = null ): array {
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
