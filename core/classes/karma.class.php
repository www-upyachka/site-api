<?php
	class karma
	{
		public $availableContentTypes = ["post", "psto", "comment"];
		public $availableVoteTypes = ["plus", "minus"];
		public function delete($contentId, $contentType, $type, $votingUser)
		{
			R::exec("UPDATE `otake_karma` SET `is_valid` = 0 WHERE `content_id` = ? AND `content_type` = ? AND `type` = ? AND `voting_user` = ?", [$contentId, $contentType, $type, $votingUser]);
		}
		public function exists($contentId, $contentType, $type, $votingUser)
		{
			$karmaEntry = R::getAll("SELECT * FROM `otake_karma` WHERE `content_id` = ? AND `content_type` = ? AND `type` = ? AND `voting_user` = ? AND `is_valid` = ? ORDER BY `id` DESC", [$contentId, $contentType, $type, $votingUser, 1]);
			return !empty($karmaEntry);
		}
		public function set($contentId, $contentType, $type, $mass, $toUser)
		{
			R::exec("INSERT INTO `otake_karma`(`content_id`, `content_type`, `type`, `mass`, `voting_user`, `date`, `to_user`) VALUES (?, ?, ?, ?, ?, ?, ?)", [$contentId, $contentType, $type, $mass, $GLOBALS['username'], time(), $toUser]);
		}
		public function onPost($id) {
			$onPost = R::getAll("SELECT * FROM `otake_karma` WHERE `content_type` = ? AND `content_id` = ? AND `is_valid` = ?", [
				'psto',
				$id,
				1
			]);
			return $onPost;
		}
		public function countOnPost ($id) {
			$onPost = $this->onPost($id);
			$sum = 0;
			foreach ($onPost as $karmaEntry) {
				$mass = $karmaEntry['mass'];
				if($karmaEntry['type'] == 'minus') {
					$mass = $mass * (-1);
				}
				$sum += $mass;
			}
			return $sum;
		}
		public function countOnPostWithoutMass($id) {
			$onPost = $this->onPost($id);
			$sum = 0;
			foreach ($onPost as $karmaEntry) {
				if($karmaEntry['type'] == 'minus') {
					$sum -= 1;
				}
				else {
					$sum += 1;
				}
			}
			return $sum;
		}
		public function onComment($id) {
			$onComment = R::getAll("SELECT * FROM `otake_karma` WHERE `content_type` = ? AND `content_id` = ? AND `is_valid` = ?", [
				'comment',
				$id,
				1
			]);
			return $onComment;
		}
		public function countOnComment($id) {
			$onComment = $this->onComment($id);
			$sum = 0;
			foreach ($onComment as $karmaEntry) {
				$mass = $karmaEntry['mass'];
				if($karmaEntry['type'] == 'minus') {
					$mass = $mass * (-1);
				}
				$sum += $mass;
			}
			return $sum;
		}
		public function countOnCommentWithoutMass($id) {
			$onComment = $this->onPost($id);
			$sum = 0;
			foreach ($onComment as $karmaEntry) {
				if($karmaEntry['type'] == 'minus') {
					$sum -= 1;
				}
				else {
					$sum += 1;
				}
			}
		}
		public function ofUser($usernick) {
			$ofUser = R::getAll("SELECT * FROM `otake_karma` WHERE `to_user` = ? AND `is_valid` = ?", [
				$usernick,
				1
			]);
			return $ofUser;
		}
		public function countOfUser($usernick) {
			$ofUser = $this->ofUser($usernick);
			$sum = 0;
			foreach($ofUser as $karmaEntry) {
				$mass = $karmaEntry['mass'];
				if($karmaEntry['type'] == 'minus') {
					$mass = $mass * (-1);
				}
				$sum += $mass;
			}
			return $sum;
		}
		public function countOfUserWithoutMass($usernick) {
			$ofUser = $this->ofUser($usernick);
			$sum = 0;
			foreach ($ofUser as $karmaEntry) {
				if($karmaEntry['type'] == 'minus') {
					$sum -= 1;
				}
				else {
					$sum += 1;
				}
			}
			return $sum;
		}
		public function countOfPlusOnPost ($id) {
			$onPost = R::getAll("SELECT COUNT(*) FROM `otake_karma` WHERE `content_type` = ? AND `content_id` = ? AND `is_valid` = ? AND `type` = ?", [
				'psto',
				$id,
				1,
				'plus'
			]);
			return $onPost[0]['COUNT(*)'];
		}
		public function countOfMinusOnPost($id) {
			$onPost = R::getAll("SELECT COUNT(*) FROM `otake_karma` WHERE `content_type` = ? AND `content_id` = ? AND `is_valid` = ? AND `type` = ?", [
				'comment',
				$id,
				1,
				'plus'
			]);
			return $onPost[0]['COUNT(*)'];
		}
		public function postIsGold($id) {
			$countOfPlus = $this->countOfPlusOnPost($id);
			$totalCount = $this->countOnPost($id);
			$totalCountWithoutMass = $this->countOnPostWithoutMass($id);
			$percentOfPlus = $countOfPlus / $totalCountWithoutMass * 100;
			return($totalCount > 5 && $totalCountWithoutMass > 5 && $percentOfPlus > 98);
		}
	}
?>