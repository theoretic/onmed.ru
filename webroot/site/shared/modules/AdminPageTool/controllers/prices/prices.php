<?
/*
Admin page tool prices controller
AT
01.11.23
*/

$selector[] = "template=offer,prices.count>0,include=all";
$selector[] = 'sort=title';
$selector = implode(',',$selector);
$pagesWithPrices = $this->pages->find($selector);

if( $_REQUEST['action'] ) include "$controllerPath/_include/actions/{$_REQUEST['action']}.php";

$output = $this->files->render( "$templatesPath/prices.php", ['this_'=>$this, 'pagesWithPrices'=>$pagesWithPrices] );

return $output;