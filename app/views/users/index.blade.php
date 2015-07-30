<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Welcome to BackOffice TimeTracker! BPO TimeTracker!</title>

    <!-- Bootstrap core CSS -->
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">    

    <!-- Custom styles for this template -->
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="{{ URL::asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">

      <div class="row" id="login-section">
        
        <div class="login col-md-4 col-md-offset-4">

          <h1>            
            <span class="branding">
            <span class="back">Back</span><span class="office">Office</span>

            <span style="display:block; font-size:12px;">BPO TimeTracker</span>
            </span>
            
          </h1>

          <div id="login-content"> 

            @if ($errors->has())
            <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}
                    @endforeach
            </div>
            @endif            

            {{ Form::open( array( 'url' => 'users/login', 'class' => 'form-horizontal' ) ) }}
              <div class="form-group">
                <label for="inputEmployeeNumber" class="sr-only col-sm-2 control-label">Employee No.</label>
                <div class="col-sm-12">
                  <div class="input-group margin-bottom-sm">
                    <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                    <input type="employeeno" name="employeeno" class="form-control" id="inputEmployeeNumber" placeholder="Employee #">
                  </div>                  

                </div>
              </div>

              <div class="form-group">
                <label for="inputPassword3" class="sr-only col-sm-2 control-label">Password</label>
                <div class="col-sm-12">                  
                  <div class="input-group margin-bottom-sm">
                    <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>                  
                    <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
                  </div>                  

                </div>
              </div>

              <!--div class="form-group">
                <div class="col-sm-12">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox"> Remember me
                    </label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <div class="col-sm-12">
                  <button type="submit" class="btn btn-custom-default">Log in <i class="fa fa-sign-in"></i></button>
                </div>
              </div-->

              <div class="form-group">
                <div class="col-sm-12">
                  <button type="submit" class="btn btn-custom-default btn-block">Log in <i class="fa fa-sign-in"></i></button>                  
                  
                  <div class="checkbox">
                    <label>
                      <input type="checkbox"> Remember me
                    </label>
                  </div>
                </div>
              </div>

            {{ Form::close() }}

          <div><!--//#login-content-->          

        </div><!--//.login-->

      </div><!--//.row #login-section-->

    </div><!-- /.container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ URL::asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>
  </body>
</html>