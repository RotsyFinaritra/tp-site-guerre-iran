<?php

return [
	// Docker-compose service name by default
	'host' => getenv('DB_HOST') ?: 'db',
	'port' => getenv('DB_PORT') ?: '3306',

	// docker-compose.yml defines MYSQL_DATABASE=iran_news
	'name' => getenv('DB_NAME') ?: 'iran_news',

	// docker-compose.yml defines MYSQL_ROOT_PASSWORD=root
	'user' => getenv('DB_USER') ?: 'root',
	'pass' => getenv('DB_PASS') ?: 'root',

	'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
];

