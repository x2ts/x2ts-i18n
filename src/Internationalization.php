<?php
/**
 * Created by IntelliJ IDEA.
 * User: rek
 * Date: 2016/3/22
 * Time: 下午4:37
 */

namespace x2ts\i18n;

use x2ts\Component;
use x2ts\ComponentFactory as X;

class Internationalization extends Component {
    protected static $_conf = [
        'default' => 'En',
    ];

    protected static $messages = [];

    public static function getInstance(array $args, array $conf, string $confHash = '') {
        $language = $args[0] ?? $conf['default'] ?? static::$_conf['default'];
        if (!empty($language)) {
            $class = "\\lang\\$language";
            return new $class();
        }
        $acceptLanguage = X::router()->action->header('Accept-Language', '');
        if ($acceptLanguage) {
            preg_match_all(
                '#([\w\-, ]+); ?q=([01]\.\d)#',
                $acceptLanguage,
                $m,
                PREG_SET_ORDER
            );
            usort($m, function ($a, $b) {
                $a = (float) $a[2] * 100;
                $b = (float) $b[2] * 100;
                return $b - $a;
            });
            $langs = [];
            foreach ($m as $item) {
                $group = explode(',', $item[1]);
                foreach ($group as $lang) {
                    $lang = trim($lang);
                    if (!empty($lang))
                        $langs[] = str_replace('-', '', ucfirst($lang));
                }
            }
            foreach ($langs as $lang) {
                $class = '\\lang\\' . $lang;
                if (class_exists($class)) {
                    return new $class();
                }
            }
        }

        $class = '\\lang\\' . static::$_conf['default'];
        return new $class();
    }
}