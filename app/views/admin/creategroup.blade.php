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
        
        {{Form::open(array('url'=>'/createGroup','method'=>'post'))}}
        <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading" style="font-size:14px; font-weight:bold;">Group<div class="pull-right"><a href="{{ url('/admin/user/new') }}" class="">Add New</a></div></div>
              
          <div class="panel-body">
            
            <!--div class="row">
                <div class="span12">
                    <div class="block"-->
                    <div>
                        <p class="block-heading" style="">Add New Group</p>
                        <div class="block-body" style="">
                            {{-- Former::xlarge_text('name','Name')->required() --}}
                            <input id="name" type="text" name="groupname" placeholder="Enter Group Name" required="" value="{{Input::old('name')}}" />
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Create</button>
                                <a href="{{ action('GroupController@index') }}" class="btn">Cancel</a>
                            </div>
                        </div>
                    </div>
                    <!--/div>
                </div>
            </div-->            


          </div><!--.panel-body-->
          


            <!--div class="row">
                <div class="span12">
                    <div class="block"-->
                    <div>    
                        <p class="block-heading" style="">Group Permissions</p>
                        <div class="block-body" style="">
                            @include('admin.permissions_form');

                        </div>
                    </div>
                    
                    <table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
                                <tr>
                                    <td colspan="2">
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">Save changes</button>
                                            <a href="{{ action('GroupController@index') }}" class="btn">Cancel</a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                    <!--/div>
                </div>
            </div-->

        {{-- Former::close() --}}

          <div class="panel-body hide hidden">
            <p>...</p>
          </div>
        </div>  
    </div>

</div>  
<!--/div-->

@stop