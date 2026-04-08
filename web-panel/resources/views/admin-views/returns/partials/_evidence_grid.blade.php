<div class="row g-3">
    @forelse($resource->media as $media)
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100">
                <img src="{{ $media->file_fullpath }}" alt="{{ $media->capture_type ?? 'evidence' }}" class="card-img-top" style="height: 220px; object-fit: cover;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <strong class="text-capitalize">{{ str_replace('_', ' ', $media->capture_type ?? 'evidence') }}</strong>
                        <span class="badge badge-soft-secondary">{{ strtoupper($media->media_type) }}</span>
                    </div>
                    <div class="text-muted small mt-2">
                        Uploaded {{ $media->created_at?->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-soft-warning mb-0">
                No evidence uploaded yet.
            </div>
        </div>
    @endforelse
</div>
