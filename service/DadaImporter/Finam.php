<?php

namespace app\service\DadaImporter;

use cs\services\Url;
use cs\services\VarDumper;

class Finam implements DadaImporterInterface
{
    private $url    = 'http://195.128.78.52/SBERP_150701_150818.txt';

    private $params = [
        'market'    => 1,
        'em'        => 23,
        'code'      => 'SBERP',
        'df'        => 1,
        'mf'        => 6,
        'yf'        => 2015,
        'from'      => '01.07.2015',
        'dt'        => 18,
        'mt'        => 7,
        'yt'        => 2015,
        'to'        => '18.08.2015',
        'p'         => 8,
        'f'         => 'SBERP_150805_150805',
        'e'         => '.txt',
        'cn'        => 'SBERP',
        'dtf'       => 1,
        'tmf'       => 1,
        'MSOR'      => 1,
        'mstimever' => 0,
        'sep'       => 1,
        'sep2'      => 1,
        'datf'      => 4,
        'at'        => 0,
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
        $this->params['from'] = $start->format('d.m.Y');
        $this->params['df'] = (int)$start->format('j');
        $this->params['mf'] = (int)$start->format('n') - 1;
        $this->params['yf'] = (int)$start->format('Y');
        $this->params['to'] = $end->format('d.m.Y');
        $this->params['dt'] = (int)$end->format('j');
        $this->params['mt'] = (int)$end->format('n') - 1;
        $this->params['yt'] = $end->format('Y');
        $u = new Url($this->url, $this->params);
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
} 