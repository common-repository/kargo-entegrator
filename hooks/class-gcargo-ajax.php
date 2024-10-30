<?php
/**
 * Frontend aksiyonları için backend uç noktalarını oluşturan ve
 * ilgili fonksiyonlara yönlendiren GCARGO_Ajax sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * GCARGO_Ajax
 */
class GCARGO_Ajax {

	/**
	 * Ajax çağrılarında kullanılacak ön ek
	 * Örn : gcargo_get_settings
	 *
	 * @var string $prefix
	 */
	private $prefix = GCARGO_PREFIX;

	/**
	 * GCARGO_Ajax bu dizi içerisindeki uç noktalara cevap verir
	 * ve fonksiyonlara yönlendirir.
	 *
	 * 'get_settings' => array( $this, 'get_settings'),
	 * 'is_active'    => 'is_active',
	 * ...
	 *
	 * @var array $endpoints
	 */
	private $endpoints;

	/**
	 * GCARGO_Ajax kurucu method.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'ajax_actions' ) );
	}

	/**
	 * GCARGO_Ajax ajax_actions
	 *
	 * @return void
	 */
	public function ajax_actions() {
		$this->endpoints = apply_filters(
			/**
			 * Ajax uç noktalarına ekle/çıkar yapmak için kullanılır.
			 *
			 * @param array Varsayılan uç noktalar.
			 */
			"{$this->prefix}_ajax_endpoints",
			array(
				'save_api_key'                => array( $this, 'save_api_key' ),
				'index_resource'              => array( $this, 'index_resource' ),
				'store_resource'              => array( $this, 'store_resource' ),
				'show_resource'               => array( $this, 'show_resource' ),
				'update_resource'             => array( $this, 'update_resource' ),
				'destroy_resource'            => array( $this, 'destroy_resource' ),
				'clear_http_logs'             => array( $this, 'clear_http_logs' ),
				'print_barcode'               => array( $this, 'print_barcode' ),
				'update_shipment_setting'     => array( $this, 'update_shipment_setting' ),
				'update_other_settings'       => array( $this, 'update_other_settings' ),
				'update_wc_order_settings'    => array( $this, 'update_wc_order_settings' ),
				'update_wc_checkout_settings' => array( $this, 'update_wc_checkout_settings' ),
				'bulk_print'                  => array( $this, 'bulk_print' ),
			)
		);

		if ( false === empty( $this->endpoints ) ) {
			foreach ( array_keys( $this->endpoints ) as $endpoint ) {
				add_action( "wp_ajax_{$this->prefix}_{$endpoint}", array( $this, 'middleware' ) );
				add_action( "wp_ajax_nopriv_{$this->prefix}_{$endpoint}", array( $this, 'middleware' ) );
			}
		}
	}

	/**
	 * Ajax çağrılarının güvenlik kontrolünü yapar ve
	 * ilgili aksiyona yönlendirir.
	 *
	 * @return void
	 */
	public function middleware() {
		// Ajax nonce kontrolü yap.
		if ( check_ajax_referer() && isset( $_REQUEST['action'] ) ) {
			$next_action = str_replace( "{$this->prefix}_", '', sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) );

			try {
				/**
				 * Uç noktaya istinaden çalıştırılacak fonksiyonu tanımlar,
				 * filter aracılığı ile farklı sınıfların farklı fonksiyonları ile de aksiyon alınabilir.
				 *
				 * @param string|array $callback Çalıştırılacak fonksiyon.
				 * @param mixed $next_action Uç nokta.
				 */
				$action = apply_filters( "{$this->prefix}_ajax_action", $this->endpoints[ $next_action ], $next_action );
				// $action tanımlanan fonksiyonu çağır.
				$response = call_user_func( $action, json_decode( file_get_contents( 'php://input' ) ) );

				// WP_Error kontrolü yapar.
				if ( is_wp_error( $response ) ) {
					wp_send_json( array( 'error_message' => $response->get_error_message() ), 500 );
				}

				wp_send_json( $response );
			} catch ( Exception $e ) {

				if ( $e instanceof GCARGO_Ajax_Validation_Exception ) {
					wp_send_json( $e->get_validation_errors(), 422 );
				}
				wp_send_json( array( 'error_message' => $e->getMessage() ), 500 );
			}
		}
	}

	/**
	 * Geri dönüş fonksiyonu; save_api_key.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function save_api_key( $request ) {
		return gcargo_api_key()->set( $request->api_key );
	}

	/**
	 * Geri dönüş fonksiyonu; index_resource.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function index_resource( $request ) {
		$resource = "gcargo_{$request->resource}";
		return $resource()->index();
	}

	/**
	 * Geri dönüş fonksiyonu; store_resource.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function store_resource( $request ) {
		$resource = "gcargo_{$request->resource}";
		return $resource()->store( $request->data );
	}

	/**
	 * Geri dönüş fonksiyonu; show_resource.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function show_resource( $request ) {
		$resource = "gcargo_{$request->resource}";
		return $resource()->show( $request->id );
	}

	/**
	 * Geri dönüş fonksiyonu; update_resource.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function update_resource( $request ) {
		$resource = "gcargo_{$request->resource}";
		return $resource()->update( (array) $request->data, $request->id );
	}

	/**
	 * Geri dönüş fonksiyonu; destroy_resource.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function destroy_resource( $request ) {
		$resource = "gcargo_{$request->resource}";
		return $resource()->destroy( $request->id );
	}


	/**
	 * Geri dönüş fonksiyonu; clear_http_logs.
	 */
	public function clear_http_logs() {
		gcargo_http_client()->logger->clear_logs();
	}

	/**
	 * Geri dönüş fonksiyonu; print_barcode.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function print_barcode( $request ) {
		return gcargo_api_requests()->print( (array) $request->shipments );
	}

	/**
	 * Geri dönüş fonksiyonu; update_shipment_setting.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function update_shipment_setting( $request ) {
		return gcargo_api_requests()->update_shipment_setting( (array) $request->data );
	}

	/**
	 * Geri dönüş fonksiyonu; update_wc_order_settings.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function update_wc_order_settings( $request ) {
		return gcargo_woocommerce_order_status()->update_settings( (array) $request );
	}

		/**
		 * Geri dönüş fonksiyonu; update_other_settings.
		 *
		 * @param stdClass $request İstek parametreleri.
		 *
		 * @return mixed
		 */
	public function update_other_settings( $request ) {
		return gcargo_other_settings()->update_settings( (array) $request );
	}

	/**
	 * Geri dönüş fonksiyonu; bulk_print.
	 *
	 * @param stdClass $request İstek parametreleri.
	 *
	 * @return mixed
	 */
	public function bulk_print( $request ) {
		return gcargo_bulk_actions()->print_bulk_shipments( (array) $request );
	}
}
