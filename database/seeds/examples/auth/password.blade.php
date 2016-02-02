@extends('layouts.noauth')

@section('body')
	<body class="login-page">
	<div class="login-box">

		<div class="login-box-body">
			<div class="login-logo">
				<a href="/"><img src="../../img/logo2.svg" width="200px" alt="" class="logo"/></a>
			</div><!-- /.login-logo -->
			<p class="login-box-msg">Reset Password</p>

			@if (count($errors) > 0)
				<div class="centered text-center">
					@foreach ($errors->all() as $error)
						<p class="text-red">{{ $error }}</p>
					@endforeach
				</div>
			@endif

			@if (Session::has('success-message'))
				<div class="centered text-center">
					<p class="text-green">{{ Session::get('success-message') }}</p>
				</div>
			@endif
			<form class="form-horizontal" id="loginForm" role="form" method="POST" action="{{ route('admin.postemail') }}">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<div class="form-group has-feedback">
					<input type="email" name="email" class="form-control" placeholder="Email"/>

				</div>
				<div class="form-group has-feedback">
					<button type="submit" class="btn btn-primary btn-block btn-flat">Send Password Link</button>
				</div>
				<!-- /.col -->
			</form>
		</div><!-- /.login-box-body -->
	</div><!-- /.login-box -->
	<p class="text-center" style="color:#808588;font-size:12px;">Your Site admin</p>
	<!-- REQUIRED JS SCRIPTS -->
	<!-- jQuery 2.1.3 -->
	<script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
	<!-- Bootstrap 3.3.2 JS -->
	<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
	<!-- AdminLTE App -->
	<script src="{{ asset ("/bower_components/admin-lte/dist/js/app.min.js") }}" type="text/javascript"></script>
	<!-- iCheck -->
	<script src="{{ asset("/bower_components/admin-lte/plugins/iCheck/icheck.min.js") }}" type="text/javascript"></script>
	</body>
@endsection
