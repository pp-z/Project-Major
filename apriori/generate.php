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
		return 'salary- <30k ';
	else if ($salary>30000 and $salary<60000)
		return 'salary-between 30k and 60k';
	else
		return 'salary-more than 60k';
	
	
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
		
		printInterestingRules($rules);
		saveAssociationRules($rules);
		
		//$Apriori->saveFreqItemsets('freqItemsets.txt');
		//$Apriori->saveAssociationRules('associationRules.txt');
		
	

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
	
echo '<h3>Association Rules Generated</h3>';

$rec_products=array();

echo '<table><tr><td><b>Rule</td><td><b>Confidence</td></tr>';

foreach ($rules as $lhs => $rhs_list) {
    foreach ($rhs_list as $rhs_key =>$rhs_conf){
			if(is_int($rhs_key)){
				$content.='<tr><td>'.$lhs.'=>'.$rhs_key.'</td><td>'.$rhs_conf.'</td></tr>';
				
				
			}
	
	}
}
echo $content;

echo '</table>';

}




function saveAssociationRules($rules){
	 $content = '';
     
	foreach ($rules as $lhs => $rhs_list) {
		foreach ($rhs_list as $rhs_key =>$rhs_conf){
				if(is_int($rhs_key)){
					$content.=$lhs.'=>'.$rhs_key.PHP_EOL;
					
				}
		
		}
        file_put_contents("rules.txt", $content);
	}

}


function isSatisfied($conditions){
    
	foreach($conditions as $condition){
		
		if(substr($condition, 0, 4 ) === "age-")
		{
		
			//echo $condition.' = '.get_age_range($_SESSION['age']).'<br>';
			if(strcmp(trim($condition),trim(get_age_range($_SESSION['age'])))!==0)
				return false;
			
						
		}
		else if (substr($condition,0,7)==="gender-")
		{
			//echo substr($condition,7).' = '.$_SESSION['gender'].'<br>';
			if(!(substr($condition,7)==$_SESSION['gender']))
				return false;
			
			
		}
		else if(substr($condition,0,7)=="salary-")
		{
			//echo $condition.' = '.get_salary_range($_SESSION['salary']).'<br>';
			if(strcmp(trim($condition),trim(get_salary_range($_SESSION['salary'])))!==0)
				return false;
			
			
		}
	

	}
	return true;
}


	

?>  
</body>
</html>