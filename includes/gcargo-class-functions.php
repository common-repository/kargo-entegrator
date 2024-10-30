<?php
/**
 * Bu dosya "gcargo_" prefixli sınıf fonksiyonları barındırır.
 *
 * @package GurmeHub
 */

/**
 * Vue.js renderlarını ekrana getirmek için kullanılır.
 *
 * @return GCARGO_Vue
 */
function gcargo_vue() {
	return new GCARGO_Vue();
}

/**
 * Yönetici menü ve bar için kullanılır.
 *
 * @return GCARGO_Admin
 */
function gcargo_admin() {
	return new GCARGO_Admin();
}

/**
 * Api Key yönetim sınıfı.
 *
 * @return GCARGO_Api_Key
 */
function gcargo_api_key() {
	return new GCARGO_Api_Key();
}

/**
 * WooCommerce sipariş durumu yönetim sınıfı.
 *
 * @return GCARGO_WooCommerce_Order_Status
 */
function gcargo_woocommerce_order_status() {
		return new GCARGO_WooCommerce_Order_Status();
}


/**
 * WooCommerce diğer ayarlar sınıfı.
 *
 * @return GCARGO_Other_Settings
 */
function gcargo_other_settings() {
	return new GCARGO_Other_Settings();
}

/**
 * HTTP istek sınıfı
 *
 * @return GCARGO_Http_Client
 */
function gcargo_http_client() {
	return new GCARGO_Http_Client();
}

/**
 * Log sınıfı
 *
 * @param string $file_name Log dosyası adı.
 *
 * @return GCARGO_Logger
 */
function gcargo_logger( $file_name ) {
	return new GCARGO_Logger( $file_name );
}

/**
 * Meta box sınıfı.
 *
 * @return GCARGO_Meta_Boxes
 */
function gcargo_meta_boxes() {
	return new GCARGO_Meta_Boxes();
}

/**
 * Gönderici bilgileri sınıfı
 *
 * @return GCARGO_Warehouses
 */
function gcargo_warehouses() {
	return new GCARGO_Warehouses();
}

/**
 * Kargo hesapları sınıfı
 *
 * @return GCARGO_Cargo_Integrations
 */
function gcargo_cargo_integrations() {
	return new GCARGO_Cargo_Integrations();
}

/**
 * Gönderiler sınıfı
 *
 * @return GCARGO_Shipments
 */
function gcargo_shipments() {
	return new GCARGO_Shipments();
}

/**
 * Statik veri tutan sınıf
 *
 * @return GCARGO_Data_Handler
 */
function gcargo_data_handler() {
	return new GCARGO_Data_Handler();
}

/**
 * Api istek methodlarını tutan sınıf
 *
 * @return GCARGO_Api_Requests
 */
function gcargo_api_requests() {
	return new GCARGO_Api_Requests();
}

/**
 * Sipariş alanlarını eşleştiren sınıf.
 *
 * @param string|int $order_id Sipariş no.
 * @return GCARGO_Map_WC_Order
 */
function gcargo_map_wc_order( $order_id ) {
	return new GCARGO_Map_WC_Order( $order_id );
}

/**
 * Toplu işlemleri yönetim sınıfı.
 */
function gcargo_bulk_actions() {
	return new GCARGO_Bulk_Actions();
}

/**
 * Admin bildirimleri yönetim sınıfı.
 */
function gcargo_admin_notices() {
	return new GCARGO_Admin_Notices();
}
