<?php
	include 'include/var.inc.php';
	include 'include/class.inc.php';
	session_unset();
	session_start();
	$token = md5(uniqid(rand(),true));
	$_SESSION['token'] = $token;
	
	if(isset($_COOKIE['username'])){
		$loginuser=$_COOKIE['username'];
	}else{
		$loginuser='';
	}
	
	if(isset($_COOKIE['busunit'])){
		$busunit=$_COOKIE['busunit'];
	}
	/*$colAccountUser=new Collection;
	$mysqli=new mysqli($server,$username,$password,$database);
	$query='SELECT idAccountUser,idAccountGroup,AUUserName,AULastName,AUFirstName,AUMiddleName,AUPassword,Status FROM accountuser WHERE 1';
	
	$result=$mysqli->query($query);
	while($row=$result->fetch_assoc()){
		$oAccountUser = new AccountUser($row['idAccountGroup'],$row['AUUserName'],$row['AULastName'],$row['AUFirstName'],$row['AUMiddleName'],$row['AUPassword'],$row['Status'],$row['idAccountUser']);
		$colAccountUser->Add($oAccountUser,$oAccountUser->idAccountUser);
	}*/
	
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="css/class.css">
<link rel="stylesheet" type="text/css" href="css/login.css">
<link id="favicon" rel="SHORTCUT ICON" href="img/<?php 
		if(isset($busunit)){
			echo $busunit; 
		}?>.ico" type="image/x-icon">
<script src="js/jquery1.5.2.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
    if($('#txtusername').val()!=''){
		$('#txtpassword').focus();
	}
});

function changeLogo(busunit){
	//alert($(busunit).val());
	switch ($(busunit).val()){
		case 'wti':
			var siteurl='waffletime.com';
			var title='Waffle Time';
			var color='#da251d';
			var logo='wti';
		break;
		case 'wtitest':
			var siteurl='waffletime.com';
			var title='Waffle Time';
			var color='#996b5c';
			var logo='wti'
		break;
		case 'rnm':
			var siteurl='ricenmore.ph';
			var title="Rice N' More";
			var color='#daa520';
			var logo='rnm'
		break;
	}
	$('#indexlogo').css("background-image", "url(img/"+logo+".png)");
	$('#homewebsite').attr("href", "http://www."+siteurl+"/");
	document.title = title+' Daily Inventory Report';
	$('#welcome').empty();
	$('#welcome').append('Welcome to '+title+' Daily Inventory Report. Login to continue.');
	$('#indexfooter').empty();
	$('#indexfooter').append('&copy; Copyright 2013 '+title+' Inc. All Rights Reserved.');
	$('#favicon').remove();
    $('head').append('<link rel="SHORTCUT ICON" href="img/'+logo+'.ico" type="image/x-icon" id="favicon">');
	$('#indexfooter').css("color", color);
	$('#indexlogo').css("background-color", color);
}
</script>
<title><?php
	 	if(isset($busunit)&&$busunit=='wti'||isset($busunit)&&$busunit=='wtitest'){
			echo "Waffle Time"; 
		}else if(isset($busunit)&&$busunit=='rnm'){
			echo "Rice N' More"; 
		}else{echo "Waffle Time";}
	 	?> Daily Inventory Report</title>
</head>
<body style="background-color:#BBB">
<div id="indexlogin">
	<div id="indexlogo" style="background-image:url(img/<?php if(isset($busunit)){ echo $busunit;}else{ echo 'wti';}?>.png); background-color:<?php
	 	if(isset($busunit)&&$busunit=='wti'){
			echo "#da251d"; 
		}else if(isset($busunit)&&$busunit=='rnm'){
			echo "#daa520"; 
		}else if(isset($busunit)&&$busunit=='wtitest'){
			echo "#996b5c"; 
		}else{echo "#da251d";}
	 	?>"></div>
	<div id="indexfrlogin" align="center">
    <p id="welcome">Welcome to <?php
	 	if(isset($busunit)&&$busunit=='wti'){
			echo "Waffle Time"; 
		}else if(isset($busunit)&&$busunit=='rnm'){
			echo "Rice N' More"; 
		}else if(isset($busunit)&&$busunit=='wtitest'){
			echo "Waffle Time [TEST]"; 
		}else{echo "Waffle Time";}
	 	?> Daily Inventory Report. Login to continue.</p>
    <br>
    <form name="form1" action="auth.php" method="post">
    <input type="text" name="txtusername" id="txtusername" class="indextextbox" placeholder="username" autofocus required value="<?php //echo $loginuser; ?>" style="border: 0px solid;" />
	<br><br>
    <input type="password" name="txtpassword" id="txtpassword" class="indextextbox"  placeholder="password" style="border: 0px solid;" />
    <br><?php
	
	/*$aucount = strlen('princess');
	$salt = substr(md5('princess'),$aucount);
	$pwhash = md5('princess');
	$pwhashsount=strlen($pwhash);
	echo substr($pwhash,$aucount).$salt.substr($pwhash,$aucount+1,$pwhashsount-$aucount);*/
	?>
	<br><br>
    <select name="busunit" style="width:230px; border-radius:0px; padding:5px;" onChange="changeLogo(busunit)" style="border: 0px solid;" >
    	<option value="wti" <?php if(isset($busunit)&&$busunit=='wti'){ echo 'selected'; } ?>>Waffletime</option>
       	<option value="wtitest" <?php if(isset($busunit)&&$busunit=='wtitest'){ echo 'selected'; } ?>>Waffletime [TEST]</option>
        <option value="rnm" <?php if(isset($busunit)&&$busunit=='rnm'){ echo 'selected'; } ?>>Rice n' More</option>
    </select>
    <br><br>
    <button type="submit" name="btnlogin" id="btnlogin" class="buttons" value="Login" >Login</button>
    <button type="reset" name="btnreset" id="btnreset" class="buttons" value="Clear" >Clear</button>

    <input type="hidden" name="token" value="<?php echo $token; ?>"/>
        
    </form>
    <br>
    <p>Not the page you expected? Click <a id="homewebsite" href="http://www.<?php
	 	if(isset($busunit)&&$busunit=='wti'){
			echo 'waffletime.com'; 
		}else if(isset($busunit)&&$busunit=='rnm'){
			echo 'ricenmore.ph'; 
		}else if(isset($busunit)&&$busunit=='wtitest'){
			echo "Wafflet Time [Test]"; 
		}else{echo "Waffle Time";}
	 ?>">here</a>.
    <br>
    Forgot password? Contact the MIS Department</p>
    </div>
    <div id="indexfooter" style="color:<?php
	 	if(isset($busunit)&&$busunit=='wti'){
			echo "#da251d"; 
		}else if(isset($busunit)&&$busunit=='rnm'){
			echo "#daa520"; 
		}else if(isset($busunit)&&$busunit=='wtitest'){
			echo "#996b5c"; 
		}else{echo "#da251d";}
	 	?>">
    &copy; Copyright 2013 <?php
	 	if(isset($busunit)&&$busunit=='wti'){
			echo "Waffle Time"; 
		}else if(isset($busunit)&&$busunit=='rnm'){
			echo "Rice N' More Kiosks"; 
		}else if(isset($busunit)&&$busunit=='wtitest'){
			echo "Waffle Time [TEST]"; 
		}else{echo "Waffle Time";}
	 	?> Inc. All Rights Reserved.
    </div>
</div>
</body>
</html>