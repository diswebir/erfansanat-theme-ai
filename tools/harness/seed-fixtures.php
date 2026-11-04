<?php
/** Deterministic, database-free fixtures for the Phase 3 compatibility gate. */
return array(
	'projects' => array(
		9201 => array( 'post_type' => 'projects', 'title' => 'Urban Square Lighting', 'meta' => array( 'second-image' => '9101', 'foure-image' => '9104', 'finish-pro-time' => '1403/08/12', 'mortabet_mahsole_2' => serialize( array( 0 => 9301 ) ) ) ),
		9202 => array( 'post_type' => 'projects', 'title' => 'Tunnel Lighting', 'meta' => array( 'second-image' => '9102', 'finish-pro-time' => '1402/11/20', 'mortabet_mahsole_2' => '[9302]' ) ),
	),
	'products' => array(
		9301 => array( 'post_type' => 'product', 'title' => 'Dimmable LED Controller', 'purchasable' => true, 'meta' => array( 'true_sale' => 'true', 'product_id' => 'LED-301', 'product-feature' => '{"item-0":{"add-new-feature":"Dimmable"}}', 'galerry_produ' => '9101,9103', 'project-ids' => '[9201]' ) ),
		9302 => array( 'post_type' => 'product', 'title' => 'Custom Tunnel Luminaire', 'purchasable' => false, 'meta' => array( 'true_sale' => 'false', 'english_titl' => 'Tunnel luminaire', 'faq' => serialize( array( 'item-0' => array( 'name_question' => 'Lead time?', 'answer_question' => 'Contact sales.' ) ) ), 'usage_countr' => '[71,72]' ) ),
	),
	'terms' => array(
		71 => array( 'taxonomy' => 'product_cat', 'name' => 'Lighting', 'meta' => array( 'thumbnail' => '9150', 'faq' => '{"item-0":{"name_question":"Warranty?","answer_question":"Two years."}}' ) ),
		72 => array( 'taxonomy' => 'product_cat', 'name' => 'Controls', 'meta' => array( 'thumbnail' => '9151' ) ),
	),
);
