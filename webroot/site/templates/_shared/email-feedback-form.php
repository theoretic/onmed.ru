<?
/*
Feedback form
AT
03.09.25
*/
?>

<form
	id="email-feedback-form"
	data-validator="/api/validator/mail/email-feedback"
	data-action="/api/mail/email-feedback"
	data-event="emailFeedbackSent"
	>
	<div class="flex flex-top flex-wrap">
		<div class="required field">
			<label>
				Ваше имя
				<span></span>
				<input type="text" name="name" required placeholder="Александр Кузнецов">
			</label>
		</div>
		<div class="required field">
			<label>
				Ваш email
				<span></span>
				<input type="email" name="email" required placeholder="name@server.com">
			</label>
		</div>
		<div class="required field">
			<label>
				Ваш телефон
				<span></span>
				<input type="phone" name="phone" required placeholder="8 916 123 45 67">
				</label>
		</div>
		<div class="required field flex5">
			<label>
				Ваше сообщение
				<span></span>
				<textarea name="message" required placeholder="Доктор, Вы кудесник!"></textarea>
				</label>
		</div>
	</div>
	<div class="flex">
		<div>
			<? include '_shared/personal-data-consent.php' ?>
		</div>
		<div class="right-aligned flex-end">
			<button class="L">Отправить</button>
		</div>
	</div>
</form>

<div id="email-feedback-success-message" class="hidden centered padded success message">
	<h2>Спасибо!</h2>
	<p class="L">
		Мы постараемся среагировать на Ваше сообщение как можно быстрее.
	</p>
</div>

<div id="email-feedback-error-message" class="hidden centered padded error message">
	<h2>Кажется, у нас что-то сломалось.</h2>
	<p class="L">
		Попробуйте обновить страницу и отправить эту форму ещё раз. Если не поможет -- свяжитесь с нами любым другим удобным для Вас способом. И да, простите за неудобство!
	</p>
</div>