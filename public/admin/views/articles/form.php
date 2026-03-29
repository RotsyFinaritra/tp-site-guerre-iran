<?php
/** @var array $article */
/** @var array $categories */
/** @var array $errors */

$isEdit = !empty($article['id']);
$action = $isEdit
	? ('/admin/articles/update?id=' . (int) $article['id'])
	: '/admin/articles/store';
?>

<?php if (!empty($errors)): ?>
	<div class="alert alert-danger">
		<ul class="mb-0">
			<?php foreach ($errors as $e): ?>
				<li><?= htmlspecialchars((string) $e, ENT_QUOTES, 'UTF-8') ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="vstack gap-3">
	<div>
		<label for="category_id" class="form-label">Catégorie</label>
		<select class="form-select" name="category_id" id="category_id" required>
			<option value="" disabled <?= empty($article['category_id']) ? 'selected' : '' ?>>Choisir…</option>
			<?php foreach (($categories ?? []) as $c): ?>
				<option value="<?= (int) $c['id'] ?>" <?= ((int)($article['category_id'] ?? 0) === (int)$c['id']) ? 'selected' : '' ?>>
					<?= htmlspecialchars((string) $c['name'], ENT_QUOTES, 'UTF-8') ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>

	<div>
		<label for="title" class="form-label">Titre</label>
		<input class="form-control" type="text" name="title" id="title" value="<?= htmlspecialchars((string)($article['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
	</div>

	<div>
		<label for="slug" class="form-label">Slug</label>
		<input class="form-control" type="text" name="slug" id="slug" value="<?= htmlspecialchars((string)($article['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
		<div class="form-text">Ex: mon-article-iran</div>
	</div>

	<div>
		<label for="excerpt" class="form-label">Extrait</label>
		<textarea class="form-control" name="excerpt" id="excerpt" rows="3"><?= htmlspecialchars((string)($article['excerpt'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
	</div>

	<div>
		<label for="content_html" class="form-label">Contenu (HTML)</label>
		<textarea class="form-control" name="content_html" id="content_html" rows="10" required><?= htmlspecialchars((string)($article['content_html'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
	</div>

	<div>
		<label for="status" class="form-label">Statut</label>
		<select class="form-select" name="status" id="status" required>
			<option value="draft" <?= (($article['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Brouillon</option>
			<option value="published" <?= (($article['status'] ?? '') === 'published') ? 'selected' : '' ?>>Publié</option>
		</select>
	</div>

	<div class="d-flex gap-2">
		<button type="submit" class="btn btn-primary">
			<?= $isEdit ? 'Enregistrer' : 'Créer' ?>
		</button>
		<a href="/admin/articles" class="btn btn-outline-secondary">Annuler</a>
	</div>
</form>
