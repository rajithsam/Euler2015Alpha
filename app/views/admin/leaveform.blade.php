@extends('layouts.admin.default')

@section('content')

<?php

$employeeId = Session::get('userEmployeeId');

$companies = Company::all();
$departments = Department::all();
$jobTitles = JobTitle::all();
$managers = Employee::where('id', '<>', $employeeId)->get();

//var_dump($managers);

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
?>
<div class="page-container">

    <div class="row">

        <!--div class="col-md-3" id="sidebar" role="navigation">          
            <ul class="list-group">
                <li><a href="{{ url('/admin/dashboard') }}" class="list-group-item">Dashboard</a></li>
                <li><a href="{{ url('/admin/scheduling') }}" class="list-group-item">Employee Scheduling</a></li>
                <li><a href="{{ url('/admin/timeclock') }}" class="list-group-item">TimeClock &amp; Attendance</a></li>
                <li><a href="{{ url('/admin/hr') }}" class="list-group-item active">Human Resources</a></li>
                <li><a href="{{ url('/admin/payroll') }}" class="list-group-item">Payroll</a></li>
            </ul>
        </div-->
        <div class="col-md-12" id="content" role="main">

            <div class="panel panel-default">
              <!-- Default panel contents -->
              <div class="panel-heading" style="font-size:14px; font-weight:bold;">Leave</div>
              <div class="panel-body">

                @if ($errors->has())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif             

                {{ Form::open(array('url' => '/admin/user/leave/', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}                

                {{ Form::hidden('employeeid', $employeeId); }}

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
                    {{ Form::label('Job Title', 'Job Title', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::select('position_id', $jobTitleArr, 0, array('id' => '', 'class' => 'form-control')) }}                
                    </div>
                </div>  

                <div class="form-group">
                    {{ Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::select('department_head', $managerArr, 0, array('id' => '', 'class' => 'form-control')) }}                
                    </div>
                </div>                                   

                <!--div class="form-group">
                    {{ Form::label('Department Head', 'Department Head', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::text('department_head', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Department Head')) }}
                    </div>
                </div-->             


                <h5>Nature of Leave Application</h5>
                
                <div class="form-group">            
                    <div class="checkbox">                
                        <label for="Vacation Leave">
                        {{ Form::radio('nature_of_leave', 'Vacation Leave') }}
                        Vacation Leave
                        </label>    

                        <label for="Sick Leave">
                        {{ Form::radio('nature_of_leave', 'Sick Leave') }}
                        Sick Leave
                        </label> 

                        <label for="Maternity/Paternity Leave">
                        {{ Form::radio('nature_of_leave', 'Maternity/Paternity Leave') }}
                        Maternity/Paternity Leave
                        </label>                       
                    </div>


                    <div class="checkbox">                
                        <label for="Vacation Leave">
                        {{ Form::radio('nature_of_leave', 'Vacation Leave') }}
                        Vacation Leave
                        </label> 
                        <label for="Others">
                        {{ Form::radio('nature_of_leave', 'others') }}
                        Others
                        </label>                   
                    </div> 

                </div>                                      

                <div class="form-group">
                    {{ Form::label('Other Nature of Leave', 'Other Nature of Leave', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::text('other_nature_of_leave', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Other Nature of Leave')) }}
                    </div>
                </div>                                     

                <div class="form-group"> 
                    <div class="checkbox">                
                        <label for="With Pay">
                        {{ Form::radio('with_pay', '1') }}
                        With Pay
                        </label> 
                        <label for="Without Pay">
                        {{ Form::radio('with_pay', '0') }}
                        Without Pay
                        </label>                   
                    </div> 
                </div>     

                <div class="form-group">
                    {{ Form::label('Number of Day/s', 'Number of Day/s', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::text('number_of_days', '', array('id' => '', 'class' => 'form-control  ', 'placeholder' => 'Number of days')) }}
                    </div>
                </div>   

                <div class="form-group">
                    {{ Form::label('From', 'From', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::text('from', '', array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => '')) }}
                    </div>
                </div>   

                <div class="form-group">
                    {{ Form::label('To', 'To', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::text('to', '', array('id' => '', 'class' => 'form-control datepicker', 'placeholder' => '')) }}
                    </div>
                </div>            


                <div class="form-group">
                    {{ Form::label('Reason/s', 'Reason/s', array('class' => 'col-sm-2 control-label')) }}           
                    <div class="col-sm-3">
                    {{ Form::textarea('reasons', '', array('id' => '', 'class' => 'form-control', 'placeholder' => 'Reason/s')) }}
                    </div>
                </div>            

                <div class="col-sm-offset-2 col-sm-10">
                    <div class="col-sm-3">                        
                    {{ Form::submit('File Leave', array('class' => '', 'class' => 'btn btn-primary')) }}
                    </div>                                        
                </div>

                {{ Form::close() }}   

              </div>        
            </div>

        </div>

    </div>

</div>

@stop