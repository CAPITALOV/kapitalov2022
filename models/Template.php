<?php

namespace app\models;

use yii\db\Query;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use Suffra\Config as SuffraConfig;

class Template
{
    private $fields;

    public function __construct($fields) {
        $this->fields = $fields;
    }

    /**
     * Ищет пользователя по идентификатору
     * @param integer $id  идентификатор пользователя
     * @return Voting
     */
    public static function find($id)
    {
        $query = new Query();
        $row = $query->select('*')->from('cms_goods')->where(['id' => $id])->one();
        if (!$row) {
            return null;
        } else {
            return new self($row);
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     * [0 => 'footer', ... ]
     */
    public static function getPositionList()
    {
        $base = SuffraConfig::getBasePath();
        $base .= '/templates/_default_/positions.txt';
        $data = file_get_contents($base);
        $strings = explode("\r", $data);
        if (count($strings) == 1) $strings = explode("\n", $data);
        $arr = [];
        foreach($strings as $str) {
            if (!StringHelper::startsWith(trim($str),'#')) {
                if (trim($str) != '') {
                    $arr[] = trim($str);
                }
            }
        }
        return $arr;
    }

    /**
     * @return array
     * ['footer' => 'footer', ... ]
     */
    public static function getPositionList2() {
        $ret = [];
        $u = self::getPositionList();
        foreach ($u as $item) {
            $ret[$item] = $item;
        }
        return $ret;
    }
}
