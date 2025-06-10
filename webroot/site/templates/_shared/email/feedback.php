<?
/*
AT
16.11.23
*/
?>

Поступил новый отзыв.

Автор: <?=$input->post->author?><br>
Email: <?=$input->post->email?><br>
Тел.: <?=$input->post->phone?><br>
Сообщение: <br>
<?=$input->post->summary?><br>

Просмотр и редактирование -- <a href="<?=$_SERVER['HTTP_HOST']?>/backend/page/edit/?id=<?=$newFeedbackPage->id?>">здесь</a>.