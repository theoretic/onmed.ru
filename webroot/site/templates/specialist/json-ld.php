<?
/*
specialist
json+ld
AT
08.09.25
*/
?>


<script type="application/ld+json">
{
"@context": "http://schema.org/",
"@type": "Person",
"name": "<?=$page->lastname?> <?=$page->firstname?> <?=$page->patronymic?> ",
<? if($page->job_title->title): ?>
	"jobTitle": "<?=$page->job_title->title?>",
<? endif ?>
"address": {
	"@type": "PostalAddress",
	"addressLocality": "<?=$settings->contacts->city?>",
	"addressRegion": "<?=$settings->contacts->region?>",
}
}
</script>