<?php

class Pretix_Attendee_List_Tools {
	public function first_or_none( $array ) {
		foreach ( $array as $item ) {
			return $item;
		}

		return null;
	}

	public function get_closest_subevent( $subevents ) {
		if ( isset( $subevents['results'] ) && is_array( $subevents['results'] ) ) {
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

		return null;
	}

	public function get_all_attendee_names( $orders, $sona_name_question ): array {
		$approvedPeople = [];
		foreach ( $orders['results'] as $result ) {
			foreach ( $result['positions'] as $position ) {
				$sonaNameAnswers  = array_filter( $position['answers'], function ( $a ) use ( $sona_name_question ) {
					return $a['question_identifier'] == $sona_name_question;
				} );
				$approvedPeople[] = $this->first_or_none( array_column( $sonaNameAnswers, 'answer' ) );

			}
		}

		return $approvedPeople;
	}

	public function get_approved_attendee_names( $orders, $permission_question, $sona_name_question ): array {
		$approved_people = [];
		foreach ( $orders['results'] as $result ) {
			foreach ( $result['positions'] as $position ) {
				$permissionAnswers     = array_filter( $position['answers'], function ( $a ) use ( $permission_question ) {
					return $a['question_identifier'] == $permission_question;
				} );
				$firstPermissionAnswer = $this->first_or_none( array_column( $permissionAnswers, 'answer' ) );
				if ( $firstPermissionAnswer == 'True' ) {
					$sonaNameAnswers   = array_filter( $position['answers'], function ( $a ) use ( $sona_name_question ) {
						return $a['question_identifier'] == $sona_name_question;
					} );
					$approved_people[] = $this->first_or_none( array_column( $sonaNameAnswers, 'answer' ) );
				}
			}
		}

		return $approved_people;
	}
}
