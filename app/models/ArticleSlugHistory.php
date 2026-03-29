<?php

require_once __DIR__ . '/../core/Model.php';

class ArticleSlugHistory extends Model
{
    public static function add(int $articleId, string $oldSlug): bool
    {
        // old_slug is UNIQUE, so ignore duplicates
        return self::execute(
            'INSERT IGNORE INTO article_slug_history (article_id, old_slug) VALUES (:article_id, :old_slug)',
            [
                'article_id' => $articleId,
                'old_slug' => $oldSlug,
            ]
        );
    }

    public static function findArticleIdByOldSlug(string $oldSlug): ?int
    {
        $row = self::fetchOne(
            'SELECT article_id FROM article_slug_history WHERE old_slug = :old_slug',
            ['old_slug' => $oldSlug]
        );

        return $row ? (int) $row['article_id'] : null;
    }

    public static function listByArticleId(int $articleId): array
    {
        return self::fetchAll(
            'SELECT * FROM article_slug_history WHERE article_id = :article_id ORDER BY created_at DESC',
            ['article_id' => $articleId]
        );
    }
}
