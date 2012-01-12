<?php defined('INDVR') or exit(); 

#ACTi control class
class RTSP_ACTI {//extends ipCameraControl{
	const URL_BASE = 'http://%RTSP_USERNAME%:%RTSP_PASSWORD%@%IP%:%PORT%/%FILE%?%OPTIONS%';
	public function __construct($info){
		$this->info = $info;
	}
	private function construct_url($command){
		return str_replace(
							array('%IP%', '%PORT%', '%RTSP_USERNAME%', '%RTSP_PASSWORD%', '%FILE%', '%OPTIONS%',), 
							array($this->info['ipAddr'], $this->info['portMjpeg'], $this->info['rtsp_username'], $this->info['rtsp_password'], $command['file'], $command['options']),
							self::URL_BASE
		);
	}
	private function send_command($command){
		return @file_get_contents($command);
	}
	private function check_response($response){
		#if response is false -- connection failed
		if (!$response) return array(false, COUND_NOT_CONNECT);
		#if not, check whether change was successful or not
		$std_resp = preg_match("/(OK|ERROR):(.*)/", $response, $response);
		if ($std_resp){
			if ($response[1] == 'OK') { return array(true, ''); }
				elseif ($response[1] == 'ERROR') { return array(false, $response[2]); };
		} else {
			return array(false, COUND_NOT_CONNECT);
		}
	}
	
	public function set_streaming_method($type){
		$command['file'] = 'cgi-bin/cmd/system';
		$command['options'] = 'V2_STREAMING_METHOD='.intval($type);
		$command = $this->construct_url($command);
		return $this->check_response($this->send_command($command));
	}
	public function get_streaming_method(){
	}
}
?>
