<?php
class Utils
{
    public static function getWeekdayName(int $weekday) : string {
		$weekdayName = '';
		switch ($weekday) {
			case 1:
				$weekdayName = 'Monday';
				break;
			case 2:
				$weekdayName = 'Tuesday';
				break;
			case 3:
				$weekdayName = 'Wednesday';
				break;
			case 4:
				$weekdayName = 'Thursday';
				break;
			case 5:
				$weekdayName = 'Friday';
				break;
			case 6:
				$weekdayName = 'Saturday';
				break;
			case 7:
				$weekdayName = 'Sunday';
				break;
		}
		return $weekdayName;
	}

    public static function containsCiryllicLetters($string) {
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $code = Utils::uniOrd($string[$i]);
            if ($code >= 1040 && $code <= 1103) {
                return true;
            }
        }
        return false;
    }

    public static function uniOrd($u) {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));
        return $k2 * 256 + $k1;
    }
}
