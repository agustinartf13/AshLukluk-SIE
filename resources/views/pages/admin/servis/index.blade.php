@extends('layouts.admin')
@section('title')

@endsection
@section('content')

<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">Data Service</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.servis.index')}}">Service</a></li>
                <li class="breadcrumb-item"><a href="{{route('admin.servis.create')}}"></a>Add Service</li>
            </ol>
        </div>
    </div>
</div>

<div class="page-content-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card m-b-20">
                <div class="card-body">
                    @if (session("status"))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-check"></i> Good Job!</h4>
                        {{session('status')}}
                    </div>
                    @endif
                    <h4 class="mt-0 header-title" style="font-size: 22px"><i class="fa fa-cart-arrow-down"></i> Data
                        Service</h4>
                    <a href="{{route('admin.servis.create')}}" class="btn btn-danger btn-flat d-inline"
                        style="float: right"><i class="fa fa-plus mr-2"></i>Add Service</a>
                    <br><br><br>
                    <table id="datatable" class="table table-bordered dt-responsive nowrap table-striped mt-5"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Invoice Number</th>
                                <th>Tanggal Service</th>
                                <th>No Polis</th>
                                <th>Customer Service</th>
                                <th>Tipe Motor</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                           <tr>
                            <th>No</th>
                            <th>Invoice Number</th>
                            <th>Tanggal Service</th>
                            <th>No Polis</th>
                            <th>Customer Service</th>
                            <th>Tipe Motor</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                           </tr>
                        </tfoot>
                        <tbody>
                            {{-- server Side --}}
                        </tbody>
                        </form>
                    </table>
                </div>
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    @endsection

    @section('js')
    <script text="text/javascript">
        $(document).ready(function () {
            $('#datatable').DataTable({
                aaSorting: [
                    [0, "DESC"]
                ],
                processing: true,
                serverSide: true,
                ajax: "{{route('admin.api.servis')}}",
                columns: [{
                        data: 'id',
                        sortable: true,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        },
                        width: '20'
                    },
                    {
                        data: 'invocie_number',
                        name: 'invocie_number'
                    },
                    {
                        data: 'tanggal_servis',
                        name: 'tanggal_servis'
                    },
                    {
                        data: 'no_polis',
                        name: 'no_polis'
                    },
                    {
                        data: 'customer_servis',
                        name: 'customer_servis'
                    },
                    {
                        data: 'motor.tipe_motor',
                        name: 'motor.tipe_motor'
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
                    targets: 6,
                    render: function (data, type, row) {
                        var css1 = 'badge badge-danger ';
                        var css2 = 'badge badge-success';
                        var css3 = 'badge badge-warning';
                        if (data == 'FINISH') {
                            css1 = 'badge badge-success';
                            return '<h6><span class="' + css1 + '">' + data +
                            '</span></h6>';
                        }
                        if (data == 'SERVICE') {
                            css2 = 'badge badge-danger';
                            return '<h6><span class="' + css2 + '">' + data +
                            '</span></h6>';
                        }
                        if (data == 'CHECKING') {
                            css3 = 'badge badge-warning';
                            return '<h6><span class="' + css3 + '">' + data +
                            '</span></h6>';
                        }
                    }
                }]
            });

            // load id motor for delete
            $(document).on('click', '#delete', function (event) {
                var serviceId = $(this).data('id');
                SwalDelete(serviceId);
                event.preventDefault();
            });
        });

         // delete action
         function SwalDelete(serviceId) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            swal({
                title: 'Are you sure?',
                text: 'it will be deleted permanently!',
                type: 'warning',
                showCancelButton: true,
                confrimButtonColor: '#3058d0',
                cancelButtonColor: '#d33',
                confrimButtonText: 'Yes, delete it!',
                showLoaderOnConfrim: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                                url: "{{ url('admin/servis') }}" + '/' + serviceId,
                                type: "DELETE",
                                data: {
                                    '_method': 'DELETE',
                                    '_token': csrf_token
                                },
                            })
                            .done(function (response) {
                                swal('Deleted!', response.message, response.status);
                                readMotor();
                            })
                            .fail(function () {
                                swal('Oops...', 'Something want worng with ajax!', 'error');
                            });
                    });
                },
                allowOutsideClick: false
            });

            function readMotor() {
                $('#datatable').DataTable().ajax.reload();
            }
        }
    </script>
    @endsection