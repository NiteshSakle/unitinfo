<?php
error_reporting(0);
include("connect.php");
$scrfile = "C:/xampp/htdocs/new_unitinfo/group2.txt";
//$scrfile = "C:/wamp/www/unitinfo/group2.txt";
$coalU5 = 0;
$genU5 = 0;
$StaCoalFlow = 0;
$Act_div1_4 = 0;
/* ---------------------------  Data Access from 500 MW --------------------------------- */
if (file_exists($scrfile)) {
    $handle = fopen($scrfile, "r");
    if ($handle == false) {
        $genU5 = "N/A";
        exit;
        return false;
    } else {
        fgets($handle);
        fgets($handle);
        $string_U5 = trim(fgets($handle));
        $string_U5 = preg_replace('/\s+/', " ", $string_U5);    // Remove multiple space
        $unitInfo = explode(" ", $string_U5);
        //var_dump($unitInfo);
        $genU5 = $unitInfo[sizeof($unitInfo) - 2];   //previously -2 instead of -1
        if ($genU5 == '') {
            $genU5 = $unitInfo[sizeof($unitInfo) - 1];
        }
        $string_Coal = trim(fgets($handle));
        $coalInfo = preg_replace('/\s+/', " ", $string_Coal);  // Remove multiple space
        $coalInfo = explode(" ", $coalInfo);
        $coalU5 = $coalInfo[sizeof($coalInfo) - 2];
        if ($coalU5 == '') {
            $coalU5 = $coalInfo[sizeof($coalInfo) - 1];
        }

        if ($coalU5 > 1000) {
            $coalU5 = 0.0;
        }
        fclose($handle);
    }
} else {
    $genU5 = "No file";
}

$timezone = new DateTimeZone("Asia/Kolkata");
$date = new DateTime();
$date->setTimezone($timezone);

$sqlGen1 = "SELECT convert(varchar(19),TIMESTAMP)TIMESTAMP,S1N1_KW_VAL0 GEN1 FROM ALLKW1	WHERE TIMESTAMP=(select max(timestamp) from ALLKW1)";
$sqlGen2_3 = "SELECT S1N21_KW_VAL0 GEN2, S1N33_KW_VAL0 GEN3 FROM ALLKW2 WHERE TIMESTAMP=(select max(timestamp) from ALLKW2)";
$sqlGen4 = "SELECT S1N49_KW_VAL0 GEN4 FROM ALLKW3 WHERE TIMESTAMP=(select max(timestamp) from ALLKW3)";

$resGen1 = odbc_exec($connect, $sqlGen1);
$resGen2_3 = odbc_exec($connect, $sqlGen2_3);
$resGen4 = odbc_exec($connect, $sqlGen4);

$genU1 = number_format(odbc_result($resGen1, "GEN1"), 2);
$genU2 = number_format(odbc_result($resGen2_3, "GEN2"), 2);
$genU3 = number_format(odbc_result($resGen2_3, "GEN3"), 2);
$genU4 = number_format(odbc_result($resGen4, "GEN4"), 2);

//COAL FLOW
/*$sql = "SELECT * FROM " . $dbName . ".tag000000007 order by DTime asc limit 1 ; ";
$coal1row = $conn->query($sql)->fetch_assoc();
$coal1 = $coal1row["PV"];
$sql = "SELECT * FROM " . $dbName . ".tag000000008 order by DTime asc limit 1 ; ";
$coal2row = $conn->query($sql)->fetch_assoc();
$coal2 = $coal2row["PV"];
$sql = "SELECT * FROM " . $dbName . ".tag000000009 order by DTime asc limit 1 ; ";
$coal3row = $conn->query($sql)->fetch_assoc();
$coal3 = $coal3row["PV"];
$sql = "SELECT * FROM " . $dbName . ".tag000000010 order by DTime asc limit 1 ; ";
$coal4row = $conn->query($sql)->fetch_assoc();
$coal4 = $coal4row["PV"];
*/


    $scrfile ="C:\modbus-scan\modbus.txt";
//--------------------------- CSV File Handling -----------------------------------
    $StaCoalFlow=0;
    $sql ="";
    if (file_exists($scrfile))
	{
        $handle = fopen($scrfile, "r");
	    if ($handle==false){
      		echo "Unable to open file.";
            exit;
		}else{
	        while(!feof($handle)){
                $unitInfo= (explode(",",fgets($handle)));

                $DATETIME=   $date->format( 'd M , Y  H:i' );
                 $colU1= $unitInfo[6];
             if($colU1>300){
                    $colU1=0.0;
                }
               $StaCoalFlow = $StaCoalFlow + $colU1;
               $colU2= $unitInfo[7];
            if($colU2>300){
                 $colU2=0.0;
             }
            $StaCoalFlow=   $StaCoalFlow + $colU2;
            $colU3= $unitInfo[8];
            if($colU3>300){
                 $colU3=0.0;
             }
            $StaCoalFlow=   $StaCoalFlow + $colU3;
            $colU4= $unitInfo[9];
            if($colU4>300){
                 $colU4=0.0;
             }
            $StaCoalFlow=   $StaCoalFlow + $colU4;
            $u1_u4coalflow=$StaCoalFlow;
            $StaCoalFlow=   $StaCoalFlow + $coalU5;
            fclose($handle);

           //ECHO $sql;
		   break;
		}
  	      }
	   }






//actual generation
$act_gen = 0;
if ($genU1 != 0) {
    $act_gen += 210;
}
if ($genU2 != 0) {
    $act_gen += 210;
}
if ($genU3 != 0) {
    $act_gen += 210;
}
if ($genU4 != 0) {
    $act_gen += 210;
}
$act_gen1234 = $act_gen;

if ($genU5 != 0) {
    $act_gen = $act_gen + 500;
}
$genU1toU4 = $genU1 + $genU2 + $genU3 + $genU4;
$coal1to4 = $colU1 + $colU2 + $colU3 + $colU4;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=0.5">
        <meta name="description" content="mw display">
        <meta name="author" content="Nitesh">
        <META HTTP-EQUIV="Refresh" CONTENT="10">
        <title>KPKD Generation</title>
        <link href="css/responsive_css.css" rel="stylesheet">
    </head>
     <style>
@media screen and (max-width: 540px) {
    table {
        width:100%;
        height: 100%;
    }
    h2{
      font-size:18px;
      text-align:center;
    }

}
</style>
    <body>
        <div  style="font-family: verdana" align="center">
            <h2><u>KHAPERKHEDA TPS - GENERATION OVERVIEW</u></h2>
            <div>
                <h3 >
                    <span style="background-color: #34495E;border-radius: 9px;">
                        <span id="curTime" style="color:#ffffff;font-style: normal;font-size: smaller;font-weight: bold;">Date & Time: <?php echo $DATETIME; ?></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                    </span>
                </h3>
            </div>

            <table  id="genTable" class="responsive-table responsive-table-input-matrix" style="width:100%;border-radius: 25px;">
                <thead>
                    <tr style='font-size:200%'>
                        <th style="width:5%">UNIT</th>
                        <th  style="width:25%">LOAD </th>
                        <th style="width:25%">PLF%</th>
                        <th  style="width:25%">COAL FLOW</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style='font-size:210%' >
                        <td  style="color:#ffc600;width:10%">UNIT-1</td>
                        <td> <?php echo number_format($genU1); ?></td>
                        <td> <?php echo number_format(($genU1 / 210) * 100, 2) ?></td>
                        <td> <?php echo number_format($colU1); ?></td>
                    </tr>
                    <tr style='font-size:210%'>
                        <td style="color:#ffc600;width:10%">UNIT-2</td>
                        <td><?php echo number_format($genU2); ?></td>
                        <td><?php echo number_format(($genU2 / 210) * 100, 2) ?></td>
                        <td> <?php echo number_format($colU2); ?></td>
                    </tr>
                    <tr style='font-size:210%'>
                        <td style="color:#ffc600;width:10%">UNIT-3</td>
                        <td><?php echo number_format($genU3); ?></td>
                        <td><?php echo number_format(($genU3 / 210) * 100, 2) ?></td>
                        <td> <?php echo number_format($colU3); ?></td>
                    </tr>
                    <tr style='font-size:210%'>
                        <td style="color:#ffc600;width:10%" >UNIT-4</td>
                        <td><?php echo number_format($genU4); ?></td>
                        <td><?php echo number_format(($genU4 / 210) * 100, 2) ?></td>
                        <td> <?php echo number_format($colU4); ?></td>
                    </tr>
                    <tr style="color:#06FE49;font-size:210%">
                        <td style="width:10%">UNIT 1-4</td>
                        <td><?php echo number_format($genU1toU4); ?></td>
                        <td><?php echo number_format(($genU1toU4 / $act_gen1234) * 100, 2) ?></td>
                        <td> <?php echo number_format($coal1to4); ?></td>
                    </tr>
                    <tr style='font-size:210%'>
                        <td style="color:#ffc600;width:10%">UNIT-5</td>
                        <td><?php echo number_format($genU5); ?></td>
                        <td><?php echo number_format(($genU5 / 500) * 100,2) ?></td>
                        <td> <?php echo number_format($coalU5); ?></td>
                    </tr>
                    <tr style="color:#06FE49;font-size:210%">
                        <td style="width:10%">STATION</td>
                        <td><?php echo number_format($genU1toU4 + $genU5); ?></td>
                        <td><?php echo number_format((($genU1toU4 + $genU5) / $act_gen) * 100, 2) ?></td>
                        <td> <?php echo number_format($coal1to4 + $coalU5); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>
