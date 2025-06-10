<?
/*
Yandex YML offer

Оффер для врача - это связка "Врач"-"Клиника"-"Тип приема".
При наличии у врача нескольких офферов нужно предоставить каждый из них отдельно.
В таком случае URL разных офферов одного врача должны различаться незначащим cgi-параметром ?offer-id=...

В фиде нужно передавать данные о тех врачах, по которым нужно выводить информацию в поиске. У врачей может не быть отзывов, тогда тег <param name="Число отзывов"> по таким врачам указывать не нужно.

В фиде нужно передавать только те отзывы, которые есть на сайте клиники.

Также для корректной обработки фида нужно убрать из специальностей слово «детский». Информацию о том, с какой возрастной категорией работает врач, нужно передавать в офферах в 1 или обоих тегах: param name="Взрослый врач" и param name="Детский врач", которые принимают значения true или false.

AT
16.05.25
*/

$maxAllowedFeedbacks = 250;

//specializations
$acceptsChildren = $specialistPage->parent("/deti")->id? 'true' : 'false';
$acceptsAdults = $acceptsChildren == 'false'? 'true' : 'false';

$specializations = [];

foreach( $specialistPage->specializations as $specializationPage ){
	if( !isYandexSpecialization($specializationPage->title) ) continue;
	$specializations[] = trim($specializationPage->ymlName);
}

if( count($specializations)==0 ) return;

$specializations = implode( ',', $specializations );
$specializations = trim($specializations);//

//link
//archimedURLs can be rejected by Yandex!
//$link = $specialistPage->archimedURL? : "https://{$_SERVER['HTTP_HOST']}{$specialistPage->url}";
$link = "https://{$_SERVER['HTTP_HOST']}{$specialistPage->url}";
if( !strstr($link,'https://') ) $link = 'https:'.$link;
$link = htmlspecialchars($link);

//echo '$specialistPage: ', var_dump($specialistPage);//
$feedbackPages = $pages->find("template=feedback,has_parent=$specialistPage,sort=-published");
//echo '$feedbackPages: ', var_dump($feedbackPages);//
$feedbacksTotal = count($feedbackPages);
//echo '$feedbacksTotal: ', var_dump($feedbacksTotal);//

$feedbacksCounter = 0;

?>

<offer id="vrach<?=$specialistPage->id?>" group_id="<?=$specialistPage->id?>">

	<!--ФИО, именно в таком порядке-->
	<name>
		<?=$specialistPage->title?>
		<?=$specialistPage->firstname?>
		<?=$specialistPage->patronymic?>
	</name>

	<!-- URL карточки врача -->
	<!-- URL может совпадать ТОЛЬКО для офферов с одинаковым group_id -->
	<url>
		https://<?=$_SERVER['HTTP_HOST']?><?=$specialistPage->url?>
	</url>

	<!-- Цена приема при записи в источнике фида, с учетом скидки -->
	<? if($specialistPage->price): ?>
		<price from="true">
			<?=$specialistPage->price?>
		</price>
	<? endif ?>

	<!--Цена приема в клинике, должна быть не ниже price-->
	<? if($specialistPage->oldprice): ?>
		<oldprice>
			<?=$specialistPage->oldprice?>
		</oldprice>
	<? endif ?>

	<currencyId>RUR</currencyId>

	<sales_notes>Первичный прием</sales_notes>

	<!--специальности через запятую-->
	<set-ids><?=$specializations?></set-ids>

	<? if( $specialistPage->image->url ): ?>
		<picture>
			https://<?=$_SERVER['HTTP_HOST']?><?=$specialistPage->image->url?>
		</picture>
	<? endif ?>

	<description>
		<? if( $specialistPage->summary ): ?>
				<?=$specialistPage->summary?>.
		<? endif ?>
		<? if( $specialistPage->skills ): ?>
			<?=strip_tags($specialistPage->skills)?>
		<? endif ?>
	</description>

	<categoryId>1</categoryId>

	<!-- Пустых блоков param быть не должно. Если необязательное поле не заполнено, его нужно исключить из фида -->

	<param name="Фамилия">
		<?=$specialistPage->title?>
	</param>

	<? if( $specialistPage->firstname ): ?>
		<param name="Имя">
			<?=$specialistPage->firstname?>
		</param>
	<? endif ?>

	<? if( $specialistPage->patronymic ): ?>
		<param name="Отчество">
			<?=$specialistPage->patronymic?>
		</param>
	<? endif ?>

	<? if( $specialistPage->year_since ): ?>
		<param name="Годы опыта">
			<?=date('Y') - (int)$specialistPage->year_since?>
		</param>
		<!--Дата начала карьеры, для подсчета стажа-->
		<param name="Начало карьеры">
			<?=$specialistPage->year_since?>-01-01
		</param>
	<? endif ?>

	<param name="Город">г. Москва</param>

	<!--Принимает взрослых, от 18 лет-->
	<param name="Взрослый врач">
		<?=$acceptsAdults?>
	</param>

	<!--Принимает детей, до 18 лет-->
	<param name="Детский врач">
		<?=$acceptsChildren?>
	</param>

	<? if( mb_stristr($specialistPage->summary, 'кмн' ) || mb_stristr($specialistPage->summary, 'кандидат' ) ): ?>
		<param name="Степень">Кандидат наук</param>
	<? endif ?>

	<!-- Средняя оценка ПОЛЬЗОВАТЕЛЕЙ. Профессиональный рейтинг сюда не входит -->
	<?/* mandatory field before 2025 */?>
<?/*
	<? if( $specialistPage->rating ): ?>
		<param name="Средняя оценка">
			<?=(float)$specialistPage->rating? : 5?>
		</param>
	<? endif ?>

	<!-- Общее число отзывов, доступных по URL данного врача -->
	<? if( $feedbacksTotal>0 ): ?>
		<param name="число отзывов">
			<?=$feedbacksTotal?>
		</param>
	<? endif ?>
*/?>

	<!--Профессиональный рейтинг на основе стажа и т.п.-->
<?/*
	<param name="Профессиональный рейтинг">5.0</param>
	<param name="Степень">Кандидат наук</param>
	<param name="Звание">Профессор</param>
	<param name="Категория">Вторая категория</param>
*/?>

<?/*
	<param name="конверсия">
		1
	</param>
*/?>

	<param name="Ссылка на профиль врача">
		<?=$link?>
	</param>

<?/*
	<param name="регион">г. Москва</param>
*/?>
	<param name="Город клиники">г. Москва</param>
	<param name="Адрес клиники">
		<?=$settings->contacts->address?>
	</param>

	<param name="Название клиники">
		<?=$settings->requisites->company_name?>
	</param>
	<!-- Возможность записаться на прием через сайт-поставщик фида -->
	<param name="Возможность записи">true</param>

	<param name="Онлайн-расписание">false</param>

	<param name="Телефон для записи">
		<?=$settings->contacts->phone?>
	</param>

<?/*
	<param name="Образование - 1" unit="Организация">Астраханская государственная медицинская академия</param>
	<param name="Образование - 1" unit="Дата">2006</param>
	<param name="Образование - 1" unit="Название">Базовое образование</param>
	<param name="Образование - 1" unit="Специальность">Педиатрия</param>
	<param name="Образование - 2" unit="Организация">Ростовский государственный медицинский университет</param>
	<param name="Образование - 2" unit="Дата">2011</param>
	<param name="Образование - 2" unit="Название">Курсы переподготовки</param>
	<param name="Образование - 2" unit="Специальность">Ультразвуковая диагностика</param>
*/?>
	<param name="Место работы - 1" unit="Организация">Клиника Онмед</param>
<?/*
	<param name="Место работы - 1" unit="Дата">2010-н.в.</param>
	<param name="Место работы - 2" unit="Организация">Детская городская больница г. Москвы</param>
	<param name="Место работы - 2" unit="Дата">2007-2009</param>
	<param name="Место работы - 2" unit="Название">Врач-педиатр</param>
	<param name="Место работы - 3" unit="Организация">Клиника «ЕМС»</param>
	<param name="Место работы - 3" unit="Дата">2009-н.в.</param>
	<param name="Место работы - 3" unit="Название">Заведующий отделением педиатрии</param>
	<param name="Сертификат - 1" unit="Организация">
		Национальный медико-хирургический центр им. Н.И. Пирогова
	</param>
	<param name="Сертификат - 1" unit="Дата">2013</param>
	<param name="Сертификат - 1" unit="Название">
		Ультразвуковая диагностика заболеваний сосудов нижних конечностей
	</param>
*/?>

	<!--Семпл отзывов. Не более $maxAllowedFeedbacks штук-->
	<? foreach( $feedbackPages as $feedbackPage ): ?>
		<?
		if( ++$feedbacksCounter > $maxAllowedFeedbacks ) break;
		?>
		<param name="Отзыв - <?=$feedbacksCounter?>" unit="Автор">
			<?=$feedbackPage->author?>
		</param>
		<param name="Отзыв - <?=$feedbacksCounter?>" unit="Дата">
			<?=date( 'd.m.Y H:i:s', $feedbackPage->created )?>
		</param>
		<param name="Отзыв - <?=$feedbacksCounter?>" unit="Отзыв проверен">true</param>
		<!-- Есть подтверждение записи ко врачу + отзыв прошел модерацию -->
		<param name="Отзыв - <?=$feedbacksCounter?>" unit="Отзыв участвует в рейтинге">true</param>
		<param name="Отзыв - <?=$feedbacksCounter?>" unit="Оценка">
			<?=$feedbackPage->rating?>
		</param>
		<param name="Отзыв - <?=$feedbacksCounter?>" unit="Понравилось">
			<?=$feedbackPage->summary?>
		</param>
	<? endforeach ?>

<?/*
	<!--Оценка пользователем, от 1 до 5-->
	<param name="Отзыв - 1" unit="Понравилось">Доктор замечательный, помог мне</param>
	<param name="Отзыв - 1" unit="Не понравилось">Общался не очень вежливо</param>
	<param name="Отзыв - 1" unit="Комментарий">Долго ждать в регистратуре</param>
	<param name="Отзыв - 1" unit="Ответ">Спасибо за отзыв!</param>
	<!--Ответ клиники-->
	<param name="Отзыв - 2" unit="Автор">Аноним</param>
	<param name="Отзыв - 2" unit="Дата">12.03.2020 14:28:00</param>
	<param name="Отзыв - 2" unit="Отзыв проверен">false</param>
	<param name="Отзыв - 2" unit="Отзыв участвует в рейтинге">false</param>
	<param name="Отзыв - 2" unit="Оценка">1</param>
	<!-- Пустых param быть НЕ должно. У этого отзыва нет полей "Понравилось" и "Ответ", поэтому не указываем их в фиде -->
	<param name="Отзыв - 2" unit="Не понравилось">Ужасно!!!!!!!</param>
	<param name="Отзыв - 2" unit="Комментарий">Худший врач</param>
*/?>
</offer>