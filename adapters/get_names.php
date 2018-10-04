<?php
define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );
require( ROOT . '/sys/core.php' );
$pdo = create_PDO();

$request = json_decode( file_get_contents( "php://input" ) );
$am = count($request);
$str = '?';
$str2 = ' OR ?';
for($i = 1; $i < $am; $i++) $str .= $str2;
for($i = 0; $i < $am; $i++) $request[$i] = "%$request[$i]%";
try {
	$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	$pdo->beginTransaction();
	$PDOS = $pdo->prepare( 'SELECT label, id 
							FROM vertex WHERE label LIKE '.$str );
	$PDOS->execute( $request );
	$pdo->commit();
	print( json_encode( $PDOS->fetchAll() ) );
} catch ( Exception $e ) {
	$pdo->rollBack();
	header( "HTTP/1.1 500 Internal Server Error" );
	exit;
}
?>




