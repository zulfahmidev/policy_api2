<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Member;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        // dd($this->getMBD());
        $data["moons"] = explode(", ", "januari, februari, mare, april, mei, juni, juli, agustus, september, oktober, november, desember");
        $data['mbd'] = $this->getMBD();
        // dd($data['mbd']);
        return view('admin.pages.dashboard.index', $data);
    }
    
    public function getMBD() {
        $mbd = Member::where('born_at', '!=', null)->select('id', 'name', 'born_at')->get()->toArray();
        $rows = [];
        for ($i=0; $i<12; $i++) {
            $rows[$i] = [];
            for ($i2=0; $i2<32; $i2++) {
                $rows[$i][$i2] = [];
            }
        }

        
        foreach ($mbd as $v) {
            $time = strtotime($v['born_at']);
            $rows[(int) date('m', $time) - 1][(int)date('d', $time) - 1][] = $v;
            // echo $v['name'] . ' - ' . date('d/m', strtotime($v["born_at"])) . '<br>';
        }
        // die;
        return $rows;
    }
}
