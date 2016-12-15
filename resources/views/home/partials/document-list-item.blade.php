<article class="document-list-item">
    <header class="document-info">
        <h3 class="subheading">
            <a href="/documents/{{ $document['slug'] }}" title="{{ $document['title'] }}">
                {{ $document['title'] }}
            </a>
        </h3>
        <div class="document-sponsors">
            @if ($document['discussion_state'] == 'closed')
                @include('partials/closed-discussion-icon')
            @endif

            {{ trans('messages.document.sponsoredby') }}
            <ul>
                @foreach ($document['sponsors'] as $sponsor)
                    {{-- TODO - Would be cool to link this to a sponsor view page --}}
                    <li class="document-sponsor">{{ $sponsor['display_name'] }}</li>
                @endforeach
            </ul>
        </div>
        <div class="document-categories">
            <ul>
                @foreach ($document['categories'] as $category)
                    <li class="category">
                        <!-- URL to add this category to search query -->
                        <a href="{{ CategoryHelpers::urlPlusCategory(app('request'), $category['id']) }}">
                            <span>{{ $category['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </header>
        <div class="document-meta">
            <!-- Formerly "list-doc-info" -->
            <div class="document-info-list">
                <div class="date">
                    <span>{{ trans('messages.updated') }}</span>
                    <time class="document-updated-at" datetime="{{ $document['updated_at'] }}">
                        {{ date('M d, Y', strtotime($document['updated_at'])) }}
                    </time>
                </div>
                <div class="document-stats">
                    <div class="row">
                        <div class="col-md-12">
                            {{ $document['comment_count'] }} {{ trans('messages.document.comments') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {{ $document['note_count'] }} {{ trans('messages.document.notes') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            {{ $document['user_count'] }} {{ trans('messages.document.collaborators') }}
                        </div>
                    </div>
                </table>

                <ul class="document-dates">
                    @foreach ($document->dates as $date)
                        <li class="date">{{ $date['label'] }} on {{ date('M d, Y', strtotime($date['date'])) }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="read-action">
                <a href="/documents/{{ $document['slug'] }}" class="read-more-button">{{ trans('messages.readmore') }}</a>
            </div>
        </div>
    </article>
