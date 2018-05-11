<?php
	class user {
		function register($login, $password, $password_1, $email, $invite) {
			global $config;
			global $ip;
			if(empty($login)) {
				 echo api::error("Логине пустой!!1");
			}
			elseif(empty($password)) {
				echo api::error("Пароле пустой!!1");
			}
			elseif(empty($password_1)) {
				echo api::error("Второй пароле пустой!!1");
			}
			elseif($password != $password_1) {
				echo api::error("Пароле не совпадают!!1");
			}
			elseif(empty($email)) {
				echo api::error("Мыло пустое!!1");
			}
			else {
				$check_invite = R::getAll("SELECT * FROM `otake_invites` WHERE `code` = ? AND `is_used` = 0", array($invite));
				$check_user = R::getAll("SELECT * FROM `otake_users` WHERE `login` = ?", array($login));
				if(empty($check_invite)) {
					echo api::error("Такого инвайта нет!!1");
				}
				elseif(!empty($check_user)) {
					echo api::error("Такой юзер уже существует!!1");
				}
				else {
					R::exec("INSERT INTO `otake_users`(`login`, `passwd`, `joindate`, `join_ip`, `ugroup`, `email`, `parent_user`) VALUES (?, ?, ?, ?, ?, ?, ?)", array($login, md5($password), time(), $ip, 'user', $email, $check_invite[0]['parent_user']));
					R::exec("UPDATE `otake_invites` SET `is_used` = 1 WHERE `code` = ?", array($invite));
					echo api::success("Юзер зареган!!1");
				}
			}
		}
		function login($login, $password) {
			global $config;
			$password = md5($password);
			$login_user = R::getAll("SELECT * FROM `otake_users` WHERE `login` = ? AND `passwd` = ?", array($login, $password));
			if(empty($login_user)) {
				echo api::error("Такого юзера нет, либо неправильно введены логин или пароль!!1");
			}
			else {
				$_SESSION['user'] = $login_user[0]['login'];
				echo api::success("Залогинено!!1");
			}
		}
		function is_logged() {
			if(isset($_SESSION['user'])) {
				return true;
			} else {
				return false;
			}
		}
		function logout() {
			global $config;
			unset($_SESSION['user']);
			echo api::success("Разлогинено!!1");
		}
		function getInvites() {
			global $config;
			$all_invites = R::getAll("SELECT * FROM `otake_invites` WHERE `parent_user` = ? ORDER BY `id` DESC", array($_SESSION['user']));
			if(time() > ($all_invites[0]['create_time']+86400) || empty($all_invites)) {
				$new_invites = [md5(random_bytes(100)), md5(random_bytes(100)), md5(random_bytes(100))]; // ФУНКЦИЯ random_bytes((int) $length); РАБОТАЕТ ТОЛЬКО В PHP 7
				foreach($new_invites as $new_invite) {
					R::exec("INSERT INTO `otake_invites`(`code`, `parent_user`, `create_time`, `is_used`) VALUES (?, ?, ?, ?)", array($new_invite, $_SESSION['user'], time(), 0));
				}
				echo api::success("Инвайте получены!!1");
			} else {
				echo api::error("Инвайте за этот день уже получены!!1");
			}
		}
		function invites() {
			global $config;
			$invites = R::getAll("SELECT * FROM `otake_invites` WHERE `parent_user` = ? AND `is_used` = ? ORDER BY `create_time` DESC", array($_SESSION['user'], 0));
			foreach ($invites as $invite)
            {
                $invite_codes[] = $invite['code'];
            }
			echo json_encode($invite_codes);
		}
		static function user_array($username)
        {
            return R::getAll("SELECT * FROM `otake_users` WHERE `login` = ?", array($username));
        }
		static function current_user_array()
        {
            if($this->is_logged())
            {
                $user_info = self::user_array($_SESSION['user']);
                return $user_info;
            }
            else
            {
                return false;
            }
        }
	}
?>