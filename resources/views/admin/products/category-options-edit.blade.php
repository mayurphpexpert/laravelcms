@foreach ($categories as $category)
    <option {{ ($productCategory == $category->id) ? 'selected' : '' }} value="{{ $category->id }}">{{ $prefix . $category->name }}</option>
    @if ($category->children->isNotEmpty())
        @include('partials.category-options-edit', ['categories' => $category->children, 'productCategory' => $productCategory, 'prefix' => $prefix . ' -- '])
    @endif
@endforeach
