<?php
/**
 * GCARGO_WooCommerce sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

/**
 * Bu sınıf eklenti aktif olur olmaz çalışmaya başlar ve
 * kurucu fonksiyonu içerisindeki WooCommerce kancalarına tutunur.
 */
class GCARGO_WooCommerce {

	/**
	 * Eklenti Prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * GCARGO_WooCommerce kurucu fonksiyonu
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'before_woocommerce_init', array( $this, 'before_woocommerce_init' ) );
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );
		add_filter( 'woocommerce_account_orders_columns', array( $this, 'account_orders_columns' ) );
		add_action( 'woocommerce_my_account_my_orders_column_gcargo-shipments', array( $this, 'my_orders_column' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'after_order_table' ) );
		add_filter( 'wc_order_statuses', array( $this, 'add_gcargo_order_status' ) );
	}

	/**
	 * before_woocommerce_init.
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function before_woocommerce_init() {

		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', GCARGO_PLUGIN_BASEFILE, true );
		}
	}

	/**
	 * Sipariş sonrası ekranında müşteriden, WooCommerce sipariş detay sayfasında
	 * yöneticiden sipariş ürünlerinin meta bilgilerini gizler.
	 *
	 * @param  array $hidden_metas Gizli metalar.
	 * @return array $hidden_metas
	 */
	public function hidden_order_itemmeta( $hidden_metas ) {
		$hidden_metas = array_merge(
			$hidden_metas,
			array( '_gcargo_shipped_qty' )
		);
		return $hidden_metas;
	}

	/**
	 * Müşteri sipariş tablosu kolon ekleme.
	 *
	 * @param  array $columns Kolonlar.
	 * @return array $columns
	 */
	public function account_orders_columns( $columns ) {
		$actions = $columns['order-actions'];
		unset( $columns['order-actions'] );
		$columns['gcargo-shipments'] = __( 'Cargo Tracking', 'kargo-entegrator' );
		$columns['order-actions']    = $actions;
		return $columns;
	}

	/**
	 * Kolon içeriği.
	 *
	 * @param  WC_Order $order Sipariş.
	 */
	public function my_orders_column( $order ) {
		$order     = wc_get_order( $order->get_id() );
		$shipments = $order->get_meta( 'gcargo_shipments', true );
		$mapper    = gcargo_map_wc_order( $order->get_id() );

		gcargo_vue()
		->set_localize(
			array_merge(
				gcargo_data_handler()->default_localize_data(),
				array(
					'order_id'    => $order->get_id(),
					'shipments'   => $shipments,
					'order_lines' => $mapper->get_lines(),
				)
			)
		)
		->set_vue_page( 'customer-order-detail' )
		->require_script()
		->require_style()
		->create_app_div();
	}

	/**
	 * Kolon içeriği.
	 *
	 * @param  WC_Order $order Sipariş.
	 */
	public function after_order_table( $order ) {
		?>
		<div>
			<h4><?php esc_html_e( 'Shipment Tracking', 'kargo-entegrator' ); ?></h4>
		<?php
		$this->my_orders_column( $order );
		?>
		</div>
		<br>
		<hr>
		<?php
	}

	/**
	 * Kargo Entegratör özel sipariş durumlarının gösterilmesini sağlar
	 *
	 * @param array $order_status Sipariş durumları.
	 */
	public function add_gcargo_order_status( $order_status ) {

		$gcargo_statuses       = gcargo_woocommerce_order_status()->get_gcargo_order_status();
		$order_status_settings = gcargo_woocommerce_order_status()->get_settings();

		foreach ( $order_status_settings as $status ) {
			if ( $status->active ) {
				$index = array_search( $status->status, array_column( $gcargo_statuses, 'key' ), true );
				if ( false !== $index ) {
					$order_status[ $status->status ] = $gcargo_statuses[ $index ]['text'];
				}
			}
		}

		return $order_status;
	}
}
