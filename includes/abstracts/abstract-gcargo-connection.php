<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * GurmeCargo wp_options tablosu için abstract sınıfını barındırır.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo GCARGO_Connection abstract sınıfı
 */
abstract class GCARGO_Connection {

	/**
	 * Çağrı sınıfı.
	 *
	 * @var GCARGO_Http_Client
	 */
	protected $http_client;

	/**
	 * Kurucu method.
	 */
	public function __construct() {
		/**
		 * More details for api check readme or links
		 *
		 * https://kargoentegrator.com/kisisel-veri-kullanimi-aydinlatma-metni/
		 * https://kargoentegrator.com/gizlilik-politikasi/
		 */
		$this->http_client = gcargo_http_client()
		->set_url( defined( 'GCARGO_DEVELOPMENT' ) && GCARGO_DEVELOPMENT ? 'http://172.18.0.4:8080/api' : 'http://app.kargoentegrator.com/api' )
		->set_headers(
			array(
				'Content-Type'  => 'application/json',
				'Accept'        => 'application/json',
				'Authorization' => 'Bearer ' . gcargo_api_key()->get(),
			)
		);
	}
}
