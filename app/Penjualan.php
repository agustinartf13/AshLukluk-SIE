<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualans';
    protected $primaryKey = 'id';
    protected $fillable = [
        'tanggal_trasnaksi', 'name_pembeli', 'alamat', 'no_telphone', 'invoice_number', 'total_harga', 'profit', 'created_by', 'updated_by'
    ];

    public function barangs() {
        return $this->belongsTo('App\Barang');
    }

    public function dtlbarangs() {
        return $this->belongsTo(BarangDetail::class, 'id');
    }

    public function dtlpenjualans() {
        return $this->hasMany(DetailPenjualan::class, 'penjualan_id', 'id');
    }
}