<?php
/**
 * GCARGO_Gph (Gurmehub Plugin Helper) sınıfını barındıran dosya.
 *
 * @package GurmeHub
 */

/**
 * Bu sınıf GurmeCargo un Gurmehub Plugin Helper'a attığı kancaları taşır.
 */
class GCARGO_Gph {

	/**
	 * GPOS_Gph kurucu fonksiyonu
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'gph_' . GCARGO_PLUGIN_BASENAME . '_deactive_reasons', array( $this, 'deactive_reasons' ) );
		add_filter( 'gph_' . GCARGO_PLUGIN_BASENAME . '_texts', array( $this, 'texts' ) );
	}

	/**
	 * GurmePOS kaldırılma nedenleri kancası.
	 *
	 * @param array $reasons Nedenler.
	 *
	 * @return array
	 */
	public function deactive_reasons( $reasons ) {
		return array_merge(
			$reasons,
			array(
				array(
					'value' => 'integration',
					'label' => __( 'I couldn\'t find the cargo company integration I was looking for (Please share below)', 'kargo-entegrator' ),
				),
				array(
					'value' => 'satisfied',
					'label' => __( 'I am using another shipping plugin', 'kargo-entegrator' ),
				),
				array(
					'value' => 'about',
					// translators: %s is the name of the plugin.
					'label' => sprintf( __( 'I need more information about %s', 'kargo-entegrator' ), 'Kargo Entegratör' ),
				),
				array(
					'value' => 'temporarily',
					'label' => __( 'Temporarily deactivating', 'kargo-entegrator' ),
				),
				array(
					'value' => 'didnt_work',
					'label' => __( 'Didn\'t work as expected', 'kargo-entegrator' ),
				),
				array(
					'value' => 'other',
					'label' => __( 'Other (Please share below)', 'kargo-entegrator' ),
				),
			)
		);
	}

	/**
	 * GurmeCargo kaldırılma nedenleri buton yazıları kancası.
	 *
	 * @param array $texts Buton yazıları.
	 *
	 * @return array
	 */
	public function texts( $texts ) {
		$texts = array(
			'skip_button'      => __( 'Skip & Deactive', 'kargo-entegrator' ),
			'submit_button'    => __( 'Deactive', 'kargo-entegrator' ),
			'cancel_button'    => __( 'Cancel', 'kargo-entegrator' ),
			'reasons_title'    => __( 'Why you are leaving us ?', 'kargo-entegrator' ),
			'comment_title'    => __( 'Comments (Optional)', 'kargo-entegrator' ),
			// translators: %s is the name of the plugin.
			'main_title'       => __( 'No thanks, i don\'t want the %s', 'kargo-entegrator' ),
			'main_description' => __( 'After a step, the plugin will be deactivated. Could you please take a moment and support us to make your app better?', 'kargo-entegrator' ),
		);
		return $texts;
	}
}
