<?php

declare(strict_types=1);

// TinyMCE expects JSON responses.
header('Content-Type: application/json; charset=utf-8');

// Only allow POST.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
	http_response_code(405);
	echo json_encode(['error' => 'Method not allowed']);
	exit;
}

// Admin auth (same session flag as the rest of the backoffice).
if (session_status() !== PHP_SESSION_ACTIVE) {
	@session_start();
}

if (!isset($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
	http_response_code(403);
	echo json_encode(['error' => 'Forbidden']);
	exit;
}

// File input name used by TinyMCE: "file".
if (empty($_FILES['file']) || !is_array($_FILES['file'])) {
	http_response_code(400);
	echo json_encode(['error' => 'Missing file']);
	exit;
}

$upload = $_FILES['file'];
if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
	http_response_code(400);
	echo json_encode(['error' => 'Upload failed']);
	exit;
}

$tmpPath = (string) ($upload['tmp_name'] ?? '');
if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid upload']);
	exit;
}

// Basic limits (adjust as needed).
$maxBytes = 8 * 1024 * 1024; // 8 MB
$size = (int) ($upload['size'] ?? 0);
if ($size <= 0 || $size > $maxBytes) {
	http_response_code(413);
	echo json_encode(['error' => 'File too large']);
	exit;
}

// Detect type by content (not by extension).
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = (string) $finfo->file($tmpPath);

$allowedMimes = [
	'image/jpeg' => 'jpg',
	'image/png' => 'png',
	'image/webp' => 'webp',
];

if (!array_key_exists($mime, $allowedMimes)) {
	http_response_code(415);
	echo json_encode(['error' => 'Unsupported image type']);
	exit;
}

// Make sure GD is available for compression.
if (!extension_loaded('gd')) {
	http_response_code(500);
	echo json_encode(['error' => 'Image processing not available (GD missing)']);
	exit;
}

// Read dimensions; reject extremely large images.
$imgInfo = @getimagesize($tmpPath);
if (!is_array($imgInfo) || empty($imgInfo[0]) || empty($imgInfo[1])) {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid image']);
	exit;
}

$srcW = (int) $imgInfo[0];
$srcH = (int) $imgInfo[1];

if ($srcW <= 0 || $srcH <= 0 || $srcW > 12000 || $srcH > 12000) {
	http_response_code(400);
	echo json_encode(['error' => 'Image dimensions not allowed']);
	exit;
}

// Load source image.
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
	http_response_code(400);
	echo json_encode(['error' => 'Unable to read image']);
	exit;
}

// Resize to a max box while keeping aspect ratio.
$maxW = 1600;
$maxH = 1600;

$scale = min($maxW / $srcW, $maxH / $srcH, 1);
$dstW = (int) max(1, floor($srcW * $scale));
$dstH = (int) max(1, floor($srcH * $scale));

$dstImg = imagecreatetruecolor($dstW, $dstH);

// Preserve alpha for PNG/WebP.
imagealphablending($dstImg, false);
imagesavealpha($dstImg, true);
$transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
imagefilledrectangle($dstImg, 0, 0, $dstW, $dstH, $transparent);

imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH);

imagedestroy($srcImg);

// Store under public/uploads so it's directly reachable.
$uploadsDirFs = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
if (!is_dir($uploadsDirFs) && !@mkdir($uploadsDirFs, 0775, true)) {
	imagedestroy($dstImg);
	http_response_code(500);
	echo json_encode(['error' => 'Unable to create uploads directory']);
	exit;
}

// Always re-encode to WebP for better compression.
$quality = 78; // 0-100
$fileName = bin2hex(random_bytes(16)) . '.webp';
$destFs = $uploadsDirFs . DIRECTORY_SEPARATOR . $fileName;

if (!imagewebp($dstImg, $destFs, $quality)) {
	imagedestroy($dstImg);
	http_response_code(500);
	echo json_encode(['error' => 'Unable to save image']);
	exit;
}

imagedestroy($dstImg);
@chmod($destFs, 0644);

echo json_encode([
	'location' => '/uploads/' . $fileName,
]);

