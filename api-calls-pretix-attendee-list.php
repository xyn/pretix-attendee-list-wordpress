<?php

    class Pretix_Attendee_List_Api_Calls {
        function api_call($path, $method = 'get', $payload = null, $api_url, $api_token) {
            if (strpos($path, $api_url) === 0) {
                $url = $path;
            } else {
                $url = $api_url . $path;
            };
            $headers = array(
                'Authorization' => 'Token ' . $api_token,
            );
            $args = array(
                'headers' => $headers,
            );
            if ($method === 'post' && $payload !== null) {
                $args['body'] = $payload;
            }
            $response = wp_remote_request($url, $args);
            if (!is_wp_error($response) && $response['response']['code'] == 200) {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                if ($data !== null) {
                    return $data;
                } else {
                    return new WP_Error('json_decode_error', 'Error decoding JSON data');
                }
            } else {
                return $response;
            }
        }

        public function get_subevents($api_url, $api_token, $organizer, $event, $subevent) {
            $path = '';
            if(empty($subevent)) {
                $path = "organizers/{$organizer}/events/{$event}/subevents";
            } else {
                $path = "organizers/{$organizer}/events/{$event}/subevents/{$subevent}";
            }
            $subevents = $this->api_call($path, 'get', null, $api_url, $api_token);
            return $subevents;
        }

        public function get_orders($api_url, $api_token, $organizer, $event, $subevent) {
            $path = '';
            if(empty($subevent)) {
                $path = "organizers/{$organizer}/events/{$event}/orders";
            } else {
                $path = "organizers/{$organizer}/events/{$event}/orders?subevent={$subevent}";
            }
            $orders = $this->api_call($path, 'get', null, $api_url, $api_token);
            return $orders;
        }
    }

?>