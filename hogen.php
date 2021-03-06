<?php
include("connect.php");
error_reporting(0);
$lineno=0;
//    $scrfile ="//172.16.160.36/wwwroot/group2.txt";
    $scrfile ="C:/xampp/htdocs/unitinfo/group2.txt";
    $coalU5=0;
    $genU5=0;
    $StaCoalFlow=0;
    $totPLFSta=0;
    /* ---------------------------  Data Access from 500 MW --------------------------------- */
    if (file_exists($scrfile)) {
        $handle = fopen($scrfile, "r");
	    if ($handle==false){
    		$genU5="N/A";
            exit;
    		return false;
	    }else{
            fgets($handle);
            fgets($handle);
            $string_U5=trim(fgets($handle));
            $string_U5=preg_replace('/\s+/'," ",$string_U5);    // Remove multiple space
               
            $unitInfo= explode(" ",$string_U5);
            $genU5=$unitInfo[sizeof($unitInfo)-2];   //previously -2 instead of -1
            if($genU5 == '') {
                $genU5=$unitInfo[sizeof($unitInfo)-1];
            }
            if($genU5>600){
                 $genU1=0.0;
            }
            $string_Coal=trim(fgets($handle));
            $coalInfo= preg_replace('/\s+/'," ",$string_Coal);  // Remove multiple space
            $coalInfo= explode(" ",$coalInfo);
            $coalU5=$coalInfo[sizeof($coalInfo)-2];
            if($coalU5 == '') {
                $coalU5=$coalInfo[sizeof($coalInfo)-1];
            }
            if($coalU5>1000){
               $coalU5=0.0;
            }
            fclose($handle);
       }
       $Act_div5=500;
    } else{
        $genU5="N/A";
    }

$timezone = new DateTimeZone("Asia/Kolkata" );
$date = new DateTime();
$date->setTimezone($timezone );
$DATETIME=   $date->format( 'd M , Y  H:i A' );

$sqlGen1="SELECT convert(varchar(19),TIMESTAMP)TIMESTAMP,S1N1_KW_VAL0 GEN1 FROM ALLKW1	WHERE TIMESTAMP=(select max(timestamp) from ALLKW1)";
$sqlGen2_3 = "SELECT S1N21_KW_VAL0 GEN2, S1N33_KW_VAL0 GEN3 FROM ALLKW2 WHERE TIMESTAMP=(select max(timestamp) from ALLKW2)";
$sqlGen4="SELECT S1N49_KW_VAL0 GEN4 FROM ALLKW3 WHERE TIMESTAMP=(select max(timestamp) from ALLKW3)";

$resGen1 = odbc_exec($connect, $sqlGen1);
$resGen2_3 = odbc_exec($connect, $sqlGen2_3);
$resGen4 = odbc_exec($connect, $sqlGen4);

$genU1 = number_format(odbc_result($resGen1,"GEN1"),2); 
$genU2 = number_format(odbc_result($resGen2_3,"GEN2"),2); 
$genU3 = number_format(odbc_result($resGen2_3,"GEN3"),2); 
$genU4 = number_format(odbc_result($resGen4,"GEN4"),2); 

//COAL FLOW
$sql = "SELECT * FROM ". $dbName .".tag000000007 order by DTime asc limit 1 ; ";
$coal1row = $conn->query($sql)->fetch_assoc();
$coal1 = $coal1row["PV"];
$sql = "SELECT * FROM ". $dbName .".tag000000008 order by DTime asc limit 1 ; ";
$coal2row = $conn->query($sql)->fetch_assoc();
$coal2 = $coal2row["PV"];
$sql = "SELECT * FROM ". $dbName .".tag000000009 order by DTime asc limit 1 ; ";
$coal3row = $conn->query($sql)->fetch_assoc();
$coal3 = $coal3row["PV"];
$sql = "SELECT * FROM ". $dbName .".tag000000010 order by DTime asc limit 1 ; ";
$coal4row = $conn->query($sql)->fetch_assoc();
$coal4 = $coal4row["PV"];

//actual generation
$act_gen = 0;
if($genU1 != 0){
    $act_gen += 210;
}
if ($genU2!= 0) {
    $act_gen += 210;
}
if($genU3 !=0) {
    $act_gen += 210; 
}
if ($genU4 != 0) {
   $act_gen +=210;
}
if($genU5 != 0){
    $act_genstation = $act_gen + 500;
}
$genU1toU4 = $genU1+$genU2+$genU3+$genU4;
$coal1to4 = $coal1 + $coal2 + $coal3 + $coal4;
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="mw display">
        <meta name="author" content="Nitesh">    
        <!--<META HTTP-EQUIV="Refresh" CONTENT="30" URL="http://192.168.10">-->
        <title>KPKD Generation</title>   
        <link href="css/responsive_css.css" rel="stylesheet">
    </head>

    <body>
        <div  style="overflow-x:auto;font-family: verdana" align="center">   
            <h1 style="margin: 1px"><u>KHTPS - Generation Overview</u></h1>
                <div>
                    <h3 >
                        <span style="background-color: #34495E;border-radius: 9px;">
                            <span id="curTime" style="color:#ffffff;font-style: normal;font-size: smaller;font-weight: bold;"> Current Time: <?php echo $DATETIME;?></span>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                    </h3> 
                </div>

                <table  id="genTable" class="responsive-table responsive-table-input-matrix" style="font-size:39px;width: 100%">
                    <thead>
                        <tr>
                            <th>UNIT</th>
                            <th>LOAD </th>
                            <th>%PLF </th>
                            <th>COAL FLOW</th>                            
                        </tr>
                    </thead>
                    <tbody>          
                        <tr >
                            <td  style="color:#ffc600;">UNIT-1</td>
                            <td> <?php echo number_format($genU1,2); ?></td>
                            <td> <?php echo number_format(($genU1/210)* 100,2) ?></td>
                            <td> <?php echo number_format($coal1,2); ?></td>
                        </tr>
                        <tr>
                            <td style="color:#ffc600;">UNIT-2</td>
                            <td><?php echo number_format($genU2,2); ?></td>
                            <td><?php echo number_format(($genU2/210)* 100,2) ?></td>
                            <td> <?php echo number_format($coal2,2); ?></td>                            
                        </tr>
                        <tr>
                            <td style="color:#ffc600;">UNIT-3</td>
                            <td><?php echo $genU3; ?></td>
                            <td><?php echo number_format(($genU3/210)* 100,2) ?></td>
                            <td> <?php echo number_format($coal3,2); ?></td>                            
                        </tr>
                        <tr>
                            <td style="color:#ffc600;" >UNIT-4</td>
                            <td><?php echo $genU4; ?></td>
                            <td><?php echo number_format(($genU4/210)* 100,2) ?></td>
                            <td> <?php echo number_format($coal4,2); ?></td>                            
                        </tr>
                        <tr style="color:#06FE49;">
                            <td>UNIT-1 TO 4</td>
                            <td><?php echo number_format($genU1toU4,2); ?></td>
                            <td><?php echo number_format(($genU1toU4/$act_gen)* 100,2) ?></td>
                            <td> <?php echo number_format($coal1to4,2); ?></td>                           
                        </tr>
                        <tr>
                            <td style="color:#ffc600;">UNIT-5</td>
                            <td><?php echo $genU5; ?></td>
                            <td><?php echo number_format(($genU5/500)* 100,2) ?></td>
                            <td> <?php echo number_format($coalU5,2); ?></td>                           
                        </tr>
                        <tr style="color:#06FE49;">
                            <td>STATION</td>
                            <td><?php echo number_format($genU1toU4+$genU5,2); ?></td>
                            <td><?php echo number_format((($genU1toU4+$genU5)/$act_genstation)* 100,2) ?></td>
                            <td> <?php echo number_format($coal1to4+$coalU5,2); ?></td>                           
                        </tr>                        
                    </tbody>
                </table>
        </div>    
    </body>

</html>
