<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait GenerateCode
{
    protected function getKode()
    {
        $q   = DB::table('code_generator')->select(DB::raw('MAX(RIGHT(code_number,9)) as kd_max'));
        $prx = 'INV-NS-' . date('y') . '-' . date('m') . '-';
        if ($q->count() > 0) {
            foreach ($q->get() as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd  = $prx . sprintf('%09s', $tmp);
            }
        } else {
            $kd = $prx . '000000001';
        };

        DB::table('code_generator')->insert([
            'code_number' => $kd
        ]);

        return $kd;
    }
}
