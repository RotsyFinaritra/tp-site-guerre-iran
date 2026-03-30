<?php

declare(strict_types=1);

require_once __DIR__ . '/../app/models/Article.php';
require_once __DIR__ . '/../app/models/Category.php';

$slug = isset($_GET['slug']) ? trim((string) $_GET['slug']) : '';
if ($slug === '') {
	http_response_code(400);
	echo 'Slug manquant.';
	exit;
}

try {
	$article = Article::findBySlug($slug, false);
} catch (Throwable $e) {
	$article = null;
}

if (!$article) {
	http_response_code(404);
	echo 'Article introuvable.';
	exit;
}

$category = null;
try {
	$category = Category::findById((int) ($article['category_id'] ?? 0));
} catch (Throwable $e) {
	$category = null;
}

// Permet à la navbar de marquer la catégorie active et filtrer le ticker
if (!isset($_GET['category']) && !empty($category['slug'])) {
	$_GET['category'] = (string) $category['slug'];
}

// Navbar component
require_once __DIR__ . '/views/layout/navbar.php';

$title = (string) ($article['title'] ?? '');
$publishedAt = (string) ($article['published_at'] ?? '');
$categoryName = (string) ($category['name'] ?? '');

$heroPath = (string) ($article['hero_image_path'] ?? '');
$heroAlt = (string) ($article['hero_image_alt'] ?? $title);

if ($heroPath !== '' && $heroPath[0] !== '/') {
	$heroPath = '/' . $heroPath;
}

// content_html est produit par l'admin (TinyMCE). On l'affiche tel quel.
$contentHtml = (string) ($article['content_html'] ?? '');

?><!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
	<?= renderStyles() ?>
	<style>
		.fi-article-wrap {
			max-width: 1000px;
			margin: 18px auto 40px;
			padding: 24px;
			background: #fff;
			border: 1px solid rgba(26,18,9,0.12);
			font-family: 'Source Serif 4', Georgia, serif;
		}
		.fi-article-head {
			margin-bottom: 16px;
		}
		.fi-article-kicker {
			font-family: 'IBM Plex Mono', monospace;
			font-size: 10px;
			letter-spacing: .18em;
			text-transform: uppercase;
			color: rgba(26,18,9,0.65);
			margin-bottom: 10px;
		}
		.fi-article-title {
			font-family: 'Playfair Display', serif;
			font-size: 34px;
			font-weight: 900;
			line-height: 1.15;
			color: #1A1209;
			margin: 0 0 10px;
		}
		.fi-article-meta {
			font-family: 'IBM Plex Mono', monospace;
			font-size: 11px;
			letter-spacing: .04em;
			color: rgba(26,18,9,0.6);
		}
		.fi-article-hero {
			margin: 18px 0 18px;
		}
		.fi-article-hero img {
			width: 100%;
			height: auto;
			display: block;
			border: 1px solid rgba(26,18,9,0.12);
		}
		.fi-article-content {
			color: rgba(26,18,9,0.92);
			line-height: 1.85;
			font-size: 16px;
		}
		.fi-article-content h1,
		.fi-article-content h2 {
			font-family: 'Playfair Display', serif;
			font-weight: 800;
			line-height: 1.2;
			margin: 26px 0 14px;
			color: #1A1209;
		}
		.fi-article-content h1 { font-size: 30px; }
		.fi-article-content h2 { font-size: 24px; }
		.fi-article-content h3,
		.fi-article-content h4,
		.fi-article-content h5,
		.fi-article-content h6 {
			font-family: 'Playfair Display', serif;
			font-weight: 700;
			font-size: 18px;
			margin: 22px 0 10px;
			color: #1A1209;
		}
		.fi-article-content p {
			margin: 0 0 14px;
		}
		.fi-article-content ul,
		.fi-article-content ol {
			margin: 0 0 16px 22px;
		}
		.fi-article-content img {
			max-width: 100%;
			height: auto;
			display: block;
			margin: 14px auto;
		}
		.fi-article-content figure { margin: 16px 0; }
		.fi-article-content figcaption {
			font-family: 'IBM Plex Mono', monospace;
			font-size: 11px;
			color: rgba(26,18,9,0.6);
			margin-top: 8px;
		}
		.fi-article-content a { color: inherit; text-decoration: underline; }
		.fi-backlink {
			display: inline-block;
			margin-top: 18px;
			font-family: 'IBM Plex Mono', monospace;
			font-size: 11px;
			letter-spacing: .08em;
			text-transform: uppercase;
			color: rgba(26,18,9,0.75);
			text-decoration: none;
		}
		.fi-backlink:hover { text-decoration: underline; }
	</style>
</head>
<body>

<?= renderNavbar($site_config, $nav_items, $breaking_news) ?>

<main>
	<article class="fi-article-wrap">
		<header class="fi-article-head">
			<div class="fi-article-kicker">
				<?= htmlspecialchars($categoryName !== '' ? $categoryName : 'Article', ENT_QUOTES, 'UTF-8') ?>
			</div>
			<h1 class="fi-article-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
			<div class="fi-article-meta">
				<?= htmlspecialchars($publishedAt !== '' ? $publishedAt : 'Publié', ENT_QUOTES, 'UTF-8') ?>
			</div>
		</header>

		<?php if ($heroPath !== ''): ?>
			<div class="fi-article-hero">
				<img src="<?= htmlspecialchars($heroPath, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($heroAlt, ENT_QUOTES, 'UTF-8') ?>">
			</div>
		<?php endif; ?>

		<div class="fi-article-content">
			<?= $contentHtml ?>
		</div>

		<a class="fi-backlink" href="/views/home.php<?= $categoryName !== '' && !empty($category['slug']) ? ('?category=' . rawurlencode((string)$category['slug'])) : '' ?>">
			← Retour à l’accueil
		</a>
	</article>
</main>

</body>
</html>
