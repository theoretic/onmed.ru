<?
/*
SEO redirects of kind /path/to/page -> /page
Disabled )))
AT
20.11.23

if( $input->urlSegment(-1) ) {
	//trying to display a page different from branch page
	//$pageCandidate = $pages->get("has_parent={$branchPage},name={$input->urlSegment(-1)}");
	$pageCandidate = $pages->get("name={$input->urlSegment(-1)}");

	if( $pageCandidate instanceof ProcessWireNullPage ){
		$errorPage = $pages->get("name=http404");
		$session->redirect($errorPage->url); 
	}

	$session->redirect($pageCandidate->url); 
}
*/