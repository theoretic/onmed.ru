<?
/*
Appointment email template
AT
28.10.24
*/
?>

<?=$input->post->full_name?> хочет записаться на <?=$offerPage->title?>.
<br>
Контактная информация:
<ul>
<li>Email: <?=$input->post->email?></li>
<li>Тел.: <?=$input->post->phone?></li>
</ul>
Комментарии: <br>
<?=$input->post->comment?><br>