<?php
/**
 * GurmeCargo WP admin bildirimlerini organize eden sınıfı barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * WP admin bildirimlerini organize eden sınıf.
 */
class GCARGO_Admin_Notices {
	/**
	 * Bildirimlerin tutulduğu veri tabanı anahtarı.
	 *
	 * @var string
	 */
	public $notices_key = 'gcargo_admin_notices';

	/**
	 * Bildirimleri getirir.
	 *
	 * @return array
	 */
	public function get_notices() {
		return get_option( $this->notices_key, array() );
	}

	/**
	 * Bildirimleri kayıt eder.
	 *
	 * @param string $notice Bildirim mesajı.
	 * @param string $type Bildirimin tipi (error, success, info, warning).
	 *
	 * @return void
	 */
	public function add_notice( $notice, $type = 'error' ) {
		$notices   = $this->get_notices();
		$notices[] = [
			'text' => $notice,
			'args' => array(
				'type' => $type,
			),
		];
		update_option( $this->notices_key, $notices );
	}

	/**
	 * Bildilerimleri resetler.
	 *
	 * @return void
	 */
	public function reset_notices() {
		update_option( $this->notices_key, array() );
	}

	/**
	 * Bildirimleri render eder.
	 *
	 * @return void
	 */
	public function render() {
		$notices = $this->get_notices();
		foreach ( $notices as $notice ) {
			wp_admin_notice( $notice['text'], $notice['args'] );
		}
		$this->reset_notices();
	}
}
