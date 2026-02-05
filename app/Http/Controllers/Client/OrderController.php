<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\FormFilterRequest;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\Tec;
use App\Class\Logger;
use App\Class\TextFormat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\Controller;
use App\Http\Requests\FormOrderApiRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public readonly Order $os;
    public readonly User $user;
    public $logger; // logger class
    public $text; // text format functions
    public $client_name;
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
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->user->isCli()) {
            return redirect()->route('login.destroy')->withErrors(['error' => 'Acesso negado']);
        }

        // Create date start_date and end_date
        $start_date = Carbon::now()->subMonth()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');

        // get orders
        $orders = $this->os
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished')
            ->whereBetween('req_date', [$start_date, $end_date])
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->where('client_id', $this->user->clientId()) // Somente os orders do client logado
            ->orderBy('id', 'desc')
            ->get();

        $tecs = Tec::all();

        return view('order.orders_list', [
            'orders' => $orders,
            'tecs' => $tecs->sortBy('user.name'),
            'old_tec' => 'Técnico (todos)',
            'old_finished' => 2,
            'date_s' => $start_date,
            'date_e' => $end_date
        ]);
    }

    public function search(Request $request)
    {

        // If user is not suprevisor or administrator, redirect to login
        if (!$this->user->isCli()) {
            return redirect()->route('login.destroy')->withErrors(['error' => 'Acesso negado']);
        }

        $validated = $request->validate([
            'search' => 'required|numeric|max:999999999|min:1',
        ]);

        // get orders
        $orders = $this->os
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished')
            ->where('id', $validated['search'])
            ->where('client_id', $this->user->clientId()) // Somente os orders do client logado
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->get();

        $tecs = Tec::all();

        return view('order.orders_list', [
            'orders' => $orders,
            'order_ids' => $orders->pluck('id')->implode(','),
            'tecs' => $tecs->sortBy('user.name'),
            'old_client' => 'Cliente (todos)',
            'old_tec' => 'Técnico (todos)',
            'old_finished' => 2,
            'date_s' => Carbon::now()->subMonth()->format('Y-m-d'),
            'date_e' => Carbon::now()->format('Y-m-d'),
            'old_client' => $this->user->clientStringForUnlabeledDList(),
        ]);
    }

    // Show the form for filtering orders
    public function filter(FormFilterRequest  $request)
    {
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->user->isCli()) {
            return redirect()->route('login.destroy')->withErrors(['error' => 'Acesso negado']);
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
            // 1. Verificamos se o campo tec_id existe na requisição e não é uma string vazia/null
            ->when($request->filled('tec_id') || $request->tec_id === '0', function ($query) use ($request) {
                $tec = $request->tec_id;

                // 2. Se for '0' ou 0, buscamos os "sem técnico" (null ou 0)
                if ($tec === '0' || $tec == 0) {
                    $query->where(function ($q) {
                        $q->where('tec_id', 0)
                            ->orWhereNull('tec_id');
                    });
                }
                // 3. Se for um ID normal, filtra por ele
                else {
                    $query->where('tec_id', $tec);
                }
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
            ->where('client_id', $this->user->clientId()) // Somente as ordens do cliente logado
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->orderBy('id', 'desc')
            ->get();

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

        $tecs = Tec::all();

        return view('order.orders_list', [
            'orders' => $orders,
            'ids' => $order_ids ?? 0,
            'tecs' => $tecs->sortBy('user.name'),
            'date_s' => $request->date_start,
            'date_e' => $request->date_end,
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
        // If user is not a authorized client, redirect to login
        if (!$this->user->clientCanCreateSat()) {
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para abrir solicitações.');
        }

        // Create session variable wich contains all order types ids to validated in FormOrderRequest
        $types = OrderType::all();

        return view('order.order_create', [
            'tecs' => Tec::all(),
            'types' => $types
        ]);
    }

    // Create a new order
    public function store(FormOrderApiRequest $request)
    {

        // If user is not a authorized client, redirect to login
        if (!$this->user->clientCanCreateSat()) {
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para abrir solicitações.');
        }

        $request->validated();

        // Create new order
        $created = $this->os->create([
            'client_id' => $this->user->clientId(),
            'order_type_id' => $request->order_type_id,
            'sector' => $request->sector,
            'req_name' => $this->user->name,
            'user_id' => $this->user->id,
            'equipment' => $request->equipment,
            'req_date' => Carbon::now()->format('Y-m-d'),
            'req_time' => Carbon::now()->format('H:i:s'),
            'req_descr' => $this->text->spaceAfterPunctuation($request->req_descr),
            'is_emergency' => $request->is_emergency ?? false
        ]);

        $msg = $created ? 'Solicitação de Assistência Técnica criada com sucesso.' : 'Erro ao criar Solicitação de Assistência Técnica.';
        return redirect()->route('client.orders.index')->with('message', $msg);
    }

    // Shows the the order
    public function show($order)
    {
        // If user is not a authorized client, redirect to login
        if (!$this->user->clientCanSeeSat()) {
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para ver solicitações.');
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

        // If user is not a authorized client, redirect to login
        if (!$this->user->clientCanSeeSat()) {
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para ver solicitações.');
        }

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
