<p>
    {{ trans('messages.pagination', [
          'start' => $collection->total() === 0 ? 0 : ($collection->currentPage() - 1) * $collection->perPage() + 1,
          'end' => min($collection->currentPage() * $collection->perPage(), $collection->total()),
          'total' => $collection->total()
        ])
    }}
</p>

{{ $collection->appends(request()->query())->links() }}
