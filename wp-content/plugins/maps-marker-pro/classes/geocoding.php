<?php
namespace MMP;

use MMP\Maps_Marker_Pro as MMP;

class Geocoding {
	/**
	 * Registers the hooks
	 *
	 * @since 4.24
	 */
	public function init() {
		add_action('wp_ajax_mmp_geocoding', array($this, 'geocoding'));
		add_action('wp_ajax_nopriv_mmp_geocoding', array($this, 'geocoding'));
		add_action('wp_ajax_mmp_google_places', array($this, 'googlePlaces'));
		add_action('wp_ajax_nopriv_mmp_google_places', array($this, 'googlePlaces'));
	}

	/**
	 * AJAX request for geocoding
	 *
	 * @since 4.24
	 */
	public function geocoding() {
		$provider = (isset($_POST['provider'])) ? $_POST['provider'] : null;
		$query = (isset($_POST['query'])) ? $_POST['query'] : null;

		$results = $this->getResults($provider, $query);

		wp_send_json($results);
	}

	/**
	 * AJAX request for Google Places
	 *
	 * @since 4.24
	 */
	public function googlePlaces() {
		$place_id = (isset($_POST['placeId'])) ? $_POST['placeId'] : null;

		$result = $this->placesToLatLng($place_id);

		wp_send_json($result);
	}

	/**
	 * Geocodes an address
	 *
	 * @since 4.0
	 * @since 4.20 $use_cache parameter added
	 *
	 * @param string $address Address to geocode
	 * @param string $provider Provider to geocode with
	 * @param bool $use_cache (optional) Whether to use the geocoding cache
	 */
	public function getLatLng($address, $provider, $use_cache = true) {
		$db = MMP::get_instance('MMP\DB');

		if ($use_cache) {
			$cache = $db->get_cached_address($address);
			if ($cache !== null) {
				return array(
					'success' => true,
					'cached'  => true,
					'lat'     => $cache->lat,
					'lon'     => $cache->lng,
					'address' => $cache->address
				);
			}
		}

		$response = $this->getResults($provider, $address, 1);

		if (!$response['success']) {
			return $response;
		}

		if (!count($response['results'])) {
			return array(
				'success' => false,
				'message' => esc_html__('No results', 'mmp')
			);
		}

		if (isset($response['results'][0]['placeId'])) {
			$result = $this->placesToLatLng($response['results'][0]['placeId']);

			if ($result['success'] === false) {
				return $result;
			}

			$response['results'][0]['lat'] = $result['lat'];
			$response['results'][0]['lon'] = $result['lon'];
		}

		$db->cache_address(
			$address,
			$response['results'][0]['address'],
			$response['results'][0]['lat'],
			$response['results'][0]['lon']
		);

		return array(
			'success' => true,
			'lat'     => $response['results'][0]['lat'],
			'lon'     => $response['results'][0]['lon'],
			'address' => $response['results'][0]['address']
		);
	}

	/**
	 * Gets results from a geocoding provider
	 *
	 * @since 4.24
	 *
	 * @param string $provider Provider to query
	 * @param string $query Search query
	 * @param int $limit (optional) Maximum number of results to retrieve
	 */
	private function getResults($provider, $query, $limit = 10) {
		$query = remove_accents($query);

		switch ($provider) {
			case 'locationiq':
				$results = $this->locationiq($query, $limit);
				break;
			case 'mapquest':
				$results = $this->mapquest($query, $limit);
				break;
			case 'google':
				$results = $this->google($query, $limit);
				break;
			case 'tomtom':
				$results = $this->tomtom($query, $limit);
				break;
			default:
				$results = array(
					'success' => false,
					'message' => esc_html__('Invalid provider', 'mmp')
				);
				break;
		}

		return $results;
	}

	/**
	 * Geocodes an address using LocationIQ
	 *
	 * @since 4.0
	 * @since 4.24 $limit parameter added
	 *
	 * @param string $address Address to geocode
	 * @param int $limit (optional) Maximum number of results to retrieve
	 */
	private function locationiq($address, $limit = 10) {
		if (!MMP::$settings['geocodingLocationIqApiKey']) {
			return array(
				'success' => false,
				'message' => esc_html__('API key missing', 'mmp')
			);
		}

		$params = array(
			'key'             => MMP::$settings['geocodingLocationIqApiKey'],
			'q'               => $address,
			'limit'           => $limit,
			'normalizecity'   => 1,
			'accept-language' => (MMP::$settings['geocodingLocationIqLanguage']) ? MMP::$settings['geocodingLocationIqLanguage'] : substr(get_locale(), 0, 2),
			'countrycodes'    => MMP::$settings['geocodingLocationIqCountries']
		);
		if (MMP::$settings['geocodingLocationIqBounds']) {
			$params['viewbox'] = implode(',', array(
				MMP::$settings['geocodingLocationIqBoundsLon1'],
				MMP::$settings['geocodingLocationIqBoundsLat1'],
				MMP::$settings['geocodingLocationIqBoundsLon2'],
				MMP::$settings['geocodingLocationIqBoundsLat2']
			));
		}
		$url = 'https://api.locationiq.com/v1/autocomplete.php?' . http_build_query($params, '', '&');

		$response = wp_remote_get($url, array(
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			return array(
				'success' => false,
				'message' => $response->get_error_message()
			);
		}

		if ($response['response']['code'] !== 200) {
			return array(
				'success' => false,
				'message' => $response['response']['code'] . ' ' . $response['response']['message']
			);
		}

		$body = json_decode($response['body'], true);
		if (!is_array($body)) {
			return array(
				'success' => false,
				'message' => esc_html__('Invalid response', 'mmp')
			);
		}

		$results = array();
		foreach ($body as $suggestion) {
			$country = (isset($suggestion['address']['country'])) ? $suggestion['address']['country'] : null;
			$city = (isset($suggestion['address']['city'])) ? $suggestion['address']['city'] : null;
			$house_number = (isset($suggestion['address']['house_number'])) ? $suggestion['address']['house_number'] : null;
			$street = (isset($suggestion['address']['street'])) ? $suggestion['address']['street'] : null;
			$postcode = (isset($suggestion['address']['postcode'])) ? $suggestion['address']['postcode'] : null;
			$state = (isset($suggestion['address']['state'])) ? $suggestion['address']['state'] : null;
			$name = (isset($suggestion['address']['name'])) ? $suggestion['address']['name'] . ',' : null;
			$out = $name . ' ' . (($street) ? $street . (($house_number) ? ' ' . $house_number : '') . ', ' : '') . (($state) ? $state . ', ' : '') . (($country) ? '' . $country : '');
			$results[] = array(
				'address' => $out,
				'lat'     => floatval($suggestion['lat']),
				'lon'     => floatval($suggestion['lon'])
			);
		}

		return array(
			'success' => true,
			'results' => $results
		);
	}

	/**
	 * Geocodes an address using MapQuest
	 *
	 * @since 4.0
	 * @since 4.24 $limit parameter added
	 *
	 * @param string $address Address to geocode
	 * @param int $limit (optional) Maximum number of results to retrieve
	 */
	private function mapquest($address, $limit = 10) {
		if (!MMP::$settings['geocodingMapQuestApiKey']) {
			return array(
				'success' => false,
				'message' => esc_html__('API key missing', 'mmp')
			);
		}

		$params = array(
			'key'        => MMP::$settings['geocodingMapQuestApiKey'],
			'location'   => $address,
			'maxResults' => $limit
		);
		if (MMP::$settings['geocodingMapQuestBounds']) {
			$params['boundingBox'] = implode(',', array(
				MMP::$settings['geocodingMapQuestBoundsLat1'],
				MMP::$settings['geocodingMapQuestBoundsLon1'],
				MMP::$settings['geocodingMapQuestBoundsLat2'],
				MMP::$settings['geocodingMapQuestBoundsLon2']
			));
		}
		$url = 'https://www.mapquestapi.com/geocoding/v1/address?' . http_build_query($params, '', '&');

		$response = wp_remote_get($url, array(
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			return array(
				'success' => false,
				'message' => $response->get_error_message()
			);
		}

		if ($response['response']['code'] !== 200) {
			return array(
				'success' => false,
				'message' => $response['response']['code'] . ' ' . $response['response']['message']
			);
		}

		$body = json_decode($response['body'], true);
		if (!isset($body['results'][0]['locations']) || !is_array($body['results'][0]['locations'])) {
			return array(
				'success' => false,
				'message' => esc_html__('Invalid response', 'mmp')
			);
		}

		$results = array();
		foreach ($body['results'][0]['locations'] as $suggestion) {
			$out = '';
			$out .= (isset($suggestion['street']) && $suggestion['street']) ? $suggestion['street'] . ', ' : '';
			$out .= (isset($suggestion['adminArea5']) && $suggestion['adminArea5']) ? $suggestion['adminArea5'] . ', ' : '';
			$out .= (isset($suggestion['adminArea4']) && $suggestion['adminArea4']) ? $suggestion['adminArea4'] . ', ' : '';
			$out .= (isset($suggestion['adminArea3']) && $suggestion['adminArea3']) ? $suggestion['adminArea3'] . ', ' : '';
			$out .= (isset($suggestion['adminArea2']) && $suggestion['adminArea2']) ? $suggestion['adminArea2'] . ', ' : '';
			$out .= (isset($suggestion['adminArea1']) && $suggestion['adminArea1']) ? $suggestion['adminArea1'] : '';
			$results[] = array(
				'address' => $out,
				'lat'     => floatval($suggestion['displayLatLng']['lat']),
				'lon'     => floatval($suggestion['displayLatLng']['lng'])
			);
		}

		return array(
			'success' => true,
			'results' => $results
		);
	}

	/**
	 * Geocodes an address using Google
	 *
	 * @since 4.0
	 * @since 4.24 $limit parameter added
	 *
	 * @param string $address Address to geocode
	 * @param int $limit (optional) Maximum number of results to retrieve
	 */
	private function google($address, $limit = 10) {
		if (MMP::$settings['geocodingGoogleAuthMethod'] === 'clientid-signature' && (!MMP::$settings['geocodingGoogleClient'] || !MMP::$settings['geocodingGoogleSignature'] || !MMP::$settings['geocodingGoogleChannel'])) {
			return array(
				'success' => false,
				'message' => esc_html__('Credentials missing or incomplete', 'mmp')
			);
		} else if (!MMP::$settings['geocodingGoogleApiKey']) {
			return array(
				'success' => false,
				'message' => esc_html__('API key missing', 'mmp')
			);
		}

		$params = array(
			'input' => $address
		);
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
		$url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . http_build_query($params, '', '&');

		$response = wp_remote_get($url, array(
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			return array(
				'success' => false,
				'message' => $response->get_error_message()
			);
		}

		if ($response['response']['code'] !== 200) {
			return array(
				'success' => false,
				'message' => $response['response']['code'] . ' ' . $response['response']['message']
			);
		}

		$body = json_decode($response['body'], true);
		if (!isset($body['status']) || !isset($body['predictions']) || !is_array($body['predictions'])) {
			return array(
				'success' => false,
				'message' => esc_html__('Invalid response', 'mmp')
			);
		}

		if ($body['status'] !== 'OK' && $body['status'] !== 'ZERO_RESULTS') {
			return array(
				'success' => false,
				'message' => $body['error_message']
			);
		}

		$results = array();
		foreach ($body['predictions'] as $suggestion) {
			$results[] = array(
				'address' => $suggestion['description'],
				'placeId' => $suggestion['place_id'],
			);
		}

		return array(
			'success' => true,
			'results' => $results
		);
	}

	/**
	 * Geocodes an address using TomTom
	 *
	 * @since 4.6
	 * @since 4.24 $limit parameter added
	 *
	 * @param string $address Address to geocode
	 * @param int $limit (optional) Maximum number of results to retrieve
	 */
	private function tomtom($address, $limit) {
		if (!MMP::$settings['geocodingTomTomApiKey']) {
			return array(
				'success' => false,
				'message' => esc_html__('API key missing', 'mmp')
			);
		}

		$params = array(
			'key'      => MMP::$settings['geocodingTomTomApiKey'],
			'limit'    => $limit
		);
		if (MMP::$settings['geocodingTomTomLat']) {
			$params['lat'] = MMP::$settings['geocodingTomTomLat'];
		}
		if (MMP::$settings['geocodingTomTomLon']) {
			$params['lon'] = MMP::$settings['geocodingTomTomLon'];
		}
		if (MMP::$settings['geocodingTomTomRadius']) {
			$params['radius'] = MMP::$settings['geocodingTomTomRadius'];
		}
		$language = (MMP::$settings['geocodingTomTomLanguage']) ? MMP::$settings['geocodingTomTomLanguage'] : str_replace('_', '-', get_locale());
		if (in_array($language, array('NGT', 'NGT-Latn', 'af-ZA', 'ar', 'eu-ES', 'bg-BG', 'ca-ES', 'zh-CN', 'zh-TW', 'cs-CZ', 'da-DK', 'nl-BE', 'nl-NL', 'en-AU', 'en-NZ', 'en-GB', 'en-US', 'et-EE', 'fi-FI', 'fr-CA', 'fr-FR', 'gl-ES', 'de-DE', 'el-GR', 'hr-HR', 'he-IL', 'hu-HU', 'id-ID', 'it-IT', 'kk-KZ', 'lv-LV', 'lt-LT', 'ms-MY', 'No-NO', 'nb-NO', 'pl-PL', 'pt-BR', 'pt-PT', 'ro-RO', 'ru-RU', 'ru-Latn-RU', 'ru-Cyrl-RU', 'sr-RS', 'sk-SK', 'sl-SI', 'es-ES', 'es-419', 'sv-SE', 'th-TH', 'tr-TR', 'uk-UA', 'vi-VN'))) {
			$params['language'] = $language;
		}
		if (MMP::$settings['geocodingTomTomCountrySet']) {
			$params['countrySet'] = MMP::$settings['geocodingTomTomCountrySet'];
		}
		$url = "https://api.tomtom.com/search/2/geocode/{$address}.JSON?" . http_build_query($params, '', '&');

		$response = wp_remote_get($url, array(
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			return array(
				'success' => false,
				'message' => $response->get_error_message()
			);
		}

		if ($response['response']['code'] !== 200) {
			return array(
				'success' => false,
				'message' => $response['response']['code'] . ' ' . $response['response']['message']
			);
		}

		$body = json_decode($response['body'], true);
		if (!isset($body['results']) || !is_array($body['results'])) {
			return array(
				'success' => false,
				'message' => esc_html__('Invalid response', 'mmp')
			);
		}

		$results = array();
		foreach ($body['results'] as $suggestion) {
			$out = $suggestion['address']['freeformAddress'];
			$out .= (isset($suggestion['address']['country']) && $address !== $suggestion['address']['country']) ? ', ' . $suggestion['address']['country'] : '';
			$results[] = array(
				'address' => $out,
				'lat'     => floatval($suggestion['position']['lat']),
				'lon'     => floatval($suggestion['position']['lon'])
			);
		}

		return array(
			'success' => true,
			'results' => $results
		);
	}

	/**
	 * Gets the coordinates for a Google Places ID
	 *
	 * @since 4.24
	 *
	 * @param string $place_id Place ID
	 */
	private function placesToLatLng($place_id) {
		if (MMP::$settings['geocodingGoogleAuthMethod'] === 'clientid-signature' && (!MMP::$settings['geocodingGoogleClient'] || !MMP::$settings['geocodingGoogleSignature'] || !MMP::$settings['geocodingGoogleChannel'])) {
			return array(
				'success' => false,
				'message' => esc_html__('Credentials missing or incomplete', 'mmp')
			);
		} else if (!MMP::$settings['geocodingGoogleApiKey']) {
			return array(
				'success' => false,
				'message' => esc_html__('API key missing', 'mmp')
			);
		}

		$params = array(
			'placeid' => $place_id
		);
		if (MMP::$settings['geocodingGoogleAuthMethod'] === 'clientid-signature') {
			$params['client'] = MMP::$settings['geocodingGoogleClient'];
			$params['signature'] = MMP::$settings['geocodingGoogleSignature'];
			$params['channel'] = MMP::$settings['geocodingGoogleChannel'];
		} else {
			$params['key'] = MMP::$settings['geocodingGoogleApiKey'];
		}
		$url = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query($params, '', '&');

		$response = wp_remote_get($url, array(
			'timeout'   => 10
		));

		if (is_wp_error($response)) {
			return array(
				'success' => false,
				'message' => $response->get_error_message()
			);
		}

		if ($response['response']['code'] !== 200) {
			return array(
				'success' => false,
				'message' => $response['response']['code'] . ' ' . $response['response']['message']
			);
		}

		$body = json_decode($response['body'], true);
		if (!isset($body['status']) || $body['status'] !== 'OK' || !isset($body['result']) || !is_array($body['result'])) {
			return array(
				'success' => false,
				'message' => esc_html__('Invalid response', 'mmp')
			);
		}

		return array(
			'success' => true,
			'address' => $body['result']['formatted_address'],
			'lat'     => floatval($body['result']['geometry']['location']['lat']),
			'lon'     => floatval($body['result']['geometry']['location']['lng'])
		);
	}
}
