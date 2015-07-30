@extends('layouts.admin.default')

@section('content')

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
              <div class="panel-heading" style="font-size:14px; font-weight:bold;">Edit Group</div>
              <div class="panel-body">

                {{ Form::open(array('action' => array('GroupController@handleEditGroup'))) }}

                        {{ Form::label('groupname', 'Group Name') }}
                        <input type="text" name="groupname" required="" value="{{$group->name}}" />
                        <input type="hidden" name="id" value="{{$group->id}}" />
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-custom-default">Save changes</button>
                            <a href="{{ action('GroupController@index') }}" class="btn btn-custom-default">Cancel</a>
                        <br><br>
                        @include('admin.edit_permissions_form')
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-custom-default">Save changes</button>
                            <a href="{{ action('GroupController@index') }}" class="btn btn-custom-default">Cancel</a>
                        </div>    
            
                {{ Form::close() }}   

              </div>   
            </div>

        </div><!--//#content .col-md-9-->           

        </div><!--//.row-->

      </div><!--//.page-container-->

@stop