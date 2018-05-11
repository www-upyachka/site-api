<?php
    class modlog
    {
        static function add_entry($moderator, $type, $post_id, $comment_id, $sub, $user_moderated, $datetime)
        {
            R::exec("INSERT INTO `otake_modlog`(`moderator`, `type`, `post_id`, `comment_id`, `sub`, `user_moserated`, `datetime`) VALUES (?, ?, ? , ?, ?, ?, ?)", array($moderator, $type, $post_id, $comment_id, $sub, $user_moderated, $datetime));
        }
//        static function read()
////        {
////
////        }
    }
?>