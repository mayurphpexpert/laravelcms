@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Products</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    <!-- Additional buttons for bulk actions and export -->
    <div class="card-footer clearfix">
        <div class="float-left">
            <button class="btn btn-danger" onclick="bulkDelete()">
                <i class="fas fa-trash-alt"></i> Bulk Delete
            </button>
            <button class="btn  btn-info m-btn font-weight-bolder" onclick="bulkPublish()">
                <i class="fas fa-check-circle"></i> Bulk Publish
            </button>
            <button class="btn btn-warning" onclick="bulkUnpublish()">
                <i class="fas fa-times-circle"></i> Bulk Unpublish
            </button>
        </div>

        <div class="float-right">
            <a href="{{ route('products.export') }}" class="btn btn-info">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>
    <!-- ... (Your existing code) ... -->
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
                        <button type="button" onclick="window.location.href='{{ route("products.index") }}'" class="btn btn-default btn-sm">Reset</button>
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
                            <th width="60" onclick="sortById('{{ request('order') === 'asc' ? 'desc' : 'asc' }}')" style="cursor: pointer;">ID<span class="sort-icon">
                                @if (request('sort') === 'id' && request('order') === 'asc')
                                    <i class="fas fa-arrow-up"></i>
                                @elseif (request('sort') === 'id' && request('order') === 'desc')
                                    <i class="fas fa-arrow-down"></i>
                                @endif
                            </span></th>
                            <th width="80"></th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>SKU</th>
                            <th width="100">Status</th>
                            <th width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($products->isNotEmpty())
                            @foreach ($products as $product)
                            @php
                                $productImage = $product->product_images->first();
                            @endphp
                            <tr>
                                <td><input type="checkbox" name="ids" class="checkbox_ids" id="" value="{{ $product->id }}"></td>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if (!empty($productImage->image))
                                    <img src="{{ asset('/public/uploads/product/small/'.$productImage->image) }}" class="img-thumbnail" width="50" />
                                    @else
                                    <img src="{{ asset('public/admin-assets/img/default-150x150.png')}}" class="img-thumbnail" width="50" alt="" />
                                    @endif
                                    
                                </td>
                                <td><a href="{{ route('products.edit',$product->id) }}">{{ $product->title }}</a></td>
                                <td>{{ $product->price }}</td>
                                <td>{{ $product->qty }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>
                                    @if ($product->status == 1)
                                    <svg class="text-success-500 h-6 w-6 text-success" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @else
                                    <svg class="text-danger h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @endif
                                    
                                </td>
                                <td>
                                    <a href="{{ route('products.edit',$product->id) }}">
                                        <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                        </svg>
                                    </a>
                                    <a href="#" onclick="deleteProduct({{ $product->id }})" class="text-danger w-4 h-4 mr-1">
                                        <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path ath fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
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
                    {{ $products->links() }}
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
                <!-- <div class="float-left">
                    Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} entries
                </div> -->
            </div>
                <!-- <ul class="pagination pagination m-0 float-right">
                    <li class="page-item"><a class="page-link" href="#">«</a></li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">»</a></li>
                </ul> -->

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
        window.location.href = "{{ route('products.index') }}" + "?per_page=" + value;
    }
    // Function to handle sorting by ID
    function sortById(order) {
        window.location.href = "{{ route('products.index') }}?sort=id&order=" + order;
    }

    function deleteProduct(id) {
        var url = '{{ route("products.delete","ID") }}';
        var newUrl = url.replace("ID", id)

        if (confirm("Are you sure you want to delete ")) {
            $.ajax({
                url: newUrl,
                type: 'delete',
                data: {},
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response["status"] == true) {
                        window.location.href = "{{ route('products.index') }}";
                    } else {
                        window.location.href = "{{ route('products.index') }}";
                    }
                }
            });
        }
    }

    // Function to handle "Select All" checkbox
    $('#select_all_checkbox').change(function() {
        $('.checkbox_ids').prop('checked', $(this).prop('checked'));
    });

    // Function to handle bulk deletion
    function bulkDelete() {
        var selectedIds = [];

        // Iterate through all checkboxes and get selected IDs
        $('.checkbox_ids:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0 && confirm("Are you sure you want to delete selected products?")) {
            var url = '{{ route("products.bulkDelete") }}';

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
                success: function(response) {
                    if (response["status"]) {
                        window.location.href = "{{ route('products.index') }}";
                    } else {

                    }
                }
            });
        } else {
            alert("Please select at least one products to delete.");
        }
    }

    // Function to handle bulk publish
    function bulkPublish() {
        var selectedIds = getSelectedIds();

        if (selectedIds.length > 0 && confirm("Are you sure you want to publish selected products?")) {
            var url = '{{ route("products.bulkPublish") }}';

            sendBulkActionRequest(url, selectedIds);
        } else {
            alert("Please select at least one products to publish.");
        }
    }

    // Function to handle bulk unpublish
    function bulkUnpublish() {
        var selectedIds = getSelectedIds();

        if (selectedIds.length > 0 && confirm("Are you sure you want to unpublish selected products?")) {
            var url = '{{ route("products.bulkUnpublish") }}';

            sendBulkActionRequest(url, selectedIds);
        } else {
            alert("Please select at least one products to unpublish.");
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
                    window.location.href = "{{ route('products.index') }}";
                } else {
                    // Handle error case
                }
            }
        });
    }
</script>
@endsection