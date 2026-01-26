<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\FormFilterRequest;
use App\Http\Requests\FormOrderRequest;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\Tec;
use App\Class\Logger;
use App\Class\TextFormat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public readonly Order $os;
    public readonly User $user;
    public $logger; // logger class
    public $text; // text format functions
    public function __construct()
    {
        // Set a nem service order
        $this->os = new Order();

        // Set a user
        $this->user = Auth::user();

        // Set a logger
        $this->logger = new Logger();

        // Class with text format functions
        $this->text = new TextFormat;
    }

    public function index()
    {
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        // create session variable wich contains 0 and all clients ids to validated in FormFilterRequest
        $cli_ids_array = $clients->pluck('id')->toArray();
        array_unshift($cli_ids_array, 0);
        session()->put('client_ids', $cli_ids_array);
        session()->put('reference_router_back', 'orders.index');

        // Create date start_date and end_date
        $start_date = \Carbon\Carbon::now()->subMonth()->format('Y-m-d');
        $end_date = \Carbon\Carbon::now()->format('Y-m-d');

        // get orders
        $orders = $this->os
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished')
            ->whereBetween('req_date', [$start_date, $end_date])
            ->where('client_id', $this->user->cli->client_id)
            ->orderBy('id', 'desc')
            ->get();

        session()->put('ords', $orders);

        // create an array with the orders ids for generate the pdf
        $order_ids = $orders->pluck('id')->implode(',');

        // Verify if the list of orders is not empty and if all orders are finished to ability "Gerar pdf" button
        $finisheds = $orders->pluck('finished')->toArray();
        if (in_array(0, $finisheds) || count($finisheds) == 0) {
            $able_btn = 'Não é possívle gerar arquivo de Solicitação de Assistência Técnica não finalizadas, tente filtar novamente';
        }

        $tecs = Tec::all();

        return view('order.orders_list', [
            'orders' => $orders,
            'order_ids' => $order_ids,
            'able_btn' => $able_btn ?? '',
            'tecs' => $tecs->sortBy('user.name'),
            'clients' => $clients,
            'main' => $this->m ?? null,
            'sup' => $this->s ?? null,
            'adm' => $this->a ?? null,
            'old_client' => 'Cliente (todos)',
            'old_tec' => 'Técnico (todos)',
            'old_finished' => 2,
            'date_s' => $start_date,
            'date_e' => $end_date
        ]);
    }

    // Show the form for filtering orders
    public function filter(FormFilterRequest  $request)
    {
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->s && !$this->a) {
            return view('login');
        }

        $request->validated();

        // Return the view with the last finished selected option
        $fin_select = [];
        for ($i = 0; $i < 3; $i++) {
            $fin_select[$i] = '';
            if ($i == $request->finished) {
                $fin_select[$i] = 'selected';
            }
        }

        // Return the view with the last date_type selected option
        $order_open_select = 'selected';
        $last_note_select = '';
        if ($request->date_type == 'last_note_date') {
            $order_open_select = '';
            $last_note_select = 'selected';
        }

        // If techician selected is "Não selecionado", get orders where tec_id is 0
        if ($request->tec_id == '0') {
            $tec_selected = '0';
        } elseif ($request->tec_id == null) {
            $tec_selected = null;
        } else {
            $tec_selected = $request->tec_id;
        }

        $orders = $this->os
            ->when($request->client_id, function ($query) use ($request) {
                $query->where('client_id', $request->client_id);
            })
            ->when($tec_selected, function ($query) use ($request) {
                $query->where('tec_id', $request->tec_id);
            })
            ->when($tec_selected == '0', function ($query) {
                $query->where('tec_id', '0')->orWhereNull('tec_id');
            })
            ->when($request->date_start, function ($query) use ($request) {
                if ($request->date_type == 'order_open_date') {
                    $query->where('req_date', '>=', $request->date_start);
                } elseif ($request->date_type == 'last_note_date') {
                    $query->where(function ($query) use ($request) {
                        $query->whereRaw("(
                        SELECT MAX(date)
                        FROM notes
                        WHERE notes.order_id = orders.id AND deleted_at IS NULL
                    ) >= ?", [$request->date_start]);
                    });
                }
            })
            ->when($request->date_end, function ($query) use ($request) {
                if ($request->date_type == 'order_open_date') {
                    $query->where('req_date', '<=', $request->date_end);
                } elseif ($request->date_type == 'last_note_date') {
                    $query->where(function ($query) use ($request) {
                        $query->whereRaw("(
                        SELECT MAX(date)
                        FROM notes
                        WHERE notes.order_id = orders.id AND deleted_at IS NULL
                    ) <= ?", [$request->date_end]);
                    });
                }
            })
            ->when($request->finished != 2, function ($query) use ($request) {
                $query->where('finished', $request->finished);
            })
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished')
            ->orderBy('id', 'desc')
            ->get();

        // create an array with the orders ids for generate the pdf
        $order_ids = $orders->pluck('id')->implode(',');

        if ($order_ids == '' || $order_ids == null) {
            $order_ids = 0;
        }

        // Verify if the list of orders is not empty and if all orders are finished to ability "Gerar pdf" button
        $finisheds = $orders->pluck('finished')->toArray();
        if (in_array(0, $finisheds) || count($finisheds) == 0) {
            $able_btn = 'Não é possívle gerar arquivo de Solicitação de Assistência Técnica não finalizadas, tente filtrar novamente';
        }

        //Last client selected
        $old_client = Client::select('id', 'name')->where('id', $request->client_id)->first();
        if ($old_client) {
            $old_client = $old_client->name . ' - [' . $old_client->id . ']';
        }

        // Last tecnician selected
        if ($tec_selected == '') {
            $old_tec = 'Técnico (todos)';
        } elseif ($tec_selected == '0') {
            $old_tec = 'Não selecionado - [0]';
        } else {
            $old_tec = Tec::with('user:id,name') // Eager load the user relationship
                ->where('id', $request->tec_id)
                ->first();
            if ($old_tec) {
                $old_tec = $old_tec->user->name . ' - [' . $old_tec->id . ']';
            }
        }

        // Orders list, to updated tecnicians
        session()->put('ords', $orders);

        $tecs = Tec::all();

        return view('order.orders_list', [
            'orders' => $orders,
            'ids' => $order_ids ?? 0,
            'able_btn' => $able_btn ?? '',
            'tecs' => $tecs->sortBy('user.name'),
            'clients' => Client::select('id', 'name')->orderBy('name')->get(),
            'main' => $this->m ?? null,
            'sup' => $this->s ?? null,
            'adm' => $this->a ?? null,
            'date_s' => $request->date_start,
            'date_e' => $request->date_end,
            'old_client' => $old_client ?? 'Cliente (todos)',
            'old_tec' => $old_tec ?? 'Técnico (todos)',
            'old_finished' => $request->finished ?? null,
            'fin_select' => $fin_select ?? ['', '', ''],
            'order_open_select' => $order_open_select,
            'last_note_select' => $last_note_select
        ]);
    }

    // Show the form for creating a new order
    public function create()
    {
        // If user is not administrator or on call technician, redirect to login
        if (!$this->a && !$this->o) {
            return view('login');
        }

        // Get id and name of all clients order by name
        $clients = Client::select('id', 'name')->orderBy('name')->get();

        // Create session variable wich contains all order types ids to validated in FormOrderRequest
        $types = OrderType::all();
        session()->put('types_ids', $types->pluck('id')->toArray());

        // Create session variable wich contains all clients ids to validated in FormOrderRequest
        $cli_ids_array = Client::all()->pluck('id')->toArray();
        session()->put('client_ids', $cli_ids_array);

        return view('order.order_create', [
            'clients' => $clients,
            'tecs' => Tec::all(),
            'types' => $types
        ]);
    }

    // Create a new order
    public function store(FormOrderRequest $request)
    {

        // If user is not administrator or on call technician, redirect to login
        if (!$this->a && !$this->o) {
            return view('login');
        }

        $request->validated();

        $auth = auth()->user();

        // If there is no contact name, then use the name of the client that was selected
        $cont_name_client = $request->req_name;
        if (!$request->req_name) {
            $cont_name_client = Client::find($request->client_id)->contact;
        }

        // Create new order
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'order_type_id' => $request->order_type_id,
            'sector' => $request->sector,
            'req_name' => $cont_name_client,
            'user_id' => $auth->id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $this->text->spaceAfterPunctuation($request->req_descr),
        ]);

        // Provisório, teste
        // Notification management when a Technical Assistance Request is opened by the client.
        // $created->notificationWhenOpenedByClient();

        $msg = $created ? 'Solicitação de Assistência Técnica criada com sucesso.' : 'Erro ao criar Solicitação de Assistência Técnica.';
        $route = $this->o && !$this->a ? 'notes.index' : 'orders.index';
        $route = session('reference_router_back') ?? 'orders.index';
        return redirect()->route($route)->with('message', $msg);
    }

    // Shows the form to delete the order
    public function show($order)
    {
        // Only administrator can delete orders
        if (!$this->a) {
            return view('login');
        }

        // Decrypt the order id
        try {
            $order = $this->os->find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            $this->logger->log('error', 'Decryption error (order/show).');
            return redirect()->back()->with('error', 'Erro de desencriptação (order/show).');
            die;
        }

        return view('order.order_delete', ['order' => $order]);
    }

    // Shows the PDF for the order
    public function show_pdf($order)
    {
        // Decrypt the order id
        try {
            $order = $this->os->find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            $this->logger->log('error', 'Decryption error (order/show_pdf).');
            return redirect()->back()->with('error', 'Erro de desencriptação (order/show_pdf).');
            die;
        }

        // Null values will be replaced by - - : - - and the time will be formatted without seconds
        $order->notes_time_format();

        return view('order.order_pdf', ['order' => $order]);
    }
}
