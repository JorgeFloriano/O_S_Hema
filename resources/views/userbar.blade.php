<style>
    .ico {
        background-color: rgb(35, 33, 33);
        color: white;
    }
    a {
        color: black;
        text-decoration: none;
    }
    a:hover, i:hover {
        color: rgb(129, 190, 208)
    }
</style>
<div class="container-fluid" id="userbar">
    <div class="row pe-1 mb-2 ico">
        <div class="col-4 py-2">
            <img src="{{asset('assets/img/logo_hema.png')}}" alt="logo hema" width="100px">
        </div>
        <div class="col-6 pt-4" style="font-size: large">
            @if (session()->has('success'))
                {{session()->get('success')}}
            @endif

            @if (auth()->check())
                {{auth()->user()->name}}
            @endif
        </div>

        @if (session('main') == auth()->user()->id)
            <div class="nav-item dropdown col-1 p-2 text-center">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white">
                    <i style="font-size: x-large" class="fa fa-list-ol" aria-hidden="true"></i>
                </a>
                <ul class="dropdown-menu">

                    @if (session('main') == auth()->user()->id)
                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('order_types.index')}}">
                                Segmentos de serviços
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('note_types.index')}}">
                                Tipos de serviços
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('defects.index')}}">
                                Defeitos 
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('causes.index')}}">
                                Causas
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('solutions.index')}}">
                                Soluções
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @else
            <div class="col-1"></div>
        @endif
        
        <div class="nav-item dropdown col-1 p-2 text-center">
            <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: white">
                <i style="font-size: x-large" class="fa fa-bars" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu">

                @if (session('main'))
                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('users.edit', ['user' => auth()->user()->id])}}">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            Perfil
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('clients.index')}}">
                            <i class="fa fa-handshake-o" aria-hidden="true"></i>
                            Clientes
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('users.index')}}">
                            <i class="fa fa-user-o" aria-hidden="true"></i>
                            Usuários
                        </a>
                    </li>
                @endif

                @if (auth()->user()->tec()->first())
                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('notes.index')}}">
                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                            Programação
                        </a>
                    </li>
                @endif

                @if (auth()->user()->sup()->first() || auth()->user()->adm()->first())
                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('orders.index')}}">
                            <i class="fa fa-files-o" aria-hidden="true"></i>
                            Ordens
                        </a>
                    </li>
                @endif

                @if (auth()->user()->sup()->first())
                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('tec_on')}}">
                            <i class="fa fa-mobile" aria-hidden="true"></i></i>
                            Sobreaviso
                        </a>
                    </li>
                @endif

                <li>
                    <a class="dropdown-item btn-lg" href="{{route('login.destroy')}}">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>