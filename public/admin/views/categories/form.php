<?php /** @var array $category */ /** @var array $errors */ ?>

<?php if (!empty($errors)): ?>
	<div class="alert alert-danger py-2 small mb-3">
		<ul class="mb-0 ps-3">
			<?php foreach ($errors as $err): ?>
				<li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>

<div class="mb-3 d-flex justify-content-between align-items-center">
	<div>
		<h2 class="h5 mb-1"><?= htmlspecialchars($pageHeading, ENT_QUOTES, 'UTF-8') ?></h2>
		<p class="text-muted small mb-0">
			<?= $pageHeading === 'Nouvelle catégorie'
				? 'Définissez un nom clair et un slug URL lisible pour vos visiteurs.'
				: 'Ajustez le nom ou le slug de cette catégorie.' ?>
		</p>
	</div>
	<div>
		<a href="/admin/?r=categories" class="btn btn-sm btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Retour à la liste
		</a>
	</div>
</div>

<form method="post" action="<?= htmlspecialchars($formAction ?? '', ENT_QUOTES, 'UTF-8') ?>">
	<div class="row g-3 mb-3">
		<div class="col-md-6">
			<label for="name" class="form-label small text-uppercase text-muted fw-semibold mb-1">Nom de la catégorie</label>
			<input type="text" class="form-control form-control-sm" id="name" name="name" required
			       value="<?= htmlspecialchars($category['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
			       placeholder="Ex. Diplomatie" />
		</div>
		<div class="col-md-6">
			<label for="slug" class="form-label small text-uppercase text-muted fw-semibold mb-1">Slug (URL)</label>
			<input type="text" class="form-control form-control-sm" id="slug" name="slug"
			       value="<?= htmlspecialchars($category['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
			       placeholder="ex. diplomatie" />
			<div class="form-text small">Laissez vide pour générer automatiquement à partir du nom.</div>
		</div>
	</div>

	<div class="d-flex justify-content-end">
		<button type="submit" class="btn btn-sm btn-dark">
			<i class="bi bi-save me-1"></i> Enregistrer
		</button>
	</div>
</form>
