<?php

class Pretix_Attendee_List_Tools {
	public static function first_or_none( iterable $array ): mixed {
		foreach ( $array as $item ) {
			return $item;
		}

		return null;
	}

	public function get_closest_subevent( array $subevents ): ?array {
		if ( ! isset( $subevents['results'] ) || ! is_array( $subevents['results'] ) ) {
			throw new InvalidArgumentException();
		}

		$closestSubevent = null;
		$closestDateDiff = PHP_INT_MAX;
		$currentDate     = time();

		foreach ( $subevents['results'] as $subevent ) {
			if ( ! isset( $subevent['date_from'] ) ) {
				continue;
			}
			$subeventDate = strtotime( $subevent['date_from'] );
			$dateDiff     = abs( $subeventDate - $currentDate );

			if ( $subeventDate >= $currentDate && $dateDiff < $closestDateDiff ) {
				$closestDateDiff = $dateDiff;
				$closestSubevent = $subevent;
			}
		}

		return $closestSubevent;
	}

	public function get_all_attendee_names( array $orders, string $display_name_question_id ): array {
		if ( ! isset( $orders['results'] ) ) {
			throw new InvalidArgumentException();
		}

		$names = [];
		foreach ( $orders['results'] as $result ) {
			foreach ( $result['positions'] as $position ) {
				// in case there are multiple answers, take the first
				$names[] = self::first_or_none( self::filter_answers( $position, $display_name_question_id ) );
			}
		}

		return $names;
	}

	public function get_approved_attendee_names( array $orders, string $permission_question_id, string $display_name_question_id ): array {
		if ( ! isset( $orders['results'] ) ) {
			throw new InvalidArgumentException();
		}

		$approved_people = [];
		foreach ( $orders['results'] as $result ) {
			if ( ! is_array( $result['positions'] ) || ! isset( $result['positions'] ) ) {
				continue;
			}

			foreach ( $result['positions'] as $position ) {
				// in case there are multiple answers, take the first
				$permission_answer = self::first_or_none( self::filter_answers( $position, $permission_question_id ) );
				if ( $permission_answer === 'True' ) {
					$approved_people[] = self::first_or_none( self::filter_answers( $position, $display_name_question_id ) );
				}
			}
		}

		return $approved_people;
	}

	private static function filter_answers( array $position, string $question_identifier ): array {
		if ( ! isset( $position['answers'] ) ) {
			throw new InvalidArgumentException();
		}

		// filter for all answers to a specific question using the question identifier
		$answers = array_filter( $position['answers'], fn( $a ) => $a['question_identifier'] === $question_identifier );

		// use array_column to get the actual answer values ($answers is an object/array)
		return array_column( $answers, 'answer' );
	}
}

class AttendeeListException extends Exception {

}