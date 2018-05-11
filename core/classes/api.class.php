<?php
	class api
	{
		static function error($error)
		{
			return json_encode(['error' => $error]);
		}
		static function success($success)
        {
            return json_encode(['success' => $success]);
        }
		static function error_no_method()
		{
			echo self::error("Не выбран методе!!1");
		}
		static function error_no_login()
		{
			echo self::error("Нет входа!!1");
		}
		static function error_login_exists()
        {
            echo self::error("При логине за такое убивают!!1");
        }
        static function error_no_mod()
        {
            echo self::error("Не модератор!!1");
        }
	}
?>