<?php
session_start();
define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );
require( ROOT . '/sys/core.php' );
require( ROOT . '/sys/core.user.php' );

$pdo = create_PDO();
$user = new User( $pdo );

if ( $user->id == 0 ) {
	$UserLabel = "Авторизоваться";
} elseif ( $user->type == 255 ) {
	$UserLabel = "admin:  " . $user->email;
} else {
	$UserLabel = "user:   " . $user->email;
};
require( ROOT . '/Graph.php' );
?>