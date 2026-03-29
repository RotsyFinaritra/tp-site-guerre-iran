<?php
session_start();

require_once __DIR__ . '/../../../app/models/User.php';
require_once __DIR__ . '/../../../app/models/Category.php';

if (!isset($_SESSION['admin']) || !is_array($_SESSION['admin'])) {
	header('Location: /admin/');
	exit;
}

$currentUser = $_SESSION['admin'];

// Petite fonction utilitaire pour générer un slug basique à partir du nom
function category_slugify(string $text): string
{
	$text = trim(mb_strtolower($text, 'UTF-8'));
	// Remplacer les accents de base
	$text = strtr($text, [
		'à' => 'a', 'â' => 'a', 'ä' => 'a',
		'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
		'î' => 'i', 'ï' => 'i',
		'ô' => 'o', 'ö' => 'o',
		'ù' => 'u', 'û' => 'u', 'ü' => 'u',
		'ç' => 'c',
	]);
	// Remplacer tout ce qui n'est pas alphanum par des tirets
	$text = preg_replace('~[^a-z0-9]+~u', '-', $text);
	// Trim tirets
	$text = trim($text, '-');
	return $text !== '' ? $text : 'categorie';
}

$error   = '';
$success = '';

if (isset($_GET['success'])) {
	if ($_GET['success'] === 'created') {
		$success = 'Catégorie créée avec succès.';
	} elseif ($_GET['success'] === 'updated') {
		$success = 'Catégorie mise à jour avec succès.';
	} elseif ($_GET['success'] === 'deleted') {
		$success = 'Catégorie supprimée avec succès.';
	}
}
