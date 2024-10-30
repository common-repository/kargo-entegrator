<?php
/**
 * GCARGO_WordPress sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * Bu sınıf eklenti aktif olur olmaz çalışmaya başlar ve
 * kurucu fonksiyonu içerisindeki WordPress kancalarına tutunur.
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class GCARGO_WordPress {

	/**
	 * Eklenti Prefix
	 *
	 * @var string $prefix
	 */
	protected $prefix = GCARGO_PREFIX;

	/**
	 * SaaStan gelen durum güncellemelerini karşılar.
	 *
	 * @var string $notification_endpoint
	 */
	private $notification_endpoint = 'gcargo_notification';

	/**
	 * GCARGO_WordPress kurucu fonksiyonu
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( gcargo_admin(), 'admin_menu' ) );
		add_action( 'admin_notices', array( gcargo_admin_notices(), 'render' ) );
		add_action( 'add_meta_boxes', array( gcargo_meta_boxes(), 'add_meta_box' ) );

		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		add_filter( 'template_include', array( $this, 'template_include' ) );
		add_filter( 'script_loader_tag', array( $this, 'script_loader_tag' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_to_shop_order' ) );
		add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions_to_shop_order' ) );
		add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'shop_order_bulk_actions_handler' ), 10, 3 );
		add_filter( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'shop_order_bulk_actions_handler' ), 10, 3 );
		add_filter( 'plugin_action_links_' . GCARGO_PLUGIN_BASENAME, array( $this, 'actions_links' ) );
	}

	/**
	 * WordPress init kancası
	 *
	 * @return void
	 */
	public function init() {
		gcargo_woocommerce_order_status()->create_wc_order_status();

		$locale = determine_locale();
		unload_textdomain( 'kargo-entegrator' );
		load_textdomain( 'kargo-entegrator', GCARGO_PLUGIN_DIR_PATH . 'languages/kargo-entegrator-' . $locale . '.mo' );
		load_plugin_textdomain( 'kargo-entegrator', false, plugin_basename( dirname( GCARGO_PLUGIN_BASEFILE ) ) . '/languages' );

		add_rewrite_rule( "^{$this->prefix}-notification", 'index.php?' . $this->notification_endpoint . '=1', 'top' );
		flush_rewrite_rules();
	}

	/**
	 * WordPress sorgu parametreleri
	 *
	 * @param array $vars Parametreler.
	 *
	 * @return array $vars Parametreler
	 */
	public function query_vars( $vars ) {
		$vars[] = $this->notification_endpoint;
		return $vars;
	}

	/**
	 * WordPress şablon yükleme
	 *
	 * @param mixed $template Şablon.
	 *
	 * @return mixed|void $template Şablon.
	 *
	 * @SuppressWarnings(PHPMD.ExitExpression)
	 */
	public function template_include( $template ) {
		// Geri dönüş noktası için kullanılacak blok.
		if ( get_query_var( $this->notification_endpoint ) ) {

			$post_data = gcargo_clean( json_decode( file_get_contents( 'php://input' ), true ) );
			$hash      = hash_hmac( 'sha512', "{$post_data['shipment_id']}{$post_data['status']}{$post_data['time']}", gcargo_api_key()->get() );

			if ( $post_data['hash'] === $hash ) {
				do_action( "{$this->prefix}_shipment_notification_request_completed", $post_data );
				echo 'OK';
				exit;
			}
			header( 'HTTP/1.1 401 Unauthorized' );
			exit;
		}

		return $template;
	}


	/**
	 * plugins_loaded.
	 *
	 * @return void
	 */
	public function plugins_loaded() {
			require_once GCARGO_PLUGIN_DIR_PATH . 'hooks/class-gcargo-woocommerce.php';
			new GCARGO_WooCommerce();
	}

	/**
	 * script_loader_tag.
	 *
	 * @param string $tag HTML tag
	 * @param string $handle Script id
	 *
	 * @return string $tag
	 */
	public function script_loader_tag( $tag, $handle ) {
		$handlers = array( $this->prefix );
		if ( in_array( $handle, $handlers, true ) ) {
			$tag = str_replace( 'id=\'' . $handle . '-js\'', 'type="module" id=\'' . $handle . '-js\'', $tag );
			$tag = str_replace( 'id="' . $handle . '-js"', 'type="module" id="' . $handle . '-js"', $tag );
			$tag = str_replace( 'text/javascript', 'module', $tag );
		}
		return $tag;
	}

	/**
	 * WordPress admin script kancası
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( $this->prefix . '-admin', GCARGO_ASSETS_DIR_URL . '/js/admin.js', array( 'jquery' ), GCARGO_VERSION, true );
		wp_localize_script(
			$this->prefix . '-admin',
			$this->prefix,
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce(),
			)
		);
	}

	/**
	 * shop_order post tipinin toplu işlemlerini yöneten filtre.
	 *
	 * bulk_actions-edit-shop_order
	 *
	 * @param array $actions Toplu işlemler.
	 *
	 * @return array $actions Toplu işlemler.
	 */
	public function add_bulk_actions_to_shop_order( $actions ) {
		unset( $actions['trash'] );
		$actions = array_merge(
			$actions,
			array(
				'gcargo_create_bulk_shipments' => __( 'Kargo Entegratör -> Gönderi oluştur', 'kargo-entegrator' ),
				'gcargo_print_bulk_shipments'  => __( 'Kargo Entegratör -> Gönderi barkodlarını yazdır', 'kargo-entegrator' ),
				'trash'                        => __( 'Move to Trash' ),
			)
		);
		return $actions;
	}

	/**
	 * shop_order post tipinin toplu işlemlerinin çalıştıracağı aksiyonları yöneten filtre.
	 *
	 * @param string $sendback Geri dönüş URL.
	 * @param string $doaction İşlem adı.
	 * @param array  $items İşlenen postlar.
	 *
	 * @return string $sendback Geri dönüş URL.
	 */
	public function shop_order_bulk_actions_handler( $sendback, $doaction, $items ) {

		switch ( $doaction ) {
			case 'gcargo_create_bulk_shipments':
				$sendback = gcargo_bulk_actions()->create_bulk_shipments( $items );
				break;

		}
		return $sendback;
	}

		/**
		 * Eklentiler sayfasına ayarlar linki ekleme.
		 *
		 * @param array $links Varolan linkler.
		 *
		 * @return array
		 */
	public function actions_links( $links ) {

		$new_links = array(
			'settings' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=gcargo-settings' ), __( 'Settings', 'kargo-entegrator' ) ),
		);

		return array_merge( $links, $new_links );
	}
}
