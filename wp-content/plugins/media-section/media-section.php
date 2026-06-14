<?php
/**
 * Plugin Name: Media Section
 * Plugin URI:  https://github.com/yugyurjevna/wp
 * Description: Добавляет новый раздел «Медиа», который полностью копирует функционал блога: та же вёрстка страниц постов (через шаблоны темы) и те же функции создания статей (редактор, рубрики, метки, изображение записи, комментарии, Brizy).
 * Version:     1.0.0
 * Author:      konard
 * License:     GPL-2.0-or-later
 * Text Domain: media-section
 *
 * @package MediaSection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Post type key for the new "Media" section.
 *
 * It intentionally mirrors the built-in "post" type so the section behaves
 * exactly like the blog (same editor, same single/archive page layout via the
 * theme template hierarchy fall-back to single.php / archive.php).
 */
define( 'MEDIA_SECTION_POST_TYPE', 'media' );

/**
 * Hierarchical taxonomy that mirrors blog "categories" (Рубрики) for media.
 */
define( 'MEDIA_SECTION_CATEGORY', 'media_category' );

/**
 * Non-hierarchical taxonomy that mirrors blog "tags" (Метки) for media.
 */
define( 'MEDIA_SECTION_TAG', 'media_tag' );

/**
 * Register the "Media" custom post type.
 *
 * The supports list and arguments are deliberately copied from the WordPress
 * built-in "post" type so creating a media item gives the same experience as
 * writing a blog article.
 *
 * @return void
 */
function media_section_register_post_type() {
	$labels = array(
		'name'                  => _x( 'Медиа', 'Post type general name', 'media-section' ),
		'singular_name'         => _x( 'Медиа', 'Post type singular name', 'media-section' ),
		'menu_name'             => _x( 'Медиа', 'Admin Menu text', 'media-section' ),
		'name_admin_bar'        => _x( 'Медиа', 'Add New on Toolbar', 'media-section' ),
		'add_new'               => __( 'Добавить новую', 'media-section' ),
		'add_new_item'          => __( 'Добавить новую запись', 'media-section' ),
		'new_item'              => __( 'Новая запись', 'media-section' ),
		'edit_item'             => __( 'Редактировать запись', 'media-section' ),
		'view_item'             => __( 'Просмотреть запись', 'media-section' ),
		'view_items'            => __( 'Просмотреть медиа', 'media-section' ),
		'all_items'             => __( 'Все записи', 'media-section' ),
		'search_items'          => __( 'Искать в медиа', 'media-section' ),
		'parent_item_colon'     => __( 'Родительская запись:', 'media-section' ),
		'not_found'             => __( 'Записи не найдены.', 'media-section' ),
		'not_found_in_trash'    => __( 'В корзине записи не найдены.', 'media-section' ),
		'featured_image'        => __( 'Изображение записи', 'media-section' ),
		'set_featured_image'    => __( 'Задать изображение записи', 'media-section' ),
		'remove_featured_image' => __( 'Удалить изображение записи', 'media-section' ),
		'use_featured_image'    => __( 'Использовать как изображение записи', 'media-section' ),
		'archives'              => __( 'Архив медиа', 'media-section' ),
		'insert_into_item'      => __( 'Вставить в запись', 'media-section' ),
		'uploaded_to_this_item' => __( 'Загружено в эту запись', 'media-section' ),
		'filter_items_list'     => __( 'Фильтровать список записей', 'media-section' ),
		'items_list_navigation' => __( 'Навигация по списку записей', 'media-section' ),
		'items_list'            => __( 'Список записей', 'media-section' ),
	);

	$args = array(
		'labels'             => $labels,
		'description'        => __( 'Раздел «Медиа», копирующий функционал блога.', 'media-section' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => true,
		'show_in_admin_bar'  => true,
		// Expose to the REST API so the block editor (Gutenberg) works just like posts.
		'show_in_rest'       => true,
		'query_var'          => true,
		'rewrite'            => array(
			'slug'       => 'media',
			'with_front' => true,
		),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 5, // Right after "Записи" (Posts).
		'menu_icon'          => 'dashicons-format-video',
		// Same feature set as the built-in "post" type.
		'supports'           => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'trackbacks',
			'custom-fields',
			'comments',
			'revisions',
			'post-formats',
		),
		'taxonomies'         => array( MEDIA_SECTION_CATEGORY, MEDIA_SECTION_TAG ),
	);

	register_post_type( MEDIA_SECTION_POST_TYPE, $args );
}
add_action( 'init', 'media_section_register_post_type' );

/**
 * Register the media-specific taxonomies that mirror blog categories and tags.
 *
 * Dedicated taxonomies keep media content independent from the blog while
 * providing the exact same categorisation features.
 *
 * @return void
 */
function media_section_register_taxonomies() {
	// Hierarchical taxonomy (like blog categories / Рубрики).
	register_taxonomy(
		MEDIA_SECTION_CATEGORY,
		array( MEDIA_SECTION_POST_TYPE ),
		array(
			'labels'            => array(
				'name'              => _x( 'Рубрики медиа', 'taxonomy general name', 'media-section' ),
				'singular_name'     => _x( 'Рубрика медиа', 'taxonomy singular name', 'media-section' ),
				'search_items'      => __( 'Искать рубрики', 'media-section' ),
				'all_items'         => __( 'Все рубрики', 'media-section' ),
				'parent_item'       => __( 'Родительская рубрика', 'media-section' ),
				'parent_item_colon' => __( 'Родительская рубрика:', 'media-section' ),
				'edit_item'         => __( 'Редактировать рубрику', 'media-section' ),
				'update_item'       => __( 'Обновить рубрику', 'media-section' ),
				'add_new_item'      => __( 'Добавить новую рубрику', 'media-section' ),
				'new_item_name'     => __( 'Название новой рубрики', 'media-section' ),
				'menu_name'         => __( 'Рубрики', 'media-section' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'media-category' ),
		)
	);

	// Non-hierarchical taxonomy (like blog tags / Метки).
	register_taxonomy(
		MEDIA_SECTION_TAG,
		array( MEDIA_SECTION_POST_TYPE ),
		array(
			'labels'            => array(
				'name'              => _x( 'Метки медиа', 'taxonomy general name', 'media-section' ),
				'singular_name'     => _x( 'Метка медиа', 'taxonomy singular name', 'media-section' ),
				'search_items'      => __( 'Искать метки', 'media-section' ),
				'all_items'         => __( 'Все метки', 'media-section' ),
				'edit_item'         => __( 'Редактировать метку', 'media-section' ),
				'update_item'       => __( 'Обновить метку', 'media-section' ),
				'add_new_item'      => __( 'Добавить новую метку', 'media-section' ),
				'new_item_name'     => __( 'Название новой метки', 'media-section' ),
				'menu_name'         => __( 'Метки', 'media-section' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'media-tag' ),
		)
	);
}
add_action( 'init', 'media_section_register_taxonomies' );

/**
 * Allow editing media items with the Brizy page builder, exactly like blog posts.
 *
 * The blog's single post layout is rendered through a Brizy template in the
 * active theme, so media items need Brizy support to share the same creation
 * functions and page layout.
 *
 * @param array $post_types Currently supported Brizy post types.
 * @return array
 */
function media_section_brizy_support( $post_types ) {
	if ( ! is_array( $post_types ) ) {
		$post_types = (array) $post_types;
	}

	if ( ! in_array( MEDIA_SECTION_POST_TYPE, $post_types, true ) ) {
		$post_types[] = MEDIA_SECTION_POST_TYPE;
	}

	return $post_types;
}
add_filter( 'brizy_supported_post_types', 'media_section_brizy_support' );

/**
 * Flush rewrite rules on activation so the /media/ permalinks work immediately.
 *
 * @return void
 */
function media_section_activate() {
	media_section_register_taxonomies();
	media_section_register_post_type();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'media_section_activate' );

/**
 * Clean up rewrite rules on deactivation.
 *
 * @return void
 */
function media_section_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'media_section_deactivate' );
