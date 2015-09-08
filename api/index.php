<?php

// Get parameters
include 'config.php';

//----------------------------------------------------------------------
// MySQL Functions
//----------------------------------------------------------------------

// Get records in table
function getAllRecords($db,$table) {
  $response = array();
  $sql="SELECT id,question,answer FROM `$table`";
  $result=$db->query($sql);
  if(!$result) die('Error : ('. $db->errno .') '. $db->error);
  while ($row = $result->fetch_assoc()) {
    $response[] = $row;
  }       
  return($response);
}

// Get available tables
function getAllLists($db) {
  $response = array();
  $sql="SHOW TABLES";
  $result=$db->query($sql);
  if(!$result) die('Error : ('. $db->errno .') '. $db->error);
  while ($row = $result->fetch_assoc()) {
    $response[] = reset($row);
  }       
  return($response);
}

//----------------------------------------------------------------------
// Get GET parameters
//----------------------------------------------------------------------

$list="NA";
if (isset($_GET['list'])) {
  if (preg_match("/^[0-9a-zA-Z_-]+$/",$_GET['list'])) {
    $list=$_GET['list'];
  } else die("You must provide a valid list!");
};

//----------------------------------------------------------------------
// Connect to MySQL server
//----------------------------------------------------------------------

// MySQL Connection
$db = new mysqli('localhost', $username, $password, $database);
if($db->connect_errno > 0){
  die('Unable to connect to database [' . $db->connect_error . ']');
}

// Check list
$lists = getAllLists($db);

//----------------------------------------------------------------------
// Compile XML (for API)
//----------------------------------------------------------------------

if ($list!="NA") {

  if (!in_array($list,$lists)) die("This list does not exist!");
  $data = getAllRecords($db,$list);

  header('Content-Type: application/xml; charset=utf-8');

  echo '<?xml version="1.0" encoding="utf-8" ?>'."\n";
  echo '<?xml-stylesheet type="text/xsl" href="/show.xsl" ?>'."\n";
  echo '<qalist name="'.$list.'" updated="'.$date." ".$time.'" >'."\n";

  // Entries
  foreach ($data as $key => $row) {
    echo '<entry id="'.$row['id'].'">'."\n";
    echo "<question><![CDATA[".$row['question']."]]></question>\n";
    echo "<answer><![CDATA[".$row['answer']."]]></answer>\n";
    echo "</entry>\n\n";
  }		
  
  echo "</qalist>";

} 
//----------------------------------------------------------------------
// Compile HTML (for listing of tables)
//----------------------------------------------------------------------
else {
  ?>

  <!DOCTYPE HTML>
  <html>
  	<head>
  	<title><?php echo $website_name; ?></title>
  	<meta charset="UTF-8">
  	<meta http-equiv="expires" content="0">
	<meta http-equiv="author" content="<?php echo $administrator; ?>">
  	<link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,700' rel='stylesheet' type='text/css'>
  	<link rel="stylesheet" href="style.css">
  </head>
  <body>

  <h1>How Q&A List Server</h1>
  
  This is a Q&L list server for the command-line script <b>how</b>.<br>
  Documentation is available on GitHub: <a href="https://github.com/NcLang/how">github.com/NcLang/how</a>.
 
  <h2>Available Q&A lists</h2>

  The following Q&A lists are currently available on this server (click to view the XLS-styled XML files):

  <?php
  // Entries
  echo "<ol>";
  foreach ($lists as $key => $val) {
    echo "<li><a href='/xml/$val' target='_blank'>$val</a></li>\n";
  }	
  echo "</ol>";
  ?>
  
  <h2>Quick Start: How to use the lists</h2>
  
  To use one (or multiple) of the lists from above, extend your local configuration file <code>~/.how/how.cfg</code> 
  by one (or multiple) of the following blocks:
  <div class="code">[UNIQUENAME]
  # Update interval in hours
  UpdateInterval = 24
  # URL of this API server
  URL = http://<?php echo $server_name; ?>/xml/
  # One of the available lists from above
  List = QA-default
  </div>
  
  </body>
  </html>
  
<?php
}
?>

