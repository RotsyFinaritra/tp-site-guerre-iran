<?php

require_once __DIR__ . '/../../../app/models/Category.php';

class CategoryController
{
	private function requireAuth(): array
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			@session_start();
		}

		if (!isset($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
			header('Location: /admin/');
			exit;
		}

		return $_SESSION['admin'];
	}

	private function postString(string $key): string
	{
		return trim((string) ($_POST[$key] ?? ''));
	}

	public function index(): void
	{
		$currentUser = $this->requireAuth();
		$categories  = Category::all();

		// Messages de feedback simples via ?success=
		$success = '';
		$error   = '';
		if (isset($_GET['success'])) {
			switch ($_GET['success']) {
				case 'created':
					$success = 'Catégorie créée avec succès.';
					break;
				case 'updated':
					$success = 'Catégorie mise à jour avec succès.';
					break;
				case 'deleted':
					$success = 'Catégorie supprimée avec succès.';
					break;
			}
		}

		$pageTitle   = 'Backoffice – Catégories';
		$pageHeading = 'Catégories';

		ob_start();
		require __DIR__ . '/../views/categories/index.php';
		$content = ob_get_clean();

		require __DIR__ . '/../views/layouts/main.php';
	}

	public function create(): void
	{
		$currentUser = $this->requireAuth();
		$category = [
			'name' => '',
			'slug' => '',
		];
		$errors = [];
		$formAction = '/admin/?r=categories_store';

		$pageTitle   = 'Backoffice – Nouvelle catégorie';
		$pageHeading = 'Nouvelle catégorie';

		ob_start();
		require __DIR__ . '/../views/categories/form.php';
		$content = ob_get_clean();
		require __DIR__ . '/../views/layouts/main.php';
	}

	public function store(): void
	{
		$this->requireAuth();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/?r=categories_create');
			return;
		}

		$name = $this->postString('name');
		$slug = $this->postString('slug');

		$errors = [];
		if ($name === '') {
			$errors[] = 'Le nom de la catégorie est obligatoire.';
		}

		if ($slug === '' && $name !== '') {
			$slug = $this->slugify($name);
		}

		if ($errors !== []) {
			$currentUser = $_SESSION['admin'];
			$category = ['name' => $name, 'slug' => $slug];
			$formAction = '/admin/?r=categories_store';
			$pageTitle   = 'Backoffice – Nouvelle catégorie';
			$pageHeading = 'Nouvelle catégorie';
			ob_start();
			require __DIR__ . '/../views/categories/form.php';
			$content = ob_get_clean();
			require __DIR__ . '/../views/layouts/main.php';
			return;
		}

		Category::create($name, $slug);
		header('Location: /admin/?r=categories&success=created');
		exit;
	}

	public function edit(int $id): void
	{
		$currentUser = $this->requireAuth();
		$category = Category::findById($id);
		if (!$category) {
			header('Location: /admin/?r=categories');
			return;
		}

		$errors = [];
		$formAction = '/admin/?r=categories_update&id=' . (int) $id;
		$pageTitle   = 'Backoffice – Modifier une catégorie';
		$pageHeading = 'Modifier une catégorie';

		ob_start();
		require __DIR__ . '/../views/categories/form.php';
		$content = ob_get_clean();
		require __DIR__ . '/../views/layouts/main.php';
	}

	public function update(int $id): void
	{
		$this->requireAuth();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/?r=categories');
			return;
		}

		$category = Category::findById($id);
		if (!$category) {
			header('Location: /admin/?r=categories');
			return;
		}

		$name = $this->postString('name');
		$slug = $this->postString('slug');

		$errors = [];
		if ($name === '') {
			$errors[] = 'Le nom de la catégorie est obligatoire.';
		}
		if ($slug === '' && $name !== '') {
			$slug = $this->slugify($name);
		}

		if ($errors !== []) {
			$currentUser = $_SESSION['admin'];
			$category = array_merge($category, ['name' => $name, 'slug' => $slug]);
			$formAction = '/admin/?r=categories_update&id=' . (int) $id;
			$pageTitle   = 'Backoffice – Modifier une catégorie';
			$pageHeading = 'Modifier une catégorie';
			ob_start();
			require __DIR__ . '/../views/categories/form.php';
			$content = ob_get_clean();
			require __DIR__ . '/../views/layouts/main.php';
			return;
		}

		Category::update($id, ['name' => $name, 'slug' => $slug]);
		header('Location: /admin/?r=categories&success=updated');
		exit;
	}

	public function delete(int $id): void
	{
		$this->requireAuth();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/?r=categories');
			return;
		}

		Category::delete($id);
		header('Location: /admin/?r=categories&success=deleted');
		exit;
	}

	private function slugify(string $text): string
	{
		$text = trim(mb_strtolower($text, 'UTF-8'));
		$text = strtr($text, [
			'à' => 'a', 'â' => 'a', 'ä' => 'a',
			'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
			'î' => 'i', 'ï' => 'i',
			'ô' => 'o', 'ö' => 'o',
			'ù' => 'u', 'û' => 'u', 'ü' => 'u',
			'ç' => 'c',
		]);
		$text = preg_replace('~[^a-z0-9]+~u', '-', $text);
		$text = trim($text, '-');
		return $text !== '' ? $text : 'categorie';
	}
}
