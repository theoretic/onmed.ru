<?
/*
Feedbacks
AT
12.02.25
*/

$feedbackPages = $feedbackPages? : $page->children("template=feedback");

?>

<section id="feedbacks" class="flex flex-gap">
<? foreach( $feedbackPages as $feedbackPage ): ?>
	<?
	$specialistPage = $feedbackPage->parents("template=specialist")->first();
	?>
	<div class="padded w100 feedback card">
		<div class="flex flex-space-between flex-middle">
			<? if($feedbackPage->rating): ?>
				<div class="stars">
					<?= str_repeat( "&starf;", (int)$feedbackPage->rating ) ?>
				</div>
			<? endif ?>
			<noindex>
			<div class="feedback-date">
				<?=$feedbackPage->genericDate?>
			</div>
			</noindex>
		</div>
		<br>
		<p class="ML">
			<?=$feedbackPage->summary?>
		</p>
		<? if($feedbackPage->body): ?>
			<p>
				<?=$feedbackPage->body?>
			</p>
		<? endif ?>
		<div class="flex flex-space-between flex-middle">
			<div class="feedback-specialists flex flex-center flex-middle">
				<a href="<?=$specialistPage->url?>" class="half-padded feedback-specialist flex flex-middle">
					<? if( $specialistPage->image->url ): ?>
						<img class="feedback-specialist-avatar" data-src="<?=$specialistPage->image->url?>" data-aspect="1:1">
					<? endif ?>
					<?=$specialistPage->firstname?>
					<?=$specialistPage->title?>
				</a>
			</div>
			<div class="flex-end right-aligned feedback-author">
				<? if($feedbackPage->author): ?>
					<?=$feedbackPage->author?><br>
				<? endif ?>
			</div>
		</div>
	</div>
<? endforeach ?>
</section>