@props(['status'])

@php
    $isFinalizada = strtoupper($status) === 'F';
    
    $config = $isFinalizada ? [
        'bg' => 'bg-success',
        'color' => '',
        'label' => 'F',
        'title' => 'Finalizada'
    ] : [
        'bg' => '',
        'color' => 'background-color: #fd7e14;',
        'label' => 'P',
        'title' => 'Pendente'
    ];
@endphp

<div {{ $attributes->merge([
    'class' => "rounded-circle text-white d-flex align-items-center justify-content-center " . $config['bg'],
    'style' => "width: 20px; height: 20px; font-size: 0.8rem; font-weight: bold; " . $config['color'],
    'title' => $config['title']
]) }}>
    {{ $config['label'] }}
</div>