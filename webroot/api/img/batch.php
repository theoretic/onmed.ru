<?
/*

*/

//error_reporting(0);

$currentDir=dirname(__FILE__);
$batchDir="$currentDir/.batch";

//finding the last file

//$batchFile=array_pop(sort(glob("$batchDir/*.batch")));
$batchFiles=glob("$batchDir/*.batch");
sort($batchFiles);
$batchFile=array_pop($batchFiles);

if(!is_file($batchFile)) die('no batch file.');

$command="cd $currentDir".PHP_EOL;
$command.=implode(PHP_EOL,file($batchFile));


$result=`$command`;
//echo "--$command-- <br><br>";
//echo "--$result--<br><br>";

//logging
$fp=fopen('.log','w+');
fwrite($fp,$command);
fclose($fp);

//unlink($batchFile);

//if($_SERVER['HTTP_USER_AGENT']) header("refresh:5;url=/assets/images/batch.php");

echo "file $batchFile processed.";

?>