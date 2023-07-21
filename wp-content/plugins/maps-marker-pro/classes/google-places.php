<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Google_Places {
	/**
	 * Processes the Google Places request
	 *
	 * @since 4.0
	 */
	public function request() {
		if (!isset($_GET['nonce']) || wp_verify_nonce($_GET['nonce'], 'mmp-google-places') === false) {
			$this->response('Security check failed!');
		}

		if (MMP::$settings['geocodingGoogleAuthMethod'] === 'api-key' && !MMP::$settings['geocodingGoogleApiKey']) {
			$this->response('Google geocoding authentication failed - please provide an API key!');
		}

		if (MMP::$settings['geocodingGoogleAuthMethod'] === 'clientid-signature') {
			if (!MMP::$settings['geocodingGoogleClient']) {
				$this->response('Google geocoding authentication failed - please provide a client ID!');
			}
			if (!MMP::$settings['geocodingGoogleSignature']) {
				$this->response('Google geocoding authentication failed - please provide a signature!');
			}
		}

		if (isset($_GET['address']) && $_GET['address']) {
			$this->autocomplete();
		} else if (isset($_GET['place_id']) && $_GET['place_id']) {
			$this->details();
		} else {
			$this->response('Invalid request!');
		}
	}

	/**
	 * Returns a list of autocomplete results
	 *
	 * @since 4.0
	 */
	private function autocomplete() {
		$url = $this->prepare_api_url('autocomplete');

		$response = wp_remote_get($url, array(
			'sslverify' => false,
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			$this->response($response->get_error_message());
		}

		$body = json_decode($response['body'], true);
		if ($response['response']['code'] !== 200 || !isset($body['status'])) {
			$this->response($body['message']);
		}

		if ($body['status'] === 'OK') {
			$data = array();
			foreach ($body['predictions'] as $prediction) {
				$data[] = array(
					'address'  => $prediction['description'],
					'place_id' => $prediction['place_id'],
					'types'    => $prediction['types']
				);
			}
			$this->response('OK', $data);
		} else if ($body['status'] === 'ZERO_RESULTS') {
			$data[] = array(
				'address'  => '',
				'place_id' => '',
				'types'    => ''
			);
			$this->response('ZERO_RESULTS', $data);
		} else {
			$data = array(
				'status'        => $body['status'],
				'error_message' => $body['error_message']
			);
			$this->response('GOOGLE_ERROR', $data);
		}
	}

	/**
	 * Returns the details for a Google Places ID
	 *
	 * @since 4.0
	 */
	private function details() {
		$url = $this->prepare_api_url('details');

		$response = wp_remote_get($url, array(
			'sslverify' => false,
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			$this->response($response->get_error_message());
		}

		$body = json_decode($response['body'], true);
		if ($response['response']['code'] !== 200 || !isset($body['status'])) {
			$this->response($body['message']);
		}

		if ($body['status'] === 'OK') {
			$data = array(
				'address'  => $body['result']['formatted_address'],
				'types'    => $body['result']['types'],
				'geometry' => array(
					'location' => array(
						'lat' => $body['result']['geometry']['location']['lat'],
						'lng' => $body['result']['geometry']['location']['lng']
					)
				)
			);
			$this->response('OK', $data);
		} else {
			$data = array(
				'status'        => $details['status'],
				'error_message' => $details['error_message']
			);
			$this->response('GOOGLE_ERROR', $data);
		}
	}

	/**
	 * Returns the appropriate URL for the request
	 *
	 * @since 4.0
	 *
	 * @param string $type Request type
	 */
	private function prepare_api_url($type) {
		if ($type === 'autocomplete') {
			$endpoint = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?';
			$params['input'] = $_GET['address'];
		} else if ($type === 'details') {
			$endpoint = 'https://maps.googleapis.com/maps/api/place/details/json?';
			$params['placeid'] = $_GET['place_id'];
		}

		if (MMP::$settings['geocodingGoogleAuthMethod'] === 'clientid-signature') {
			$params['client'] = MMP::$settings['geocodingGoogleClient'];
			$params['signature'] = MMP::$settings['geocodingGoogleSignature'];
			$params['channel'] = MMP::$settings['geocodingGoogleChannel'];
		} else {
			$params['key'] = MMP::$settings['geocodingGoogleApiKey'];
		}

		if (MMP::$settings['geocodingGoogleLocation']) {
			$params['location'] = MMP::$settings['geocodingGoogleLocation'];
		}
		if (MMP::$settings['geocodingGoogleRadius']) {
			$params['radius'] = MMP::$settings['geocodingGoogleRadius'];
		}
		if (MMP::$settings['geocodingGoogleRegion']) {
			$params['region'] = MMP::$settings['geocodingGoogleRegion'];
		}
		if (MMP::$settings['geocodingGoogleComponents']) {
			$params['components'] = MMP::$settings['geocodingGoogleComponents'];
		}

		if (MMP::$settings['geocodingGoogleLanguage']) {
			$params['language'] = MMP::$settings['geocodingGoogleLanguage'];
		} else {
			$params['language'] = substr(get_locale(), 0, 2);
		}

		return $endpoint . http_build_query($params, '', '&');
	}

	/**
	 * Sends the response
	 *
	 * @since 4.0
	 *
	 * @param string $msg Status message
	 * @param array $data (optional) List of results
	 */
	private function response($msg, $data = array()) {
		$response = array(
			'status'  => $msg,
			'results' => $data
		);
		header('Content-type: application/json; charset=utf-8');
		echo json_encode($response);
		die;
	}
}
