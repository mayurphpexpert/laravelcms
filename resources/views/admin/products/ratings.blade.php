@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ratings</h1>
            </div>
            <!-- <div class="col-sm-6 text-right">
                <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
            </div> -->
        </div>
    </div>
    <div class="card-footer clearfix">
        <div class="float-left">
            <button class="btn  btn-info m-btn font-weight-bolder" onclick="bulkPublish()">
                <i class="fas fa-check-circle"></i> Bulk Active
            </button>
            <button class="btn btn-warning" onclick="bulkUnpublish()">
                <i class="fas fa-times-circle"></i> Bulk Inactive
            </button>
        </div>
    </div>
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
    @include('admin.message')
        <div class="card">
            <!-- <div class="card-header">
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 250px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div> -->
            <form action="" method="get">
                <div class="card-header">
                    <div class="card-title">
                        <button type="button" onclick="window.location.href='{{ route("products.productRatings") }}'" class="btn btn-default btn-sm">Reset</button>
                    </div>
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 250px;">
                            <input value="{{ Request::get('keyword') }}" type="text" name="keyword" class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="" id="select_all_checkbox"></th>
                            <!-- <th width="60">ID</th> -->
                            <th  onclick="sortById('{{ request('order') === 'asc' ? 'desc' : 'asc' }}')" style="cursor: pointer;">ID<span class="sort-icon">
                                @if (request('sort') === 'id' && request('order') === 'asc')
                                    <i class="fas fa-arrow-up"></i>
                                @elseif (request('sort') === 'id' && request('order') === 'desc')
                                    <i class="fas fa-arrow-down"></i>
                                @endif
                            </span></th>
                            <th>Product</th>
                            <th>Ratings</th>
                            <th>Comment</th>
                            <th>Rated by</th>                            
                            <th>Status</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @if ($ratings->isNotEmpty())
                            @foreach ($ratings as $rating)
                            <tr>
                                <td><input type="checkbox" name="ids" class="checkbox_ids" id="" value="{{ $rating->id }}"></td>
                                <td>{{ $rating->id }}</td>
                                <td>{{ $rating->productTitle }}`</td>
                                <td>{{ $rating->rating }}</td>
                                <td>{{ $rating->comment }}</td>
                                <td>{{ $rating->username }}</td>
                                <td>
                                    @if ($rating->status == 1)
                                    <a href="javascript:void(0);" onclick="changeStatus(0,'{{ $rating->id}}');">
                                    <svg class="text-success-500 h-6 w-6 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    </a>
                                    @else
                                    <a href="javascript:void(0);" onclick="changeStatus(1,'{{ $rating->id}}');">
                                    <svg class="text-danger h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    </a>
                                    @endif                                    
                                </td>
                                
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td>Record Not Found.</td>
                        </tr>                            
                        @endif
                        
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $ratings->links() }}
                </div>
                <div class="form-inline">
                    <label for="perPage">Number of rows: </label>
                    <select id="perPage" class="form-control" onchange="changePerPage(this.value)">
                        <option value="5" {{ session('perPage', 10) == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ session('perPage', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ session('perPage', 10) == 15 ? 'selected' : '' }}>15</option>
                        <option value="20" {{ session('perPage', 10) == 20 ? 'selected' : '' }}>20</option>
                        <option value="25" {{ session('perPage', 10) == 25 ? 'selected' : '' }}>25</option>
                        <!-- Add more options as needed -->
                    </select>
                </div>
                
            </div>

            </div>
        </div>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection



@section('customJs')
<script>

    function changePerPage(value) {
        window.location.href = "{{ route('products.productRatings') }}" + "?per_page=" + value;
    }
    // Function to handle sorting by ID
    function sortById(order) {
        window.location.href = "{{ route('products.productRatings') }}?sort=id&order=" + order;
    }

    //change status 
    function changeStatus(status,id){
        if (confirm("Are you sure you want to change status. ")) {
            $.ajax({
                url: '{{ route("products.changeRatingStatus") }}',
                type: 'get',
                data: {status:status, id:id},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    window.location.href = "{{ route('products.productRatings') }}";
                    // if (response["status"] == true) {
                    // } else {
                    //     window.location.href = "{{ route('products.productRatings') }}";
                    // }
                }
            });
        }
    }

    // Function to handle "Select All" checkbox
    $('#select_all_checkbox').change(function() {
        $('.checkbox_ids').prop('checked', $(this).prop('checked'));
    });

    // Function to handle bulk publish
    function bulkPublish() {
        var selectedIds = getSelectedIds();

        if (selectedIds.length > 0 && confirm("Are you sure you want to active status?")) {
            var url = '{{ route("products.bulkRatingPublish") }}';

            sendBulkActionRequest(url, selectedIds);
        } else {
            alert("Please select at least one record.");
        }
    }

    // Function to handle bulk unpublish
    function bulkUnpublish() {
        var selectedIds = getSelectedIds();

        if (selectedIds.length > 0 && confirm("Are you sure you want to inactive status?")) {
            var url = '{{ route("products.bulkRatingUnpublish") }}';

            sendBulkActionRequest(url, selectedIds);
        } else {
            alert("Please select at least one record.");
        }
    }

    // Function to get selected category IDs
    function getSelectedIds() {
        var selectedIds = [];

        // Iterate through all checkboxes and get selected IDs
        $('.checkbox_ids:checked').each(function () {
            selectedIds.push($(this).val());
        });

        return selectedIds;
    }

    // Function to send bulk action request
    function sendBulkActionRequest(url, selectedIds) {
        $.ajax({
            url: url,
            type: 'post',
            data: {
                ids: selectedIds
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response["status"]) {
                    window.location.href = "{{ route('products.productRatings') }}";
                } else {
                    // Handle error case
                }
            }
        });
    }
</script>
@endsection