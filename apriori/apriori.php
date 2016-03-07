<!DOCTYPE HTML>
<html>
<head> 
    <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
	<title>Apriori Alghoritm</title>
</head>
<body style="font-family: monospace;">
<?php   
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
$Apriori->setMinSup(1);         //Minimum support 1, 2, 3, ...
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
	
		while (mysql_num_rows($result) && $row=mysql_fetch_array($result)) { ?>
					<li onclick="location.href='productInfo.php?id=<?php echo $row['id'];?>';">
						<img src="<?php echo $row['img'];?>" width="282" height="118">
						<div class="post-info">
							<div class="post-basic-info">
								<h3><a href="#"><?php echo $row['name'];?></a></h3>
							</div>
							<div class="post-info-rate-share">
								<div class="">
									<span>Price:Rs.<?php echo $row['price'];?></span>
								</div>
								<div class="">
									<span><?php echo $row['pcount'];?>&nbsp;remaining</span>
								</div>
								<div class="clear"> </div>
							</div>
						</div>
					</li>
			        <?php	
			         

		}
	

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
</body>
</html>