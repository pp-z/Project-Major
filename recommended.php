<?php
error_reporting(E_ERROR | E_PARSE);
if (!isset($_SESSION)) {session_start();}
include 'class.apriori.php';
if (!isset($_SESSION)) {session_start();}
function get_age_range($age){
	if($age<20)
		return 'age- < 20 ';
	else if ($age>20 and $age<40)
		return 'age-between 20 and 40';
	else
		return 'age-more than 40';
		
}
function get_salary_range($salary){
	if($salary<30000)
		return 'salary- <30k$ ';
	else if ($salary>30000 and $salary<60000)
		return 'salary-between 30k$ and 60k$';
	else
		return 'salary-more than 60$k';
	
	
}
$Apriori = new Apriori();

$Apriori->setMaxScan(20);       //Scan 2, 3, ...
$Apriori->setMinSup(2);         //Minimum support 1, 2, 3, ...
$Apriori->setMinConf(30);       //Minimum confidence - Percent 1, 2, ..., 100
$Apriori->setDelimiter(',');    //Delimiter 


$dataset = array();
$conn=mysql_connect('localhost','root','');
if($conn){
	if(mysql_select_db('olx',$conn)){
		
		$result=mysql_query("SELECT age,gender,salary,pid FROM user,cart,products WHERE pid=id and user_id=uid",$conn);
		if (!mysql_num_rows($result)) {
			$err='No product available';
		}
		$results = array();
		while($res = mysql_fetch_assoc($result)) {
			$res['age']=get_age_range($res['age']);
			$res['salary']=get_salary_range($res['salary']);
			$res['gender']='gender-'.$res['gender'];
			$r=array($res['age'],$res['salary'],$res['gender'],$res['pid']);
			$results[] = implode (", ", $r);
			
			
			
		}
		$Apriori->process($results);
		$rules=$Apriori->getAssociationRules(); 
		//$Apriori->saveFreqItemsets('freqItemsets.txt');
		//$Apriori->saveAssociationRules('associationRules.txt');
		//printInterestingRules($rules);
		$rec_products=array();

		foreach ($rules as $lhs => $rhs_list) {
			foreach ($rhs_list as $rhs_key =>$rhs_conf){
					if(is_int($rhs_key)){
						
						$str=$lhs.'=>'.$rhs_key.'<br>';
						//echo '<b> Rule - '.$str.'</b>';
						if(isSatisfied(explode(',',$lhs)))
							$rec_products[]=$rhs_key;
				//print_r(explode(',',$lhs));
				//echo $str;
					}
	
			}
		}


		$ids = join(',',$rec_products);  
		$sql = "SELECT * FROM products WHERE id IN ($ids)";
		$result=mysql_query($sql,$conn);
		
		
		
}
	else{
		$err='database error';
	}
}
else{
	$err='connection error';
}

function printInterestingRules($rules){
	$content='';
	
echo '<h3>Recommended Products </h3>';
echo $_SESSION['age'].' , '.$_SESSION['salary'].' , '.$_SESSION['gender'].'<br>';
$rec_products=array();

foreach ($rules as $lhs => $rhs_list) {
    foreach ($rhs_list as $rhs_key =>$rhs_conf){
			if(is_int($rhs_key)){
				$content.= $lhs.'=>'.$rhs_key.'<br>';
				$str=$lhs.'=>'.$rhs_key.'<br>';
				//echo '<b> Rule - '.$str.'</b>';
				if(isSatisfied(explode(',',$lhs)))
					$rec_products[]=$rhs_key;
				//print_r(explode(',',$lhs));
				//echo $str;
			}
	
	}
}

print_r($rec_products);
}


function isSatisfied($conditions){
    
	foreach($conditions as $condition){
		
		if(substr($condition, 0, 4 ) === "age-")
		{
		
			if(strcmp(trim($condition),trim(get_age_range($_SESSION['age'])))!==0)
				return false;
			//echo $condition.' = '.get_age_range($_SESSION['age']).'<br>';
						
		}
		else if (substr($condition,0,7)==="gender-")
		{
			
			if(!(substr($condition,7)==$_SESSION['gender']))
				return false;
			//echo substr($condition,7).' = '.$_SESSION['gender'].'<br>';
			
		}
		else if(substr($condition,0,7)=="salary-")
		{
			
			if(strcmp(trim($condition),trim(get_salary_range($_SESSION['salary'])))!==0)
				return false;
		//	echo $condition.' = '.get_salary_range($_SESSION['salary']).'<br>';
			
		}
	

	}
	return true;
}



?>


<!DOCTYPE HTML>
<html>
	<head>
		<title>Recommended</title>
		<link href="css/products.css" rel='stylesheet' type='text/css' />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" type="image/x-icon" href="images/fav-icon.png" />
		<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
		</script>
		<!----webfonts---->
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>
		<!--//webfonts-->
		<!-- Global CSS for the page and tiles -->
  		<link rel="stylesheet" href="css/main.css">
  		<!-- //Global CSS for the page and tiles -->
		<!--start-click-drop-down-menu-->
		<script src="js/jquery.min.js"></script>
        <!--start-dropdown-->
         <script type="text/javascript">
			var $ = jQuery.noConflict();
				$(function() {
					$('#activator').click(function(){
						$('#box').animate({'top':'0px'},500);
					});
					$('#boxclose').click(function(){
					$('#box').animate({'top':'-700px'},500);
					});
				});
				$(document).ready(function(){
				//Hide (Collapse) the toggle containers on load
				$(".toggle_container").hide(); 
				//Switch the "Open" and "Close" state per click then slide up/down (depending on open/close state)
				$(".trigger").click(function(){
					$(this).toggleClass("active").next().slideToggle("slow");
						return false; //Prevent the browser jump to the link anchor
				});
									
			});
		</script>
        <!--//End-dropdown-->
		<!--//End-click-drop-down-menu-->
	</head>
	<body>
		<!--start-wrap-->
			<!--start-header-->
			<div class="header">
				<div class="wrap">
				<div class="logo">
					
				</div>
				<div class="nav-icon">
					 <a href="#" class="right_bt" id="activator"><span> </span> </a>
				</div>
				 <div class="box" id="box">
					 <div class="box_content">        					                         
						<div class="box_content_center">
						 	<div class="form_content">
								<div class="menu_box_list">
									<ul>
										<li><a href="product.php"><span>home</span></a></li>
										<li><a href="recommended.php"><span>Recommended</span></a></li>
								
										<!-- <li><a href="#"><span>Works</span></a></li>
										<li><a href="#"><span>Clients</span></a></li>
										<li><a href="#"><span>Blog</span></a></li> -->
										<?php
										if (!isset($_SESSION)) {session_start();}
										if($_SESSION['category']=='A'){
											echo '<li><a href="post.php"><span>Post</span></a></li>';
										}
										if ($_SESSION['olx']!='guest') {
											echo '<li><a href="orders.php"><span>My Orders</span></a></li>';
											echo '<li><a href="#"><span>Balance:Rs.'.$_SESSION['rupees'].'</span></a></li>';
											echo '<li><a href="logout.php"><span>Logout</span></a></li>';
										}
										?>
										<div class="clear"> </div>
									</ul>
								</div>
								<a class="boxclose" id="boxclose"> <span> </span></a>
							</div>                                  
						</div> 	
					</div> 
				</div>       	  
				<!-- <div class="top-searchbar">
					<form>
						<input type="text" /><input type="submit" value="" />
					</form>
				</div> -->
				<div class="userinfo">
					<div class="user">
						<ul>
							<li><a href="<?php echo($_SESSION['olx']=="guest"?"index.php":"#");?>"><img src="images/user-pic.png" title="user-name" /><span><?php echo $_SESSION['olx'];?></span></a></li>
						</ul>
					</div>
				</div>
				<div class="clear"> </div>
			</div>
		</div>
		<!---//End-header-->
		<!---start-content-->
		<div class="content">
			<div class="wrap">
			 <div id="main" role="main">
			      <ul id="tiles">
			        <!-- These are our grid blocks -->
			        <?php
			        	while (mysql_num_rows($result) && $row=mysql_fetch_array($result)) {?>
			        		        <li onclick="location.href='productInfo.php?id=<?php echo $row['id'];?>';">
			        		        	<img src="<?php echo $row['img'];?>" width="282" height="118">
			        		        	<div class="post-info">
			        		        		<div class="post-basic-info">
			        			        		<h3><a href="#"><?php echo $row['name'];?></a></h3>
			        			        		<!-- <p>Lorem Ipsum is simply dummy text of the printing & typesetting industry.</p> -->
			        		        		</div>
			        		        		<div class="post-info-rate-share">
			        		        			<div class="">
			        		        				<span>Price:Rs.<?php echo $row['price'];?></span>
			        		        			</div>
			        		        			<div class="">
			        		        				<span><?php echo $row['pcount'];?>&nbsp;bought</span>
			        		        			</div>
			        		        			<div class="clear"> </div>
			        		        		</div>
			        		        	</div>
			        		        </li>
			        <?php	}
			         ?>
			        <!-- -->
			        <!-- End of grid blocks -->
			      </ul>
			    </div>
			</div>
		</div>
		<!---//End-content-->
		<!--wookmark-scripts-->
		  <script src="js/jquery.imagesloaded.js"></script>
		  <script src="js/jquery.wookmark.js"></script>
		  <script type="text/javascript">
		    (function ($){
		      var $tiles = $('#tiles'),
		          $handler = $('li', $tiles),
		          $main = $('#main'),
		          $window = $(window),
		          $document = $(document),
		          options = {
		            autoResize: true, // This will auto-update the layout when the browser window is resized.
		            container: $main, // Optional, used for some extra CSS styling
		            offset: 20, // Optional, the distance between grid items
		            itemWidth:280 // Optional, the width of a grid item
		          };
		      /**
		       * Reinitializes the wookmark handler after all images have loaded
		       */
		      function applyLayout() {
		        $tiles.imagesLoaded(function() {
		          // Destroy the old handler
		          if ($handler.wookmarkInstance) {
		            $handler.wookmarkInstance.clear();
		          }
		
		          // Create a new layout handler.
		          $handler = $('li', $tiles);
		          $handler.wookmark(options);
		        });
		      }
		      /**
		       * When scrolled all the way to the bottom, add more tiles
		       */
		      function onScroll() {
		        // Check if we're within 100 pixels of the bottom edge of the broser window.
		        var winHeight = window.innerHeight ? window.innerHeight : $window.height(), // iphone fix
		            closeToBottom = ($window.scrollTop() + winHeight > $document.height() - 100);
		
		        if (closeToBottom) {
		          // Get the first then items from the grid, clone them, and add them to the bottom of the grid
		          var $items = $('li', $tiles),
		              $firstTen = $items.slice(0, 10);
		          $tiles.append($firstTen.clone());
		
		          applyLayout();
		        }
		      };
		
		      // Call the layout function for the first time
		      applyLayout();
		
		      // Capture scroll event.
		      // $window.bind('scroll.wookmark', onScroll);
		    })(jQuery);
		  </script>
		<!--//wookmark-scripts-->
		<!--start-footer-->
		<div class="footer">
			
		</div>
		<!--//End-footer-->
		<!--//End-wrap-->
	</body>
</html>

