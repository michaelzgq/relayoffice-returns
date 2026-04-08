<div class="card">
    <div class="card-header border-0">
        <h4 class="mb-0">Case Timeline</h4>
    </div>
    <div class="card-body">
        @forelse($resource->events as $event)
            <div class="d-flex gap-3 mb-4">
                <div class="mt-1">
                    <span class="badge badge-soft-primary">{{ strtoupper(str_replace('_', ' ', $event->event_type)) }}</span>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <strong>{{ $event->title }}</strong>
                        <span class="text-muted small">{{ $event->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($event->description)
                        <div class="text-muted mt-1">{{ $event->description }}</div>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-soft-secondary mb-0">No timeline events yet.</div>
        @endforelse
    </div>
</div>
