<?php

abstract class Model
{
	private static ?PDO $pdo = null;

	protected static function db(): PDO
	{
		if (self::$pdo instanceof PDO) {
			return self::$pdo;
		}

		$configPath = __DIR__ . '/../config/database.php';
		if (!is_file($configPath)) {
			throw new RuntimeException('Database config not found: ' . $configPath);
		}

		$cfg = require $configPath;
		$host = $cfg['host'] ?? 'db';
		$port = $cfg['port'] ?? '3306';
		$name = $cfg['name'] ?? 'iran_news';
		$user = $cfg['user'] ?? 'root';
		$pass = $cfg['pass'] ?? 'root';
		$charset = $cfg['charset'] ?? 'utf8mb4';

		$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $name, $charset);

		self::$pdo = new PDO(
			$dsn,
			$user,
			$pass,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false,
			]
		);

		return self::$pdo;
	}

	protected static function fetchOne(string $sql, array $params = []): ?array
	{
		$stmt = self::db()->prepare($sql);
		$stmt->execute($params);
		$row = $stmt->fetch();
		return $row === false ? null : $row;
	}

	protected static function fetchAll(string $sql, array $params = []): array
	{
		$stmt = self::db()->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	protected static function execute(string $sql, array $params = []): bool
	{
		$stmt = self::db()->prepare($sql);
		return $stmt->execute($params);
	}

	protected static function insertRow(string $table, array $data): int
	{
		if ($data === []) {
			throw new InvalidArgumentException('insertRow: empty data');
		}

		$columns = array_keys($data);
		$placeholders = array_map(static fn($c) => ':' . $c, $columns);
		$sql = sprintf(
			'INSERT INTO %s (%s) VALUES (%s)',
			$table,
			implode(', ', $columns),
			implode(', ', $placeholders)
		);

		self::execute($sql, $data);
		return (int) self::db()->lastInsertId();
	}

	protected static function updateRowById(string $table, int $id, array $data, string $idColumn = 'id'): bool
	{
		if ($data === []) {
			return true;
		}

		$setParts = [];
		$params = [];
		foreach ($data as $col => $val) {
			$setParts[] = sprintf('%s = :%s', $col, $col);
			$params[$col] = $val;
		}
		$params['_id'] = $id;

		$sql = sprintf('UPDATE %s SET %s WHERE %s = :_id', $table, implode(', ', $setParts), $idColumn);
		return self::execute($sql, $params);
	}
}

