<?php

require_once __DIR__ . '/../core/Model.php';

class Category extends Model
{
    public static function all(): array
    {
        return self::fetchAll('SELECT * FROM categories ORDER BY name ASC');
    }

    public static function findById(int $id): ?array
    {
        return self::fetchOne('SELECT * FROM categories WHERE id = :id', ['id' => $id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::fetchOne('SELECT * FROM categories WHERE slug = :slug', ['slug' => $slug]);
    }

    public static function create(string $name, string $slug): int
    {
        return self::insertRow('categories', [
            'name' => $name,
            'slug' => $slug,
        ]);
    }

    public static function update(int $id, array $data): bool
    {
        $row = [];
        if (array_key_exists('name', $data)) {
            $row['name'] = $data['name'];
        }
        if (array_key_exists('slug', $data)) {
            $row['slug'] = $data['slug'];
        }

        return self::updateRowById('categories', $id, $row);
    }

    public static function delete(int $id): bool
    {
        return self::execute('DELETE FROM categories WHERE id = :id', ['id' => $id]);
    }
}
