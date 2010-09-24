<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link type="text/css" href="/css/custom-theme/jquery-ui-1.7.1.custom.css" rel="stylesheet" />	
<link type="text/css" href="/css/admin.css" rel="stylesheet" />	
<script type="text/javascript" src="/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="/js/admin_jq.js"></script>
<script type="text/javascript">
    $(function() {
        $(".datePicker").datepicker();
    });
</script>
</head>

<body>
<div id="adminCont">
    <div id="contentCont">
        <h1>View Newsletters</h1>
<?php

require_once( "db_connect.php" );
	
$sql = "SELECT * FROM sally_www.pr_files WHERE type = '1' ORDER BY pub_date DESC";

$result = mysql_query($sql, $link) or die(mysql_error());
$num_rows = mysql_num_rows($result);

if($num_rows){

echo "<table class=\"adminList\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n";

//$fields = mysql_fetch_field($result);
//foreach($fields as $k => $v) {
//	echo "$k: $v<br />";
//}

while($row = mysql_fetch_assoc($result)){

if(!$wroteFields) { 
	echo "<tr>\n";
	foreach($row as $k => $v) {
		echo "<th class=\"$k\">". strtoupper($k) . "</th>\n";
	}
	echo "<th></th>";
	echo "</tr>\n";
	$wroteFields = true;
}

echo "<tr id=\"" . $row["id"] . "\">\n";
foreach($row as $k => $v) {
	echo "<td class=\"$k\">$v</td>\n";
}
echo "<td><a href=\"#\" class=\"deletePub\">Delete</a></td>\n";
echo "</tr>\n";

}

echo "</table>";

}
else {
echo "No records in the database";
}

?>
	</div>
</div>

<body>
</body>
</html>
