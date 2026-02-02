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
                @if (session('main') == auth()->user()->id || auth()->user()->isCliAdmin())
                    <li>
                        <a class="dropdown-item" href="{{route('users.edit', ['user' => Crypt::encryptString(auth()->user()->id)])}}">
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
                @if (auth()->user()->cli)
                    <li>
                        <a class="nav-link me-2 {{ Request::is('*orders*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('client.orders.index')}}">
                            <i class="fa fa-file-text-o"></i>
                            Solicitações
                        </a>
                    </li>
                    
                    @if (auth()->user()->isCliAdmin())
                        <li class="nav-item">
                            <a class="nav-link me-2 {{ Request::is('*users*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('client.users.index')}}">
                                <i class="fa fa-user-o"></i>
                                Usuários
                            </a>
                        </li>
                    @endif
                @endif

                @if (auth()->user()->sup()->first() || auth()->user()->adm()->first())
                    <li>
                        <a class="nav-link me-2 {{ Request::is('*orders*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('orders.index')}}">
                            <i class="fa fa-file-text-o"></i>
                            SATs
                        </a>
                    </li>
                @endif

                @if (auth()->user()->tec()->first())
                    <li class="nav-item">
                        <a class="nav-link me-2 {{ Request::is('*notes*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('notes.index')}}">
                            <i class="fa fa-exclamation-circle"></i>
                            Programação
                        </a>
                    </li>
                @endif

                @if (session('main') == auth()->user()->id)
                    <li class="nav-item">
                        <a class="nav-link me-2 {{ Request::is('*clients*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('clients.index')}}">
                            <i class="fa fa-handshake-o"></i>
                            Clientes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link me-2 {{ Request::is('*users*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('users.index')}}">
                            <i class="fa fa-user-o"></i>
                            Usuários
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link me-2 {{ Request::is('*material*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('materials.index')}}">
                            <i class="fa fa-hdd-o"></i>
                            Materiais
                        </a>
                    </li>
                @endif

                @if (session('cli') == auth()->user()->id)
                    <li class="nav-item">
                        <a class="nav-link me-2 {{ Request::is('*clients*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('clients.index')}}">
                            <i class="fa fa-handshake-o"></i>
                            Clientes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link me-2 {{ Request::is('*materials*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('materials.index')}}">
                            <i class="fa fa-hdd-o"></i>
                            Materiais
                        </a>
                    </li>
                @endif

                @if (auth()->user()->sup()->first())
                    <li>
                        <a class="nav-link me-2 {{ Request::is('*tec_on*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="{{route('tec_on')}}">
                            <i class="fa fa-bell-o"></i>
                            Sobreaviso
                        </a>
                    </li>
                @endif

                @if (session('main') == auth()->user()->id)
                    <li class="nav-item dropdown">
                        <a class="nav-link me-2 dropdown-toggle {{ Request::is('*order_types*', '*note_types*', '*defects*', '*causes*', '*solutions*') ? 'fw-bold active border-bottom border-white pb-1' : '' }}" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bars"></i>
                        Códigos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item {{ Request::is('*order_types*') ? 'active' : '' }}" href="{{route('order_types.index')}}">
                                    Segmentos de serviços
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item {{ Request::is('*note_types*') ? 'active' : '' }}" href="{{route('note_types.index')}}">
                                    Tipos de serviços
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item {{ Request::is('*defects*') ? 'active' : '' }}" href="{{route('defects.index')}}">
                                    Defeitos 
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item {{ Request::is('*causes*') ? 'active' : '' }}" href="{{route('causes.index')}}">
                                    Causas
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item {{ Request::is('*solutions*') ? 'active' : '' }}" href="{{route('solutions.index')}}">
                                    Soluções
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>