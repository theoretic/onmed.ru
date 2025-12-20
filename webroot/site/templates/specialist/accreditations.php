<?
/*
Specialist
accreditations
AT
04.09.25
*/

$specialistPage = $specialistPage? : $page;

?>

<table class="w100 accreditations">
	<thead>
		<tr>
			<th>
				специальность
			</th>
			<th>
				дата получения
			</th>
			<th>
				действует до
			</th>
		</tr>
	</thead>
	<tbody>
	<? foreach( $specialistPage->accreditations as $accreditationItem ): ?>
		<tr>
			<td>
				<?=$accreditationItem->title?>
			</td>
			<td>
				<?=$accreditationItem->formattedDateStart?>
			</td>
			<td>
				<?=$accreditationItem->formattedDateEnd?>
			</td>
		</tr>
	<? endforeach ?>
	</tbody>
</table>