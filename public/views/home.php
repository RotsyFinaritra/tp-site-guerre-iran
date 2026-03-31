<?php
require_once __DIR__ . '/layout/navbar.php';
require_once __DIR__ . '/components/articles.php';

$selectedCategorySlug = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
$selectedCategoryId = null;

try {
    if ($selectedCategorySlug !== '' && class_exists('Category')) {
        $cat = Category::findBySlug($selectedCategorySlug);
        if (!empty($cat['id'])) {
            $selectedCategoryId = (int) $cat['id'];
        }
    }
} catch (Throwable $e) {
    $selectedCategoryId = null;
}

[$article_hero, $articles_grid3, $article_horizontal, $breves, $articles_grid2] = frontiran_articles_data($selectedCategoryId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <meta name="description" content="<?= htmlspecialchars(($site_config['nom'] ?? 'Iran Correspondent') . ' : couverture de guerre, brèves et analyses.', ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($site_config['nom']) ?> — Couverture de guerre</title>
    <?= renderStyles() ?>
</head>
<body>

<?= renderNavbar($site_config, $nav_items, $breaking_news) ?>

<!-- Contenu principal de la page -->
<main>
    <?= frontiran_render_articles_page(
        $article_hero,
        $articles_grid3,
        $article_horizontal,
        $breves,
        $articles_grid2
    ) ?>
</main>

</body>
</html>