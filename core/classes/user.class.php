<?php
	class user
	{
		public function info($usernick)
		{
			return R::getAll("SELECT * FROM `otake_users` WHERE `login` = ?", [$usernick]);
		}
		public function exists($usernick)
		{
			if(!empty($this->info($usernick)))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		public function loginAreCorrect($login, $passwd)
		{
			$passwd = md5($passwd);
			$userInfo = R::getAll("SELECT * FROM `otake_users` WHERE `login` = ? AND `passwd` = ?", [$login, $passwd]);
			if(!empty($userInfo))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		public function register($login, $passwd, $join_ip, $email, $parent_user)
		{
			R::getAll("INSERT INTO `otake_users`(`login`, `passwd`, `joindate`, `join_ip`, `ugroup`, `email`, `parent_user`) VALUES (?, ?, ?, ?, ?, ?, ?)", [$login, $passwd, time(), $join_ip, "user", $email, $parent_user]);
		}
		public function userTokens($usernick)
		{
			return R::getAll("SELECT * FROM `otake_access_tokens` WHERE `for_user` = ? ORDER BY `id` DESC", [$usernick]);
		}
		public function tokenExists($usernick)
		{
			$tokens = $this->userTokens($usernick);
			if($tokens[0]['expires_in'] > time() && $tokens[0]['aborted'] != 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		public function addToken($usernick)
		{
			$expiresIn = 604800;
			$tokenCode = generateRandomKey() . generateRandomKey();
			R::exec("INSERT INTO `otake_access_tokens`(`code`, `for_user`, `expires_in`, `create_time`, `aborted`) VALUES (?, ?, ?, ?, ?)", [$tokenCode, $usernick, time() + $expiresIn, time(), 0]);
		}
		public function tokenByCode($code)
		{
			return R::getAll("SELECT * FROM `otake_access_tokens` WHERE `code` = ?", [$code]);
		}
		public function tokenIsValid($code)
		{
			$token = $this->tokenByCode($code);
			if($token[0]['expires_in'] > time() && $token[0]['aborted'] != 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		public function isLogged()
		{
			if(isset($GLOBALS['username']))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		public function isGlobalMod($usernick)
		{
			$userInfo = $this->info($usernick);
			return($userInfo[0]['ugroup'] == 'admin');
		}
		public function isAdminInSub($usernick, $sub)
		{
			$subClass = new sub();
			$subInfo = $subClass->info($sub);
			$isAdmin = $subClass->info($sub)[0]['admin'] == $usernick || $this->isGlobalMod($usernick);
			return $isAdmin;
		}
		public function isModInSub($usernick, $sub)
		{
			$subClass = new sub();
			return($subClass->exists($sub) && ($this->isGlobalMod($usernick) || $this->isAdminInSub($usernick, $sub) || $subClass->moderatorExists($usernick, $sub)));
		}
		public function currentIsModInSub($sub) {
			return $this->isModInSub($GLOBALS['username'], $sub);
		}
		public function isBannedInSub($usernick, $sub)
		{
			$ban = new ban;
			$banInfo = $ban->infoInSub($usernick, $sub);
			$lastBan = $banInfo;
			if(!isset($lastBan[0]) || empty($lastBan[0]) || !$ban->actual($lastBan[0]))
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		public function currentIsBannedInSub($sub)
		{
			return $this->isBannedInSub($GLOBALS['username'], $sub);
		}
		public function isPostAuthor($postId, $usernick) {
			$post = new post();
			$postInfo = $post->info($postId);
			return $postInfo[0]['author'] == $usernick;
		}
		public function isCommentAuthor($commentId, $usernick) {
			$comment = new comment();
			$commentInfo = $comment->info($commentId);
			return ( $commentInfo[0]['author'] == $usernick);
		}
	}
?>