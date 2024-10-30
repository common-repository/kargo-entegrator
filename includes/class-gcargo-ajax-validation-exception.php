<?php
/**
 * GurmeCargo ajax hatalarında fırlatılacak olan sınıf.
 *
 * @package GurmeHub
 */

/**
 * Ajax Exception sınıfı
 */
class GCARGO_Ajax_Validation_Exception extends Exception {

	/**
	 * Validasyon hataları
	 *
	 * @var array $validations Hata mesajı.
	 */
	protected $validation_errors = array();

	/**
	 * Validasyon hataları atama
	 *
	 * @param array $value Hatalar.
	 */
	public function set_validation_errors( array $value ) {
		$this->validation_errors = $value;
	}

	/**
	 * Validasyon hataları getirme
	 *
	 * @return array  Hatalar.
	 */
	public function get_validation_errors() {
		return $this->validation_errors;
	}
}
