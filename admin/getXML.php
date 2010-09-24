<?php

require_once( "db_connect.php" );

header("Content-type: text/xml"); 	

$pubType = isset($_GET[t]) ? $_GET[t] : "0";

$sql = "SELECT * FROM sally_www.pr_files WHERE type = '$pubType' ORDER BY pub_date DESC";

$result = mysql_query($sql, $link) or die(mysql_error());
$num_rows = mysql_num_rows($result);

if($num_rows){

echo "<response>\n";

//$fields = mysql_fetch_field($result);
//foreach($fields as $k => $v) {
//	echo "$k: $v<br />";
//}

while($row = mysql_fetch_assoc($result)){
/*
if(!$wroteFields) { 
	echo "<item>\n";
	foreach($row as $k => $v) {
		echo "<th>". strtoupper($k) . "</th>\n";
	}
	echo "</item>\n";
	$wroteFields = true;
}
*/
echo "<item>\n";
foreach($row as $k => $v) {
	echo "<$k><![CDATA[$v]]></$k>\n";
}
echo "</item>\n";

}

echo "</response>";

}
else {
echo "<error>No records in the database</error>";
}

?>
