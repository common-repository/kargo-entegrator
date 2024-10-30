<?php //phpcs:ignore
/**
 * Plugin Name: Kargo Entegratör
 * Plugin URI: https://kargoentegrator.com
 * Description: Kolay, Hızlı Entegre Edilebilir WooCommerce Kargo Eklentisi 10’dan fazla kargo firması ile entegre olun.
 * Version: 1.0.35
 * Author: GurmeHub
 * Author URI: https://gurmehub.com
 * Text Domain: kargo-entegrator
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 * WC requires at least: 7.6
 * WC tested up to: 9.3.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package GurmeHub
 */

defined( 'ABSPATH' ) || exit;

/**
 * GurmeCargo eklenti anasınıfı.
 *
 * @package GurmeHub
 */
final class GurmeCargo {

	/**
	 * Eklenti öneki.
	 *
	 * @var string
	 */
	public $prefix = 'gcargo';

	/**
	 * Eklenti versiyonu.
	 *
	 * @var string
	 */
	public $version = '1.0.35';

	/**
	 * Veritabanı versiyonu.
	 *
	 * @var string
	 */
	public $db_version = '1.0.0';

	/**
	 * Sınıfın bir örneği.
	 *
	 * @var GurmeCargo|null
	 */
	protected static $instance;

	/**
	 * Sınıf örneklerini taşır.
	 *
	 * @var array
	 */
	public $container = [];

	/**
	 * GurmeCargo sınıfının bir örneğini türetir.
	 *
	 * @see GurmeCargo()
	 * @return GurmeCargo
	 */
	public static function get() {
		if ( is_null( self::$instance ) || ! ( self::$instance instanceof GurmeCargo ) ) {
			self::$instance = new GurmeCargo();
			self::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Kurulum methodu.
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
	 */
	private function setup() {
		$this->define_constants();

		$this->includes();

		$this->instantiate();
	}

	/**
	 * Eklenti sabitleri tanımlama.
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'GCARGO_PREFIX', $this->prefix );
		define( 'GCARGO_VERSION', $this->version );
		define( 'GCARGO_DB_VERSION', $this->db_version );
		define( 'GCARGO_PRODUCTION', true );
		define( 'GCARGO_PLUGIN_BASEFILE', __FILE__ );
		define( 'GCARGO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'GCARGO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
		define( 'GCARGO_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );
		define( 'GCARGO_ASSETS_DIR_URL', plugin_dir_url( __FILE__ ) . 'assets' );
	}

	/**
	 * Eklenti dosyaları yükleme.
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public function includes() {
		$files = array(
			// Vendors
			'vendor/autoload.php',
			// Abstracts
			'includes/abstracts/abstract-gcargo-settings.php',
			'includes/abstracts/abstract-gcargo-connection.php',
			'includes/abstracts/abstract-gcargo-resource.php',
			'includes/abstracts/abstract-gcargo-options.php',
			// Resources
			'includes/resources/class-gcargo-warehouses.php',
			'includes/resources/class-gcargo-cargo-integrations.php',
			'includes/resources/class-gcargo-shipments.php',
			// Includes
			'includes/class-gcargo-logger.php',
			'includes/class-gcargo-ajax-validation-exception.php',
			'includes/class-gcargo-api-key.php',
			'includes/class-gcargo-other-settings.php',
			'includes/class-gcargo-data-handler.php',
			'includes/class-gcargo-meta-boxes.php',
			'includes/class-gcargo-http-client.php',
			'includes/class-gcargo-api-requests.php',
			'includes/class-gcargo-admin.php',
			'includes/class-gcargo-vue.php',
			'includes/class-gcargo-bulk-actions.php',
			'includes/class-gcargo-admin-notices.php',
			'includes/class-gcargo-map-wc-order.php',
			'includes/class-gcargo-woocommerce-order-status.php',
			// Functions
			'includes/gcargo-class-functions.php',
			'includes/gcargo-functions.php',
			// Hooks
			'hooks/class-gcargo-ajax.php',
			'hooks/class-gcargo-wordpress.php',
			'hooks/class-gcargo-actions.php',
			'hooks/class-gcargo-gph.php',
			'hooks/class-gcargo-woocommerce.php',
		);
		foreach ( $files as $file ) {
			require_once $file;
		}
	}

	/**
	 * Sınıf türetme.
	 */
	public function instantiate() {
		$this->container = array(
			'GCARGO_WordPress' => new GCARGO_WordPress(),
			'GCARGO_Ajax'      => new GCARGO_Ajax(),
			'GCARGO_Actions'   => new GCARGO_Actions(),
			'GCARGO_Gph'       => new GCARGO_Gph(),
		);

		$gurmehub_client = new \GurmeHub\Client( GCARGO_PLUGIN_BASEFILE );
		$gurmehub_client->insights();
	}

	/**
	 * Anasınıfı türetir ve eklentinin çalışmasını başlatır.
	 *
	 * @return GurmeCargo
	 *
	 * @SuppressWarnings(PHPMD.StaticAccess)
	 */
	public static function init() {
			return self::get();
	}
}

// Hadi başlayalım.
GurmeCargo::init();
