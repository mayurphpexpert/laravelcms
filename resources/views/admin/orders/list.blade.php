@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Orders</h1>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
    <!-- Additional buttons for bulk actions and export -->
    <div class="card-footer clearfix">
        <!-- <div class="float-left">
            <button class="btn btn-danger" onclick="bulkDelete()">
                <i class="fas fa-trash-alt"></i> Bulk Delete
            </button>
            <button class="btn  btn-info m-btn font-weight-bolder" onclick="bulkPublish()">
                <i class="fas fa-check-circle"></i> Bulk Publish
            </button>
            <button class="btn btn-warning" onclick="bulkUnpublish()">
                <i class="fas fa-times-circle"></i> Bulk Unpublish
            </button>
        </div> -->

        <!-- <div class="float-right">
            <a href="#" class="btn btn-info">
                <i class="fas fa-download"></i> Export
            </a>
        </div> -->
    </div>
    <!-- ... (Your existing code) ... -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        @include('admin.message')
        <div class="card">
            <form action="" method="get">
                <div class="card-header">
                    <div class="card-title">
                        <button type="button" onclick="window.location.href='{{ route("orders.index") }}'" class="btn btn-default btn-sm">Reset</button>
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
                            <!-- <th><input type="checkbox" name="" id="select_all_checkbox"></th> -->
                            <!-- <th width="60">ID</th> -->
                            <th width="60" onclick="sortById('{{ request('order') === 'asc' ? 'desc' : 'asc' }}')" style="cursor: pointer;">ID<span class="sort-icon">
                                    @if (request('sort') === 'id' && request('order') === 'asc')
                                    <i class="fas fa-arrow-up"></i>
                                    @elseif (request('sort') === 'id' && request('order') === 'desc')
                                    <i class="fas fa-arrow-down"></i>
                                    @endif
                                </span></th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Date Purchased</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @if ($orders->isNotEmpty())
                        @foreach ($orders as $order )
                        <tr>
                            <!-- <td><input type="checkbox" name="ids" class="checkbox_ids" id="" value="{{ $order->id }}"></td> -->
                            <td><a href="{{ route('orders.detail',$order->id) }}">{{ $order->id }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->email }}</td>
                            <td>{{ $order->mobile }}</td>
                            <td>
                                @if ($order->status == 'pending')
                                <span class="badge bg-danger">Pending</span>
                                @elseif ($order->status == 'shipped')
                                <span class="badge bg-info">Shipped</span>
                                @elseif ($order->status == 'delivered')                                                    
                                <span class="badge bg-success">Delivered</span>                                                
                                @else
                                <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>₹ {{ number_format($order->grand_total,2) }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y') }}
                            </td>
                           
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5">Records Not Found</td>
                        </tr>
                        @endif

                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                <div class="float-right">
                    {{ $orders->links() }}
                </div>
                <div class="form-inline">
                    <label for="perPage">Number of rows:</label>
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
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJs')
<SCript>
    function changePerPage(value) {
        window.location.href = "{{ route('orders.index') }}" + "?per_page=" + value;
    }

    function sortById(order) {
        window.location.href = "{{ route('orders.index') }}?sort=id&order=" + order;
    }
</SCript>
@endsection