<?php  
$employeeId = Session::get('userEmployeeId');
$userId = Session::get('userId');

$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

if( !empty($userGroups) ) {

  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

}
  
$currentUser = Sentry::getUser();


//ADMINISTRATOR
if( !empty($groups) ) :
  if( strcmp(strtolower($groups->name), strtolower('Administrator')) === 0 ) :              

    $employees = DB::table('employees')
      ->where('id', '<>', $employeeInfo[0]->id)
      ->get();  
    
  elseif( strcmp(strtolower($groups->name), strtolower('Manager')) === 0 ) :                  

    $employees = DB::table('employees')
      ->where('id', '<>', $employeeInfo[0]->id)  
      //->where('manager_id', $employeeInfo[0]->id)
      //->orWhere('supervisor_id', $employeeInfo[0]->id)
      ->where('manager_id', $employeeInfo[0]->id)
      ->get();  

  elseif( strcmp(strtolower($groups->name), strtolower('Supervisor')) === 0 ) :                        

    $employees = DB::table('employees')
      ->where('id', '<>', $employeeInfo[0]->id)  
      //->where('manager_id', $employeeInfo[0]->id)
      //->orWhere('supervisor_id', $employeeInfo[0]->id)
      ->where('supervisor_id', $employeeInfo[0]->id)
      ->get();      

  endif;
endif;

$employeeArr[0] = 'Select Employee to';
$employeeIdArr = array();
if( !empty($employees) ) {

    foreach($employees as $employee) {
      $employeeArr[$employee->id] = $employee->firstname. ', ' .$employee->lastname;
      $employeeIdArr[] = $employee->id; //use in checking absences

  }
  
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Welcome to Back Office TimeTracker! BPO TimeTracker!</title>

    <!-- Bootstrap core CSS -->
    <!--link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css"-->    

    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery-ui.css') }}">     

    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>    

    <!-- Font Awesome -->
    <!--link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"-->   
    <link rel="stylesheet" href="{{ URL::asset('assets/css/font-awesome.min.css') }}">   

    <!-- Custom styles for this template -->
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">   

    <link href="{{ URL::asset('assets/css/metisMenu.min.css') }}" rel="stylesheet">    

    <link href="{{ URL::asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet"> 

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src={{ URL::asset('assets/js/ie8-responsive-file-warning.js') }}"></script><![endif]-->
    <script src="{{ URL::asset('assets/js/ie-emulation-modes-warning.js') }}"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


  </head>

  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><span>Welcome to</span> <strong><span>BackOffice</span> TimeTracker!</strong> BPO TimeTracker!</a>
        </div>

        <div id="navbar" class="collapse navbar-collapse">

          <ul class="nav navbar-nav navbar-right">            

            <?php
            /*
            Employee
            Supervisor
            Manager
            Payroll
            Administrator
            */

            if( !empty($groups) ) :

              if( strcmp(strtolower($groups->name), strtolower('Employee')) === 0 ) :              
            ?>

            <li><a href="{{ URL::to('users/logout') }}">Log Out <i class="fa fa-sign-out"></i></a></li>                            

            <?php elseif ( strcmp(strtolower($groups->name), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Manager')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Supervisor')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Payroll')) === 0 ) : ?>
            <li style="color:white;"><a href="#">Welcome <?php echo $employeeInfo[0]->firstname. ', ' .$employeeInfo[0]->lastname; ?></a></li>
            <li id="timesheet-link" class="hide hidden"><a href="{{ url('/employee/clocking') }}"><i class="fa fa-clock-o"></i> Timesheet</a></li>
            <li class="active"><a href="{{ url('/admin/dashboard') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
            <li><a href="{{ URL::to('users/logout') }}">Log Out <i class="fa fa-sign-out"></i></a></li>            

          <?php
            endif;

          endif;
          ?>

          </ul>

            <div class="navbar-form navbar-right">
            <?php
            /*
            Employee
            Supervisor
            Manager
            Payroll
            Administrator
            */

            if( !empty($groups) ) :

              if ( strcmp(strtolower($groups->name), strtolower('Administrator')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Manager')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Supervisor')) === 0 ||
                  strcmp(strtolower($groups->name), strtolower('Payroll')) === 0 ) : ?>

            {{ Form::open(array('route' => 'searchTimesheet', 'method' => 'get', 'id' => 'searchTimesheetForm', 'class' => 'form-inline')) }}  

              {{-- Form::hidden('search', 'search')--}}
              {{ Form::label('Employees', 'Employees', array('class' => 'sr-only')) }}
              {{ Form::select('employeeid', $employeeArr, '', array('id' => 'employee-id', 'class' => 'form-control')) }}

              {{-- Form::button('<i class="fa fa-search"></i>', array('id' => 'search-timesheet-btn', 'class' => 'btn btn-custom-default')) --}}
              {{ Form::submit('Edit', array('id' => 'search-timesheet-btn', 'class' => 'btn btn-custom-default')) }}

            {{ Form::close() }}

            <?php
              endif;

            endif;
            ?>  
          </div>          

        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">    
      @yield('content')
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script-->    
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>    
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
    <!--script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script-->    
    <script src="{{ URL::asset('assets/js/jquery-ui.min.js') }}"></script>     
    <script src="{{ URL::asset('assets/js/scripts.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ URL::asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>

    <!-- Moment.js -->
    <!--script src="{{ URL::asset('assets/js/moment.js') }}"></script-->

    <!-- Datatables code -->
    <script src="{{ URL::asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery.jeditable.js') }}"></script>
    <!-- Bootbox code http://bootboxjs.com/-->
    <script src="{{ URL::asset('assets/js/bootbox.js') }}"></script>  

    <!-- Twitter Bootstrap specific plugin -->
    <script src="{{ URL::asset('assets/js/collapse.js') }}"></script>
    <script src="{{ URL::asset('assets/js/transition.js') }}"></script>
    <script src="{{ URL::asset('assets/js/dropdown.js') }}"></script>

    <script src="{{ URL::asset('assets/js/holder.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/metisMenu.min.js') }}"></script>

    <script>
    
    /*function updateClock ( )
    {
        var currentTime = new Date ();
        var currentHours = currentTime.getHours ( );
        var currentMinutes = currentTime.getMinutes ( );
        var currentSeconds = currentTime.getSeconds ( );
     
        // Pad the minutes and seconds with leading zeros, if required
        currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
        currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
     
        // Choose either "AM" or "PM" as appropriate
        var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
     
        // Convert the hours component to 12-hour format if needed
        //currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
     
        // Convert an hours component of "0" to "12"
        currentHours = ( currentHours == 0 ) ? 12 : currentHours;
     
        // Compose the string for display
        var currentTimeString = currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
        //var currentTimeString = currentHours + ":" + currentMinutes + " " + timeOfDay;
         
         
        $("#clock").html(currentTimeString);
             
     }
     
    $(document).ready(function()
    {
       setInterval('updateClock()', 1000);
    });*/


    </script>

    <script>

      /**
      *
      * SIDEBAR
      *
      */

      $(function () {
        $('#collapse1, #collapse2, #collapse3').collapse('hide');        
        $('#collapse4, #collapse5, #collapse6').collapse('show');        

        $('#menu').metisMenu();
      });     
    </script>


    <script>
    //Server Time
    function updateServerTime() {

      $.get("{{ route('updateServerTime') }}", function(data, status){    
      
        //console.log("Data: " + data + "\nStatus: " + status);
        $("#clock").html(data); 

      }); 
       
    }
    setInterval('updateServerTime()', 1000);


    //returning and using the ajax value of $.ajax
    //http://stackoverflow.com/questions/14220321/how-to-return-the-response-from-an-asynchronous-call
    //http://stackoverflow.com/questions/20047163/how-to-get-return-value-in-a-function-with-inside-ajax-call-jquery

    //how can i use the data in jquery $.get outside of it
    //http://stackoverflow.com/questions/5316697/jquery-return-data-after-ajax-call-success

    function getServerDateTime(handleData) {

      var serverDateTime;

      var getServerTime = $.ajax({
          type: "GET",
          url : "{{ route('getServerDateTime') }}", //http://localhost:8000/employee",          
          success : function(data) {
              
            handleData(data);

          }
      },"json");

    }

    </script>      

    <script>  

      $(document).ready(function() { 

        /*$.ajax({
            type: "POST",
            url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
            data : dataString,
            success : function(data) {
              //console.log(data);*/  

              $.ajax({
                  type: "GET",
                  url : "{{ route('redrawTimesheet') }}", //http://localhost:8000/employee",
                  data : '',
                  success : function(data) {
                    //console.log(data);
                    var obj = JSON.parse(data);

                   // console.log(obj.data);

                    $.each(obj.data, function(i, item) {
                      
                      $(".timesheet-id-"+item.id).text(item.id);
                      $(".timesheet-daydate-"+item.id).text(item.daydate);
                      $(".timesheet-schedule-"+item.id).text(item.schedule);
                      /*$(".timesheet-inout1-"+item.id).text(item.in_out_1);
                      $(".timesheet-inout2-"+item.id).text(item.in_out_2);
                      $(".timesheet-inout3-"+item.id).text(item.in_out_3);*/
                      $(".timesheet-in1-"+item.id).text(item.in_1);
                      $(".timesheet-out1-"+item.id).text(item.out_1);
                      $(".timesheet-in2-"+item.id).text(item.in_2);
                      $(".timesheet-out2-"+item.id).text(item.out_2);
                      $(".timesheet-in3-"+item.id).text(item.in_3);
                      $(".timesheet-out3-"+item.id).text(item.out_3);
                      $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);
                      $(".timesheet-totalhours-"+item.id).text(item.total_hours);
                      $(".timesheet-workhours-"+item.id).text(item.work_hours);
                      $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
                      $(".timesheet-tardiness-"+item.id).text(item.tardiness);
                      $(".timesheet-undertime-"+item.id).text(item.undertime);
                      $(".timesheet-otstatus-"+item.id).html(item.overtime_status);

                      /*$(".timesheet-inout1-"+item.id).editable("{{ route('adminTimesheetSave') }}", { 
                          //loadurl : "{{ route('loadTimesheet') }}",
                          //indicator : "<img src='img/indicator.gif'>",
                          indicator : "Saving...",
                          tooltip   : "Click to edit...",
                          style  : "inherit",
                          id     : item.id,

                          callback : function(value, settings) {

                            $.ajax({
                                type: "GET",
                                url : "{{ route('redrawTimesheet') }}", //http://localhost:8000/employee",
                                data : '',
                                success : function(data) {

                                var obj = JSON.parse(data);

                                  $.each(obj.data, function(i, item) {

                                    $(".timesheet-id-"+item.id).text(item.id);
                                    $(".timesheet-daydate-"+item.id).text(item.daydate);
                                    $(".timesheet-schedule-"+item.id).text(item.schedule);
                                    $(".timesheet-inout1-"+item.id).text(item.in_out_1);
                                    $(".timesheet-inout2-"+item.id).text(item.in_out_2);
                                    $(".timesheet-inout3-"+item.id).text(item.in_out_3);
                                    $(".timesheet-totalhours-"+item.id).text('Total Hours');
                                    $(".timesheet-workhours-"+item.id).text('Work Hours');
                                    $(".timesheet-totalovertime-"+item.id).text('Total Overtime');
                                    $(".timesheet-tardiness-"+item.id).text('Tardiness');
                                    $(".timesheet-undertime-"+item.id).text('Undertime');
                                    $(".timesheet-otstatus-"+item.id).text('Undertime');  

                                  });                                

                                }
                            },"json");


                          },
                          submitdata : function ( value, settings ) {
                            //console.log(settings.id);
                            return {
                              "row_id":  settings.id,
                              "column": 3
                            };

                         }                        
                      });*/  
                      
                      /*$(".timesheet-inout2-"+item.id).editable("{{ route('adminTimesheetSave') }}", { 
                          //indicator : "<img src='img/indicator.gif'>",
                          tooltip   : "Click to edit...",
                          style  : "inherit"
                      });*/

                      /*$(".timesheet-inout3-"+item.id).editable("{{ route('adminTimesheetSave') }}", { 
                          //indicator : "<img src='img/indicator.gif'>",
                          tooltip   : "Click to edit...",
                          style  : "inherit"
                      });*/

                    });

                    /*var timeClockingBtn = $('#time-clocking-btn').text();

                    if (timeClockingBtn.toLowerCase() === 'time in') {

                        $('#time-clocking-btn.time-in').removeClass('hide').show();                               
                        $('#time-clocking-btn.time-out').addClass('hide').hide();              

                    }*/ 
                       
                  }
              },"json");

                                         
        /*    }
        },"json");*/



            //http://www.datatables.net/examples/api/row_details.html 
            //https://datatables.net/reference/api/row%28%29.index%28%29

            //td.ot-status-btn .ot-apply-btn
            $('#timesheet tbody, #timesheet-ajax tbody').on('click', 'td.ot-status-btn .ot-apply-btn', function () {

                  console.log('td.ot-status-btn .ot-apply-btn');

                  var tr = $(this).closest('tr');
                  ////var row = table.row( tr );
                  var rowIdx = $(this).parent().index(); //row.index(); // start at 0
                  ////var rowObj = row.data();    
                  
                  var TimesheetId = tr.attr('id'); //rowObj.id;
                  var dataType;
                  //var dataString;

                  var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;

                  //console.log(dataString);

                  $.ajax({                    
                    type: "POST",
                    url : "{{ route('redrawOvertimeStatus') }}",
                    data : dataString,
                    success : function(data) {
                      
                      var overtimeStatusText = $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').text();

                      if ('Apply OT' === overtimeStatusText) {

                        //console.log(data);

                        //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;                         
                        $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').remove();
                        $('td.ot-status-btn:eq(' + rowIdx + ')').html('<span class=\"label label-success\" style=\"padding: 2px 4px; font-size: 11px;\">Pending</span>');
                          
                      }

                        $.ajax({
                          type: "GET",
                          url : "{{ route('redrawTimesheet') }}", //http://localhost:8000/employee",
                          data : '',
                          success : function(data) {
                            //console.log(data);
                            var obj = JSON.parse(data);

                           // console.log(obj.data);

                            $.each(obj.data, function(i, item) {
                              
                              $(".timesheet-id-"+item.id).text(item.id);
                              $(".timesheet-daydate-"+item.id).text(item.daydate);
                              $(".timesheet-schedule-"+item.id).text(item.schedule);
                              /*$(".timesheet-inout1-"+item.id).text(item.in_out_1);
                              $(".timesheet-inout2-"+item.id).text(item.in_out_2);
                              $(".timesheet-inout3-"+item.id).text(item.in_out_3);*/
                              $(".timesheet-in1-"+item.id).text(item.in_1);
                              $(".timesheet-out1-"+item.id).text(item.out_1);
                              $(".timesheet-in2-"+item.id).text(item.in_2);
                              $(".timesheet-out2-"+item.id).text(item.out_2);
                              $(".timesheet-in3-"+item.id).text(item.in_3);
                              $(".timesheet-out3-"+item.id).text(item.out_3); 
                              $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);                                                   
                              $(".timesheet-totalhours-"+item.id).text(item.total_hours);
                              $(".timesheet-workhours-"+item.id).text(item.work_hours);
                              $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
                              $(".timesheet-tardiness-"+item.id).text(item.tardiness);
                              $(".timesheet-undertime-"+item.id).text(item.undertime);
                              $(".timesheet-otstatus-"+item.id).html(item.overtime_status);

                            });
     
                          }
                      },"json");


                    },
                    dataType: dataType    
                  });

             } );           
            

            //Summary Computation Init
            $.ajax({
                type: "GET",
                url : "{{ route('redrawSummary') }}", //http://localhost:8000/employee",
                data : '',
                success : function(data) {
                  
                  var obj = JSON.parse(data);

                  var lates = '', undertime = '', absences = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
                  var regular = '', regularOt = '', restDay = '', restDayOt = '';
                  var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                  var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                  //With Night Diff

                  var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
                  var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                  var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                  //SUMMARY: 1st column
                  lates = obj.data[0].tardiness;
                  undertime = obj.data[0].undertime;
                  absences = obj.data[0].absences; 

                  paidVacationLeave = obj.data[0].paid_vacation_leave; 
                  paidSickLeave = obj.data[0].paid_sick_leave;
                  leaveWithoutPay = obj.data[0].leave_without_pay;
                  maternityLeave = obj.data[0].maternity_leave;
                  paternityLeave = obj.data[0].paternity_leave;  

                  //SUMMARY: 2nd Column
                  regular = obj.data[0].regular;                  
                  regularOt = obj.data[0].regular_overtime;                  
                  restDay = obj.data[0].rest_day;
                  restDayOt = obj.data[0].rest_day_overtime;

                  //With Night Diff
                  regularOtNd = obj.data[0].regular_overtime_night_diff;
                  regularNd = obj.data[0].regular_night_differential;
                  restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                  restDayNd = obj.data[0].rest_day_night_diff;


                  //SUMMARY: 3rd Column
                  restDaySpecialHoliday = obj.data[0].rest_day_special_holiday;
                  restDaySpecialHolidayOt = obj.data[0].rest_day_special_holiday_overtime;
                  restDayLegalHoliday = obj.data[0].rest_day_legal_holiday;
                  restDayLegalHolidayOt = obj.data[0].rest_day_legal_holiday_overtime;

                  //With Night Diff
                  restDaySpecialHolidayOtNd = obj.data[0].rest_day_special_holiday_overtime_night_diff;
                  restDaySpecialHolidayNd = obj.data[0].rest_day_special_holiday_night_diff;
                  restDayLegalHolidayOtNd = obj.data[0].rest_day_legal_holiday_overtime_night_diff;
                  restDayLegalHolidayNd = obj.data[0].rest_day_legal_holiday_night_diff;


                  //SUMMARY: 4th Column
                  specialHoliday = obj.data[0].special_holiday;
                  specialHolidayOt = obj.data[0].special_holiday_overtime;
                  legalHoliday = obj.data[0].legal_holiday;
                  legalHolidayOt = obj.data[0].legal_holiday_overtime;

                  //With Night Diff
                  specialHolidayOtNd = obj.data[0].special_holiday_overtime_night_diff;
                  specialHolidayNd = obj.data[0].special_holiday_night_diff;
                  legalHolidayOtNd = obj.data[0].legal_holiday_overtime_night_diff;
                  legalHolidayNd = obj.data[0].legal_holiday_night_diff;

                  //SUMMARY: 1st Column
                  if (lates !== '0') {

                    $('#lates-ut').text(lates);

                  } 

                  if (undertime !== '0') {

                    $('#lates-ut').text(undertime);

                  }    

                  if (lates !== '0' && undertime !== '0') {

                    $('#lates-ut').text(lates + ' / ' + undertime);

                  }

                  if (absences !== '0') {

                    $('#absences').text(absences);

                  }

                  if (paidVacationLeave !== '0') {

                    $('#paid-sl').text(paidVacationLeave);

                  }

                  if (paidSickLeave !== '0') {

                    $('#paid-vl').text(paidSickLeave);

                  }

                  if (leaveWithoutPay !== '0') {

                    $('#leave-without-pay').text(leaveWithoutPay);

                  }

                  if (maternityLeave !== '0') {

                    $('#maternity-leave').text(maternityLeave);

                  }

                  if (paternityLeave !== '0') {

                    $('#paternity-leave').text(paternityLeave);

                  }                                                                        


                  //SUMMARY: 2nd Column
                  if (regular !== '0') {

                    $('#regular').text(regular);

                  }

                  if (regularOt !== '0') {

                    $('#reg-ot').text(regularOt);

                  }

                  if (restDay !== '0') {

                    $('#rd').text(restDay);

                  }                  

                  if (restDayOt !== '0') {

                    $('#rd-ot').text(restDayOt);

                  }

                  //With Night Diff
                  if (regularOtNd !== '0') {

                    $('#reg-ot-nd').text(regularOtNd);

                  }

                  if (regularNd !== '0') {

                    $('#reg-nd').text(regularNd);

                  }                                    

                  if (restDayOtNd !== '0') {

                    $('#rd-ot-nd').text(restDayOtNd);

                  }                  

                  if (restDayNd !== '0') {

                    $('#rd-nd').text(restDayNd);

                  }


                  //SUMMARY: 3rd Column 
                  if (restDaySpecialHoliday !== '0') {

                    $('#rd-spl-holiday').text(restDaySpecialHoliday);

                  }

                  if (restDaySpecialHolidayOt !== '0') {

                    $('#rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

                  }

                  if (restDayLegalHoliday !== '0') {

                    $('#rd-legal-holiday').text(restDayLegalHoliday);

                  }

                  if (restDayLegalHolidayOt !== '0') {

                    $('#rd-legal-holiday-ot').text(restDayLegalHolidayOt);

                  }                  

                  //With Night Diff 
                  if (restDaySpecialHolidayOtNd !== '0') {

                    $('#rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

                  }

                  if (restDaySpecialHolidayNd !== '0') {

                    $('#rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

                  }

                  if (restDayLegalHolidayOtNd !== '0') {

                    $('#rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

                  }

                  if (restDayLegalHolidayNd !== '0') {

                    $('#rd-legal-holiday-nd').text(restDayLegalHolidayNd);

                  }                  
                                    


                  //SUMMARY: 4th Column
                  
                  if (specialHoliday !== '0') {

                    $('#spl-holiday').text(specialHoliday);

                  }                                                                     

                  if (specialHolidayOt !== '0') {

                    $('#spl-holiday-ot').text(specialHolidayOt);

                  } 

                  if (legalHoliday !== '0') {

                    $('#legal-holiday').text(legalHoliday);

                  } 

                  if (legalHolidayOt !== '0') {

                    $('#legal-holiday-ot').text(legalHolidayOt);

                  }    

                  //With Night Diff
                  if (specialHolidayOtNd !== '0') {

                    $('#spl-holiday-ot-nd').text(specialHolidayOtNd);

                  }                                                                     

                  if (specialHolidayNd !== '0') {

                    $('#spl-holiday-nd').text(specialHolidayNd);

                  } 

                  if (legalHolidayOtNd !== '0') {

                    $('#legal-holiday-ot-nd').text(legalHolidayOtNd);

                  } 

                  if (legalHolidayNd !== '0') {

                    $('#legal-hoiday-nd').text(legalHolidayNd);

                  }                                                                     

            
                }
            },"json");        

            //Time In button
            $('#time-clocking-btn.time-in').click(function(e) {                                                    
                e.preventDefault();

                $("#wait").html("<img src='{{ URL::asset('assets/img/') }}/indicator.gif'>");  
              
                var employeeNumber = $('#employee-number').val();
                var dayDate = $('#day-date').val();
                var schedIn = $('#sched-in').val();
                var schedOut = $('#sched-out').val();
                var timeIn1 = $('#time-in-1').val();
                var timeIn2 = $('#time-in-2').val();
                var timeIn3 = $('#time-in-3').val();
                var forgotYesterdayTimeOut = $('#time-in-1').val();

                var timeClocking = 'in';             

                if (forgotYesterdayTimeOut === '') {
                                        
                    forgotYesterdayTimeOut = 0;                                        

                } else {

                    $('#forgot-yesterday-timeout').val();           

                }

                var dataString = 'timeclocking=' + timeClocking + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
                    dataString += '&timein1=' + timeIn1 + '&timein2=' + timeIn2 + '&timein3=' + timeIn3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;
        
                //http://stackoverflow.com/questions/2342371/jquery-loop-on-json-data-using-each
                //http://jsfiddle.net/S99My/

                console.log(dataString);

                $.ajax({
                    type: "POST",
                    url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
                    data : dataString,
                    success : function(data) {
                      console.log(data);  

                      $.ajax({
                          type: "GET",
                          url : "{{ route('redrawTimesheet') }}", //http://localhost:8000/employee",
                          data : '',
                          success : function(data) {
                            //console.log(data);
                            var obj = JSON.parse(data);

                           // console.log(obj.data);

                            $.each(obj.data, function(i, item) {
                              
                              /*var tableOut = '';
                              tableOut = '<tr>'
                              tableOut = '<td class="timesheet-id-' + item.id + '">' + item.id + '</td>';                 
                              tableOut = '<td class="timesheet-daydate-' + item.id + '">' + item.daydate + '</td>';
                              tableOut = '<td class="timesheet-schedule-' + item.id + '">' + item.schedule + '</td>';
                              tableOut = '<td class="timesheet-inout1-' + item.id + '">' + item.in_out_1 + '</td>';                  
                              tableOut = '<td class="timesheet-inout2-' + item.id + '">' + item.in_out_2 + '</td>';
                              tableOut = '<td class="timesheet-inout3-' + item.id + '">' + item.in_out_3 + '</td>';
                              tableOut = '<td class="timesheet-totalhours-' + item.id + '">Total Hours</td>';
                              tableOut = '<td class="timesheet-workhours-' + item.id + '">Work Hours</td>';                 
                              tableOut = '<td class="timesheet-totalovertime-' + item.id + '">Total Overtime</td>';
                              tableOut = '<td class="timesheet-tardiness-' + item.id + '">Tardiness</td>';                
                              tableOut = '<td class="timesheet-undertime-' + item.id + '">Undertime</td>';
                              tableOut = '<td class="timesheet-otstatus-' + item.id + '">OT Status</td>';
                              tableOut = '</tr>';*/

                              $(".timesheet-id-"+item.id).text(item.id);
                              $(".timesheet-daydate-"+item.id).text(item.daydate);
                              $(".timesheet-schedule-"+item.id).text(item.schedule);
                              /*$(".timesheet-inout1-"+item.id).text(item.in_out_1);
                              $(".timesheet-inout2-"+item.id).text(item.in_out_2);
                              $(".timesheet-inout3-"+item.id).text(item.in_out_3);*/
                              $(".timesheet-in1-"+item.id).text(item.in_1);
                              $(".timesheet-out1-"+item.id).text(item.out_1);
                              $(".timesheet-in2-"+item.id).text(item.in_2);
                              $(".timesheet-out2-"+item.id).text(item.out_2);
                              $(".timesheet-in3-"+item.id).text(item.in_3);
                              $(".timesheet-out3-"+item.id).text(item.out_3);
                              $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);                                    
                              $(".timesheet-totalhours-"+item.id).text(item.total_hours);
                              $(".timesheet-workhours-"+item.id).text(item.work_hours);
                              $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
                              $(".timesheet-tardiness-"+item.id).text(item.tardiness);
                              $(".timesheet-undertime-"+item.id).text(item.undertime);
                              $(".timesheet-otstatus-"+item.id).html(item.overtime_status);                             

                            });

                            var timeClockingBtn = $('#time-clocking-btn').text();

                            //console.log(timeClockingBtn);
                            
                            if (timeClockingBtn.toLowerCase() === 'time in') {

                                $('#time-clocking-btn.time-out').removeClass('hide').show(); 
                                $('#time-clocking-btn.time-in').addClass('hide').hide();                
                                $("#wait").hide();

                            }    

                               
                          }
                      },"json");




                                                 
                    }
                },"json");                              

                /*table.destroy(); 
                //$('#timesheet').empty(); // empty in case the columns change                
                //table.clear();
                //table.draw();  

                table = $('#timesheet').DataTable({                    
                    "processing": true,
                    "serverSide": true,      
                    "language": {
                        "processing": "DataTables is currently busy"
                    },
                    "createdRow": function( row, data, dataIndex ) {   //https://datatables.net/reference/option/rowCallback

                      getServerDateTime(function(result) {    
                        var result;
                        //console.log(data);                      
                        if(data.daydate === result) {

                          //console.log(data.daydate); 
                          //$(row).css( "background-color", '#9bd8eb' );
                          $(row).css( "color", 'black' );
                          //$('td:eq(0)', row).html( '<strong>' + data.id + '</strong>' );
                          $('td:eq(0)', row).html( '<strong>' + data.daydate + '</strong>' );                      
                          $('td:eq(1)', row).html( '<strong>' + data.schedule + '</strong>' );
                          $('td:eq(2)', row).html( '<strong>' + data.in_out_1 + '</strong>' );
                          $('td:eq(3)', row).html( '<strong>' + data.in_out_2 + '</strong>' );
                          $('td:eq(4)', row).html( '<strong>' + data.in_out_3 + '</strong>' );
                          $('td:eq(5)', row).html( '<strong>' + data.total_hours + '</strong>' );
                          $('td:eq(6)', row).html( '<strong>' + data.work_hours + '</strong>' );
                          $('td:eq(7)', row).html( '<strong>' + data.total_overtime + '</strong>' );
                          $('td:eq(8)', row).html( '<strong>' + data.tardiness + '</strong>' );
                          $('td:eq(9)', row).html( '<strong>' + data.undertime + '</strong>' );
                          //$('td:eq(10)', row).html( '<strong>' + data.daydate + '</strong>' );

                        }

                      });

                    },                          
                    "ajax": "{{ route('redrawTimesheet') }}",
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "columns": [    

                        { "data": "id" },
                        { "data": "daydate" },
                        //{ "data": "schedule_in" },
                        //{ "data": "schedule_out" },
                        { "data": "schedule" },
                        //{ "data": "time_in_1" },
                        //{ "data": "time_out_1" }, 
                        //{ "data": "time_in_2" },
                        //{ "data": "time_out_2" }, 
                        //{ "data": "time_in_3" },
                        //{ "data": "time_out_3" },
                        {"data": "in_out_1"},
                        {"data": "in_out_2"},
                        {"data": "in_out_3"},
                        { "data": "total_hours" },
                        { "data": "work_hours" },                                                        
                        { "data": "total_overtime" },
                        { "data": "tardiness" },
                        { "data": "undertime" },                
                        //{ "data": "overtime_status" }                    
                        {
                            "className":      'ot-status-btn',
                            "orderable":      false,
                            //"data":           null,
                            "data": "overtime_status" 
                            //"defaultContent": '<button class="ot-apply-btn btn btn-success" type="button">Apply OT</button>' //Aprroved or Denied                        
                        }                     
                    ]
                });*/

            //Summary Computation Init
            $.ajax({
                type: "GET",
                url : "{{ route('redrawSummary') }}", //http://localhost:8000/employee",
                data : '',
                success : function(data) {
                  
                  var obj = JSON.parse(data);

                  var lates = '', undertime = '', absences = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
                  var regular = '', regularOt = '', restDay = '', restDayOt = '';
                  var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                  var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                  //With Night Diff

                  var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
                  var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                  var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                  //SUMMARY: 1st column
                  lates = obj.data[0].tardiness;
                  undertime = obj.data[0].undertime;
                  absences = obj.data[0].absences; 

                  paidVacationLeave = obj.data[0].paid_vacation_leave; 
                  paidSickLeave = obj.data[0].paid_sick_leave;
                  leaveWithoutPay = obj.data[0].leave_without_pay;
                  maternityLeave = obj.data[0].maternity_leave;
                  paternityLeave = obj.data[0].paternity_leave;  

                  //SUMMARY: 2nd Column
                  regular = obj.data[0].regular;                  
                  regularOt = obj.data[0].regular_overtime;                  
                  restDay = obj.data[0].rest_day;
                  restDayOt = obj.data[0].rest_day_overtime;

                  //With Night Diff
                  regularOtNd = obj.data[0].regular_overtime_night_diff;
                  regularNd = obj.data[0].regular_night_differential;
                  restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                  restDayNd = obj.data[0].rest_day_night_diff;


                  //SUMMARY: 3rd Column
                  restDaySpecialHoliday = obj.data[0].rest_day_special_holiday;
                  restDaySpecialHolidayOt = obj.data[0].rest_day_special_holiday_overtime;
                  restDayLegalHoliday = obj.data[0].rest_day_legal_holiday;
                  restDayLegalHolidayOt = obj.data[0].rest_day_legal_holiday_overtime;

                  //With Night Diff
                  restDaySpecialHolidayOtNd = obj.data[0].rest_day_special_holiday_overtime_night_diff;
                  restDaySpecialHolidayNd = obj.data[0].rest_day_special_holiday_night_diff;
                  restDayLegalHolidayOtNd = obj.data[0].rest_day_legal_holiday_overtime_night_diff;
                  restDayLegalHolidayNd = obj.data[0].rest_day_legal_holiday_night_diff;


                  //SUMMARY: 4th Column
                  specialHoliday = obj.data[0].special_holiday;
                  specialHolidayOt = obj.data[0].special_holiday_overtime;
                  legalHoliday = obj.data[0].legal_holiday;
                  legalHolidayOt = obj.data[0].legal_holiday_overtime;

                  //With Night Diff
                  specialHolidayOtNd = obj.data[0].special_holiday_overtime_night_diff;
                  specialHolidayNd = obj.data[0].special_holiday_night_diff;
                  legalHolidayOtNd = obj.data[0].legal_holiday_overtime_night_diff;
                  legalHolidayNd = obj.data[0].legal_holiday_night_diff;

                  //SUMMARY: 1st Column
                  if (lates !== '0') {

                    $('#lates-ut').text(lates);

                  } 

                  if (undertime !== '0') {

                    $('#lates-ut').text(undertime);

                  }    

                  if (lates !== '0' && undertime !== '0') {

                    $('#lates-ut').text(lates + ' / ' + undertime);

                  }

                  if (absences !== '0') {

                    $('#absences').text(absences);

                  }

                  if (paidVacationLeave !== '0') {

                    $('#paid-sl').text(paidVacationLeave);

                  }

                  if (paidSickLeave !== '0') {

                    $('#paid-vl').text(paidSickLeave);

                  }

                  if (leaveWithoutPay !== '0') {

                    $('#leave-without-pay').text(leaveWithoutPay);

                  }

                  if (maternityLeave !== '0') {

                    $('#maternity-leave').text(maternityLeave);

                  }

                  if (paternityLeave !== '0') {

                    $('#paternity-leave').text(paternityLeave);

                  }                                                                        


                  //SUMMARY: 2nd Column
                  if (regular !== '0') {

                    $('#regular').text(regular);

                  }

                  if (regularOt !== '0') {

                    $('#reg-ot').text(regularOt);

                  }

                  if (restDay !== '0') {

                    $('#rd').text(restDay);

                  }                  

                  if (restDayOt !== '0') {

                    $('#rd-ot').text(restDayOt);

                  }

                  //With Night Diff
                  if (regularOtNd !== '0') {

                    $('#reg-ot-nd').text(regularOtNd);

                  }

                  if (regularNd !== '0') {

                    $('#reg-nd').text(regularNd);

                  }                                    

                  if (restDayOtNd !== '0') {

                    $('#rd-ot-nd').text(restDayOtNd);

                  }                  

                  if (restDayNd !== '0') {

                    $('#rd-nd').text(restDayNd);

                  }


                  //SUMMARY: 3rd Column 
                  if (restDaySpecialHoliday !== '0') {

                    $('#rd-spl-holiday').text(restDaySpecialHoliday);

                  }

                  if (restDaySpecialHolidayOt !== '0') {

                    $('#rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

                  }

                  if (restDayLegalHoliday !== '0') {

                    $('#rd-legal-holiday').text(restDayLegalHoliday);

                  }

                  if (restDayLegalHolidayOt !== '0') {

                    $('#rd-legal-holiday-ot').text(restDayLegalHolidayOt);

                  }                  

                  //With Night Diff 
                  if (restDaySpecialHolidayOtNd !== '0') {

                    $('#rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

                  }

                  if (restDaySpecialHolidayNd !== '0') {

                    $('#rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

                  }

                  if (restDayLegalHolidayOtNd !== '0') {

                    $('#rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

                  }

                  if (restDayLegalHolidayNd !== '0') {

                    $('#rd-legal-holiday-nd').text(restDayLegalHolidayNd);

                  }                  
                                    


                  //SUMMARY: 4th Column
                  
                  if (specialHoliday !== '0') {

                    $('#spl-holiday').text(specialHoliday);

                  }                                                                     

                  if (specialHolidayOt !== '0') {

                    $('#spl-holiday-ot').text(specialHolidayOt);

                  } 

                  if (legalHoliday !== '0') {

                    $('#legal-holiday').text(legalHoliday);

                  } 

                  if (legalHolidayOt !== '0') {

                    $('#legal-holiday-ot').text(legalHolidayOt);

                  }    

                  //With Night Diff
                  if (specialHolidayOtNd !== '0') {

                    $('#spl-holiday-ot-nd').text(specialHolidayOtNd);

                  }                                                                     

                  if (specialHolidayNd !== '0') {

                    $('#spl-holiday-nd').text(specialHolidayNd);

                  } 

                  if (legalHolidayOtNd !== '0') {

                    $('#legal-holiday-ot-nd').text(legalHolidayOtNd);

                  } 

                  if (legalHolidayNd !== '0') {

                    $('#legal-hoiday-nd').text(legalHolidayNd);

                  }                                                                     

            
                }
            },"json"); 

            });
            

            //Time Out button
            $('#time-clocking-btn.time-out').click(function(e) {                   
                e.preventDefault();

                $("#wait").html("<img src='{{ URL::asset('assets/img/') }}/indicator.gif'>");               

                var employeeNumber = $('#employee-number').val();
                var dayDate = $('#day-date').val();
                var schedIn = $('#sched-in').val();
                var schedOut = $('#sched-out').val();
                var timeOut1 = $('#time-out-1').val();
                var timeOut2 = $('#time-out-2').val();
                var timeOut3 = $('#time-out-3').val();
                var timeNow = $('#time-now').val();

                var timeClocking = 'out';             
                var forgotYesterdayTimeOut = 0;


                var dataString = 'timeclocking=' + timeClocking + '&timenow=' + timeNow + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
                    dataString += '&timeout1=' + timeOut1 + '&timeout2=' + timeOut2 + '&timeout3=' + timeOut3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;                                              

                //http://stackoverflow.com/questions/2342371/jquery-loop-on-json-data-using-each

                $.ajax({
                    type: "POST",
                    url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
                    data : dataString,
                    success : function(data) {

                      console.log(data);  

                      $.ajax({
                          type: "GET",
                          url : "{{ route('redrawTimesheet') }}", //http://localhost:8000/employee",
                          data : '',
                          success : function(data) {
                            //console.log(data);
                            var obj = JSON.parse(data);

                            console.log(obj.data);

                            $.each(obj.data, function(i, item) {
                              
                              /*var tableOut = '';
                              tableOut = '<tr>'
                              tableOut = '<td class="timesheet-id-' + item.id + '">' + item.id + '</td>';                 
                              tableOut = '<td class="timesheet-daydate-' + item.id + '">' + item.daydate + '</td>';
                              tableOut = '<td class="timesheet-schedule-' + item.id + '">' + item.schedule + '</td>';
                              tableOut = '<td class="timesheet-inout1-' + item.id + '">' + item.in_out_1 + '</td>';                  
                              tableOut = '<td class="timesheet-inout2-' + item.id + '">' + item.in_out_2 + '</td>';
                              tableOut = '<td class="timesheet-inout3-' + item.id + '">' + item.in_out_3 + '</td>';
                              tableOut = '<td class="timesheet-totalhours-' + item.id + '">Total Hours</td>';
                              tableOut = '<td class="timesheet-workhours-' + item.id + '">Work Hours</td>';                 
                              tableOut = '<td class="timesheet-totalovertime-' + item.id + '">Total Overtime</td>';
                              tableOut = '<td class="timesheet-tardiness-' + item.id + '">Tardiness</td>';                
                              tableOut = '<td class="timesheet-undertime-' + item.id + '">Undertime</td>';
                              tableOut = '<td class="timesheet-otstatus-' + item.id + '">OT Status</td>';
                              tableOut = '</tr>';*/

                              $(".timesheet-id-"+item.id).text(item.id);
                              $(".timesheet-daydate-"+item.id).text(item.daydate);
                              $(".timesheet-schedule-"+item.id).text(item.schedule);
                              /*$(".timesheet-inout1-"+item.id).text(item.in_out_1);
                              $(".timesheet-inout2-"+item.id).text(item.in_out_2);
                              $(".timesheet-inout3-"+item.id).text(item.in_out_3);*/
                              $(".timesheet-in1-"+item.id).text(item.in_1);
                              $(".timesheet-out1-"+item.id).text(item.out_1);
                              $(".timesheet-in2-"+item.id).text(item.in_2);
                              $(".timesheet-out2-"+item.id).text(item.out_2);
                              $(".timesheet-in3-"+item.id).text(item.in_3);
                              $(".timesheet-out3-"+item.id).text(item.out_3);  
                              $(".timesheet-nightdifferential-"+item.id).text(item.night_differential);                                  
                              $(".timesheet-totalhours-"+item.id).text(item.total_hours);
                              $(".timesheet-workhours-"+item.id).text(item.work_hours);
                              $(".timesheet-totalovertime-"+item.id).text(item.total_overtime);
                              $(".timesheet-tardiness-"+item.id).text(item.tardiness);
                              $(".timesheet-undertime-"+item.id).text(item.undertime);
                              $(".timesheet-otstatus-"+item.id).html(item.overtime_status);                             

                            });

                            var timeClockingBtn = $('#time-clocking-btn').text();

                            if (timeClockingBtn.toLowerCase() === 'time in') {

                                $('#time-clocking-btn.time-in').removeClass('hide').show();                               
                                $('#time-clocking-btn.time-out').addClass('hide').hide();   
                                $("#wait").hide();           
                            }  
                               
                          }
                      },"json");
      
                    }
                },"json");












                   /*table.destroy(); 
                    //$('#timesheet').empty(); // empty in case the columns change                
                    //table.clear();
                    //table.draw();  

                    table = $('#timesheet').DataTable({                         
                        "processing": true,
                        "serverSide": true,
                        "createdRow": function( row, data, dataIndex ) {   //https://datatables.net/reference/option/rowCallback

                          getServerDateTime(function(result) {    
                            var result;
                            //console.log(data);                      
                            if(data.daydate === result) {

                              //console.log(data.daydate); 
                              //$(row).css( "background-color", '#9bd8eb' );
                              $(row).css( "color", 'black' );
                              //$('td:eq(0)', row).html( '<strong>' + data.id + '</strong>' );
                              $('td:eq(0)', row).html( '<strong>' + data.daydate + '</strong>' );                      
                              $('td:eq(1)', row).html( '<strong>' + data.schedule + '</strong>' );
                              $('td:eq(2)', row).html( '<strong>' + data.in_out_1 + '</strong>' );
                              $('td:eq(3)', row).html( '<strong>' + data.in_out_2 + '</strong>' );
                              $('td:eq(4)', row).html( '<strong>' + data.in_out_3 + '</strong>' );
                              $('td:eq(5)', row).html( '<strong>' + data.total_hours + '</strong>' );
                              $('td:eq(6)', row).html( '<strong>' + data.work_hours + '</strong>' );
                              $('td:eq(7)', row).html( '<strong>' + data.total_overtime + '</strong>' );
                              $('td:eq(8)', row).html( '<strong>' + data.tardiness + '</strong>' );
                              $('td:eq(9)', row).html( '<strong>' + data.undertime + '</strong>' );
                              //$('td:eq(10)', row).html( '<strong>' + data.daydate + '</strong>' );

                            }

                          });

                        },                                    
                        "ajax": "{{ route('redrawTimesheet') }}",
                        "columnDefs": [
                            {
                                "targets": [0],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        "columns": [    

                            { "data": "id" },
                            { "data": "daydate" },
                            //{ "data": "schedule_in" },
                            //{ "data": "schedule_out" },
                            { "data": "schedule" },
                            //{ "data": "time_in_1" },
                            //{ "data": "time_out_1" }, 
                            //{ "data": "time_in_2" },
                            //{ "data": "time_out_2" }, 
                            //{ "data": "time_in_3" },
                            //{ "data": "time_out_3" },
                            {"data": "in_out_1"},
                            {"data": "in_out_2"},
                            {"data": "in_out_3"},
                            { "data": "total_hours" },
                            { "data": "work_hours" },                                                        
                            { "data": "total_overtime" },
                            { "data": "tardiness" },
                            { "data": "undertime" },                
                            //{ "data": "overtime_status" }                    
                            {
                                "className":      'ot-status-btn',
                                "orderable":      false,
                                //"data":           null,
                                "data": "overtime_status" 
                                //"defaultContent": '<button class="ot-apply-btn btn btn-success" type="button">Apply OT</button>' //Aprroved or Denied                        
                            }                     
                        ]
                    });*/
               /* } else {                    
                    var result = confirm("Do you really want to timeout");
                    if (result == true) {
                        alert('Please report this to your "HR Deparment or Manager/Supervisor"');
                    } */                 
                    /*bootbox.confirm("Are you sure?", function(result) {
                        if(result == true) {
                            bootbox.alert('Please report this to your "HR Deparment or Superior"');    
                        }
                    });*/                    
                //}

              //Summary Computation Init
              $.ajax({
                  type: "GET",
                  url : "{{ route('redrawSummary') }}", //http://localhost:8000/employee",
                  data : '',
                  success : function(data) {
                    
                    var obj = JSON.parse(data);

                    var lates = '', undertime = '', absences = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
                    var regular = '', regularOt = '', restDay = '', restDayOt = '';
                    var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                    var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                    //With Night Diff

                    var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
                    var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                    var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                    //SUMMARY: 1st column
                    lates = obj.data[0].tardiness;
                    undertime = obj.data[0].undertime;
                    absences = obj.data[0].absences; 

                    paidVacationLeave = obj.data[0].paid_vacation_leave; 
                    paidSickLeave = obj.data[0].paid_sick_leave;
                    leaveWithoutPay = obj.data[0].leave_without_pay;
                    maternityLeave = obj.data[0].maternity_leave;
                    paternityLeave = obj.data[0].paternity_leave;  

                    //SUMMARY: 2nd Column
                    regular = obj.data[0].regular;                  
                    regularOt = obj.data[0].regular_overtime;                  
                    restDay = obj.data[0].rest_day;
                    restDayOt = obj.data[0].rest_day_overtime;

                    //With Night Diff
                    regularOtNd = obj.data[0].regular_overtime_night_diff;
                    regularNd = obj.data[0].regular_night_differential;
                    restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                    restDayNd = obj.data[0].rest_day_night_diff;


                    //SUMMARY: 3rd Column
                    restDaySpecialHoliday = obj.data[0].rest_day_special_holiday;
                    restDaySpecialHolidayOt = obj.data[0].rest_day_special_holiday_overtime;
                    restDayLegalHoliday = obj.data[0].rest_day_legal_holiday;
                    restDayLegalHolidayOt = obj.data[0].rest_day_legal_holiday_overtime;

                    //With Night Diff
                    restDaySpecialHolidayOtNd = obj.data[0].rest_day_special_holiday_overtime_night_diff;
                    restDaySpecialHolidayNd = obj.data[0].rest_day_special_holiday_night_diff;
                    restDayLegalHolidayOtNd = obj.data[0].rest_day_legal_holiday_overtime_night_diff;
                    restDayLegalHolidayNd = obj.data[0].rest_day_legal_holiday_night_diff;


                    //SUMMARY: 4th Column
                    specialHoliday = obj.data[0].special_holiday;
                    specialHolidayOt = obj.data[0].special_holiday_overtime;
                    legalHoliday = obj.data[0].legal_holiday;
                    legalHolidayOt = obj.data[0].legal_holiday_overtime;

                    //With Night Diff
                    specialHolidayOtNd = obj.data[0].special_holiday_overtime_night_diff;
                    specialHolidayNd = obj.data[0].special_holiday_night_diff;
                    legalHolidayOtNd = obj.data[0].legal_holiday_overtime_night_diff;
                    legalHolidayNd = obj.data[0].legal_holiday_night_diff;

                    //SUMMARY: 1st Column
                    if (lates !== '0') {

                      $('#lates-ut').text(lates);

                    } 

                    if (undertime !== '0') {

                      $('#lates-ut').text(undertime);

                    }    

                    if (lates !== '0' && undertime !== '0') {

                      $('#lates-ut').text(lates + ' / ' + undertime);

                    }

                    if (absences !== '0') {

                      $('#absences').text(absences);

                    }

                    if (paidVacationLeave !== '0') {

                      $('#paid-sl').text(paidVacationLeave);

                    }

                    if (paidSickLeave !== '0') {

                      $('#paid-vl').text(paidSickLeave);

                    }

                    if (leaveWithoutPay !== '0') {

                      $('#leave-without-pay').text(leaveWithoutPay);

                    }

                    if (maternityLeave !== '0') {

                      $('#maternity-leave').text(maternityLeave);

                    }

                    if (paternityLeave !== '0') {

                      $('#paternity-leave').text(paternityLeave);

                    }                                                                        


                    //SUMMARY: 2nd Column
                    if (regular !== '0') {

                      $('#regular').text(regular);

                    }

                    if (regularOt !== '0') {

                      $('#reg-ot').text(regularOt);

                    }

                    if (restDay !== '0') {

                      $('#rd').text(restDay);

                    }                  

                    if (restDayOt !== '0') {

                      $('#rd-ot').text(restDayOt);

                    }

                    //With Night Diff
                    if (regularOtNd !== '0') {

                      $('#reg-ot-nd').text(regularOtNd);

                    }

                    if (regularNd !== '0') {

                      $('#reg-nd').text(regularNd);

                    }                                    

                    if (restDayOtNd !== '0') {

                      $('#rd-ot-nd').text(restDayOtNd);

                    }                  

                    if (restDayNd !== '0') {

                      $('#rd-nd').text(restDayNd);

                    }


                    //SUMMARY: 3rd Column 
                    if (restDaySpecialHoliday !== '0') {

                      $('#rd-spl-holiday').text(restDaySpecialHoliday);

                    }

                    if (restDaySpecialHolidayOt !== '0') {

                      $('#rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

                    }

                    if (restDayLegalHoliday !== '0') {

                      $('#rd-legal-holiday').text(restDayLegalHoliday);

                    }

                    if (restDayLegalHolidayOt !== '0') {

                      $('#rd-legal-holiday-ot').text(restDayLegalHolidayOt);

                    }                  

                    //With Night Diff 
                    if (restDaySpecialHolidayOtNd !== '0') {

                      $('#rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

                    }

                    if (restDaySpecialHolidayNd !== '0') {

                      $('#rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

                    }

                    if (restDayLegalHolidayOtNd !== '0') {

                      $('#rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

                    }

                    if (restDayLegalHolidayNd !== '0') {

                      $('#rd-legal-holiday-nd').text(restDayLegalHolidayNd);

                    }                  
                                      


                    //SUMMARY: 4th Column
                    
                    if (specialHoliday !== '0') {

                      $('#spl-holiday').text(specialHoliday);

                    }                                                                     

                    if (specialHolidayOt !== '0') {

                      $('#spl-holiday-ot').text(specialHolidayOt);

                    } 

                    if (legalHoliday !== '0') {

                      $('#legal-holiday').text(legalHoliday);

                    } 

                    if (legalHolidayOt !== '0') {

                      $('#legal-holiday-ot').text(legalHolidayOt);

                    }    

                    //With Night Diff
                    if (specialHolidayOtNd !== '0') {

                      $('#spl-holiday-ot-nd').text(specialHolidayOtNd);

                    }                                                                     

                    if (specialHolidayNd !== '0') {

                      $('#spl-holiday-nd').text(specialHolidayNd);

                    } 

                    if (legalHolidayOtNd !== '0') {

                      $('#legal-holiday-ot-nd').text(legalHolidayOtNd);

                    } 

                    if (legalHolidayNd !== '0') {

                      $('#legal-hoiday-nd').text(legalHolidayNd);

                    }                                                                     

              
                  }
              },"json"); 

            });  










           


            $('#search-timesheet-btn2').click(function(e) {                                      
                
                e.preventDefault();

                $('#timesheet-link').removeClass('hide hidden');
              
                var employeeNumber = $('#employee-number').val();
                var dayDate = $('#day-date').val();
                var schedIn = $('#sched-in').val();
                var schedOut = $('#sched-out').val();
                var timeIn1 = $('#time-in-1').val();
                var timeIn2 = $('#time-in-2').val();
                var timeIn3 = $('#time-in-3').val();
                var forgotYesterdayTimeOut = $('#time-in-1').val();

                var employeeId = $('#employee-id').val();
                var search = 'search';

                var dataString = 'employeeId=' + employeeId + '&search=' + search;

                //Search Timesheet Result
                $.ajax({
                    type: "POST",
                    url : "{{ route('searchTimesheet') }}", //http://localhost:8000/employee",
                    data : dataString,
                    success : function(data) {
                      //console.log(data);  

                        //var timeClockingBtn = $('#time-clocking-btn').text();

                        //console.log(timeClockingBtn.toLowerCase());                

                        /*if (timeClockingBtn.toLowerCase() === 'time in') {

                            $('#time-clocking-btn.time-out').removeClass('hide').show(); 
                            $('#time-clocking-btn.time-in').addClass('hide').hide();                

                        }*/                        
                    }
                },"json");             


                             
                //https://datatables.net/reference/option/drawCallback
                //https://www.datatables.net/forums/discussion/8365/jeditable-datatables-how-can-i-refresh-table-after-edit
                table.destroy();

                //$('#timesheet').empty(); // empty in case the columns change                
                //table.clear();
                //table.draw(); 

                table = $('#timesheet').dataTable({                         
                    "processing": true,
                    "serverSide": true,
                    "fnDrawCallback": function () {
                    //"drawCallback": function( settings ) {      
                      $('#timesheet tbody td.in-out-time').editable( "{{ route('adminTimesheetSave') }}", {
                          "callback": function( sValue, y ) {

                           /*console.log(this);
                           console.log(sValue);
                           console.log(y);*/

                           var aPos = table.fnGetPosition( this );                       
                           table.fnUpdate( sValue, aPos[0], aPos[1] );

                            // Redraw the table from the new data on the server
                            table.fnDraw();
                            //table.draw();


                        //Summary Computation Init
                        $.ajax({
                            type: "GET",
                            url : "{{ route('redrawSummary') }}", //http://localhost:8000/employee",
                            data : '',
                            success : function(data) {
                              
                              var obj = JSON.parse(data);

                              var lates = '', undertime = '', absences = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
                              var regular = '', regularOt = '', restDay = '', restDayOt = '';
                              var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                              var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                              //With Night Diff

                              var regularOtNd = '', regularNd = '', restDayOtNd = '', restDayNd = '';
                              var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                              var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                              //SUMMARY: 1st column
                              lates = obj.data[0].tardiness;
                              undertime = obj.data[0].undertime;
                              absences = obj.data[0].absences; 

                              paidVacationLeave = obj.data[0].paid_vacation_leave; 
                              paidSickLeave = obj.data[0].paid_sick_leave;
                              leaveWithoutPay = obj.data[0].leave_without_pay;
                              maternityLeave = obj.data[0].maternity_leave;
                              paternityLeave = obj.data[0].paternity_leave;  

                              //SUMMARY: 2nd Column
                              regular = obj.data[0].regular;                  
                              regularOt = obj.data[0].regular_overtime;                  
                              restDay = obj.data[0].rest_day;
                              restDayOt = obj.data[0].rest_day_overtime;

                              //With Night Diff
                              regularOtNd = obj.data[0].regular_overtime_night_diff;
                              regularNd = obj.data[0].regular_night_differential;
                              restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                              restDayNd = obj.data[0].rest_day_night_diff;


                              //SUMMARY: 3rd Column
                              restDaySpecialHoliday = obj.data[0].rest_day_special_holiday;
                              restDaySpecialHolidayOt = obj.data[0].rest_day_special_holiday_overtime;
                              restDayLegalHoliday = obj.data[0].rest_day_legal_holiday;
                              restDayLegalHolidayOt = obj.data[0].rest_day_legal_holiday_overtime;

                              //With Night Diff
                              restDaySpecialHolidayOtNd = obj.data[0].rest_day_special_holiday_overtime_night_diff;
                              restDaySpecialHolidayNd = obj.data[0].rest_day_special_holiday_night_diff;
                              restDayLegalHolidayOtNd = obj.data[0].rest_day_legal_holiday_overtime_night_diff;
                              restDayLegalHolidayNd = obj.data[0].rest_day_legal_holiday_night_diff;


                              //SUMMARY: 4th Column
                              specialHoliday = obj.data[0].special_holiday;
                              specialHolidayOt = obj.data[0].special_holiday_overtime;
                              legalHoliday = obj.data[0].legal_holiday;
                              legalHolidayOt = obj.data[0].legal_holiday_overtime;

                              //With Night Diff
                              specialHolidayOtNd = obj.data[0].special_holiday_overtime_night_diff;
                              specialHolidayNd = obj.data[0].special_holiday_night_diff;
                              legalHolidayOtNd = obj.data[0].legal_holiday_overtime_night_diff;
                              legalHolidayNd = obj.data[0].legal_holiday_night_diff;

                              //SUMMARY: 1st Column
                              if (lates !== '0') {

                                $('#lates-ut').text(lates);

                              } 

                              if (undertime !== '0') {

                                $('#lates-ut').text(undertime);

                              }    

                              if (lates !== '0' && undertime !== '0') {

                                $('#lates-ut').text(lates + ' / ' + undertime);

                              }

                              if (absences !== '0') {

                                $('#absences').text(absences);

                              }

                              if (paidVacationLeave !== '0') {

                                $('#paid-sl').text(paidVacationLeave);

                              }

                              if (paidSickLeave !== '0') {

                                $('#paid-vl').text(paidSickLeave);

                              }

                              if (leaveWithoutPay !== '0') {

                                $('#leave-without-pay').text(leaveWithoutPay);

                              }

                              if (maternityLeave !== '0') {

                                $('#maternity-leave').text(maternityLeave);

                              }

                              if (paternityLeave !== '0') {

                                $('#paternity-leave').text(paternityLeave);

                              }                                                                        


                              //SUMMARY: 2nd Column
                              if (regular !== '0') {

                                $('#regular').text(regular);

                              }

                              if (regularOt !== '0') {

                                $('#reg-ot').text(regularOt);

                              }

                              if (restDay !== '0') {

                                $('#rd').text(restDay);

                              }                  

                              if (restDayOt !== '0') {

                                $('#rd-ot').text(restDayOt);

                              }

                              //With Night Diff
                              if (regularOtNd !== '0') {

                                $('#reg-ot-nd').text(regularOtNd);

                              }

                              if (regularNd !== '0') {

                                $('#reg-nd').text(regularNd);

                              }                                    

                              if (restDayOtNd !== '0') {

                                $('#rd-ot-nd').text(restDayOtNd);

                              }                  

                              if (restDayNd !== '0') {

                                $('#rd-nd').text(restDayNd);

                              }


                              //SUMMARY: 3rd Column 
                              if (restDaySpecialHoliday !== '0') {

                                $('#rd-spl-holiday').text(restDaySpecialHoliday);

                              }

                              if (restDaySpecialHolidayOt !== '0') {

                                $('#rd-spl-holiday-ot').text(restDaySpecialHolidayOt);

                              }

                              if (restDayLegalHoliday !== '0') {

                                $('#rd-legal-holiday').text(restDayLegalHoliday);

                              }

                              if (restDayLegalHolidayOt !== '0') {

                                $('#rd-legal-holiday-ot').text(restDayLegalHolidayOt);

                              }                  

                              //With Night Diff 
                              if (restDaySpecialHolidayOtNd !== '0') {

                                $('#rd-spl-holiday-ot-nd').text(restDaySpecialHolidayOtNd);

                              }

                              if (restDaySpecialHolidayNd !== '0') {

                                $('#rd-spl-holiday-nd').text(restDaySpecialHolidayNd);

                              }

                              if (restDayLegalHolidayOtNd !== '0') {

                                $('#rd-legal-holiday-ot-nd').text(restDayLegalHolidayOtNd);

                              }

                              if (restDayLegalHolidayNd !== '0') {

                                $('#rd-legal-holiday-nd').text(restDayLegalHolidayNd);

                              }                  
                                                


                              //SUMMARY: 4th Column
                              
                              if (specialHoliday !== '0') {

                                $('#spl-holiday').text(specialHoliday);

                              }                                                                     

                              if (specialHolidayOt !== '0') {

                                $('#spl-holiday-ot').text(specialHolidayOt);

                              } 

                              if (legalHoliday !== '0') {

                                $('#legal-holiday').text(legalHoliday);

                              } 

                              if (legalHolidayOt !== '0') {

                                $('#legal-holiday-ot').text(legalHolidayOt);

                              }    

                              //With Night Diff
                              if (specialHolidayOtNd !== '0') {

                                $('#spl-holiday-ot-nd').text(specialHolidayOtNd);

                              }                                                                     

                              if (specialHolidayNd !== '0') {

                                $('#spl-holiday-nd').text(specialHolidayNd);

                              } 

                              if (legalHolidayOtNd !== '0') {

                                $('#legal-holiday-ot-nd').text(legalHolidayOtNd);

                              } 

                              if (legalHolidayNd !== '0') {

                                $('#legal-hoiday-nd').text(legalHolidayNd);

                              }                                                                     

                        
                            }
                        },"json");


                          },
                          "submitdata": function ( value, settings ) {
                           //console.log(this.parentNode.getAttribute('id'));
                           //console.log(table.fnGetPosition( this )[2]);
                            return {
                              "row_id": this.parentNode.getAttribute('id'),
                              "column": table.fnGetPosition( this )[2]
                            };

                         },          
                         //"onblur" : "submit",
                        //http://stackoverflow.com/questions/1458128/jeditable-textbox-width
                         "width": ($('#timesheet tbody td input').width()) + 200, //"100%"
                         "height":"25px"           
                      });

                    },                    
                    "createdRow": function( row, data, dataIndex ) {   //https://datatables.net/reference/option/rowCallback

                      getServerDateTime(function(result) {    
                        var result;
                        //console.log(data);                      
                        if(data.daydate === result) {

                          //console.log(data.daydate); 
                          //$(row).css( "background-color", '#9bd8eb' );
                          //$(row).css( "color", 'black' );
                          $(row).css( { "color":"black", "font-weight":"bold" } ); 
                          //$('td:eq(0)', row).html( '<strong>' + data.id + '</strong>' );
                          /*$('td:eq(0)', row).html( '<strong>' + data.daydate + '</strong>' );                      
                          $('td:eq(1)', row).html( '<strong>' + data.schedule + '</strong>' );
                          $('td:eq(2)', row).html( '<strong>' + data.in_out_1 + '</strong>' );
                          $('td:eq(3)', row).html( '<strong>' + data.in_out_2 + '</strong>' );
                          $('td:eq(4)', row).html( '<strong>' + data.in_out_3 + '</strong>' );
                          $('td:eq(5)', row).html( '<strong>' + data.total_hours + '</strong>' );
                          $('td:eq(6)', row).html( '<strong>' + data.work_hours + '</strong>' );
                          $('td:eq(7)', row).html( '<strong>' + data.total_overtime + '</strong>' );
                          $('td:eq(8)', row).html( '<strong>' + data.tardiness + '</strong>' );
                          $('td:eq(9)', row).html( '<strong>' + data.undertime + '</strong>' );*/
                          //$('td:eq(10)', row).html( '<strong>' + data.daydate + '</strong>' );

                        }

                      });

                    },                                    
                    "ajax": "{{ route('redrawAdminSearchTimesheet') }}",
                    "columnDefs": [
                        {
                            "targets": [0],
                            "visible": false,
                            "searchable": false
                        }
                    ],
                    "columns": [    

                        { "data": "id" },
                        { "data": "daydate" },
                        //{ "data": "schedule_in" },
                        //{ "data": "schedule_out" },
                        { "data": "schedule" },
                        /*{ "data": "time_in_1" },
                        { "data": "time_out_1" }, 
                        { "data": "time_in_2" },
                        { "data": "time_out_2" }, 
                        { "data": "time_in_3" },
                        { "data": "time_out_3" },*/
                        {"data": "in_out_1", "class": "in-out-time"},
                        {"data": "in_out_2", "class": "in-out-time"},
                        {"data": "in_out_3", "class": "in-out-time"},
                        { "data": "total_hours" },
                        { "data": "work_hours" },                                                        
                        { "data": "total_overtime" },
                        { "data": "tardiness" },
                        { "data": "undertime" },                
                        //{ "data": "overtime_status" }                    
                        {
                            "className":      'ot-status-btn',
                            "orderable":      false,
                            //"data":           null,
                            "data": "overtime_status" 
                            //"defaultContent": '<button class="ot-apply-btn btn btn-success" type="button">Apply OT</button>' //Aprroved or Denied                        
                        }                     
                    ]
                });






                //Sidebar Employee Information
                $.ajax({
                    type: "GET",
                    url : "{{ route('redrawAdminEmployeeInfo') }}", //http://localhost:8000/employee",
                    data : '',
                    success : function(data) {
                      
                      var obj = JSON.parse(data);
                      //console.log(obj.data[0].fullname);

                      var fullname = obj.data[0].fullname;
                      var employeeNumber = obj.data[0].employeenumber;
                      var designation = obj.data[0].designation;
                      var team = obj.data[0].team;
                      var head = obj.data[0].head;
                      
                      if (fullname !== '') {

                        $('#fullname').text(fullname);

                      } 

                      if (employeeNumber !== '') {

                        $('#employee-number>strong').text(employeeNumber);

                      } 

                      if (designation !== '') {

                        $('#designation>strong').text(designation);

                      } 

                      if (team !== '') {

                        $('#team>strong').text(team);

                      } 

                      if (head !== '') {

                        $('#head>strong').text(head);

                      } 

                      $('#timeClockingForm #time-clocking-btn, #leave-link, #schedule-link').attr('disabled', 'disabled');
                      
                    }
                },"json");   


            });

      });


    /**
     * Callback for bootboxjs
     * This tiny script just helps us demonstrate
     * what the various example callbacks are doing
     */
    /*var BackOfficeCallback = (function() {
        "use strict";

        var elem,
            hideHandler,
            that = {};

        that.init = function(options) {
            elem = $(options.selector);
        };

        that.show = function(text) {
            clearTimeout(hideHandler);

            elem.find("span").html(text);
            elem.delay(200).fadeIn().delay(4000).fadeOut();
        };

        return that;
    }());*/


  

    </script>
  </body>
</html>