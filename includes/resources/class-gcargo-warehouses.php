<?php
/**
 * Gönderici adresleri kaynak sınıfını barındırır.
 *
 * @package GurmeHub
 */

/**
 * Gönderici adresleri kaynak sınıfı
 */
class GCARGO_Warehouses extends GCARGO_Resource {

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $resource = 'warehouse';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $api_path = 'settings/warehouses';
}
