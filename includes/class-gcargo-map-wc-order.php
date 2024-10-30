<?php
/**
 * GurmeCargo için sipariş niteliklerini eşleştiren GCARGO_Map_WC_Order sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * GCARGO_Map_WC_Order sınıfı
 */
class GCARGO_Map_WC_Order {

	/**
	 * WC siparişi.
	 *
	 * @var WC_Order $order
	 */
	protected $order;

	/**
	 * Eklenti Prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * Depolar.
	 *
	 * @var array
	 */
	public $warehouses;

	/**
	 * Kargo entegrasyonları.
	 *
	 * @var array
	 */
	public $cargo_integrations;

	/**
	 * Varsayılan gönderi ayarları.
	 *
	 * @var array
	 */
	public $shipment_setting;

	/**
	 * Kurucu method
	 *
	 * @param int|string $order_id WC sipariş kimliği.
	 * @param array|null $warehouses Depolar.
	 * @param array|null $cargo_integrations Kargo entegrasyonları.
	 * @param array|null $shipment_setting Varsayılan ayarlar.
	 */
	public function __construct(
		$order_id,
		$warehouses = null,
		$cargo_integrations = null,
		$shipment_setting = null
	) {
		$this->order              = wc_get_order( $order_id );
		$this->warehouses         = $warehouses ? $warehouses : gcargo_warehouses()->index();
		$this->cargo_integrations = $cargo_integrations ? $cargo_integrations : gcargo_cargo_integrations()->index();
		$this->shipment_setting   = $shipment_setting ? $shipment_setting : gcargo_api_requests()->get_shipment_setting();
	}

	/**
	 * Siparişin gönderi durumunu döndürür.
	 *
	 * @return string
	 */
	public function get_shipment_status() {
		$shipments = $this->order->get_meta( 'gcargo_shipments', true );
		if ( empty( $shipments ) ) {
			return 'ready_for_shipment';
		}

		$unpackaged_item_qty = 0;

		foreach ( $this->order->get_items() as $item ) {
			$sold_qty             = $item->get_quantity();
			$shipped_qty          = (int) wc_get_order_item_meta( $item->get_id(), '_gcargo_shipped_qty', true );
			$unpackaged_item_qty += $sold_qty - $shipped_qty;
		}

		if ( $unpackaged_item_qty ) {
			return 'partial_shipped';
		}

		return 'all_shipped';
	}

	/**
	 * Depoları döndürür.
	 *
	 * @return array
	 */
	public function get_warehouses() {
		return $this->warehouses;
	}

	/**
	 * Kargo entegrasyonlarını döndürür.
	 *
	 * @return array
	 */
	public function get_cargo_integrations() {
		return $this->cargo_integrations;
	}

	/**
	 * Doğrudan istek oluşturma methodu.
	 *
	 * @return array
	 */
	public function create_shipment_request() {

		return array_merge(
			$this->get_default(),
			$this->get_payment(),
			[ 'lines' => array_filter( $this->get_lines(), fn( $line ) => $line['quantity'] ) ],
			[ 'customer' => $this->get_receiver() ],
			[ 'need_shipment' => $this->get_shipment_status() !== 'all_shipped' ],
			[ 'current_shipments' => $this->get_shipments() ]
		);
	}

	/**
	 * Varsayılan bilgileri eşleştir.
	 *
	 * @return array
	 */
	public function get_default() {

		return array_merge(
			$this->shipment_setting,
			array(
				'cargo_integration_id' => array_column( $this->cargo_integrations, 'id', 'is_default' )[ true ] ?? null,
				'warehouse_id'         => array_column( $this->warehouses, 'id', 'is_default' )[ true ] ?? null,
			),
			array(
				'platform_id'      => $this->order->get_id(),
				'platform_d_id'    => $this->order->get_id(),
				'note'             => $this->order->get_customer_note(),
				'platform'         => 'woocommerce',
				'notification_url' => home_url( "/{$this->prefix}-notification" ),
				'waybill_number'   => '',
				'invoice_number'   => '',
			)
		);
	}

	/**
	 * Ödeme bilgilerini eşleştir.
	 *
	 * @return array
	 */
	public function get_payment() {
		return array(
			'is_pay_at_door' => false,
			'total'          => $this->order->get_total(),
			'currency'       => $this->order->get_currency(),
			'invoice_number' => (string) $this->order->get_id(),
			'waybill_number' => (string) $this->order->get_id(),
		);
	}

	/**
	 * Ürün bilgilerini eşleştir.
	 *
	 * @return array
	 */
	public function get_lines() {
		$lines = array();

		/**
		 * Order item
		 *
		 * @var WC_Order_Item_Product $item
		 */
		foreach ( $this->order->get_items() as $item ) {

			$line = array(
				'platform_id' => $item->get_id(),
				'title'       => $item->get_name(),
				'sold_qty'    => (int) $item->get_quantity(),
				'shipped_qty' => (int) wc_get_order_item_meta( $item->get_id(), '_gcargo_shipped_qty', true ),
			);

			$line['quantity'] = $line['sold_qty'] - $line['shipped_qty'];

			$product_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();

			if ( $product_id ) {
				$line['image'] = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )[0];

				$product = wc_get_product( $product_id );

				$line['sku'] = $product->get_sku();

			}

			$lines[] = $line;
		}

		return $lines;
	}

	/**
	 * Alıcı bilgilerini eşleştir.
	 *
	 * @return array
	 */
	public function get_receiver() {
		$shipping = array();

		$billing = array(
			'name'       => $this->order->get_billing_first_name(),
			'surname'    => $this->order->get_billing_last_name(),
			'phone'      => $this->order->get_billing_phone(),
			'email'      => $this->order->get_billing_email(),
			'city'       => $this->get_state( $this->order->get_billing_state(), $this->order->get_billing_country() ),
			'district'   => $this->order->get_billing_city(),
			'address'    => $this->order->get_billing_address_1() . ' ' . $this->order->get_billing_address_2(),
			'postcode'   => $this->order->get_billing_postcode(),
			'country'    => $this->get_country( $this->order->get_billing_country() ),
			'tax_number' => '',
			'tax_office' => '',
		);

		if ( $this->order->has_shipping_address() ) {
			$shipping = array(
				'name'     => $this->order->get_shipping_first_name(),
				'surname'  => $this->order->get_shipping_last_name(),
				'city'     => $this->get_state( $this->order->get_shipping_state(), $this->order->get_shipping_country() ),
				'district' => $this->order->get_shipping_city(),
				'address'  => $this->order->get_shipping_address_1() . ' ' . $this->order->get_shipping_address_2(),
				'postcode' => $this->order->get_shipping_postcode(),
				'country'  => $this->get_country( $this->order->get_shipping_country() ),
			);
		}

		return apply_filters( "{$this->prefix}_shipment_receiver_data", wp_parse_args( $shipping, $billing ) );
	}

	/**
	 * Ülke bilgisi eşleştirme.
	 *
	 * @param string $country Ülke bilgisi
	 */
	private function get_country( $country ) {
		return WC()->countries->get_countries()[ $country ];
	}
	/**
	 * İl bilgisi eşleştirme.
	 *
	 * @param string $state İl bilgisi
	 * @param string $country Ülke bilgisi
	 */
	private function get_state( $state, $country ) {
		return WC()->countries->get_states( $country )[ $state ];
	}

	/**
	 * Siparişin gönderi idlerini döndürür.
	 *
	 * @return array
	 */
	private function get_shipments() {
		$response  = [];
		$shipments = $this->order->get_meta( 'gcargo_shipments', true );
		if ( false === empty( $shipments ) ) {
			$response = array_map( fn( $res ) => $res['id'], $shipments );
		}

		return $response;
	}
}
