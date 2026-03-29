<?php
require_once __DIR__ . '/_common.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    try {
        Category::delete($id);
        header('Location: /admin/categories?success=deleted');
        exit;
    } catch (Throwable $e) {
        $error = "Erreur lors de la suppression de la catégorie.";
    }
}

// En cas d'erreur, simple redirection vers la liste avec un message générique
header('Location: /admin/categories');
exit;
