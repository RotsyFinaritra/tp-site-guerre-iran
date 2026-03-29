<?php
/**
 * Layout principal du backoffice – Iran Correspondent
 * Variables attendues :
 *   string      $pageTitle
 *   string      $pageHeading
 *   string      $content      (HTML déjà généré)
 *   array|null  $currentUser  (optionnel, avec 'username')
 */
$pageTitle   = $pageTitle   ?? 'Backoffice – Iran Correspondent';
$pageHeading = $pageHeading ?? 'Backoffice';
$currentUser = $currentUser ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet"/>

    <style>
        /* ══════════════════════════════════════
           TOKENS
        ══════════════════════════════════════ */
        :root {
            --navy:       #0a1628;
            --navy-mid:   #0f2044;
            --navy-soft:  #162952;
            --navy-card:  #111d3a;
            --white:      #ffffff;
            --off-white:  #e8edf5;
            --silver:     #c4cfe3;
            --muted:      #8a9bb8;
            --border:     rgba(255,255,255,.09);
            --border-md:  rgba(255,255,255,.14);
            --sidebar-w:  260px;
            --tr:         .18s cubic-bezier(.4,0,.2,1);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f5f6fb; /* fond global clair */
            color: #111827;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           SHELL
        ══════════════════════════════════════ */
        .bo-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════
           SIDEBAR
        ══════════════════════════════════════ */
        .bo-sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: linear-gradient(180deg, #0a1628 0%, #070f1d 100%);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            overflow: hidden;
        }

        /* halo décoratif haut-gauche */
        .bo-sidebar::after {
            content: '';
            position: absolute;
            top: -100px; left: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(30,58,110,.45) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ligne lumineuse en haut */
        .bo-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 1.5rem; right: 1.5rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.22), transparent);
            z-index: 1;
        }

        /* ── Brand ── */
        .bo-brand {
            padding: 1.6rem 1.5rem 1.4rem;
            border-bottom: 1px solid var(--border);
            position: relative; z-index: 1;
        }

        .bo-brand-logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.05rem;
            font-weight: 900;
            color: var(--white);
            letter-spacing: -.01em;
            line-height: 1.2;
        }

        .bo-brand-logo span { color: var(--muted); font-weight: 700; }

        .bo-brand-pill {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            margin-top: .55rem;
            font-size: .58rem;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--navy);
            background: var(--white);
            padding: .2rem .65rem;
            border-radius: 2px;
        }

        /* ── Nav section ── */
        .bo-nav-section {
            padding: 1.25rem 1.25rem .75rem;
            flex: 1;
            position: relative; z-index: 1;
        }

        .bo-nav-label {
            font-size: .58rem;
            font-weight: 600;
            letter-spacing: .25em;
            text-transform: uppercase;
            color: rgba(138,155,184,.45);
            padding-left: .5rem;
            margin-bottom: .5rem;
        }

        .bo-nav {
            list-style: none;
            padding: 0; margin: 0;
        }

        .bo-nav li { margin-bottom: .15rem; }

        .bo-nav li a {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem .75rem;
            border-radius: 4px;
            font-size: .82rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            position: relative;
            transition: background var(--tr), color var(--tr), padding-left var(--tr);
        }

        .bo-nav li a .ni {
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 3px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.07);
            font-size: .85rem;
            flex-shrink: 0;
            transition: background var(--tr), border-color var(--tr), color var(--tr);
        }

        .bo-nav li a:hover {
            background: rgba(255,255,255,.05);
            color: var(--white);
            padding-left: 1rem;
        }

        .bo-nav li a:hover .ni {
            background: rgba(255,255,255,.09);
            border-color: rgba(255,255,255,.12);
        }

        .bo-nav li a.active {
            background: rgba(255,255,255,.07);
            color: var(--white);
        }

        /* barre active à gauche */
        .bo-nav li a.active::before {
            content: '';
            position: absolute;
            left: 0; top: 22%; bottom: 22%;
            width: 2px;
            background: var(--white);
            border-radius: 0 2px 2px 0;
        }

        /* icône inversée quand active */
        .bo-nav li a.active .ni {
            background: var(--white);
            border-color: transparent;
            color: var(--navy);
        }

        /* ── Divider ── */
        .bo-divider {
            margin: 0 1.25rem;
            border: none;
            border-top: 1px solid var(--border);
        }

        /* ── Footer / user ── */
        .bo-sidebar-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid var(--border);
            position: relative; z-index: 1;
        }

        .bo-user {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .bo-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--navy-soft);
            border: 1px solid rgba(255,255,255,.14);
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
            color: var(--muted);
            flex-shrink: 0;
        }

        .bo-user-name {
            font-size: .82rem;
            font-weight: 600;
            color: var(--white);
            line-height: 1.2;
        }

        .bo-user-role {
            font-size: .63rem;
            color: var(--muted);
            letter-spacing: .05em;
            margin-top: .1rem;
        }

        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            width: 100%;
            background: none;
            border: 1px solid rgba(255,255,255,.11);
            border-radius: 3px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: .7rem;
            font-weight: 600;
            letter-spacing: .16em;
            text-transform: uppercase;
            padding: .5rem;
            text-decoration: none;
            cursor: pointer;
            transition: all var(--tr);
        }

        .btn-logout:hover {
            border-color: rgba(255,255,255,.28);
            color: var(--white);
            background: rgba(255,255,255,.04);
        }

        /* ══════════════════════════════════════
           MAIN CONTENT
        ══════════════════════════════════════ */
        .bo-main {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            background: #f5f6fb; /* zone principale claire */
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .bo-topbar {
            height: 56px;
            background: rgba(10,22,40,.9);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            position: sticky; top: 0; z-index: 40;
        }

        .bo-breadcrumb {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .72rem;
            color: var(--muted);
        }

        .bo-breadcrumb .sep { opacity: .4; }
        .bo-breadcrumb .current { color: var(--white); font-weight: 600; }

        .bo-topbar-actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .topbar-btn {
            width: 34px; height: 34px;
            border: 1px solid var(--border);
            border-radius: 3px;
            background: none;
            color: var(--muted);
            display: flex; align-items: center; justify-content: center;
            font-size: .95rem;
            cursor: pointer;
            transition: all var(--tr);
        }

        .topbar-btn:hover {
            color: var(--white);
            background: rgba(255,255,255,.05);
            border-color: var(--border-md);
        }

        /* ── Content area ── */
        .bo-content {
            flex: 1;
            padding: 2rem;
        }

        /* ── Page header ── */
        .bo-page-header {
            margin-bottom: 1.75rem;
        }

        .bo-page-eyebrow {
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .28em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .4rem;
        }

        .bo-page-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.65rem;
            font-weight: 900;
            color: #111827; /* titre foncé sur fond clair */
            line-height: 1.15;
        }

        .bo-page-sub {
            font-size: .78rem;
            color: #6b7280;
            margin-top: .3rem;
        }

        /* ── Card ── */
        .bo-card {
            background: #ffffff; /* carte blanche */
            border: 1px solid rgba(15,32,68,.08);
            border-radius: 6px;
            padding: 1.75rem 2rem;
            position: relative;
            overflow: hidden;
        }

        /* Ligne décorative en haut de la card */
        .bo-card::before {
            content: '';
            position: absolute;
            top: 0; left: 2rem; right: 2rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(15,32,68,.25), transparent);
        }

        /* Styles pour le $content injecté */
        .bo-card p {
            font-size: .9rem;
            color: #111827;
            line-height: 1.7;
            margin-bottom: .75rem;
        }

        .bo-card ul {
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }

        .bo-card ul li {
            font-size: .88rem;
            color: #374151;
            line-height: 1.7;
            margin-bottom: .3rem;
        }

        .bo-card h2, .bo-card h3 {
            font-family: 'Playfair Display', serif;
            color: #111827;
            margin-bottom: .75rem;
        }

        .bo-card table {
            width: 100%;
            border-collapse: collapse;
            font-size: .82rem;
        }

        .bo-card table th {
            font-size: .62rem;
            font-weight: 600;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: #6b7280;
            padding: .6rem 1rem;
            border-bottom: 1px solid rgba(15,32,68,.08);
            text-align: left;
        }

        .bo-card table td {
            padding: .85rem 1rem;
            color: #111827;
            border-bottom: 1px solid rgba(15,32,68,.06);
            vertical-align: middle;
        }

        .bo-card table tr:last-child td { border-bottom: none; }
        .bo-card table tr:hover td { background: rgba(15,32,68,.03); }

        /* Badges/status dans le contenu */
        .badge-published {
            font-size: .6rem; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            padding: .2rem .55rem; border-radius: 99px;
            background: rgba(111,207,151,.12); color: #6fcf97;
        }
        .badge-draft {
            font-size: .6rem; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            padding: .2rem .55rem; border-radius: 99px;
            background: rgba(138,155,184,.1); color: var(--muted);
        }

        /* Boutons dans le contenu */
        .bo-card .btn-primary-dark {
            display: inline-flex; align-items: center; gap: .4rem;
            background: var(--white); color: var(--navy);
            border: none; border-radius: 3px;
            font-family: 'DM Sans', sans-serif;
            font-size: .72rem; font-weight: 600;
            letter-spacing: .16em; text-transform: uppercase;
            padding: .55rem 1.1rem; cursor: pointer;
            text-decoration: none;
            transition: background var(--tr), transform var(--tr);
        }
        .bo-card .btn-primary-dark:hover {
            background: var(--off-white);
            transform: translateY(-1px);
        }

        .bo-card .btn-ghost {
            display: inline-flex; align-items: center; gap: .4rem;
            background: none;
            border: 1px solid var(--border-md);
            border-radius: 3px;
            color: var(--muted);
            font-family: 'DM Sans', sans-serif;
            font-size: .72rem; font-weight: 600;
            letter-spacing: .16em; text-transform: uppercase;
            padding: .5rem 1rem; cursor: pointer;
            text-decoration: none;
            transition: all var(--tr);
        }
        .bo-card .btn-ghost:hover { color: var(--white); border-color: rgba(255,255,255,.3); }

        /* ══════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════ */
        @media (max-width: 768px) {
            .bo-sidebar {
                position: relative;
                width: 100%;
                flex-direction: row;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            .bo-sidebar::after { display: none; }
            .bo-brand { padding: .75rem 1rem; border-bottom: none; border-right: 1px solid var(--border); }
            .bo-nav-section { padding: .5rem .75rem; flex: 1; }
            .bo-nav-label { display: none; }
            .bo-nav { display: flex; gap: .2rem; }
            .bo-nav li a { padding: .5rem .6rem; font-size: .75rem; }
            .bo-nav li a span:not(.ni) { display: none; }
            .bo-divider { display: none; }
            .bo-sidebar-footer { padding: .5rem 1rem; border-top: none; border-left: 1px solid var(--border); }
            .bo-user-name, .bo-user-role { display: none; }
            .bo-user { margin-bottom: 0; }
            .btn-logout span { display: none; }
            .btn-logout { width: auto; padding: .45rem .65rem; }
            .bo-main { margin-left: 0; }
            .bo-shell { flex-direction: column; }
            .bo-content { padding: 1.25rem; }
        }
    </style>
</head>
<body>
<div class="bo-shell">

    <!-- ══════════════════════════════════════
         SIDEBAR
    ══════════════════════════════════════ -->
    <aside class="bo-sidebar">

        <!-- Brand -->
        <div class="bo-brand">
            <div class="bo-brand-logo">Iran <span>Correspondent</span></div>
            <div class="bo-brand-pill">
                <i class="bi bi-grid-1x2-fill"></i> Backoffice
            </div>
        </div>

        <!-- Navigation -->
        <div class="bo-nav-section">
            <div class="bo-nav-label">Navigation</div>
            <ul class="bo-nav">
                <li>
                    <a href="/admin/" class="active">
                        <span class="ni"><i class="bi bi-house"></i></span>
                        Tableau de bord
                    </a>
                </li>
                <li>
                    <a href="/admin/articles">
                        <span class="ni"><i class="bi bi-file-earmark-text"></i></span>
                        Articles
                    </a>
                </li>
                <li>
                    <a href="/admin/categories">
                        <span class="ni"><i class="bi bi-tag"></i></span>
                        Catégories
                    </a>
                </li>
            </ul>
        </div>

        <hr class="bo-divider"/>

        <!-- Utilisateur -->
        <div class="bo-sidebar-footer">
            <?php if ($currentUser): ?>
            <div class="bo-user">
                <div class="bo-avatar"><i class="bi bi-person"></i></div>
                <div>
                    <div class="bo-user-name"><?= htmlspecialchars($currentUser['username'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="bo-user-role">Rédacteur</div>
                </div>
            </div>
            <?php endif; ?>
            <a href="/admin/index.php?action=logout" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Déconnexion</span>
            </a>
        </div>

    </aside>

    <!-- ══════════════════════════════════════
         MAIN
    ══════════════════════════════════════ -->
    <main class="bo-main">

        <!-- Topbar -->
        <div class="bo-topbar">
            <nav class="bo-breadcrumb">
                <span>Iran Correspondent</span>
                <span class="sep">›</span>
                <span>Backoffice</span>
                <span class="sep">›</span>
                <span class="current"><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></span>
            </nav>
            <div class="bo-topbar-actions">
                <button class="topbar-btn" title="Notifications">
                    <i class="bi bi-bell"></i>
                </button>
                <button class="topbar-btn" title="Paramètres">
                    <i class="bi bi-gear"></i>
                </button>
            </div>
        </div>

        <!-- Contenu -->
        <div class="bo-content">

            <!-- En-tête de page -->
            <div class="bo-page-header">
                <div class="bo-page-eyebrow">Backoffice · Gestion interne</div>
                <h1 class="bo-page-heading"><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h1>
                <div class="bo-page-sub">Interface de gestion éditoriale — Iran Correspondent</div>
            </div>

            <!-- Card principale : contenu dynamique injecté -->
            <div class="bo-card">
                <?= $content ?>
            </div>

        </div>
    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Marquer le lien actif selon l'URL courante
    document.querySelectorAll('.bo-nav li a').forEach(link => {
        if (link.getAttribute('href') === window.location.pathname) {
            document.querySelectorAll('.bo-nav li a').forEach(a => a.classList.remove('active'));
            link.classList.add('active');
        }
    });
</script>
</body>
</html>