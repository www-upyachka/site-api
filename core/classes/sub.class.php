<?php
	class sub {
		function create($address, $name, $description) {
			global $config;
			if(empty($address)) {
				api::error("Адрес пустой");
			}
			elseif(empty($name)) {
				api::error("Имя пустое");
			}
			elseif(empty($description)) {
				api::error("Опейсание пустое");
			}
			elseif(preg_match('/^[0-9]/iuU', $address)) {
				api::error("По техническим причинам адрес не может начинаться с числа. Можно в начале добавить _");
			}
			else {
				$sub_exists = R::getAll("SELECT * FROM `otake_subpages` WHERE `address` = ? AND `hidden` = 0", array($address));
				if(empty($sub_exists)) {
					R::exec("INSERT INTO `otake_subpages`(`address`, `name`, `description`, `admin`, `hidden`, `create_time`) VALUES (?, ?, ?, ?, ?, ?)", array($address, $name, $description, $_SESSION['user'], 0, time()));
					api::success('Раздел создан');
				}
				else {
					api::error("Раздел с таким адресом уже существует");
				}
			}
		}
		function is_exists($address) {
			global $config;
			$sub_exists = R::getAll("SELECT * FROM `otake_subpages` WHERE `address` = ? OR `id` = ? AND `hidden` = 0", array($address, $address));
			if(!empty($sub_exists)) {
				return true;
			} else {
				return false;
			}
		}
		function overall() {
			global $config;
			$overall = R::getAll("SELECT * FROM `otake_subpages` WHERE `hidden` = 0");
			foreach ($overall as $sub) {
                unset($sub['hidden']);
                $_overall[] = $sub;
			}
            echo json_encode($_overall);
		}
		function info($address) {
			global $config;
			$sub_info = R::getAll("SELECT * FROM `otake_subpages` WHERE `id` = ? OR `address` = ? AND `hidden` = 0", array($address, $address));
			if(!empty($sub_info)) {
				echo "<h2>" . htmlspecialchars($sub_info[0]['name']) . "</h2>";
				echo "<a href='{$config['site_url']}/sub/{$sub_info[0]['address']}/manage/'>Президентская лажа</a>";
			}
		}
		static function sub_info($address)
		{
			$sub = R::getAll("SELECT * FROM `otake_subpages` WHERE `address` = ? OR `id` = ? AND `hidden` = 0", array($address, $address));
			unset($sub[0]['hidden']);
			$sub = $sub[0];
			if(!empty($sub))
            {
                return json_encode($sub);
            }
            else
            {
                return api::error("Раздел не существуе!!1");
            }
		}
		static function arrayInfo($address)
        {
            return R::getAll("SELECT * FROM `otake_subpages` WHERE `address` = ? OR `id` = ? AND `hidden` = 0", array($address, $address));
        }
		function posts($address, $page) {
		    $limit = 40;
		    $begin = $page * $limit;
			$posts = R::getAll("SELECT * FROM `otake_posts` WHERE `sub` = ? AND `deleted` = 0 ORDER BY `bumped` DESC LIMIT $begin,$limit", array($address));
            return json_encode($posts);
		}
		function is_mod($user, $sub)
        {
            $sub_info = self::arrayInfo($sub);
            if($sub_info['admin'] == $user)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
	}
?>