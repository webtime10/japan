<?php
/**
 * Dashboard controller.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FCC_Dashboard_Controller extends FCC_Controller {

	public function index() {
		$groups = fcc_get_group_types();
		$counts = array();

		foreach ( $groups as $group => $label ) {
			$model            = new FCC_Category_Model( $group );
			$counts[ $group ] = $model->count_list( fcc_get_default_language_id() );
		}

		$this->render(
			'dashboard/home',
			array(
				'title'  => __( 'Family Comfort Calc', 'family-comfort-calc' ),
				'groups' => $groups,
				'counts' => $counts,
			)
		);
	}
}
