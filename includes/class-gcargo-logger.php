<?php
/**
 * GurmeCargo ile ödeme geçitlerine atılacak istekleri organize eder.
 *
 * @package GurmeHub
 */

/**
 * GurmeCargo istek sınıfı
 */
class GCARGO_Logger {

	/**
	 * Filesystem.
	 *
	 * @var WP_Filesystem_Base
	 */
	protected $wp_filesystem;

	/**
	 * Target file.
	 *
	 * @var string
	 */
	protected $log_file;


	/**
	 * Kurucu method
	 *
	 * @param string $file Log dosyası.
	 */
	public function __construct( $file ) {
		try {
			global $wp_filesystem;
			if ( ! $wp_filesystem ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			$this->wp_filesystem = $wp_filesystem;
			$base_dir            = wp_upload_dir()['basedir'];
			$base_folder         = "{$base_dir}/kargo-entegrator";
			if ( ! $this->wp_filesystem->is_dir( $base_folder ) ) {
				$this->wp_filesystem->mkdir( $base_folder, FS_CHMOD_DIR );
			}
			$this->log_file = "{$base_folder}/{$file}.log";
		} catch ( Exception $e ) {
			/**
			 * TODO: Log dosyası oluşturulamadı.
			 */
		}
	}

	/**
	 * Log ekler.
	 *
	 * @param string $message Log mesajı.
	 */
	public function add_log( string $message ) {
		$date     = current_datetime()->format( 'Y-m-d H:i:s' );
		$message  = "{$date} {$message}\n";
		$content  = $this->get_logs();
		$message .= $content;
		$this->wp_filesystem->put_contents( $this->log_file, $message );
	}

	/**
	 * Logları getirir.
	 */
	public function get_logs() {
		return $this->wp_filesystem->get_contents( $this->log_file );
	}

	/**
	 * Logları temizler.
	 */
	public function clear_logs() {
		$this->wp_filesystem->put_contents( $this->log_file, '' );
	}
}
