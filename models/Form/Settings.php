<?php

namespace app\models\Form;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;
use \ErrorSupervisor;

/**
 * Settings
 */
class Settings extends Model {
    public $sitename;
    public $hometitle;
    public $homecom;
    public $siteoff;
    public $debug;
    public $offtext;
    public $keywords;
    public $metadesc;
    public $seourl;
    public $lang;
    public $sitemail;
    public $wmark;
    public $stats;
    public $template;
    public $splash;
    public $slight;
    public $back_btn;
    public $db_host;
    public $db_base;
    public $db_base_vote;
    public $db_user;
    public $db_pass;
    public $db_prefix;
    public $show_pw;
    public $short_pw;
    public $index_pw;
    public $fastcfg;
    public $ajax_preload;
    public $mailer;
    public $mailerFrom;
    public $mailerHost;
    public $mailerPort;
    public $mailerIsSsl;
    public $mailerUserLogin;
    public $mailerUserPassword;
    public $timezone;
    public $timediff;
    public $allow_ip;
    public $isViewErrors;
    public $isViewErrorsMask;
    public $logger_level;
    public $cookie_name;

    private static $fields = [
        'sitename',
        'hometitle',
        'homecom',
        'siteoff',
        'debug',
        'offtext',
        'keywords',
        'metadesc',
        'seourl',
        'lang',
        'sitemail',
        'wmark',
        'stats',
        'template',
        'splash',
        'slight',
        'back_btn',
        'db_host',
        'db_base',
        'db_base_vote',
        'db_user',
        'db_pass',
        'db_prefix',
        'show_pw',
        'short_pw',
        'index_pw',
        'fastcfg',
        'ajax_preload',

        'mailerHost',
        'mailerPort',
        'mailerIsSsl',
        'mailerFrom',
        'mailerUserLogin',
        'mailerUserPassword',


        'timezone',
        'timediff',
        'allow_ip',
        'isViewErrors',
        'isViewErrorsMask',
        'logger_level',
        'cookie_name',
    ];
    private static $fieldsInit = [
        'sitename',
        'hometitle',
        'homecom',
        'siteoff',
        'debug',
        'offtext',
        'keywords',
        'metadesc',
        'seourl',
        'lang',
        'sitemail',
        'wmark',
        'stats',
        'template',
        'splash',
        'slight',
        'back_btn',
        'db_host',
        'db_base',
        'db_base_vote',
        'db_user',
        'db_pass',
        'db_prefix',
        'show_pw',
        'short_pw',
        'index_pw',
        'fastcfg',
        'ajax_preload',
        'mailer',
        'timezone',
        'timediff',
        'allow_ip',
        'isViewErrors',
        'isViewErrorsMask',
        'logger_level',
        'cookie_name',
    ];

    public $isPossibleToSave;

    /**
     * @return array the validation rules.
     */
    public function rules() {
        return [
            [[
            ], 'required'],
            [[
                'siteoff',
                'logger_level',
                'stats',
                'ajax_preload',
                'isViewErrorsMask',
                'mailerPort',
            ], 'integer'],
            [[
                'isViewErrors',
                'sitename',
                'sitemail',
                'db_host',
                'db_base',
                'db_base_vote',
                'db_user',
                'db_pass',
                'db_prefix',
                'keywords',
                'metadesc',
                'offtext',
                'timezone',
                'template',
                'homecom',
                'cookie_name',

                'mailerHost',
                'mailerPort',
                'mailerFrom',
                'mailerIsSsl',
                'mailerUserLogin',
                'mailerUserPassword',
            ], 'string', 'min' => 1, 'max' => 200],
        ];
    }

    /**
     * Сохраняет настройки
     * @return boolean результат операции
     */
    public function update() {
        $strings = [];
        $strings[] = '<?php';
        $strings[] = 'if(!defined(\'VALID_CMS\')) { die(\'ACCESS DENIED\'); }';
        $strings[] = '$_CFG = [';
        $mailer = [];
        foreach (self::$fields as $field) {
            $value = $this->$field;
            if (!is_null($value)) {
                if ($field == 'isViewErrorsMask') {
                    $value = self::fieldIsViewErrorsMaskSave($value);
                    $strings[] = '    ' . '\'' . $field . '\' => ' . $value . ',';
                } else if ($field == 'mailerHost') {
                    $mailer['host'] = $value;
                } else if ($field == 'mailerPort') {
                    $mailer['port'] = $value;
                } else if ($field == 'mailerFrom') {
                    $mailer['from'] = $value;
                } else if ($field == 'mailerIsSsl') {
                    $mailer['isSsl'] = $value;
                } else if ($field == 'mailerUserLogin') {
                    $mailer['user']['login'] = $value;
                } else if ($field == 'mailerUserPassword') {
                    $mailer['user']['password'] = $value;
                } else {
                    $value = str_replace('\'', '\\\'', $value);
                    if (!is_numeric($value)) {
                        $value = "'" . $value . "'";
                    }
                    $strings[] = '    ' . '\'' . $field . '\' => ' . $value . ',';
                }
            }
        }
        $strings[] = '    ' . '\'mailer\' => [' . "\r\n" .
            '    ' . '    ' . '\'host\' => \'' . $mailer['host'] . '\'' . ',' . "\r\n" .
            '    ' . '    ' . '\'port\' => ' . (!isset($mailer['port'])? "''" : $mailer['port']) . ',' . "\r\n" .
            '    ' . '    ' . '\'isSsl\' => ' . (($mailer['isSsl'] == 1) ? 'true' : 'false') . ',' . "\r\n" .
            '    ' . '    ' . '\'user\' => [' . "\r\n" .
            '    ' . '    ' . '    ' . '\'login\' => \'' . $mailer['user']['login'] . '\'' . ',' . "\r\n" .
            '    ' . '    ' . '    ' . '\'password\' => \'' . $mailer['user']['password'] . '\'' . ',' . "\r\n" .
            '    ' . '    ' . '],' . "\r\n" .
            '    ' . '    ' . '\'from\' => \'' . $mailer['from'] . '\'' . ',' . "\r\n" .
            '    ' . '],' . "\r\n";
        $strings[] = '];';
        $data = join("\r\n", $strings);
        $path = SuffraConfig::getBasePath() . '/includes/config.inc.php';
        file_put_contents($path, $data);

        return true;
    }

    /**
     * Возвращает значение переменной isViewErrorsMask
     * например E_ERROR | E_WARNING | E_PARSE | E_NOTICE
     *
     * @param array $value массив идентификаторов ошибок [1,2,4, ...]
     *
     * @return string
     */
    private static function fieldIsViewErrorsMaskSave($value) {
        $ret = [];
        if ($value === '') {
            return '~E_ALL';
        }
        $array = $value;
        foreach ($array as $item) {
            $ret[] = ErrorSupervisor::friendlyErrorType($item);
        }

        return join(' | ', $ret);
    }

    public function init() {
        $path = SuffraConfig::getBasePath() . '/includes/config.inc.php';
        define('VALID_CMS', 1);
        require $path;
        $config = $_CFG;

        foreach (self::$fieldsInit as $field) {
            if (array_key_exists($field, $config)) {
                if ($field == 'isViewErrorsMask') {
                    $this->$field = \ErrorSupervisor::getErrorType($config[ $field ]);
                } else if ($field == 'mailer') {
                    if (is_null($this->mailerFrom)) {
                        $this->mailerFrom = ArrayHelper::getValue($config, 'mailer.from', '');
                        $this->mailerHost = ArrayHelper::getValue($config, 'mailer.host', '');
                        $this->mailerPort = ArrayHelper::getValue($config, 'mailer.port', '');
                        $this->mailerIsSsl = ArrayHelper::getValue($config, 'mailer.isSsl', '');
                        $this->mailerUserLogin = ArrayHelper::getValue($config, 'mailer.user.login', '');
                        $this->mailerUserPassword = ArrayHelper::getValue($config, 'mailer.user.password', '');
                    }
                } else {
                    $this->$field = $config[ $field ];
                }
            }
        }
        $this->isPossibleToSave = substr(sprintf('%o', fileperms($path)), -4);
    }

    /**
     * Конструктор
     */
    public function __construct() {

    }
}
