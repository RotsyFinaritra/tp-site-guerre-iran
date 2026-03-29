<?php

require_once __DIR__ . '/../../../app/models/Article.php';
require_once __DIR__ . '/../../../app/models/Category.php';

class ArticleController
{
	private function requireAuth(): array
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			// In normal flow, admin/index.php already session_start()'d.
			@session_start();
		}

		if (!isset($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
			header('Location: /admin/');
			exit;
		}

		return $_SESSION['admin'];
	}

	private function getInt(string $key, int $default): int
	{
		$val = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT);
		if ($val === false || $val === null) {
			return $default;
		}
		return $val;
	}

	private function getDate(string $key): ?string
	{
		$val = (string) ($_GET[$key] ?? '');
		$val = trim($val);
		if ($val === '') {
			return null;
		}
		// Expect YYYY-MM-DD
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
			return null;
		}
		return $val;
	}

	private function getStatus(): ?string
	{
		$status = (string) ($_GET['status'] ?? '');
		$status = trim($status);
		if ($status === '') {
			return null;
		}
		if (!in_array($status, ['draft', 'published'], true)) {
			return null;
		}
		return $status;
	}

	private function getCategoryId(): ?int
	{
		$val = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
		if ($val === false || $val === null) {
			return null;
		}
		return $val > 0 ? $val : null;
	}

	private function postString(string $key): string
	{
		return trim((string) ($_POST[$key] ?? ''));
	}

	private function postNullableString(string $key): ?string
	{
		$val = trim((string) ($_POST[$key] ?? ''));
		return $val === '' ? null : $val;
	}

	private function postInt(string $key): ?int
	{
		$val = filter_input(INPUT_POST, $key, FILTER_VALIDATE_INT);
		if ($val === false || $val === null) {
			return null;
		}
		return $val;
	}

	public function index(): void
	{
		$currentUser = $this->requireAuth();

		$pageSize = 10;
		$page = max(1, $this->getInt('page', 1));

		$filters = [
			'status' => $this->getStatus(),
			'category_id' => $this->getCategoryId(),
			'from' => $this->getDate('from'),
			'to' => $this->getDate('to'),
		];

		$categories = Category::all();

		$total = Article::countForAdmin($filters);
		$totalPages = max(1, (int) ceil($total / $pageSize));
		if ($page > $totalPages) {
			$page = $totalPages;
		}

		$offset = ($page - 1) * $pageSize;
		$articles = Article::listForAdmin($filters, $pageSize, $offset);

		$pagination = [
			'page' => $page,
			'pageSize' => $pageSize,
			'total' => $total,
			'totalPages' => $totalPages,
		];

		$pageTitle = 'Backoffice – Articles';
		$pageHeading = 'Articles';

		ob_start();
		require __DIR__ . '/../views/articles/index.php';
		$content = ob_get_clean();

		require __DIR__ . '/../views/layouts/main.php';
	}

	public function create(): void
	{
		$currentUser = $this->requireAuth();
		$categories = Category::all();
		$article = [
			'category_id' => null,
			'title' => '',
			'slug' => '',
			'excerpt' => '',
			'content_html' => '',
			'status' => 'draft',
		];
		$errors = [];

		$pageTitle = 'Backoffice – Ajouter un article';
		$pageHeading = 'Ajouter un article';

		ob_start();
		require __DIR__ . '/../views/articles/form.php';
		$content = ob_get_clean();
		require __DIR__ . '/../views/layouts/main.php';
	}

	public function store(): void
	{
		$this->requireAuth();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/articles/create');
			exit;
		}

		$categoryId = $this->postInt('category_id');
		$title = $this->postString('title');
		$slug = $this->postString('slug');
		$excerpt = $this->postNullableString('excerpt');
		$contentHtml = $this->postString('content_html');
		$status = $this->postString('status');

		$errors = [];
		if (!$categoryId) {
			$errors[] = 'Veuillez choisir une catégorie.';
		}
		if ($title === '' || mb_strlen($title) < 3) {
			$errors[] = 'Titre invalide (minimum 3 caractères).';
		}
		if ($slug === '' || mb_strlen($slug) < 3) {
			$errors[] = 'Slug invalide (minimum 3 caractères).';
		}
		if ($contentHtml === '') {
			$errors[] = 'Le contenu est requis.';
		}
		if (!in_array($status, ['draft', 'published'], true)) {
			$errors[] = 'Statut invalide.';
		}

		if ($errors !== []) {
			$currentUser = $_SESSION['admin'];
			$categories = Category::all();
			$article = [
				'category_id' => $categoryId,
				'title' => $title,
				'slug' => $slug,
				'excerpt' => $excerpt,
				'content_html' => $contentHtml,
				'status' => $status,
			];
			$pageTitle = 'Backoffice – Ajouter un article';
			$pageHeading = 'Ajouter un article';
			ob_start();
			require __DIR__ . '/../views/articles/form.php';
			$content = ob_get_clean();
			require __DIR__ . '/../views/layouts/main.php';
			exit;
		}

		Article::create([
			'category_id' => $categoryId,
			'title' => $title,
			'slug' => $slug,
			'excerpt' => $excerpt,
			'content_html' => $contentHtml,
			'status' => $status,
		]);

		header('Location: /admin/articles');
		exit;
	}

	public function edit(int $id): void
	{
		$currentUser = $this->requireAuth();
		$article = Article::findById($id, true);
		if (!$article) {
			header('Location: /admin/articles');
			exit;
		}

		$categories = Category::all();
		$errors = [];

		$pageTitle = 'Backoffice – Éditer un article';
		$pageHeading = 'Éditer un article';

		ob_start();
		require __DIR__ . '/../views/articles/form.php';
		$content = ob_get_clean();
		require __DIR__ . '/../views/layouts/main.php';
	}

	public function update(int $id): void
	{
		$this->requireAuth();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/articles');
			exit;
		}

		$current = Article::findById($id, true);
		if (!$current) {
			header('Location: /admin/articles');
			exit;
		}

		$categoryId = $this->postInt('category_id');
		$title = $this->postString('title');
		$slug = $this->postString('slug');
		$excerpt = $this->postNullableString('excerpt');
		$contentHtml = $this->postString('content_html');
		$status = $this->postString('status');

		$errors = [];
		if (!$categoryId) {
			$errors[] = 'Veuillez choisir une catégorie.';
		}
		if ($title === '' || mb_strlen($title) < 3) {
			$errors[] = 'Titre invalide (minimum 3 caractères).';
		}
		if ($slug === '' || mb_strlen($slug) < 3) {
			$errors[] = 'Slug invalide (minimum 3 caractères).';
		}
		if ($contentHtml === '') {
			$errors[] = 'Le contenu est requis.';
		}
		if (!in_array($status, ['draft', 'published'], true)) {
			$errors[] = 'Statut invalide.';
		}

		if ($errors !== []) {
			$currentUser = $_SESSION['admin'];
			$categories = Category::all();
			$article = array_merge($current, [
				'category_id' => $categoryId,
				'title' => $title,
				'slug' => $slug,
				'excerpt' => $excerpt,
				'content_html' => $contentHtml,
				'status' => $status,
			]);
			$pageTitle = 'Backoffice – Éditer un article';
			$pageHeading = 'Éditer un article';
			ob_start();
			require __DIR__ . '/../views/articles/form.php';
			$content = ob_get_clean();
			require __DIR__ . '/../views/layouts/main.php';
			exit;
		}

		Article::update($id, [
			'category_id' => $categoryId,
			'title' => $title,
			'slug' => $slug,
			'excerpt' => $excerpt,
			'content_html' => $contentHtml,
			'status' => $status,
		]);

		header('Location: /admin/articles');
		exit;
	}

	public function delete(int $id): void
	{
		$this->requireAuth();
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			header('Location: /admin/articles');
			exit;
		}

		Article::softDelete($id);
		header('Location: /admin/articles');
		exit;
	}
}
