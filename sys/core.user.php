<?php
class User {
	public $authorized = false;
	public $cookie = false;
	public $cookie_time = 2592000;
	public $type = 0;
	public $id = 0;
	public $email = 0;
	public $agent = 0;
	public $pdo;
	public $err = 99;
	public $NumberOfVisits = 0;

	function __construct( $PDO ) {
		$this->pdo = $PDO;
		$this->agent = $_SERVER[ 'HTTP_USER_AGENT' ];
		if ( !( $this->err = $this->cookie_check( $this->pdo ) ) )$this->cookie = true;
		if ( $this->err == 0 )$this->err = $this->load( $_COOKIE[ 'id' ] );
		else { // Если была ошибка при авторизации то считаем пользователя не авторизованным
			if ( $stmt = $this->pdo->query( "SELECT id, NumberOfVisits FROM usertbl WHERE IP='" . $_SERVER[ 'REMOTE_ADDR' ] . "' AND status=0" )) { // Если база не вернула false
					if ( !$row = $stmt->fetch( PDO::FETCH_ASSOC ) ) // Если база не вернула пустой ответ добавляем запись
					{
						$this->pdo->exec( "
						INSERT INTO usertbl (IP, NumberOfVisits, LastVisit)
						VALUES ('" . $_SERVER[ 'REMOTE_ADDR' ] . "', 1, '" . date( "Y-m-d H:i:s" ) . "')" );
					} else { // Если база вернула запись, обновляем
						$this->NumberOfVisits = $row['NumberOfVisits'] + 1;
						$this->pdo->exec( "
					UPDATE usertbl
					SET NumberOfVisits='$this->NumberOfVisits',
					LastVisit='" . date( "Y-m-d H:i:s" ) . "'
					WHERE IP='" . $_SERVER[ 'REMOTE_ADDR' ] . "' AND status=0" );
					}
				}
			}
		}
		// 0: Авторизован успешно
		// 5: Ошибка в БД
		// 6: Нет такого id
		private

		function load( $ID ) {
			if ( !$stmt = $this->pdo->query( "SELECT email, usertype, NumberOfVisits FROM usertbl WHERE id='$ID'" ) ) return 5; // Если база вернула false возвращаем ошибку
			elseif ( !$row = $stmt->fetch( PDO::FETCH_ASSOC ) ) return 6; // Если база вернула пустой ответ возвращаем ошибку
			else {
				$this->id = $ID;
				$this->email = $row[ 'email' ];
				$this->type = $row[ 'usertype' ];
				$this->authorized = true;
				$this->NumberOfVisits = $row[ 'NumberOfVisits' ] + 1;
				$this->pdo->exec( "
				UPDATE usertbl
				SET NumberOfVisits='$this->NumberOfVisits',
				IP='" . $_SERVER[ 'REMOTE_ADDR' ] . "',
				LastVisit='" . date( "Y-m-d H:i:s" ) . "'
				WHERE id='$ID'" );
				return 0;
			};
		}

		// 0: Авторизован успешно
		// 1: hash не совпал
		// 2: Нет такого id
		// 3: Ошибка в БД
		// 4: Пустые куки
		private

		function cookie_check() {
			if ( isset( $_COOKIE[ 'id' ] ) && isset( $_COOKIE[ 'hash' ] ) ) {
				$cookie_id = $_COOKIE[ 'id' ];
				$cookie_hash = $_COOKIE[ 'hash' ];
				if ( empty( $cookie_id ) || empty( $cookie_hash ) ) return 4; // Если куки id или куки hash пустые возвращаем ошибку
				elseif ( !$stmt = $this->pdo->query( "SELECT hash FROM usertbl WHERE id='$cookie_id'" ) ) return 3; // Если база вернула false возвращаем ошибку
				elseif ( !$row = $stmt->fetch( PDO::FETCH_ASSOC ) ) return 2; // Если база вернула пустой ответ возвращаем ошибку
				elseif ( $cookie_hash == ( $db_hash = $row[ 'hash' ] ) ) {
						return 0;
					} // Если hash совпадает записываем все о юзере
				else return 1; // Если hash не совпадает возвращаем ошибку
			} else return 9;
		}

		// 0: Зарегистрирован успешно
		// 1: Учетная запись уже существует
		// 2: Ошибка в БД
		public

		function register( $email, $password ) {
			$password = md5( $password ); // Шифруем пароль
			// TODO: Проверить правильность мыла регулярным выражением
			$PDOS = $this->pdo->prepare('SELECT COUNT(id) FROM usertbl WHERE email=:email');
			$PDOS->execute( array(':email' => $email ) );
			$row = $PDOS->fetch( PDO::FETCH_NUM );
			$PDOS = $this->pdo->prepare('INSERT INTO usertbl (email, password, status, usertype) VALUES (?, ?, 1, 1)');
			if ( $row[ 0 ] > 0 ) return 1; // Учетная запись уже существует. Забыл пароль?
			elseif ( $PDOS->execute( array($email, $password )) ) return 0;
			else return 2;
		}

		// 0: Залогинился успешно
		// 1: Неверный пароль
		// 2: Ошибка в БД
		function login( $pdo, $mail, $password ) {
			$password = md5( $password );
			$PDOS = $this->pdo->prepare('SELECT id, password FROM usertbl WHERE email=:mail');
			$PDOS->execute( array(':mail' => $mail ) );
			if ( !$PDOS->execute( array(':mail' => $mail ) ) )
				return new err_ret( 4, "BD error" );
			else {
				$row = $PDOS->fetch( PDO::FETCH_ASSOC );
				if ( !$row ) return new err_ret( 3, "Email not found" );
				else {
					$db_password = $row[ 'password' ];
					$db_id = $row[ 'id' ];
					if ( $password == $db_password ) {
						$hash = md5( rand( 0, 6400000 ) );
						$PDOS = $this->pdo->prepare('UPDATE usertbl SET hash=:hash WHERE id=:db_id');
						if ( $PDOS->execute( array(':hash' => $hash, ':db_id' => $db_id ) ) ) {
							setcookie( "id", $db_id, time() + $this->cookie_time );
							setcookie( "hash", $hash, time() + $this->cookie_time );
							return new err_ret( 0, "No error" );
						} else return new err_ret( 2, "err" );;
					}
					return new err_ret( 1, "err" );
				}
			}
		}
	}
	?>