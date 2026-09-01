<?php
/**
 * Helpers.
 *
 * @package family-comfort-calc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, string>
 */
function fcc_get_group_types() {
	return array(
		'age'       => __( 'Возраст детей', 'family-comfort-calc' ),
		'interest'  => __( 'Интересы', 'family-comfort-calc' ),
		'direction' => __( 'Направления', 'family-comfort-calc' ),
	);
}

/**
 * @param string $group
 * @return bool
 */
function fcc_is_valid_group( $group ) {
	return isset( fcc_get_group_types()[ $group ] );
}

/**
 * @return array<int, object>
 */
function fcc_get_categories( $group ) {
	if ( ! fcc_is_valid_group( $group ) ) {
		return array();
	}

	require_once FCC_PATH . 'admin/models/class-fcc-category-model.php';
	$model = new FCC_Category_Model( $group );
	return $model->get_list( fcc_get_default_language_id() );
}

/**
 * Один язык на сайте — всегда ID 1.
 *
 * @return int
 */
function fcc_get_default_language_id() {
	return 1;
}
