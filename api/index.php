<?php

include 'config.php';

//----------------------------------------------------------------------
// Get parameters
//----------------------------------------------------------------------

$list="NA";
if (isset($_GET['list'])) {
  if (preg_match("/^[0-9a-zA-Z_-]+$/",$_GET['list'])) {
    $list=$_GET['list'];
  } else die("You must provide a valid list!");
};

//----------------------------------------------------------------------
// MySQL stuff
//----------------------------------------------------------------------

// MySQL Connection
$db = new mysqli('localhost', $username, $password, $database);
if($db->connect_errno > 0){
  die('Unable to connect to database [' . $db->connect_error . ']');
}

// Check list
$lists = getAllLists($db);

//----------------------------------------------------------------------
// Compile XML
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

} else {
  
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

  <h1>how - a command-line Q&A tool</h1>
  Note that this documentation is also available on GitHub: <a href="https://github.com/NcLang/how">github.com/NcLang/how</a>.
    <br>For more information, see also <a href="https://www.nicolailang.de/">www.nicolailang.de</a>.
<p>
  <b>how</b> is a simple python script I wrote to increase my "command-line agility".<br>
  It provides quick access to code snippets that I use too rarely to remember,
    but frequently enough to annoy me whenever I have to look them up anew.
</p>
<p>
  Creating the script was motivated by the following situation that disrupts my actual workflow quite frequently:
    <ol>
  <li>Work productively and happily, with your hands on the keyboard.</li>
<li>You encounter one of those non-routine tasks. What was that sequence of options again to do this and that?</li>
  <li>Grab the mouse, open Google, enter keywords, press enter.</li>
  <li>Hurry up! Hit the first result, end up on Stack Exchange.</li>
<li>Copy the code snipped you used several times before with the options you always forget.</li>
<li>Proceed with your work.</li>
</ol>
Most people working with the command-line may have encountered this kind of workflow disruption, 
  though the list of such disruptive, "non-routine" snippets clearly depends on the user and its command-line affinity.
</p>
<p>
    <code>how</code> was written to minimize such disruptions. It is used as follows:
</p>
<div class="code">how to add a user in ubuntu
</div>
or less grammatically correct
<div class="code">how add user ubuntu
</div>
<p>
  At this point is should be clear why the script is named "how".<br>
  The response to the previous question reads:
</p>

<div class="code">Add a user and create its home directory automatically: 
- sudo adduser "username" 

Add a user without a home directory: 
- sudo useradd "username" 
- sudo passwd "username"
</div>

  <h2>Installation</h2>

<code>how</code> is a simple, single-file Python script. 
  It requires Python 3 (tested with Python 3.4) and
  the following python packages (install them with your OS package manager or via <code>pip</code>):
    <ul>
  <li><code>termcolor</code></li>
  <li><code>xml</code></li>
  <li><code>configparser</code></li>
  <li><code>urllib</code></li>
</ul>

Then download the latest version
    <p>
v0.1 (04-09-2015): <a href="/download/latest/how.py" download>how.py</a>
</p>
  or clone it via git
    <div class="code">git clone http://github.com/NcLang/how</div>
			Place the script in a directory of your choice (e.g. <code>~/bin/</code> or <code>~/scripts/</code>),
			make sure that it is executable,
			<div class="code">chmod 755 how.py</div>
			and place a link in your system's search path, 
			<div class="code">sudo ln -s /path/to/script/how.py /usr/bin/how</div>
It is recommended to call the script "how" for obvious reasons.
<br> Now you are ready to go. 

  <h2>Configuration</h2>

When you first call <code>how</code>, it will create a configuration file in <code>~/.how/</code>
and download the default list from <code>how.nl5.de</code> as XML file into the same directory.
The script uses this file as database, so no internet connection is required once the list has been downloaded.
<br>It will, however, update the list automatically whenever you call <code>how</code> and the local file is older than the
update interval specified as <code>UpdateInterval</code> in the configuration file <code>how.cfg</code>. 
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

  The following Q&A lists are currently available (click to view the XLS-styled XML files):

  <?php
  // Entries
  echo "<ol>";
  foreach ($lists as $key => $val) {
    echo "<li><a href='/xml/$val' target='_blank'>$val</a></li>\n";
  }	
  echo "</ol>";
  ?>

  <h2>Licence</h2>

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
<br><br>
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
<br><br>
    You should have received a copy of the GNU General Public License
    along with this program. If not, see <a href="http://www.gnu.org/licenses/">www.gnu.org/licenses/</a>

  </body>
  </html>
  
  <?php
}
?>

