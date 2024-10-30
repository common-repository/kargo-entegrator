<?php
/**
 * GurmeCargo ile ödeme geçitlerine atılacak istekleri organize eder.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo istek sınıfı
 */
class GCARGO_Api_Requests extends GCARGO_Connection {

	/**
	 * Api Bağlantı testi.
	 *
	 * @param string $api_key Erişim anahtarı.
	 */
	public function check_connection( $api_key ) {
		return $this->http_client->set_headers(
			array(
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . $api_key,
			)
		)->request( '/helpers/check-connection', 'GET' );
	}


	/**
	 * Kargo firmaları.
	 *
	 * @return array
	 */
	public function get_cargo_companies() {
		$companies     = [];
		$http_response = $this->http_client->request( '/helpers/cargo-companies', 'GET' );

		if ( 200 === $http_response['response']['code'] ) {
			$companies = $this->http_client->get_body( $http_response );
		}
		return $companies;
	}

	/**
	 * Barkod yazdırma.
	 *
	 * @param array $shipments Gönderi kimlikleri.
	 *
	 * @return mixed
	 *
	 * @throws Exception Barko yazdırılamadı.
	 */
	public function print( $shipments ) {
		$params = '';
		foreach ( $shipments as $index => $id ) {
			$params .= "shipments[{$index}]={$id}";
			if ( array_key_last( $shipments ) !== $index ) {
				$params .= '&';
			}
		}

		$http_response = $this->http_client->request( "/print-pdf?{$params}", 'GET' );
		if ( 200 === $http_response['response']['code'] ) {
			return base64_encode( $http_response['body'] ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		}

		throw new Exception( 'Barkod yazdırılamadı. Lütfen tekrar deneyin' );
	}

	/**
	 * Gönderi ayarları kayıt etme.
	 *
	 * @param array $data Ayarlar.
	 *
	 * @return mixed
	 */
	public function update_shipment_setting( $data ) {
		$http_response = $this->http_client->request( '/settings/shipment-setting', 'PATCH', wp_json_encode( $data ) );
		if ( 200 === $http_response['response']['code'] ) {
			return $this->http_client->get_body( $http_response );
		}
	}
	/**
	 * Gönderi ayarları getirme.
	 *
	 * @return mixed
	 */
	public function get_shipment_setting() {
		$settings      = [];
		$http_response = $this->http_client->request( '/settings/shipment-setting', 'GET' );
		if ( 200 === $http_response['response']['code'] ) {
			$settings = $this->http_client->get_body( $http_response );
		}

		return $settings;
	}
}
