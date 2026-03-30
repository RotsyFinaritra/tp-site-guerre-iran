<?php

require_once __DIR__ . '/../../../app/models/User.php';

class AuthController
{
	private function ensureSession(): void
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			@session_start();
		}
	}

	public function showLogin(string $error = ''): void
	{
		$this->ensureSession();
		$loginError = $error;
		require __DIR__ . '/../views/auth/login.php';
	}

	public function login(): void
	{
		$this->ensureSession();

		$username = trim($_POST['username'] ?? '');
		$password = $_POST['password'] ?? '';

		if ($username === '' || $password === '') {
			$this->showLogin('Veuillez saisir un identifiant et un mot de passe.');
			return;
		}

		try {
			$user = User::verifyCredentials($username, $password);
			if ($user) {
				$_SESSION['admin'] = [
					'id'       => $user['id'],
					'username' => $user['username'],
				];
				header('Location: /admin/');
				exit;
			}
			$this->showLogin('Identifiants incorrects. Merci de vérifier vos accès.');
		} catch (Throwable $e) {
			$this->showLogin("Erreur de connexion au système. Contactez l'administrateur.");
		}
	}

	public function logout(): void
	{
		$this->ensureSession();
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
}

