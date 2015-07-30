@extends('layouts.admin.default')

@section('content')

<div class="row">

<div role="tabpanel">

    <h3 style="font-size:14px; font-weight:bold;">Add User | Default Schedule</h3>  		        

    @if ($errors->has())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif        

    <?php echo Session::get('newEmployeeId'); ?>

    {{ Form::open(array('url' => '/admin/user/new/schedule/', 'id' => 'timeClockingForm', 'class' => 'form-horizontal')) }}				
    
    {{ Form::hidden('new_employee_id',  $newEmployeeId); }}

    <div class="form-group">   
        {{ Form::label('rest_day', 'Rest Day', array('class' => 'col-sm-2 control-label')) }}
        <div class="col-sm-2">
        {{ Form::text('rest_day', '', array('id' => '', 'class' => 'form-control', 'placeholder' => '')) }}                    
        </div>        
    </div>    

    <div class="form-group">   
        {{ Form::label('start_time', 'Start Time', array('class' => 'col-sm-2 control-label')) }}                    
        <div class="col-sm-2">
        {{ Form::text('start_time', '', array('id' => '', 'class' => 'form-control', 'placeholder' => '00:00')) }}                    
        </div>        
    </div>                 
    
    <div class="form-group">               
        {{ Form::label('start_ampm', 'Start AM/PM', array('class' => 'col-sm-2 control-label')) }}        
        <div class="col-sm-1">        
        {{ Form::select('start_ampm', array('am' => 'AM', 'pm' => 'PM'), '', array('class' => 'form-control')) }}                    
        </div>        
    </div>                                     

    <div class="form-group">           
        {{ Form::label('end_time', 'End Time', array('class' => 'col-sm-2 control-label')) }}                    
        <div class="col-sm-2">        
        {{ Form::text('end_time', '', array('id' => '', 'class' => 'form-control', 'placeholder' => '00:00')) }}                    
        </div>                
    </div>                 
    
    <div class="form-group">           
        {{ Form::label('end_ampm', 'End AM/PM', array('class' => 'col-sm-2 control-label')) }}        
        <div class="col-sm-1">                
        {{ Form::select('end_ampm', array('am' => 'AM', 'pm' => 'PM'), '', array('class' => 'form-control')) }}                    
        </div>                
    </div>                                         

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        {{ Form::submit('Add Schedule', array('class' => '', 'class' => 'btn btn-primary')) }}          
        </div>
    </div>      
    
    {{ Form::close() }}							

</div>

@stop