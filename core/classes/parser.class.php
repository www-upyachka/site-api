<?php
	class parser
	{
		/**
		 * @param $text
		 * @return null|string|string[]
		 * @throws 1
		 */
		public function parse($text)
		{
			global $qevix, $parsedown;
			$qevix->cfgAllowTags(['a', 'marquee']);
			$qevix->cfgAllowTagParams('a', ['href']);
			$qevix->cfgAllowTagParams('marquee', [
				'behavior',
				'bgcolor',
				'direction',
				'height',
				'hspace',
				'loop',
				'scrollamount',
				'scrolldelay',
				'truespeed',
				'vspace',
				'width'
			]);
			$text = $qevix->parse($text, $errors = false);
			$text = $parsedown->parse($text);
			$text = preg_replace('/\[youtube(.*)\](.+)\[\/youtube\]/i', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$2" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>', $text);
			$text = preg_replace('#<a href="https://youtu.be/(.*)">https://youtu.be/(.*)</a>#i', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$2" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>', $text);
			$text = preg_replace('#<a href="https://www.youtube.com/watch\?v=(.*)">https://www.youtube.com/watch\?v=(.*)</a>#i', '<iframe width="560" height="315" src="https://www.youtube.com/embed/$1" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>', $text);
			$text = preg_replace('/\[textstyle=(.*?)\](.*?)\[\/textstyle\]/is', '<span style="$1">$2</span>', $text);
			$text = preg_replace('/\[irony(.*?)\](.*?)\[\/irony\]/is', '<span class="irony">$2</span>', $text);
			$text = preg_replace('/\[bkb(.*?)\](.*?)\[\/bkb\]/is', '<span class="bkb">$2</span>', $text);
			$text = preg_replace('/\[bnb(.*?)\](.*?)\[\/bnb\]/is', '<span class="bnb">$2</span>', $text);
			$text = preg_replace('/\[bigred(.*?)\](.*?)\[\/bigred\]/is', '<span class="bkb">$2</span>', $text);
			$text = preg_replace('/\[pastebin\](.*?)\[\/pastebin\]/iuU', '<iframe src="https://pastebin.com/embed_iframe/$1" style="border:none;width:100%; height:300px;"></iframe>', $text);
			$text = preg_replace('/!{15,}/iu', '! я идиот! убейте меня, кто-нибудь!', $text);
			$text = preg_replace("/%username%/iu", $GLOBALS['username'], $text);
			$text = preg_replace('/(а{0,} {0,}не{0,} {0,}пиздишь\?{0,})|(лпп)/iu', '<img src="https://armarium.org/u/2018/09/03/cfaa1efca6f7d9befb73b01d8d7f35e1.jpg" alt="А не пиздишь?" border="0" />', $text);
			$text = preg_replace('/(я{0,} {0,}(нихуя|ничего|ниче|нихера) не пон(e|я)л)|(яннп)/iu', '<img src="https://armarium.org/u/2018/09/03/7cbe48703b5556a096735f5366f1a14d.jpg" alt="Я НИХУЯ НЕ ПОНЯЛ!!!" border="0" />', $text);
			return $text;
		}

		/**
		 * @param $post
		 * @return array
		 */
		public function modifyPostArray($post)
		{
			$user = new user();
			$post['can_moderate'] = $user->isModInSub($GLOBALS['username'], $post['sub']);
			$post['can_edit'] = $user->isModInSub($GLOBALS['username'], $post['sub']) || $user->isPostAuthor($post['id'], $GLOBALS['username']);
			$post['raw_text'] = $post['post_text'];
			$post['post_text'] = $this->parse($post['post_text']);
			return ($post);
		}

		/**
		 * @param $comment
		 * @return array
		 */
		public function modifyCommentArray($comment)
		{
			$user = new user();
			$commentObject = new comment();
			$comment['can_moderate'] = $user->isModInSub($GLOBALS['username'], $comment['sub']);
			$comment['can_edit'] = $user->isModInSub($GLOBALS['username'], $comment['sub']) || $user->isCommentAuthor($comment['id'], $GLOBALS['username']);
			$comment['raw_text'] = $comment['comment_text'];
			$comment['comment_text'] = $this->parse($comment['comment_text']);
			$comment['children'] = $commentObject->loadChildren($comment['id']);
			return $comment;
		}

		/**
		 * @param $version
		 * @return array
		 */
		public function modifyPostVersion($version) {
			$version['raw_text'] = $version['text'];
			$version['text'] = $this->parse($version['text']);
			return $version;
		}

		/**
		 * @param $version
		 * @return array
		 */
		public function modifyCommentVersion($version)
		{
			$version['raw_text'] = $version['text'];
			$version['text'] = $this->parse($version['text']);
			return $version;
		}
		public function modifySub($sub)
		{
			$user = new user();
			$isMod = $user->isModInSub($GLOBALS['username'], $sub['address']);
			$sub['can_moderate'] = $isMod;
			$sub['raw_description'] = $sub['description'];
			$sub['description'] = $this->parse($sub['description']);
			unset($sub['hidden']);
			return $sub;
		}
		public function modifyBan($ban) {
			if($ban['discontinued'] != 0) {
				$ban['discontinued'] = true;
			}
			else {
				$ban['discontinued'] = false;
			}
			$ban['raw_reason'] = $ban['reason'];
			$ban['reason'] = htmlspecialchars($ban['reason']);
			if($ban['time'] < time()) {
				$ban['expired'] = true;
			}
			else {
				$ban['expired'] = false;
			}
			return $ban;
		}
		public function modifyReport($report) {
			$report['reason'] = $this->parse($report['reason']);
			return $report;
		}
	}
?>
