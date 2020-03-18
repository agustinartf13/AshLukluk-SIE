<?php

namespace App\Http\Controllers\Admin;

use App\Barang;
use App\BarangDetail;
use App\DetailPenjualan;
use App\Http\Controllers\Controller;
use App\Http\Requests\PenjualanValidRequest;
use App\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;


class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.admin.penjualan.index');
    }

    // api supplier get data
    public function apipenjualan()
    {
        $penjualan = Penjualan::all();
        return DataTables::of($penjualan)
            ->addColumn('action', function ($penjualan) {
                return '' .
                    '<a href="' . route('admin.penjualan.edit', ['penjualan' => $penjualan->id]) . '" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>' .
                    '&nbsp;<a href="" class="btn btn-danger btn-sm"><i class="fa fa-print"></i> Invoice</a>' .
                    '&nbsp;<a href="javascript:void(0)" id="delete"  data-id="' . $penjualan->id . '" class="delete btn btn-primary btn-sm"><i class="fa fa-trash"></i> Delete</button>';
            })->rawColumns(['action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $barangs = Barang::all();
        return view('pages.admin.penjualan.create', [
            'barangs' => $barangs,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach ($request->get('barang') as $key => $brg) {
            $stock = BarangDetail::find($request->get('barang')[$key]);
            $barangs = Barang::find($request->get('barang')[$key]);
            if ($request->get('qty')[$key] > $stock->stock) {
                return redirect()->route('admin.penjualan.create')->with('status', 'Stock' . $barangs->name_barang . 'Tidak Mencukupi');
            }
        }

        // create data penjualan
        $new_penjualan = new Penjualan;
        $new_penjualan->created_by = Auth::user()->id;
        $new_penjualan->updated_by = Auth::user()->id;

        $new_penjualan->name_pembeli = $request->get('name_pembeli');

        $new_penjualan->tanggal_transaksi =
            date('Y-m-d', strtotime($request->get('tanggal_transaksi')));

        $invoice = Penjualan::get('invoice_number')->last();
        if ($invoice === null) {
            $invoice_no = 5001;
        } else {
            $invoice_no = $invoice->invoice_number + 1;
        }
        $new_penjualan->invoice_number = $invoice_no;
        $new_penjualan->total_harga = 0;
        $new_penjualan->profit = 0;
        $new_penjualan->save();
        $penjualan_id = $new_penjualan->id;

        $total_harga = 0;
        $profit = 0;

        foreach ($request->get('barang') as $key => $brg) {
            $new_penjualan_barang = new DetailPenjualan;
            $new_penjualan_barang->penjualan_id = $penjualan_id;
            $new_penjualan_barang->barang_id = $brg;
            $new_penjualan_barang->qty = $request->get('qty')[$key];

            $barang = BarangDetail::find($request->get('barang')[$key]);
            $new_penjualan_barang->harga_jual = $barang->harga_jual;
            $new_penjualan_barang->harga_beli = $barang->harga_dasar;
            $new_penjualan_barang->save();

            $total_harga += $new_penjualan_barang->harga_jual * $new_penjualan_barang->qty;
            $profit += ($new_penjualan_barang->harga_jual - $new_penjualan_barang->harga_beli) * $new_penjualan_barang->qty;

            $new_stock = BarangDetail::find($request->get('barang')[$key]);
            $new_stock->stock -= $request->get('qty')[$key];
            $new_stock->save();
        }

        $new_penjualan = Penjualan::find($penjualan_id);
        $new_penjualan->total_harga = $total_harga;
        $new_penjualan->profit = $profit;
        $new_penjualan->save();

        return redirect()->route('admin.penjualan.create', ['id' => $penjualan_id])->with('status', 'penjualan successfully created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $barangs = Barang::all();
        return view('pages.admin.penjualan.edit', [
            'barangs' => $barangs
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}