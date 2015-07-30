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
            <li id="timesheet-link"><a href="{{ url('/employee/clocking') }}"><i class="fa fa-clock-o"></i> My Timesheet</a></li>

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
      
        ////console.log("Data: " + data + "\nStatus: " + status);
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

        $.ajax({
            type: "GET",
            url : "{{ route('redrawAdminSearchTimesheet') }}", //http://localhost:8000/employee",
            data : '',
            success : function(data) {
              ////console.log(data);
              var obj = JSON.parse(data);
              ////console.log(obj);

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


                //Summary Computation Init
                $.ajax({
                    type: "GET",
                    url : "{{ route('redrawSearchEmployeeSummary') }}", //http://localhost:8000/employee",
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

                

              //how to get the class id of a table row in jquery when clicking a cell
              //http://stackoverflow.com/questions/26646597/get-column-and-row-name-when-click-on-cell-in-datatables
              //https://datatables.net/reference/api/row().index()
              //http://jsfiddle.net/UW38e/
              //http://stackoverflow.com/questions/9126264/getting-attribute-of-a-parent-node
              //http://www.dreamincode.net/forums/topic/266941-jeditable-pass-row-id-to-php-script/

                $("#timesheet-ajax tbody>tr>td.edit-cell").editable("{{ route('adminTimesheetSave') }}", { 
                  //loadurl : "{{ route('loadTimesheet') }}",
                  indicator : "<img src= {{ URL::asset('assets/img/indicator.gif') }}>",
                  //indicator : "Saving...",
                  tooltip   : "Click to edit...",
                  placeholder : '',
                  style  : "inherit",
                 // id     : $(this).parent().attr('id'), //item.id,

                  callback : function(value, settings) {

                    $.ajax({
                        type: "GET",
                        url : "{{ route('redrawAdminSearchTimesheet') }}", //http://localhost:8000/employee",
                        data : '',
                        success : function(data) {
                        //console.log(data);
                        var obj = JSON.parse(data);

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

                    //Summary Computation Init
                    $.ajax({
                        type: "GET",
                        url : "{{ route('redrawSearchEmployeeSummary') }}", //http://localhost:8000/employee",
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
                  submitdata : function ( value, settings ) {
                    settings.id = $(this).parent().attr('id');
                    //console.log($(this).index());
                    ////console.log(settings.id);
                   //console.log(value);
                    return {
                      "row_id":  settings.id,  //settings.id,
                      "column": $(this).index() //3
                    };

                 }                        
                });

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

            }
        },"json");        

      });

    </script>
  </body>
</html>