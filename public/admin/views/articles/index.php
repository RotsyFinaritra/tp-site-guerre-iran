<?php
/** @var array $articles */
/** @var array $filters */
/** @var array $pagination */
/** @var array $categories */

function buildQuery(array $filters, int $page): string
{
	$params = [
		'r' => 'articles',
		'page' => $page,
	];
	if (!empty($filters['status'])) {
		$params['status'] = $filters['status'];
	}
	if (!empty($filters['from'])) {
		$params['from'] = $filters['from'];
	}
	if (!empty($filters['to'])) {
		$params['to'] = $filters['to'];
	}
	if (!empty($filters['category_id'])) {
		$params['category_id'] = (int) $filters['category_id'];
	}
	return '/admin/index.php?' . http_build_query($params);
}
?>
<div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3">
	<div class="text-muted small">
		<?= (int)($pagination['total'] ?? 0) ?> résultat(s) — page <?= (int)($pagination['page'] ?? 1) ?> / <?= (int)($pagination['totalPages'] ?? 1) ?>
	</div>
	<a href="/admin/articles/create" class="btn btn-primary btn-sm">
		<i class="bi bi-plus-lg"></i> Ajouter
	</a>
</div>
<form method="get" action="/admin/index.php" class="mb-3">
	<input type="hidden" name="r" value="articles">
	<div class="d-flex flex-row flex-nowrap gap-2 align-items-end overflow-auto">
		<div class="flex-shrink-0" style="min-width: 240px;">
			<label for="category_id" class="form-label">Catégorie</label>
			<select name="category_id" id="category_id" class="form-select">
				<option value="" <?= empty($filters['category_id']) ? 'selected' : '' ?>>Toutes</option>
				<?php foreach (($categories ?? []) as $c): ?>
					<option value="<?= (int) $c['id'] ?>" <?= ((int)($filters['category_id'] ?? 0) === (int) $c['id']) ? 'selected' : '' ?>>
						<?= htmlspecialchars((string) $c['name'], ENT_QUOTES, 'UTF-8') ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="flex-shrink-0" style="min-width: 200px;">
			<label for="status" class="form-label">Statut</label>
			<select name="status" id="status" class="form-select">
				<option value="" <?= empty($filters['status']) ? 'selected' : '' ?>>Tous</option>
				<option value="draft" <?= ($filters['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Brouillon</option>
				<option value="published" <?= ($filters['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publié</option>
			</select>
		</div>

		<div class="flex-shrink-0" style="min-width: 190px;">
			<label for="from" class="form-label">Date (du)</label>
			<input type="date" name="from" id="from" class="form-control" value="<?= htmlspecialchars((string)($filters['from'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
		</div>

		<div class="flex-shrink-0" style="min-width: 190px;">
			<label for="to" class="form-label">Date (au)</label>
			<input type="date" name="to" id="to" class="form-control" value="<?= htmlspecialchars((string)($filters['to'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
		</div>

		<div class="flex-shrink-0 d-flex gap-2" style="min-width: 260px;">
			<button type="submit" class="btn btn-primary">Filtrer</button>
			<a href="/admin/articles" class="btn btn-outline-secondary">Réinitialiser</a>
		</div>
	</div>
</form>

<div class="table-responsive">
	<table class="table table-sm align-middle mb-0">
	<thead>
		<tr>
			<th>ID</th>
			<th>Titre</th>
			<th>Catégorie</th>
			<th>Statut</th>
			<th>Créé</th>
			<th>Publié</th>
			<th class="text-end">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php if (empty($articles)): ?>
			<tr><td colspan="7" class="text-muted">Aucun article</td></tr>
		<?php else: ?>
			<?php foreach ($articles as $a): ?>
				<tr>
					<td><?= (int)$a['id'] ?></td>
					<td>
						<div><strong><?= htmlspecialchars((string)$a['title'], ENT_QUOTES, 'UTF-8') ?></strong></div>
						<div class="text-muted small">slug: <?= htmlspecialchars((string)$a['slug'], ENT_QUOTES, 'UTF-8') ?></div>
					</td>
					<td><?= htmlspecialchars((string)($a['category_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
					<td>
						<span class="badge text-bg-<?= $a['status'] === 'published' ? 'success' : 'secondary' ?>">
							<?= htmlspecialchars((string)$a['status'], ENT_QUOTES, 'UTF-8') ?>
						</span>
					</td>
					<td><?= htmlspecialchars((string)$a['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
					<td><?= htmlspecialchars((string)($a['published_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
					<td class="text-end">
						<div class="d-inline-flex gap-2">
							<a class="btn btn-outline-secondary btn-sm" href="/admin/articles/edit?id=<?= (int) $a['id'] ?>">
								<i class="bi bi-pencil"></i> Éditer
							</a>
							<form method="post" action="/admin/articles/delete?id=<?= (int) $a['id'] ?>" onsubmit="return confirm('Supprimer cet article ?');" class="m-0">
								<button type="submit" class="btn btn-outline-danger btn-sm">
									<i class="bi bi-trash"></i> Supprimer
								</button>
							</form>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
	</table>
</div>


<nav class="d-flex align-items-center justify-content-between mt-3">
	<?php
		$page = (int)($pagination['page'] ?? 1);
		$totalPages = (int)($pagination['totalPages'] ?? 1);
	?>
	<?php if ($page > 1): ?>
		<a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(buildQuery($filters, $page - 1), ENT_QUOTES, 'UTF-8') ?>">← Précédent</a>
	<?php else: ?>
		<span class="text-muted small">← Précédent</span>
	<?php endif; ?>

	<span class="text-muted small">Page <?= $page ?> / <?= $totalPages ?></span>

	<?php if ($page < $totalPages): ?>
		<a class="btn btn-outline-secondary btn-sm" href="<?= htmlspecialchars(buildQuery($filters, $page + 1), ENT_QUOTES, 'UTF-8') ?>">Suivant →</a>
	<?php else: ?>
		<span class="text-muted small">Suivant →</span>
	<?php endif; ?>
</nav>
