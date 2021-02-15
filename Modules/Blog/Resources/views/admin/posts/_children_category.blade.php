@foreach ($childrenCategories as $category)
    <ul class="nested-categories">
        <li><input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $categoryIds) ? 'checked' : '' }}/> {{ $category->name }}</li>
        @if ($category->children->count())
            @include('blog::admin.posts._children_category', ['childrenCategories' => $category->children])
        @endif
    </ul>
@endforeach