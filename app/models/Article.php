<?php

require_once __DIR__ . '/../core/Model.php';

class Article extends Model
{
	private static function buildAdminWhere(array $filters, array &$params): string
	{
		$where = ['a.deleted_at IS NULL'];
		$params = [];

		if (!empty($filters['status']) && in_array($filters['status'], ['draft', 'published'], true)) {
			$where[] = 'a.status = :status';
			$params['status'] = $filters['status'];
		}

		if (!empty($filters['category_id']) && is_numeric($filters['category_id'])) {
			$where[] = 'a.category_id = :category_id';
			$params['category_id'] = (int) $filters['category_id'];
		}

		if (!empty($filters['from'])) {
			$where[] = 'a.created_at >= :from_dt';
			$params['from_dt'] = $filters['from'] . ' 00:00:00';
		}
		if (!empty($filters['to'])) {
			$where[] = 'a.created_at <= :to_dt';
			$params['to_dt'] = $filters['to'] . ' 23:59:59';
		}

		return 'WHERE ' . implode(' AND ', $where);
	}

	public static function countForAdmin(array $filters = []): int
	{
		$params = [];
		$whereSql = self::buildAdminWhere($filters, $params);
		$row = self::fetchOne('SELECT COUNT(*) AS cnt FROM articles a ' . $whereSql, $params);
		return (int) ($row['cnt'] ?? 0);
	}

	public static function listForAdmin(array $filters = [], int $limit = 10, int $offset = 0): array
	{
		$limit = max(1, min(100, $limit));
		$offset = max(0, $offset);

		$params = [];
		$whereSql = self::buildAdminWhere($filters, $params);

		$sql = "SELECT a.id, a.category_id, a.title, a.slug, a.status, a.created_at, a.updated_at, a.published_at,
					c.name AS category_name
				FROM articles a
				INNER JOIN categories c ON c.id = a.category_id
				" . $whereSql . "
				ORDER BY a.created_at DESC
				LIMIT " . (int) $limit . " OFFSET " . (int) $offset;

		return self::fetchAll($sql, $params);
	}

	public static function findById(int $id, bool $includeDeleted = false): ?array
	{
		$sql = 'SELECT * FROM articles WHERE id = :id';
		$params = ['id' => $id];

		if (!$includeDeleted) {
			$sql .= ' AND deleted_at IS NULL';
		}

		return self::fetchOne($sql, $params);
	}

	/**
	 * Finds an article by current slug; if not found, tries slug history.
	 */
	public static function findBySlug(string $slug, bool $includeDrafts = false): ?array
	{
		$sql = 'SELECT * FROM articles WHERE slug = :slug AND deleted_at IS NULL';
		$params = ['slug' => $slug];

		if (!$includeDrafts) {
			$sql .= " AND status = 'published'";
		}

		$article = self::fetchOne($sql, $params);
		if ($article) {
			return $article;
		}

		// slug history: resolve old slug -> article_id
		$hist = self::fetchOne(
			'SELECT article_id FROM article_slug_history WHERE old_slug = :slug',
			['slug' => $slug]
		);
		if (!$hist) {
			return null;
		}

		return self::findById((int) $hist['article_id'], false);
	}

	public static function listPublished(int $limit = 10, int $offset = 0, ?int $categoryId = null): array
	{
		$limit = max(1, min(100, $limit));
		$offset = max(0, $offset);

		$sql = "SELECT a.*
				FROM articles a
				WHERE a.status = 'published'
				  AND a.deleted_at IS NULL
				  AND a.published_at IS NOT NULL";
		$params = [];

		if ($categoryId !== null) {
			$sql .= ' AND a.category_id = :category_id';
			$params['category_id'] = $categoryId;
		}

		$sql .= ' ORDER BY a.published_at DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

		return self::fetchAll($sql, $params);
	}

	public static function create(array $data): int
	{
		$allowed = [
			'category_id',
			'title',
			'slug',
			'excerpt',
			'content_html',
			'hero_image_path',
			'hero_image_alt',
			'meta_title',
			'meta_description',
			'canonical_url',
			'meta_robots',
			'status',
			'published_at',
		];

		$row = [];
		foreach ($allowed as $key) {
			if (array_key_exists($key, $data)) {
				$row[$key] = $data[$key];
			}
		}

		// Required columns
		foreach (['category_id', 'title', 'slug', 'content_html'] as $required) {
			if (!array_key_exists($required, $row)) {
				throw new InvalidArgumentException('Missing required field: ' . $required);
			}
		}

		if (!isset($row['status'])) {
			$row['status'] = 'draft';
		}

		if ($row['status'] === 'published' && empty($row['published_at'])) {
			$row['published_at'] = date('Y-m-d H:i:s');
		}

		return self::insertRow('articles', $row);
	}

	public static function update(int $id, array $data): bool
	{
		$current = self::findById($id, true);
		if (!$current) {
			return false;
		}

		$allowed = [
			'category_id',
			'title',
			'slug',
			'excerpt',
			'content_html',
			'hero_image_path',
			'hero_image_alt',
			'meta_title',
			'meta_description',
			'canonical_url',
			'meta_robots',
			'status',
			'published_at',
		];

		$row = [];
		foreach ($allowed as $key) {
			if (array_key_exists($key, $data)) {
				$row[$key] = $data[$key];
			}
		}

		if (isset($row['status']) && $row['status'] === 'published' && empty($row['published_at'])) {
			$row['published_at'] = $current['published_at'] ?: date('Y-m-d H:i:s');
		}
		if (isset($row['status']) && $row['status'] === 'draft') {
			// keep published_at as-is unless explicitly passed
		}

		// Handle slug change -> write history
		if (isset($row['slug']) && $row['slug'] !== $current['slug']) {
			self::execute(
				'INSERT IGNORE INTO article_slug_history (article_id, old_slug) VALUES (:article_id, :old_slug)',
				[
					'article_id' => $id,
					'old_slug' => $current['slug'],
				]
			);
		}

		return self::updateRowById('articles', $id, $row);
	}

	public static function publish(int $id): bool
	{
		return self::updateRowById('articles', $id, [
			'status' => 'published',
			'published_at' => date('Y-m-d H:i:s'),
		]);
	}

	public static function unpublish(int $id): bool
	{
		return self::updateRowById('articles', $id, [
			'status' => 'draft',
			'published_at' => null,
		]);
	}

	public static function softDelete(int $id): bool
	{
		return self::updateRowById('articles', $id, [
			'deleted_at' => date('Y-m-d H:i:s'),
		]);
	}

	public static function restore(int $id): bool
	{
		return self::updateRowById('articles', $id, [
			'deleted_at' => null,
		]);
	}
}

