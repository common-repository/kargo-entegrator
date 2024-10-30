<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * GurmeCargo wp_options tablosu için abstract sınıfını barındırır.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo GCARGO_Resource abstract sınıfı
 */
abstract class GCARGO_Resource extends GCARGO_Connection {

	/**
	 * Eklenti Prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * Kaynak tanımı
	 *
	 * @var string
	 */
	protected $resource;

	/**
	 * Kaynak api pathi.
	 *
	 * @var string
	 */
	protected $api_path;

	/**
	 * Kaynak kimliği.
	 *
	 * @var string
	 */
	protected $resource_id;

	/**
	 * Çağrı adresi.
	 *
	 * @return string
	 */
	private function request_path() {
		$path = "/{$this->api_path}";
		if ( $this->resource_id ) {
			$path = "{$path}/{$this->resource_id}";
		}
		return $path;
	}

	/**
	 * Api isteği yapan method.
	 *
	 * @param string $method İstek tipi 'POST', 'GET', 'HEAD' ...
	 * @param mixed  $body İstekte gönderilecek parametreler.
	 * @throws Exception Bağlantı hataları.
	 */
	public function request( $method, $body = array() ) {

		if ( false === empty( $body ) ) {
			$body = wp_json_encode( $body );
		}

		$response = $this->http_client->request( $this->request_path(), $method, $body );

		if ( 500 === $response['response']['code'] && 'GET' !== $method ) {
			throw new Exception( esc_html( $response['response']['message'] ) );
		}

		return $response;
	}

	/**
	 * Apiden kaynak getirme.
	 */
	public function index() {
		$resources     = [];
		$http_response = $this->request( 'GET' );
		if ( 200 === $http_response['response']['code'] ) {
			$resources = $this->http_client->get_body( $http_response );
			do_action( "{$this->prefix}_{$this->resource}_index_request_completed", $resources );
		}
		return $resources;
	}

	/**
	 * Apiye kaynak ekleme.
	 *
	 * @param mixed $request Kaynak verileri.
	 *
	 * @return mixed
	 * @throws GCARGO_Ajax_Validation_Exception Validasyon hataları.
	 */
	public function store( $request ) {
		$http_response = $this->request( 'POST', $request );

		if ( 201 === $http_response['response']['code'] ) {
			$response = $this->http_client->get_body( $http_response );
			do_action( "{$this->prefix}_{$this->resource}_store_request_completed", $response, $request );
			return $response;
		}

		if ( 422 === $http_response['response']['code'] ) {
			$errors               = $this->http_client->get_body( $http_response );
			$validation_exception = new GCARGO_Ajax_Validation_Exception( esc_html( 'Validation exception' ) );
			$validation_exception->set_validation_errors( $errors );
			throw $validation_exception;
		}
	}

	/**
	 * Apiden kaynak getirme.
	 *
	 * @param int|string $id Güncellenecek veri kimliği.
	 *
	 * @return mixed
	 */
	public function show( $id ) {
		$this->resource_id = $id;
		$resource          = [];
		$http_response     = $this->request( 'GET' );
		if ( 200 === $http_response['response']['code'] ) {
			$resource = $this->http_client->get_body( $http_response );
			do_action( "{$this->prefix}_{$this->resource}_show_request_completed", $resource );
		}
		return $resource;
	}

	/**
	 * Api kaynağını güncelleme ekleme.
	 *
	 * @param mixed      $request Kaynak verileri.
	 * @param int|string $id Güncellenecek veri kimliği.
	 */
	public function update( $request, $id ) {
		$this->resource_id = $id;
		$resource          = [];
		$response          = $this->request( 'PATCH', $request );
		if ( 200 === $response['response']['code'] ) {
			$resource = $this->http_client->get_body( $response );
			do_action( "{$this->prefix}_{$this->resource}_update_request_completed", $resource );
		}
		return $resource;
	}

	/**
	 * Api kaynağını siler.
	 *
	 * @param int|string $id Silinecek veri kimliği.
	 *
	 * @throws Exception Silinme hatası.
	 */
	public function destroy( $id ) {
		$this->resource_id = $id;
		$resource          = false;
		$response          = $this->request( 'DELETE' );

		if ( 200 === $response['response']['code'] ) {
			$resource = $this->http_client->get_body( $response );
			do_action( "{$this->prefix}_{$this->resource}_destroy_request_completed", $resource );
			return $resource;
		}

		if ( 422 === $response['response']['code'] ) {
			$errors = $this->http_client->get_body( $response );
			throw new Exception( esc_html( $errors['message'] ) );
		}
	}
}
