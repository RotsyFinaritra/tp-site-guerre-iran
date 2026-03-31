<?php

session_start();

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/ArticleController.php';
require_once __DIR__ . '/controllers/CategoryController.php';

require_once __DIR__ . '/../../app/models/User.php';

$authController = new AuthController();

// Gestion de la déconnexion
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
	$authController->logout();
}

$isLoggedIn = isset($_SESSION['admin']) && is_array($_SESSION['admin']);

// Mode démo: accès direct au backoffice quand on vient de la navbar (Admin -> ?demo=1)
// Ne s'active pas sans ce paramètre.
$demo = isset($_GET['demo']) && (string) $_GET['demo'] === '1';
if (!$isLoggedIn && $demo) {
	$demoUser = null;
	try {
		$demoUser = User::findById(1);
		if (!$demoUser) {
			$demoUser = User::findByUsername('admin');
		}
	} catch (Throwable $e) {
		$demoUser = null;
	}

	if (is_array($demoUser) && isset($demoUser['id'], $demoUser['username'])) {
		$_SESSION['admin'] = [
			'id' => (int) $demoUser['id'],
			'username' => (string) $demoUser['username'],
		];
		$isLoggedIn = true;
	} else {
		// Fallback (si DB vide): session minimale pour accéder au BO en mode démo.
		$_SESSION['admin'] = ['id' => 0, 'username' => 'demo'];
		$isLoggedIn = true;
	}
}

// Si l'utilisateur n'est pas connecté, on délègue à l'AuthController
if (!$isLoggedIn) {
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$authController->login();
	} else {
		$authController->showLogin();
	}
	exit;
}

// Utilisateur connecté : route vers les contrôleurs du backoffice
$currentUser = $_SESSION['admin'];
$route       = isset($_GET['r']) ? (string) $_GET['r'] : 'dashboard';

$articleController  = new ArticleController();
$categoryController = new CategoryController();

switch ($route) {
	case 'articles':
		$articleController->index();
		break;
	case 'articles_create':
		$articleController->create();
		break;
	case 'articles_store':
		$articleController->store();
		break;
	case 'articles_edit':
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$articleController->edit($id);
		break;
	case 'articles_update':
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$articleController->update($id);
		break;
	case 'articles_delete':
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$articleController->delete($id);
		break;
	case 'categories':
		$categoryController->index();
		break;
	case 'categories_create':
		$categoryController->create();
		break;
	case 'categories_store':
		$categoryController->store();
		break;
	case 'categories_edit':
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$categoryController->edit($id);
		break;
	case 'categories_update':
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$categoryController->update($id);
		break;
	case 'categories_delete':
		$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
		$categoryController->delete($id);
		break;
	default:
		// Dashboard simple
		$pageTitle   = 'Backoffice – Tableau de bord';
		$pageHeading = 'Tableau de bord – Iran Correspondent';

		// Petites statistiques BO
		$totalArticles   = Article::countForAdmin([]);
		$totalPublished  = Article::countForAdmin(['status' => 'published']);
		$totalDrafts     = Article::countForAdmin(['status' => 'draft']);
		$totalCategories = count(Category::all());
		$latestArticles  = Article::listForAdmin([], 5, 0);

		ob_start();
		require __DIR__ . '/views/dashboard.php';
		$content = ob_get_clean();
		require __DIR__ . '/views/layouts/main.php';
}

exit;
