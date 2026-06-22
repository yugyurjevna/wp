<?php
/**
 * Lightweight, dependency-free unit test for the Media Section article seeder.
 *
 * It stubs the WordPress functions the seeder uses, runs the seeding routine and
 * asserts that the five articles requested in issue #3 are created in the
 * `media` section, that they carry the required topics (Юлия Голубкова,
 * обучение ИИ, реклама «Цифровых структур»), and that seeding is idempotent.
 *
 * Run with:  php tests/test-media-articles.php
 *
 * @package MediaSection
 */

// ---------------------------------------------------------------------------
// Minimal WordPress stubs.
// ---------------------------------------------------------------------------

define( 'ABSPATH', __DIR__ . '/' );

// Constants the seeder relies on from the main plugin file.
define( 'MEDIA_SECTION_POST_TYPE', 'media' );
define( 'MEDIA_SECTION_CATEGORY', 'media_category' );

$GLOBALS['__actions']   = array();
$GLOBALS['__filters']   = array();
$GLOBALS['__options']   = array();
$GLOBALS['__posts']     = array(); // id => postarr
$GLOBALS['__postmeta']  = array(); // id => [key => value]
$GLOBALS['__terms']     = array(); // taxonomy => [name => term_id]
$GLOBALS['__objterms']  = array(); // id => [term_id, ...]
$GLOBALS['__next_id']   = 1;
$GLOBALS['__next_term'] = 1;

function add_action( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['__actions'][ $hook ][] = $cb;
}

function add_filter( $hook, $cb, $priority = 10, $args = 1 ) {
	$GLOBALS['__filters'][ $hook ][] = $cb;
}

function get_option( $name, $default = false ) {
	return isset( $GLOBALS['__options'][ $name ] ) ? $GLOBALS['__options'][ $name ] : $default;
}

function update_option( $name, $value ) {
	$GLOBALS['__options'][ $name ] = $value;
	return true;
}

function is_wp_error( $thing ) {
	return $thing instanceof WP_Error;
}

class WP_Error {
	public $message;
	public function __construct( $code = '', $message = '' ) {
		$this->message = $message;
	}
}

function wp_insert_post( $postarr, $wp_error = false ) {
	$id = $GLOBALS['__next_id']++;
	$GLOBALS['__posts'][ $id ] = $postarr;
	return $id;
}

function update_post_meta( $post_id, $key, $value ) {
	$GLOBALS['__postmeta'][ $post_id ][ $key ] = $value;
	return true;
}

/**
 * Minimal get_posts() supporting the meta_key/meta_value lookup the seeder uses.
 */
function get_posts( $args ) {
	$found = array();
	$key   = isset( $args['meta_key'] ) ? $args['meta_key'] : null;
	$value = isset( $args['meta_value'] ) ? $args['meta_value'] : null;

	foreach ( $GLOBALS['__postmeta'] as $id => $meta ) {
		if ( $key && isset( $meta[ $key ] ) && $meta[ $key ] === $value ) {
			$found[] = $id;
		}
	}

	return $found;
}

function term_exists( $term, $taxonomy = '' ) {
	if ( isset( $GLOBALS['__terms'][ $taxonomy ][ $term ] ) ) {
		return array( 'term_id' => $GLOBALS['__terms'][ $taxonomy ][ $term ] );
	}
	return null;
}

function wp_insert_term( $term, $taxonomy, $args = array() ) {
	$id = $GLOBALS['__next_term']++;
	$GLOBALS['__terms'][ $taxonomy ][ $term ] = $id;
	return array( 'term_id' => $id );
}

function wp_set_object_terms( $object_id, $terms, $taxonomy, $append = false ) {
	$GLOBALS['__objterms'][ $object_id ] = (array) $terms;
	return (array) $terms;
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

// ---------------------------------------------------------------------------
// Load the seeder and run it.
// ---------------------------------------------------------------------------

require __DIR__ . '/../wp-content/plugins/media-section/media-section-articles.php';

echo "Seed data definition:\n";
$data = media_section_seed_articles_data();
check( is_array( $data ) && count( $data ) === 5, 'exactly 5 articles are defined' );

$slugs = array_map( function ( $a ) { return $a['slug']; }, $data );
check( count( array_unique( $slugs ) ) === 5, 'all article slugs are unique' );

echo "\nFirst seeding run:\n";
$created = media_section_seed_articles();
check( 5 === $created, '5 media articles are created on first run' );
check( 5 === count( $GLOBALS['__posts'] ), '5 posts stored' );

// Every created post must belong to the media section.
$all_media = true;
foreach ( $GLOBALS['__posts'] as $post ) {
	if ( 'media' !== $post['post_type'] ) {
		$all_media = false;
	}
}
check( $all_media, 'every seeded article uses the "media" post type (same layout as blog)' );

$all_published = true;
foreach ( $GLOBALS['__posts'] as $post ) {
	if ( 'publish' !== $post['post_status'] ) {
		$all_published = false;
	}
}
check( $all_published, 'every seeded article is published' );

// Each article must carry the seed meta marker.
check( 5 === count( $GLOBALS['__postmeta'] ), 'every seeded article carries the seed meta marker' );

// Each article must be assigned to a media category.
check( 5 === count( $GLOBALS['__objterms'] ), 'every seeded article is assigned a media category' );

echo "\nRequired topics are covered:\n";
$all_content = '';
foreach ( $GLOBALS['__posts'] as $post ) {
	$all_content .= ' ' . $post['post_title'] . ' ' . $post['post_content'];
}
check( false !== mb_strpos( $all_content, 'Юлия Голубкова' ), 'an article about Юлия Голубкова exists' );
check( false !== mb_strpos( $all_content, 'Обучение ИИ' ) || false !== mb_strpos( $all_content, 'обучен' ), 'an article about обучение ИИ exists' );

$ads = 0;
foreach ( $GLOBALS['__posts'] as $post ) {
	if ( false !== mb_strpos( $post['post_title'] . $post['post_content'], 'Цифровые структуры' )
		|| false !== mb_strpos( $post['post_title'] . $post['post_content'], 'Цифровых структур' ) ) {
		$ads++;
	}
}
check( $ads >= 3, 'at least 3 advertising articles mention «Цифровые структуры»' );

echo "\nIdempotency:\n";
$created_again = media_section_seed_articles();
check( 0 === $created_again, 'running the seeder again creates no duplicates' );
check( 5 === count( $GLOBALS['__posts'] ), 'still exactly 5 posts after a second run' );

echo "\nActivation hook wiring:\n";
check( isset( $GLOBALS['__actions']['init'] ), 'seeder is hooked on init' );

// ---------------------------------------------------------------------------
// Summary.
// ---------------------------------------------------------------------------

echo "\n----------------------------------------\n";
echo "Passed: {$GLOBALS['__pass']}  Failed: {$GLOBALS['__fail']}\n";

exit( $GLOBALS['__fail'] > 0 ? 1 : 0 );
