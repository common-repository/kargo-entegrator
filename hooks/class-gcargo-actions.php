<?php
/**
 * GCARGO_Actions sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * Bu sınıf eklentinin kendi kancalarına tutunur.
 */
class GCARGO_Actions {

	/**
	 * Eklenti Prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * Gönderinin tutulduğu meta key
	 *
	 * @var string $order_meta_key
	 */
	private $order_meta_key = 'gcargo_shipments';

	/**
	 * Gönderi adedinin tutulduğu meta key
	 *
	 * @var string $item_meta_key
	 */
	private $item_meta_key = '_gcargo_shipped_qty';

	/**
	 * Kurucu method
	 */
	public function __construct() {
		add_action( "{$this->prefix}_shipment_store_request_completed", array( $this, 'add_shipment' ) );
		add_action( "{$this->prefix}_shipment_destroy_request_completed", array( $this, 'delete_shipment' ) );
		add_action( "{$this->prefix}_shipment_notification_request_completed", array( $this, 'update_shipment' ) );
		add_action( "{$this->prefix}_shipment_notification_request_completed", array( $this, 'update_wc_order_status' ) );
	}

	/**
	 * Gönderi oluştuğunda sipariş verilerine gönderi bilgisini ekleme.
	 *
	 * @param array $response Gönderi bilgisi.
	 */
	public function add_shipment( $response ) {
		$order = wc_get_order( $response['platform_id'] );

		if ( $order ) {

			$shipments = $order->get_meta( $this->order_meta_key, true );
			if ( ! is_array( $shipments ) ) {
				$shipments = array();
			}
			$shipments[] = $response;
			$order->update_meta_data( $this->order_meta_key, $shipments );

			foreach ( $response['lines'] as $line ) {
				$qty = (int) wc_get_order_item_meta( $line['platform_id'], $this->item_meta_key, true );
				wc_update_order_item_meta( $line['platform_id'], $this->item_meta_key, $qty + $line['quantity'] );
			}

			$order->save();
			$this->update_wc_order_status( [ 'shipment_id' => $response['id'] ] );

		}
	}
	/**
	 * Gönderi iptal edildiğinde sipariş verilerinden gönderi bilgisini siler.
	 *
	 * @param array $response Gönderi bilgisi.
	 */
	public function delete_shipment( $response ) {

		$order = wc_get_order( $response['platform_id'] );

		if ( $order ) {

			$shipments = $order->get_meta( $this->order_meta_key, true );

			foreach ( $shipments as $key => $shipment ) {
				if ( (int) $shipment['id'] === (int) $response['id'] ) {
					unset( $shipments[ $key ] );
				}
			}

			$order->update_meta_data( $this->order_meta_key, $shipments );

			foreach ( $response['lines'] as $line ) {
				$qty = (int) wc_get_order_item_meta( $line['platform_id'], $this->item_meta_key, true );
				wc_update_order_item_meta( $line['platform_id'], $this->item_meta_key, $qty - $line['quantity'] );
			}

			$order->save();
		}
	}

	/**
	 * SaaS tarafından gelen bildirimleri yorumlar
	 *
	 * @param array $post_data Bildirim verisi.
	 */
	public function update_shipment( $post_data ) {

		$shipment_data = gcargo_shipments()->show( $post_data['shipment_id'] );

		$order = wc_get_order( $shipment_data['platform_id'] );
		if ( $order ) {

			$shipments = $order->get_meta( $this->order_meta_key, true );

			if ( is_array( $shipments ) ) {
				foreach ( $shipments as $key => $shipment ) {

					if ( (int) $shipment['id'] === (int) $shipment_data['id'] ) {
						$shipments[ $key ] = $shipment_data;
					}
				}
				$order->update_meta_data( $this->order_meta_key, $shipments );
				$order->save();
			}
		}
	}

	/**
	 * SaaS tarafından gelen bildirimlere göre sipariş durumunu değiştiren fonksiyon.
	 *
	 * @param array $post_data Bildirim verisi.
	 */
	public function update_wc_order_status( $post_data ) {

		$shipment_data = gcargo_shipments()->show( $post_data['shipment_id'] );
		$order         = wc_get_order( $shipment_data['platform_id'] );

		if ( $order ) {
			$settings = gcargo_woocommerce_order_status()->get_settings();

			foreach ( $settings as $setting ) {
				if ( $setting->active && $setting->key === $shipment_data['status'] ) {
					$order->update_status( $setting->status, '<b style="color: #4338ca;">Kargo Entegratör:</b>' );
					do_action( $this->prefix . '_shippment_status_update_' . $shipment_data['status'], $shipment_data, $order );
					break;
				}
			}
		}
	}
}
