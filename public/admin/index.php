<?php
session_start();

require_once __DIR__ . '/../../app/models/User.php';

$error = '';

// Déconnexion simple: /admin/index.php?action=logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params['path'], $params['domain'],
			$params['secure'], $params['httponly']
		);
	}
	session_destroy();
	header('Location: /admin/');
	exit;
}

// Si déjà connecté, afficher un petit tableau de bord en utilisant le layout BO
if (isset($_SESSION['admin']) && is_array($_SESSION['admin'])) {
	$currentUser = $_SESSION['admin'];
	$route = (string) ($_GET['r'] ?? 'dashboard');

	require_once __DIR__ . '/controllers/ArticleController.php';
	$controller = new ArticleController();

	switch ($route) {
		case 'articles':
			$controller->index();
			exit;
		case 'articles_create':
			$controller->create();
			exit;
		case 'articles_store':
			$controller->store();
			exit;
		case 'articles_edit':
			$id = (int) ($_GET['id'] ?? 0);
			$controller->edit($id);
			exit;
		case 'articles_update':
			$id = (int) ($_GET['id'] ?? 0);
			$controller->update($id);
			exit;
		case 'articles_delete':
			$id = (int) ($_GET['id'] ?? 0);
			$controller->delete($id);
			exit;
		default:
			// dashboard
			break;
	}

	$pageTitle   = 'Backoffice – Tableau de bord';
	$pageHeading = 'Tableau de bord – Iran Correspondent';

	ob_start();
	?>
	<p class="mb-3">Bienvenue dans le backoffice. Vous pourrez ici gérer les articles, les catégories et le contenu éditorial lié à la guerre en Iran.</p>
	<ul class="mb-4">
		<li>Accès à la gestion des articles (création, édition, suppression).</li>
		<li>Organisation des catégories éditoriales.</li>
		<li>Suivi rapide du statut des contenus (brouillon / publié).</li>
	</ul>
	<a href="/admin/index.php?action=logout" class="btn btn-outline-light btn-sm">Se déconnecter</a>
	<?php
	$content = ob_get_clean();
	require __DIR__ . '/views/layouts/main.php';
	exit;
}

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$password = $_POST['password'] ?? '';

	if ($username === '' || $password === '') {
		$error = 'Veuillez saisir un identifiant et un mot de passe.';
	} else {
		try {
			$user = User::verifyCredentials($username, $password);
			if ($user) {
				$_SESSION['admin'] = [
					'id' => $user['id'],
					'username' => $user['username'],
				];
				header('Location: /admin/');
				exit;
			}
			$error = 'Identifiants incorrects. Merci de vérifier vos accès.';
		} catch (Throwable $e) {
			$error = "Erreur de connexion au système. Contactez l'administrateur.";
		}
	}
}

// Affichage du formulaire de login (vue dédiée)
$loginError = $error;
require __DIR__ . '/views/auth/login.php';
