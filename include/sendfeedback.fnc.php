<?php
  include 'var.inc.php';
  include 'class.inc.php';

  session_start();
  $oAccountUser=$_SESSION['oAccountUser'];
  $sessionid = ExecuteReader('Select AUSession as `result` From accountuser Where unAccountUser='.$oAccountUser->unAccountUser);

  if ($_SESSION['Session'] == '') {header("location:../end.php");}
  if ($_SESSION['Session'] != $sessionid) {header("location:../end.php");}

  if(isset($_POST['btnSFGetList']))
  {

  }

  if(isset($_POST['btnSFSend'])){
    $form_MESSAGE = htmlspecialchars($_POST['FEEDBACKMESSAGE']);
    $form_TYPE = htmlspecialchars($_POST['FEEDBACKTYPE']);

    $mysqli=new mysqli($_SESSION['server'],$_SESSION['username'],$_SESSION['password'],$_SESSION['database']);
    $stmt=$mysqli->stmt_init();
    if($stmt->prepare("INSERT INTO feedback(unAccountUser, FType, FMessage) VALUES (?,?,?)")){
      $stmt->bind_param("sss",$oAccountUser->unAccountUser,$form_TYPE,$form_MESSAGE);
      if(! $stmt->execute()){
        die("error");
      }
    }

    $stmt->close();
  }

  header('location:../'.$_POST['sessURL']);
 ?>
