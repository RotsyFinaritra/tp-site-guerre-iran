<div class="mb-4">
	<h2 class="h5 mb-1">Bienvenue dans le backoffice</h2>
	<p class="text-muted small mb-0">Vue d'ensemble rapide de l'activité éditoriale sur le conflit en Iran.</p>
</div>

<div class="row g-3 mb-4">
	<div class="col-md-3">
		<div class="bo-card h-100">
			<div class="text-muted small text-uppercase mb-1">Articles au total</div>
			<div class="fs-4 fw-bold mb-1"><?= (int) ($totalArticles ?? 0) ?></div>
			<div class="text-muted small">Toutes rubriques confondues</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="bo-card h-100">
			<div class="text-muted small text-uppercase mb-1">Publiés</div>
			<div class="fs-4 fw-bold mb-1 text-success"><?= (int) ($totalPublished ?? 0) ?></div>
			<div class="text-muted small">Visibles côté site</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="bo-card h-100">
			<div class="text-muted small text-uppercase mb-1">Brouillons</div>
			<div class="fs-4 fw-bold mb-1 text-warning"><?= (int) ($totalDrafts ?? 0) ?></div>
			<div class="text-muted small">En cours de rédaction</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="bo-card h-100">
			<div class="text-muted small text-uppercase mb-1">Catégories</div>
			<div class="fs-4 fw-bold mb-1"><?= (int) ($totalCategories ?? 0) ?></div>
			<div class="text-muted small">Rubriques éditoriales</div>
		</div>
	</div>
</div>


<div class="row g-3">
	<div class="col-lg-12">
		<div class="bo-card h-100">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<div>
					<h3 class="h6 mb-1">Derniers articles</h3>
					<p class="text-muted small mb-0">Les 5 contenus les plus récents créés dans le backoffice.</p>
				</div>
				<a href="/admin/?r=articles" class="btn btn-sm btn-outline-secondary">Voir tous les articles</a>
			</div>

			<?php if (empty($latestArticles)): ?>
				<p class="text-muted small mb-0">Aucun article pour le moment. Commencez par en créer un depuis la section Articles.</p>
			<?php else: ?>
				<div class="table-responsive small">
					<table class="table align-middle table-sm mb-0">
						<thead class="table-light">
						<tr>
							<th>Titre</th>
							<th>Catégorie</th>
							<th>Statut</th>
							<th>Date</th>
							<th class="text-end">Actions</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($latestArticles as $art): ?>
							<tr>
								<td><?= htmlspecialchars($art['title'], ENT_QUOTES, 'UTF-8') ?></td>
								<td><?= htmlspecialchars($art['category_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
								<td>
									<?php if (($art['status'] ?? '') === 'published'): ?>
										<span class="badge bg-success-subtle text-success border border-success-subtle">Publié</span>
									<?php else: ?>
										<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Brouillon</span>
									<?php endif; ?>
								</td>
								<td class="text-nowrap small">
									<?= htmlspecialchars(substr((string)($art['created_at'] ?? ''), 0, 16), ENT_QUOTES, 'UTF-8') ?>
								</td>
								<td class="text-end">
									<a href="/admin/?r=articles_edit&id=<?= (int) $art['id'] ?>" class="btn btn-sm btn-outline-secondary btn-xs">
										<i class="bi bi-pencil"></i>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
