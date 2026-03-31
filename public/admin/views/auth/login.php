<?php
/** @var string $loginError */

$loginError = $loginError ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Iran Correspondent — Connexion backoffice</title>

	<link rel="stylesheet" href="/assets/vendor/bootstrap/bootstrap.min.css"/>
	<link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css"/>

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" media="print" onload="this.media='all'">
	<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"></noscript>

	<style>
		:root {
			--navy:      #0a1628;
			--navy-mid:  #0f2044;
			--navy-soft: #162952;
			--white:     #ffffff;
			--off-white: #e8edf5;
			--muted:     #8a9bb8;
			--border:    rgba(255,255,255,.1);
			--shadow:    0 32px 80px rgba(0,0,0,.55);
		}

		*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

		body {
			min-height: 100vh;
			background: #f5f6fb;
			font-family: 'DM Sans', sans-serif;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			overflow: hidden;
		}

		body::before {
			content: '';
			position: fixed; inset: 0;
			background-image:
				radial-gradient(ellipse 80% 60% at 50% 0%, rgba(148,163,184,.3) 0%, transparent 70%);
			pointer-events: none;
		}

		body::after {
			content: '';
			position: fixed; inset: 0;
			background-image:
				radial-gradient(1.5px 1.5px at 20% 30%, rgba(148,163,184,.18) 0%, transparent 100%),
				radial-gradient(1.5px 1.5px at 75% 20%, rgba(148,163,184,.14) 0%, transparent 100%),
				radial-gradient(1px 1px at 60% 70%, rgba(148,163,184,.1) 0%, transparent 100%),
				radial-gradient(2px 2px at 10% 80%, rgba(148,163,184,.08) 0%, transparent 100%);
			pointer-events: none;
		}

		.login-card {
			position: relative; z-index: 1;
			width: 100%;
			max-width: 420px;
			background: var(--navy-mid);
			border: 1px solid var(--border);
			border-radius: 6px;
			padding: 2.75rem 2.5rem 2.25rem;
			box-shadow: var(--shadow), inset 0 1px 0 rgba(255,255,255,.07);
			animation: riseIn .7s cubic-bezier(.22,1,.36,1) both;
			color: var(--white);
		}

		@keyframes riseIn {
			from { opacity: 0; transform: translateY(24px) scale(.98); }
			to   { opacity: 1; transform: translateY(0) scale(1); }
		}

		.login-card::before {
			content: '';
			position: absolute;
			top: 0; left: 2rem; right: 2rem;
			height: 2px;
			background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
			border-radius: 99px;
		}

		.card-header-custom {
			text-align: center;
			margin-bottom: 2rem;
		}

		.site-tag {
			font-size: .65rem;
			font-weight: 600;
			letter-spacing: .3em;
			text-transform: uppercase;
			color: var(--muted);
			margin-bottom: 1rem;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: .5rem;
		}

		.site-tag::before,
		.site-tag::after {
			content: '';
			flex: 1;
			max-width: 40px;
			height: 1px;
			background: var(--border);
		}

		.site-name {
			font-family: 'Playfair Display', serif;
			font-size: 1.65rem;
			font-weight: 900;
			color: var(--white);
			letter-spacing: -.01em;
			line-height: 1.1;
		}

		.site-sub {
			font-size: .72rem;
			color: var(--muted);
			letter-spacing: .12em;
			text-transform: uppercase;
			margin-top: .3rem;
		}

		.field {
			margin-bottom: 1.1rem;
		}

		.field label {
			display: block;
			font-size: .68rem;
			font-weight: 600;
			letter-spacing: .15em;
			text-transform: uppercase;
			color: var(--muted);
			margin-bottom: .45rem;
		}

		.input-wrap {
			position: relative;
		}

		.input-wrap .icon {
			position: absolute;
			left: .9rem;
			top: 50%; transform: translateY(-50%);
			color: var(--muted);
			font-size: .95rem;
			pointer-events: none;
			transition: color .2s;
		}

		.input-wrap input {
			width: 100%;
			background: var(--navy);
			border: 1px solid var(--border);
			border-radius: 4px;
			color: var(--white);
			font-family: 'DM Sans', sans-serif;
			font-size: .9rem;
			padding: .72rem 1rem .72rem 2.6rem;
			outline: none;
			transition: border-color .2s, box-shadow .2s;
		}

		.input-wrap input::placeholder { color: rgba(138,155,184,.4); }

		.input-wrap input:focus {
			border-color: rgba(255,255,255,.35);
			box-shadow: 0 0 0 3px rgba(255,255,255,.05);
		}

		.input-wrap:focus-within .icon { color: var(--off-white); }

		.toggle-pw {
			position: absolute;
			right: .85rem;
			top: 50%; transform: translateY(-50%);
			background: none; border: none; padding: 0;
			color: var(--muted); cursor: pointer; font-size: .95rem;
			transition: color .2s;
		}
		.toggle-pw:hover { color: var(--white); }

		.form-extras {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin: .25rem 0 1.5rem;
		}

		.form-check-label {
			font-size: .78rem;
			color: var(--muted);
			cursor: pointer;
		}

		.form-check-input {
			background-color: transparent;
			border-color: var(--border);
		}
		.form-check-input:checked {
			background-color: var(--white);
			border-color: var(--white);
		}
		.form-check-input:focus { box-shadow: 0 0 0 3px rgba(255,255,255,.08); }

		.forgot-link {
			font-size: .78rem;
			color: var(--muted);
			text-decoration: none;
			transition: color .2s;
		}
		.forgot-link:hover { color: var(--white); }

		.btn-login {
			width: 100%;
			background: var(--white);
			color: var(--navy);
			border: none;
			border-radius: 4px;
			font-family: 'DM Sans', sans-serif;
			font-size: .78rem;
			font-weight: 600;
			letter-spacing: .2em;
			text-transform: uppercase;
			padding: .85rem;
			cursor: pointer;
			transition: background .2s, transform .15s, box-shadow .2s;
			box-shadow: 0 4px 16px rgba(0,0,0,.3);
		}

		.btn-login:hover {
			background: var(--off-white);
			transform: translateY(-1px);
			box-shadow: 0 8px 24px rgba(0,0,0,.4);
		}

		.btn-login:active { transform: translateY(0); }

		.alert-custom {
			background: rgba(255,255,255,.05);
			border: 1px solid rgba(255,255,255,.15);
			border-radius: 4px;
			color: var(--off-white);
			font-size: .8rem;
			padding: .6rem .9rem;
			margin-bottom: 1.25rem;
			display: flex;
			align-items: center;
			gap: .5rem;
		}

		.card-foot {
			margin-top: 1.75rem;
			padding-top: 1.25rem;
			border-top: 1px solid var(--border);
			text-align: center;
			font-size: .72rem;
			color: var(--muted);
			line-height: 1.8;
		}

		.card-foot a {
			color: rgba(255,255,255,.5);
			text-decoration: none;
			transition: color .2s;
		}
		.card-foot a:hover { color: var(--white); }

		@media (max-width: 480px) {
			.login-card { margin: 1rem; padding: 2rem 1.5rem 1.75rem; }
		}
	</style>
</head>
<body>

<div class="login-card">

	<div class="card-header-custom">
		<div class="site-tag">
			<i class="bi bi-broadcast-pin"></i> Accès Backoffice
		</div>
		<div class="site-name">Iran Correspondent</div>
		<div class="site-sub">Interface d'administration</div>
	</div>

	<?php if ($loginError): ?>
		<div class="alert-custom">
			<i class="bi bi-exclamation-circle"></i>
			<?= htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8') ?>
		</div>
	<?php endif; ?>

	<form method="post" action="" novalidate>

		<div class="field">
			<label for="username">Identifiant</label>
			<div class="input-wrap">
				<input
					type="text"
					id="username"
					name="username"
					placeholder="admin ou email"
					value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '' ?>"
					autocomplete="username"
					required
				/>
				<i class="bi bi-person icon"></i>
			</div>
		</div>

		<div class="field">
			<label for="password">Mot de passe</label>
			<div class="input-wrap">
				<input
					type="password"
					id="password"
					name="password"
					placeholder="••••••••"
					required
					autocomplete="current-password"
				/>
				<i class="bi bi-lock icon"></i>
				<button type="button" class="toggle-pw" onclick="togglePw()" aria-label="Afficher le mot de passe">
					<i class="bi bi-eye" id="pw-icon"></i>
				</button>
			</div>
		</div>

		<div class="form-extras">
			<div class="form-check">
				<input class="form-check-input" type="checkbox" id="remember" name="remember"/>
				<label class="form-check-label" for="remember">Se souvenir de moi</label>
			</div>
			<a href="#" class="forgot-link">Mot de passe oublié ?</a>
		</div>

		<button type="submit" class="btn-login">
			<i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
		</button>

	</form>

	<div class="card-foot">
		Accès réservé à l'équipe éditoriale du backoffice.<br/>
		<a href="/">Retour au site public</a>
	</div>

</div>

<script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
<script>
	function togglePw() {
		const input = document.getElementById('password');
		const icon  = document.getElementById('pw-icon');
		input.type = input.type === 'password' ? 'text' : 'password';
		icon.classList.toggle('bi-eye');
		icon.classList.toggle('bi-eye-slash');
	}
</script>
</body>
</html>

