<?php
$dbServerName = "192.168.101.5";
$dbUsername = "adminroot";
$dbPassword = "Admin@123";
$dbName = 'kpkd_dataonline';
$conn = new mysqli($dbServerName, $dbUsername, $dbPassword,$dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$connect = odbc_connect("CON_RPT", "sa", "Conzerv1");
if(!$connect){
	echo "<p>Connection to sql server database failed.</p>\n";
	exit;
}
?>