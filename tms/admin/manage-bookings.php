<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{ 
	// code for cancel
if(isset($_REQUEST['bkid']))
	{
$bid=intval($_GET['bkid']);
$status=2;
$cancelby='a';
$sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby WHERE  BookingId=:bid";
$query = $dbh->prepare($sql);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query -> bindParam(':cancelby',$cancelby , PDO::PARAM_STR);
$query-> bindParam(':bid',$bid, PDO::PARAM_STR);
$query -> execute();

$sql3 = "SELECT tblbooking.BookingId as bookid,tblusers.FullName as fname,tblusers.MobileNumber as mnumber,tblusers.EmailId as email,tbltourpackages.PackageName as pckname,tblbooking.PackageId as pid,tblbooking.FromDate as fdate,DATEDIFF(tblbooking.ToDate,tblbooking.FromDate) as totaldays,tbltourpackages.PackagePrice as price, tblbooking.ToDate as tdate,tblbooking.roomsbooked as roomsbooked, tbltourpackages.rooms as roomsav, tblbooking.Comment as comment,tblbooking.status as status,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate from tblusers join  tblbooking on  tblbooking.UserEmail=tblusers.EmailId join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId";
$query = $dbh -> prepare($sql3);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{
	$pid=$result->pid;
	$fd=$result->roomsbooked;
	$fa=$result->roomsav;
	$rr=($fd+$fa);
	$con="UPDATE TblTourPackages SET rooms=:rr WHERE PackageId=:pid";
			$newrooms = $dbh->prepare($con);
			$newrooms-> bindParam(':rr', $rr, PDO::PARAM_STR);
			$newrooms-> bindParam(':pid', $pid, PDO::PARAM_STR);
			$newrooms->execute();
}

$msg="Booking Cancelled successfully";

}


}
if(isset($_REQUEST['bckid']))
	{
$bcid=intval($_GET['bckid']);
$status=1;
$cancelby='a';
$sql = "UPDATE tblbooking SET status=:status WHERE BookingId=:bcid";
$query = $dbh->prepare($sql);
$query -> bindParam(':status',$status, PDO::PARAM_STR);
$query-> bindParam(':bcid',$bcid, PDO::PARAM_STR);
$query -> execute();
$newstatus=1;
$con="update TblTourPackages set status=:status where PackageId=:pid";
$chgstatus = $dbh->prepare($con);
$chgstatus-> bindParam(':status', $newstatus, PDO::PARAM_STR);
$chgstatus-> bindParam(':pid', $pid, PDO::PARAM_STR);
$chgstatus->execute();
$msg="Booking Confirmed successfully ";
}
	?>
<!DOCTYPE HTML>
<html>
<head>
<title>TTMS | Admin manage Bookings</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<link href="css/bootstrap.min.css" rel='stylesheet' type='text/css' />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="css/morris.css" type="text/css"/>
<link href="css/font-awesome.css" rel="stylesheet"> 
<script src="js/jquery-2.1.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/table-style.css" />
<link rel="stylesheet" type="text/css" href="css/basictable.css" />
<script type="text/javascript" src="js/jquery.basictable.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
      $('#table').basictable();

      $('#table-breakpoint').basictable({
        breakpoint: 768
      });

      $('#table-swap-axis').basictable({
        swapAxis: true
      });

      $('#table-force-off').basictable({
        forceResponsive: false
      });

      $('#table-no-resize').basictable({
        noResize: true
      });

      $('#table-two-axis').basictable();

      $('#table-max-height').basictable({
        tableWrapper: true
      });
    });
</script>
<link href='//fonts.googleapis.com/css?family=Roboto:700,500,300,100italic,100,400' rel='stylesheet' type='text/css'/>
<link href='//fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/icon-font.min.css" type='text/css' />
</head> 
<body>
   <div class="page-container">
   <!--/content-inner-->
<div class="left-content">
	   <div class="mother-grid-inner">
            <!--header start here-->
				<?php include('includes/header.php');?>
				     <div class="clearfix"> </div>	
				</div>
<!--heder end here-->
<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a><i class="fa fa-angle-right"></i>Manage Bookings</li>
            </ol>
<div class="agile-grids">	
				<!-- tables -->
				<?php if($error){?><div class="errorWrap"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
				else if($msg){?><div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?> </div><?php }?>
				<div class="agile-tables">
					<div class="w3l-table-info">
					  <h2>Manage Bookings</h2>
					    <table id="table">
						<thead>
						  <tr>
						  <th>Booking id</th>					
							<th>Mobile No.</th>
							<th>Name</th>
							<th>Package Name </th>
							<th>From /To </th>
							<th>Comment </th>
							<th>Rooms Booked</th>
							<th>Status </th>
							<th>Action </th>
							<th>Edit_Mode </th>
						  </tr>
						</thead>
						<tbody>
<?php $sql = "SELECT tblbooking.BookingId as bookid,tblusers.FullName as fname,tblusers.MobileNumber as mnumber,tblusers.FullName as names, tblusers.EmailId as email,tbltourpackages.PackageName as pckname,tblbooking.PackageId as pid,tblbooking.FromDate as fdate,tblbooking.ToDate as tdate,tblbooking.Comment as comment,tblbooking.status as status,tblbooking.CancelledBy as cancelby,tblbooking.roomsbooked as bookedrooms, tbltourpackages.status as status2, tblbooking.UpdationDate as upddate from tblusers join  tblbooking on  tblbooking.UserEmail=tblusers.EmailId join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				?>		
				  <tr>
					<td>#BK-<?php echo htmlentities($result->bookid);?></td>
					<td><?php echo htmlentities($result->mnumber);?></td>
					<td><?php echo htmlentities($result->names);?></td>
					<td><a href="update-package.php?pid=<?php echo htmlentities($result->pid);?>"><?php echo htmlentities($result->pckname);?></a></td>
					<td><?php echo htmlentities($result->fdate);?> To <?php echo htmlentities($result->tdate);?></td>
						<td><?php echo htmlentities($result->comment);?>
							<td><?php echo htmlentities($result->bookedrooms);?>
						</td>
						<td><?php if($result->status==0)
{
echo "Pending";
// echo htmlentities($pid);

}
if($result->status==1)
{
echo "Confirmed";
}
if($result->status==2 and  $result->cancelby=='a')
{
echo "Canceled by you at " .$result->upddate;
} 
if($result->status==2 and $result->cancelby=='u')
{
echo "Canceled by User at " .$result->upddate;

}
?></td>

<?php if($result->status==2)
{


	?><td>Cancelled</td>
<?php } else {?>
<td><a href="manage-bookings.php?bkid=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Do you really want to cancel booking')" >Cancel</a> / <a href="manage-bookings.php?bckid=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('booking has been confirm')" >Confirm</a></td>
<td style="color: green;background-color: green;width: 10px;"><i class="fa fa-edit"></i></td>
<td style="background-color: red;"><i class="fa fa-lock"></i></td>
<?php }?>
					  </tr>
						 <?php $cnt=$cnt+1;} }?>
						</tbody>
					  </table>
					</div>
				  </table>				
			</div>
<div class="inner-block">

</div>

<?php include('includes/footer.php');?>

</div>
</div>
			<?php include('includes/sidebarmenu.php');?>
				  <div class="clearfix"></div>		
				</div>
							
<script src="js/jquery.nicescroll.js"></script>
<script src="js/scripts.js"></script>

   <script src="js/bootstrap.min.js"></script>
      

</body>
</html>
<?php } ?>