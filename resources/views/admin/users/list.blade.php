@extends('admin.layouts.app')

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Users</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('users.create') }}" class="btn btn-primary">New Users</a>
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
            <button class="btn  btn-info " onclick="bulkPublish()">
                <i class="fas fa-check-circle"></i> Bulk Active
            </button>
            <button class="btn btn-warning" onclick="bulkUnpublish()">
                <i class="fas fa-times-circle"></i> Bulk InActive
            </button>
        </div>

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
                        <button type="button" onclick="window.location.href='{{ route("users.index") }}'" class="btn btn-default btn-sm">Reset</button>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($users->isNotEmpty())
                        @foreach ($users as $user )
                        <tr>
                            <td><input type="checkbox" name="ids" class="checkbox_ids" id="" value="{{ $user->id }}"></td>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if ($user->status == 1)
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
                                <a href="{{ route('users.edit', $user->id) }}">
                                    <svg class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </a>
                                <a href="#" onclick="deleteUser({{ $user->id }})" class="text-danger w-4 h-4 mr-1">
                                    <svg wire:loading.remove.delay="" wire:target="" class="filament-link-icon w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
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
                    {{ $users->links() }}
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
<script>
    function changePerPage(value) {
        window.location.href = "{{ route('users.index') }}" + "?per_page=" + value;
    }
    // Function to handle sorting by ID
    function sortById(order) {
        window.location.href = "{{ route('users.index') }}?sort=id&order=" + order;
    }

    function deleteUser(id) {
        var url = '{{ route("users.delete","ID") }}';
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
                    if (response["status"]) {
                        window.location.href = "{{ route('users.index') }}";
                    } else {

                    }
                }
            });
        }
    }


    // Function to handle bulk deletion
    function bulkDelete() {
        var selectedIds = [];

        // Iterate through all checkboxes and get selected IDs
        $('.checkbox_ids:checked').each(function() {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length > 0 && confirm("Are you sure you want to delete selected users?")) {
            var url = '{{ route("users.bulkDelete") }}';

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
                        window.location.href = "{{ route('users.index') }}";
                    } else {

                    }
                }
            });
        } else {
            alert("Please select at least one user to delete.");
        }
    }

    // Function to handle "Select All" checkbox
    $('#select_all_checkbox').change(function() {
        $('.checkbox_ids').prop('checked', $(this).prop('checked'));
    });




    // Function to handle bulk publish
    function bulkPublish() {
        var selectedIds = getSelectedIds();

        if (selectedIds.length > 0 && confirm("Are you sure you want to publish selected users?")) {
            var url = '{{ route("users.bulkPublish") }}';

            sendBulkActionRequest(url, selectedIds);
        } else {
            alert("Please select at least one users to publish.");
        }
    }

    // Function to handle bulk unpublish
    function bulkUnpublish() {
        var selectedIds = getSelectedIds();

        if (selectedIds.length > 0 && confirm("Are you sure you want to unpublish selected users?")) {
            var url = '{{ route("users.bulkUnpublish") }}';

            sendBulkActionRequest(url, selectedIds);
        } else {
            alert("Please select at least one cateusersgory to unpublish.");
        }
    }

    // Function to get selected category IDs
    function getSelectedIds() {
        var selectedIds = [];

        // Iterate through all checkboxes and get selected IDs
        $('.checkbox_ids:checked').each(function() {
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
            success: function(response) {
                if (response["status"]) {
                    window.location.href = "{{ route('users.index') }}";
                } else {
                    // Handle error case
                }
            }
        });
    }
</script>



@endsection