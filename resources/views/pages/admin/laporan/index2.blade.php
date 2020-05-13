@extends('layouts.admin')
@section('title')

@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Laporan Penjualan</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Wellcome to Laporan Penjualan</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
            </ol>
        </div>
    </div>
</div>

@php
    function rupiah($angka) {
        $rupiah = "Rp " . number_format($angka,0,',','.');
        return $rupiah;
    }
@endphp

<div class="page-content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card m-b-20">
                <div class="card-body">
                    <h4 class="mt-0 header-title" style="font-size: 22px"><i class="mdi mdi-file-document-box mr-2"></i>
                        Laporan
                        Pembelian</h4>
                    <hr>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="input-group">
                                <h6>DATA TAHUN</h6>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn btn-primary" id="basic-addon1">Pilih Tahun</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Username"
                                    aria-describedby="basic-addon1" >
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <div class="col-lg-4">

                        </div>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <h6>Pengeluaran</h6>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text btn btn-primary" id="basic-addon1">Rp</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Username"
                                    aria-describedby="basic-addon1" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <!-- Left col -->
        <section class="col-lg-12 connectedSortable">
            <div class="box box-primary">
                <figure class="highcharts-figure">
                    <div id="container1"></div>
                </figure>
            </div>
        </section>
        <!-- /.Left col -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{route('admin.pembelian.create')}}" class="btn btn-danger btn-flat d-inline"
                        style="float: right"><i class="fa fa-plus mr-2"></i>Add Pembelian</a>
                    <h4>List Pembelian</h4>
                    <hr>
                    <div class="row input-daterange mb-3">
                        <div class="col-md-4">
                            <input type="text" name="from_date" id="from_date" class="form-control"
                                placeholder="From Date" readonly />
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="to_date" id="to_date" class="form-control" placeholder="To Date"
                                readonly />
                        </div>
                        <div class="col-md-4">
                            <button type="button" name="filter" id="filter" class="btn btn-primary">Filter</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-default">Refresh</button>
                        </div>
                    </div>
                    <br />

                    <!-- /.box-header -->

                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap datatable" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Invoice Number</th>
                                <th>Tanggal pesan</th>
                                <th>Suppliers</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>No</th>
                                <th>Invoice Number</th>
                                <th>Tanggal pesan</th>
                                <th>Suppliers</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </tfoot>
                    </table>


                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript">
$(document).ready(function() {

    var table = $('#datatable-buttons').DataTable({
        aaSorting: [
                    [0, "DESC"]
                ],
                processing: true,
                serverSide: true,
                ajax: "{{route('admin.api.pembelian')}}",
                columns: [{
                        data: 'id',
                        sortable: true,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        width: '20'
                    },
                    {
                        data: 'invoice_number',
                        name: 'invoice_number'
                    },
                    {
                        data: 'tanggl_transaksi',
                        name: 'tanggl_transaksi'
                    },
                    {
                        data: 'supplier.name_supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'total_harga',
                        name: 'total_harga'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        width: '80'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '120px'
                    }
                ],
                columnDefs: [{
                    targets: 5,
                    render: function (data, type, row) {
                        var css1 = 'badge badge-danger ';
                        var css2 = 'badge badge-success';
                        var css3 = 'badge badge-dark';
                        if (data == 'FINISH') {
                            css1 = 'badge badge-success';
                            return '<h6><span class="' + css1 + '">' + data +
                            '</span></h6>';
                        }
                        if (data == 'PROCESS') {
                            css2 = 'badge badge-danger';
                            return '<h6><span class="' + css2 + '">' + data +
                            '</span></h6>';
                        }
                        if (data == 'CANCEL') {
                            css3 = 'badge badge-dark';
                            return '<h6><span class="' + css3 + '">' + data +
                            '</span></h6>';
                        }
                    }
                }],
            dom: 'lBfrtip',
            lengthChange: true,
            buttons: ['copy', 'excel', 'pdf', 'print'],
        });

        table.buttons().container()
            .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
} );
</script>

<script>
    jQuery(document).ready(function($){
        $('#mymodal').on('show.bs.modal', function(e){
            var button = $(e.relatedTarget);
            var modal = $(this);

            modal.find('.modal-body').load(button.data("remote"));
            modal.find('.modal-title').html(button.data("title"));
        });
    });
</script>

<div class="row">
    <div id="mymodal" class="modal fade bs-example-modal-lg" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <i class="fa fa-spinner fa-spin"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
