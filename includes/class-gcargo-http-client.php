<?php
/**
 * GurmeCargo ile ödeme geçitlerine atılacak istekleri organize eder.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo istek sınıfı
 */
class GCARGO_Http_Client {

	/**
	 * Http istek başlığı
	 *
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Http istek adresi
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Http log file.
	 *
	 * @var string
	 */
	protected $log_file = 'gcargo-http-logs';

	/**
	 * Log sınıfı.
	 *
	 * @var GCARGO_Logger
	 */
	public $logger;

	/**
	 * Çağrı adresi atama.
	 *
	 * @param string $url çağrı adresi.
	 */
	public function set_url( string $url ) {
		$this->url = $url;
		return $this;
	}

	/**
	 * Http isteği başlığını ayarlar.
	 *
	 * @param array $headers Http isteği başlığı.
	 * @return $this
	 */
	public function set_headers( array $headers ) {
		$this->headers = $headers;
		return $this;
	}

	/**
	 * Http isteği başlığını döndürür.
	 *
	 * @return array
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 * Kurucu method
	 */
	public function __construct() {
		$this->logger = gcargo_logger( $this->log_file );
	}

	/**
	 * İstek methodu
	 *
	 * @param string $path İstek yapılacak adres.
	 * @param string $method İstek tipi 'POST', 'GET', 'HEAD' ...
	 * @param mixed  $body İstekte gönderilecek parametreler.
	 *
	 * @throws Exception İstekte hata durumunda fırlatılır.
	 * @return array|string $response
	 *
	 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
	 */
	public function request( $path = '', $method = 'POST', $body = false ) {

		$args = array(
			'method'      => $method,
			'timeout'     => 60,
			'httpversion' => '1.0',
			'headers'     => $this->get_headers(),
		);

		if ( $body ) {
			$args['body'] = $body;
		}

		$http_response = wp_remote_request( $this->url . $path, $args );

		if ( is_wp_error( $http_response ) ) {
			$this->logger->add_log( $http_response->get_error_message() );
			return [
				'response' => [
					'code'    => 500,
					'message' => $http_response->get_error_message(),
				],
			];
		}

		$this->logger->add_log( "{$this->url}{$path} - {$http_response['response']['code']}/{$http_response['response']['message']}" );

		return $http_response;
	}

	/**
	 * İstek içerisindeki veriyi alma
	 *
	 * @param array $http_response Http istek sonuçları.
	 *
	 * @return mixed
	 */
	public function get_body( array $http_response ) {
		$encoded_data = json_decode( $http_response['body'], true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return $http_response['body'];
		}

		if ( is_array( $encoded_data ) && isset( $encoded_data['data'] ) ) {
			return $encoded_data['data'];
		}

		return $encoded_data;
	}
}
