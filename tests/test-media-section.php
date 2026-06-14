<?php
/**
 * Lightweight, dependency-free unit test for the Media Section plugin.
 *
 * It stubs the handful of WordPress functions the plugin uses, loads the
 * plugin, fires the registered `init` hooks and asserts that the new "media"
 * section mirrors the built-in blog "post" type (same supports, REST enabled,
 * own category/tag taxonomies) and that Brizy editing is enabled for it.
 *
 * Run with:  php tests/test-media-section.php
 *
 * @package MediaSection
 */

// ---------------------------------------------------------------------------
// Minimal WordPress stubs.
// ---------------------------------------------------------------------------

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['__actions']       = array();
$GLOBALS['__filters']       = array();
$GLOBALS['__post_types']    = array();
$GLOBALS['__taxonomies']    = array();
$GLOBALS['__flush_calls']   = 0;
$GLOBALS['__activation_cb']  = null;
$GLOBALS['__deactivation_cb'] = null;

function add_action( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['__actions'][ $hook ][] = $cb;
}

function add_filter( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['__filters'][ $hook ][] = $cb;
}

function apply_filters( $hook, $value ) {
	if ( ! empty( $GLOBALS['__filters'][ $hook ] ) ) {
		foreach ( $GLOBALS['__filters'][ $hook ] as $cb ) {
			$value = call_user_func( $cb, $value );
		}
	}
	return $value;
}

function register_post_type( $key, $args ) {
	$GLOBALS['__post_types'][ $key ] = $args;
}

function register_taxonomy( $key, $object_type, $args ) {
	$GLOBALS['__taxonomies'][ $key ] = array(
		'object_type' => (array) $object_type,
		'args'        => $args,
	);
}

function register_activation_hook( $file, $cb ) {
	$GLOBALS['__activation_cb'] = $cb;
}

function register_deactivation_hook( $file, $cb ) {
	$GLOBALS['__deactivation_cb'] = $cb;
}

function flush_rewrite_rules() {
	$GLOBALS['__flush_calls']++;
}

function __( $text, $domain = 'default' ) {
	return $text;
}

function _x( $text, $context, $domain = 'default' ) {
	return $text;
}

// ---------------------------------------------------------------------------
// Tiny assertion helpers.
// ---------------------------------------------------------------------------

$GLOBALS['__pass'] = 0;
$GLOBALS['__fail'] = 0;

function check( $condition, $message ) {
	if ( $condition ) {
		$GLOBALS['__pass']++;
		echo "  PASS: {$message}\n";
	} else {
		$GLOBALS['__fail']++;
		echo "  FAIL: {$message}\n";
	}
}

function fire( $hook ) {
	if ( ! empty( $GLOBALS['__actions'][ $hook ] ) ) {
		foreach ( $GLOBALS['__actions'][ $hook ] as $cb ) {
			call_user_func( $cb );
		}
	}
}

// ---------------------------------------------------------------------------
// Load the plugin and run the lifecycle.
// ---------------------------------------------------------------------------

require __DIR__ . '/../wp-content/plugins/media-section/media-section.php';

// Simulate the WordPress `init` action firing.
fire( 'init' );

echo "Custom post type registration:\n";
check( isset( $GLOBALS['__post_types']['media'] ), "'media' post type is registered" );

$media = isset( $GLOBALS['__post_types']['media'] ) ? $GLOBALS['__post_types']['media'] : array();

check( ! empty( $media['public'] ), 'media is public' );
check( ! empty( $media['has_archive'] ), 'media has an archive (mirrors blog archive)' );
check( ! empty( $media['show_in_rest'] ), 'media is shown in REST (same block editor as posts)' );

// The supports list must mirror the built-in "post" type so the article
// creation experience is identical.
$expected_supports = array(
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
);
$actual_supports = isset( $media['supports'] ) ? $media['supports'] : array();
sort( $expected_supports );
sort( $actual_supports );
check( $expected_supports === $actual_supports, 'media supports mirror the blog post supports' );

$slug = isset( $media['rewrite']['slug'] ) ? $media['rewrite']['slug'] : null;
check( 'media' === $slug, "media uses the '/media/' permalink slug" );

echo "\nTaxonomy registration:\n";
check( isset( $GLOBALS['__taxonomies']['media_category'] ), 'media_category taxonomy registered (mirrors categories)' );
check( isset( $GLOBALS['__taxonomies']['media_tag'] ), 'media_tag taxonomy registered (mirrors tags)' );

if ( isset( $GLOBALS['__taxonomies']['media_category'] ) ) {
	check(
		! empty( $GLOBALS['__taxonomies']['media_category']['args']['hierarchical'] ),
		'media_category is hierarchical like blog categories'
	);
	check(
		in_array( 'media', $GLOBALS['__taxonomies']['media_category']['object_type'], true ),
		'media_category is attached to the media post type'
	);
}

if ( isset( $GLOBALS['__taxonomies']['media_tag'] ) ) {
	check(
		empty( $GLOBALS['__taxonomies']['media_tag']['args']['hierarchical'] ),
		'media_tag is non-hierarchical like blog tags'
	);
}

echo "\nBrizy editor support:\n";
$brizy_types = apply_filters( 'brizy_supported_post_types', array( 'page', 'post' ) );
check( in_array( 'media', $brizy_types, true ), 'media is added to Brizy supported post types' );
check( in_array( 'post', $brizy_types, true ), 'existing Brizy post types are preserved' );

echo "\nActivation lifecycle:\n";
check( is_callable( $GLOBALS['__activation_cb'] ), 'activation hook is registered' );
if ( is_callable( $GLOBALS['__activation_cb'] ) ) {
	call_user_func( $GLOBALS['__activation_cb'] );
	check( $GLOBALS['__flush_calls'] >= 1, 'activation flushes rewrite rules for /media/ permalinks' );
}

// ---------------------------------------------------------------------------
// Summary.
// ---------------------------------------------------------------------------

echo "\n----------------------------------------\n";
echo "Passed: {$GLOBALS['__pass']}  Failed: {$GLOBALS['__fail']}\n";

exit( $GLOBALS['__fail'] > 0 ? 1 : 0 );
