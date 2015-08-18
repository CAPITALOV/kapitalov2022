<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\StockKurs;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * предназначен для импорта данных из внешних источников
 */
class ImporterController extends Controller
{
    /**
     */
    public function actionIndex()
    {
        $ids = [1];
        foreach($ids as $stock_id) {
            $date = new \DateTime();
            $date->sub(new \DateInterval('P7D'));
            $data = (new \app\service\DadaImporter\Finam())->import($date->format('Y-m-d'));
            // стратегия: Если данные есть то, они не трогаются
            $dateArray = ArrayHelper::getColumn($data, 'date');
            sort($dateArray);
            $rows = StockKurs::query(['between', 'date', $dateArray[0], $dateArray[count($dateArray)-1]])->andWhere(['stock_id' => $stock_id])->all();
            $dateArrayRows = ArrayHelper::getColumn($rows, 'date');
            $new = [];
            foreach($data as $row) {
                if (!in_array($row['date'], $dateArrayRows)) {
                    $new[] = [
                        $stock_id,
                        $row['date'],
                        $row['kurs'],
                    ];
                }
            }
            if (count($new) > 0) {
                \Yii::info('Импортированы данные: ' . VarDumper::dumpAsString($new), 'gs\\importer\\index');
                echo 'Импортированы данные: ' . VarDumper::dumpAsString($new);

                StockKurs::batchInsert(['stock_id', 'date', 'kurs'], $new);
            }
        }
    }
}
