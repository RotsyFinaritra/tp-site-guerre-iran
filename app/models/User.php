<?php

require_once __DIR__ . '/../core/Model.php';

class User extends Model
{
	public static function findById(int $id): ?array
	{
		return self::fetchOne('SELECT * FROM users WHERE id = :id', ['id' => $id]);
	}

	public static function findByUsername(string $username): ?array
	{
		return self::fetchOne(
			'SELECT * FROM users WHERE username = :username',
			['username' => $username]
		);
	}

	public static function create(string $username, string $password): int
	{
		$hash = password_hash($password, PASSWORD_DEFAULT);
		if ($hash === false) {
			throw new RuntimeException('Failed to hash password');
		}

		return self::insertRow('users', [
			'username' => $username,
			'password_hash' => $hash,
		]);
	}

	public static function verifyCredentials(string $username, string $password): ?array
	{
		$user = self::findByUsername($username);
		if (!$user) {
			return null;
		}

		if (!password_verify($password, (string) $user['password_hash'])) {
			return null;
		}

		return $user;
	}

	public static function updatePassword(int $id, string $newPassword): bool
	{
		$hash = password_hash($newPassword, PASSWORD_DEFAULT);
		if ($hash === false) {
			throw new RuntimeException('Failed to hash password');
		}

		return self::updateRowById('users', $id, ['password_hash' => $hash]);
	}
}

