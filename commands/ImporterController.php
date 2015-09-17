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
        $rows = Stock::query(['not', [
            'finam_code' => null,
        ]
        ])->all();

        foreach($rows as $row) {
            $stock_id = $row['id'];
            $this->log('Попытка получить данные для: ' . $row['name']);

            $importer = [
                'params'   => [
                    'market'    => $row['finam_market'],
                    'em'        => $row['finam_em'],
                    'code'      => $row['finam_code'],       // кодовый шифр продукта
                ],
            ];
            $class = new \app\service\DadaImporter\Finam($importer);
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
    /**
     * Импортирует курсы сразу для всех индексов
     */
    public function actionCandels()
    {
        $rows = Stock::query(['not', [
            'finam_code' => null,
        ]
        ])->all();

        foreach($rows as $row) {
            $this->log('Попытка получить данные для: ' . $row['name']);

            $start = (new \DateTime())->sub(new \DateInterval('P7D'));
            $end = (new \DateTime());
            $result = \app\models\Form\StockKursImport::importCandels($row['id'], $start, $end);
            $new = $result['insert'];
            if (count($new) > 0) {
                \Yii::info('Импортированы данные: ' . VarDumper::dumpAsString($new), 'cap\\importer\\index');
                $this->log('Импортированы данные: ' . VarDumper::dumpAsString($new));
            } else {
                $this->log('Нечего импортировать');
            }
        }
    }

    public function log($message, $isNewLine = true)
    {
        echo $message;
        if ($isNewLine) {
            echo "\n";
        }
    }

    public function actionReload()
    {
        foreach(Stock::query(['not', ['finam_code' => null]])->select('id, name')->all() as $row) {
            $this->log('Попытка получить данные для: ' . $row['name'] . "  ", false);
            $row2 = StockKurs::query(['stock_id' => $row['id']])
                ->select([
                    'min(`date`) as min',
                    'max(`date`) as max',
                ])->one();
            \app\models\Form\StockKursImport::importCandels($row['id'], $row2['min'], $row2['max'], true);
            $this->log('готово');
        }
    }
}
