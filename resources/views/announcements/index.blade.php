<x-layout title="Announcements">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Announcements</h4>
                @if(auth()->user()->isAdmin())
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#announcementForm">
                        <i class="bi bi-plus-lg"></i> Publish Announcement
                    </button>
                @endif
            </div>

            @if(auth()->user()->isAdmin())
                <div class="collapse mb-4" id="announcementForm">
                    <x-card title="Publish Announcement">
                        <form action="{{ route('announcements.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-success">Publish</button>
                        </form>
                    </x-card>
                </div>
            @endif

            @foreach($announcements as $announcement)
                <x-card class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h5>{{ $announcement->title }}</h5>
                            <small class="text-muted">Published {{ $announcement->published_at?->diffForHumans() }}</small>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Delete this announcement?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endif
                    </div>
                    <p class="mb-0">{{ $announcement->description }}</p>
                </x-card>
            @endforeach

            <div class="d-flex justify-content-center">{{ $announcements->links() }}</div>
        </div>
    </div>
</x-layout>
