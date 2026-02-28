<div class="note-files-container" style="margin-top: 155px;">
    <div style="margin-bottom: 5px;">
        <strong>Anexos SAT {{ $order->id }}, Intervenção {{ $loop->iteration }} (Fotos/Imagens):</strong>
    </div>

    <div class="photo-grid">
        {{-- Verifica se houver anexos para exibir na grade, somente 9 --}}
        @foreach ($note->files->take(9) as $file)
            {{-- Verifica se NÃO é PDF para exibir na grade (PDFs serão exibidos no final de cada SAT) --}}
            @if(!Str::endsWith(strtolower($file->path), '.pdf'))
                <div class="photo-item">
                    @if(Str::contains($file->path, ['.jpg', '.jpeg', '.png', '.gif']))
                        <div class="image-wrapper">
                            <img src="storage/{{ $file->path }}" alt="Anexo">
                        </div>
                
                        <div class="file-subtitle">
                            <span>{{ $file->original_name }}</span>
                        </div>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</div>