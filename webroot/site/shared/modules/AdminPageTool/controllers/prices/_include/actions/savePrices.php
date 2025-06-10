<?
/*
Admin page tool
save current page
AT
01.11.23
*/

$pageWithPrices = $this->pages->get("id={$_POST['pageWithPrices']}");

$newPriceItem = false;

foreach( $_POST as $name=>$values ){
	if($name == 'pageWithPrices') continue;

	foreach( $values as $id=>$value){

		$priceItems[$id] = $priceItems[$id]? : $pageWithPrices->prices->get("id=$id");

		//checking whether to create a new price item
		//it should contain at least title and price
		if( $priceItems[$id] == NULL && !$newPriceItem && ( $_POST['title'][$id] && $_POST['price'][$id] ) ){
			$newPriceItem = $pageWithPrices->prices->getNew();
			$pageWithPrices->prices->add($newPriceItem);
			$pageWithPrices->save('prices');
			$priceItems[$newPriceItem->id] = $newPriceItem;
		}

		//setting id = new price item id for new price item
		$id = $newPriceItem->id? : $id;

		if( $priceItems[$id] == NULL ) continue;

		$priceItems[$id]->$name = $value;
	}
}

foreach( $priceItems as $id=>$priceItem){
if( $priceItem == NULL ) continue;
	$priceItem->of(false);
	$priceItem->save();
	$priceItem->of(true);
}

$this->message("Сохранены цены для страницы {$pageWithPrices->title}.");