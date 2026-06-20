<?php
/**
 * Seed the «Медиа» section with the articles requested in issue #3.
 *
 * Adds five ready-to-publish articles to the `media` custom post type that
 * mirrors the blog. Because the `media` type uses the very same theme template
 * (single.php → the same Brizy layout + the_content()) as blog posts, these
 * articles get the exact "post page layout" of the blog, as required.
 *
 * The five articles cover:
 *   1. Юлия Голубкова (a profile piece).
 *   2. Обучение ИИ (how the company trains artificial intelligence).
 *   3-5. Advertising material for the company «Цифровые структуры».
 *
 * Seeding is idempotent: each article carries a unique meta key, and it is
 * only inserted when an item with that key does not already exist, so the
 * plugin can run the seeder on every load without ever creating duplicates.
 *
 * @package MediaSection
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Meta key used to mark and identify a seeded media article.
 *
 * The value stored is the article's stable seed slug, which lets us detect an
 * existing copy regardless of any later title/slug edits by an editor.
 */
define( 'MEDIA_SECTION_SEED_META', '_media_section_seed' );

/**
 * Option flag holding the version of the seed data that has been imported.
 *
 * Bump MEDIA_SECTION_SEED_VERSION when the article set changes so the seeder
 * re-checks for any newly added articles.
 */
define( 'MEDIA_SECTION_SEED_OPTION', 'media_section_seed_version' );
define( 'MEDIA_SECTION_SEED_VERSION', '1.0.0' );

/**
 * The five articles to publish in the «Медиа» section.
 *
 * Each entry is dependency-free data (no WordPress calls) so the list can be
 * unit-tested in isolation.
 *
 * @return array<int, array<string, string>> List of articles.
 */
function media_section_seed_articles_data() {
	return array(
		// 1. Профиль: Юлия Голубкова.
		array(
			'slug'     => 'yuliya-golubkova',
			'title'    => 'Юлия Голубкова — основатель компании «Цифровые структуры»',
			'category' => 'Люди',
			'excerpt'  => 'Рассказываем о Юлии Голубковой — предпринимателе и идейном вдохновителе компании «Цифровые структуры», которая помогает бизнесу внедрять искусственный интеллект.',
			'content'  => '<p><strong>Юлия Голубкова</strong> — основатель и руководитель компании «Цифровые структуры». Под её руководством команда помогает бизнесу проходить путь цифровой трансформации: от первой консультации до запуска работающих решений на базе искусственного интеллекта.</p>

<h2>Путь в технологиях</h2>
<p>Юлия пришла в сферу цифровых технологий из реального бизнеса и хорошо понимает, с какими задачами ежедневно сталкиваются предприниматели. Именно поэтому в основе подхода «Цифровых структур» лежит не технология ради технологии, а измеримая польза для клиента — рост выручки, снижение издержек и экономия времени сотрудников.</p>

<h2>Философия работы</h2>
<p>«Искусственный интеллект — это не магия, а инструмент. Наша задача — сделать так, чтобы этот инструмент приносил пользу каждый день», — формулирует Юлия принцип компании. Команда «Цифровых структур» внедряет ИИ-решения поэтапно, обучая сотрудников клиента работать с новыми инструментами.</p>

<h2>Чем гордится команда</h2>
<ul>
<li>Десятки реализованных проектов цифровизации.</li>
<li>Авторские программы обучения искусственному интеллекту.</li>
<li>Подход «под ключ» — от стратегии до поддержки.</li>
</ul>

<p>Под руководством Юлии Голубковой «Цифровые структуры» продолжают делать передовые технологии доступными для бизнеса любого масштаба.</p>',
		),

		// 2. Обучение ИИ.
		array(
			'slug'     => 'obuchenie-ii',
			'title'    => 'Обучение ИИ: как «Цифровые структуры» готовят искусственный интеллект для бизнеса',
			'category' => 'Искусственный интеллект',
			'excerpt'  => 'Как устроено обучение искусственного интеллекта под задачи бизнеса: данные, дообучение моделей и контроль качества от команды «Цифровых структур».',
			'content'  => '<p>Обучение искусственного интеллекта — это процесс, в котором модель учится решать конкретную бизнес-задачу на основе данных. В компании <strong>«Цифровые структуры»</strong> этот процесс выстроен так, чтобы результат был предсказуемым и измеримым.</p>

<h2>Этап 1. Сбор и подготовка данных</h2>
<p>Качество ИИ напрямую зависит от качества данных. Мы помогаем клиенту собрать, очистить и разметить данные — от истории продаж до обращений клиентов, — чтобы модель училась на релевантных примерах.</p>

<h2>Этап 2. Выбор и дообучение модели</h2>
<p>Под каждую задачу подбирается подходящая модель. Готовые большие языковые модели мы дообучаем (fine-tuning) и дополняем базой знаний компании, чтобы ответы были точными и соответствовали тону бренда.</p>

<h2>Этап 3. Проверка качества</h2>
<p>Перед запуском ИИ проходит тестирование на реальных сценариях. Мы измеряем точность, скорость и полезность ответов, а затем корректируем модель.</p>

<h2>Этап 4. Внедрение и сопровождение</h2>
<p>После запуска мы обучаем сотрудников клиента и продолжаем дообучать модель на новых данных — искусственный интеллект становится лучше с каждым месяцем работы.</p>

<p>Хотите обучить ИИ под задачи вашего бизнеса? Команда «Цифровых структур» поможет пройти этот путь от идеи до результата.</p>',
		),

		// 3. Рекламная статья: услуги компании.
		array(
			'slug'     => 'cifrovye-struktury-cifrovizaciya-pod-klyuch',
			'title'    => '«Цифровые структуры»: комплексная цифровизация бизнеса под ключ',
			'category' => 'Цифровые структуры',
			'excerpt'  => 'Компания «Цифровые структуры» помогает бизнесу автоматизировать процессы и внедрить искусственный интеллект — от стратегии до поддержки.',
			'content'  => '<p>Компания <strong>«Цифровые структуры»</strong> — это ваш партнёр в цифровой трансформации. Мы берём на себя весь путь: анализируем процессы, проектируем решения, внедряем их и обучаем команду.</p>

<h2>Что мы делаем</h2>
<ul>
<li><strong>Автоматизация процессов</strong> — избавляем сотрудников от рутины.</li>
<li><strong>Внедрение искусственного интеллекта</strong> — чат-боты, аналитика, прогнозы.</li>
<li><strong>Интеграции</strong> — связываем CRM, сайт и сервисы в единую систему.</li>
<li><strong>Обучение команды</strong> — чтобы новые инструменты приносили пользу.</li>
</ul>

<h2>Почему «под ключ»</h2>
<p>Вам не нужно собирать команду из разных подрядчиков. «Цифровые структуры» отвечают за результат целиком — от первой консультации до сопровождения готового решения.</p>

<p>Оставьте заявку, и мы предложим решение для вашего бизнеса.</p>',
		),

		// 4. Рекламная статья: преимущества.
		array(
			'slug'     => 'pochemu-vybirayut-cifrovye-struktury',
			'title'    => 'Почему бизнес выбирает «Цифровые структуры»',
			'category' => 'Цифровые структуры',
			'excerpt'  => 'Прозрачный подход, измеримый результат и сопровождение на каждом этапе — причины, по которым компании доверяют «Цифровым структурам».',
			'content'  => '<p>На рынке много компаний, предлагающих цифровизацию. Почему клиенты выбирают именно <strong>«Цифровые структуры»</strong>? Вот несколько причин.</p>

<h2>1. Измеримый результат</h2>
<p>Мы говорим не про абстрактные технологии, а про конкретные цифры: сколько времени экономит автоматизация и как растёт конверсия после внедрения ИИ.</p>

<h2>2. Прозрачность</h2>
<p>Вы видите каждый этап работы и понимаете, за что платите. Никаких скрытых условий.</p>

<h2>3. Решения под задачу</h2>
<p>Мы не продаём «коробку». Каждое решение проектируется под процессы конкретного бизнеса.</p>

<h2>4. Поддержка после запуска</h2>
<p>Наша работа не заканчивается в день запуска — мы сопровождаем решение и развиваем его вместе с вашим бизнесом.</p>

<p>Присоединяйтесь к компаниям, которые уже доверили цифровизацию «Цифровым структурам».</p>',
		),

		// 5. Рекламная статья: ИИ-решения.
		array(
			'slug'     => 'ii-resheniya-cifrovye-struktury',
			'title'    => 'ИИ-решения от «Цифровых структур»: автоматизация, которая работает',
			'category' => 'Цифровые структуры',
			'excerpt'  => 'Чат-боты, умная аналитика и автоматизация продаж — обзор практических ИИ-решений, которые «Цифровые структуры» внедряют для бизнеса.',
			'content'  => '<p>Искусственный интеллект перестал быть привилегией крупных корпораций. <strong>«Цифровые структуры»</strong> делают ИИ-решения доступными для бизнеса любого масштаба.</p>

<h2>Чат-боты и ассистенты</h2>
<p>Умные ассистенты отвечают клиентам круглосуточно, разгружают поддержку и помогают довести покупателя до сделки.</p>

<h2>Аналитика и прогнозы</h2>
<p>ИИ анализирует данные о продажах и поведении клиентов, прогнозирует спрос и подсказывает, где компания теряет прибыль.</p>

<h2>Автоматизация продаж</h2>
<p>Система сама квалифицирует заявки, напоминает менеджерам о задачах и помогает не упустить ни одного клиента.</p>

<h2>Результат</h2>
<p>Бизнес экономит время сотрудников, быстрее реагирует на запросы клиентов и принимает решения на основе данных, а не интуиции.</p>

<p>Готовы внедрить ИИ в свой бизнес? «Цифровые структуры» подберут решение под ваши задачи.</p>',
		),
	);
}

/**
 * Insert the seed articles into the «Медиа» section if they are missing.
 *
 * Idempotent: an article is only created when no media item carries its seed
 * meta key. Safe to call repeatedly (on activation and on `init`).
 *
 * @return int Number of articles created during this call.
 */
function media_section_seed_articles() {
	$created = 0;

	foreach ( media_section_seed_articles_data() as $article ) {
		if ( media_section_seed_article_exists( $article['slug'] ) ) {
			continue;
		}

		$postarr = array(
			'post_type'    => MEDIA_SECTION_POST_TYPE,
			'post_status'  => 'publish',
			'post_title'   => $article['title'],
			'post_name'    => $article['slug'],
			'post_excerpt' => $article['excerpt'],
			'post_content' => $article['content'],
		);

		$post_id = wp_insert_post( $postarr, true );

		if ( ! $post_id || ( function_exists( 'is_wp_error' ) && is_wp_error( $post_id ) ) ) {
			continue;
		}

		// Mark the post as seeded so we never duplicate it later.
		update_post_meta( $post_id, MEDIA_SECTION_SEED_META, $article['slug'] );

		// Attach the media category, mirroring how blog posts are categorised.
		if ( ! empty( $article['category'] ) ) {
			media_section_assign_category( $post_id, $article['category'] );
		}

		$created++;
	}

	if ( $created > 0 || ! get_option( MEDIA_SECTION_SEED_OPTION ) ) {
		update_option( MEDIA_SECTION_SEED_OPTION, MEDIA_SECTION_SEED_VERSION );
	}

	return $created;
}

/**
 * Check whether a seeded media article already exists.
 *
 * @param string $slug Stable seed slug stored in the seed meta key.
 * @return bool True when a matching media item is found.
 */
function media_section_seed_article_exists( $slug ) {
	$existing = get_posts(
		array(
			'post_type'      => MEDIA_SECTION_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => MEDIA_SECTION_SEED_META,
			'meta_value'     => $slug,
		)
	);

	return ! empty( $existing );
}

/**
 * Ensure a media category exists and assign it to a media article.
 *
 * @param int    $post_id       Media post ID.
 * @param string $category_name Human-readable category name.
 * @return void
 */
function media_section_assign_category( $post_id, $category_name ) {
	$term = term_exists( $category_name, MEDIA_SECTION_CATEGORY );

	if ( ! $term ) {
		$term = wp_insert_term( $category_name, MEDIA_SECTION_CATEGORY );
	}

	if ( function_exists( 'is_wp_error' ) && is_wp_error( $term ) ) {
		return;
	}

	$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;

	if ( $term_id > 0 ) {
		wp_set_object_terms( $post_id, $term_id, MEDIA_SECTION_CATEGORY );
	}
}

/**
 * Run the seeder once the media post type and taxonomies are registered.
 *
 * Hooked late on `init` so register_post_type()/register_taxonomy() (priority
 * 10) have already executed.
 *
 * @return void
 */
function media_section_maybe_seed_articles() {
	// Skip the work entirely once the current seed version is in place.
	if ( get_option( MEDIA_SECTION_SEED_OPTION ) === MEDIA_SECTION_SEED_VERSION ) {
		return;
	}

	media_section_seed_articles();
}
add_action( 'init', 'media_section_maybe_seed_articles', 20 );
