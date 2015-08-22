<?php

namespace app\models\Form;

use app\models\StockKurs;
use app\models\StockPrognosis;
use app\models\StockPrognosisBlue;
use app\models\StockPrognosisRed;
use app\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 *
 */
class StockKursImport extends Model
{
    public $fileRed;
    public $fileBlue;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                [
                    'fileRed',
                    'fileBlue',
                ],
                'file',
            ],
        ];
    }

    /**
     * Импортирует данные
     *
     * @return boolean
     */
    public function import($stock_id)
    {
        if ($this->validate()) {
            $dataArray = StockPrognosisRed::query()->select('date')->column();
            $rows = $this->get('fileRed', $stock_id, $dataArray);
            StockPrognosisRed::batchInsert(['stock_id', 'date', 'delta'], $rows);

            $dataArray = StockPrognosisBlue::query()->select('date')->column();
            $rows = $this->get('fileBlue', $stock_id, $dataArray);
            StockPrognosisBlue::batchInsert(['stock_id', 'date', 'delta'], $rows);


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
     */
    public function get($fieldName, $stock_id, $dataArray)
    {
        $fileModel = UploadedFile::getInstance($this, $fieldName);
        if ($fileModel) {
            $data = file_get_contents($fileModel->tempName);
            $rows = explode("\n", $data);
            $new = [];
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
                if (!in_array($data, $dataArray)) {
                    $new[] = [
                        $stock_id,
                        $data,
                        $delta,
                    ];
                }
            }

            return $new;
        } else {
            return [];
        }
    }
}
