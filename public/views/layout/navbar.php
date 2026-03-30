<?php
/**
 * FrontIran - Navbar Component
 * Site d'information de guerre en Iran
 */

// ─── Dépendances (models) ────────────────────────────────────────────────────
// Cette vue peut tourner même si la DB n'est pas disponible (fallback UI).
$__root = realpath(__DIR__ . '/../../../');
if ($__root) {
    $categoryModelPath = $__root . '/app/models/Category.php';
    $articleModelPath = $__root . '/app/models/Article.php';

    if (is_file($categoryModelPath)) {
        require_once $categoryModelPath;
    }
    if (is_file($articleModelPath)) {
        require_once $articleModelPath;
    }
}

// ─── Styles (frontoffice) ───────────────────────────────────────────────────
if (!function_exists('renderStyles')) {
    function renderStyles(): string
    {
        // Le document root Apache est supposé pointer sur /public
        return '<link rel="stylesheet" href="/assets/css/navbar.css">';
    }
}

// ─── Configuration ────────────────────────────────────────────────────────────
$site_config = [
    'nom'           => 'FRONTIRAN',
	'slogan'        => 'Guerre en Iran · Analyses',
    'edition'       => 847,
    'correspondants'=> 47,
    'langues'       => ['FR', 'EN', 'AR', 'FA'],
    'langue_active' => 'FR',
];

// ─── Navigation ───────────────────────────────────────────────────────────────
$nav_items = [
    ['label' => 'Accueil',          'href' => '/views/home.php',  'active' => true],
    ['label' => 'Front militaire',  'href' => '/front-militaire', 'active' => false],
    ['label' => 'Diplomatie',       'href' => '/diplomatie',      'active' => false],
    ['label' => 'Humanitaire',      'href' => '/humanitaire',     'active' => false],
    ['label' => 'Cartes & données', 'href' => '/cartes',          'active' => false],
    ['label' => 'Témoignages',      'href' => '/temoignages',     'active' => false],
    ['label' => 'Analyses',         'href' => '/analyses',        'active' => false],
    ['label' => 'Archives',         'href' => '/archives',        'active' => false],
];

// ─── Fil de dépêches (dernière heure) ─────────────────────────────────────────
$breaking_news = [
    'Cessez-le-feu partiel annoncé dans la région de Tabriz — pourparlers en cours à Genève',
    'Le Conseil de sécurité de l\'ONU convoqué en session d\'urgence ce soir',
    'Corridor humanitaire ouvert depuis Kirmanshah — 12 000 civils évacués',
    'Frappe aérienne signalée près d\'Isfahan — bilan en cours d\'évaluation',
];

// ─── Données dynamiques ─────────────────────────────────────────────────────
// Catégories (nav) + derniers articles publiés (ticker)
try {
    $homeUrl = '/views/home.php';
    $selectedCategorySlug = isset($_GET['category']) ? trim((string) $_GET['category']) : '';
    $selectedCategoryId = null;

    if ($selectedCategorySlug !== '' && class_exists('Category')) {
        $catSel = Category::findBySlug($selectedCategorySlug);
        if (!empty($catSel['id'])) {
            $selectedCategoryId = (int) $catSel['id'];
        }
    }

    if (class_exists('Category')) {
        $categories = Category::all();

        if (!empty($categories)) {
            $dynamicNav = [
                ['label' => 'Accueil', 'href' => $homeUrl, 'active' => ($selectedCategorySlug === '')],
            ];

            foreach ($categories as $cat) {
                $name = (string)($cat['name'] ?? '');
                $slug = (string)($cat['slug'] ?? '');
                if ($name === '' || $slug === '') {
                    continue;
                }

                // Route front la plus simple en attendant un vrai router
                $dynamicNav[] = [
                    'label' => $name,
					'href' => $homeUrl . '?category=' . rawurlencode($slug),
					'active' => ($selectedCategorySlug !== '' && $selectedCategorySlug === $slug),
                ];
            }

            if (count($dynamicNav) > 1) {
                $nav_items = $dynamicNav;
            }
        }
    }

    if (class_exists('Article')) {
		$latest = Article::listPublished(8, 0, $selectedCategoryId);
        if (!empty($latest)) {
            $ticker = [];
            foreach ($latest as $a) {
                $title = trim((string)($a['title'] ?? ''));
                if ($title !== '') {
                    $ticker[] = $title;
                }
            }
            if (!empty($ticker)) {
                $breaking_news = $ticker;
            }
        }
    }
} catch (Throwable $e) {
    // Silencieux: on garde le contenu mock si la DB est indisponible.
}

// ─── Helpers ──────────────────────────────────────────────────────────────────

/**
 * Formate la date courante en français
 */
function formatDate(): string {
    $jours   = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
    $mois    = ['janvier','février','mars','avril','mai','juin',
                'juillet','août','septembre','octobre','novembre','décembre'];
    $ts      = time();
    return $jours[date('w', $ts)] . ' ' . date('j', $ts) . ' ' . $mois[date('n', $ts) - 1] . ' ' . date('Y', $ts);
}

/**
 * Génère la barre supérieure (topbar)
 */
function renderTopbar(array $config): string {
    $date     = htmlspecialchars(formatDate());
    $langues  = implode(' · ', array_map('htmlspecialchars', $config['langues']));

    return <<<HTML
    <div class="fi-topbar">
        <div class="fi-topbar__left">
            <span>{$date}</span>
            <span>Édition internationale</span>
            <span>Zone de conflit — Moyen-Orient</span>
        </div>
        <div class="fi-topbar__right">
            <div class="fi-live-badge">
                <span class="fi-live-badge__dot"></span>
                EN DIRECT
            </div>
            <span class="fi-topbar__langs">{$langues}</span>
        </div>
    </div>
    HTML;
}

/**
 * Génère le masthead principal
 */
function renderMasthead(array $config): string {
    $titre  = htmlspecialchars($config['nom']);
    $slogan = htmlspecialchars($config['slogan']);

    // Sépare le préfixe et la partie colorée
    $prefix = 'FRONT';
    $accent = 'IRAN';

    return <<<HTML
    <div class="fi-masthead">
        <div class="fi-masthead__brand">
            <div class="fi-masthead__title">
                {$prefix}<span class="fi-masthead__accent">{$accent}</span>
            </div>
            <div class="fi-masthead__slogan">{$slogan}</div>
        </div>
    </div>
    HTML;
}

/**
 * Génère la barre de navigation principale
 */
function renderNav(array $items): string {
    $links = '';
    foreach ($items as $item) {
        $label  = htmlspecialchars($item['label']);
        $href   = htmlspecialchars($item['href']);
        $active = $item['active'] ? ' fi-nav__link--active' : '';

        $links .= <<<HTML
        <a href="{$href}" class="fi-nav__link{$active}">
            <span class="fi-nav__dot"></span>{$label}
        </a>
        <div class="fi-nav__divider"></div>
        HTML;
    }

    return <<<HTML
    <nav class="fi-nav" aria-label="Navigation principale">
        {$links}
        <div class="fi-nav__search">
            <form class="fi-search" action="/recherche" method="get" role="search">
                <span class="fi-search__icon" aria-hidden="true">⌕</span>
                <input
                    type="search"
                    name="q"
                    class="fi-search__input"
                    placeholder="Rechercher..."
                    aria-label="Rechercher sur FrontIran"
                >
            </form>
        </div>
    </nav>
    HTML;
}

/**
 * Génère le bandeau "Dernière heure" avec ticker
 */
function renderBreakingBar(array $news): string {
    // Double la liste pour un défilement fluide en boucle
    $all   = array_merge($news, $news);
    $items = '';
    foreach ($all as $headline) {
        $text   = htmlspecialchars($headline);
        $items .= "<span>{$text}</span>\n";
    }

    return <<<HTML
    <div class="fi-breaking" role="marquee" aria-live="polite" aria-label="Dernières nouvelles">
        <div class="fi-breaking__label" aria-hidden="true">Dernière heure</div>
        <div class="fi-breaking__track">
            <div class="fi-breaking__ticker">
                {$items}
            </div>
        </div>
    </div>
    HTML;
}

/**
 * Rendu complet de la navbar
 */
function renderNavbar(array $config, array $nav_items, array $breaking_news): string {
    $topbar   = renderTopbar($config);
    $masthead = renderMasthead($config);
    $nav      = renderNav($nav_items);
    $breaking = renderBreakingBar($breaking_news);

    return <<<HTML
    <header class="fi-navbar" role="banner">
        {$topbar}
        {$masthead}
        {$nav}
        {$breaking}
    </header>
    HTML;
}

