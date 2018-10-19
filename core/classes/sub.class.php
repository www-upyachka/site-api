<?php
	class sub
	{
		public function info($address)
		{
			$info = R::getAll("SELECT * FROM `otake_subpages` WHERE `address` = ?", [$address]);
			return $info;
		}
		public function allList()
		{
			$list = R::getAll("SELECT * FROM `otake_subpages` ORDER BY `id` ASC");
			return $list;
		}
		public function exists($address)
		{
			$subInfo = $this->info($address);
			if(empty($subInfo))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		public function create($address, $name, $description)
		{
			R::exec("INSERT INTO `otake_subpages`(`address`, `name`, `description`, `admin`, `hidden`, `create_time`) VALUES (?, ?, ?, ?, ?, ?)", [$address, $name, $description, $GLOBALS['username'], 0, time()]);
		}
		public function edit($address, $name, $description)
		{
			R::exec("UPDATE `otake_subpages` SET `name` = ?, `description` = ? WHERE `address` = ?", [$name, $description, $address]);
		}
		public function moderatorExists($usernick, $sub) {
			$moderator = R::getAll("SELECT * FROM `otake_moderators` WHERE `username` = ? AND `sub` = ? AND `discontinued` = ?", [
				$usernick,
				$sub,
				0
			]);
			return $moderator;
		}
		public function addModerator($usernick, $sub) {
			$modlog = new modlog();
			R::exec("INSERT INTO `otake_moderators`(`username`, `date`, `king`, `sub`, `discontinued`, `discontinued_in_date`) VALUES (?, ?, ?, ?, ?, ?)", [
				$usernick,
				time(),
				$GLOBALS['username'],
				$sub,
				0,
				0
			]);
			$modlog->addEntry(
				$GLOBALS['username'],
				'add_mod',
				0,
				0,
				$sub,
				$usernick,
				time()
			);
		}
		public function removeModerator($usernick, $sub) {
			$modlog = new modlog();
			R::exec("UPDATE `otake_moderators` SET `discontinued` = ?, `discontinued_in_date` = ?, `discontinued_by` = ? WHERE `sub` = ? AND `username` = ?", [
				1,
				time(),
				$GLOBALS['username'],
				$sub,
				$usernick
			]);
			$modlog->addEntry(
				$GLOBALS['username'],
				'remove_mod',
				0,
				0,
				$sub,
				$usernick,
				time()
			);
		}
		public function mods($sub) {
			$mods = R::getAll("SELECT * FROM `otake_moderators` WHERE `sub` = ? AND `discontinued` = ?", [
				$sub,
				0
			]);
			return $mods;
		}
	}
?>