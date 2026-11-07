<?php
$cardsSection = $section;
$cardsSection['id'] = 'certifications';
$args = ['section' => $cardsSection, 'post_id' => isset($args['post_id']) ? absint($args['post_id']) : 0];
get_template_part('template-parts/sections/cards-grid', null, $args);
