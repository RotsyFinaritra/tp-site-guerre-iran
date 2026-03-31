<?php

require_once __DIR__ . '/../../../app/models/Article.php';
require_once __DIR__ . '/../../../app/models/Category.php';

class ArticleController
{
	private function uploadHeroImageOrNull(string $fileField = 'hero_image_file'): ?string
	{
		if (empty($_FILES[$fileField]) || !is_array($_FILES[$fileField])) {
			return null;
		}

		$upload = $_FILES[$fileField];
		if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
			return null;
		}
		if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
			throw new RuntimeException('Upload échoué.');
		}

		$tmpPath = (string) ($upload['tmp_name'] ?? '');
		if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
			throw new RuntimeException('Upload invalide.');
		}

		$maxBytes = 2 * 1024 * 1024; // 2 MB
		$size = (int) ($upload['size'] ?? 0);
		if ($size <= 0 || $size > $maxBytes) {
			throw new RuntimeException('Image trop lourde (max 2 Mo).');
		}

		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = (string) $finfo->file($tmpPath);
		$allowedMimes = [
			'image/jpeg' => 'jpg',
			'image/png' => 'png',
			'image/webp' => 'webp',
		];
		if (!array_key_exists($mime, $allowedMimes)) {
			throw new RuntimeException('Type d\'image non supporté.');
		}
		if (!extension_loaded('gd')) {
			throw new RuntimeException('Traitement image indisponible (GD manquant).');
		}

		$imgInfo = @getimagesize($tmpPath);
		if (!is_array($imgInfo) || empty($imgInfo[0]) || empty($imgInfo[1])) {
			throw new RuntimeException('Image invalide.');
		}
		$srcW = (int) $imgInfo[0];
		$srcH = (int) $imgInfo[1];
		$maxDim = 6000;
		$maxMegaPixels = 20;
		$megaPixels = ($srcW * $srcH) / 1_000_000;
		if ($srcW <= 0 || $srcH <= 0 || $srcW > $maxDim || $srcH > $maxDim || $megaPixels > $maxMegaPixels) {
			throw new RuntimeException('Dimensions trop grandes (max 6000px et 20MP).');
		}

		switch ($mime) {
			case 'image/jpeg':
				$srcImg = @imagecreatefromjpeg($tmpPath);
				break;
			case 'image/png':
				$srcImg = @imagecreatefrompng($tmpPath);
				break;
			case 'image/webp':
				$srcImg = @imagecreatefromwebp($tmpPath);
				break;
			default:
				$srcImg = false;
		}
		if (!$srcImg) {
			throw new RuntimeException('Impossible de lire l\'image.');
		}

		$maxW = 1600;
		$maxH = 1600;
		$scale = min($maxW / $srcW, $maxH / $srcH, 1);
		$dstW = (int) max(1, floor($srcW * $scale));
		$dstH = (int) max(1, floor($srcH * $scale));

		$dstImg = imagecreatetruecolor($dstW, $dstH);
		imagealphablending($dstImg, false);
		imagesavealpha($dstImg, true);
		$transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
		imagefilledrectangle($dstImg, 0, 0, $dstW, $dstH, $transparent);
		imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

		imagedestroy($srcImg);

		$uploadsDirFs = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads';
		if ($uploadsDirFs === '' || $uploadsDirFs === DIRECTORY_SEPARATOR . 'uploads') {
			imagedestroy($dstImg);
			throw new RuntimeException('Chemin uploads invalide.');
		}
		if (!is_dir($uploadsDirFs) && !@mkdir($uploadsDirFs, 0775, true)) {
			imagedestroy($dstImg);
			throw new RuntimeException('Impossible de créer le dossier uploads.');
		}

		$quality = 78;
		$fileName = bin2hex(random_bytes(16)) . '.webp';
		$destFs = $uploadsDirFs . DIRECTORY_SEPARATOR . $fileName;
		if (!imagewebp($dstImg, $destFs, $quality)) {
			imagedestroy($dstImg);
			throw new RuntimeException('Impossible d\'enregistrer l\'image.');
		}

		imagedestroy($dstImg);
		@chmod($destFs, 0644);

		return '/uploads/' . $fileName;
	}

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

	private function normalizeUploadsInHtml(string $html): string
	{
		if ($html === '') {
			return $html;
		}

		// Ensure we persist root-relative URLs for local uploads (portable across localhost/prod).
		// Example: src="http://localhost:8080/uploads/x.webp" => src="/uploads/x.webp"
		$normalized = preg_replace(
			'~(<img\\b[^>]*\\ssrc=["\'])(?:https?:)?//[^"\']+(/uploads/[^"\']+)(["\'])~i',
			'$1$2$3',
			$html
		);

		return is_string($normalized) ? $normalized : $html;
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
			'hero_image_path' => null,
			'hero_image_alt' => null,
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
		$contentHtml = $this->normalizeUploadsInHtml($this->postString('content_html'));
		$heroImageAlt = $this->postNullableString('hero_image_alt');
		$status = $this->postString('status');

		$errors = [];

		$heroImagePath = null;
		try {
			$heroImagePath = $this->uploadHeroImageOrNull('hero_image_file');
		} catch (Throwable $e) {
			$errors[] = $e->getMessage();
		}
		if ($heroImagePath === null) {
			$errors[] = "L'image hero est requise.";
		}

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
				'hero_image_path' => null,
				'hero_image_alt' => $heroImageAlt,
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
			'hero_image_path' => $heroImagePath,
			'hero_image_alt' => $heroImageAlt,
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
		$contentHtml = $this->normalizeUploadsInHtml($this->postString('content_html'));
		$heroImageAlt = $this->postNullableString('hero_image_alt');
		$status = $this->postString('status');

		$errors = [];

		$heroImagePath = null;
		try {
			$heroImagePath = $this->uploadHeroImageOrNull('hero_image_file');
		} catch (Throwable $e) {
			$errors[] = $e->getMessage();
		}
		if ($heroImagePath === null && empty($current['hero_image_path'])) {
			$errors[] = "L'image hero est requise.";
		}
		$heroImagePathFinal = $heroImagePath !== null ? $heroImagePath : (string)($current['hero_image_path'] ?? '');

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
				'hero_image_path' => $heroImagePathFinal,
				'hero_image_alt' => $heroImageAlt,
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
			'hero_image_path' => $heroImagePathFinal !== '' ? $heroImagePathFinal : null,
			'hero_image_alt' => $heroImageAlt,
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
