<? if( $settings->medflex?->appointment_url ): ?>
<a href="<?=$sanitizer->entities($settings->medflex?->appointment_url)?>" class="simple white flex flex-middle margin-xs button" target="_blank" rel="noopener">
	Запись на услугу
</a>
<? endif ?>
