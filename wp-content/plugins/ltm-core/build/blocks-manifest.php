<?php
// This file is generated. Do not modify it manually.
return array(
	'featured-post-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'latitudemedia/featured-post-block',
		'version' => '0.1.0',
		'title' => 'Featured Post',
		'category' => 'ltm-common-blocks',
		'icon' => 'star-filled',
		'description' => 'Highlight a single post as a featured card, optionally scoped to the page\'s sponsor.',
		'keywords' => array(
			'featured',
			'post',
			'sponsor'
		),
		'example' => array(
			
		),
		'attributes' => array(
			'postId' => array(
				'type' => 'number',
				'default' => 0
			),
			'isSponsored' => array(
				'type' => 'boolean',
				'default' => false
			),
			'excludeFromOtherBlocks' => array(
				'type' => 'boolean',
				'default' => true
			)
		),
		'supports' => array(
			'html' => false,
			'anchor' => true
		),
		'textdomain' => 'ltm',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
	'title-block' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'latitudemedia/title-block',
		'version' => '0.1.0',
		'title' => 'Title Block',
		'category' => 'ltm-page-custom-blocks',
		'icon' => 'cover-image',
		'description' => 'Full-bleed page title header for Thematic Pages, with sponsor-aware theming.',
		'keywords' => array(
			'title',
			'hero',
			'thematic page'
		),
		'example' => array(
			
		),
		'attributes' => array(
			'kicker' => array(
				'type' => 'string',
				'default' => ''
			),
			'backgroundImageId' => array(
				'type' => 'number',
				'default' => 0
			),
			'backgroundImageUrl' => array(
				'type' => 'string',
				'default' => ''
			),
			'sponsorLogoOverrideId' => array(
				'type' => 'number',
				'default' => 0
			),
			'sponsorLogoOverrideUrl' => array(
				'type' => 'string',
				'default' => ''
			)
		),
		'supports' => array(
			'html' => false,
			'anchor' => true
		),
		'textdomain' => 'ltm',
		'editorScript' => 'file:./index.js',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	)
);
