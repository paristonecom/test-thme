<?php

define('MY_DIR_URL', get_stylesheet_directory_uri());
define('MY_DIR_PATH', get_stylesheet_directory());

/**
 * Set up theme defaults and registers support for various WordPress feaures.
 */
add_action('after_setup_theme', function () {
	load_theme_textdomain('bathe', get_theme_file_uri('languages'));

	add_theme_support('automatic-feed-links');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	));
	add_theme_support('post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	));
	add_theme_support('custom-background', apply_filters('bathe_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	)));

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support('custom-logo', array(
		'height' => 200,
		'width' => 50,
		'flex-width' => true,
		'flex-height' => true,
	));

	register_nav_menus(array(
		'primary' => __('Primary Menu', 'bathe'),
	));
});

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
add_action('after_setup_theme', function () {
	$GLOBALS['content_width'] = apply_filters('bathe_content_width', 960);
}, 0);

/**
 * Register widget area.
 */
add_action('widgets_init', function () {
	register_sidebar(array(
		'name' => __('Sidebar', 'bathe'),
		'id' => 'sidebar-1',
		'description' => '',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget' => '</section>',
		'before_title' => '<h2 class="widget-title">',
		'after_title' => '</h2>',
	));
});

/**
 * Enqueue scripts and styles.
 */
add_action('wp_enqueue_scripts', function () {

	wp_enqueue_style('bathe-main', get_theme_file_uri('assets/css/main.css'));

	wp_enqueue_script('bathe-bundle', get_theme_file_uri('assets/js/bundle.js'), array(), null, true);

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
});


if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title' => 'オプションページ',
		'menu_title' => 'オプションページ',
		'menu_slug' => 'site-option',
		'position' => '30.5',
		'redirect' => false,
		'update_button' => '保存',
		'updated_message' => '保存しました',
		'icon_url' => 'dashicons-desktop',
	));

	acf_add_options_sub_page(array(
		'page_title' => 'ロゴ設定',
		'menu_title' => 'ロゴ設定',
		'menu_slug' => 'site-option-logo-settings',
		'parent_slug' => 'site-option',
		'update_button' => '保存',
		'updated_message' => '保存しました',
	));

	acf_add_options_sub_page(array(
		'page_title' => '商品オプション',
		'menu_title' => '商品オプション',
		'menu_slug' => 'site-option-product-options',
		'parent_slug' => 'site-option',
		'update_button' => '保存',
		'updated_message' => '保存しました',
	));
}

// お花を送るの商品を取得。
function refl_add_tags()
{
	add_rewrite_tag('%refl_product_purpose%', '([^&]+)');
}

add_action('init', 'refl_add_tags', 10, 0);

function refl_filter_archive($query)
{
	if (is_admin() || !$query->is_main_query()) {
		return;
	}

	// カスタム投稿タイプが「jobs」の場合
	if ($query->is_post_type_archive('products')) {

		$meta_query = [
			'relation' => 'AND',
		];

		// 勤務地（都道府県）
		if (isset($_GET['products']) && strval($_GET['products']) !== "") {
			$meta_query[] = array(
				'key' => '基本情報_勤務地_都道府県',
				'value' => $_GET['prefecture'],
				'compare' => '='
			);
		}

		// 職種
		if (!empty($_GET['occupations']) and is_array($_GET['occupations'])) {
			$sub_meta_query = [
				'relation' => 'OR',
			];
			foreach ($_GET['occupations'] as $index => $occupation) {
				$sub_meta_query[] = [
					'key' => '基本情報_募集職種',
					'value' => $occupation,
					'compare' => '='
				];
			}
			$meta_query[] = $sub_meta_query;
		}

		// 雇用形態
		if (!empty($_GET['employment_statuses']) and is_array($_GET['employment_statuses'])) {
			$sub_meta_query = [
				'relation' => 'OR',
			];
			foreach ($_GET['employment_statuses'] as $index => $employment_status) {
				$sub_meta_query[] = [
					'key' => '基本情報_雇用形態',
					'value' => $employment_status,
					'compare' => '='
				];
			}
			$meta_query[] = $sub_meta_query;
		}

		$query->set('meta_query', $meta_query);
	}


	// PW設定した投稿はアーカイブで非表示 投稿、カスタム投稿全てと固定ページ
	if (!is_singular()) {
		set_query_var('has_password', false);
	}

	// 事例・セミナー・お役立ち情報の各投稿タイプで適用するフィルター
	if (empty($query->query_vars['post_type'])) {
		return;
	}

	if ($query->query_vars['post_type'] != 'nkgr_case'
		&& $query->query_vars['post_type'] != 'nkgr_seminar'
		&& $query->query_vars['post_type'] != 'nkgr_useful') {
		return;
	}

	// セミナー単体は期限切れ、受付終了も表示可能、それ以外は非表示
	$query_args = array();

	// セミナー単体は期限切れ、受付終了も表示可能、それ以外は非表示
	// また、投稿の並び順は開催日で降順
	if ('nkgr_seminar' == $query->query_vars['post_type'] && is_archive()) {
		$query_args = nkgr_hide_disable_seminar($query_args);

		//名前付きmeta_queryで開催日フィールドを取得して、順序指定
		$query_args[] = array(
			'seminardate' => array(
				'key' => 'seminardate',
				'type' => 'DATE'
			)
		);
		set_query_var('orderby', array('seminardate' => 'ASC'));
		// セミナー一覧ページの表示件数を変更。
		set_query_var('posts_per_page', 20);
	}

	// 業種、課題などの絞り込み表示のmeta_queryを作る
	$filter = array(
		'industry' => '',
		'issue' => '',
		'employee' => '',
		'format' => '',
		'area' => '',
		'price' => '',
		'type' => '',
	);

	$filtered = array_intersect_key($query->query, $filter);

	//クエリー文字列を配列に変換、業種・課題は投稿名からサービス投稿のIDに変えておく
	array_walk($filtered, function (&$value, $tag) {
		$params = explode(',', $value);
		if ($tag == 'industry' || $tag == 'issue') {
			$params = array_map(function ($v) {
				return (get_page_by_path($v, OBJECT, 'nkgr_service')->ID);
			}, $params);
		}
		$value = $params;
	});

	$query_args = array_merge($query_args, nkgr_build_meta_query($filtered));

	if (!empty ($query_args)) {
		set_query_var('meta_query', $query_args);
	}
}

//add_action( 'pre_get_posts', 'refl_filter_archive' );

if (isset($_GET['tag'])) {
	global $wp_rewrite;
	?>
	<pre>
  <p>リライトタグ（構造タグ）/rewritecode</p>
  <?php var_dump($wp_rewrite->rewritecode); ?>
  <p>リライトタグを置き換える正規表現/rewritereplace</p>
  <?php var_dump($wp_rewrite->rewritereplace); ?>
  <p>置換後に追加するクエリパラメータ/queryreplace</p>
  <?php var_dump($wp_rewrite->queryreplace); ?>
</pre>
	<?php
	exit;
}

function echo_qury_var()
{
	global $wp_query;
	echo "<pre>";
	var_dump($wp_query);
	exit;
}

//add_action('template_redirect', 'echo_qury_var');

function my_query_vars($vars)
{
	$vars[] = 'product_category';
	return $vars;
}

//add_filter( 'query_vars', 'my_query_vars' );


add_action('wp_enqueue_scripts', 'add_styles');
function add_styles()
{
	// google fonts
	wp_enqueue_style(
		'google-fonts_style',
		'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap'
	);

	// CSSの読み込み
	$timestamp = date('Ymdgis', filemtime(MY_DIR_PATH . '/dist/css/main.css'));
	wp_enqueue_style('my-block-style', MY_DIR_URL . '/dist/css/main.css', [], $timestamp);
}

add_action('enqueue_block_editor_assets', function () {
	// CSSの読み込み
	$timestamp = date('Ymdgis', filemtime(MY_DIR_PATH . '/dist/css/blocks.css'));
	wp_enqueue_style('my-block-style', MY_DIR_URL . '/dist/css/blocks.css', [], $timestamp);

	// JSの読み込み
	$timestamp = date('Ymdgis', filemtime(MY_DIR_PATH . '/dist/js/bundle.js'));
	wp_enqueue_script('my-block-script', MY_DIR_URL . '/dist/js/bundle.js', [], $timestamp, true);
});

add_action('init', 'test_register_blocks');
function test_register_blocks()
{
	$blocks = [
		'sample01',
	];
	foreach ($blocks as $slug) {
		// ブロックの block.json を読み込ませる
		register_block_type(__DIR__ . "/block-editor/dist/blocks/{$slug}");

		// ブロックを作成するスクリプトを登録
		$timestamp = date('Ymdgis', filemtime(MY_DIR_PATH . "/block-editor/dist/blocks/{$slug}/index.js"));
		wp_register_script("my-block-{$slug}", MY_DIR_URL . "/block-editor/dist/blocks/{$slug}/index.js", [], date('Ymdgis'), true);
	}
}
