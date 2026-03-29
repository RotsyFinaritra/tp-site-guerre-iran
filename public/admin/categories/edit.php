<?php
require_once __DIR__ . '/_common.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: /admin/categories');
    exit;
}

$category = Category::findById($id);
if (!$category) {
    $error = 'Catégorie introuvable.';
}

$pageTitle   = 'Backoffice – Modifier une catégorie';
$pageHeading = 'Modifier une catégorie';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $category) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');

    if ($name === '') {
        $error = 'Le nom de la catégorie est obligatoire.';
    } else {
        if ($slug === '') {
            $slug = category_slugify($name);
        }

        try {
            Category::update($id, ['name' => $name, 'slug' => $slug]);
            header('Location: /admin/categories?success=updated');
            exit;
        } catch (Throwable $e) {
            $error = "Erreur lors de la mise à jour de la catégorie.";
        }
    }
}

$nameValue = $_POST['name'] ?? ($category['name'] ?? '');
$slugValue = $_POST['slug'] ?? ($category['slug'] ?? '');

ob_start();
?>
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="h5 mb-1">Modifier une catégorie</h2>
        <p class="text-muted small mb-0">Ajustez le nom ou le slug de cette catégorie.</p>
    </div>
    <div>
        <a href="/admin/categories" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger py-2 small mb-3">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($category): ?>
<form method="post" action="">
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label for="name" class="form-label small text-uppercase text-muted fw-semibold mb-1">Nom de la catégorie</label>
            <input type="text" class="form-control form-control-sm" id="name" name="name" required
                   value="<?= htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8') ?>" />
        </div>
        <div class="col-md-6">
            <label for="slug" class="form-label small text-uppercase text-muted fw-semibold mb-1">Slug (URL)</label>
            <input type="text" class="form-control form-control-sm" id="slug" name="slug"
                   value="<?= htmlspecialchars($slugValue, ENT_QUOTES, 'UTF-8') ?>" />
            <div class="form-text small">Laissez vide pour générer automatiquement à partir du nom.</div>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-sm btn-dark">
            <i class="bi bi-save me-1"></i> Enregistrer les modifications
        </button>
    </div>
</form>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../views/layouts/main.php';
