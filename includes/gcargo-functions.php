<?php
/**
 * Bu dosya "gcargo_" prefixli yardımcı fonksiyonları barındırır.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo için görünüm dosyasını getirir.
 *
 * @param string $view_name Dahil edilecek görünüm.
 * @param array  $args Görünüm içerisinde kullanılacak veriler.
 * @param string $view_path Görünüm klasör yolu.
 *
 * @return void
 */
function gcargo_get_view( $view_name, $args = array(), $view_path = GCARGO_PLUGIN_DIR_PATH ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); //phpcs:ignore  WordPress.PHP.DontExtract.extract_extract
	}
	$view = $view_path . '/views/' . $view_name;
	include $view;
}


/**
 * Veri temizleme işlemi. sanitize_text_field fonksiyonunu kullanır.
 * Gönderilen parametre dizi ise (array) her elemanını için tekrar kendini çağırır.
 *
 * @param mixed $varible Temizlenecek veri.
 *
 * @return mixed
 */
function gcargo_clean( $varible ) {
	if ( is_array( $varible ) ) {
		return array_map( 'gcargo_clean', $varible );
	}
	return is_scalar( $varible ) ? sanitize_text_field( wp_unslash( $varible ) ) : $varible;
}

/**
 * Frontend için gerekli kelime, cümle çevirilerini döndürür.
 *
 * @return array
 */
function gcargo_get_i18n_texts() {
	$gcargo_texts = include GCARGO_PLUGIN_DIR_PATH . '/languages/gcargo-settings-texts.php';
	return array( 'en' => $gcargo_texts );
}

/**
 * Kullanıcıya göre yetki tanımı.
 *
 * @return string $role;
 */
function gcargo_capability() {
	return apply_filters( 'gcargo_capability', 'manage_options' );
}

/**
 * GurmeKargo için ortam bilgisi.
 *
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @return array
 */
function gcargo_get_env_info() {
	$theme = wp_get_theme( get_stylesheet() );

	$response   = wp_remote_get( 'https://icanhazip.com/' );
	$ip_address = __( 'Not available', 'kargo-entegrator' );

	if ( ! is_wp_error( $response ) ) {
		$ip_address = trim( wp_remote_retrieve_body( $response ) );
		$ip_address = filter_var( $ip_address, FILTER_VALIDATE_IP ) ? $ip_address : __( 'Not available', 'kargo-entegrator' );
	}

	return array(
		'wordpress'   => array(
			array(
				'label' => __( 'Theme Name', 'kargo-entegrator' ),
				'value' => $theme->get( 'Name' ),
			),
			array(
				'label' => __( 'Theme Version', 'kargo-entegrator' ),
				'value' => $theme->get( 'Version' ),
			),
			array(
				'label' => __( 'WordPress Version', 'kargo-entegrator' ),
				'value' => get_bloginfo( 'version' ),
			),
			array(
				'label' => __( 'Multisite', 'kargo-entegrator' ),
				'value' => is_multisite() ? __( 'Yes', 'kargo-entegrator' ) : __( 'No', 'kargo-entegrator' ),
			),
			array(
				'label' => __( 'Debug Mode', 'kargo-entegrator' ),
				'value' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? __( 'Activated', 'kargo-entegrator' ) : __( 'Disabled', 'kargo-entegrator' ),
			),
		),
		'server'      => array(
			array(
				'label' => 'PHP Version',
				'value' => function_exists( 'phpversion' ) && phpversion() ? phpversion() : __( 'Not available', 'kargo-entegrator' ),
			),
			array(
				'label' => 'PHP cURL',
				'value' => function_exists( 'curl_init' ) ? __( 'Yes', 'kargo-entegrator' ) : __( 'No', 'kargo-entegrator' ),
			),
			array(
				'label' => 'PHP Memory Limit',
				'value' => ini_get( 'memory_limit' ),
			),
			array(
				'label' => 'PHP Max Execution Time',
				'value' => ini_get( 'max_execution_time' ),
			),
			array(
				'label' => __( 'IP Address', 'kargo-entegrator' ),
				'value' => $ip_address,
			),
		),
		'woocommerce' => array(
			array(
				'label' => 'Version',
				'value' => WC_VERSION,
			),
		),
	);
}
