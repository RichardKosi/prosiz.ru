<?php
define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
require( ROOT . '/sys/core.php' );
require( ROOT . '/sys/core.user.php' );

$pdo = create_PDO();
$user = new User( $pdo );

if ( $user->id == 0 ) {
	header( "HTTP/1.1 401 Unauthorized" );
	exit;
} else {
	$obj = json_decode( file_get_contents( "php://input" ) );
	if ( isset( $obj ) ) {
		if ( isset( $obj->id ) ) {
			try {
				$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
				$pdo->beginTransaction();
				$PDOS = $pdo->prepare( 'DELETE FROM chduv WHERE userid=:userid AND nodeid=:nodeid' );
				$PDOS->execute( array( ':userid' => $user->id, ':nodeid' => $obj->id ) );
				if ( $PDOS->rowCount() == 0 ) {
					header( "HTTP/1.1 422 Unprocessable Entity" );
					exit;
				}
				$pdo->commit();
				header( "HTTP/1.1 204 No Content" );
				exit;
			} catch ( Exception $e ) {
				$pdo->rollBack();
				header( "HTTP/1.1 500 Internal Server Error" );
				exit;
			}
		} else {
			header( "HTTP/1.1 400 Bad Request" );
			exit;
		}
	}
}
?>