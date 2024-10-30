<?php
/**
 * GurmeCargo toplu işlemleri yönetim sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo toplu işlemleri yönetim sınıfı.
 */
class GCARGO_Bulk_Actions {

	/**
	 * Toplu yolla gönderimlerin oluşturulacağı metod.
	 *
	 * @param array $items İşlenen postlar.
	 *
	 * @return string  Geri dönüş URL.
	 */
	public function print_bulk_shipments( $items ) {
		$shipment_ids = [];
		foreach ( $items as $item ) {
			$shipments = wc_get_order( $item )->get_meta( 'gcargo_shipments', true );
			if ( false === empty( $shipments ) ) {
				foreach ( $shipments as $shipment ) {
					$shipment_ids[] = $shipment['id'];
				}
			}
		}

		if ( false === empty( $shipment_ids ) ) {
			return add_query_arg(
				array(
					'page'       => 'gcargo-bulk-print',
					'print-data' => $shipment_ids,
				),
				admin_url( 'admin.php' )
			);
		}
	}

	/**
	 * Toplu yolla gönderimlerin oluşturulacağı metod.
	 *
	 * @param array $items İşlenen postlar.
	 *
	 * @return string $sendback Geri dönüş URL.
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function create_bulk_shipments( $items ) {

		return add_query_arg(
			array(
				'page'      => 'gcargo-bulk-create',
				'order_ids' => $items,
			),
			admin_url( 'admin.php' )
		);
	}
}
