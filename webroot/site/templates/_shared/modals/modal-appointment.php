<?
/*
Appointment modal
AT
03.09.25
*/
?>

<div class="absolute hidden modal" id="modal-appointment">
	<a class="close" data-modal-close>✕</a>
	<div class="narrow padded container">
		<form
			id="appointment-form"
			data-validator="/api/validator/appointment"
			data-action="/api/appointment"
			data-method="post"
			data-event="appointmentSent"
			data-messaging="html"
			>

			<h2 class="half-padded">Запишитесь на приём</h2>

			<div class="form-messages">
				<div class="hidden centered padded success message">
					<h2>Спасибо!</h2>
					<p class="L">
						Мы постараемся среагировать на Ваше сообщение как можно быстрее.
					</p>
				</div>

				<div class="hidden centered padded error message">
					<h2>Кажется, у нас что-то сломалось.</h2>
					<p class="L">
						Попробуйте обновить страницу и отправить эту форму ещё раз. Если не поможет -- свяжитесь с нами любым другим удобным для Вас способом. И да, простите за неудобство!
					</p>
				</div>
			</div>

			<div class="flex">
				<div class="required field">
					<label>Ваше имя
						<span class="error"></span>
						<input type="text" name="fullname" required placeholder="Александр Кузнецов">
					</label>
				</div>
				<div class="required field">
					<label>Ваш телефон
						<span class="error"></span>
						<input type="phone" name="phone" required placeholder="8 916 123 45 67">
					</label>
				</div>

				<!--
				<div class="field">
					<label>Услуга</label>
					<select name="offer">
						<option></option>>
						<? foreach( $offerPages as $offerPage ): ?>
							<option>
								<?=$offerPage->title?>
							</option>
						<? endforeach ?>
					</select>
				</div>
				<div class="field">
					<label>Специалист</label>
					<select name="specialist" >
						<option></option>
						<? foreach( $specialistPages as $specialistPage ): ?>
							<option>
								<?=$specialistPage->title?>
								<?=$specialistPage->firstname?>
							</option>
						<? endforeach ?>
					</select>
				</div>
				-->
			</div>

			<div class="required field">
				<label>Ваше сообщение
					<span class="error"></span>
					<textarea rows=5 name="message" required placeholder="Хочу записаться на приём. Спасибо!"></textarea>
				</label>
			</div>

			<div class="flex flex-middle">
				<div>
					<? include '_shared/personal-data/consent.php' ?>
				</div>
				<div class="flex-end">
					<button class="L">Записаться</button>
				</div>
			</div>

		</form>

	</div>
</div>