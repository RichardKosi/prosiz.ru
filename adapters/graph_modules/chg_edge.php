<?php
define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );
require( ROOT . '/sys/core.php' );
require( ROOT . '/sys/core.user.php' );
class BaseVertex {
	public $id = null;
	public $label = null;
}
class BaseLink {
	public $id = null;
	public $source = null;
	public $target = null;

	function __construct() {
		$this->source = new BaseVertex();
		$this->target = new BaseVertex();
	}
}
$pdo = create_PDO();
$user = new User( $pdo );
$debug = true;
if ( $user->id == 0 ) print( 'Log in' );
elseif ( $user->type != 255 ) print( 'Not admin' );
else {
	$request = json_decode( file_get_contents( "php://input" ) );
	if ( isset( $request ) ) {
		$obj = get_object_vars( $request );
		$ret = array();
		$ERR = "errors";
		$CV = "changevertex";
		if ( isset( $obj[ $CV ] ) ) {
			$index = 0;
			foreach ( $obj[ $CV ] as $value ) {
				try {
					$pdo->setAttribute( 
						PDO::ATTR_ERRMODE, 
						PDO::ERRMODE_EXCEPTION );
					$pdo->beginTransaction();
					$ret[ $DL ][ $index ] = new StdClass();
					$ret[ $DL ][ $index ]->Rdata = new BaseLink();
					$ret[ $DL ][ $index ]->Sdata = $value;
					if ( isset( $value->id ) ) {
						$PDOS = $pdo->prepare( 'UPDATE vertex
						SET label=:label, x=:x, y=:y, size=:size
						WHERE id=:id' );
						$PDOS->execute( array( ':label' => $value->label,
											  ':x' => $value->x,
											  ':y' => $value->y,
											  ':size' => $value->size,
											  ':id' => $value->id ) );
					} else {} //Если искать по названию
					$pdo->commit();
					$ret[ $DL ][ $index ]->Rdata->label; //
				} catch ( Exception $e ) {
					$pdo->rollBack();
					$ret[ $DL ][ $index ]->errors[] = "Transaction error";
				}
				$index++;
			};
		}
		$CL = "changelink";
		if ( isset( $obj[ $CL ] ) ) {
			$index = 0;
			$exit = false;
			foreach ( $obj[ $CL ] as $value ) {
				try {
					$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$pdo->beginTransaction();
					$ret[ $CL ][ $index ] = new StdClass();
					$ret[ $CL ][ $index ]->Rdata = new BaseLink();
					$ret[ $CL ][ $index ]->Sdata = $value;
					if ( isset( $value->id ) ) {
						$PDOS = $pdo->prepare( 'UPDATE edges SET source=:src, target=:trg WHERE id=:id' );
						$PDOS->execute( array( ':src' => $value->new->source, ':trg' => $value->new->target, ':id' => $value->id ) );
					} else {
						if ( isset( $value->old->source->id ) ) { //TODO
						} elseif ( isset( $value->old->source->label ) ) { //TODO
						}
						else {
							$ret[ $CL ][ $index ]->errors[] = "Not set source id or source label or link id";
							$exit = true;
						}
						if ( isset( $value->old->target->id ) ) { //TODO
						} elseif ( isset( $value->old->target->label ) ) { //TODO
						}
						else {
							$ret[ $CL ][ $index ]->errors[] = "Not set target id or target label or link id";
							$exit = true;
						}
					}
					if ( !$exit ) {
						$pdo->commit();
						$ret[ $CL ][ $index ]->Rdata->id = $value->id;
					} else {
						$pdo->rollBack();
						$ret[ $CL ][ $index ]->errors[] = "Not set source/target id or source/target label or link id";

					}
				} catch ( Exception $e ) {
					$pdo->rollBack();
					$ret[ $CL ][ $index ]->errors[] = "Transaction error";
				}
				$index++;
			}
		}
		$DV = "deletevertex";
		if ( isset( $obj[ $DV ] ) ) {
			$index = 0;
			foreach ( $obj[ $DV ] as $value ) {
				try {
					$pdo->setAttribute( 
						PDO::ATTR_ERRMODE, 
						PDO::ERRMODE_EXCEPTION );
					$pdo->beginTransaction();
					$ret[ $DV ][ $index ] = new StdClass();
					$ret[ $DV ][ $index ]->Rdata = new BaseVertex();
					$ret[ $DV ][ $index ]->Sdata = $value;
					if ( isset( $value->id ) ) {
						$PDOS = $pdo->prepare( '
						DELETE FROM edges WHERE target=:id OR source=:id' );
						$PDOS->execute( array( ':id' => $value->id ) );
						$PDOS = $pdo->prepare( '
						DELETE FROM vertex WHERE id=:id' );
						$PDOS->execute( array( ':id' => $value->id ) );
						$ret[ $DV ][ $index ]->Rdata->id = $value->id;
					} elseif ( isset( $value->label ) ) {
						$PDOS = $pdo->prepare( '
						SELECT id FROM vertex WHERE label=:label' );
						$PDOS->execute( array( ':label' => $value->label ) );
						$id = $PDOS->fetch()['id'];
						$PDOS = $pdo->prepare( '
						DELETE FROM edges WHERE target=:id OR source=:id' );
						$PDOS->execute( array( ':id' => $id ) );
						$PDOS = $pdo->prepare( '
						DELETE FROM vertex WHERE id=:id' );
						$PDOS->execute( array( ':id' => $id ) );
						$ret[ $DV ][ $index ]->Rdata->id = $id;
						$ret[ $DV ][ $index ]->Rdata->label = $value->label;
					}
					else {
						$ret[ $DV ][ $index ]->errors[] = "Not set id or label";
					}
					$pdo->commit();
				} catch ( Exception $e ) {
					$pdo->rollBack();
					$ret[ $DV ][ $index ]->errors[] = "Transaction error";
				}
				$index++;
			};
		}
		$DL = "deletelink";
		if ( isset( $obj[ $DL ] ) ) {
			$index = 0;
			foreach ( $obj[ $DL ] as $value ) {
				try {
					$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$pdo->beginTransaction();
					$ret[ $DL ][ $index ] = new StdClass();
					$ret[ $DL ][ $index ]->Rdata = new BaseLink();
					$ret[ $DL ][ $index ]->Sdata = $value;
					if ( isset( $value->id ) ) {
						$PDOS = $pdo->prepare( 'DELETE FROM edges WHERE id=:id' );
						$PDOS->execute( array( ':id' => $value->id ) );
					} else {} //Если по названиям или по source id и target id
					$pdo->commit();
					$ret[ $DL ][ $index ]->Rdata->id = $value->id;
				} catch ( Exception $e ) {
					$pdo->rollBack();
					$ret[ $DL ][ $index ]->errors[] = "Transaction error";
				}
				$index++;
			};
		}
		$AV = "addvertex";
		if ( isset( $obj[ $AV ] ) ) {
			$index = 0;
			foreach ( $obj[ $AV ] as $value ) {
				try {
					$pdo->setAttribute(
						PDO::ATTR_ERRMODE,
						PDO::ERRMODE_EXCEPTION );
					$pdo->beginTransaction();
					$ret[ $AV ][ $index ] = new StdClass();
					$ret[ $AV ][ $index ]->Rdata = new BaseVertex();
					$ret[ $AV ][ $index ]->Sdata = $value;
					$PDOS = $pdo->prepare( '
					INSERT INTO vertex (label, x, y, size)
					VALUES (:label,:x,:y,:size)' );
					$PDOS->execute( array( ':label' => $value->label,
										  ':x' => $value->x,
										  ':y' => $value->y,
										  ':size' => $value->size ) );
					$PDOS = $pdo->prepare( '
					SELECT id FROM vertex WHERE label=:label' );
					$PDOS->execute( array( ':label' => $value->label ) );
					$id = $PDOS->fetch()[ 'id' ];
					$pdo->commit();
					$ret[ $AV ][ $index ]->Rdata->id = $id;
					$ret[ $AV ][ $index ]->Rdata->label = $value->label;
				} catch ( Exception $e ) {
					$pdo->rollBack();
					$ret[ $AV ][ $index ]->errors[] = "Transaction error";
				}
				$index++;
			};
		}
		$AL = "addlink";
		if ( isset( $obj[ $AL ] ) ) {
			$index = 0;
			foreach ( $obj[ $AL ] as $value ) {
				try {
					$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
					$pdo->beginTransaction();
					$ret[ $AL ][ $index ] = new StdClass();
					$ret[ $AL ][ $index ]->Rdata = new BaseLink();
					$ret[ $AL ][ $index ]->Sdata = $value;
					if ( !isset( $value->source->id ) ) {
						$PDOS = $pdo->prepare( 'SELECT id FROM vertex WHERE label LIKE :label' );
						$PDOS->execute( array( ':label' => $value->source->label ) );
						if ( $PDOS && $PDOS->rowCount() == 1 ) {
							$_source = $PDOS->fetch()[ 'id' ];
							$ret[ $AL ][ $index ]->Rdata->source->id = $_source;
						} else {
							$ret[ $AL ][ $index ]->errors[] = "Source duplicate in DB";
						}
					} else {
						$ret[ $AL ][ $index ]->Rdata->source->id = $_source = $value->source->id;
					}
					if ( !isset( $value->target->id ) ) {
						$PDOS = $pdo->prepare( 'SELECT id FROM vertex WHERE label LIKE :label' );
						$PDOS->execute( array( ':label' => $value->target->label ) );
						if ( $PDOS && $PDOS->rowCount() == 1 ) {
							$_target = $PDOS->fetch()[ 'id' ];
							$ret[ $AL ][ $index ]->Rdata->target->id = $_target;
						} else {
							$ret[ $AL ][ $index ]->errors[] = "Target duplicate in DB";
						}
					} else {
						$ret[ $AL ][ $index ]->Rdata->target->id = $_target = $value->target->id;
					}
					$PDOS = $pdo->prepare( 'INSERT INTO edges(source, target) VALUES (:source, :target)' );
					$PDOS->execute( array( ':source' => $_source, ':target' => $_target ) );
					$PDOS = $pdo->prepare( 'SELECT id FROM edges WHERE source=:source AND target=:target' );
					$PDOS->execute( array( ':source' => $_source, ':target' => $_target ) );
					$RC = $PDOS->rowCount();
					$id = $PDOS->fetchAll();
					if ( $RC > 1 ) {
						$pdo->rollBack();
						$ret[ $AL ][ $index ]->errors[] = "Link duplicate in DB";
						$ret[ $AL ][ $index ]->errors[] = $id;
					} else {
						$pdo->commit();
						$ret[ $AL ][ $index ]->Rdata->id = $id[ 0 ][ 'id' ];
					}
				} catch ( Exception $e ) {
					$pdo->rollBack();
					$ret[ $AL ][ $index ]->errors[] = "Transaction error";
				}
				$index++;
			}
		}
		print( json_encode( $ret ) );
	}
}
?>