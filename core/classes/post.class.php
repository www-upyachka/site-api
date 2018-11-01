<?php
	class post
	{
		/**
		 * @var int $delay кулдаун при постинге
		 */
		public $delay = 180;
		/**
		 * @var int $postsInPage постов на странице
		 */
		public $postsInPage = 20;
		public function create($text, $sub)
		{
			R::exec("INSERT INTO `otake_posts`(`author`, `create_time`, `post_text`, `sub`, `deleted`, `bumped`) VALUES (?, ?, ?, ?, ?, ?)", [$GLOBALS['username'], time(), $text, $sub, 0, time()]);
		}
		public function info($id)
		{
			return R::getAll("SELECT * FROM `otake_posts` WHERE `id` = ?", [$id]);
		}
		public function lastPostByUser($username)
		{
			return R::getAll("SELECT * FROM `otake_posts` WHERE `author` = ? ORDER BY `create_time` DESC", [$username]);
		}
		public function listInSub($sub, $page=0)
		{
			$begin = $page * $this->postsInPage;
			return R::getAll("SELECT * FROM `otake_posts` WHERE `sub` = ? AND `deleted` = 0 ORDER BY `bumped` DESC LIMIT {$begin}, {$this->postsInPage}", array($sub));
		}
		public function countInSub($sub)
		{
			return R::getAll("SELECT COUNT(*) FROM `otake_posts` WHERE `sub` = ? AND `deleted` = 0", [$sub])[0]['COUNT(*)'];
		}
		public function exists($id)
		{
			$postInfo = $this->info($id);
			$sub = new sub;
			if(empty($postInfo))
			{
				return false;
			}
			elseif($postInfo[0]['deleted'] == 1)
			{
				return false;
			}
			else
			{
				$subExists = $sub->exists($postInfo[0]['sub']);
				if(!$subExists)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
		}
		public function inSub($sub)
		{
			$sub_obj = new sub;
			if(!$sub_obj->exists($sub))
			{
				return R::getAll("SELECT * FROM `otake_posts` WHERE `sub` = ? AND `deleted` = 0", [$sub]);
			}
			else
			{
				return false;
			}
		}
		public function delete($id)
		{
			R::exec("UPDATE `otake_posts` SET `deleted` = 1 WHERE `id` = ?", [$id]);
			$modlog = new modlog;
			$postInfo = $this->info($id);
			$modlog->addEntry($GLOBALS['username'], "delete_psto", $id, 0, $postInfo[0]['sub'], $postInfo[0]['author'], time());
		}
		public function versions($id)
		{
			return R::getAll("SELECT * FROM `otake_posts_versions` WHERE `post_id` = ?", [$id]);
		}
		public function edit($id, $text)
		{
			$versions = $this->versions($id);
			$postInfo = $this->info($id);
			$modlog = new modlog;
			if(empty($versions))
			{
				R::exec("INSERT INTO `otake_posts_versions`(`author`, `editor`, `ver_time`, `text`, `post_time`, `post_id`) VALUES (?, ?, ?, ?, ?, ?)", [$postInfo[0]['author'], $postInfo[0]['author'], $postInfo[0]['create_time'], $postInfo[0]['post_text'], $postInfo[0]['create_time'], $id]);
				R::exec("INSERT INTO `otake_posts_versions`(`author`, `editor`, `ver_time`, `text`, `post_time`, `post_id`) VALUES (?, ?, ?, ?, ?, ?)", [$postInfo[0]['author'], $GLOBALS['username'], time(), $text, $postInfo[0]['create_time'], $id]);
			}
			else
			{
				R::exec("INSERT INTO `otake_posts_versions`(`author`, `editor`, `ver_time`, `text`, `post_time`, `post_id`) VALUES (?, ?, ?, ?, ?, ?)", [$postInfo[0]['author'], $GLOBALS['username'], time(), $text, $postInfo[0]['create_time'], $id]);
			}
			R::exec("UPDATE `otake_posts` SET `post_text` = ? WHERE `id` = ?", [$text, $id]);
			$modlog->addEntry($GLOBALS['username'], "edit_psto", $id, 0, $postInfo[0]['sub'], $postInfo[0]['author'], time());
		}
		public function versionsToProd($id)
		{
			$versions = R::getAll("SELECT * FROM `otake_posts_versions` WHERE `post_id` = ? ORDER BY `ver_time`", [$id]);
			return $versions;
		}
		public function setInvited ($id) {
			R::exec("UPDATE `otake_posts` SET `is_invited` = ? WHERE `id` = ?", [
				1,
				$id
			]);
		}
	}
?>