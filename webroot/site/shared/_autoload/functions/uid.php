<?
/*
AT
28.07.16
*/

function uid()
	{
	$length = 12;
	$uid = md5(microtime());
	$pos = rand( 0, strlen($uid)-$length );
	$uid = substr($uid,$pos,$length);
	//randomizing case -- no need if $length >=6
	/*
	$letters = str_split($uid);
	foreach($letters as $i=>$letter) $letters[$i] = rand(0,1)? strtoupper($letter) : $letter;
	$uid = implode('',$letters);
	*/
	return $uid;
}