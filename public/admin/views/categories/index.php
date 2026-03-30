<?php /** @var array $categories */ ?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="h5 mb-1">Catégories éditoriales</h2>
		<p class="text-muted small mb-0">Organisez les rubriques de vos articles (Diplomatie, Économie, Sécurité...).</p>
	</div>
	<div>
		<a href="/admin/?r=categories_create" class="btn btn-sm btn-dark">
			<i class="bi bi-plus-lg me-1"></i> Nouvelle catégorie
		</a>
	</div>
</div>

<?php if ($success): ?>
	<div class="alert alert-success py-2 small mb-3">
		<?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
	</div>
<?php endif; ?>

<?php if ($error): ?>
	<div class="alert alert-danger py-2 small mb-3">
		<?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
	</div>
<?php endif; ?>

<?php if (empty($categories)): ?>
	<div class="alert alert-light border small mb-0">
		Aucune catégorie pour le moment. Créez votre première catégorie pour structurer vos articles.
	</div>
<?php else: ?>
	<div class="table-responsive small">
		<table class="table align-middle table-sm mb-0">
			<thead class="table-light">
			<tr>
				<th style="width: 5%">#</th>
				<th>Nom</th>
				<th>Slug</th>
				<th style="width: 18%" class="text-end">Actions</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($categories as $cat): ?>
				<tr>
					<td><?= (int) $cat['id'] ?></td>
					<td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></td>
					<td><code class="small"><?= htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
					<td class="text-end">
						<a href="/admin/?r=categories_edit&id=<?= (int) $cat['id'] ?>" class="btn btn-outline-secondary btn-xs btn-sm me-1">
							<i class="bi bi-pencil"></i>
						</a>
						<form action="/admin/?r=categories_delete&id=<?= (int) $cat['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Supprimer cette catégorie ?');">
							<button type="submit" class="btn btn-outline-danger btn-xs btn-sm">
								<i class="bi bi-trash"></i>
							</button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>
