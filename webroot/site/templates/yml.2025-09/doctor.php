<?
/*
Yandex YML doctor
https://yandex.ru/support/webmaster/ru/search-appearance/doctors.html

<doctor id="doctor_1">
  <name>Иванов Иван Иванович</name>
  <url>https://example.ru/doctors/2246852315/</url>
  <description>Высококвалифицированный специалист - универсал со множеством специализаций!</description>
  <internal_id>2246852315</internal_id>
  <first_name>Иван</first_name>
  <surname>Иванов</surname>
  <patronymic>Иванович</patronymic>
  <experience_years>9</experience_years>
  <career_start_date>2015-01-01</career_start_date>
  <degree>доктор медицинских наук</degree>
  <rank>Профессор</rank>
  <category>Первая</category>
  <education>
    <organization>Медицинский университет Реавиз</organization>
    <finish_year>2010</finish_year>
    <type>Специалитет</type>
    <specialization>Лечебное дело (Лечебно-профилактическое дело)</specialization>
  </education>
  <job>
    <organization>Яндекс.Врачи</organization>
    <period_years>2010-н.в.</period_years>
    <position>Терапевт</position>
  </job>
  <certificate>
    <organization>Московский институт психоанализа</organization>
    <finish_year>2020</finish_year>
    <name>Лечебная физкультура и спортивная медицина</name>
  </certificate>
  <reviews_total_count>100</reviews_total_count>
  <review>
    <date>2024-12-07 09:00:24</date>
    <checked>true</checked>
    <used_in_rating>true</used_in_rating>
    <author>Наталья</author> 
    <author_id>natalia123</author_id>
    <author_picture>https://example.ru/reviews/natalia123.png</author_picture>
    <url>https://example.ru/doctors/2246852315/reviews</url>
    <comment>Долго ждать в регистратуре</comment>
    <grade>4.5</grade>
    <positive>Что-то, что понравилось</positive>
    <negative>Что-то, что не понравилось</negative>
    <response>Спасибо за отзыв!</response>
  </review>
</doctor>

AT
02.09.25
*/

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

//YML 2.0 fields

$feedbacksCounter = 0;
$year_since = $specialistPage->year_since? : 1999;
$experience_years = date('Y') - $year_since;

/*
it is supposed that educations are like this:
<ul>
	<li>2015г. Первый МГМУ им.И.М. Сеченова&nbsp;по специальности «Стоматология».</li>
	<li>2016г. Интернатура по специальности «Стоматология общей практики», Первый МГМУ им. И.М.Сеченова.</li>
	<li>2018г.&nbsp;Ординатура по специальности «Ортодонтия», МГМСУ им. А.И.Евдокимова.</li>
</ul>

*/
$educations = [];
if( strpos($specialistPage->education,'<ul>') !== false ){
	$education = str_ireplace( ['<ul>','</ul>'], '', $specialistPage->education );
	$parts = explode( '<li>', $education );
	foreach( $parts as $i=>$part )
		$part = str_ireplace('</li>', '', $part);
		$part = strip_tags($part);
		$part = str_replace('&nbsp;', '', $part);

		$subparts = explode(' ', $part);
		$year = array_shift($subparts);
		$year = str_replace('г.', '', $year);
		$year = trim($year);
		$organization = implode(' ', $subparts);
		$organization = str_replace('г.', '', $organization);
		$organization = trim($organization);

		$educations[] = (Object)[
			'year'				=> $year,
			'organization'		=> $organization,
		];

}

$reviewsCounter = 0;

?>

<doctor id="doctor_<?=$specialistPage->id?>">

	<name>
		<?=$specialistPage->title?>
		<?=$specialistPage->firstname?>
		<?=$specialistPage->patronymic?>
	</name>

	<url>https://<?=$_SERVER['HTTP_HOST']?><?=$specialistPage->url?></url>

<!--
	<description>Высококвалифицированный специалист</description>
-->

	<internal_id><?=$specialistPage->id?></internal_id>

	<first_name><?=$specialistPage->firstname?></first_name>
	<surname><?=$specialistPage->title?></surname>
	<patronymic><?=$specialistPage->patronymic?></patronymic>

	<experience_years><?=$experience_years?></experience_years>

	<career_start_date><?=$year_since?>-01-01</career_start_date>

	<? if( mb_stristr($specialistPage->summary, 'кмн' ) || mb_stristr($specialistPage->summary, 'кандидат' ) ): ?>
		<degree>Кандидат медицинских наук</degree>
	<? endif ?>

<!--
	<rank>Профессор</rank>
	<category>Первая</category>
-->

	<education>
		<? foreach( $educations as $education ): ?>
			<organization><?=strip_tags($education->organization)?></organization>
			<finish_year><?=$education->year?></finish_year>
			<type>Специалитет</type>
			<specialization><?=strip_tags($education->organization)?></specialization>
		<? endforeach ?>
	</education>

<!--
	<job>
		<organization>Яндекс.Врачи</organization>
		<period_years>2010-н.в.</period_years>
		<position>Терапевт</position>
	</job>
	<certificate>
		<organization>Московский институт психоанализа</organization>
		<finish_year>2020</finish_year>
		<name>Лечебная физкультура и спортивная медицина</name>
	</certificate>
-->

	<reviews_total_count><?=$feedbacksTotal?></reviews_total_count>

	<? foreach($feedbackPages as $feedbackPage): ?>
		<?
		if( ++$reviewsCounter > $maxReviews ) break;
		?>
		<review>
			<date><?=date('Y-m-d H:i:s', $feedbackPage->created)?></date>
			<checked>true</checked>
			<used_in_rating>true</used_in_rating>
			<author><?=$feedbackPage->author?></author> 
			<author_id><?=$feedbackPage->id?></author_id>
			<!--
			<author_picture>https://example.ru/reviews/natalia123.png</author_picture>
			-->
			<url>https://<?=$_SERVER['HTTP_HOST']?><?=$feedbackPage->path?></url>
			<comment><?=strip_tags($feedbackPage->summary)?></comment>
			<? if($feedbackPage->rating): ?>
				<grade><?=(float)$feedbackPage->rating?></grade>
			<? endif ?>
			<!--
			<positive>Что-то, что понравилось</positive>
			<negative>Что-то, что не понравилось</negative>
			<response>Спасибо за отзыв!</response>
			-->
		</review>
	<? endforeach ?>


</doctor>