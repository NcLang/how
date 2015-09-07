<?php

// General data
$admin_email="kontakt@nicolailang.de";
$administrator="Nicolai Lang";
$website_name="HowAPI";

if (PHP_SAPI != "cli") {
  // IP-Adresse
  $ip= $_SERVER['REMOTE_ADDR'];
  // Pfade
  $server_name=$_SERVER['SERVER_NAME'];
  $file_name=$_SERVER['PHP_SELF'];
}

// Date & Time
$date = date('Y-m-d');
$time = date('H:i:s');

// MySQL data
$username="how_user";    
$password="g2y6Hy_5";        
$database="nicolai_how"; 

function getAllRecords($db,$table) {
  
  $response = array();

  // Get record
  $sql="SELECT id,question,answer FROM `$table`";
  $result=$db->query($sql);
  if(!$result) die('Error : ('. $db->errno .') '. $db->error);
  while ($row = $result->fetch_assoc()) {
    $response[] = $row;
  }       

  return($response);
}

function getAllLists($db) {
  
  $response = array();

  // Get record
  $sql="SHOW TABLES";
  $result=$db->query($sql);
  if(!$result) die('Error : ('. $db->errno .') '. $db->error);
  while ($row = $result->fetch_assoc()) {
    $response[] = reset($row);
  }       

  return($response);
}



/*
  <!--
  ========================================
  LOADED successfully config.inc
  ========================================
  -->
*/
?>