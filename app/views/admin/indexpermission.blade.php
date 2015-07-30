<?php
    $permns = Session::get('perms');
    
    foreach($permns as $permn)
    {
        $grpname = $permn->name;   
    }
?>

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
              
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Permission<div class="pull-right"><a href="{{ action('PermissionController@create') }}" class="">Add New</a></div></div>
          
          <div class="panel-body hide hidden">
            <p>...</p>
          </div>
            @if (count($permissions) == 0)
                <div class="alert alert-info">
                    <span>No Permissions Found.</span>
                </div>
            @else
            <table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Roles</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{{ $permission->modulename }}}</td>
                                        <td>
                                            <ul style="list-style-type: none; padding: 0">
                                                <li><?php 
                            
                                                            $permissions = str_replace(array('[',']'),'',$permission->permissions);
                                                            $permissions2 = str_replace('"','',$permissions);
                                                            $a = explode(',',$permissions2);
                            
                                                            for($i=0;$i<sizeof($a);$i++)
                                                            {
                                                                
                                                                echo $a[$i] . ' ';
                                                            }
                                                    ?>
                                                    {{-- explode(',' , $permission->permissions) --}}
                                                </li>
                                            </ul>
                                        </td>
                                        <td>
                                            <a href="{{ action('PermissionController@editpermission', $permission->id) }} {{-- route('cpanel.permissions.edit', array($permission->id)) --}}"
                                                class="btn" rel="tooltip" title="Edit Permission">
                                                <span><b>Edit</b></span>
                                            </a>
                                            <a href="{{ action('PermissionController@destroyperm', $permission->id) }}"
                                                class="btn btn-danger" 
                                                rel="tooltip" 
                                                title="Delete Permission" 
                                                data-method="delete"
                                                data-modal-text="delete this Permission?">
                                                <i class="icon-remove">Delete</i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach        
                </tbody>
            </table>
         @endif   

        </div>  



    </div>

</div>  
<!--/div-->

@stop