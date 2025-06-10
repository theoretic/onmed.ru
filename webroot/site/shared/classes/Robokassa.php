<?
/*
Robokassa

settings->robokassa object

Параметры запроса: MerchantLogin, OutSum, InvId, Description и SignatureValue.
База для расчёта контрольной суммы: MerchantLogin:OutSum:InvId:Пароль#1 
где: Пароль#1  – это тот самый пароль, который Вы указали в Технических настройках.

Если Вы хотите передавать нам пользовательские параметры, например: Shp_login=Vasya и Shp_oplata=1, то база для расчёта контрольной суммы должна выглядеть так:

MerchantLogin:OutSum:InvId:Пароль#1:Shp_login=Vasya:Shp_oplata=1

https://docs.robokassa.ru/?_ga=2.202226321.167824674.1525786637-1237898267.1521542726&_gl=1*1rdyfdx*_ga*MTIwMjA1NzU4Nw..*_ga_01GQYDTSB3*MTY0MTEwNTgzNy40LjEuMTY0MTEwNjM4OC4w#6865
При использовании Receipt:

Формат параметра – json. Максимальное количество символов в запросе json, составляет 30000 символов.

Пример передаваемого значения параметра:

{
	"sno": "osn",
	"items": [
		{
			"name": "Название товара 1",
			"quantity": 1,
			"sum": 100,
			"payment_method": "full_payment",
			"payment_object": "commodity",
			"tax": "vat10"
		},
		{
			"name": "Название товара 2",
			"quantity": 3,
			"sum": 450,
			"payment_method": "full_prepayment",
			"payment_object": "excise",
			"tax": "vat120",
			"nomenclature_code": "04620034587217"
		}
	]
}

Параметр включается в контрольную подпись запроса (после номера счета магазина и после UserIp (Ip конечного пользователя)). Например: MerchantLogin:OutSum:InvId:Receipt:Пароль#1.

Внимание! Поле sum содержит полную стоимость за все единицы данного товара вместе, учитывая (если есть) скидки, бонусы и специальные/премиальные цены на отдельные единицы товара (например, третий товар за половину стоимости или за пять единиц товара скидка 12%).

Внимание! В связи с тем, что значение этого параметра может иметь большую длину, мы рекомендуем передавать запросы с этим параметром только методом POST, во избежание превышения максимальной длины строки запроса.

ВАЖНО! Значение параметра Receipt перед использованием в строке для подсчета контрольной суммы и отправкой его формой необходимо URL-кодировать.

ВАЖНО! Параметр Receipt не участвует в контрольной сумме, при уведомлении платежа ResultURL.

Пример кода:

// your registration data
$mrh_login = "test";      // your login here
$mrh_pass1 = "securepass1";   // merchant pass1 here

// order properties
$inv_id = 5;        // shop's invoice number
// (unique for shop's lifetime)
$inv_desc = "desc";   // invoice desc
$out_summ = "550";   // invoice summ

// receipt (JSON array -> urlencode)
$receipt = "%7B%22sno%22%3A%22osn%22%2C%22items%22%3A%5B%7B%22name%22%3A%22%D0%9D%D0%B0%D0%B7%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5+%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%B0+1%22%2C%22quantity%22%3A1%2C%22sum%22%3A100%2C%22payment_method%22%3A%22full_payment%22%2C%22payment_object%22%3A%22commodity%22%2C%22tax%22%3A%22vat10%22%7D%2C%7B%22name%22%3A%22%D0%9D%D0%B0%D0%B7%D0%B2%D0%B0%D0%BD%D0%B8%D0%B5+%D1%82%D0%BE%D0%B2%D0%B0%D1%80%D0%B0+2%22%2C%22quantity%22%3A3%2C%22sum%22%3A450%2C%22payment_method%22%3A%22full_prepayment%22%2C%22payment_object%22%3A%22excise%22%2C%22tax%22%3A%22vat120%22%2C%22nomenclature_code%22%3A%2204620034587217%22%7D%5D%7D";
$receipt_urlencode = urlencode($receipt);

// build CRC value
$crc = md5("$mrh_login:$out_summ:$inv_id:$receipt:$mrh_pass1");

// build URL
$url = "https://auth.robokassa.ru/Merchant/Index.aspx?MerchantLogin=$mrh_login&" .
    "OutSum=$out_summ&InvId=$inv_id&Description=$inv_desc&Receipt=$receipt_urlencode&SignatureValue=$crc";

// print URL if you need
echo "<a href='$url'>Payment link</a>";

AT
14.08.23
*/

//namespace Robokassa;

class Robokassa {
	public
		$data,
		$settings
		;

	function __construct($data,$settings) {
		$this->data = $data;
		$this->settings = $settings;

		//mode
		if( $this->settings->test_mode == 1 ){
			$this->settings->password1 = $this->settings->test_password1;
			$this->settings->password2 = $this->settings->test_password2;
		}

		//defaults
		$this->settings->hash_algorythm = $this->settings->hash_algorythm? : 'md5';
	}

	public function makeRequestCRC() {
		$params = [
			'mandatory' => [
				'MerchantLogin'			=> $this->settings->login,
				'OutSum'				=> $this->data['OutSum'],
				'InvId'					=> $this->data['InvId'],
				'Receipt'				=> $this->data['Receipt'],
				'password1'				=> $this->settings->password1,
			],
			'optional' => [
				'owner'					=> $this->data['owner'],
				'service'				=> $this->data['service'],
				'uid'					=> $this->data['uid'],
			]
		];

		return $this->makeCRC($params);
	}

	public function makeResultCRC() {
		$params = [
			'mandatory' => [
				'OutSum'				=> $this->data['OutSum'],
				'InvId'					=> $this->data['InvId'],
				'password1'				=> $this->settings->password1,
			],
			'optional' => [
				'owner'					=> $this->data['owner'],
				'service'				=> $this->data['service'],
				'uid'					=> $this->data['uid'],
			]
		];

		return $this->makeCRC($params);
	}

	private function makeCRC($params) {

		ksort($params['optional']);
		$crc = [];

		//1. processing mandatory params
		foreach( $params['mandatory'] as $name=>$value ) {
			if(!$value) continue;

			switch( $name ){

				case 'Receipt':
				if( !is_string($value) ) {
					$value = json_encode($value);
					$value = urlencode($value);//?? not sure it's necessary
				}
				break;

			}

			$crc[] = $value;

		}

		//2. processing optional params
		sort($params['optional']);
		foreach( $params['optional'] as $name=>$value ) {
			if(!$value) continue;
			$value = urlencode($value);
			$crc[] = "shp_{$name}=$value";
		}

		$crc = implode(':',$crc);
//echo "crc: -$crc-<br>";//
		$crc = hash($this->settings->hash_algorythm, $crc);
//echo "crc hash: -$crc-<br>";//
		return $crc;

	}

}