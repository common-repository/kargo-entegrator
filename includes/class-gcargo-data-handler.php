<?php
/**
 * GurmeCargo için firmaların niteliklerini taşıyan GCARGO_Data_Handler sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * GCARGO_Data_Handler sınıfı
 */
class GCARGO_Data_Handler {

	/**
	 * Varsayılan window dataları
	 *
	 * @return array
	 */
	public function default_localize_data() {
		return array(
			'prefix'     => GCARGO_PREFIX,
			'assets_url' => GCARGO_ASSETS_DIR_URL,
			'version'    => GCARGO_VERSION,
			'home_url'   => home_url(),
			'admin_url'  => admin_url(),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce(),
			'strings'    => gcargo_get_i18n_texts(),
		);
	}
}
