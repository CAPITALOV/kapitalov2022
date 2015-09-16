<?php

namespace app\models\Form;

use app\models\StockKurs;
use app\models\StockPrognosis;
use app\models\StockPrognosisBlue;
use app\models\StockPrognosisRed;
use app\models\User;
use cs\base\BaseForm;
use cs\services\VarDumper;
use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 *
 */
class StockPrognosisImport extends BaseForm
{
    public $fileRed;
    public $fileBlue;
    /** @var  bool Заменять уже имеющиеся данные?
     *                                  true - если в таблице уже есть курс на эту дату то он будет перезатерт
     *                                  false - если в таблице уже есть курс на эту дату то он сохранится
     */
    public $isReplaceExisting;

    public function __construct($fields = []){
        self::$fields = [
            [
                'fileRed',
                'Красная линия',
                0,
                'string'
            ],
            [
                'fileBlue',
                'Синяя линия',
                0,
                'string'
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
        parent::__construct($fields);
    }

    /**
     * Импортирует данные
     *
     * @return boolean
     */
    public function import($stock_id)
    {
        if ($this->validate()) {
            // выбираю уже имеющиеся данные
            $dataArray = StockPrognosisRed::query(['stock_id' => $stock_id])->select('date')->column();
            $rows = $this->get('fileRed', $stock_id, $dataArray);
            VarDumper::dump($rows);
            if (count($rows['insert']) > 0) {
                StockPrognosisRed::batchInsert(['stock_id', 'date', 'delta'], $rows['insert']);
            }
            if ($this->isReplaceExisting) {
                foreach ($rows['update'] as $date => $kurs) {
                    (new Query())->createCommand()->update(StockPrognosisRed::TABLE, ['delta' => $kurs], ['date' => $date, 'stock_id' => $stock_id])->execute();
                }
            }

            // выбираю уже имеющиеся данные
            $dataArray = StockPrognosisBlue::query(['stock_id' => $stock_id])->select('date')->column();
            $rows = $this->get('fileBlue', $stock_id, $dataArray);
            if (count($rows['insert']) > 0) {
                StockPrognosisBlue::batchInsert(['stock_id', 'date', 'delta'], $rows['insert']);
            }
            if ($this->isReplaceExisting) {
                foreach ($rows['update'] as $date => $kurs) {
                    (new Query())->createCommand()->update(StockPrognosisBlue::TABLE, ['delta' => $kurs], ['date' => $date, 'stock_id' => $stock_id])->execute();
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * Выбирает из файла данные и возвращает в виде массива
     *
     * @param string $fieldName
     * @param integer $stock_id
     * @param array $dataArray
     *
     * @return array
     * [
     *     'insert' => array массив данных для вставки
     *     'update' => array массив данных для обновления
     * ]
     */
    public function get($fieldName, $stock_id, $dataArray)
    {
        $fileModel = UploadedFile::getInstance($this, $fieldName);
        if ($fileModel) {
            $data = file_get_contents($fileModel->tempName);
            $rows = explode("\n", $data);
            $new = [];
            $update = [];
            foreach ($rows as $row) {
                $items = explode(' ', $row);
                $c = 1;
                foreach ($items as $i) {
                    if (trim($i) != '') {
                        if ($c == 1) {
                            $data = trim($i);
                            $data = substr($data, 6, 4) . '-' . substr($data, 0, 2) . '-' . substr($data, 3, 2);
                            $c++;
                        } else {
                            $delta = trim($i);
                        }
                    }
                }
                if (in_array($data, $dataArray)) {
                    $update[ $data ] = $delta;
                } else {
                    $new[] = [
                        $stock_id,
                        $data,
                        $delta,
                    ];
                }
            }

            return [
                'insert' => $new,
                'update' => $update,
            ];
        } else {
            return [
                'insert' => [],
                'update' => [],
            ];
        }
    }
}
