<style>
    .nested-categories {
        list-style: none;
    }

    .nested-categories li {
        margin-left: -30px;
    }

    .nested-categories-inner {
        padding: 5px;
        border: 1px solid #e3eaef;
        background: #f4f6f9;
        overflow: scroll;
        height: 200px;
    }
</style>
<div class="nested-categories-inner">
    <ul class="nested-categories">
        @foreach ($categories as $category)
            <li><input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $categoryIds) ? 'checked' : '' }}/> {{ $category->name }}</li>
            @if ($category->children->count())
                @include('blog::admin.posts._children_category', ['childrenCategories' => $category->children])
            @endif
        @endforeach
    </ul>
</div>