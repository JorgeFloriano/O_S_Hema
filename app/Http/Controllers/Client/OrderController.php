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
use App\Http\Requests\Clients\FormOrderRequest;
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

    public function index(FormFilterRequest $request)
    {
        try {

            // If user is not a authorized client, redirect to login
            if (!$this->user->isCli()) {
                logger_main('error', "Acesso Negado");
                return redirect()->route('login.destroy')->withErrors(['error' => 'Acesso negado']);
            }

            // get orders
            $query = $this->os->select(
                'id',
                'order_type_id',
                'req_descr',
                'req_name',
                'equipment',
                'sector',
                'client_id',
                'user_id',
                'tec_id',
                'req_date',
                'req_time',
                'finished',
                'is_emergency'
            )
                ->whereNull('deleted_at') // Adicione esta linha explicitamente
                ->where('client_id', $this->user->userClientCompanyId()); // Somente os orders do client logado

            // 2. Apply Filters (Logical check: if request has data, use it. Otherwise, use defaults for index)
            $date_s = $request->input('date_start', Carbon::now()->subMonth()->format('Y-m-d'));
            $date_e = $request->input('date_end', Carbon::now()->format('Y-m-d'));
            $finished = $request->input('finished', 2); // Default to 2 (All)
            $date_type = $request->input('date_type', "order_open_date"); // Default to req_date

            // Filter by Date Type
            if ($request->date_type == 'last_note_date') {
                $query->whereRaw("(SELECT MAX(date) FROM notes WHERE notes.order_id = orders.id AND deleted_at IS NULL) BETWEEN ? AND ?", [$date_s, $date_e]);
            } else {
                $query->whereBetween('req_date', [$date_s, $date_e]);
            }

            // Filter by Finished status
            $query->when($finished != 2, function ($q) use ($finished) {
                $q->where('finished', $finished);
            });

            // 3. Execution with Pagination
            // appends(request()->all()) is CRITICAL for the "Next Page" links to work with filters
            $orders = $query->orderBy('id', 'desc')->simplePaginate(100)->appends($request->all());

            return view('order.orders_list', [
                'orders' => $orders,
                'old_tec' => 'Técnico (todos)',
                'old_finished' => 2,
                'date_s'    => $date_s,
                'date_e'    => $date_e,
                'old_date_type' => $date_type,
                'old_finished' => $finished,
            ]);
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
            return back()->withErrors('Erro ao processar listagem.');
        }
    }

    public function search(Request $request)
    {
        // If user is not a authorized client, redirect to login
        if (!$this->user->isCli()) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('login.destroy')->withErrors(['error' => 'Acesso negado']);
        }

        $validated = $request->validate([
            'search' => 'required|numeric|max:999999999|min:1',
        ]);

        // get orders
        $orders = $this->os
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished', 'is_emergency')
            ->where('id', $validated['search'])
            ->where('client_id', $this->user->userClientCompanyId()) // Somente os orders do client logado
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->get();

        return view('order.orders_list', [
            'orders' => $orders,
            'order_ids' => $orders->pluck('id')->implode(','),
            'old_client' => 'Cliente (todos)',
            'old_tec' => 'Técnico (todos)',
            'old_finished' => 2,
            'date_s' => Carbon::now()->subMonth()->format('Y-m-d'),
            'date_e' => Carbon::now()->format('Y-m-d'),
            'old_client' => $this->user->clientStringForUnlabeledDList(),
            'old_date_type' => 'order_open_date',
        ]);
    }

    // Show the form for filtering orders
    public function filter(FormFilterRequest  $request)
    {
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->user->isCli()) {
            logger_main('error', "Acesso Negado");
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

        $orders = $this->os
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
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished', 'is_emergency')
            ->where('client_id', $this->user->userClientCompanyId()) // Somente as ordens do cliente logado
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->orderBy('id', 'desc')
            ->get();

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
            logger_main('error', "Acesso Negado");
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
    public function store(FormOrderRequest $request)
    {

        // If user is not a authorized client, redirect to login
        if (!$this->user->clientCanCreateSat()) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para abrir solicitações.');
        }

        $request->validated();

        // Create new order
        $created = $this->os->create([
            'client_id' => $this->user->userClientCompanyId(),
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

        // Notification management when a Technical Assistance Request is opened by the client.
        $created->notificationWhenOpenedByClient();

        $msg = $created ? 'Solicitação de Assistência Técnica criada com sucesso.' : 'Erro ao criar Solicitação de Assistência Técnica.';
        return redirect()->route('client.orders.index')->with('message', $msg);
    }

    // Shows the the order
    public function show($order)
    {
        // Decrypt the order id
        try {
            $order = $this->os->find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            $this->logger->log('error', 'Decryption error (order/show).');
            return redirect()->back()->with('error', 'Erro de desencriptação (order/show).');
            die;
        }

        // If user is not a authorized client, redirect to orders index
        if (!$this->user->clientCanSeeSat($order)) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para ver solicitações.');
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

        // If user is not a authorized client, redirect to login
        if (!$this->user->clientCanSeeSat($order)) {
            logger_main('error', "Acesso Negado");
            return redirect()->route('client.orders.index')->with('message', 'Usuário sem permissão para ver solicitações.');
        }

        // Null values will be replaced by - - : - - and the time will be formatted without seconds
        $order->notes_time_format();

        return view('order.order_pdf', ['order' => $order]);
    }
}
