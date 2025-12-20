<?
/*
branch
json+ld
AT
08.09.25
*/
?>

<script type="application/ld+json">
{
"@context": "http://schema.org/",
"@type": "Organization",
"name": "<?=$settings->requisites->company_fullname?>",
"address": {
	"@type": "PostalAddress",
	"streetAddress": "<?=$settings->contacts->address?>",
	"addressLocality": "<?=$settings->contacts->city?>",
	"addressRegion": "<?=$settings->contacts->region?>",
	"postalCode": "<?=$settings->contacts->postal_code?>"
},
"telephone": "<?=$settings->contacts->phone?>",
"email": "<?=$settings->contacts->email?>"
}
</script>