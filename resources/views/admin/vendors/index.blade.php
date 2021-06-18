@extends('admin.app')

@section('content')
<div class="container">
    <div class="rwo">
        @include('admin.menu.index')
        <div class="col-md-12">
            @if ($alertFm = Session::get('success'))
            <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $alertFm }}</strong>
            </div>
            @endif

            @if ($alertFm = Session::get('error'))
            <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $alertFm }}</strong>
            </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('All Vendors') }}  
                <a id="" href="{{ route('admin-vendors-add')}}" class="float-right btn btn-primary">Add </a></div>

                <div class="card-body">
                    
                         <table class="table table-bordered yajra-datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>E-mail</th>
                                    <th>Store Name</th>
                                    <th width="300px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
         
                        
                </div>
            </div>
        </div>
    </div>    
</div>
@endsection
@section('script')
<script type="text/javascript" >
  $(function () {
    
    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.vendor.list') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'username', name: 'username'},
            {data: 'email', name: 'email'},
            {data: 'details.store_name', name: 'details.store_name'},
            {
                data: 'action', 
                name: 'action', 
                orderable: true, 
                searchable: true
            },
        ]
    });

    $('.yajra-datatable').on('click', '.delete', function(e) {
        e.preventDefault();
        var deleteConfirm = confirm("Are you sure?");
        if (deleteConfirm == true) {
            // AJAX request
            $.ajax({
                url: this.getAttribute('href'),
                type: 'delete',
                success: function(response) {
                    table.ajax.reload();
                    alert(response.message);
                }
            });
        }
    });
    
  });
</script>
@endsection