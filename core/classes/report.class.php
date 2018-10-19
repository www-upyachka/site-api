<?php
	class report {
		public $allowedContentTypes = [
			'post',
			'psto',
			'pots',
			'comment'
		];
		public function create ($usernick, $contentType, $contentId, $reason, $sub, $date) {
			R::getAll("INSERT INTO `otake_reports`(`content_type`, `content_id`, `user`, `reason`, `sub`, `date`) VALUES (?, ?, ?, ?, ?, ?)", [
				$contentType,
				$contentId,
				$usernick,
				$reason,
				$sub,
				$date
			]);
		}
		public function info($usernick, $contentType, $contentId, $sub) {
			return R::getAll("SELECT * FROM `otake_reports` WHERE `user` = ? AND `content_type` = ? AND `content_id` = ? AND `sub` = ?", [
				$usernick,
				$contentType,
				$contentId,
				$sub
			]);
		}
		public function exists ($usernick, $contentType, $contentId, $sub) {
			$info = $this->info($usernick, $contentType, $contentId, $sub);
			return $info;
		}
		public function inSub ($sub) {
			return R::getAll("SELECT * FROM `otake_reports` WHERE `sub` = ? ORDER BY `id` DESC", [
				$sub
			]);
		}
	}
?>