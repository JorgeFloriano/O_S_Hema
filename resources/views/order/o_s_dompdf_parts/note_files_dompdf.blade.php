<div class="note-files-container" style="width: 100%;">
    <div style="margin-bottom: 10px;">
        <strong>Anexos SAT {{ $order->id }}, Intervenção {{ $loop->iteration }} (Fotos/Imagens):</strong>
    </div>

    {{-- Dividimos a coleção em grupos de 3 --}}
    @foreach ($note->images()->take(9)->chunk(3) as $chunk)
        <div class="photo-row" style="clear: both; width: 100%; display: block; height: 345px;">
            @foreach ($chunk as $file)
                <div class="photo-item">
                    @if(Str::contains(strtolower($file->path), ['.jpg', '.jpeg', '.png', '.gif']))
                        <div class="image-wrapper">
                            <div class="image-cell">
                                {{-- Caminho relativo para dompdf --}}
                                <img src="storage/{{ $file->path }}" alt="Anexo">
                            </div>
                        </div>
                
                        <div class="file-subtitle">
                            <span>{{ $file->original_name }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach
</div>