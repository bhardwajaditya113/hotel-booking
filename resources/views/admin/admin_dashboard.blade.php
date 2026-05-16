<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="theme-color" content="#f4f6f8">

	<link rel="icon" href="{{ asset('backend/assets/images/favicon-32x32.png') }}" type="image/png" />
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700&family=Outfit:wght@500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">

	{{-- Tabler UI (MIT) — same major line as npm @tabler/core@1.4.0 --}}
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/css/tabler-vendors.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/boxicons.min.css') }}">
	<link href="{{ asset('backend/assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
	@vite(['resources/css/nexstay-admin-tabler.css'])

	<title>{{ config('app.name', 'Elapse') }} — Admin</title>
	@auth
		<meta name="portal-sync-version" content="{{ \App\Support\PortalSync::version() }}">
		<meta name="portal-sync-poll-url" content="{{ route('portal.sync') }}">
		@vite(['resources/js/portal-sync.js'])
	@endauth
</head>

<body class="page portal-admin nex-tabler" data-bs-theme="light">
		@include('admin.body.sidebar')
		<div class="page-wrapper">
			@include('admin.body.header')
			<div class="page-body">
				<div class="container-xl">
					@yield('admin')
				</div>
			</div>
			@include('admin.body.footer')
		</div>

	<script src="{{ asset('frontend/assets/js/jquery.min.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.4.0/dist/js/tabler.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="{{ asset('backend/assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<script src="{{ asset('backend/assets/js/code.js') }}"></script>
	<script src="{{ asset('backend/assets/js/validate.min.js') }}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

	<script>
		@if (Session::has('message'))
			var type = "{{ Session::get('alert-type', 'info') }}";
			switch (type) {
				case 'info':
					toastr.info(" {{ Session::get('message') }} ");
					break;
				case 'success':
					toastr.success(" {{ Session::get('message') }} ");
					break;
				case 'warning':
					toastr.warning(" {{ Session::get('message') }} ");
					break;
				case 'error':
					toastr.error(" {{ Session::get('message') }} ");
					break;
			}
		@endif
	</script>

	<script>
		$(function () {
			if ($.fn.DataTable && document.getElementById('example')) {
				$('#example').DataTable();
			}
		});
	</script>

	<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
	<script>
		if (document.querySelector('textarea#myeditorinstance')) {
			tinymce.init({
				selector: 'textarea#myeditorinstance',
				plugins: 'powerpaste advcode table lists checklist',
				toolbar: 'undo redo | blocks| bold italic | bullist numlist checklist | code | table'
			});
		}
	</script>
</body>

</html>
