<?
/*
ProcessAdminPageTool prices table
AT
27.11.23
*/

$samplePageWithPrices = $pagesWithPrices->first();
$samplePriceItem = $samplePageWithPrices->prices->first();

?>

<style>
	.isHidden{
		opacity:.5;
	}
	.isUnpublished{
		opacity:.25;
	}

	td:first-child{
		width:60%;
		min-width:36rem;
	}

</style>

<p class="">
Enter в любом поле ввода сохраняет цены для соответствующей страницы.
</p>

<? foreach($pagesWithPrices as $pageWithPrices): ?>
	<?
	$cssClasses = [];
	if ( $pageWithPrices->isUnpublished() ) $cssClasses[] = 'isUnpublished';
	if ( $pageWithPrices->isHidden() ) $cssClasses[] = 'isHidden';
	?>
	<div class="<?=implode(' ', $cssClasses)?> uk-card uk-card-default uk-card-body">
		<form method="post">

			<h3 class="uk-flex uk-flex-middle uk-flex-between">
				<div>
					<?=$pageWithPrices->title?>
					<a href="/backend/page/edit/?id=<?=$pageWithPrices->id?>" target=_blank>
						✐
					</a>
					<a href="<?=$pageWithPrices->url?>" target=_blank>
						🔗
					</a>
				</div>
				<button class="uk-button uk-button-main uk-float-right">
					сохранить
				</button>
			</h3>

			<input type="hidden" name="action" value="savePrices">
			<input type="hidden" name="pageWithPrices" value="<?=$pageWithPrices->id?>">

			<table data-sortable class='uk-table uk-table-divider uk-table-small uk-table-hover uk-table-justify uk-table-responsive'>
				<thead>
					<tr>
					<? foreach( $samplePriceItem->template->fields as $field ): ?>
						<td><?=$field->label?></td>
					<? endforeach ?>
						<td></td>
					</tr>
				</thead>
				<tbody>
				<? foreach( $pageWithPrices->prices as $priceItem): ?>
					<tr>
					<? foreach( $samplePriceItem->template->fields as $field ): ?>
						<td>
							<input type="text" name="<?=$field->name?>[<?=$priceItem->id?>]" value="<?=addslashes( strip_tags($priceItem->{$field->name}) )?>" class="uk-input"/>
						</td>
					<? endforeach ?>
						<td>
							<a href="?action=removePrice&pageWithPrices=<?=$pageWithPrices->id?>&priceItem=<?=$priceItem->id?>" class="uk-text-large" onclick="return confirm('Точно удалить?')">
								⨯
							</a>
						</td>
					</tr>
				<? endforeach ?>

					<?/* new price item */?>
					<tr>
					<? foreach( $samplePriceItem->template->fields as $field ): ?>
						<td>
							<input type="text" name="<?=$field->name?>[]" class="uk-input"/>
						</td>
					<? endforeach ?>
						<td></td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
	<br>
<? endforeach ?>