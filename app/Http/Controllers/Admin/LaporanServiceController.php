<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LaporanServiceController extends Controller
{
    public function laporanService(Request $request)
    {
        if ($request->get('year') != '') {
            $year_today = $request->get('year');
        } else {
            $year_today = Carbon::now()->format('Y');
        }
        $month_today = Carbon::now()->format('m');

        // chart pendapatan di tahun
        $months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $profit = [];

        foreach ($months as $key => $month) {
            $service_laba = Service::selectRaw('CAST(sum(profit) as UNSIGNED) as profit')
            ->selectRaw('YEAR(tanggal_servis) year, MONTH(tanggal_servis) month')
            ->whereMonth('tanggal_servis', '=', $key + 1)
            ->whereYear('tanggal_servis', '=', $year_today)
            ->groupBy('year', 'month')
            ->first();

            if (!empty($service_laba)) {
                $profit[$key] = $service_laba->profit;
            } else {
                $profit[$key] = 0;
            }
        }

        $month_a = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $jasa = [];

        foreach ($month_a as $key => $month) {
            $jasa_laba = DB::table('services')
            ->join('details_service', 'services.id', '=', 'details_service.service_id')
            ->selectRaw('CAST(sum(details_service.harga_jasa) as UNSIGNED) as jasa')
            ->selectRaw('YEAR(tanggal_servis) year, MONTH(tanggal_servis) month')
            ->whereMonth('tanggal_servis', '=', $key + 1)
            ->whereYear('tanggal_servis', '=', $year_today)
            ->groupBy('year', 'month')
            ->first();

            if (!empty($jasa_laba)) {
                $jasa[$key] = $jasa_laba->jasa;
            } else {
                $jasa[$key] = 0;
            }
        }

        $month_b = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        $omset = [];

        foreach ($month_b as $key => $month) {
            $omset_a = DB::table('services')
            ->selectRaw('CAST(sum(sub_total) as UNSIGNED) as omset')
            ->selectRaw('YEAR(tanggal_servis) year, MONTH(tanggal_servis) month')
            ->whereMonth('tanggal_servis', '=', $key + 1)
            ->whereYear('tanggal_servis', '=', $year_today)
            ->groupBy('year', 'month')
            ->first();

            if (!empty($omset_a)) {
                $omset[$key] = $omset_a->omset;
            } else {
                $omset[$key] = 0;
            }
        }

        //chart service bulanan
        //    $services = DB::table('services')
        //         ->join('details_service', 'services.id', '=', 'details_service.service_id')
        //         ->join('barangs', 'barangs.id', '=', 'details_service.barang_id')
        //         ->select('barangs.name_barang as name_barang')
        //         ->selectRaw('cast(sum(details_service.qty) as UNSIGNED) as y')
        //         ->whereYear('services.tanggal_servis', $year_today)
        //         ->groupBy('barangs.name_barang')
        //         ->orderBy('services.sub_total', 'asc')
        //         ->get();
        // dd($services);

        // query pendapatan jasa
        $cari_jasa = DB::table('services')
            ->join('details_service', 'services.id', '=', 'details_service.service_id')
            ->selectRaw('sum(details_service.harga_jasa)as harga_jasa')
            ->whereYear('services.tanggal_servis', $year_today)  //mencari omset
            ->first();
        $total_jasa = $cari_jasa->harga_jasa;

        // query omset service tahun ini
        $cari_omset = DB::table('services')
            ->join('details_service', 'services.id', '=', 'details_service.service_id')
            ->selectRaw('sum(sub_total)as omset')
            ->whereYear('services.tanggal_servis', $year_today)  //mencari omset
            ->first();
        $total_omset = $cari_omset->omset;
        // dd($total_omset);

        // query mencari profit bulan ini
        $cari_profit = DB::table('services')
            ->selectRaw('sum(profit) as profit')
            ->whereYear('services.tanggal_servis', $year_today)  //mencari profit
            ->first();
        $total_profit = $cari_profit->profit;
        // dd($total_profit);

        //range custemer servis
        $cari_customer = DB::table('services')->selectRaw('sum(customer_servis) as customer')
            ->whereYear('services.tanggal_servis', $year_today)
            ->first();
        $total_customer = $cari_customer->customer;
        // dd($total_customer);

        // customer paling banyak ganti sparepart
        $rank_customer = DB::table('services')
            ->join('details_service', 'services.id', '=', 'details_service.service_id')
            ->select('services.customer_servis as customer_servis')
            ->selectRaw('cast(sum(details_service.qty)as UNSIGNED) as jumlah')
            ->whereYear('services.tanggal_servis', $year_today)
            ->groupBy('services.customer_servis')
            ->orderBy('jumlah', 'DESC')
            ->limit(1)
            ->get();
        // dd($rank_customer);

        // sparepart paling banyak di beli
        $rank_barang = DB::table('services')
            ->join('details_service', 'services.id', '=', 'details_service.service_id')
            ->join('barangs', 'barangs.id', '=', 'details_service.barang_id')
            ->join('details_barang', 'barangs.id', '=', 'details_barang.barang_id')
            ->select('barangs.name_barang as name_barang')
            ->selectRaw('cast(sum(details_service.qty)as UNSIGNED) as jumlah')
            ->whereYear('services.tanggal_servis', $year_today)
            ->groupBy('barangs.name_barang')
            ->orderBy('jumlah', 'desc')
            ->limit(1)
            ->get();
        // dd($rank_barang);

        return view('pages.admin.laporan.index3', [
            'year_today' => $year_today, 'month_today' => $month_today,
            'months' => $months, 'profit' => $profit,
            'total_omset' => $total_omset, 'total_profit' => $total_profit,
            'total_customer' => $total_customer, 'rank_customer' => $rank_customer,
            'rank_barang' => $rank_barang , 'total_jasa' => $total_jasa, 'jasa' => $jasa, 'omset' => $omset
        ]);
    }

    // api data service datatabls
    public function apiservice(Request $request)
    {
        if (request()->ajax()) {
            if (!empty($request->from_date)) {
                $service = Service::with('dtlservice')->with('motor')
                ->whereBetween('tanggal_servis', array($request->from_date, $request->to_date))
                ->orderBy('id', 'DESC')
                ->get();
            } else {
                $service = Service::with('dtlservice')->with('motor')->orderBy('id', 'DESC')
                ->get();
            }

            return DataTables::of($service)
            ->addColumn('action', function ($service) {
                return '' .
                    '&nbsp;<a href="#mymodal" data-remote="' . route('admin.servis.show', $service->id) . '" data-toggle="modal" data-target="#mymodal" data-title="Invoice Number #' . $service->invocie_number . '" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>' .
                    '&nbsp;<a href="' . route('admin.servis.invoice', ['id' => $service->id]) . '" class="btn btn-danger btn-sm"><i class="fa fa-print"></i></a>';
            })->rawColumns(['action'])->make(true);
            return response()->toJson(['service' => $service]);

        }
   }
}
