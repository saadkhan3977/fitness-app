@extends('admin.layouts.master')

@section('content')

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.notification')
            </div>
        </div>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary float-left">Negotiator Lists</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                @if (count($users) > 0)
                    <table class="table table-bordered" id="order-dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>S.N.</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Language</th>
                                <th>Expertise</th>
                                <th>Photo</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>S.N.</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Language</th>
                                <th>Expertise</th>
                                <th>Photo</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach ($users as $user)
                                @php
                                    $id = 1;
                                    $expert = json_decode($user->expertise);
                                    $lang = json_decode($user->language);
                                @endphp
                                <tr>
                                    <td>{{ $id++ }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if ($lang != null)
                                            @foreach ($lang as $items)
                                                <span class="badge badge-primary">{{ $items }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($expert != null)
                                            @foreach ($expert as $item)
                                                <span class="badge badge-success">{{ $item }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $user->photo }}">
                                            <img src="{{ $user->photo }}" class="w-50" alt="">
                                        </a>
                                    </td>
                                    <td>
									 @switch($user->status)
                                            @case('active')
                                                <span class="badge badge-success">Active</span>
                                            @break

                                            @case('inactive')
                                                <span class="badge badge-warning">In Active</span>
                                            @break

                                            @default
                                                <span class="badge badge-secondary">Unknown</span>
                                        @endswitch
									</td>
                                    <td>
                                        <form method="POST" action="{{ route('orders.destroy', [$user->id]) }}">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-danger btn-sm dltBtn" data-id="{{ $user->id }}"
                                                style="height:30px; width:30px;border-radius:50%" data-toggle="tooltip"
                                                data-placement="bottom" title="Delete"><i
                                                    class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h6 class="text-center">No orders found!!! Please order some products</h6>
                @endif
            </div>
        </div>
    </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        div.dataTables_wrapper div.dataTables_paginate {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    <!-- Page level plugins -->
    <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('backend/js/demo/datatables-demo.js') }}"></script>
    <script>
        $('#order-dataTable').DataTable({
            "columnDefs": [{
                "orderable": false,
                "targets": [8]
            }]
        });

        // Sweet alert

        function deleteData(id) {

        }
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.dltBtn').click(function(e) {
                var form = $(this).closest('form');
                var dataID = $(this).data('id');
                // alert(dataID);
                e.preventDefault();
                swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to recover this data!",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            form.submit();
                        } else {
                            swal("Your data is safe!");
                        }
                    });
            })
        })
    </script>
@endpush
