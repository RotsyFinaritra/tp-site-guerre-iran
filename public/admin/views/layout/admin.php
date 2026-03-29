<?php
/** @var string $pageTitle */
/** @var string $content */
?>
<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?= htmlspecialchars($pageTitle ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></title>
	<style>
		body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;margin:20px;}
		table{border-collapse:collapse;width:100%;}
		th,td{border:1px solid #ddd;padding:8px;vertical-align:top;}
		th{background:#f5f5f5;text-align:left;}
		form .row{display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:12px;}
		label{display:block;font-size:12px;margin-bottom:4px;}
		input,select{padding:6px 8px;}
		.actions{display:flex;gap:10px;align-items:center;}
		.pager{margin-top:12px;display:flex;gap:12px;align-items:center;}
		.small{color:#666;font-size:12px;}
		.badge{display:inline-block;padding:2px 8px;border-radius:10px;background:#eee;font-size:12px;}
	</style>
</head>
<body>
	<?= $content ?? '' ?>
</body>
</html>
