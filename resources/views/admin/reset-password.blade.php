<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Lara CMS</title>
		<!-- Google Font: Source Sans Pro -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="{{asset('admin-assets/plugins/fontawesome-free/css/all.min.css')}}">
		<!-- Theme style -->
		<link rel="stylesheet" href="{{asset('admin-assets/css/adminlte.min.css')}}">
		<link rel="stylesheet" href="{{asset('admin-assets/css/custom.css')}}">
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
					<p class="login-box-msg">Reset Password</p>
					<form action="{{ route('admin.processResetPassword') }}" method="post">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
				  		<div class="input-group mb-3">
							<input type="password" name="new_password" id="new_password"  value=""  class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password">
							<div class="input-group-append">
					  			<div class="input-group-text">
									<span class="fas fa-lock"></span>
					  			</div>
							</div>
                            @error('new_password')
                                <p class="invalid-feedback">{{$message}}</p>                                
                            @enderror
				  		</div>
                          <div class="input-group mb-3">
							<input type="password" name="confirm_password" id="confirm_password"  value=""  class="form-control @error('confirm_password') is-invalid @enderror" placeholder="Confirm Password">
							<div class="input-group-append">
					  			<div class="input-group-text">
									<span class="fas fa-lock"></span>
					  			</div>
							</div>
                            @error('confirm_password')
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
				  		<a href="{{ route('admin.login') }}">Click Here to Login</a>
					</p>					
			  	</div>
			  	<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
		<!-- ./wrapper -->
		<!-- jQuery -->
		<script src="{{asset('admin-assets/plugins/jquery/jquery.min.js')}}"></script>
		<!-- Bootstrap 4 -->
		<script src="{{asset('admin-assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
		<!-- AdminLTE App -->
		<script src="{{asset('admin-assets/js/adminlte.min.js')}}"></script>
		<!-- AdminLTE for demo purposes -->
		<script src="{{asset('admin-assets/js/demo.js')}}"></script>
	</body>
</html>