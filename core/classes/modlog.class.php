<?php
	class modlog
	{
		public function addEntry($moderator, $type, $post_id, $comment_id, $sub, $user_moderated, $datetime)
		{
			R::exec("INSERT INTO `otake_modlog`(`moderator`, `type`, `post_id`, `comment_id`, `sub`, `user_moderated`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?)", [$moderator, $type, $post_id, $comment_id, $sub, $user_moderated, $datetime]);
		}
		public function read () {
			return R::getAll("SELECT * FROM `otake_modlog` ORDER BY `datetime` DESC");
		}
	}
?>