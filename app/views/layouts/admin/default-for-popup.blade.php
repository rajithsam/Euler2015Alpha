<?php  
$employeeId = Session::get('userEmployeeId');
$userId = Session::get('userId');

$userGroups = DB::table('users_groups')->where('user_id', $userId)->first(); 
//$userGroups = DB::table('users_groups')->where('user_id', Auth::user()->id)->first(); 

if( !empty($userGroups) ) {

  $groups = DB::table('groups')->where('id', (int) $userGroups->group_id)->first();  

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
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">    

    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- Bootstrap theme -->
    <!--link href="{{ URL::asset('assets/css/bootstrap-theme.min.css') }}" rel="stylesheet"-->

    <!-- Google Font -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:700,300,400|Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">    

    <!-- Custom styles for this template -->
    <!--link href="{{ URL::asset('assets/css/starter-template.css') }}" rel="stylesheet"-->        
    <!--link href="{{ URL::asset('assets/css/signin.css') }}" rel="stylesheet"-->
    <link href="{{ URL::asset('assets/css/main.css') }}" rel="stylesheet">    
    
    <!--link href="{{ URL::asset('assets/css/clock.css') }}" rel="stylesheet"--> 
    <link href="{{ URL::asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet"> 

    <!--style>

    body { padding-top:70px; }

    </style-->

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

    <div class="container">    
      @yield('content')
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/scripts.js') }}"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="{{ URL::asset('assets/js/ie10-viewport-bug-workaround.js') }}"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

    <!-- Moment.js -->
    <!--script src="{{ URL::asset('assets/js/moment.js') }}"></script-->

    <!-- Jeditable code -->
    <script src="{{ URL::asset('assets/js/jquery.jeditable.js') }}"></script>    

    <!-- Datatables code -->
    <script src="{{ URL::asset('assets/js/jquery.dataTables.min.js') }}"></script>    

    <!-- Datatables code - Admin -->
    <!--script src="{{ URL::asset('assets/js/jquery.dataTables.1.9.4.js') }}"></script-->

    <!-- Bootbox code http://bootboxjs.com/-->
    <script src="{{ URL::asset('assets/js/bootbox.js') }}"></script>   


  <script>
    //#Jquery UI
    $(function() {
    
      $( ".datepicker" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
      });

    });


  </script>


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


           /* $('#timesheet tbody td').editable("{{ route('adminTimesheetSave') }}", {

              callback : function(value, settings) {
                console.log(this);
                console.log(value);
                console.log(settings);
              }

            });*/

            /*
              // Apply the jEditable handlers to the table 
              $('#timesheet tbody td').editable( function( sValue ) {
                // Get the position of the current data from the node 
                var aPos = oTable.fnGetPosition( this );
                
                // Get the data array for this row 
                var aData = oTable.fnGetData( aPos[0] );
                
                // Update the data array and return the value 
                aData[ aPos[1] ] = sValue;
                return sValue;
              }, { "onblur": 'submit' } ); // Submit the form when bluring a field
              
              // Init DataTables
              oTable = $('#timesheet').dataTable();
            */
            

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

            //fnDrawCallback - new https://datatables.net/reference/option/drawCallback 
            //https://datatables.net/reference/api/draw()

            /* Init DataTables */
       /*var oTable = $('#timesheet tbody td').dataTable();
       //$('td', oTable.fnGetNodes()).editable( '/index.cfm/futurepremiums/update', {
       oTable.$('#timesheet tbody td').editable("{{ route('adminTimesheetSave') }}", {
              "callback": function( sValue, y ) {
                 var aPos = oTable.fnGetPosition( this );
                 oTable.fnUpdate( sValue, aPos[0], aPos[1] );

                 console.log(sValue);
              },
              "submitdata": function ( value, settings ) {
                 return {
                    "row_id": this.parentNode.getAttribute('id'),
                    "column": oTable.fnGetPosition( this )[2]
                 };
              },
              "onblur" : "submit",
              "height": "25px",
              "width": "50%"
           }); */

            //https://datatables.net/api
            //http://henke.ws/post.cfm/datatables-and-jeditable-inline-editing
            //http://datatables.net/forums/discussion/8365/jeditable-datatables-how-can-i-refresh-table-after-edit
            //http://www.sprymedia.co.uk/dataTables-1.4/example_editable.html

          /* Apply the jEditable handlers to the table */
            //var oTable;

            //$('#timesheet tbody td').editable( function( sValue ) {
              /* Get the position of the current data from the node */
            //  var aPos = oTable.fnGetPosition( this );
              
              /* Get the data array for this row */
            //  var aData = oTable.fnGetData( aPos[0] );
              
              /* Update the data array and return the value */
           //   aData[ aPos[1] ] = sValue;
           //   return sValue;
           // }, { "onblur": 'submit' } ); /* Submit the form when bluring a field */
            
            /* Init DataTables */
           // oTable = $('#timesheet').dataTable();  


           // var table = $('#timesheet').DataTable( {
            var table = $('#timesheet').dataTable( {
                //"scrollY": 350,                
                "processing": true,
                "serverSide": true, 
                "fnDrawCallback": function () {
                //"drawCallback": function( settings ) {      
                  $('#timesheet tbody td').editable( "{{ route('adminTimesheetSave') }}", {
                      "callback": function( sValue, y ) {

                       var aPos = table.fnGetPosition( this );                       
                       table.fnUpdate( sValue, aPos[0], aPos[1] );

                        // Redraw the table from the new data on the server
                        table.fnDraw();
                        //table.draw();

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
                //"ajax": "{{ route('redrawTimesheet') }}",
                "sAjaxSource" : "{{ route('redrawTimesheet') }}",
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

          //$('#timesheet tbody').on('click', 'td', function () {
            //table.$('td').editable( function( sValue ) {
              /* Get the position of the current data from the node */
              //var aPos = table.fnGetPosition( this );
              
              /* Get the data array for this row */
              //var aData = table.fnGetData( aPos[0] );
              
              /* Update the data array and return the value */
              //aData[ aPos[1] ] = sValue;
              //return sValue;

              //console.log(sValue);
            //}, { "onblur": 'submit' } ); /* Submit the form when bluring a field */

             //} ); 




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
                      url : "{{-- route('redrawOvertimeStatus') --}}",
                      data : dataString,
                      success : function(data) {
                        
                        var overtimeStatusText = $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').text();

                        if ('Apply OT' === overtimeStatusText) {

                          console.log(data);

                          //var dataString = 'timesheetId=' + TimesheetId + '&otStatus=' + -1;                         
                          $('td.ot-status-btn:eq(' + rowIdx + ') .ot-apply-btn').remove();
                          $('td.ot-status-btn:eq(' + rowIdx + ')').html('<span class=\"label label-success\" style=\"padding: 2px 4px; font-size: 11px;\">Applied OT</span>');
                            
                        }


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

                  var lates = '', undertime = '';
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

                      var lates = '', undertime = '';
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

                      var lates = '', undertime = '';
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



    //Administration javascript

    /*$(document).ready(function() {

      $('#timesheet tbody td').editable("{{ route('adminTimesheetSave') }}");

    });*/


     
   
    </script>
  </body>
</html>