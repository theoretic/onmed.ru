<?
/*
onLeaveModal modal
AT
03.09.25
*/

$onLeavePage = $pages->get("name=modal-onleave");

?>

<div
	id="modal-onleave"
	class="absolute dimmed w100 h100 hidden modal flex flex-center flex-middle"
	>


	<? if($onLeavePage->image->url): ?>
		<img data-src="<?=$onLeavePage->image->url?>"/>
	<? endif ?>

	<div
		class="relative padded card"
<?/*
		<? if($onLeavePage->background->url): ?>
			data-back="<?=$onLeavePage->background->url?>"
		<? endif ?>
*/?>
		>

			<a class="close" data-modal-close>✕</a>
			<h2>
				<?=$onLeavePage->longtitle?>
			</h2>

			<p>
				<?=$onLeavePage->summary?>
			</p>

			<form
				id="callback-form"
				data-validator="/api/validator/callback"
				data-action="/api/callback"
				data-method="post"
				data-messaging="html"
				>

				<div class="messaging-error hidden error message"></div>
				<div class="messaging-success hidden success message"></div>
				<div class="messaging-message hidden warning message"></div>

				<fieldset class="flex flex-top flex-wrap">
					<div class="required field">
						<label>
							Ваше имя <span class="error"></span>
							<input type="text" name="author" required placeholder="Александр Кузнецов">
						</label>
					</div>
					<div class="required field">
						<label>
							Ваш телефон <span class="error"></span>
							<input type="phone" name="phone" required placeholder="8 916 123 45 67">
						</label>
					</div>
				</fieldset>

				<div class="flex flex-middle">
					<div>
						<? include '_shared/personal-data-consent.php' ?>
					</div>
					<div class="right-aligned flex-end">
						<button class="L">Перезвоните мне!</button>
					</div>
				</div>
			</form>

	</div>

</div>