<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StokObatExport implements FromView
{
    protected $tanggal;
    protected $stokObats;
    protected $user;

    public function __construct($tanggal, $stokObats, $user)
    {
        $this->tanggal = $tanggal;
        $this->stokObats = $stokObats;
        $this->user = $user;
    }

    public function view(): View
    {
        return view('export.stokobat', [
            'tanggal' => $this->tanggal,
            'stokObats' => $this->stokObats,
            'user' => $this->user,
        ]);
    }
}
