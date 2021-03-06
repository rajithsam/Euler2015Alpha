<?php  
$employeeId = Session::get('userEmployeeId');
$userId = Session::get('userId');

$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

if( !empty($userGroups) ) {

  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

}

$currentUser = Sentry::getUser();
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
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ URL::asset('assets/css/jquery-ui.css') }}">

    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>    

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ URL::asset('assets/css/font-awesome.min.css') }}">

    <!-- Custom styles for this template -->
    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">   

    <link href="{{ URL::asset('assets/css/metisMenu.min.css') }}" rel="stylesheet">    
    <link href="{{ URL::asset('assets/css/metisMenu-default-theme.css') }}" rel="stylesheet">

    <link href="{{ URL::asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="{{ URL::asset('assets/css/select2.css') }}">   
    
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
        <div id="navbar" class="collapse navbar-collapse pull-right">
          <ul class="nav navbar-nav">  
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

              <li class="active"><a href="{{ url('/employee/clocking') }}"><i class="fa fa-clock-o"></i> Timesheet</a></li>
              <li><a href="{{ URL::to('users/logout') }}">Log Out <i class="fa fa-sign-out"></i></a></li>            

          <?php
            endif;

          endif;
          ?>

          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">    
      @yield('content')
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>    
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
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

    <script src="{{ URL::asset('assets/js/select2.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/admin.js') }}"></script>     

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


    // check all checkboxes
      $('tbody').children().children('.check-column').find(':checkbox').click( function(e) {
        if ( 'undefined' == e.shiftKey ) { return true; }
        if ( e.shiftKey ) {
          if ( !lastClicked ) { return true; }
          checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' );
          first = checks.index( lastClicked );
          last = checks.index( this );
          checked = $(this).prop('checked');
          if ( 0 < first && 0 < last && first != last ) {
            sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
            sliced.prop( 'checked', function() {
              if ( $(this).closest('tr').is(':visible') )
                return checked;

              return false;
            });
          }
        }
        lastClicked = this;

        // toggle "check all" checkboxes
        var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible').not(':checked');
        $(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
          return ( 0 === unchecked.length );
        });

        return true;
      });

      $('thead, tfoot').find('.check-column :checkbox').on( 'click.wp-toggle-checkboxes', function( event ) {
        var $this = $(this),
          $table = $this.closest( 'table' ),
          controlChecked = $this.prop('checked'),
          toggle = event.shiftKey || $this.data('wp-toggle');

        $table.children( 'tbody' ).filter(':visible')
          .children().children('.check-column').find(':checkbox')
          .prop('checked', function() {
            if ( $(this).is(':hidden') ) {
              return false;
            }

            if ( toggle ) {
              return ! $(this).prop( 'checked' );
            } else if ( controlChecked ) {
              return true;
            }

            return false;
          });

        $table.children('thead,  tfoot').filter(':visible')
          .children().children('.check-column').find(':checkbox')
          .prop('checked', function() {
            if ( toggle ) {
              return false;
            } else if ( controlChecked ) {
              return true;
            }

            return false;
          });
      });


      //#Jquery UI
      $(function() {
      
        $( ".datepicker" ).datepicker({
          changeMonth: true,
          changeYear: true,
          dateFormat: 'yy-mm-dd'
        });

      });

     $(document).ready(function(){

        var designationId = $('#designation').val();

        if ( designationId === '1' ) { //Manager

          $('.department-head-form-group').hide();
          $('.supervisor-form-group').hide();

        } else if ( designationId === '2' ) { //Supervisor

          $('.department-head-form-group').show();
          $('.supervisor-form-group').show();

        } else if ( designationId === '3' || designationId === '0' || designationId === '' ) { //Employee or empty string

          $('.department-head-form-group').show();
          $('.supervisor-form-group').show();

        } else {

          $('.department-head-form-group').show();
          $('.supervisor-form-group').show();

        }

        $('#designation').change(function(){
          
          var designationId = $('#designation').val();

          //console.log(designationId);

          if ( designationId === '1' ) { //Manager

            $('.department-head-form-group').hide();
            $('.supervisor-form-group').hide();

          } else if ( designationId === '2' ) { //Supervisor

            $('.department-head-form-group').show();
            $('.supervisor-form-group').show();

          } else if ( designationId === '3' || designationId === '0' || designationId === '' ) { //Employee or empty string

            $('.department-head-form-group').show();
            $('.supervisor-form-group').show();

          } else {

            $('.department-head-form-group').show();
            $('.supervisor-form-group').show();

          }  


        });

      });


      //http://coffeecupweb.com/dynamic-dependent-select-box-with-jquery-and-ajax/
      //Dynamic Dependent Select Box with jQuery and Ajax
     /* $(document).ready(function(){
        $('#department-head').change(function(){
          
          var departmentHeadId = $('#department-head').val();

          //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;
          if(departmentHeadId != 0)
          {
            $.ajax({
              type:"POST",
              url:"route('getSupervisors')",//'getvalue.php',
              data:{id:departmentHeadId},
              cache:false,
              success: function(data){
                //$('#supervisor').html(data);
               //$('#supervisor').html('<option value="21">Justino, Arciga</option>');
                console.log(data);
              }
            });
          }
        })
      }) */     


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

            //$('.editable').editable('http://www.example.com/save.php');

            //http://legacy.datatables.net/release-datatables/examples/server_side/editable.html
            //http://www.sprymedia.co.uk/dataTables-1.4/example_editable.html            
            //http://datatables.net/api#fnGetPosition
            //http://datatables.net/api#fnGetData
            /* Data tables */
            //Setting defaults - http://www.datatables.net/manual/options
            $.extend( $.fn.dataTable.defaults, {
              scrollX: false,
              scrollY: false, //250 
              searching: false,
              ordering: false,
              paging: false,
              info: false,
              deferRender: true
              
            } );

            //https://datatables.net/reference/api/destroy%28%29
            //https://www.datatables.net/forums/discussion/23649/how-do-i-access-columns-data-inside-of-defaultcontent
            //https://datatables.net/reference/option/columns.how-do-i-access-columns-data-inside-of-defaultcontent
            var table = $('#timesheet').DataTable( {
                //"scrollY": 350,
                //"processing": true,
                //"serverSide": true, 
                //"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {},
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
                    /*{ "data": "time_in_1" },
                    { "data": "time_out_1" }, 
                    { "data": "time_in_2" },
                    { "data": "time_out_2" }, 
                    { "data": "time_in_3" },
                    { "data": "time_out_3" },*/
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

            } ); 



            //http://www.datatables.net/examples/api/row_details.html 
            //https://datatables.net/reference/api/row%28%29.index%28%29

            //td.ot-status-btn .ot-apply-btn
            $('#timesheet tbody').on('click', 'td.ot-status-btn .ot-apply-btn', function () {

                    var tr = $(this).closest('tr');
                    var row = table.row( tr );
                    var rowIdx = row.index(); // start at 0
                    var rowObj = row.data();    
                    var TimesheetId = rowObj.id;
                    var dataType;
                    //var dataString;

                    var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;

                    $.ajax({                    
                      type: "POST",
                      url : "{{ route('redrawOvertimeStatus') }}",
                      data : dataString,
                      success : function(data) {
                        
                        var overtimeStatusText = $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').text();

                        if ('Apply OT' === overtimeStatusText) {

                          console.log(data);

                          //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;                         
                          $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').remove();
                          $('td.ot-status-btn:eq(' + rowIdx + ')').html('<span class=\"label label-success\" style=\"padding: 2px 4px; font-size: 11px;\">Pending</span>');
                            
                        }

                        /*if ('Pending' === overtimeStatusText) {

                          console.log(data);

                          //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;                         
                          $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').remove();
                          $('td.ot-status-btn:eq(' + rowIdx + ')').html('<span class=\"label label-success\" style=\"padding: 2px 4px; font-size: 11px;\">Pending</span>');
                            
                        }*/


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

                  var lates = '', undertime = '', absence = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';
                  var regularOt = '', restDay = '', restDayOt = '';
                  var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                  var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                  //With Night Diff

                  var regularOtNd = '', restDayNd = '', restDayOtNd = '', restDayNd = '';
                  var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                  var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                  //SUMMARY: 1st column
                  lates = obj.data[0].tardiness;
                  undertime = obj.data[0].undertime;
                  absence = obj.data[0].absence; 

                  paidVacationLeave = obj.data[0].paid_vacation_leave; 
                  paidSickLeave = obj.data[0].paid_sick_leave;
                  leaveWithoutPay = obj.data[0].leave_without_pay;
                  maternityLeave = obj.data[0].maternity_leave;
                  paternityLeave = obj.data[0].paternity_leave;  

                  //SUMMARY: 2nd Column
                  regularOt = obj.data[0].regular_overtime;                  
                  restDay = obj.data[0].rest_day;
                  restDayOt = obj.data[0].rest_day_overtime;

                  //With Night Diff
                  regularOtNd = obj.data[0].regular_overtime_night_diff;
                  restDayNd = obj.data[0].regular_night_differential;
                  restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                  restDayNd = obj.data[0].rest_day_night_differential;


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

                  if (absence !== '0') {

                    $('#absences').text(absence);

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

                    $('#martenity-leave').text(maternityLeave);

                  }

                  if (paternityLeave !== '0') {

                    $('#paternity-leave').text(paternityLeave);

                  }                                                                        


                  //SUMMARY: 2nd Column
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

                  if (restDayNd !== '0') {

                    $('#reg-nd').text(restDayNd);

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

                    $('#legal-holiday-ot+nd').text(legalHolidayOtNd);

                  } 

                  if (legalHolidayNd !== '0') {

                    $('#legal-hoiday-nd').text(legalHolidayNd);

                  }                                                                     

            
                }
            },"json");        

            //Time In button
            $('#time-clocking-btn.time-in').click(function(e) {                                      
                //alert('time in');                
                e.preventDefault();
               // $('#time-clocking-btn.time-out').removeClass('hide').show(); 
               // $('#time-clocking-btn.time-in').addClass('hide').hide();                                           
              
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


                /*var overtimeStatus1 = $('#overtime-status-1').val();                            
                var overtimeStatus2 = $('#overtime-status-2').val();                                            
                var overtimeStatus3 = $('#overtime-status-3').val();  

                var resultOvertimeStatus1 = false;
                var resultOvertimeStatus2 = false;
                var resultOvertimeStatus3 = false;

                if ( overtimeStatus1 === '' &&
                     overtimeStatus2 === '' && 
                     overtimeStatus3 === '' ) {

                  //console.log(typeof overtimeStatus1);

                  alert('Do you want to apply for OT');

                  var txt;
                  var result = confirm("Do you want to apply your first OT");
                  if (result == true) {
                      timeClocking = 'out';
                      resultOvertimeStatus1 = result;
                  }else {
                      txt = "You pressed Cancel!";
                  }               
                  
                  bootbox.confirm({ 
                      size: 'small',
                      message: "Do you want to apply your OT for your 1st shift?", 
                      callback: function(result){ 
                        
                        var resultOvertimeStatus1 = false;
                        resultOvertimeStatus1 = result;

                      }
                  })

                } else if ( overtimeStatus1 !== '' &&
                     overtimeStatus2 === '' && 
                     overtimeStatus3 === '' ) {

                  console.log('overtime status 1:' + overtimeStatus1);                      


                } else if ( overtimeStatus1 !== '' &&
                     overtimeStatus2 === '' && 
                     overtimeStatus3 !== '' ) {

                  console.log('overtime status 2:' + overtimeStatus1);                      


                } */               

                //var dataString = 'timeclocking=' + timeClocking + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
                    //dataString += '&timein1=' + timeIn1 + '&timein2=' + timeIn2 + '&timein3=' + timeIn3 + '&overtimeStatus1=' + overtimeStatus1 + '&overtimeStatus2=' + overtimeStatus3 + '&overtimeStatus1=' + overtimeStatus3 + '&resultOvertimeStatus1=' + resultOvertimeStatus1; + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;


                var dataString = 'timeclocking=' + timeClocking + '&employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut;
                    dataString += '&timein1=' + timeIn1 + '&timein2=' + timeIn2 + '&timein3=' + timeIn3 + '&forgotyesterdaytimeout=' + forgotYesterdayTimeOut;

                

                //var dataString = 'employeeno=' + employeeNumber + '&daydate=' + dayDate + '&schedin=' + schedIn + '&schedout=' + schedOut + '&timein1=' + timeIn1;

                //console.log(dataString);

                $.ajax({
                    type: "POST",
                    url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
                    data : dataString,
                    success : function(data) {
                      console.log(data);  

                        var timeClockingBtn = $('#time-clocking-btn').text();

                        //console.log(timeClockingBtn.toLowerCase());                

                        if (timeClockingBtn.toLowerCase() === 'time in') {

                            $('#time-clocking-btn.time-out').removeClass('hide').show(); 
                            $('#time-clocking-btn.time-in').addClass('hide').hide();                

                        }                        
                    }
                },"json");                

                table.destroy(); 
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
                        /*{ "data": "time_in_1" },
                        { "data": "time_out_1" }, 
                        { "data": "time_in_2" },
                        { "data": "time_out_2" }, 
                        { "data": "time_in_3" },
                        { "data": "time_out_3" },*/
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
                });

                //Summary Computation Init
                $.ajax({
                    type: "GET",
                    url : "{{ route('redrawSummary') }}", //http://localhost:8000/employee",
                    data : '',
                    success : function(data) {
                      
                      var obj = JSON.parse(data);

                      var lates = '', undertime = '', absence = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';  
                      var regularOt = '', restDay = '', restDayOt = '';
                      var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                      var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                      //With Night Diff

                      var regularOtNd = '', restDayNd = '', restDayOtNd = '', restDayNd = '';
                      var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                      var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                      //SUMMARY: 1st column
                      lates = obj.data[0].tardiness;
                      undertime = obj.data[0].undertime;
                      absence = obj.data[0].absence; 

                      paidVacationLeave = obj.data[0].paid_vacation_leave; 
                      paidSickLeave = obj.data[0].paid_sick_leave;
                      leaveWithoutPay = obj.data[0].leave_without_pay;
                      maternityLeave = obj.data[0].maternity_leave;
                      paternityLeave = obj.data[0].paternity_leave;                                              
                      
                      //SUMMARY: 2nd Column
                      regularOt = obj.data[0].regular_overtime;                  
                      restDay = obj.data[0].rest_day;
                      restDayOt = obj.data[0].rest_day_overtime;

                      //With Night Diff
                      regularOtNd = obj.data[0].regular_overtime_night_diff;
                      restDayNd = obj.data[0].regular_night_differential;
                      restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                      restDayNd = obj.data[0].rest_day_night_differential;


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

                          

                      if (lates !== '0' && undertime !== '0') {

                        $('#lates-ut').text(lates + ' / ' + undertime);

                      }

                      if (absence !== '0') {

                        $('#absences').text(absence);

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

                        $('#martenity-leave').text(maternityLeave);

                      }

                      if (paternityLeave !== '0') {

                        $('#paternity-leave').text(paternityLeave);

                      } 

                      //SUMMARY: 2nd Column
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

                      if (restDayNd !== '0') {

                        $('#reg-nd').text(restDayNd);

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

                        $('#legal-holiday-ot+nd').text(legalHolidayOtNd);

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
                //alert('time out');                
                            
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

                //console.log('1424768400' + '  ' + schedOut);
                //console.log(timeNow + '  ' + schedOut);
                

                //if(timeNow >= schedOut) { //Compare the time in for today to the schedule out                    
                
                   // $('#time-clocking-btn.time-in').removeClass('hide').show();                               
                   // $('#time-clocking-btn.time-out').addClass('hide').hide();                                                 

                    $.ajax({
                        type: "POST",
                        url : "{{ route('timeClocking') }}", //http://localhost:8000/employee",
                        data : dataString,
                        success : function(data) {
                          console.log(data);  

                            var timeClockingBtn = $('#time-clocking-btn').text();

                            if (timeClockingBtn.toLowerCase() === 'time in') {

                                $('#time-clocking-btn.time-in').removeClass('hide').show();                               
                                $('#time-clocking-btn.time-out').addClass('hide').hide();              

                            }  
                                                     
                        }
                    },"json");

                   table.destroy(); 
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
                            /*{ "data": "time_in_1" },
                            { "data": "time_out_1" }, 
                            { "data": "time_in_2" },
                            { "data": "time_out_2" }, 
                            { "data": "time_in_3" },
                            { "data": "time_out_3" },*/
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
                    });
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

                      var lates = '', undertime = '', absence = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';  
                      var regularOt = '', restDay = '', restDayOt = '';
                      var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                      var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                      //With Night Diff

                      var regularOtNd = '', restDayNd = '', restDayOtNd = '', restDayNd = '';
                      var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                      var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                      //SUMMARY: 1st column
                      lates = obj.data[0].tardiness;
                      undertime = obj.data[0].undertime;
                      absence = obj.data[0].absence; 

                      paidVacationLeave = obj.data[0].paid_vacation_leave; 
                      paidSickLeave = obj.data[0].paid_sick_leave;
                      leaveWithoutPay = obj.data[0].leave_without_pay;
                      maternityLeave = obj.data[0].maternity_leave;
                      paternityLeave = obj.data[0].paternity_leave;                                             
                      
                      //SUMMARY: 2nd Column
                      regularOt = obj.data[0].regular_overtime;                  
                      restDay = obj.data[0].rest_day;
                      restDayOt = obj.data[0].rest_day_overtime;

                      //With Night Diff
                      regularOtNd = obj.data[0].regular_overtime_night_diff;
                      restDayNd = obj.data[0].regular_night_differential;
                      restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                      restDayNd = obj.data[0].rest_day_night_differential;


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

                      if (absence !== '0') {

                        $('#absences').text(absence);

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

                        $('#martenity-leave').text(maternityLeave);

                      }

                      if (paternityLeave !== '0') {

                        $('#paternity-leave').text(paternityLeave);

                      } 

                      //SUMMARY: 2nd Column
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

                      if (restDayNd !== '0') {

                        $('#reg-nd').text(restDayNd);

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

                        $('#legal-holiday-ot+nd').text(legalHolidayOtNd);

                      } 

                      if (legalHolidayNd !== '0') {

                        $('#legal-hoiday-nd').text(legalHolidayNd);

                      }                                                                     

                
                    }
                },"json"); 

            });  




            //Search
            $('#search-timesheet-btn').click(function(e) {                                      
                
                e.preventDefault();                                       
              
                var employeeNumber = $('#employee-number').val();
                var dayDate = $('#day-date').val();
                var schedIn = $('#sched-in').val();
                var schedOut = $('#sched-out').val();
                var timeIn1 = $('#time-in-1').val();
                var timeIn2 = $('#time-in-2').val();
                var timeIn3 = $('#time-in-3').val();
                var forgotYesterdayTimeOut = $('#time-in-1').val();

                //var timeClocking = 'in';             

                var employeeId = $('#employee-id').val();
                var search = 'search';



                /*if (forgotYesterdayTimeOut === '') {
                                        
                    forgotYesterdayTimeOut = 0;                                        

                } else {

                    $('#forgot-yesterday-timeout').val();           

                }*/

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
                                url : "{{ route('redrawAdminSearchSummary') }}", //http://localhost:8000/employee",
                                data : '',
                                success : function(data) {
                                  
                                  var obj = JSON.parse(data);

                                  var lates = '', undertime = '', absence = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';  
                                  var regularOt = '', restDay = '', restDayOt = '';
                                  var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                                  var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                                  //With Night Diff

                                  var regularOtNd = '', restDayNd = '', restDayOtNd = '', restDayNd = '';
                                  var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                                  var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                                  //SUMMARY: 1st column
                                  lates = obj.data[0].tardiness;
                                  undertime = obj.data[0].undertime;
                                  absence = obj.data[0].absence;

                                  paidVacationLeave = obj.data[0].paid_vacation_leave; 
                                  paidSickLeave = obj.data[0].paid_sick_leave;
                                  leaveWithoutPay = obj.data[0].leave_without_pay;
                                  maternityLeave = obj.data[0].maternity_leave;
                                  paternityLeave = obj.data[0].paternity_leave;                        
                                  
                                  //SUMMARY: 2nd Column
                                  regularOt = obj.data[0].regular_overtime;                  
                                  restDay = obj.data[0].rest_day;
                                  restDayOt = obj.data[0].rest_day_overtime;

                                  //With Night Diff
                                  regularOtNd = obj.data[0].regular_overtime_night_diff;
                                  restDayNd = obj.data[0].regular_night_differential;
                                  restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                                  restDayNd = obj.data[0].rest_day_night_differential;


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

                                  if (absence !== '0') {

                                    $('#absences').text(absence);

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

                                    $('#martenity-leave').text(maternityLeave);

                                  }

                                  if (paternityLeave !== '0') {

                                    $('#paternity-leave').text(paternityLeave);

                                  }

                                  //SUMMARY: 2nd Column
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

                                  if (restDayNd !== '0') {

                                    $('#reg-nd').text(restDayNd);

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

                                    $('#legal-holiday-ot+nd').text(legalHolidayOtNd);

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

                //Summary Computation Init
                $.ajax({
                    type: "GET",
                    url : "{{ route('redrawAdminSearchSummary') }}", //http://localhost:8000/employee",
                    data : '',
                    success : function(data) {
                      
                      var obj = JSON.parse(data);

                      var lates = '', undertime = '', absence = '', paidVacationLeave = '', paidSickLeave = '', leaveWithoutPay = '', maternityLeave = '', paternityLeave = '';  
                      var regularOt = '', restDay = '', restDayOt = '';
                      var restDaySpecialHoliday = '', restDaySpecialHolidayOt = '', restDayLegalHoliday = '', restDayLegalHolidayOt = '';
                      var specialHoliday = '', specialHolidayOt = '', legalHoliday = '', legalHolidayOt = '';

                      //With Night Diff

                      var regularOtNd = '', restDayNd = '', restDayOtNd = '', restDayNd = '';
                      var restDaySpecialHolidayOtNd = '', restDaySpecialHolidayNd = '', restDayLegalHolidayOtNd = '', restDayLegalHolidayNd = '';
                      var specialHolidayOtNd = '', specialHolidayNd = '', legalHolidayOtNd = '', legalHolidayNd = '';                  


                      //SUMMARY: 1st column
                      lates = obj.data[0].tardiness;
                      undertime = obj.data[0].undertime;
                      absence = obj.data[0].absence;

                      paidVacationLeave = obj.data[0].paid_vacation_leave; 
                      paidSickLeave = obj.data[0].paid_sick_leave;
                      leaveWithoutPay = obj.data[0].leave_without_pay;
                      maternityLeave = obj.data[0].maternity_leave;
                      paternityLeave = obj.data[0].paternity_leave;                        
                      
                      //SUMMARY: 2nd Column
                      regularOt = obj.data[0].regular_overtime;                  
                      restDay = obj.data[0].rest_day;
                      restDayOt = obj.data[0].rest_day_overtime;

                      //With Night Diff
                      regularOtNd = obj.data[0].regular_overtime_night_diff;
                      restDayNd = obj.data[0].regular_night_differential;
                      restDayOtNd = obj.data[0].rest_day_overtime_night_diff;
                      restDayNd = obj.data[0].rest_day_night_differential;


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

                      if (absence !== '0') {

                        $('#absences').text(absence);

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

                        $('#martenity-leave').text(maternityLeave);

                      }

                      if (paternityLeave !== '0') {

                        $('#paternity-leave').text(paternityLeave);

                      }

                      //SUMMARY: 2nd Column
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

                      if (restDayNd !== '0') {

                        $('#reg-nd').text(restDayNd);

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

                        $('#legal-holiday-ot+nd').text(legalHolidayOtNd);

                      } 

                      if (legalHolidayNd !== '0') {

                        $('#legal-hoiday-nd').text(legalHolidayNd);

                      }                                                                     

                
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