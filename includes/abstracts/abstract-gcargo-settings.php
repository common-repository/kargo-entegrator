<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * GurmeCargo wp_options tablosu için abstract sınıfını barındırır.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo GCARGO_Settings abstract sınıfı
 */
abstract class GCARGO_Settings {

	/**
	 * Kayıt edilecek ayarları tutacak wp_options tablosundaki option_name
	 *
	 * @var string
	 */
	public $options_table_key;


	/**
	 * Ayarları döndürür
	 *
	 * @return array
	 */
	public function get_settings() {
		$settings = get_option( $this->options_table_key, array() );

		// Varsayılan ayarları yükleme, sonradan ayar eklenmesi durumunda varsayılan olarak değer ataması yapma.
		foreach ( $this->get_default_settings() as $key => $value ) {
			if ( false === array_key_exists( $key, $settings ) ) {
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Anahtarı verilen ayarı döndürür.
	 *
	 * @param string $key Döndürülmesi istenen ayar.
	 *
	 * @return mixed
	 */
	public function get_setting_by_key( $key ) {
		$settings = $this->get_settings();
		return array_key_exists( $key, $settings ) ? $settings[ $key ] : false;
	}

	/**
	 * Ayarlar kayıt eder.
	 *
	 * @param mixed $options Kayıt edilecek veri.
	 *
	 * @return mixed
	 */
	public function set_settings( $options ) {
		update_option( $this->options_table_key, $options );

		if ( method_exists( $this, 'settings_updated' ) ) {
			call_user_func( array( $this, 'settings_updated' ) );
		}

		return true;
	}


	/**
	 * Ön tanımlı ayarları döndürür.
	 *
	 * @return array
	 */
	private function get_default_settings() {
		$default_settings = apply_filters(
			'gcargo_default_settings',
			array()
		);

		return array_key_exists( $this->options_table_key, (array) $default_settings ) ? $default_settings[ $this->options_table_key ] : array();
	}
}
