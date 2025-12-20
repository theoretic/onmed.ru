<?
/*
Feedback form
AT
03.09.25
*/
?>

<form
	id="feedback-form"
	data-validator="/api/validator/feedback"
	data-action="/api/feedback"
	data-method="post"
	data-messaging="html"
	>

	<div class="messaging-error hidden error message"></div>
	<div class="messaging-success hidden success message"></div>
	<div class="messaging-message hidden warning message"></div>

	<div class="flex">
		<label class="required field">
			Ваше имя
			<span class="error"></span>
			<input type="text" name="author" required placeholder="Александр Кузнецов">
		</label>
		<label class="required field">
			Ваш email
			<span class="error"></span>
			<input type="email" name="email" required placeholder="name@server.com">
		</label>
		<label class="required field">
			Ваш телефон
			<span class="error"></span>
			<input
				type="phone"
				name="phone"
				required
				placeholder="+7 (916) 123-45-67"
				data-mask='+{7} (000) 000-00-00'
				>
		</label>
	</div>

	<div class="flex flex-top flex-wrap">
		<label class="required field">
			Специалист, у которого Вы были
			<? if($page->template->name == 'specialist'): ?>
				<input type="hidden" name="specialist" value="<?=$page->id?>"/>
				<div class="half-v-padded">
					<?=$page->firstname?>
					<?=$page->title?>
				</div>
			<? else: ?>
				<span class="error"></span>
				<select name="specialist" >
					<option></option>
					<? foreach( $specialistPages as $specialistPage ): ?>
						<?
							$directionPage = $specialistPage->closest("template=branch");
						?>
						<option value="<?=$specialistPage->id?>">
							<?=$specialistPage->title?>
							<?=$specialistPage->firstname?>
							(<?=$directionPage->title?>)
						</option>
					<? endforeach ?>
				</select>
			<? endif ?>
		</label>
		<label class="required field">
			Ваше сообщение
			<span class="error"></span>
			<textarea name="summary" rows=5 required placeholder="Всё очень понравилось!"></textarea>
		</label>
		<label class="required field">
			Ваша оценка
			<span class="error"></span>
			<!--
			<input type="number" name="rating" required min=1 max=5 value="5" class="centered">
			-->
			<div class="starsInput flex flex-middle">
			<? for( $i=1; $i<=5; $i++ ): ?>
				<label class="star">
					<input type=radio name=rating value=<?=$i?>>
				</label>
			<? endfor ?>
			</div>
		</label>
	</div>

	<div class="half-padded flex flex-middle">
		<div>
			<? include '_shared/personal-data-consent.php' ?>
		</div>
		<div class="right-aligned flex-end">
			<button class="L">Отправить</button>
		</div>
	</div>

</form>

<div id="feedback-success-message" class="hidden centered padded success message">
	<h2>Спасибо!</h2>
	<p class="L">
		Ваше сообщение будет опубликовано на сайте после модерации.
	</p>
</div>

<div id="feedback-error-message" class="hidden centered padded error message">
	<h2>Кажется, у нас что-то сломалось.</h2>
	<p class="L">
		Попробуйте обновить страницу и отправить эту форму ещё раз. Если не поможет -- свяжитесь с нами любым другим удобным для Вас способом. И да, простите за неудобство!
	</p>
</div>

<? $pageHasFeedbackForm=true ?>