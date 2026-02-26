<div class="note-files-container" style="margin-top: 155px;">
    <div style="margin-bottom: 5px;">
        <strong>Anexos SAT {{ $order->id }}, Intervenção {{ $loop->iteration }} (Fotos/Imagens):</strong>
    </div>

    <div class="photo-grid">
        @foreach ($note->files->take(9) as $file)
            <div class="photo-item">
                @if(Str::contains($file->path, ['.jpg', '.jpeg', '.png', '.gif']))
                    <div class="image-wrapper">
                        <img src="{{ asset('storage/' . $file->path) }}" alt="Anexo">
                    </div>
            
                    <div class="file-subtitle">
                        <span>{{ $file->original_name }}</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>