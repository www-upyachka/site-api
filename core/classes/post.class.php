<?php
	class post
    {
        function create($text, $sub)
		{
			global $config;
			include 'sub.class.php';
			$sub = sub::arrayInfo($sub);
			if(empty($text))
			{
				return api::error("Пост не может быть пустым!!1");
			}
			elseif(empty($sub))
			{
				return api::error("Такого раздела нет!!1");
			}
			else
			{
				R::exec("INSERT INTO `otake_posts`(`author`, `create_time`, `post_text`, `sub`, `deleted`, `bumped`) VALUES (?, ?, ?, ?, ?, ?)", array($_SESSION['user'], time(), $text, $sub[0]['address'], 0, time()));
				return api::success("Псто написан!!1");
			}
		}
		static function Arrayinfo($id)
        {
            return R::getAll("SELECT * FROM `otake_posts` WHERE `id` = ?", array($id));
        }
        static function info($id)
        {
            return json_encode(R::getAll("SELECT * FROM `otake_posts` WHERE `id` = ? AND `deleted` = 0;", array($id)));
        }
		function delete($id)
        {
            include 'modlog.class.php';
            $post_info = self::Arrayinfo($id);
            R::exec("UPDATE `otake_posts` SET `deleted` = 1 WHERE `id` = ?", array($id));
            modlog::add_entry($_SESSION['user'], "delete_psto", $id, 0, $post_info, time());
        }
	}
?>