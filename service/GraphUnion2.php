<?php

namespace app\service;

/**
 * Объединяет несколько графиков в один
 * из формата
 * [
 *     [[
 *        'date' => 'yyyy-mm-dd'
 *        'kurs' => float
 *     ],...],
 *     [[
 *        'date' => 'yyyy-mm-dd'
 *        'red' => float
 *     ],...],
 *     [[
 *        'date' => 'yyyy-mm-dd'
 *        'blue' => float
 *     ],...],
 * ]
 * в формат
 * [
 *     [[
 *        'date' => 'yyyy-mm-dd'
 *        'kurs' => float
 *        'red' => float
 *        'blue' => float
 *     ],...],
 * ]
 * Входящие графики должны быть отсортирваны по возрастанию даты
 *
 * Стратегия совмещения:
 * вычисляется минимум всех графиков
 * вычисляется максимум всех графиков
 * на эту шкалу наносятся значения всех графиков не изменяясь и строго соответствуя имени значения
 *
 */
use cs\services\VarDumper;
use cs\web\Exception;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class GraphUnion2 extends Object
{
    public $lines;

    public function run()
    {
        // получаю минимальное значение из первого графика и максимальное значение
        $x = $this->getMinMax();
        $new = [];
        $newlines = $this->transform();
        for($i = new \DateTime($x['min']); $i->format('Y-m-d') < $x['max']; $i->add(new \DateInterval('P1D'))) {
            $newItem = [
                'date' => $i->format('Y-m-d'),
            ];
            foreach($newlines as $line) {
                $newItem[ $line['field'] ] = \yii\helpers\ArrayHelper::getValue($line['data'], $i->format('Y-m-d'), null);
            }
            $new[] = $newItem;
        }

        return $new;
    }

    /**
     * Трансформирует графики
     * каждая линия преобразуется из
     * [[
     *     'date' => 'yyyy-mm-dd',
     *     'kurs' => float
     * ],...]
     * в
     * [
     *     'field' => 'kurs',
     *     'data' => [ 'yyyy-mm-dd' => float, ...]
     * ]
     *
     * @throws \cs\services\Exception
     * @return array
     * [[
     *     'field' => 'kurs',
     *     'data' => [ 'yyyy-mm-dd' => float, ...]
     * ], ...]
     */
    private function transform()
    {
        $ret = [];
        foreach($this->lines as $line) {
            $field = '';
            foreach($line[0] as $k => $v) {
                if ($k != 'date') $field = $k;
            }
            if ($field == '') {
                throw new \cs\services\Exception('Нет данных');
            }
            $data = \yii\helpers\ArrayHelper::map($line, 'date', $field);
            $ret[] = [
                'field' => $field,
                'data'  => $data,
            ];
        }

        return $ret;
    }

    /**
     * Статический метод для вызова класса
     *
     * @param array $options массив инициализируемых значений через инициализацию [[yii\base\Object]]
     *
     * @return mixed
     */
    public static function convert($config = [])
    {
        $item = new static($config);

        return $item->run();
    }

    /**
     * Получает минимальное и максимальое значение даты всех графиков $this->lines
     *
     * @return array
     * [
     *    'min' => 'yyyy-mm-dd'
     *    'max' => 'yyyy-mm-dd'
     * ]
     */
    public function getMinMax()
    {
        $min = null;
        $max = null;
        foreach($this->lines as $line) {
            if (is_null($min)) {
                $min = $line[0]['date'];
                $max = $line[count($line)-1]['date'];
            } else {
                $thisMin = $line[0]['date'];
                $thisMax = $line[count($line)-1]['date'];
                if ($thisMin < $min) $min = $thisMin;
                if ($thisMax > $max) $max = $thisMax;
            }
        }

        return [
            'min'=> $min,
            'max'=> $max,
        ];
    }
}