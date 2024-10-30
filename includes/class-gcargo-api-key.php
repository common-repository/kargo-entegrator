<?php
/**
 * GurmeCargo ile saas arasındaki iletişimin güvenlik anahtarı.
 *
 * @package GurmeHub
 */

/**
 * Api Key sınıfı
 */
class GCARGO_Api_Key {

	/**
	 * wp_options option anahtarı.
	 *
	 * @var string $options_table_key
	 */
	private $options_table_key = 'gcargo_api_key';

	/**
	 * Anahtarı getirir.
	 */
	public function get() {
		return get_option( $this->options_table_key, '' );
	}

	/**
	 * Anahtarı getirir.
	 *
	 * @param string $api_key Anahtar.
	 *
	 * @throws Exception Anahtar doğru değilse hata verir.
	 */
	public function set( string $api_key ) {
		$api_key  = trim( $api_key );
		$response = gcargo_api_requests()->check_connection( $api_key );
		if ( 200 !== $response['response']['code'] ) {
			throw new Exception( esc_html__( 'Api key is incorrect, please check and try again.', 'kargo-entegrator' ) );
		}
		return update_option( $this->options_table_key, $api_key );
	}
}
