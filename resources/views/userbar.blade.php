<style>
    .ico {
        background-color: rgb(35, 33, 33);
        color: white;
    }
    a {
        text-decoration: none;
    }
    a:hover, i:hover {
        color: rgb(129, 190, 208)
    }
</style>
<div class="container-fluid" id="userbar">
    <div class="row pe-1 mb-2 ico">
        <div class="col-4 p-2">
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
        <div class="nav-item dropdown col-2 pt-2 text-center">
            <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i style="font-size: x-large" class="fa fa-bars" aria-hidden="true"></i>
            </a>
            <ul class="dropdown-menu">

                @if (auth()->user()->adm()->first())
                    @if (auth()->user()->adm()->first()->main)
                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('clients.index')}}">
                                <i class="fa fa-user" aria-hidden="true"></i>
                                Clientes
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item btn-lg" href="{{route('users.index')}}">
                                <i class="fa fa-user-o" aria-hidden="true"></i>
                                Colaboradores
                            </a>
                        </li>
                    @endif

                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('tec_on')}}">
                            <i class="fa fa-wrench" aria-hidden="true"></i></i>
                            Plantonistas
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item btn-lg" href="{{route('orders.index')}}">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                            Ordens
                        </a>
                    </li>
                @endif

                @if (auth()->user()->tec()->first())
                    <li><a class="dropdown-item btn-lg" href="{{route('notes.index')}}"><i class="fa fa-list-ol" aria-hidden="true"></i></i>Programação</a></li> 
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