<?php
	class ban
	{
		public function giveInSub($username, $sub, $term, $reason)
		{
			R::exec("INSERT INTO `otake_bans`(`moderator`, `banned_user`, `time`, `reason`, `sub`, `discontinued`, `discontinued_in_date`, `ban_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [$GLOBALS['username'], $username, time() + $term, $reason, $sub, 0, 0, time()]);
		}
		public function infoInSub($username, $sub)
		{
			return R::getAll("SELECT * FROM `otake_bans` WHERE `banned_user` = ? AND `sub` = ? ORDER BY `id` DESC", [$username, $sub]);
		}
		public function currentInfoInSub($sub)
		{
			return $this->infoInSub($GLOBALS['username'], $sub);
		}
		public function listInSub($sub) {
			$list = R::getAll("SELECT * FROM `otake_bans` WHERE `sub` = ? ORDER BY `id` DESC", [$sub]);
			return $list;
		}
		public function byId($id) {
			return R::getAll("SELECT * FROM `otake_bans` WHERE `id` = ?", [$id]);
		}
		public function actual($ban) {
			return $ban['time'] > time() && $ban['discontinued'] == 0;
		}
		public function pardon($id) {
			R::exec("UPDATE `otake_bans` SET `discontinued` = ?, `discontinued_in_date` = ?, `discontinued_by` = ? WHERE `id` = ?", [
				1,
				time(),
				$GLOBALS['username'],
				$id
			]);
		}
	}
?>