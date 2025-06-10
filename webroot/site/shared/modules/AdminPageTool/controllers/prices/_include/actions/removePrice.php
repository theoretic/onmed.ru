<?
/*
Admin page tool
remove price item
AT
01.11.23
*/

$pageWithPrices = $this->pages->get("id={$_GET['pageWithPrices']}");
$priceItem = $pageWithPrices->prices->get("id={$_GET['priceItem']}");

$pageWithPrices->prices->remove($priceItem);
$pageWithPrices->save('prices');

$this->message("Цена удалена.");