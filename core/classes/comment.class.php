<?php
	class comment
	{
		/**
		 * @var int $delay кулдаун при комментировании
		 */
		public $delay = 5;
		public function info($id)
		{
			return R::getAll("SELECT * FROM `otake_comments` WHERE `id` = ?", [$id]);
		}
		public function lastCommentByUser($username)
		{
			return R::getAll('SELECT * FROM `otake_comments` WHERE `author` = ? ORDER BY `create_time` DESC', [$username]);
		}
		public function exists($id)
		{
			$post = new post();
			$commentInfo = $this->info($id);

			if(empty($commentInfo) || !$post->exists($commentInfo[0]['parent_post']) || $commentInfo[0]['deleted'] == 1)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		public function create($postId, $text, $parentComment = 0)
		{
			$post = new post;
			$postInfo = $post->info($postId);
			R::exec("INSERT INTO `otake_comments`(`author`, `create_time`, `comment_text`, `sub`, `parent_post`, `parent_comment`, `deleted`) VALUES (?, ?, ?, ?, ?, ?, ?)", [$GLOBALS['username'], time(), $text, $postInfo[0]['sub'], $postId, $parentComment, 0]);
			R::exec("UPDATE `otake_posts` SET `bumped` = ? WHERE `id` = ?", [time(), $postId]);
		}
		public function delete($id)
		{
			R::exec("UPDATE `otake_comments` SET `deleted` = 1 WHERE `id` = ?", [$id]);
			$modlog = new modlog;
			$commentInfo = $this->info($id);
			$modlog->addEntry($GLOBALS['username'], "delete_comment", $commentInfo[0]['parent_post'], $id, $commentInfo[0]['sub'], $commentInfo[0]['author'], time());
		}
		public function versions($id)
		{
			return R::getAll("SELECT * FROM `otake_comments_versions` WHERE `post_id` = ?", [$id]);
		}
		public function versionsToProd($id)
		{
			return( R::getAll("SELECT * FROM `otake_comments_versions` WHERE `post_id` = ? ORDER BY `id` DESC", [$id]));
		}
		public function edit($id, $text)
		{
			$versions = $this->versions($id);
			$postInfo = $this->info($id);
			$post = new post();
			$parentPostInfo = $post->info($postInfo[0]['parent_post']);
			$modlog = new modlog();
			if(empty($versions))
			{
				R::exec("INSERT INTO `otake_comments_versions`(`author`, `editor`, `ver_time`, `text`, `post_time`, `post_id`) VALUES (?, ?, ?, ?, ?, ?)", [$postInfo[0]['author'], $postInfo[0]['author'], $postInfo[0]['create_time'], $postInfo[0]['comment_text'], $postInfo[0]['create_time'], $id]);
				R::exec("INSERT INTO `otake_comments_versions`(`author`, `editor`, `ver_time`, `text`, `post_time`, `post_id`) VALUES (?, ?, ?, ?, ?, ?)", [$postInfo[0]['author'], $GLOBALS['username'], time(), $text, $postInfo[0]['create_time'], $id]);
			}
			else
			{
				R::exec("INSERT INTO `otake_comments_versions`(`author`, `editor`, `ver_time`, `text`, `post_time`, `post_id`) VALUES (?, ?, ?, ?, ?, ?)", [$postInfo[0]['author'], $GLOBALS['username'], time(), $text, $postInfo[0]['create_time'], $id]);
			}
			R::exec("UPDATE `otake_comments` SET `comment_text` = ? WHERE `id` = ?", [$text, $id]);
			$modlog->addEntry($GLOBALS['username'], "edit_comment", $parentPostInfo[0]['id'], $id, $parentPostInfo[0]['sub'], $postInfo[0]['author'], time());
		}
		public function underPost($postId)
		{
			return R::getAll("SELECT * FROM `otake_comments` WHERE `parent_post` = ? AND `deleted` = 0 AND `parent_comment` = 0", [$postId]);
		}
		public function countUnderPost($postId)
		{
			return R::getAll("SELECT COUNT(*) FROM `otake_comments` WHERE `parent_post` = ? AND `deleted` = 0", [$postId])[0]['COUNT(*)'];
		}
		public function parentComments($commentId)
		{
			return R::getAll("SELECT * FROM `otake_comments` WHERE `parent_comment` = ? AND `deleted` = 0", [$commentId]);
		}
		public function loadChildren ($commentId) {
			$parser = new parser();
			$childrens = $this->parentComments($commentId);
			foreach ($childrens as $key => $children) {
				$childrens[$key] = $parser->modifyCommentArray($children);
				$childrens[$key]['children'] = $this->loadChildren($children['id']);
			}
			return $childrens;
		}
	}
?>