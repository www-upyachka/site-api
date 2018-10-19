<?php
	class invite
	{
		public function info($code)
		{
			$invite = R::getAll("SELECT * FROM `otake_invites` WHERE `code` = ?", [$code]);
			return $invite;
		}

		public function set_used($code)
		{
			R::exec("UPDATE `otake_invites` SET `is_used` = 1 WHERE `code` = ?", [$code]);
		}

		public function getList($username)
		{
			return R::getAll("SELECT * FROM `otake_invites` WHERE `parent_user` = ? ORDER BY `id` DESC", [$username]);
		}

		public function getNotUsedList($username)
		{
			return R::getAll("SELECT * FROM `otake_invites` WHERE `parent_user` = ? AND `is_used` = 0 ORDER BY `id` DESC", [$username]);
		}

		public function getCurrentUserNotUsedList()
		{
			return $this->getNotUsedList($GLOBALS['username']);
		}

		public function getCurrentUserList()
		{
			return $this->getList($GLOBALS['username']);
		}

		public function get($usernick)
		{
			$invite_codes = [md5(rand(1,1000000000)), md5(rand(1,1000000000)), md5(rand(1,1000000000))];
			foreach($invite_codes as $code)
			{
				R::exec("INSERT INTO `otake_invites` (`code`, `parent_user`, `create_time`, `is_used`) VALUES (?, ?, ?, ?)", [$code, $usernick, time(), 0]);
			}
		}
		public function freeList()
		{
			$list = R::getAll("SELECT * FROM `otake_invites` WHERE `is_used` = 0 ORDER BY `id` DESC");
			return $list;
		}
	}
?>