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
 *                           если значение не будет задано то будет использовано самое малое значение из тех что даны в $rows
 * @param \DateTime $end   - конечное значение на графике
 *                           можно задать как \DateTime или string 'yyyy-mm-dd'
 *                           если значение не будет задано то будет использовано самое большое значение из тех что даны в $rows
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
use cs\services\VarDumper;
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
        if (is_null($this->start)) {
            $this->start = $this->getMin();
        }
        if (! ($this->start instanceof \DateTime)) {
            $this->start = new \DateTime($this->start);
        }
        if (is_null($this->end)) {
            $this->end = $this->getMax();
        }
        if (! ($this->end instanceof \DateTime)) {
            $this->end = new \DateTime($this->end);
        }
        VarDumper::dump($this);
    }

    public function run()
    {
        $y = [];
        VarDumper::dump($this);
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

        VarDumper::dump([
            'x' => $x,
            'y' => $y,
        ]);

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

    /**
     * Получает минимальную дату из $rows
     */
    public function getMin()
    {
        $min = null;
        foreach ($this->rows as $row) {
            $dateArray = ArrayHelper::getColumn($row, 'date');
            $dateArray = sort($dateArray);
            if (is_null($min)) {
                $min = $dateArray[0];
            } else {
                if (!$this->compare($min, $dateArray[0])) {
                    $min = $dateArray[0];
                }
            }
        }

        return $min;
    }

    /**
     * Получает минимальную дату из $rows
     */
    public function getMax()
    {
        $max = null;
        foreach ($this->rows as $row) {
            $dateArray = ArrayHelper::getColumn($row, 'date');
            $dateArray = sort($dateArray);
            $dateArray = array_reverse($dateArray);
            if (is_null($max)) {
                $max = $dateArray[0];
            } else {
                if ($this->compare($max, $dateArray[0])) {
                    $max = $dateArray[0];
                }
            }
        }

        return $max;
    }
}