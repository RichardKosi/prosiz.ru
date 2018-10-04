<?php
define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
require( ROOT . '/sys/core.php' );
require( ROOT . '/sys/core.user.php' );

$pdo = create_PDO();
$user = new User( $pdo );

try {
	$vert = $pdo->query( 'SELECT id, label, x, y, size FROM vertex' );
	$edge = $pdo->query( 'SELECT id, source, target FROM edges' );
	$check = $pdo->query( "SELECT nodeid FROM chduv WHERE userid=$user->id" );
	$obj = array(
		"nodes" => $vert->fetchAll(),
		"edges" => $edge->fetchAll(),
		"check" => $check->fetchAll(),
		"settings" => parse_ini_file(ROOT.'/graph.settings.ini')
	);
	print( json_encode( $obj ) );
} catch ( Exception $e ) {
	header( "HTTP/1.1 500 Internal Server Error" );
	exit;
}
?>