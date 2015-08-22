<?php

namespace app\models\Form;

use app\models\StockKurs;
use app\models\StockPrognosis;
use app\models\StockPrognosisBlue;
use app\models\StockPrognosisRed;
use app\models\User;
use cs\services\VarDumper;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 *
 */
class StockKursImport extends \cs\base\BaseForm
{
    /** @var  \DateTime */
    public $dateStart;

    /** @var  \DateTime */
    public $dateEnd;

    /** @var  bool Заменять уже имеющиеся данные?
     *                                  true - если в таблице уже есть курс на эту дату то он будет перезатерт
     *                                  false - если в таблице уже есть курс на эту дату то он сохранится
     */
    public $isReplaceExisting;

    public function __construct($config = [])
    {
        self::$fields = [
            [
                'dateStart',
                'Начало',
                1,
                'cs\Widget\DatePicker\Validator',
                'widget' => ['\cs\Widget\DatePicker\DatePicker',
                    [
                        'dateFormat' => 'php:d.m.Y',
                    ]
                ]
            ],
            [
                'dateEnd',
                'Конец',
                1,
                'cs\Widget\DatePicker\Validator',
                'widget' => ['\cs\Widget\DatePicker\DatePicker',
                    [
                        'dateFormat' => 'php:d.m.Y',
                    ]
                ]
            ],
            [
                'isReplaceExisting',
                'Заменять уже имеющиеся данные?',
                0,
                'cs\Widget\CheckBox2\Validator',
                'widget' => [
                    'cs\Widget\CheckBox2\CheckBox', [
                    ]
                ]
            ],
        ];
        parent::__construct($config);
    }

    /**
     * Импортирует данные
     *
     * @return boolean
     */
    public function import($stock_id)
    {
        if ($this->validate()) {
            VarDumper::dump($this);
            self::importData($stock_id, $this->dateStart->format('Y-m-d'), $this->dateEnd->format('Y-m-d'), $this->isReplaceExisting);

            return true;
        } else {
            return false;
        }
    }

    /**
     * Импортирует данные с Finam в таблицу курсов
     *
     * @param int    $stock_id
     * @param string $start             дата 'yyyy-mm-dd'
     * @param string $end               дата 'yyyy-mm-dd'
     * @param bool   $isReplaceExisting Заменять уже имеющиеся данные
     *                                  true - если в таблице уже есть курс на эту дату то он будет перезатерт
     *                                  false - если в таблице уже есть курс на эту дату то он сохранится
     *
     * @throws \yii\base\InvalidConfigException
     */
    public static function importData($stock_id, $start, $end, $isReplaceExisting = false)
    {
        $data = \app\service\DadaImporter\Data::getFirst($stock_id);
        $importer = Yii::createObject($data);
        $data = $importer->import($start, $end);
        $dateArrayRows = StockKurs::query(['between', 'date', $start, $end])->select(['date'])->andWhere(['stock_id' => $stock_id])->column();
        $insert = [];
        $update = [];
        foreach ($data as $row) {
            if (in_array($row['date'], $dateArrayRows)) {
                $update[ $row['date'] ] = $row['kurs'];
            } else {
                $insert[] = [
                    $stock_id,
                    $row['date'],
                    $row['kurs'],
                ];
            }
        }
        StockKurs::batchInsert(['stock_id', 'date', 'kurs'], $insert);
        if ($isReplaceExisting) {
            foreach ($update as $date => $kurs) {
                (new Query())->createCommand()->update(StockKurs::TABLE, ['kurs' => $kurs], ['date' => $date, 'stock_id' => $stock_id])->execute();
            }
        }
    }
}
