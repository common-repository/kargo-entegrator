<?php
/**
 * Kargo hesapları kaynak sınıfını barındırır.
 *
 * @package GurmeHub
 */

/**
 *  Kargo hesapları kaynak sınıfı
 */
class GCARGO_Cargo_Integrations extends GCARGO_Resource {

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $resource = 'cargo_integrations';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $api_path = 'integration/cargos';
}
