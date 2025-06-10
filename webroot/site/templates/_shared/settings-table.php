<?
/*
Settings table
AT
18.02.25
*/

//$settingName = ;

//getting settings file
$rawSettings = include "{$_SERVER['DOCUMENT_ROOT']}/site/templates/_shared/settings/{$settingName}.php";
?>

<table>
<? foreach($rawSettings as $rawSetting): ?>
	<?
	$rawSetting = (Object)$rawSetting;
	?>
	<tr>
		<td>
			<?=$rawSetting->label?>
		</td>
		<td>
			<?=$settings->$settingName->{$rawSetting->name}?>
		</td>
	</tr>
<? endforeach ?>
</table>