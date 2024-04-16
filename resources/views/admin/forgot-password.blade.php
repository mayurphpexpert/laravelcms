<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Lara CMS</title>
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{asset('public/admin-assets/plugins/fontawesome-free/css/all.min.css')}}">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{asset('public/admin-assets/css/adminlte.min.css')}}">
		<link rel="stylesheet" href="{{asset('public/admin-assets/css/custom.css')}}">
	</head>
	<body class="hold-transition login-page">
		<div class="login-box">
			<!-- /.login-logo -->
            @include('admin.message')
			<div class="card card-outline card-primary">
			  	<div class="card-header text-center">
					<a href="#" class="h3">Admin Panel</a>
			  	</div>
			  	<div class="card-body">
					<p class="login-box-msg">Forgot Password</p>
					<form action="{{ route('admin.processForgotPassword') }}" method="post">
                        @csrf
				  		<div class="input-group mb-3">
							<input type="email" name="email" id="email" value="{{ old('email') }}"  class="form-control @error('email') is-invalid @enderror" placeholder="Email">
							<div class="input-group-append">
					  			<div class="input-group-text">
									<span class="fas fa-envelope"></span>
					  			</div>
							</div>
                            @error('email')
                                <p class="invalid-feedback">{{$message}}</p>                                
                            @enderror
				  		</div>
                        <div class="row">
							<!-- /.col -->
							<div class="col-4">
					  			<button type="submit" class="btn btn-primary btn-block">Submit</button>
							</div>
							<!-- /.col -->
				  		</div>
					</form>
		  			<p class="mb-1 mt-3 text-right">
				  		<a href="{{ route('admin.login') }}">login Here!</a>
					</p>					
			  	</div>
			  	<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
		<!-- ./wrapper -->
		<!-- jQuery -->
		<script src="{{asset('public/admin-assets/plugins/jquery/jquery.min.js')}}"></script>
		<!-- Bootstrap 4 -->
		<script src="{{asset('public/admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
		<!-- AdminLTE App -->
		<script src="{{asset('public/admin-assets/js/adminlte.min.js')}}"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="{{asset('public/admin-assets/js/demo.js')}}"></script>
	</body>
</html>