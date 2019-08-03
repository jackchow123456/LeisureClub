<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class EntryExport implements FromCollection
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return new Collection([
            ['申请人', '单位名', '汇款金额', '汇款日期'],
            ['张三','张三的公司', '0.01', '2019-07-17'],
            ['李四','李四的公司', '0.02', '2019.07.17'],
            ['王五','王五的公司', '0.03', '2019.07.17'],
        ]);
    }
}
