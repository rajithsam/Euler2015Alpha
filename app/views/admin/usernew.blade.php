@extends('layouts.admin.default')

@section('content')

<?php
$employeeId = Session::get('userEmployeeId');

$supervisorArr = array();

$companies = Company::all();
$departments = Department::all();
$jobTitles = JobTitle::all();

/*$managers = Employee::where('id', '<>', $employeeId)->get();
$supervisors = Employee::where('id', '<>', $employeeId)->get();

$managers = Employee::all();
$supervisors = Employee::all();*/

$managers = Employee::where('id', '<>', 1)->get();
$supervisors = Employee::where('id', '<>', 1)->get();

//$supervisorArr = array();

$roles = DB::table('groups')->get();

$companyArr[0] = '';
foreach ($companies as $company) {

    $companyArr[$company->id] = $company->name;

}

$departmentArr[0] = '';
foreach ($departments as $department) {

    $departmentArr[$department->id] = $department->name;

}

$jobTitleArr[0] = '';
foreach ($jobTitles as $jobTitle) {

    $jobTitleArr[$jobTitle->id] = $jobTitle->name;

}

$managerArr[0] = '';
foreach ($managers as $manager) {

   $fullname = $manager->firstname.', '.$manager->lastname;

    $managerArr[$manager->id] = $fullname;

}

$supervisorArr[0] = '';
foreach ($supervisors as $supervisor) {

   $fullname = $supervisor->firstname.', '.$supervisor->lastname;

    $supervisorArr[$supervisor->id] = $fullname;

}

$roleArr[0] = '';
foreach($roles as $role) {

    //echo $role->name;
    $roleArr[$role->id] = $role->name;

}
?>

<div class="page-container">

        <div class="row" style="padding-bottom:20px;">

          <div class="col-md-2 clearfix">

            <aside class="sidebar">
              <nav class="sidebar-nav">
                <ul id="menu">
                  <li>
                    <a href="{{ url('/admin/dashboard') }}">
                      <span class="sidebar-nav-item-icon fa fa-tachometer fa-lg"></span>                      
                      <span class="sidebar-nav-item">Dashboard</span>                      
                    </a>
                    
                  </li>
                  <li>
                    <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-users fa-lg"></span>                      
                      <span class="sidebar-nav-item">Employees</span>                      
                    </a>

                  <ul class="submenu-1 collapse">
                      <li><a href="{{ url('/admin/hr/employees') }}">All Employees</a></li>
                      <li><a href="{{ url('/admin/user/new') }}">Add New</a></li>
                  </ul>                    
                    
                  </li>                   
                  <li>
                      <a href="{{ url('/admin/scheduling') }}">
                      <span class="sidebar-nav-item-icon fa fa-calendar fa-lg"></span>
                      <span class="sidebar-nav-item">Schedule</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                  
                  <li>
                      <a href="{{ url('/admin/timeclock') }}">
                      <span class="sidebar-nav-item-icon fa fa-clock-o fa-lg"></span>
                      <span class="sidebar-nav-item">TimeClock</span>
                      </a>
                      <!--ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/timeclock') }}">Overtime</a></li>
                          <li><a href="{{ url('/admin/timeclock/report') }}">Overtime</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-folder-o fa-lg"></span>
                      <span class="sidebar-nav-item">Requests</span>
                      </a>
                      <!--ul class="submenu-1 collapse">
                          <li><a href="{{ url('/admin/timeclock') }}">Overtime</a></li>
                          <li><a href="{{ url('/admin/timeclock/report') }}">Overtime</a></li>
                      </ul-->
                  </li>
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-bar-chart fa-lg"></span>
                      <span class="sidebar-nav-item">Reports</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                 
                  <li>
                      <a href="">
                      <span class="sidebar-nav-item-icon fa fa-building fa-lg"></span>
                      <span class="sidebar-nav-item">Company</span>
                      </a>
                  </li>                  

                  <li class="hide hidden">
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Admin</span>
                      </a>
                  </li>                                                                        
                  <li>
                      <a href="#">
                      <span class="sidebar-nav-item-icon fa fa-cogs fa-lg"></span>
                      <span class="sidebar-nav-item">Settings</span>
                      </a>
                      <!--ul class="submenu-1 collapse hide hidden">
                          <li><a href="#">item 0.1</a></li>
                      </ul-->
                  </li>                                                                                          
                </ul>

        <!--a href="{{ action('PermissionController@index') }}" class="list-group-item">Permissions</a>
        <a href="{{ action('GroupController@index') }}" class="list-group-item">Group</a> 
        <a href="{{ url('/admin/payroll') }}" class="list-group-item"></a>
        <a href="{{ url('/admin/dashboard') }}" class="list-group-item active">Settings</a-->       


               </nav>

            </aside>                        

          </div><!--//.col-md-2-->

          <div id="content" class="col-md-10" role="main">

            <ol class="breadcrumb hide hidden">
              <li><a href="#">Home</a></li>
              <li class="active">Page</li>
            </ol>

            <h1 class="page-header">Add New</h1>

        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Add New User</div>
          <div class="panel-body">

            @if ($errors->has())
            <div class="alert alert-danger" role="alert">
                <ul style="list-style:none; margin:0; padding:0;">
                    @foreach ($errors->all() as $error)
                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            @elseif ( !empty($message) )
                <div class="alert alert-success" role="alert">
                    <ul style="list-style:none; margin:0; padding:0;">
                        
                        <li style="list-style-type:none; margin:0; padding:0;"><span class="glyphicon" aria-hidden="true"></span> <span class="sr-only">Success:</span>{{ $message }}</li>

                    </ul>
                </div>
            @endif            

            {{ Form::open(array('url' => '/admin/user/new', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}                

            <!--div class="form-group hide hidden">
                {{ Form::label('Designation', 'Designation', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('designation', array(0 => '', 1 => 'Manager', 2 => 'Supervisor', 3 => 'Employee'), 0, array('id' => 'designation', 'class' => 'form-control')) }}                
                </div>
            </div-->             
                        
            <div class="form-group">
                {{ Form::label('employe_number', 'Employee Number', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::text('employee_number', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Employee Number')) }}
                </div>
            </div>            

            <div class="form-group">           
                {{ Form::label('firstname', 'Firstname', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('firstname', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'First name')) }}            
                </div>                        
            </div>            

            <div class="form-group">           
                {{ Form::label('lastname', 'Lastname', array('class' => 'col-sm-2 control-label')) }}            
                <div class="col-sm-3">            
                {{ Form::text('lastname', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'last name')) }}            
                </div>            
            </div>            
            

            
            <div class="form-group">           
                {{ Form::label('middlename', 'Middlename', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::text('middlename', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Middle name')) }}            
                </div>                        
            </div>            
            
            <div class="form-group">           
                {{ Form::label('nick_name', 'Nick name', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::text('nick_name', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Nick name')) }}            
                </div>                        
            </div>

            <div class="form-group">
                {{ Form::label('Company', 'Company', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('company_id', $companyArr, 0, array('id' => '', 'class' => 'form-control')) }}
                </div>
            </div>

            <div class="form-group">
                {{ Form::label('Department/Campaign', 'Department/Campaign', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('department_id', $departmentArr, 0, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div>      

            <div class="form-group">
              {{ Form::label('Type of Employee', 'Type of Employee', array('class' => 'col-sm-2 control-label')) }}                         
              <div class="checkbox">                
                  <label for="Is Employee">
                  {{ Form::radio('is_employee_type', 'is_employee', true, array('id' => 'is-employee-type')) }}
                  Is Employee
                  </label>    

                  <label for="Is Manager">
                  {{ Form::radio('is_employee_type', 'is_manager', false, array('id' => 'is-employee-type')) }}
                  Is Manager
                  </label>    

                  <label for="Is Supervisor">
                  {{ Form::radio('is_employee_type', 'is_supervisor', false, array('id' => 'is-employee-type')) }}
                  Is Supervisor
                  </label> 
                  
              </div>   
            </div>          

            <!--div class="form-group">
              {{ Form::label('Type of Employee', 'Type of Employee', array('class' => 'col-sm-2 control-label')) }}                         
              <div class="checkbox">                
                  <label for="Is Manager">
                  {{ Form::checkbox('is_employee_type', 'is_manager', false, array('id' => 'is-employee-type')) }}
                  Is Manager
                  </label>    

              </div>   
            </div-->            

            <div class="form-group department-head-form-group">

                {{ Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('department_head', $managerArr, 0, array('id' => 'department-head-new', 'class' => 'form-control')) }}                
                </div>
            </div>

            <div class="form-group supervisor-form-group">
                {{ Form::label('Supervisor', 'Supervisor', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('supervisor_id', $supervisorArr, 0, array('id' => 'supervisor-new', 'class' => 'form-control')) }}                
                </div>
            </div>             

            <div class="form-group">
                {{ Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('position_id', $jobTitleArr, 0, array('id' => 'position', 'class' => 'form-control')) }}                
                </div>
            </div>       


            <div class="form-group">
                {{ Form::label('Role', 'Role', array('class' => 'col-sm-2 control-label')) }}           
                <div class="col-sm-3">
                {{ Form::select('role_id', $roleArr, 0, array('id' => '', 'class' => 'form-control')) }}                
                </div>
            </div>                   

            <div class="form-group">           
                {{ Form::label('email', 'Email', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::text('email', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Email')) }}            
                </div>                        
            </div>            
            
            <div class="form-group">           
                {{ Form::label('password', 'Password', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::password('password', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Password')) }}            
                </div>                        
            </div>   

            <div class="form-group">           
                {{ Form::label('Confirm password', 'Confirm Password', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">            
                {{ Form::password('password_confirmation', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Confirm Password')) }}            
                </div>                        
            </div>               

            <!--<div class="form-group">           
                {{ Form::label('Role', 'Role', array('class' => 'col-sm-2 control-label')) }}
                <div class="col-sm-3">           
                {{ Form::select('role', array('1' => 'Employee', '3' => 'Administrator', '4' => 'Supervisor', '7' => 'HR User'), '1', array('class' => 'form-control')) }}            
                </div>                        
            </div-->                                 

            <div class="col-sm-offset-2 col-sm-10">
                <div class="col-sm-3">                        
                {{ Form::submit('Add User', array('class' => '', 'class' => 'btn btn-primary')) }}
                </div>                                        
            </div>            
            
            {{ Form::close() }}   


          </div>        
        </div>           

          </div><!--//#content .col-md-9-->          

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop