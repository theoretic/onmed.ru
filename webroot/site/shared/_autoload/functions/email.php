<?
/*
email wrapper for Processwire
AT
12.09.23
*/

function email( $data ) {

	$mail = \ProcessWire\wireMail();

	$data = (Object)$data;

	$mail
		->to($data->to)
		->subject($data->subject)
		->bodyHtml($data->html)
		;

	if($data->from) $mail->from($data->from);
	if($data->body) $mail->body($data->body);
	if($data->attachment) $mail->attachment($data->attachment);

	if ( $mail->send() ) {
		$result = [
			'success' => 1,
			];
		}
	else {
		$result = [
			'errors' => $mail->getResult()
			];
		}

	return (Object)$result;
}