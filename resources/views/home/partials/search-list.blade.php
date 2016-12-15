<section class="document-search-form-container">
    <h3>{{ trans('messages.search.title') }}</h3>

    <form action="{{ request()->fullUrl() }}" id="document-search-form" method="get">
        <input type="text" name="search"
        placeholder="{{ trans('messages.search.placeholder') }}">

        <button class="document-search-button" type="submit">
            {{ trans('messages.submit') }}
        </button>
    </form>
</section>

<section class="document-search-results">
    <!-- Pagination! Thanks Laravel! -->
    {{ $documents->links() }}

    <h2>{{ trans('messages.recentlegislation') }}</h2>

    <!-- Category filter -->
    @if (request()->input('categories'))
        <div class="category-filter">
            {{ trans('messages.document.categories') }}
            @foreach ($selectedCategories as $category)
                <div class="category" role="button">
                    <!-- URL to remove this category from search query -->
                    <a href="{{ CategoryHelpers::urlMinusCategory(request(), $category['id']) }}">
                        {{ $category['name'] }}
                    </a>
                </div>
            @endforeach
            <div class="clear-category" role="button">
                <a href="{{ request()->fullUrlWithQuery(['categories' => null ]) }}">
                    {{ trans('messages.clear') }}
                </a>
            </div>
        </div>
    @endif

    <!-- Search filter -->
    @if (request()->input('search'))
        <div class="search-filter">
            {{ trans('messages.searchdisplay') }}
            <span class="search-query">{{ Request::input('search') }}</span>
            <div class="clear-search" role="button">
                <a href="{{ request()->fullUrlWithQuery(['search' => null ]) }}">
                    {{ trans('messages.clear') }}
                </a>
            </div>
        </div>
    @endif

    @each('home.partials.document-list-item', $documents, 'document')

</section>
