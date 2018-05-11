<?php
	session_start();
	header('Content-Type: application/json; charset=utf-8');
	include 'core/includes/includes.php';
	include 'core/classes/user.class.php';
	include 'core/classes/api.class.php';
	$user = new user;
	if(!isset($_GET['method']))
	{
		api::error_no_method();
	}
	elseif(empty($_GET['method']))
	{
		api::error_no_method();
	}
	else
	{
		$method = $_GET['method'];
		$method_explode = explode('.', $method);
		if($method_explode[0] == 'user')
		{
			if($method_explode[1] == 'register')
            {
                if(!$user->is_logged())
                {
                    $user->register($_GET['login'], $_GET['password'], $_GET['password_1'], $_GET['email'], $_GET['invite']);
                }
                else
                {
                    api::error_login_exists();
                }
            }
            elseif ($method_explode[1] == 'login')
            {
                if(!$user->is_logged())
                {
                    $user->login($_GET['login'], $_GET['password']);
                }
                else
                {
                    api::error_login_exists();
                }
            }
            elseif ($method_explode[1] == 'logout')
            {
                if($user->is_logged())
                {
                    $user->logout();
                }
                else
                {
                    api::error_no_login();
                }
            }
            elseif ($method_explode[1] == 'getInvites')
            {
                if($user->is_logged())
                {
                    $user->getInvites();
                }
                else
                {
                    api::error_no_login();
                }
            }
            elseif ($method_explode[1] == 'invites')
            {
                if($user->is_logged())
                {
                    $user->invites();
                }
                else
                {
                    api::error_no_login();
                }
            }
		}
		elseif($method_explode[0] == 'sub')
		{
			include 'core/classes/sub.class.php';
			$api_sub = new sub;
			if($method_explode[1] == 'create')
			{

				if($user->is_logged())
				{
					echo $api_sub->create($_GET['address'], $_GET['name'], $_GET['description']); 
				}
				else
				{
					api::error_no_login();
				}
			}
			elseif ($method_explode[1] == 'overall')
            {
                if($user->is_logged())
                {
                    $api_sub->overall();
                }
                else
                {
                    api::error_no_login();
                }
            }
            elseif($method_explode[1] == 'info')
            {
                if($user->is_logged())
                {
                    if(isset($_GET['address']) && !empty($_GET['address']))
                    {
                        echo sub::sub_info($_GET['address']);
                    }
                    else
                    {
                        echo api::error("Адрес пустой!!1");
                    }
                }
                else
                {
                    api::error_no_login();
                }
            }
            elseif($method_explode[1] == 'posts')
            {
                if($user->is_logged())
                {
                    if(!isset($_GET['page']))
                    {
                        $page = 0;
                    }
                    elseif(empty($_GET['page']))
                    {
                        $page = 0;
                    }
                    elseif(!is_numeric($_GET['page']))
                    {
                        $page = 0;
                    }
                    else
                    {
                        $page = $_GET['page'];
                    }
                    if(!isset($_GET['address']))
                    {
                        echo api::error("Адрес раздела пустой!!1");
                    }
                    else
                    {
                         echo $api_sub->posts($_GET['address'], $page);
                    }
                }
            }
		}
		elseif ($method_explode[0] == 'post')
        {
            include 'core/classes/post.class.php';
            $api_post = new post;
            if($method_explode[1] == 'create')
            {
                if($user->is_logged())
                {
                    echo $api_post->create($_GET['text'], $_GET['sub']);
                }
                else
                {
                    api::error_no_login();
                }
            }
            elseif($method_explode[1] == 'info')
            {
                if($user->is_logged())
                {
                    if(isset($_GET['id']) && !empty($_GET['id']))
                    {
                        echo post::info($_GET['id']);
                    }
                    else
                    {
                        api::error("Нужен id поста!!1");
                    }
                }
                else
                {
                    api::error_no_login();
                }
            }
        }
        else
        {
            api::error("Не выбран методе!!1");
        }
	}
?>