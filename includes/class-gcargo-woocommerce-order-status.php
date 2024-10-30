<?php
/**
 * GurmeCargo WooCommerce durumları ile entegre olmayı sağlayan sınıf.
 *
 * @package GurmeHub
 */

/**
 * Order Status Sınıfı.
 */
class GCARGO_WooCommerce_Order_Status extends GCARGO_Options {

	/**
	 * wp_options option anahtarı.
	 *
	 * @var string $options_table_key
	 */
	public $options_table_key = 'gcargo_wc_order_status_settings';


	/**
	 * Kargo Entegratör için özel sipariş durumları getirir
	 *
	 * @return array
	 */
	public function get_gcargo_order_status() {

		$gcargo_statuses = array(
			array(
				'key'  => 'wc-gcargo-ready',
				'text' => __( 'Ready to Ship', 'kargo-entegrator' ),
			),
			array(
				'key'  => 'wc-gcargo-shipped',
				'text' => __( 'Delivered to Cargo', 'kargo-entegrator' ),
			),
			array(
				'key'  => 'wc-gcargo-on-transit',
				'text' => __( 'During the Cargo Transfer Process', 'kargo-entegrator' ),
			),
			array(
				'key'  => 'wc-gcargo-in-courier',
				'text' => __( 'Cargo Delivery Process', 'kargo-entegrator' ),
			),
			array(
				'key'  => 'wc-gcargo-dlvr',
				'text' => __( 'At the Distribution Center', 'kargo-entegrator' ),
			),
			array(
				'key'  => 'wc-gcargo-delivered',
				'text' => __( 'Cargo Delivered', 'kargo-entegrator' ),
			),

		);

		return apply_filters( 'gcargo_custom_order_statuses', $gcargo_statuses );
	}

	/**
	 * WooCommerce Siparişlerini Döndürür..
	 *
	 * @return array
	 */
	public function get_wc_order_status() {
		$wc_order_status = wc_get_order_statuses();
		$all_statuses    = array();

		foreach ( $wc_order_status as $key => $status ) {

			if ( 'wc-failed' === $key || 'wc-checkout-draft' === $key ) {
				continue;
			}

			$all_statuses[] = array(
				'key'  => $key,
				'text' => $status,
			);
		}

		$gcargo_statuses = $this->get_gcargo_order_status();
		foreach ( $gcargo_statuses as $status ) {
			$all_statuses[] = array(
				'key'  => $status['key'],
				'text' => $status['text'],
			);
		}

		return $all_statuses;
	}

	/**
	 * WooCommerce Sipariş durumlarını yaratır
	 */
	public function create_wc_order_status() {
		$order_status = gcargo_woocommerce_order_status()->get_settings();

		foreach ( $order_status as $status ) {
			if ( isset( $status->status ) ) {
				register_post_status(
					$status->status,
					array(
						'label'                     => ucfirst( str_replace( 'gcargo-', '', $status->status ) ),
						'public'                    => true,
						'show_in_admin_status_list' => true,
						'show_in_admin_all_list'    => true,
						'exclude_from_search'       => false,
						'label_count'               => _n_noop( ucfirst( str_replace( 'gcargo-', '', $status->status ) ) . ' <span class="count">(%s)</span>', ucfirst( str_replace( 'gcargo-', '', $status->status ) ) . ' <span class="count">(%s)</span>' ), // @codingStandardsIgnoreLine
					)
				);
			}
		}
	}
}
