<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Stock;
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
     * Импортирует курсы сразу для всех индексов
     */
    public function actionIndex()
    {
        $rows = Stock::query()->select('id, name')->all();

        foreach($rows as $row) {
            $stock_id = $row['id'];
            $this->log('Попытка получить данные для: ' . $row['name']);
            // получаю список импортеров
            $importList = [];
            foreach (\app\service\DadaImporter\Data::$importerData as $d) {
                if ($d['stock_id'] == $stock_id) {
                    unset($d['stock_id']);
                    $importList[] = $d;
                }
            }
            foreach($importList as $importer) {
                $class = $importer['class'];
                unset($importer['class']);
                /** @var \app\service\DadaImporter\DadaImporterInterface $class */
                $class = new $class($importer);

                $date = new \DateTime();
                $date->sub(new \DateInterval('P7D'));
                $data = $class->import($date->format('Y-m-d'));
                // стратегия: Если данные есть то, они не трогаются
                $dateArray = ArrayHelper::getColumn($data, 'date');
                sort($dateArray);
                $rows2 = StockKurs::query(['between', 'date', $dateArray[0], $dateArray[count($dateArray)-1]])->andWhere(['stock_id' => $stock_id])->all();
                $dateArrayRows = ArrayHelper::getColumn($rows2, 'date');
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
                    $this->log('Импортированы данные: ' . VarDumper::dumpAsString($new));

                    StockKurs::batchInsert(['stock_id', 'date', 'kurs'], $new);
                } else {
                    $this->log('Нечего импортировать');
                }
            }
        }
    }

    public function log($message)
    {
        echo $message;
        echo "\n";
    }
}
