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
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>how - Q&A tool</title>
  <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="style.css">
  </head>
  <body>

  <h1>How-API Server</h1>
  
  Documentation is also available on GitHub: <a href="https://github.com/NcLang/how">github.com/NcLang/how</a>.
  <br>For more information, see also <a href="https://www.nicolailang.de/">www.nicolailang.de</a>.

You can change the list used to search for code snippets by setting the parameter <code>List</code> to one of the names listed below in the API section.
<br>To force an update and redownload the currently specified list, call
			<div class="code">how -u</div>
If you want to manage your database (<code>~/.how/howdb.xml</code>) manually (e.g. to add/modify your personal entries), you can disable the automatic update (which would overwrite your local modifications) by setting <code>UpdateInterval = 0</code>. 

  <h2>API</h2>

  <h3>Using the API</h3>

  The lists containing regular expression defining the questions and the corresponding answers
    are internally stored in a MySQL database and served as dynamically generated XML files.<br>
    The latter can be directly accessed via
    <p><code>http://how.nl5.de/xml/LIST</code></p>
  where LIST should be replaced by one of the valid list names listed below.<br>
    The above URL ist set as default API in the python script.<br>
      You can define the list your local script uses by setting the <code>List</code> parameter in
      the configuration file <code>~/.how/how.cfg</code>. By default the list "QA-default" is used.

  <h3>Available Q&A lists</h3>

  The following Q&A lists are currently available on this server (click to view the XLS-styled XML files):

  <?php
  // Entries
  echo "<ol>";
  foreach ($lists as $key => $val) {
    echo "<li><a href='/xml/$val' target='_blank'>$val</a></li>\n";
  }	
  echo "</ol>";
  ?>
  
  </body>
  </html>
  
<?php
}
?>

