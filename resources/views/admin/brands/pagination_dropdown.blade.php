<!-- ... (Your existing code) ... -->

<!-- <div class="card-footer clearfix" >
    <div  class="input-group" style="width: 120px;"> -->
    <select class="selectpicker datatable-pager-size form-control" title="Select page size" data-width="60px" data-container="body" name="pagination_limit" id="pagination_limit" onchange="changePerPage(this)">
        <option value="5" {{ $brands->perPage() == 5 ? 'selected' : '' }}>5</option>
        <option value="10" {{ $brands->perPage() == 10 ? 'selected' : '' }}>10</option>
        <option value="15" {{ $brands->perPage() == 15 ? 'selected' : '' }}>15</option>
        <option value="20" {{ $brands->perPage() == 20 ? 'selected' : '' }}>20</option>
        <option value="25" {{ $brands->perPage() == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ $brands->perPage() == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ $brands->perPage() == 100 ? 'selected' : '' }}>100</option>
        <option value="200" {{ $brands->perPage() == 200 ? 'selected' : '' }}>200</option>
        <option value="500" {{ $brands->perPage() == 500 ? 'selected' : '' }}>500</option>
    </select>
    <!-- </div>
</div> -->

<!-- ... (Your existing code) ... -->

<script>
    function changePerPage(select) {
        var perPage = select.value;
        var currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('pagination_limit', perPage);
        window.location.href = currentUrl.toString();
    }
</script>

<!-- ... (Your existing code) ... -->
