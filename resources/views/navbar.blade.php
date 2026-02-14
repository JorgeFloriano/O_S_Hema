
@php
    $user = auth()->user();

    $navLinks = collect([
        [
            'label' => 'Solicitações',
            'route' => route('client.orders.index'),
            'icon'  => 'fa-file-text-o',
            'active'=> Request::is('*orders*'),
            'show'  => $user->isCli()
        ],
        [
            'label' => 'Usuários',
            'route' => route('client.users.index'),
            'icon'  => 'fa-user-o',
            'active'=> Request::is('*users*'),
            'show'  => $user->isCliAdmin()
        ],
        [
            'label' => 'SATs',
            'route' => route('orders.index'),
            'icon'  => 'fa-file-text-o',
            'active'=> Request::is('*orders*'),
            'show'  => $user->isSup() || $user->isAdm()
        ],
        [
            'label' => 'Programação',
            'route' => route('notes.index'),
            'icon'  => 'fa-exclamation-circle',
            'active'=> Request::is('*notes*'),
            'show'  => $user->isTec()
        ],
        [
            'label' => 'Clientes',
            'route' => route('clients.index'),
            'icon'  => 'fa-handshake-o',
            'active'=> Request::is('*clients*'),
            'show'  => $user->canAcessClientsAndMaterials()
        ],
        [
            'label' => 'Materiais',
            'route' => route('materials.index'),
            'icon'  => 'fa-hdd-o',
            'active'=> Request::is('*material*'),
            'show'  => $user->canAcessClientsAndMaterials()
        ],
        [
            'label' => 'Usuários',
            'route' => route('users.index'),
            'icon'  => 'fa-user-o',
            'active'=> Request::is('*users*'),
            'show'  => $user->isMainAdm()
        ],
        [
            'label' => 'Sobreaviso',
            'route' => route('tec_on'),
            'icon'  => 'fa-bell-o',
            'active'=> Request::is('*tec_on*'),
            'show'  => $user->isSup() || $user->isMainAdm()
        ],
    ])->where('show', true);

    // Array específico para o dropdown de "Códigos"
    $codigoLinks = [
        ['label' => 'Segmentos', 'route' => route('order_types.index'), 'pattern' => '*order_types*'],
        ['label' => 'Tipos', 'route' => route('note_types.index'), 'pattern' => '*note_types*'],
        ['label' => 'Defeitos', 'route' => route('defects.index'), 'pattern' => '*defects*'],
        ['label' => 'Causas', 'route' => route('causes.index'), 'pattern' => '*causes*'],
        ['label' => 'Soluções', 'route' => route('solutions.index'), 'pattern' => '*solutions*'],
    ];
@endphp

<nav class="navbar navbar-expand-lg navbar-dark" style="background: #1b0363ff">
    {{-- Se a URL contiver 'orders', usa fluid (100%), caso contrário usa o container padrão --}}
    <div class="container-sm">
        <div>
            <img src="{{asset('assets/img/'.env('LOGO'))}}" alt="logo hema" width="140px">
        </div>

        <div class="dropdown">
            <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                @if (session()->has('success'))
                    {{session()->get('success')}}
                @endif

                @if (auth()->check())
                    {{auth()->user()->name}}
                @endif
            </a>
          
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                @if (auth()->user()->isMainAdm() || auth()->user()->isCliAdmin())
                    <li>
                        <a class="dropdown-item" href="{{route(auth()->user()->isCli() ? 'client.users.edit' : 'users.edit', ['user' => Crypt::encryptString(auth()->user()->id)])}}">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            Perfil
                        </a>
                    </li>
                @endif
                <li>
                    <a class="dropdown-item" href="{{route('login.destroy')}}">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse mt-3" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-lg-0">
                @foreach ($navLinks as $link)
                    <li class="nav-item">
                        <a class="nav-link me-2 {{ $link['active'] ? 'fw-bold active border-bottom border-white pb-1' : '' }}" 
                        href="{{ $link['route'] }}">
                            <i class="fa {{ $link['icon'] }}"></i>
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach

                {{-- Dropdown de Códigos (Lógica especial) --}}
                @if ($user->isMainAdm())
                    <li class="nav-item dropdown">
                        <a class="nav-link me-2 dropdown-toggle {{ Request::is('*order_types*', '*note_types*', '*defects*', '*causes*', '*solutions*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" 
                        href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-bars"></i> Códigos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @foreach ($codigoLinks as $item)
                                <li>
                                    <a class="dropdown-item {{ Request::is($item['pattern']) ? 'active' : '' }}" href="{{ $item['route'] }}">
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>