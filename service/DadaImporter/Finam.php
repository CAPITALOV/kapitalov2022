<?php

namespace app\service\DadaImporter;

use cs\services\Url;
use cs\services\VarDumper;
use yii\base\Object;
use yii\helpers\ArrayHelper;

class Finam extends Object implements DadaImporterInterface
{
    private $url    = 'http://export.finam.ru';

    public $path    = '/SBERP_150701_150818.txt';

    public $params;
    
    public $default = [
        'market'    => 1,
        'em'        => 23,
        'p'         => 8,                     // шаг, 8 - по дням
        'f'         => 'SBERP_150805_150805', // название файла
        'e'         => '.txt',                // расширение файла с точкой
        'dtf'       => 1,
        'tmf'       => 1,
        'MSOR'      => 1,
        'mstimever' => 0,
        'sep'       => 1,
        'sep2'      => 1,
        'datf'      => 4,                     // формат выводимых данных 'TICKER, PER, DATE, TIME, OPEN, HIGH, LOW, CLOSE, VOL', ...
        'at'        => 0,                     // использовать заголовок в генерируемом документе? 1 - да, 0 - нет
    ];

    /**
     * @inheritdoc
     */
    public function import($start, $end = null)
    {
        if (is_null($end)) {
            $end = gmdate('Y-m-d');
        }
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $params = ArrayHelper::merge($this->default, $this->params);
        $params['from'] = $start->format('d.m.Y');
        $params['df'] = (int)$start->format('j');
        $params['mf'] = (int)$start->format('n') - 1;
        $params['yf'] = (int)$start->format('Y');
        $params['to'] = $end->format('d.m.Y');
        $params['dt'] = (int)$end->format('j');
        $params['mt'] = (int)$end->format('n') - 1;
        $params['yt'] = $end->format('Y');
        $params['cn'] = $params['code'];

        $u = new Url($this->url . $this->path, $params);
        $url = (string) $u;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($ch);
        curl_close($ch);

        $arr = explode("\n", $out);
        $ret = [];
        foreach ($arr as $row) {
            if (trim($row) != '') {
                $items = explode(',', $row);
                $ret[] = [
                    'date' => substr($items[2],0,4) . '-' . substr($items[2],4,2) . '-' . substr($items[2],6,2),
                    'kurs' => (float)trim($items[4]),
                ];
            }
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function importCandels($start, $end = null)
    {
        if (is_null($end)) {
            $end = gmdate('Y-m-d');
        }
        $start = new \DateTime($start);
        $end = new \DateTime($end);
        $params = ArrayHelper::merge($this->default, $this->params);
        $params['from'] = $start->format('d.m.Y');
        $params['df'] = (int)$start->format('j');
        $params['mf'] = (int)$start->format('n') - 1;
        $params['yf'] = (int)$start->format('Y');
        $params['to'] = $end->format('d.m.Y');
        $params['dt'] = (int)$end->format('j');
        $params['mt'] = (int)$end->format('n') - 1;
        $params['yt'] = $end->format('Y');
        $params['cn'] = $params['code'];
        $params['datf'] = 5;

        $u = new Url($this->url . $this->path, $params);
        $url = (string) $u;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $out = curl_exec($ch);
        curl_close($ch);

        $arr = explode("\n", $out);
        $ret = [];
        foreach ($arr as $row) {
            if (trim($row) != '') {
                $items = explode(',', $row);
                $ret[] = [
                    'date'   => substr($items[0], 0, 4) . '-' . substr($items[0], 4, 2) . '-' . substr($items[0], 6, 2),
                    'open'   => (float)trim($items[2]),
                    'high'   => (float)trim($items[3]),
                    'low'    => (float)trim($items[4]),
                    'close'  => (float)trim($items[5]),
                    'volume' => (float)trim($items[6]),
                ];
            }
        }

        return $ret;
    }
}