<?
/*
Request
AT
15.02.22
*/

class Request{

	public
		$protocol,
		$headers,
		$host,
		$url,
		$acceptedImageFormats
		;

	function __construct(){
		$this->getProtocol();
		$this->getHeaders();
		$this->getHost();
		$this->getURL();
		$this->getAcceptedImageFormats();
	}

////

	private function getProtocol(){
		$this->protocol = 'http';

		if (
			isset($_SERVER['HTTPS'])
			&& ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 )
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
			&& $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
		) {
			$this->protocol = 'https';
		}

		return $this->protocol;
	}

	private function getHost() {
		$this->host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
		return $this->host;
	}

	private function getURL() {
		if( !$this->protocol ) return null;
		if( !$this->host ) return null;
		$this->url = "{$this->protocol}://{$this->host}";
		return $this->url;
	}

	private function getHeaders(){
		 $this->headers = [];

		foreach($_SERVER as $key => $value) {
			if (substr($key, 0, 5) <> 'HTTP_') continue;

			$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
			$this->headers[$header] = $value;
		}
		return $this->headers;
	}

	private function getAcceptedImageFormats(){
//Accept header format: «text/html, application/xhtml+xml, application/xml;q=0.9,image/avif, image/webp,*/*;q=0.8»
		$this->acceptedImageFormats = [];

		$parts = explode(';',$this->headers['Accept']);
		foreach( $parts as $part ){
			if( !strstr($part,'image' ) ) continue;
			$subparts = explode(',',$part);
			foreach( $subparts as $subpart ){
				if( !strstr($subpart,'image' ) ) continue;
				$subpart = trim($subpart);
				$subpart = str_replace ( 'image/', '', $subpart );
				$this->acceptedImageFormats[] = $subpart;
			}
		}
	}

}