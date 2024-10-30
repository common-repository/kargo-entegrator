<?php
/**
 * Kargo hesapları kaynak sınıfını barındırır.
 *
 * @package GurmeHub
 */

/**
 *  Kargo hesapları kaynak sınıfı
 */
class GCARGO_Shipments extends GCARGO_Resource {

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $resource = 'shipment';

	/**
	 * {@inheritDoc}
	 *
	 * @var string
	 */
	protected $api_path = 'shipments';
}
