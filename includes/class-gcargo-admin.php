<?php
/**
 * GurmeCargo için admin menülerini olşturan sınıfı olan GCARGO_Admin_Menu sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo admin menü ve bar sınıfı
 */
class GCARGO_Admin {


	/**
	 * Eklenti prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * Eklenti simgesi
	 *
	 * @var string $icon
	 */
	protected $icon = '';

	/**
	 * Eklenti menü ismi
	 *
	 * @var string $parent_title
	 */
	public $parent_title = 'Kargo Entegratör';

	/**
	 * Eklenti menü urlini oluşturacak slug
	 *
	 * @var string $parent_slug
	 */
	public $parent_slug = 'gurmecargo';

	/**
	 * Admin menüye eklenecek menüleri ekler ve callback fonksiyonlarını organize eder
	 *
	 * @return void
	 *   */
	public function admin_menu() {
		$menu_pages = array(
			array(
				'menu_title' => __( 'Dashboard', 'kargo-entegrator' ),
				'menu_slug'  => $this->parent_slug,
			),
			array(
				'menu_title' => __( 'Settings', 'kargo-entegrator' ),
				'menu_slug'  => "{$this->prefix}-settings",
			),
			array(
				'menu_title' => __( 'Bulk Print', 'kargo-entegrator' ),
				'menu_slug'  => "{$this->prefix}-bulk-print",
				'hidden'     => true,
			),
			array(
				'menu_title' => __( 'Bulk Create', 'kargo-entegrator' ),
				'menu_slug'  => "{$this->prefix}-bulk-create",
				'hidden'     => true,
			),
		);

		// Eğer Diğer Ayarlar -> Admin Menüsünü Aktif Et seçeneğini aktif ise çalışır.
		if ( true === gcargo_other_settings()->get_setting_by_key( 'admin_bar_menu' ) ) {

			add_menu_page(
				$this->parent_title,
				$this->parent_title,
				gcargo_capability(),
				$this->parent_slug,
				'__return_false',
				'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTQiIHZpZXdCb3g9IjAgMCAxNiAxNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xLjc2OTc5IDIuOTk2OEMxLjYzOTgzIDMuMDU1OTMgMS41MjgzMiAzLjE1MzM3IDEuNDYxOTcgMy4yODYwNUwwLjE5MDk3NCA1LjgyNjc4QzAuMDI4ODQ0MiA2LjE1MDkyIDAuMTcwMTYxIDYuNTQ0NzIgMC41MDEyNzYgNi42OTE5NEwxLjM5NDk1IDcuMDg5MTZWOS45MjUyOUMxLjM5NDk1IDEwLjE3NjUgMS41NDI3NyAxMC40MDQxIDEuNzcyMzEgMTAuNTA2Mkw3LjQ5MzA4IDEzLjA0ODJDNy42NTcxIDEzLjEyMDkgNy44NDQxOSAxMy4xMjA5IDguMDA4MjEgMTMuMDQ4MkwxMy43Mjc4IDEwLjUwNjJDMTMuOTU3MiAxMC40MDQxIDE0LjEwNTEgMTAuMTc2NSAxNC4xMDUgOS45MjUyVjcuMDg5MDZMMTQuOTk4NyA2LjY5MTg1QzE1LjMyOTggNi41NDQ2MiAxNS40NzExIDYuMTUwODMgMTUuMzA5IDUuODI2NjlMMTQuMDM4IDMuMjg1OTZDMTMuOTcyMSAzLjE1NDM0IDEzLjg2MTcgMy4wNTczOCAxMy43MzI2IDIuOTk4MDFMOC4wMDgyNCAwLjQ0ODUzNUM3LjkzMDQzIDAuNDE0MDA1IDcuODQ2MzUgMC4zOTU0MzggNy43NjEyIDAuMzkzOUM3LjY2ODQ5IDAuMzkyMjQ0IDcuNTc2NjEgMC40MTA5MjkgNy40OTE4MiAwLjQ0ODUzNUwxLjc2OTc5IDIuOTk2OFpNNy43NTAwMSAxLjcyNThWNS40MTcwNEwzLjU5NTU4IDMuNTcxM0w3Ljc1MDAxIDEuNzI1OFpNMi4zMjcxMyA0LjM5NjgzTDYuODgzNTggNi40MjI1NEw2LjE4MTE1IDcuODI2MzVMMS42MjQ2OSA1LjgwMTk0TDIuMzI3MTMgNC4zOTY4M1pNMTMuMTcyOSA0LjM5NjgzTDEzLjg3NTQgNS44MDE5NEw5LjMxODk5IDcuODI2MzVMOC42MTY0MyA2LjQyMjU0TDEzLjE3MjkgNC4zOTY4M1pNMi42NjYxOSA3LjY1Mzk1TDYuMjIxMiA5LjIzMzk1QzYuNTMxMzggOS4zNzEyNCA2Ljg5NDY2IDkuMjQwNTcgNy4wNDY1MSA4LjkzNzM2TDcuMTE0ODcgOC44MDA3OFYxMS40ODkyTDIuNjY2MTggOS41MTE5OUwyLjY2NjE5IDcuNjUzOTVaTTEyLjgzNDEgNy42NTM5NVY5LjUxMjFMOC4zODU0NSAxMS40ODkzVjguODAwODlMOC40NTM2OCA4LjkzNzQ3QzguNjA1NTIgOS4yNDA4MSA4Ljk2ODk0IDkuMzcxMzUgOS4yNzkxMiA5LjIzNDE3TDEyLjgzNDEgNy42NTM5NVoiIGZpbGw9ImJsYWNrIi8+CjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBkPSJNMS43Njk3OSAyLjk5NjhDMS42Mzk4MyAzLjA1NTkzIDEuNTI4MzIgMy4xNTMzNyAxLjQ2MTk3IDMuMjg2MDVMMC4xOTA5NzQgNS44MjY3OEMwLjAyODg0NDIgNi4xNTA5MiAwLjE3MDE2MSA2LjU0NDcyIDAuNTAxMjc2IDYuNjkxOTRMMS4zOTQ5NSA3LjA4OTE2VjkuOTI1MjlDMS4zOTQ5NSAxMC4xNzY1IDEuNTQyNzcgMTAuNDA0MSAxLjc3MjMxIDEwLjUwNjJMNy40OTMwOCAxMy4wNDgyQzcuNjU3MSAxMy4xMjA5IDcuODQ0MTkgMTMuMTIwOSA4LjAwODIxIDEzLjA0ODJMMTMuNzI3OCAxMC41MDYyQzEzLjk1NzIgMTAuNDA0MSAxNC4xMDUxIDEwLjE3NjUgMTQuMTA1IDkuOTI1MlY3LjA4OTA2TDE0Ljk5ODcgNi42OTE4NUMxNS4zMjk4IDYuNTQ0NjIgMTUuNDcxMSA2LjE1MDgzIDE1LjMwOSA1LjgyNjY5TDE0LjAzOCAzLjI4NTk2QzEzLjk3MjEgMy4xNTQzNCAxMy44NjE3IDMuMDU3MzggMTMuNzMyNiAyLjk5ODAxTDguMDA4MjQgMC40NDg1MzVDNy45MzA0MyAwLjQxNDAwNSA3Ljg0NjM1IDAuMzk1NDM4IDcuNzYxMiAwLjM5MzlDNy42Njg0OSAwLjM5MjI0NCA3LjU3NjYxIDAuNDEwOTI5IDcuNDkxODIgMC40NDg1MzVMMS43Njk3OSAyLjk5NjhaTTcuNzUwMDEgMS43MjU4VjUuNDE3MDRMMy41OTU1OCAzLjU3MTNMNy43NTAwMSAxLjcyNThaTTIuMzI3MTMgNC4zOTY4M0w2Ljg4MzU4IDYuNDIyNTRMNi4xODExNSA3LjgyNjM1TDEuNjI0NjkgNS44MDE5NEwyLjMyNzEzIDQuMzk2ODNaTTEzLjE3MjkgNC4zOTY4M0wxMy44NzU0IDUuODAxOTRMOS4zMTg5OSA3LjgyNjM1TDguNjE2NDMgNi40MjI1NEwxMy4xNzI5IDQuMzk2ODNaTTIuNjY2MTkgNy42NTM5NUw2LjIyMTIgOS4yMzM5NUM2LjUzMTM4IDkuMzcxMjQgNi44OTQ2NiA5LjI0MDU3IDcuMDQ2NTEgOC45MzczNkw3LjExNDg3IDguODAwNzhWMTEuNDg5MkwyLjY2NjE4IDkuNTExOTlMMi42NjYxOSA3LjY1Mzk1Wk0xMi44MzQxIDcuNjUzOTVWOS41MTIxTDguMzg1NDUgMTEuNDg5M1Y4LjgwMDg5TDguNDUzNjggOC45Mzc0N0M4LjYwNTUyIDkuMjQwODEgOC45Njg5NCA5LjM3MTM1IDkuMjc5MTIgOS4yMzQxN0wxMi44MzQxIDcuNjUzOTVaIiBmaWxsPSJibGFjayIvPgo8L3N2Zz4=',
				59
			);

			foreach ( $menu_pages as $sub_menu_page ) {
				add_submenu_page(
					array_key_exists( 'hidden', $sub_menu_page ) && $sub_menu_page['hidden'] ? 'admin.php' : $this->parent_slug,
					$sub_menu_page['menu_title'],
					$sub_menu_page['menu_title'],
					gcargo_capability(),
					$sub_menu_page['menu_slug'],
					array( $this, 'view' ),
				);
			}
		} else {
			add_submenu_page(
				'woocommerce',
				__( 'Kargo Ayarları', 'kargo-entegrator' ),
				__( 'Kargo Ayarları', 'kargo-entegrator' ),
				gcargo_capability(),
				"{$this->prefix}-settings",
				array( $this, 'view' )
			);

			foreach ( $menu_pages as $key => $menu_page ) {
				if ( __( 'Settings', 'kargo-entegrator' ) === isset( $menu_page['menu_title'] ) && $menu_page['menu_title'] ) {
					unset( $menu_pages[ $key ] );
				}
			}

			foreach ( $menu_pages as $hidden_menu ) {
				add_submenu_page(
					'admin.php',
					$hidden_menu['menu_title'],
					$hidden_menu['menu_title'],
					gcargo_capability(),
					$hidden_menu['menu_slug'],
					array( $this, 'view' )
				);
			}
		}
	}

	/**
	 * Eklenti alt menüleri açıldığında ilgili vue sayfasını render eder
	 *
	 * @return void
	 */
	public function view() {
		$page = isset( $_GET['page'] ) ? str_replace( "{$this->prefix}-", '', gcargo_clean( $_GET['page'] ) ) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( $page ) {
			gcargo_vue()
				->set_vue_page( $page )
				->set_localize( $this->get_localize_data( $page ) )
				->require_script()
				->require_style()
				->create_app_div();
		}
	}

	/**
	 * Vue render edildiğinde kullanacağı verileri düzenler.
	 *
	 * @param string $page Açılan sayfa.
	 *
	 * @return array
	 */
	private function get_localize_data( $page ) {
		$localize = gcargo_data_handler()->default_localize_data();

		switch ( $page ) {
			case 'settings':
				$localize['api_key']               = gcargo_api_key()->get();
				$localize['warehouses']            = gcargo_warehouses()->index();
				$localize['cargo_integrations']    = gcargo_cargo_integrations()->index();
				$localize['cargo_companies']       = gcargo_api_requests()->get_cargo_companies();
				$localize['http_logs']             = gcargo_http_client()->logger->get_logs();
				$localize['shipment_setting']      = gcargo_api_requests()->get_shipment_setting();
				$localize['other_settings']        = gcargo_other_settings()->get_settings();
				$localize['order_status']          = gcargo_woocommerce_order_status()->get_wc_order_status();
				$localize['order_status_settings'] = gcargo_woocommerce_order_status()->get_settings();
				$localize['status']                = gcargo_get_env_info();

				break;
			case 'bulk-print':
				$localize['print_data'] = isset( $_GET['print-data'] ) ? gcargo_clean( $_GET['print-data'] ) : false; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
				break;
			case 'bulk-create':
				$warehouses                     = gcargo_warehouses()->index();
				$cargo_integrations             = gcargo_cargo_integrations()->index();
				$shipment_setting               = gcargo_api_requests()->get_shipment_setting();
				$localize['warehouses']         = $warehouses;
				$localize['cargo_integrations'] = $cargo_integrations;
				$localize['shipment_setting']   = $shipment_setting;
				$localize['orders']             = array_map(
					function ( $order_id ) use ( $warehouses, $cargo_integrations, $shipment_setting ) {
						return gcargo_map_wc_order(
							$order_id,
							$warehouses,
							$cargo_integrations,
							$shipment_setting
						)->create_shipment_request();
					},
					array_reverse( $_GET['order_ids'] )  //phpcs:ignore 
				);
				break;
		}
		return $localize;
	}
}
