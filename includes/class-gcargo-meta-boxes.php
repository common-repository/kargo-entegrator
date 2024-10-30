<?php
/**
 * GurmeCargo meta boxları olşturan sınıf olan GCARGO_Meta_Boxes sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

/**
 * GurmeCargo meta box sınıfı
 */
class GCARGO_Meta_Boxes {

	/**
	 * Eklenti Prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * Meta boxları kayıt etme.
	 *
	 * @return void
	 */
	public function add_meta_box() {
		if ( class_exists( CustomOrdersTableController::class ) && function_exists( 'wc_get_container' ) ) {
			$hpos_enabled = wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled();
			add_meta_box( "{$this->prefix}_shop_order_meta_box", 'Kargo Entegratör', array( $this, 'shop_order_meta_box' ), $hpos_enabled ? wc_get_page_screen_id( 'shop-order' ) : 'shop_order', 'side', 'high' );
		}
	}

	/**
	 * GurmePOS shop order meta boxu render eder.
	 *
	 * @param Automattic\WooCommerce\Admin\Overrides\Order|WC_Order|WP_Post $post Post
	 */
	public function shop_order_meta_box( $post ) {

		$post_id = $post instanceof Automattic\WooCommerce\Admin\Overrides\Order || $post instanceof WC_Order ? $post->get_id() : $post->ID;

		if ( $post_id ) {
			$order     = wc_get_order( $post_id );
			$shipments = $order->get_meta( 'gcargo_shipments', true );
			$mapper    = gcargo_map_wc_order( $post_id );
			$localize  = array_merge(
				gcargo_data_handler()->default_localize_data(),
				array(
					'order_id'              => $post_id,
					'shipments'             => $shipments,
					'order_shipment_status' => $mapper->get_shipment_status(),
					'shipment'              => $mapper->create_shipment_request(),
					'warehouses'            => $mapper->get_warehouses(),
					'cargo_integrations'    => $mapper->get_cargo_integrations(),
					'api_key'               => gcargo_api_key()->get(),
				)
			);

			gcargo_vue()
			->set_localize( $localize )
			->set_vue_page( 'admin-order-detail' )
			->require_script()
			->require_style()
			->create_app_div();
		}
	}
}
