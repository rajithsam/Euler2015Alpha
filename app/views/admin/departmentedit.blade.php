@extends('layouts.admin.default')

@section('content')

<?php
  $departmentCount = Department::count();
  $departments = Department::paginate(10);
  $departmentEdit = Department::find($id);

  $message = Session::get('message');
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

            <h1 class="page-header">Edit</h1>

        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Edit Department</div>
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

            {{ Form::open(array('url' => '/admin/department/edit/'.$id, 'id' => '', 'class' => 'form-inline')) }}              
            {{ Form::hidden('Department_id', $id); }}
            <div class="form-group">
              {{ Form::label('Department Name', 'Department Name', array('class' => 'sr-only')) }}           
              {{ Form::text('department_name', $departmentEdit->name, array('id' => '', 'class' => 'form-control', 'placeholder' => 'Department Name')) }}
            </div>            
              {{ Form::submit('Update', array('class' => '', 'class' => 'btn btn-custom-default')) }}
            </div>            
            
            {{ Form::close() }}  
          </div>        
          <?php if ( $departmentCount !== 0 ) { ?>
          <table class="table table-striped table-hover table-list display">
          <thead>
            <tr>
              <th>Name</th>
              <th>Date Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php               
            if ( !empty($departments) ) {
              foreach( $departments as $department ) {
          ?>
 
              <tr>
              <td>{{ $department->name }}</td>
              <td>{{ $department->created_at }}</td>
              <td>&nbsp;</td>
              </tr>

          <?php
              }
            }
          ?>
          </tbody>
          </table>
          <nav class="pull-right">
          {{ $departments->links(); }}             
          </nav>
          <?php } ?>

        </div>           

          </div><!--//#content .col-md-9-->          

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop