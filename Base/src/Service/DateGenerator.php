<?php

namespace App\Service;

class DateGenerator
{
    public function getMonthlist(): array
    {
        $list = [];

        for ($m=1; $m<=12; $m++) {
            $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
            $list[$m] = $month;
        }

        return $list;
    }
}