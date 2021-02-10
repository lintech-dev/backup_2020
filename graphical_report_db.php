<!DOCTYPE html>
<html lang="en">
<?php 
error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set("Asia/Kolkata");

session_start();
include 'conn.php';

?>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="test">

    <title>Dashboard</title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="vendor/morrisjs/morris.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    


</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
<?php 

//include 'admin_header.php';
$role = $_SESSION['role'];

if($role == 'admin' || $role == 'superadmin')
{
include 'admin_header.php';
}
else
{
include 'user_header.php';
}

function dateDiffInDays($from_date, $to_date)
{
    // Calculating the difference in timestamps
    $diff = strtotime($to_date) - strtotime($from_date);
    
    // 1 day = 24 hours
    // 24 * 60 * 60 = 86400 seconds
    return abs(round($diff / 86400));
}

$from_date = $_POST['fdate'];
$to_date = $_POST['todate'];
$location = $_POST['location'];
$lab_type = $_POST['lab_type'];
$labname = $_POST['labname'];

    /* $date1 = date_create($from_date);
    $date2 = date_create($to_date);
    $diff = date_diff($date1,$date2);
    $main_diff = $diff->format("%a");
    //echo"maindiff: $main_diff";
    $hours = $main_diff*24; */


?> 
 

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h2 class="page-header">Graphical Reports</h2>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            
              <?php     
    echo "<center><font size='4px'; face='Calibri' margin-top='30px'>" . $_SESSION ['mess'] ."</font></center>";
    //echo "session";
    unset ( $_SESSION ['mess'] );
    
    ?>
    
    <?php
    
    
    $query_11 = $conn->prepare("select distinct location from lab where localtion_id='$location'");
    $query_11->execute();
    while($derow = $query_11->fetch())
    {
        $location11 = $derow['location'];
    }
    
    $query_22 = $conn->prepare("select distinct lab_type from lab where lab_type_id='$lab_type'");
    $query_22->execute();
    while($derow2 = $query_22->fetch())
    {
        $lab_type11 = $derow2['lab_type'];
    }
    
    
    $dateDiff = dateDiffInDays($from_date, $to_date); 
    $hours = $dateDiff*24;
    //echo"days: $dateDiff";
    //echo"hours: $hours";
    
    
    
        /* $sql1 = "SELECT count(*) FROM `lab_book` WHERE from_date BETWEEN '$from_date' AND '$to_date' AND lab_name = '$labname' AND location='$location11' AND lab_type='$lab_type11'";
        $result1 = $conn->prepare($sql1);
        $result1->execute();
        $number1 = $result1->fetchColumn();
        echo"<br>$number1"; */
    
    /* $query = "SELECT * FROM `lab_book` WHERE from_date BETWEEN '$from_date' AND '$to_date' AND lab_name = '$labname' AND location='$location11' AND lab_type='$lab_type11'";
    $stmt = $conn->query($query);
    while($row = $stmt->fetch())
    {
        $fdate_ftime = $row['fdate_ftime'];
        $tdate_ttime = $row['tdate_ttime'];
    } */
    
    $value_11 = array();
    $query_11 = "select time_format(timediff(fdate_ftime,tdate_ttime),'%H:%i:%s') from lab_book where from_date BETWEEN '$from_date' AND '$to_date' AND lab_name = '$labname' AND location='$location11' AND lab_type='$lab_type11' AND Approved_status = 'approved'";
    $stmt_11 = $conn->query($query_11);
    while($row_11 = $stmt_11->fetch())
    {
        $value_11[] = $row_11[0];
    }
    $value2_11 = array_sum($value_11);
    $total_hrs_used = str_replace("-","","$value2_11");
    //echo"value-- $total_hrs_used";
                                   ?>
      
    
    <?php 
    /*$date1 = date('t');
    $totalhours = $date1*24;
    //echo"$totalhours";
    
    $number11 = $number1/$hours;
    
    $number111 = round($number11*100);*/
    ?>
 

<center>
<table border="0px" >
<tr>
<td style="height: 70px;">
<p style="margin-top: -100px;"><b>Hours</b></p></td>
<td>
<div id="columnchart_values" style="width: 900px; height: 600px; margin-left: 0px"></div>
</td>
</table>
</center>
    
    
    
    
    
    
      

</body>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Hours", { role: "style" } ],
		["Total Hours", <?php echo"$hours"; ?>, "#0d6aad"],
		["Booked Hours", <?php echo"$total_hrs_used"; ?>, "#0d6aad"],


       
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        title: "Lab Name : <?php echo"$labname"; ?>",
		
        width: 900,
        height: 400,
        bar: {groupWidth: "70%"},
        legend: { position: "none" },
		
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
      chart.draw(view, options);
  }
  </script>

</html>

