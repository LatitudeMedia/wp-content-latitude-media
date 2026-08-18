<?php
// This file is generated. Do not modify it manually.
return array(
	'category-post-listing' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'latitudemedia/category-post-listing',
		'version' => '0.1.0',
		'title' => 'Category Post Listing',
		'category' => 'ltm-common-blocks',
		'icon' => 'list-view',
		'description' => 'List published posts by category, optionally scoped to posts tagged for the current thematic page.',
		'keywords' => array(
			'category',
			'posts',
			'listing',
			'thematic'
		),
		'example' => array(
			
		),
		'attributes' => array(
			'categories' => array(
				'type' => 'array',
				'items' => array(
					'type' => 'number'
				),
				'default' => array(
					
				)
			),
			'onlyThematicPageTagged' => array(
				'type' => 'boolean',
				'default' => true
			),
			'layout' => array(
				'type' => 'string',
				'enum' => array(
					'five-post-feature',
					'paginated-list'
				),
				'default' => 'five-post-feature'
			)
		),
		'supports' => array(
			'html' => false,
			'anchor' => true
		),
		'textdomain' => 'ltm',
		'editorScript' => 'file:./index.js',
		'viewScript' => 'file:./view.js',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php'
	),
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
	'news-type-preview' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'latitudemedia/news-type-preview',
		'version' => '0.1.0',
		'title' => 'News Type Preview',
		'category' => 'ltm-common-blocks',
		'icon' => 'megaphone',
		'description' => 'List published posts by News Type, optionally narrowed to one podcast show.',
		'keywords' => array(
			'news type',
			'podcast',
			'posts',
			'listing'
		),
		'example' => array(
			
		),
		'attributes' => array(
			'newsType' => array(
				'type' => 'string',
				'default' => 'news'
			),
			'podcastId' => array(
				'type' => 'number',
				'default' => 0
			),
			'numberOfPosts' => array(
				'type' => 'number',
				'default' => 5
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
	'subscriber-form' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'latitudemedia/subscriber-form',
		'version' => '0.1.0',
		'title' => 'Subscriber Form',
		'category' => 'ltm-common-blocks',
		'icon' => 'email-alt2',
		'description' => 'Newsletter signup form with a title, disclaimer, and embeddable form code.',
		'keywords' => array(
			'form',
			'subscribe',
			'newsletter',
			'signup',
			'hubspot'
		),
		'example' => array(
			
		),
		'attributes' => array(
			'title' => array(
				'type' => 'string',
				'default' => ''
			),
			'disclaimer' => array(
				'type' => 'string',
				'default' => ''
			),
			'embedCode' => array(
				'type' => 'string',
				'default' => ''
			),
			'layout' => array(
				'type' => 'string',
				'enum' => array(
					'square'
				),
				'default' => 'square'
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
