<?php
/**
 * Created by PhpStorm.
 * User: jaslew
 * Date: 18-3-1
 * Time: 下午1:47
 */

class Poem{
    public static $title;
    public static $content;

    public function __construct($no)
    {
        switch ($no){
            case $no == 1 :{
                self::$title = "《诗经·国风·王风·黍离》<br><br>";
                self::$content  = "彼黍离离，彼稷之苗。行迈靡靡，中心摇摇。知我者，谓我心忧；不知我者，谓我何求。悠悠苍天，此何人哉？<br>";
                self::$content .= "彼黍离离，彼稷之穗。行迈靡靡，中心如醉。知我者，谓我心忧；不知我者，谓我何求。悠悠苍天，此何人哉？<br>";
                self::$content .= "彼黍离离，彼稷之实。行迈靡靡，中心如噎。知我者，谓我心忧；不知我者，谓我何求。悠悠苍天，此何人哉？<br>";
                break;
            }

            case $no == 2 :{
                self::$title = "《诗经·国风·周南·桃夭》<br><br>";
                self::$content  = "桃之夭夭，灼灼其华。之子于归，宜其室家<br>";
                self::$content .= "桃之夭夭，有蕡其实。之子于归，宜其家室<br>";
                self::$content .= "桃之夭夭，其叶蓁蓁。之子于归，宜其家人<br>";
                break;
            }

            case $no == 3 :{
                self::$title = "《诗经·国风·卫风·木瓜》<br><br>";
                self::$content  = "投我以木瓜，报之以琼琚。匪报也，永以为好也。<br>";
                self::$content .= "投我以木桃，报之以琼瑶。匪报也，永以为好也。<br>";
                self::$content .= "投我以木李，报之以琼玖。匪报也，永以为好也。<br>";
                break;
            }

            case $no == 4 :{
                self::$title = "《诗经·国风·郑风·风雨》<br><br>";
                self::$content  = "风雨凄凄，鸡鸣喈喈。既见君子，云胡不夷。<br>";
                self::$content .= "风雨潇潇，鸡鸣胶胶。既见君子，云胡不瘳。<br>";
                self::$content .= "风雨如晦，鸡鸣不已。既见君子，云胡不喜。<br>";
                break;
            }

            case $no == 5 :{
                self::$title = "《诗经·国风·秦风·蒹葭》<br><br>";
                self::$content  = "蒹葭苍苍，白露为霜。所谓伊人，在水一方，溯洄从之，道阻且长。溯游从之，宛在水中央。<br>";
                self::$content .= "蒹葭萋萋，白露未晞。所谓伊人，在水之湄。溯洄从之，道阻且跻。溯游从之，宛在水中坻。<br>";
                self::$content .= "蒹葭采采，白露未已。所谓伊人，在水之涘。溯洄从之，道阻且右。溯游从之，宛在水中沚。<br>";
                break;
            }

            case $no == 6 :{
                self::$title = "《诗经·国风·郑风·子衿》<br><br>";
                self::$content  = "青青子衿，悠悠我心。纵我不往，子宁不嗣音？<br>";
                self::$content .= "青青子佩，悠悠我思。纵我不往，子宁不来？<br>";
                self::$content .= "挑兮达兮，在城阙兮。一日不见，如三月兮。<br>";
                break;
            }

            case $no == 7 :{
                self::$title = "《诗经·国风·陈风·月出》<br><br>";
                self::$content  = "月出皎兮，佼人僚兮。舒窈纠兮，劳心悄兮。<br>";
                self::$content .= "月出皓兮，佼人懰兮。舒懮受兮，劳心慅兮。<br>";
                self::$content .= "月出照兮，佼人燎兮。舒夭绍兮，劳心惨兮。<br>";
                break;
            }
        }
    }
}