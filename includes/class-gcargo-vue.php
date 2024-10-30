<?php // phpcs:ignoreFile
/**
 * GurmeCargo için Vue kullanımını sağlayan sınıf olan GPOS_Vue sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * Eklenti için Vue kullanımını sağlayan sınıf
 */
class GCARGO_Vue {

	/**
	 * Vue instance id
	 *
	 * @var string $id
	 */
	public $id = GCARGO_PREFIX;

	/**
	 * Eklenti versiyonu
	 *
	 * @var string $version
	 */
	protected $version = GCARGO_VERSION;

	/**
	 * Ana Vue dizini
	 *
	 * @var string $vue_path
	 */
	protected $vue_path = 'src';

	/**
	 * Dahil edilecek Vue sayfasını temsil eder
	 *
	 * @var string $vue_page
	 */
	protected $vue_page = '';

	/**
	 * Windowdaki veriye ulaşılaak anahtar.
	 *
	 * @var string $vue_page
	 */
	protected $localize_key = 'gcargo';

	/**
	 * Vue içerisinde kullanılacak window değişkenlerini taşır.
	 *
	 * @var array $localize_variables
	 */
	protected $localize_variables = array();

	/**
	 * Eklenti dosyalarının bulunduğu dizinin klasör yolu.
	 *
	 * @var string $plugin_dir_path
	 */
	protected $plugin_dir_path = GCARGO_PLUGIN_DIR_PATH; // @phpstan-ignore-line

	/**
	 * Eklenti asset dosyalarının bulunduğu dizinin klasör linki.
	 *
	 * @var string $asset_dir_url
	 */
	protected $asset_dir_url = GCARGO_ASSETS_DIR_URL;

	/**
	 * Dahil edilmesi istenen Vue sayfasını ayarlar.
	 *
	 * @param string $page dashboard, woocommerce-settings vb.
	 *
	 * @return GPOS_Vue $this
	 */
	public function set_vue_page( string $page ) {
		$this->vue_page = $page;
		return $this;
	}

	/**
	 * Ana Vue dizinini ayarlar.
	 *
	 * @param string $path src, vue vb.
	 *
	 * @return GPOS_Vue $this
	 */
	public function set_vue_path( string $path ) {
		$this->vue_path = $path;
		return $this;
	}

	/**
	 * Dahil edilecek javascript dosyasında kullanılmak istenen
	 * window değişkenlerini ayarlar.
	 *
	 * @param mixed  $variables window.$localize_key.$variables Şeklinde kullanılır.
	 * @param string $localize_key window.$localize_key.$variables Şeklinde kullanılır.
	 *
	 * @return GPOS_Vue $this
	 */
	public function set_localize( $variables, $localize_key = 'gcargo' ) {
		$this->localize_key       = $localize_key;
		$this->localize_variables = $variables;
		return $this;
	}

	/**
	 * Vue aplikasyonu için idsi app olan divi oluşturur.
	 *
	 * @return GPOS_Vue $this
	 */
	public function create_app_div() {
		gcargo_get_view( 'vue-app-div.php' );
		return $this;
	}

	/**
	 * Vue projesinin gösterimi için gereki js dosyalarını dahil eder.
	 *
	 * @return GPOS_Vue $this
	 */
	public function require_script() {
		wp_enqueue_script(
			$this->id,
			"{$this->asset_dir_url}/vue/js/{$this->vue_page}-{$this->replaced_version()}.js",
			array( 'jquery' ),
			$this->version,
			false
		);

		if ( ! empty( $this->localize_variables ) ) {
			// @phpstan-ignore-next-line
			@wp_localize_script( $this->id, $this->localize_key, (object) $this->localize_variables );  // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		return $this;
	}

	/**
	 * Vue projesinin gösterimi için gereki css dosyalarını dahil eder.
	 *
	 * @param string|bool $css_file Çağrılacak css dosyası
	 *
	 * @return GPOS_Vue $this
	 */
	public function require_style( $css_file = 'admin-app' ) {
		wp_enqueue_style(
			$this->vue_page,
			"{$this->asset_dir_url}/vue/css/{$css_file}-{$this->replaced_version()}.css",
			array(),
			$this->version,
		);
		return $this;
	}

	/**
	 * Cache uygulamalarını engellemek için kullanılır.
	 */
	public function replaced_version() {
		return str_replace( '.', '-', $this->version );
	}
}
