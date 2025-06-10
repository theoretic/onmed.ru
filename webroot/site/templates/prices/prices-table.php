<?
/*
prices table
AT
10.03.25
*/

//$now = time();
//$hasDiscounts = false;

$pageWithPrices = $pageWithPrices? : $page;

$priceItems = $priceItems? : $pageWithPrices->prices;
$samplePriceItem = $samplePriceItem? : $priceItems->first();

//echo '$pageWithPrices: ', var_dump($pageWithPrices);//

$deprecatedPricesTableFields = [
	'is_action',
	'code',
	'oldprice',
	//'units',
];

$testPriceItems = [];

//echo '$priceItems: ', var_dump($priceItems);//

?>

<table>

	<thead>
		<? foreach( $samplePriceItem->template->fields as $field ): ?>
			<?
			if( in_array($field->name,$deprecatedPricesTableFields) ) continue;
			?>
			<td><?=$field->label?></td>
		<? endforeach ?>
	</thead>

	<? foreach( $priceItems as $priceItem ): ?>
		<?
		//$discountPage = $pages->findOne("template=discount, date_start<$now, date_end>$now, offers.id={$priceItem->id}, discount|price>0, sort=discount");
		//$discountedPrice = $discountPage->discount? round( $priceItem->price * (1-$discountPage->discount/100), 2 ) : false;
		//if( in_array( $priceItem->title, $testPriceItems ) ) continue;
		//$testPriceItems[] = $priceItem->title;
		?>
		<tr>
			<? foreach( $samplePriceItem->template->fields as $field ): ?>
				<?
				if( in_array($field->name,$deprecatedPricesTableFields) ) continue;

				switch($field->name){
					case 'title':
?>
<td>
<!-- <?=$priceItem->id?>&nbsp; -->
<? if($priceItem->is_action): ?>
		<span class="green badge">
			акция
		</span>
	<? endif ?>
	<?=$priceItem->title?>
</td>
<?
					break;

					case 'price':
?>
<td class="price" nowrap>
	<? if($priceItem->oldprice): ?>
		<span class="oldprice">
			<?=$priceItem->oldprice?>
		</span>
	<? endif ?>
	<?=$priceItem->price?> ₽
</td>
<?
					break;

					case 'comment':
?>
<td class="comment">
	<?=$priceItem->comment?>
</td>
<?
					break;

					default:
?>
<td>
<?=$priceItem->{$field->name}?>
</td>
<?
					}
					?>
			<? endforeach ?>
		</tr>
	<? endforeach ?>
</table>

<!--
<? if( $config->disclaimer !== false ): ?>
	<p class="padded centered comment">
		<?=$settings->general->discounts?>
	</p>
<? endif ?>
-->

<?
unset(
	$config,
	$priceItems,
	$pageWithPrices
);
?>