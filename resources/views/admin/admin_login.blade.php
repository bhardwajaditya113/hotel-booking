<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#f4f6f8">
	<link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Outfit:wght@500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/boxicons.min.css') }}">
	@vite(['resources/css/nexstay-admin-tabler.css'])
	<title>{{ config('app.name', 'Elapse') }} — Admin sign in</title>
</head>

<body class="page page-center portal-admin nex-tabler d-flex flex-column bg-secondary-lt" data-bs-theme="light">
	<div class="container container-tight py-4 flex-grow-1 d-flex flex-column justify-content-center">
		<div class="text-center mb-4 px-1">
			<div class="d-inline-flex flex-column align-items-center gap-3 mb-2">
				<x-admin-brand-lockup size="lg" class="justify-content-center flex-column nx-admin-login-brand" />
			</div>
			<h1 class="h2 mb-1 fw-semibold nx-admin-login-heading">Sign in</h1>
			<p class="text-secondary mb-0 small">Administrator access · {{ config('app.name', 'Elapse') }}</p>
		</div>
		<div class="card card-md shadow-sm">
			<div class="card-body">
				<form id="admin-login-form" method="POST" action="{{ route('admin.login.store') }}" autocomplete="off">
					@csrf
					<div class="mb-3">
						<label class="form-label" for="login">Email / name / phone</label>
						<input type="text" name="login" class="form-control @error('login') is-invalid @enderror" id="login" placeholder="you@example.com" autocomplete="username">
						@error('login')
							<div class="invalid-feedback d-block">{{ $message }}</div>
						@enderror
					</div>
					<div class="mb-3">
						<label class="form-label" for="password">Password</label>
						<div class="input-group input-group-flat" id="show_hide_password">
							<input type="password" name="password" class="form-control" id="password" placeholder="Enter password" autocomplete="current-password">
							<span class="input-group-text">
								<button type="button" class="btn btn-ghost-secondary px-2" aria-label="Show password"><i class="bx bx-hide"></i></button>
							</span>
						</div>
					</div>
					<div class="form-footer">
						<button type="submit" class="btn btn-primary w-100">Sign in</button>
					</div>
				</form>
			</div>
		</div>
		<div class="text-center text-secondary mt-3">
			<a href="{{ url('/') }}" class="link-primary">← Back to website</a>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script>
		(function () {
			var btn = document.querySelector('#show_hide_password button');
			if (!btn) return;
			btn.addEventListener('click', function () {
				var input = document.querySelector('#show_hide_password input');
				var icon = this.querySelector('i');
				if (!input || !icon) return;
				if (input.getAttribute('type') === 'password') {
					input.setAttribute('type', 'text');
					icon.classList.remove('bx-hide');
					icon.classList.add('bx-show');
				} else {
					input.setAttribute('type', 'password');
					icon.classList.add('bx-hide');
					icon.classList.remove('bx-show');
				}
			});
		})();
	</script>
</body>

</html>
