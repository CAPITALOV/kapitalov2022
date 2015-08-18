<?php

namespace app\service;

/**
 * Подготавливает табличные данные для вывода на график
 * где
 *
 * @param array $rows - массив линий графиков в виде значений из таблицы не сортированный
 *                    [
 *                       [
 *                          [
 *                              'date' => 'yyyy-mm-dd'
 *                              'kurs' => float
 *                          ], ...
 *                       ], ...
 *                    ]
 * @param \DateTime $start - стартовое значение на графике
 *                           можно задать как \DateTime или string 'yyyy-mm-dd'
 * @param \DateTime $end   - конечное значение на графике
 *                           можно задать как \DateTime или string 'yyyy-mm-dd'
 *
 * После экспорта все линии будут идти от `$start` до `$end`, если данных в таблице не хватает то они будут
 * дозаполнены значениями null
 *
 * В результате будет получен массив
 * [
 *    'x' => array - значения подписей оси Х
 *    'y' => [
 *           []  - значения оси y для каждого Х
 *           , ...
 *     ]
 * ]
 */
use cs\web\Exception;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class GraphExporter extends Object
{
    /** @var \DateTime */
    public $start;

    /** @var \DateTime */
    public $end;

    /** @var array данные для графика */
    public $rows;

    /**
     * @var string формат для значений оси X
     */
    public $formatX = 'd.m.Y';

    public function init()
    {
        // проверка на входящие данные
        if (!$this->compare($this->start, $this->end)) {
            throw new Exception('Дата end больше start');
        }
        if (! $this->start instanceof \DateTime) {
            $this->start = new \DateTime($this->start);
        }
        if (! $this->end instanceof \DateTime) {
            $this->end = new \DateTime($this->end);
        }
    }

    public function run()
    {
        $y = [];
        foreach($this->rows as $row) {
            $new = [];
            $arrayOfDate = ArrayHelper::getColumn($row, 'date');
            for ($i = $this->start; $this->compare($i, $this->end); $i->add(new \DateInterval('P1D'))) {
                $date = $i->format('Y-m-d');
                $value = ArrayHelper::getValue($arrayOfDate, $date, null);
                if ($value) {
                    $new[] = $date;
                } else {
                    $new[] = null;
                }
            }
            $y[] = $new;
        }

        $x = [];
        for ($i = $this->start; $this->compare($i, $this->end); $i->add(new \DateInterval('P1D'))) {
            $x[] = $i->format($this->formatX);
        }

        return [
            'x' => $x,
            'y' => $y,
        ];
    }

    /**
     * Сравнивает две даты
     *
     * @param \DateTime $d1
     * @param \DateTime $d2
     *
     * @return boolean
     * true - $d2 >= $d1
     */
    public function compare($d1, $d2)
    {
        return ($d2->format('U') - $d1->format('U')) >= 0;
    }

    /**
     * Статический метод для вызова класса
     *
     * @param array $options массив инициализируемых значений через инициализацию [[yii\base\Object]]
     *
     * @return mixed
     */
    public static function convert($options)
    {
        $item = new static($options);

        return $item->run();
    }
}