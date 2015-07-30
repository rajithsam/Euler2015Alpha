@extends('layouts.admin.default')

@section('content')

<!--div class="container"-->
<div class="row">

    <div class="col-md-3 " id="sidebar" role="navigation">          
        <ul class="list-group">
            <a href="{{ url('/admin/dashboard') }}" class="list-group-item">Dashboard</a>
            <!--li><a href="{{ url('/admin/scheduling') }}" class="list-group-item">Employee Scheduling</a></li-->
            <a href="{{ url('/admin/employee') }}" class="list-group-item active">Employee</a>
            <a href="{{ url('/admin/timeclock') }}" class="list-group-item">TimeClock &amp; Attendance</a></li>
            <a href="{{ url('/admin/hr') }}" class="list-group-item">Human Resources</a>
            <a href="{{ url('/admin/payroll') }}" class="list-group-item">Payroll</a>
            <a href="{{ action('PermissionController@index') }}" class="list-group-item">Permissions</a>
            <a href="{{ action('GroupController@index') }}" class="list-group-item">Group</a>
        </ul>
    </div>

    <div class="col-md-9" id="content" role="main">
        
        {{ Form::open(array('url'=>'/createPermission','method'=>'post')) }}
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Permission<div class="pull-right"><a href="{{ url('/admin/user/new') }}" class="">Add New</a></div></div>
              
          <div class="panel-body">
            
                    {{ Form::label('modulename', 'Module Name') }}
                    <input type="text" name="modulename" placeholder="Module Name" required=""/><br>
                    {{ Form::label('permissions', 'Permissions') }}
                    <input type="text" name="permissions" id="permission-tags" required=""/><br>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <a href="{{ action('PermissionController@index') }}" class="btn">Cancel</a>
                    </div>

          </div><!--.panel-body-->

        {{-- Former::close() --}}

          <div class="panel-body hide hidden">
            <p>...</p>
          </div>
        </div>  
    </div>

</div>  
<!--/div-->

@stop