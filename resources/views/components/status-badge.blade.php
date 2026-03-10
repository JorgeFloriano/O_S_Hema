@props(['status', 'urgent' => false])

@php
    $status = strtoupper($status);
    $isFinalizada = $status === 'F';
    
    // 1. Caso: Emergência e NÃO finalizada (Círculo Vermelho com "E")
    if ($urgent && !$isFinalizada) {
        $config = [
            'bg' => 'bg-danger',
            'color' => '',
            'label' => 'E',
            'title' => 'Emergencial (Pendente)',
            'showBadge' => false
        ];
    } 
    // 2. Caso: Emergência e JÁ finalizada (Círculo Verde com Bolinha Vermelha)
    elseif ($urgent && $isFinalizada) {
        $config = [
            'bg' => 'bg-success',
            'color' => '',
            'label' => 'F',
            'title' => 'Finalizada (Era Emergencial)',
            'showBadge' => true // Aqui mostramos a bolinha
        ];
    }
    // 3. Caso: Finalizada Normal (Círculo Verde)
    elseif ($isFinalizada) {
        $config = [
            'bg' => 'bg-success',
            'color' => '',
            'label' => 'F',
            'title' => 'Finalizada',
            'showBadge' => false
        ];
    }
    // 4. Caso: Pendente Normal (Círculo Laranja)
    else {
        $config = [
            'bg' => '',
            'color' => 'background-color: #fd7e14;',
            'label' => 'P',
            'title' => 'Pendente',
            'showBadge' => false
        ];
    }
@endphp

<div class="d-inline-block position-relative" title="{{ $config['title'] }}">
    
    {{-- Círculo Principal --}}
    <div {{ $attributes->merge([
        'class' => "rounded-circle text-white d-flex align-items-center justify-content-center " . $config['bg'],
        'style' => "width: 22px; height: 22px; font-size: 0.75rem; font-weight: bold; " . $config['color']
    ]) }}>
        {{ $config['label'] }}
    </div>

    {{-- Bolinha Vermelha (Apenas se for Emergência + Finalizada) --}}
    @if($config['showBadge'])
        <span class="position-absolute border border-white rounded-circle bg-danger" 
              style="top: -2px; right: -2px; width: 9px; height: 9px;">
        </span>
    @endif
</div>