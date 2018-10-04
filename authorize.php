<?php
session_start();
define( "DEBUG", false );
define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );
require( ROOT . '/sys/core.php' );
require( ROOT . '/sys/core.user.php' );
$pdo = create_PDO();
$user = new User( $pdo );

if(isset($_POST["login_submit"])) {
	echo $user->login($pdo, $_POST["mail"], $_POST["password"])->text;
}
if(isset($_POST["register_submit"])) {
	echo $user->register($_POST["mail"], $_POST["password"]);
}
header('Location: /', true, 307);
exit;
?>