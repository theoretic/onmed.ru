<?
/*
deletes css files of given $pattern
AT
04.06.18
*/

$patterns = [
	"./*/.cache/.hash",
	"./*/.cache/*.css",
	];

function rglob($pattern, $flags = 0)
	{
	$files = glob($pattern, $flags); 
	foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
		$files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
	return $files;
	}

foreach($patterns as $pattern)
	{

	$files = rglob($pattern);
	//var_dump($files);//

	foreach ($files as $file)
		{
		echo "removing $file...\n";
		unlink($file);
		}
	}

echo 'done.';