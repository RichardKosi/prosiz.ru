<?php
class err_ret {
	public $number = 0;
	public $text = "";
	
	function __construct($Number, $Text){
		$this->number = $Number;
		$this->text = $Text;
	}
}
function create_PDO(){
	$config = parse_ini_file(ROOT.'/sys/config.ini');
	$dsn = "{$config['driver']}:host={$config['host']};dbname={$config['DB']};charset={$config['charset']}";
	return new PDO(
		$dsn,
		$config['user'],
		$config['password'],
		array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
    }
function clearInt($num){
	return abs((int)$num);
    }

function clearStr($str){
	return trim(strip_tags($str));
    }

function clearHTML($html){
	return trim(htmlspecialchars($html));
    }
?>
