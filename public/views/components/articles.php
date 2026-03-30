<?php
/**
 * FrontIran — Composant Articles (réutilisable)
 * Grille éditoriale : hero + grid3 + split + grid2
 */

// ─── Dépendances (models) ────────────────────────────────────────────────────
// Le composant peut fonctionner sans DB (fallback mock).
$__publicRoot = realpath(__DIR__ . '/../..'); // .../public
if ($__publicRoot) {
    $categoryModelPath = $__publicRoot . '/../app/models/Category.php';
    $articleModelPath = $__publicRoot . '/../app/models/Article.php';
    if (!class_exists('Category') && is_file($categoryModelPath)) {
        require_once $categoryModelPath;
    }
    if (!class_exists('Article') && is_file($articleModelPath)) {
        require_once $articleModelPath;
    }
}

function frontiran_time_ago_fr(?string $dt): string
{
    if (!$dt) {
        return '';
    }
    $ts = strtotime($dt);
    if ($ts === false) {
        return '';
    }
    $diff = time() - $ts;
    if ($diff < 60) {
        return "À l'instant";
    }
    if ($diff < 3600) {
        $min = (int) floor($diff / 60);
        return 'Il y a ' . $min . ' min';
    }
    if ($diff < 86400) {
        $h = (int) floor($diff / 3600);
        return 'Il y a ' . $h . 'h';
    }
    $j = (int) floor($diff / 86400);
    return 'Il y a ' . $j . ' j';
}

function frontiran_excerpt_from_html(string $html, int $maxLen = 160): string
{
    $text = trim(html_entity_decode(strip_tags($html), ENT_QUOTES, 'UTF-8'));
    $text = preg_replace('/\s+/u', ' ', $text) ?: '';
    if (mb_strlen($text) <= $maxLen) {
        return $text;
    }
    $cut = mb_substr($text, 0, $maxLen);
    $lastSpace = mb_strrpos($cut, ' ');
    if ($lastSpace !== false && $lastSpace > 60) {
        $cut = mb_substr($cut, 0, $lastSpace);
    }
    return rtrim($cut) . '…';
}

function frontiran_mock_articles_data(): array
{
    $article_hero = [
        'tag' => 'Urgent — Front militaire',
        'urgent' => true,
        'titre' => 'Les forces se repositionnent autour d\'Urmia après 48h de combats d\'une rare intensité',
        'extrait' => 'Les observateurs sur place signalent un repli tactique au nord-ouest de la ville. Les corridors humanitaires restent sous pression malgré les engagements pris lors des négociations de Genève.',
        'auteur' => 'Ahmed Karimi',
        'role' => 'Correspondant de guerre',
        'temps' => 'Il y a 22 min',
        'image' => 'images/hero-urmia.jpg',
        'alt' => 'Combats dans la région d\'Urmia',
        'href' => '/articles/repositionnement-urmia',
    ];

    $articles_grid3 = [
        [
            'tag' => 'Diplomatie',
            'titre' => 'Washington et Moscou divergent sur les conditions d\'un accord de paix durable',
            'extrait' => 'Les tractations en coulisse révèlent des positions irréconciliables sur le statut de la zone tampon.',
            'auteur' => 'Sophie Leclerc',
            'temps' => 'Il y a 1h',
            'image' => 'images/diplomatie-geneve.jpg',
            'alt' => 'Réunion diplomatique à Genève',
            'href' => '/articles/washington-moscou-paix',
        ],
        [
            'tag' => 'Humanitaire',
            'titre' => '12 000 civils évacués depuis Kirmanshah en moins de 36 heures',
            'extrait' => 'L\'UNHCR coordonne l\'ouverture d\'un troisième camp de transit dans la province de Kermanshah.',
            'auteur' => 'Leila Ahmadi',
            'temps' => 'Il y a 3h',
            'image' => 'images/evacuation-kirmanshah.jpg',
            'alt' => 'Évacuation de civils à Kirmanshah',
            'href' => '/articles/evacuation-kirmanshah',
        ],
        [
            'tag' => 'Analyse',
            'titre' => 'Le rôle des drones dans la redéfinition des lignes de front',
            'extrait' => 'Décryptage des nouvelles tactiques aériennes qui transforment la nature du conflit depuis six semaines.',
            'auteur' => 'Col. (r) Marc Duval',
            'temps' => 'Il y a 5h',
            'image' => 'images/drones-front.jpg',
            'alt' => 'Drones sur le front iranien',
            'href' => '/articles/drones-lignes-front',
        ],
    ];

    $article_horizontal = [
        'tag' => 'Témoignage',
        'titre' => '« Nous avons marché trois jours avant de trouver un passage sûr »',
        'extrait' => 'Rania, 34 ans, raconte l\'exode depuis son village natal au sud de Tabriz. Un récit parmi des milliers que notre équipe recueille sur le terrain.',
        'auteur' => 'Maria Ionescu',
        'role' => 'Envoyée spéciale',
        'temps' => 'Il y a 7h',
        'image' => 'images/temoignage-rania.jpg',
        'alt' => 'Réfugiés sur la route au sud de Tabriz',
        'href' => '/articles/temoignage-rania-tabriz',
    ];

    $breves = [
        ['titre' => 'L\'ONU demande un accès immédiat aux zones enclavées d\'Isfahan', 'temps' => 'Il y a 30 min', 'href' => '/breves/onu-isfahan'],
        ['titre' => 'Turquie : fermeture partielle de la frontière nord-ouest', 'temps' => 'Il y a 2h', 'href' => '/breves/turquie-frontiere'],
        ['titre' => 'Réunion d\'urgence de l\'AIEA sur les sites nucléaires iraniens', 'temps' => 'Il y a 4h', 'href' => '/breves/aiea-nucleaire'],
        ['titre' => 'Marché pétrolier : le Brent franchit les 112 $/baril', 'temps' => 'Il y a 6h', 'href' => '/breves/brent-112'],
    ];

    $articles_grid2 = [
        [
            'tag' => 'Géopolitique',
            'titre' => 'La recomposition des alliances régionales à l\'épreuve du conflit iranien',
            'extrait' => 'De Riyad à Ankara, les puissances régionales recalibrent leurs positions dans une partie d\'échecs aux enjeux considérables.',
            'auteur' => 'Jean-Pierre Moreau',
            'temps' => 'Il y a 9h',
            'image' => 'images/alliances-regionales.jpg',
            'alt' => 'Alliances régionales Moyen-Orient',
            'href' => '/articles/alliances-regionales',
        ],
        [
            'tag' => 'Économie de guerre',
            'titre' => 'Comment le conflit reshape les routes d\'approvisionnement énergétique mondiales',
            'extrait' => 'Les terminaux du Golfe persique sous surveillance, les contrats de long terme renégociés en urgence.',
            'auteur' => 'Nadia Rousseau',
            'temps' => 'Il y a 12h',
            'image' => 'images/routes-energetiques.jpg',
            'alt' => 'Routes énergétiques du Golfe',
            'href' => '/articles/routes-energetiques',
        ],
    ];

    return [$article_hero, $articles_grid3, $article_horizontal, $breves, $articles_grid2];
}

function frontiran_articles_data(?int $categoryId = null): array
{
    try {
        if (class_exists('Article') && method_exists('Article', 'listPublishedWithCategory')) {
            $rows = Article::listPublishedWithCategory(12, 0, $categoryId);
            if (!empty($rows)) {
                return frontiran_articles_data_from_rows($rows);
            }
        }
        if (class_exists('Article')) {
            $rows = Article::listPublished(12, 0, $categoryId);
            if (!empty($rows)) {
                return frontiran_articles_data_from_rows($rows);
            }
        }
    } catch (Throwable $e) {
        // fallback mock
    }

    return frontiran_mock_articles_data();
}

function frontiran_articles_data_from_rows(array $rows): array
{
    $mapped = [];
    foreach ($rows as $r) {
        $title = (string)($r['title'] ?? '');
        $slug = (string)($r['slug'] ?? '');
        if ($title === '' || $slug === '') {
            continue;
        }

        $categoryName = (string)($r['category_name'] ?? '');
        $tag = $categoryName !== '' ? $categoryName : 'Actualités';
        $excerpt = (string)($r['excerpt'] ?? '');
        if ($excerpt === '') {
            $excerpt = frontiran_excerpt_from_html((string)($r['content_html'] ?? ''), 170);
        }

        $publishedAt = (string)($r['published_at'] ?? '');
        $timeLabel = frontiran_time_ago_fr($publishedAt);
        if ($timeLabel === '') {
            $timeLabel = 'Récemment';
        }

        $ymd = '';
        if ($publishedAt !== '') {
            $ts = strtotime($publishedAt);
            if ($ts !== false) {
                $ymd = date('Ymd', $ts);
            }
        }

        $imagePath = (string)($r['hero_image_path'] ?? '');
        $imageAlt = (string)($r['hero_image_alt'] ?? '');
        if ($imageAlt === '') {
            $imageAlt = $title;
        }

        // Pas d'auteur en base pour l'instant -> libellé simple
        $mapped[] = [
            'tag' => $tag,
            'urgent' => false,
            'titre' => $title,
            'extrait' => $excerpt,
            'auteur' => 'Rédaction FrontIran',
            'role' => '',
            'temps' => $timeLabel,
            'image' => $imagePath,
            'alt' => $imageAlt,
            'href' => $ymd !== ''
                ? ('/article/' . rawurlencode($ymd . '-' . $slug))
                : ('/article/' . rawurlencode($slug)),
        ];
    }

    if (empty($mapped)) {
        return frontiran_mock_articles_data();
    }

    $hero = $mapped[0];
    $hero['urgent'] = true;

    $grid3 = array_slice($mapped, 1, 3);
    $horizontal = $mapped[4] ?? $mapped[0];
    $brevesSource = array_slice($mapped, 5, 4);
    $breves = [];
    foreach ($brevesSource as $b) {
        $breves[] = [
            'titre' => (string)$b['titre'],
            'temps' => (string)$b['temps'],
            'href' => (string)$b['href'],
        ];
    }
    $grid2 = array_slice($mapped, 9, 2);
    if (count($grid2) < 2) {
        $grid2 = array_slice($mapped, max(0, count($mapped) - 2), 2);
    }

    return [$hero, $grid3, $horizontal, $breves, $grid2];
}

function frontiran_render_image(
    string $src,
    string $alt,
    string $height = '340px',
    string $label = '',
    string $loading = 'lazy',
    ?string $fetchPriority = null
): string
{
    $alt_e = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');
    $label_e = htmlspecialchars($label ?: $alt, ENT_QUOTES, 'UTF-8');

    // Vérifie l'existence du fichier sur le disque (chemin relatif à /public)
    $publicRoot = realpath(__DIR__ . '/../..'); // .../public
    $diskPath = $publicRoot ? ($publicRoot . '/' . ltrim($src, '/')) : null;

    if ($diskPath && is_file($diskPath)) {
        $webSrc = '/' . ltrim($src, '/');
        $src_e = htmlspecialchars($webSrc, ENT_QUOTES, 'UTF-8');

        $loading = in_array($loading, ['lazy', 'eager', 'auto'], true) ? $loading : 'lazy';
        $fetchAttr = $fetchPriority ? ' fetchpriority="' . htmlspecialchars($fetchPriority, ENT_QUOTES, 'UTF-8') . '"' : '';

        return "<img src=\"{$src_e}\" alt=\"{$alt_e}\" loading=\"{$loading}\" decoding=\"async\"{$fetchAttr} style=\"width:100%;height:{$height};object-fit:cover;display:block;\">";
    }

    return <<<HTML
    <div class="fi-img-ph" style="height:{$height};">
        <span>{$label_e}</span>
    </div>
    HTML;
}

function frontiran_render_tag(string $tag, bool $urgent = false): string
{
    $tag_e = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8');
    $dot = $urgent ? '<span class="fi-tag__dot"></span>' : '';
    return "<div class=\"fi-tag\">{$dot}{$tag_e}</div>";
}

function frontiran_render_meta(string $auteur, string $temps, string $role = ''): string
{
    $a = htmlspecialchars($auteur, ENT_QUOTES, 'UTF-8');
    $t = htmlspecialchars($temps, ENT_QUOTES, 'UTF-8');
    $r = $role ? ' · ' . htmlspecialchars($role, ENT_QUOTES, 'UTF-8') : '';
    return "<div class=\"fi-meta\"><strong>{$a}</strong>{$r} · {$t}</div>";
}

function frontiran_render_section_label(string $label): string
{
    return '<div class="fi-section-label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</div>';
}

function frontiran_render_hero(array $a): string
{
    $img = frontiran_render_image((string)$a['image'], (string)$a['alt'], '340px', (string)$a['tag'], 'eager', 'high');
    $tag = frontiran_render_tag((string)$a['tag'], (bool)($a['urgent'] ?? false));
    $meta = frontiran_render_meta((string)$a['auteur'], (string)$a['temps'], (string)($a['role'] ?? ''));
    $href = htmlspecialchars((string)$a['href'], ENT_QUOTES, 'UTF-8');
    $titre = htmlspecialchars((string)$a['titre'], ENT_QUOTES, 'UTF-8');
    $extrait = htmlspecialchars((string)$a['extrait'], ENT_QUOTES, 'UTF-8');

    return <<<HTML
    <a href="{$href}" class="fi-hero fi-article-link">
        <div class="fi-hero__img">{$img}</div>
        <div class="fi-hero__body">
            <div>
                {$tag}
                <h2 class="fi-hero__title">{$titre}</h2>
                <p class="fi-excerpt">{$extrait}</p>
            </div>
            {$meta}
        </div>
    </a>
    HTML;
}

function frontiran_render_card(array $a, string $imgHeight = '180px', string $titleSize = '17px'): string
{
    $img = frontiran_render_image((string)$a['image'], (string)$a['alt'], $imgHeight, (string)$a['tag']);
    $tag = frontiran_render_tag((string)$a['tag']);
    $meta = frontiran_render_meta((string)$a['auteur'], (string)$a['temps']);
    $href = htmlspecialchars((string)$a['href'], ENT_QUOTES, 'UTF-8');
    $titre = htmlspecialchars((string)$a['titre'], ENT_QUOTES, 'UTF-8');
    $extrait = htmlspecialchars((string)$a['extrait'], ENT_QUOTES, 'UTF-8');

    return <<<HTML
    <a href="{$href}" class="fi-card fi-article-link">
        {$img}
        <div class="fi-card__body">
            <div>
                {$tag}
                <h3 class="fi-card__title" style="font-size:{$titleSize}">{$titre}</h3>
                <p class="fi-excerpt">{$extrait}</p>
            </div>
            {$meta}
        </div>
    </a>
    HTML;
}

function frontiran_render_card_horizontal(array $a): string
{
    $img = frontiran_render_image((string)$a['image'], (string)$a['alt'], '100%', (string)$a['tag']);
    $tag = frontiran_render_tag((string)$a['tag']);
    $meta = frontiran_render_meta((string)$a['auteur'], (string)$a['temps'], (string)($a['role'] ?? ''));
    $href = htmlspecialchars((string)$a['href'], ENT_QUOTES, 'UTF-8');
    $titre = htmlspecialchars((string)$a['titre'], ENT_QUOTES, 'UTF-8');
    $extrait = htmlspecialchars((string)$a['extrait'], ENT_QUOTES, 'UTF-8');

    return <<<HTML
    <a href="{$href}" class="fi-card-h fi-article-link">
        <div class="fi-card-h__img">{$img}</div>
        <div class="fi-card__body">
            <div>
                {$tag}
                <h3 class="fi-card__title" style="font-size:19px">{$titre}</h3>
                <p class="fi-excerpt">{$extrait}</p>
            </div>
            {$meta}
        </div>
    </a>
    HTML;
}

function frontiran_render_sidebar(array $breves): string
{
    $items = '';
    foreach ($breves as $b) {
        $titre = htmlspecialchars((string)$b['titre'], ENT_QUOTES, 'UTF-8');
        $temps = htmlspecialchars((string)$b['temps'], ENT_QUOTES, 'UTF-8');
        $href = htmlspecialchars((string)$b['href'], ENT_QUOTES, 'UTF-8');
        $items .= <<<HTML
        <a href="{$href}" class="fi-breve fi-article-link">
            <span class="fi-breve__title">{$titre}</span>
            <span class="fi-breve__meta">{$temps}</span>
        </a>
        HTML;
    }

    return <<<HTML
    <aside class="fi-sidebar">
        <div class="fi-sidebar__header">En bref</div>
        {$items}
    </aside>
    HTML;
}

function frontiran_render_grid(array $articles, int $cols, string $imgH = '180px', string $titleSize = '17px'): string
{
    $cards = '';
    foreach ($articles as $a) {
        $cards .= frontiran_render_card($a, $imgH, $titleSize);
    }
    return "<div class=\"fi-grid fi-grid--{$cols}\">{$cards}</div>";
}

function frontiran_render_article_styles(): string
{
    return <<<CSS
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Serif+4:ital,wght@0,300;0,400;0,600;1,300&family=IBM+Plex+Mono:wght@400;500&display=swap');

    :root {
        --fi-red:    #C0392B;
        --fi-ink:    #1A1209;
        --fi-muted:  #6B5B4E;
        --fi-border: rgba(26,18,9,0.12);
        --fi-bg:     #EEEAE4;
    }

    .fi-page {
        width: 100%;
        max-width: 1400px;
        margin: 18px auto 0;
        padding: 32px 24px;
        background: var(--fi-bg);
        font-family: 'Source Serif 4', Georgia, serif;
    }

    .fi-section-label {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: .22em;
        color: var(--fi-red);
        text-transform: uppercase;
        font-weight: 500;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .fi-section-label::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--fi-border);
    }

    .fi-article-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: opacity .15s;
    }
    .fi-article-link:hover { opacity: .93; }

    .fi-img-ph {
        width: 100%;
        background: #D6C9BB;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .fi-img-ph span {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        letter-spacing: .1em;
        color: rgba(26,18,9,.35);
        text-transform: uppercase;
    }

    .fi-tag {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9.5px;
        letter-spacing: .18em;
        text-transform: uppercase;
        font-weight: 500;
        color: var(--fi-red);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .fi-tag__dot {
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--fi-red);
        flex-shrink: 0;
    }

    .fi-meta {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 10px;
        color: var(--fi-muted);
        letter-spacing: .04em;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid var(--fi-border);
    }
    .fi-meta strong { color: var(--fi-ink); font-weight: 500; }

    .fi-excerpt {
        font-size: 13.5px;
        line-height: 1.7;
        color: var(--fi-muted);
        font-style: italic;
    }

    .fi-hero {
        display: grid;
        grid-template-columns: 1fr 380px;
        background: #fff;
        border: 1px solid var(--fi-border);
        margin-bottom: 2px;
    }
    .fi-hero__img { overflow: hidden; }
    .fi-hero__body {
        padding: 28px 28px 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        border-left: 1px solid var(--fi-border);
    }
    .fi-hero__title {
        font-family: 'Playfair Display', serif;
        font-size: 26px;
        font-weight: 900;
        line-height: 1.25;
        color: var(--fi-ink);
        margin-bottom: 12px;
    }

    .fi-grid {
        display: grid;
        gap: 2px;
        margin-bottom: 2px;
    }
    .fi-grid--3 { grid-template-columns: repeat(3, 1fr); }
    .fi-grid--2 { grid-template-columns: repeat(2, 1fr); }

    .fi-card {
        background: #fff;
        border: 1px solid var(--fi-border);
        display: flex;
        flex-direction: column;
    }
    .fi-card__body {
        padding: 18px 18px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .fi-card__title {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        line-height: 1.3;
        color: var(--fi-ink);
        margin-bottom: 8px;
    }

    .fi-card-h {
        background: #fff;
        border: 1px solid var(--fi-border);
        display: grid;
        grid-template-columns: 200px 1fr;
    }
    .fi-card-h__img { overflow: hidden; }

    .fi-split {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2px;
        margin-bottom: 2px;
    }

    .fi-sidebar {
        background: #fff;
        border: 1px solid var(--fi-border);
    }
    .fi-sidebar__header {
        background: var(--fi-ink);
        padding: 12px 16px;
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9.5px;
        letter-spacing: .2em;
        text-transform: uppercase;
        color: rgba(255,255,255,.75);
    }
    .fi-breve {
        padding: 14px 16px;
        border-bottom: 1px solid var(--fi-border);
        display: flex;
        flex-direction: column;
        gap: 5px;
        transition: background .12s;
    }
    .fi-breve:last-child { border-bottom: none; }
    .fi-breve:hover { background: #FAF7F3; }
    .fi-breve__title {
        font-family: 'Playfair Display', serif;
        font-size: 13.5px;
        font-weight: 700;
        line-height: 1.3;
        color: var(--fi-ink);
    }
    .fi-breve__meta {
        font-family: 'IBM Plex Mono', monospace;
        font-size: 9.5px;
        color: var(--fi-muted);
        letter-spacing: .03em;
    }

    @media (max-width: 900px) {
        .fi-hero          { grid-template-columns: 1fr; }
        .fi-hero__body    { border-left: none; border-top: 1px solid var(--fi-border); }
        .fi-grid--3       { grid-template-columns: 1fr 1fr; }
        .fi-split         { grid-template-columns: 1fr; }
        .fi-card-h        { grid-template-columns: 1fr; }
        .fi-card-h__img img,
        .fi-card-h__img .fi-img-ph { height: 200px; }
    }
    @media (max-width: 600px) {
        .fi-grid--3,
        .fi-grid--2       { grid-template-columns: 1fr; }
        .fi-page          { padding: 20px 16px; }
    }
    </style>
    CSS;
}

function frontiran_render_articles_page(array $hero, array $grid3, array $article_h, array $breves, array $grid2): string
{
    $styles = frontiran_render_article_styles();
    $label1 = frontiran_render_section_label('À la une');
    $heroHtml = frontiran_render_hero($hero);
    $grid3Html = frontiran_render_grid($grid3, 3);
    $splitHtml = '<div class="fi-split">' . frontiran_render_card_horizontal($article_h) . frontiran_render_sidebar($breves) . '</div>';
    $label2 = frontiran_render_section_label('Analyses & reportages');
    $grid2Html = frontiran_render_grid($grid2, 2, '220px', '19px');

    return <<<HTML
    {$styles}
    <div class="fi-page">
        {$label1}
        {$heroHtml}
        {$grid3Html}
        {$splitHtml}
        <div style="margin-top:32px">
            {$label2}
            {$grid2Html}
        </div>
    </div>
    HTML;
}