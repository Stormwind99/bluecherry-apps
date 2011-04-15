<?php DEFINE('INDVR', true);

#libs
include("../lib/lib.php");  #common functions

$current_user = new user('id', $_SESSION['id']);
$current_user->checkAccessPermissions('admin');

class update{
	public $message;
	public $status;
	public $data;
	function __construct(){
		$this->message = CHANGES_FAIL;
		$mode = $_POST['mode']; unset($_POST['mode']);
		switch ($mode) {
			case 'global':	$this->updateGlobal(); break;
			case 'kick':	$this->kickUser(); break;
			case 'deleteIp': $this->deleteIp(); break;
			case 'access_device_list': $this->editAccessList(); break;
			case 'changeStateIp': $this->changeStateIp(); break;
			case 'FPS': $this->changeFPSRES('FPS'); break;
			case 'RES': $this->changeFPSRES('RES'); break;
			case 'deleteUser' : $this->status = $this->deleteUser(); break;
			case 'update': $this->update(); break;
			case 'update_control' : $this->update_control(); break;
			case 'newUser': $this->newUser(); break;
			case 'user': $this->updateUser(); break;
			case 'editIp': $this->editIp(); break;
			case 'changeState': $this->changeState(); break;
		}
	}
	#update functions will be moved to individual files after template/js update in beta7
	private function newUser(){
		$result = user::update($_POST, true);
		data::responseXml($result[0], $result[1]);
	}
	private function updateUser(){
		$result = user::update($_POST);
		data::responseXml($result[0], $result[1]);
	}
	private function editIp(){
		$id = intval($_POST['id']);
		$result = data::query("UPDATE Devices SET device='{$_POST['ipAddr']}|{$_POST['port']}|{$_POST['rtsp']}', mjpeg_path='{$_POST['mjpeg']}', rtsp_username='{$_POST['user']}', rtsp_password='{$_POST['pass']}' WHERE id={$id}", true);
		data::responseXml($result);
	}
	private	function update_control(){
		$id = intval($_POST['id']);
		$this_device = data::query("SELECT * FROM Devices INNER JOIN AvailableSources USING (device) WHERE Devices.id='$id'");
		bc_handle_get($this_device[0]['device'], $this_device[0]['driver']);
		if (isset($_POST['hue'])) { bc_set_control($bch, BC_CID_HUE, $_POST['hue']); };
		if (isset($_POST['saturation'])) { bc_set_control($bch, BC_CID_SATURATION, $_POST['saturation']); };
		if (isset($_POST['contrast'])) { bc_set_control($bch, BC_CID_CONTRAST, $_POST['contrast']); };
		if (isset($_POST['brightness'])) { bc_set_control($bch, BC_CID_BRIGHTNESS, $_POST['brightness']); };
		bc_handle_free($bch);
		$this->update();
	}
	private function update(){
		$table = $_POST['type']; unset($_POST['type']);
		$id = $_POST['id']; unset($_POST['id']);
		$query = data::formQueryFromArray('update', $table, $_POST, 'id', $id);
		data::responseXml(data::query($query, true));
	}
	private function deleteUser(){
		$id = intval($_POST['id']);
		if ($id!=$_SESSION['id']){
			$result = user::remove($id);
		} else {
			$result = false;
			$msg = DELETE_USER_SELF;
		}
		data::responseXml($result, $msg);
	}
	private function deleteIp(){
		$id = intval($_POST['id']);
		data::responseXml(ipCamera::remove($id));
	}
	private function changeStateIp(){
		$id = intval($_POST['id']);
		$camera = new ipCamera($id);
		data::responseXml($camera->changeState());
	}
	private function updateGlobal(){
		$status = true;
		foreach ($_POST as $parameter => $value){
			$status = (data::query("UPDATE GlobalSettings SET value='{$value}' WHERE parameter='{$parameter}'", true)) ? $status : false;
			
		}
		data::responseXml($status);
	}
	private function kickUser(){
		$result = user::kick($_POST['id']); 
		if ($result===true){
			$status = true;
			$result = '';
		} else {
			$status = false;
		}
		data::responseXml($status, $result);
	}
	private function editAccessList(){
		$status = data::query("UPDATE Users SET access_device_list='".trim($_POST['value'], ",")."' WHERE id='{$_POST['id']}'", true);
		data::responseXml($status);
	}
	function changeFPSRES($type){
		$id = intval($_POST['id']);
		$this_device = data::query("SELECT * FROM Devices LEFT OUTER JOIN AvailableSources USING (device) WHERE Devices.id='$id'");
		if ($type == 'RES'){ $res = explode('x', $_POST['value']); $res['x'] = intval($res[0]); $res['y'] = intval($res[1]); } else {
			$res['x'] = $this_device[0]['resolutionX']; $res['y'] = $this_device[0]['resolutionY']; 
		}
		$fps = ($type=='FPS') ? intval($_POST['value']) : (30/$this_device[0]['video_interval']);
		$resX = ($type=='RES') ? ($res['x']) : $this_device[0]['resolutionX'];
		
		$this_device[0]['req_fps'] = (($fps) * (($resX>=704) ? 4 : 1)) - ((30/$this_device[0]['video_interval']) * (($this_device[0]['resolutionX']>=704) ? 4 : 1));
		
		$container_card = new card($this_device[0]['card_id']);
		if ($this_device[0]['req_fps'] > $container_card->info['available_capacity']){
			$result = false;
			$message = ENABLE_DEVICE_NOTENOUGHCAP;
		} else {
			$result = data::query("UPDATE Devices SET video_interval='".intval(30/$fps)."', resolutionX='{$res['x']}', resolutionY='{$res['y']}' WHERE id='$id'", true);
			$container_card = new card($this_device[0]['card_id']);
			$this->data = $container_card->info['available_capacity'];
		}
		data::responseXml($result, $msg);
	}
	
	private function changeState(){
		$device = data::escapeString($_POST['id']);
		$this_device = data::query("SELECT * FROM AvailableSources LEFT OUTER JOIN Devices USING (device) WHERE AvailableSources.device='$device' ");
		if (!$this_device) {
			$result = false;
			data::responseXml($result);
			return;
		}
		$container_card = new card($this_device[0]['card_id']);
		if (!empty($this_device[0]['protocol'])){ //if the device is configured
			$this_device[0]['req_fps'] = (30/$this_device[0]['video_interval']) * (($this_device[0]['resolutionX']>=704) ? 4 : 1);
			if ($this_device[0]['disabled']){
				if ($this_device[0]['req_fps'] > $container_card->info['available_capacity']){
					$this->status = false;
					$this->message = ENABLE_DEVICE_NOTENOUGHCAP;
				} else {
					$result = data::query("UPDATE Devices SET disabled='0' WHERE device='{$this_device[0]['device']}'", true);
				}

			} else {
				$result = data::query("UPDATE Devices SET disabled='1' WHERE device='{$this_device[0]['device']}'", true);
			}
		} else {
			$ds = ($container_card->fps_available<2) ? 1 : 0;
			if ($container_card->info['encoding'] == 'notconfigured' || $container_card->info['encoding'] == 'NTSC'){
				$res['y']='240';
				$enc = 'NTSC';
			} else {
				$res['y'] = '288';
				$enc = 'PAL';
			}
			$card_info = explode('|', $this_device[0]['device']);
			$card_info[2]++;
			$result = data::query("INSERT INTO Devices (device_name, resolutionX, resolutionY, protocol, device, driver, video_interval, signal_type, disabled) VALUES ('Port {$card_info[2]} on Card {$this_device[0]['card_id']}', 352, {$res['y']}, 'V4L2', '{$this_device[0]['device']}', '{$this_device[0]['driver']}', 15, '{$enc}', '$ds')", true);
			if ($ds==1) { $this->status = 'INFO'; $this->message = NEW_DEV_NEFPS; };
		}
	}

}

$update = new update;
?>
