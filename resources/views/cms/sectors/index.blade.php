@extends('layouts.cms')
@section('cms_title', $title)
@section('cms_content')
<div class="pagetitle">
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
          <li class="breadcrumb-item">Sektor</li>
          <li class="breadcrumb-item active">Data</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
                <br>
                <div class="d-flex flex-wrap">
                    <h5 class="mb-4">
                        <strong>{{ $title }} Table</strong>
                    </h5>
                    <div class="ms-auto">
                        <a href="{{ route("admin.{$resourceName}.create") }}" class="btn btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Create">
                            <i class="bi bi-file-earmark-plus text-blue"></i>
                        </a>
                        <button type="button" id="btn_refresh" class="btn btn-light" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
                            <i class="bi bi-recycle text-blue"></i>
                        </button>
                    </div>
                </div>
                <table class="table" id="table_datatables">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Sektor</th>
                        <th>Last Update</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
@push('cms_css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
@endpush
@push('cms_js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('assets/scripts/table-ajax.js') }}"></script>
    <script>
        (function ($) {
            $(function () {
                TableAjax.initWithAction('#table_datatables', '{{ route("admin.sectors.datatables") }}', {
                    columnDefs: [
                        { responsivePriority: 1, targets: 0 },
                        { responsivePriority: 10001, targets: 2 },
                    ],
                    columns: [
                        { 
                            data: null,
                            searchable: false,
                            orderable: false,
                            render: function (data, type, row, meta) {
                                return meta.row + 1;
                            }
                        },
                        {data: 'sektor', name: 'sektor'},
                        {data: 'updated_at', name: 'updated_at', searchable: false, sClass: 'text-right'},
                        {data: 'actions', searchable: false, orderable: false, sClass: 'text-center'}
                    ],
                    order: [[2, 'desc']],
                }, 'Sektor');
            });
        })(jQuery);
    </script>
@endpush