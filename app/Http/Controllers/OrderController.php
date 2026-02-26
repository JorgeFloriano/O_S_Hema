<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormFilterRequest;
use App\Http\Requests\FormOrderRequest;
use App\Models\Client;
use App\Models\NoteTec;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\Tec;
use App\Models\Cli;
use App\Models\User;
use App\Class\Logger;
use App\Class\TextFormat;
use App\Models\Material;
use App\Models\MaterialNote;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class OrderController extends Controller implements HasMiddleware
{
    public readonly Order $os;
    public readonly User $auth;
    public $logger; // logger class
    public $text; // text format functions

    public static function middleware(): array
    {
        return [
            // Usando o gate específico que não precisa de parâmetros extras na string
            new Middleware('can:view-sats', only: ['index', 'show', 'search', 'filter', 'show_pdf', 'edit']),

            new Middleware('can:manage-sats', only: ['create', 'store', 'update', 'destroy', 'reopen', 'orders_pdf', 'generate_pdf', 'orders_csv']),

            new Middleware('can:manage-attach-tec', only: ['ord_tec_update']),

            new Middleware('can:is-tec', only: ['finish']),

            new Middleware('can:is-main-adm', only: ['add', 'updateBusinessHours']),
        ];
    }

    public function __construct()
    {
        // Set a nem service order
        $this->os = new Order();

        // Set a logger
        $this->logger = new Logger();

        // Class with text format functions
        $this->text = new TextFormat;

        $this->auth = Auth::user();
    }

    public function index()
    {
        try {
            Gate::authorize('check-permission', ['sats', 1]);

            $clients = Client::select('id', 'name')->orderBy('name')->get();

            // create session variable wich contains 0 and all clients ids to validated in FormFilterRequest
            $cli_ids_array = $clients->pluck('id')->toArray();
            array_unshift($cli_ids_array, 0);
            session()->put('client_ids', $cli_ids_array);
            session()->put('reference_router_back', 'orders.index');

            // Create date start_date and end_date
            $start_date = Carbon::now()->subMonth()->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');

            // get orders
            $orders = $this->os
                ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished')
                ->whereNull('deleted_at') // Adicione esta linha explicitamente
                ->whereBetween('req_date', [$start_date, $end_date])
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
                'main' => $this->auth->isMainAdm() ?? null,
                'sup' => $this->auth->isSup() ?? null,
                'adm' => $this->auth->isAdm() ?? null,
                'old_client' => 'Cliente (todos)',
                'old_tec' => 'Técnico (todos)',
                'old_finished' => 2,
                'date_s' => $start_date,
                'date_e' => $end_date
            ]);
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            Gate::authorize('check-permission', ['sats', 1]);

            $validated = $request->validate([
                'search' => 'required|numeric|max:999999999|min:1',
            ]);

            // get orders
            $orders = $this->os
                ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished')
                ->where('id', $validated['search'])
                ->whereNull('deleted_at') // Adicione esta linha explicitamente
                ->get();

            $tecs = Tec::all();

            return view('order.orders_list', [
                'orders' => $orders,
                'order_ids' => $orders->pluck('id')->implode(','),
                'able_btn' => $able_btn ?? '',
                'tecs' => $tecs->sortBy('user.name'),
                'clients' => Client::select('id', 'name')->orderBy('name')->get(),
                'main' => $this->auth->isMainAdm() ?? null,
                'sup' => $this->auth->isSup() ?? null,
                'adm' => $this->auth->isAdm() ?? null,
                'old_client' => 'Cliente (todos)',
                'old_tec' => 'Técnico (todos)',
                'old_finished' => 2,
                'date_s' => Carbon::now()->subMonth()->format('Y-m-d'),
                'date_e' => Carbon::now()->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
        }
    }

    // Show the form for filtering orders
    public function filter(FormFilterRequest  $request)
    {
        try {
            Gate::authorize('check-permission', ['sats', 1]);

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
                ->when($request->client_id, function ($query) use ($request) {
                    $query->where('client_id', $request->client_id);
                })
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
                ->whereNull('deleted_at') // Adicione esta linha explicitamente
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

            // Substitua a lógica do $old_tec por esta:
            if ($request->tec_id === null || $request->tec_id === '') {
                $old_tec = 'Técnico (todos)';
            } elseif ($request->tec_id === '0' || $request->tec_id == 0) {
                $old_tec = 'Não selecionado - [0]';
            } else {
                $tec_model = Tec::with('user:id,name')->find($request->tec_id);
                $old_tec = $tec_model ? $tec_model->user->name . ' - [' . $tec_model->id . ']' : 'Técnico não encontrado';
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
                'main' => $this->auth->isMainAdm() ?? null,
                'sup' => $this->auth->isSup() ?? null,
                'adm' => $this->auth->isAdm() ?? null,
                'date_s' => $request->date_start,
                'date_e' => $request->date_end,
                'old_client' => $old_client ?? 'Cliente (todos)',
                'old_tec' => $old_tec ?? 'Técnico (todos)',
                'old_finished' => $request->finished ?? null,
                'fin_select' => $fin_select ?? ['', '', ''],
                'order_open_select' => $order_open_select,
                'last_note_select' => $last_note_select
            ]);
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
        }
    }

    // Show the form for creating a new order
    public function create()
    {
        try {
            // Bloqueia se o usuário não tiver permissão de escrita (nível 2)
            Gate::authorize('check-permission', ['sats', 2]);

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
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
        }
    }

    // Create a new order
    public function store(FormOrderRequest $request)
    {
        try {
            // Bloqueia se o usuário não tiver permissão de escrita (nível 2)
            Gate::authorize('check-permission', ['sats', 2]);

            $request->validated();

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
                'user_id' => $this->auth->id,
                'equipment' => $request->equipment,
                'req_date' => $request->req_date,
                'req_time' => $request->req_time,
                'req_descr' => $this->text->spaceAfterPunctuation($request->req_descr),
            ]);

            // Provisório, teste
            // Notification management when a Technical Assistance Request is opened by the client.
            // $created->notificationWhenOpenedByClient();

            $msg = $created ? 'Solicitação de Assistência Técnica criada com sucesso.' : 'Erro ao criar Solicitação de Assistência Técnica.';
            $route = $this->auth->isTec() && !$this->auth->isAdm() ? 'notes.index' : 'orders.index';
            $route = session('reference_router_back') ?? 'orders.index';
            return redirect()->route($route)->with('message', $msg);
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
        }
    }

    // Shows the form to delete the order
    public function show($order)
    {
        try {
            Gate::authorize('check-permission', ['sats', 1]);

            // Decrypt the order id
            try {
                $order = $this->os->find(Crypt::decryptString($order));
            } catch (DecryptException $e) {
                $this->logger->log('error', 'Decryption error (order/show).');
                return redirect()->back()->with('error', 'Erro de desencriptação (order/show).');
                die;
            }

            return view('order.order_delete', ['order' => $order]);
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
        }
    }

    // Shows the form to edit the order
    public function edit($order)
    {
        Gate::authorize('check-permission', ['sats', 1]);

        // Decrypt the order id
        try {
            $order = $this->os->find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            $this->logger->log('error', 'Decryption error (order/edit).');
            return redirect()->back()->with('error', 'Erro de desencriptação (order/edit).');
            die;
        }

        $clients = Client::select('id', 'name')->orderBy('name')->get();

        $tecs = Tec::all();

        // Create session variable wich contains all order types ids to validated in FormOrderRequest
        $types = OrderType::all();
        session()->put('types_ids', $types->pluck('id')->toArray());

        // Create session variable wich contains all clients ids to validated in FormOrderRequest
        $cli_ids_array = $clients->pluck('id')->toArray();
        session()->put('client_ids', $cli_ids_array);

        $user = User::select('name')->withTrashed()->find($order->user_id);

        // Remove seconds from requests time format
        $order->req_time = date_format(date_create($order->req_time), 'H:i');

        // If order is created by client or the user is not administrator can't edit it
        $ord_creator_is_cli = Cli::where('user_id', $order->user_id)->first();

        $disabled = '';
        $title = 'Editar ';
        $confirm_button = true;
        if (isset($ord_creator_is_cli) || !$this->auth->hasPermission('sats', 2) || (session('reference_router_back') == 'tec_on')) {
            $disabled = 'disabled';
            $title = 'Informações da ';
            $confirm_button = false;
        }


        return view('order.order_edit', [
            'order' => $order,
            'types' => $types,
            'clients' => $clients,
            'tecs' => $tecs,
            'user' => $user,
            'disabled' => $disabled,
            'title' => $title,
            'confirm_button' => $confirm_button
        ]);
    }

    // Only administrators can update orders.
    public function update(FormOrderRequest $request, string $id)
    {
        Gate::authorize('check-permission', ['sats', 2]);

        $request->validated();

        if ($request->client_id == '0') {
            return redirect()->back()->with('message', 'Selecione um cliente para prosseguir.');
        }

        $updated = $this->os->where('id', $id)->update($request->except(['_token', '_method', 'adm_id', 'tec_id', 'client', 'req_descr']));

        $os = Order::find($id);
        $os->user_id = $this->auth->id;
        $os->req_descr = $this->text->spaceAfterPunctuation($request->req_descr);
        $updated_adm = $os->save();

        $msg = $updated && $updated_adm ? 'Solicitação de Assistência Técnica atualizada com sucesso.' : 'Erro ao atualizar Solicitação de Assistência Técnica.';
        return redirect()->back()->with('message', $msg);
    }

    // Only administrators can delete orders
    public function destroy(string $id, FileService $fileService)
    {
        Gate::authorize('check-permission', ['sats', 2]);

        $order = $this->os->find($id);
        // Limpa o estado de emergência dos técnicos
        Tec::where('emergency_order_id', $order->id)->update([
            'emergency_order_id' => null,
            'emergency_notification_pending' => false
        ]);

        // Delete all notes of this order
        foreach ($order->notes as $key => $note) {
            // Primeiro apaga os arquivos (físico + banco)
            $fileService->deleteAllFiles($note);

            // Delete all tecs in note
            foreach ($note->tecs as $key => $tec) {
                $note_tec = NoteTec::where('note_id', $note->id)->where('tec_id', $tec->id)->first();
                $note_tec->delete();
            }

            // Delete all materials in note
            $material_notes = MaterialNote::where('note_id', $note->id)->get();
            if (count($material_notes) > 0 || $material_notes != null) {
                foreach ($material_notes as $material_note) {
                    $material_note->delete();
                }
            }

            $note->delete();
        }

        $deleted = $order->delete();

        $msg = $deleted ? 'Solicitação de Assistência Técnica deletada com sucesso.' : 'Erro ao deletar Solicitação de Assistência Técnica.';
        return redirect()->route('orders.index')->with('message', $msg);
    }

    public function finish($order)
    {

        Gate::authorize('is-tec');

        if (!$this->auth->isTec()) {
            logger_main('error', 'Access denied (order/finish).');
            return redirect()->route('orders.index')->with('message', 'Usuário sem acesso para finalizar SAT.');
        }

        // Decrypt the order id
        try {
            $order = $this->os->find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            $this->logger->log('error', 'Decryption error (order/finish).');
            return redirect()->back()->with('error', 'Erro de desencriptação (order/finish).');
            die;
        }

        $msg = $order->finish() ? 'Solicitação de Assistência Técnica finalizada com sucesso.' : 'Erro ao finalizar Solicitação de Assistência Técnica.';
        return redirect()->back()->with('message', $msg);
    }

    public function reopen($id)
    {
        Gate::authorize('check-permission', ['reopen_sat', 2]);

        if ($this->auth->reopenOrder($id)) {
            return redirect()->route(session('reference_router_back') ?? 'orders.index')->with('message', 'Solicitação de Assistência Técnica reaberta com sucesso.');
        }

        logger_main('error', 'Error reopening order.');
        return redirect()->route(session('reference_router_back') ?? 'orders.index')->with('message', 'Erro ao reabrir Solicitação de Assistência Técnica.');
    }

    // Shows the PDF for the order
    public function show_pdf($order)
    {
        Gate::authorize('check-permission', ['sats', 1]);

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

    // Starts the process of generating the report (generate front page)-----------------------------------------
    public function orders_pdf(Request $request)
    {
        Gate::authorize('check-permission', ['sats', 2]);

        if ($request->ids == 0 || $request->ids == '0') {
            $this->logger->log('error', 'Error, access denied (order/orders_pdf), no orders selected.');
            return redirect()->route('orders.index')->withErrors('Nenhum registro selecionado para gerar o relatório!');
        }

        // Verify if title have more than 120 charactersSolicitação de Assistência Técnica
        if (strlen($request->title) > 120) {
            $this->logger->log('error', 'Error, access denied (order/orders_pdf), title have more than 120 characters.');
            return redirect()->route('orders.index')->withErrors('Título da Solicitação de Assistência Técnica possui mais de 120 caracteres!');
        }

        // Generate an array of $request->ids (order filtered ids)
        $ids_array = explode(',', $request->ids);

        // Get all order ids
        $all_order_ids = Order::all()->pluck('id')->toArray();
        array_unshift($all_order_ids, 0);

        // Check if the ids are numeric and if they are in the database
        foreach ($ids_array as $id) {
            if (!is_numeric($id)) {
                $this->logger->log('error', 'Error, access denied (order/orders_pdf), invalid id (id is not numeric).');
                return redirect()->route('orders.index')->withErrors('ID da Solicitação de Assistência Técnica inválido (registro encontrado não numérico)!');
            }
            if (!in_array($id, $all_order_ids)) {
                $this->logger->log('error', 'Error, access denied (order/orders_pdf), invalid id (id not found in database).');
                return redirect()->route('orders.index')->withErrors('ID da Solicitação de Assistência Técnica inválido (registro não encontrado na base de dados)!');
            }
        }

        // Orders will be ordered by client name
        $orders = Order::whereIn('id', $ids_array)->with('client')->get();

        if ($orders->isEmpty() || !$orders) {
            $this->logger->log('error', 'Error (order/orders_pdf), error requesting order data.');
            return redirect()->route('orders.index')->withErrors('Erro ao requisar os dados das Solicitações de Assistência Técnica!');
        }
        $orders = $orders->sortBy('client.name');

        // Clear session variables from the previous report-----------------------------------------------
        session()->forget('order_count_client_ids');
        session()->forget('order_client_ids');
        session()->forget('page');
        session()->forget('order_index');
        session()->forget('expected_pages');

        foreach ($orders as $order) {
            if (!$order->finished) {
                $this->logger->log('error', 'Error (order/orders_pdf), order is not finished.');
                return redirect()->route('orders.index')->withErrors('Não é possível gerar um relatório com Solicitações de Assistência Técnica não finalizadas!');
            }
        }

        // Delete all pdf files in storage folder----------------------------------------------------
        $files = glob(public_path('storage/*.pdf'));
        foreach ($files as $file) {
            unlink($file);
        }


        // Group the orders by client---------------------------------------------------------------
        $ordersByClient = $orders->groupBy('client_id');

        // Expected number of pages of the final report for comparison with the real number of pages------------
        $pages = 1; // First page
        $clients = 1; // First client

        foreach ($ordersByClient as $orders) {
            $pages++;
            $clients++;

            foreach ($orders as $order) {
                $pages = $pages + count($order->notes);
                foreach ($order->notes as $note) {
                    if ($note->files->count() > 0) {
                        $pages++;
                    }

                    if ($note->files->count() > 9) {
                        $pages++;
                    }
                }
            }
        }

        $resume_pages = ceil($clients / 30);
        session()->put('expected_pages', $pages + $resume_pages);

        // Create session variables to control the report process-----------------------------------------------
        session()->put('order_count_client_ids', 0);
        session()->put('page', 1);
        session()->put('order_front', true);
        session()->put('order_index', 0);

        // Create an array with the orders ids for each client and the client names------------------------------
        $i = 0;
        $order_client_ids = [];
        foreach ($ordersByClient as $key => $orders) {
            $order_client_ids[$i]['name'] = $orders->first()->client->name;
            $order_client_ids[$i]['orders'] = [];
            foreach ($orders as $order) {
                $order_client_ids[$i]['orders'][] = $order->id;
            }
            $i++;
        }
        session()->put('order_client_ids', $order_client_ids);

        // Generate the report front page-----------------------------------------------------------------------
        $pdf = Pdf::loadView('order.report_parts.front', [
            'orders' => $orders,
            'title' => $request->title ?? 'Relatório de Solicitações de Assistência Técnica',
        ])->setPaper('A4', 'portrait');

        $front_page = $pdf->save('storage/0000_front_' . date('d_m_Y') . '.pdf');

        if (!$front_page) {
            $this->logger->log('error', 'Error (order/orders_pdf), error generating front page.');
            return redirect()->route('orders.index')->withErrors('Erro ao gerar capa do relatório !');
        }

        // Call the function to loop for each client and continue the report creation (pages and resume)---------
        return redirect()->route('orders.generate_pdf', ['msg' => 'continue']);
    }

    // Function to loop for each client continuing the report (pages and resume)----------------------------------   
    public function generate_pdf($msg)
    {
        Gate::authorize('check-permission', ['sats', 2]);

        if ($msg == 'continue') {
            if (session()->has('order_client_ids') || session()->has('order_count_client_ids')) {

                if (session('order_count_client_ids') < count(session('order_client_ids'))) {

                    // Get the orders for the current client
                    $order_ids = session('order_client_ids')[session('order_count_client_ids')]['orders'];
                    $order_id = $order_ids[session('order_index')];

                    // Generate the front client page report
                    if (session('order_front') == true) {

                        $client_name = session('order_client_ids')[session('order_count_client_ids')]['name'];

                        // Generate the PDF for the front page orders of the current client----------------------------------------------
                        $pdf = Pdf::loadView('order.report_parts.client_front', [
                            'client_name' => $client_name,
                            'count_orders' => count($order_ids)
                        ])->setPaper('A4', 'portrait');

                        session()->put('order_front', false);

                        if (!$pdf) {
                            $this->logger->log('error', 'Error (order/generate_pdf), error to generate front page of client ' . $$client_name . '.');
                            return redirect()->route('orders.index')->withErrors('Erro ao gerar capa do relatório do cliente ' . $$client_name . '!');
                        }
                    } else {
                        // Generate the PDF for the order of the current client-----------------------------------------------------------------------
                        $order = Order::find($order_id);

                        $pdf = Pdf::loadView('order.report_parts.client', [
                            'order' => $order,
                        ])->setPaper('A4', 'portrait');

                        if (!$pdf) {
                            $this->logger->log('error', 'Error (order/generate_pdf), error generating order number ' . $order_id . '.');
                            return redirect()->route('orders.index')->withErrors('Erro ao gerar Solicitação de Assistência Técnica número ' . $order_id . '!');
                        }

                        session()->put('order_index', session('order_index') + 1);
                    }

                    $percentage = intdiv((session('page') * 100), session('expected_pages'));

                    // Saves current client orders with name organized numerically
                    $save = $pdf->save('storage/' . $this->text->leftZeros(session('page')) . session('page') . '_' . $order_id . '_' . date('d_m_Y') . '.pdf');

                    if (!$save) {
                        $this->logger->log('error', 'Error (order/generate_pdf), error saving order number ' . $order_id . '.');
                        return redirect()->route('orders.index')->withErrors('Erro ao salvar a Solicitação de Assistência Técnica número ' . $order_id . '!');
                    }

                    if (session('order_index') >= count($order_ids)) {
                        session()->put('order_count_client_ids', session('order_count_client_ids') + 1);
                        session()->put('order_index', 0);
                        session()->put('order_front', true);
                    }

                    return view('order.generate_pdf', ['percentage' => $percentage])->with('message', 'Gerando relatório, aguarde...');
                }

                // Generate the PDF resume---------------------------------------------------------------------------------------
                $pdf = Pdf::loadView('order.report_parts.resume')->setPaper('A4', 'portrait');

                if (!$pdf) {
                    $this->logger->log('error', 'Error (order/generate_pdf), error generating resume.');
                    return redirect()->route('orders.index')->withErrors('Erro ao gerar o resumo do relatório !');
                }

                // Number for resume file name

                $save = $pdf->save('storage/' . $this->text->leftZeros(session('page')) . session('page') . '_resume_' . date('d_m_Y') . '.pdf');

                if (!$save) {
                    $this->logger->log('error', 'Error (order/generate_pdf), error saving resume.');
                    return redirect()->route('orders.index')->withErrors('Erro ao salvar o resumo do relatório !');
                }

                $oMerger = PDFMerger::init();

                $files = glob(public_path('storage/*.pdf'));

                if (!$files) {
                    $this->logger->log('error', 'Error (order/generate_pdf), error grouping files.');
                    return redirect()->route('orders.index')->withErrors('Erro ao agrupar os arquivos !');
                }

                foreach ($files as $file) {
                    $oMerger->addPDF($file, 'all');
                }

                $oMerger->merge();
                $oMerger->save('relatório.pdf');

                $file = public_path('relatório.pdf');

                // Get the total number of pages of the "relatório.pdf"
                $totalPages = $this->auth->adm->fileCountPages($file);

                // If dont exists $totalPages, if $totalPages < 4, if $totalPages != $expected_pages or any session variable doesnt exists return error
                if (
                    !isset($totalPages)
                    || $totalPages < 4
                    || $totalPages != session('expected_pages')
                    || !session('order_count_client_ids')
                    || !session('order_client_ids')
                    || !session('page')
                ) {
                    $this->logger->log('error', 'Error (order/generate_pdf), error finalizing report.');
                    return redirect()->route('orders.index')->withErrors('Erro ao finalizar o relatório !');
                }

                return $oMerger->stream('relatório.pdf');
            }

            $this->logger->log('error', 'Error (order/generate_pdf), Erro sessão ao gerar o relatório !');
            return redirect()->route('orders.index')->withErrors('Erro sessão ao gerar o relatório !');
        }

        $this->logger->log('error', 'Error (order/generate_pdf), Erro de parâmetro ao gerar o relatório !');
        return redirect()->route('orders.index')->withErrors('Erro de parâmetro ao gerar o relatório !');
    }

    // Only main administrators or supervisors can change the on call technician
    public function ord_tec_update(Request $request, $id)
    {
        Gate::authorize('check-permission', ['attach_tec', 1]);

        $order = session('ords')->where('id', $id)->first();

        $order->updateTecId($request->tec_id);
    }

    // Add orders for testing
    public function add($qtd)
    {
        Gate::authorize('is-main-adm');

        for ($i = 0; $i < $qtd; $i++) {
            //Create new orders
            $created = $this->os->create([
                'client_id' => 1,
                'order_type_id' => 1,
                'sector' => 'Sector' . $i,
                'req_name' => 'Solicitante' . $i,
                'user_id' => $this->auth->id,
                'tec_id' => $this->auth->tec()->first()->id,
                'equipment' => 'Equipamento' . $i,
                'req_date' => date('Y-m-d'),
                'req_time' => date('H:i'),
                'req_descr' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur aut sequi quaerat blanditiis est similique perspiciatis nihil cupiditate assumenda dignissimos sed iste fugit dicta consequuntur quae, explicabo voluptatem, laborum incidunt.' . $i
            ]);
        }

        $msg = $created ? 'Solicitações de Assistência Técnica para testes criadas com sucesso.' : 'Erro ao criar Solicitações de Assistência Técnica para testes.';
        $route = $this->auth->isTec() && !$this->auth->isAdm() ? 'notes.index' : 'orders.index';
        return redirect()->route($route)->with('message', $msg);
    }

    // Funcion to generate csv file of the selected orders
    public function orders_csv(Request $request)
    {
        Gate::authorize('check-permission', ['sats', 2]);

        if ($request->csv_ids == 0 || $request->csv_ids == '0') {
            $this->logger->log('error', 'Error, access denied (order/orders_csv), no orders selected.');
            return redirect()->route('orders.index')->withErrors('Nenhum registro selecionado para gerar o relatório!');
        }

        // Generate an array of $request->csv_ids (order filtered ids)
        $ids_array = explode(',', $request->csv_ids);

        // Get all order ids
        $all_order_ids = Order::all()->pluck('id')->toArray();
        array_unshift($all_order_ids, 0);

        // Check if the ids are numeric and if they are in the database
        foreach ($ids_array as $id) {
            if (!is_numeric($id)) {
                $this->logger->log('error', 'Error, access denied (order/orders_csv), invalid id (id is not numeric).');
                return redirect()->route('orders.index')->withErrors('ID da Solicitação de Assistência Técnica inválido (registro encontrado não numérico)!');
            }
            if (!in_array($id, $all_order_ids)) {
                $this->logger->log('error', 'Error, access denied (order/orders_csv), invalid id (id not found in database).');
                return redirect()->route('orders.index')->withErrors('ID da Solicitação de Assistência Técnica inválido (registro não encontrado na base de dados)!');
            }
        }

        // Orders will be ordered by client name
        $orders = Order::whereIn('id', $ids_array)->with('client')->get();

        if ($orders->isEmpty() || !$orders) {
            $this->logger->log('error', 'Error (order/orders_csv), error requesting order data.');
            return redirect()->route('orders.index')->withErrors('Erro ao requisar os dados das Solicitações de Assistência Técnica!');
        }
        $orders = $orders->sortBy('client.name');

        // Create the CSV file
        $fileName = 'dados_sat_hema_' . date('d_m_Y') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8 with BOM
            fwrite($file, "\xEF\xBB\xBF");

            // Add CSV headers
            $csv_headers = [
                'SAT',
                'Cliente',
                'Unidade',
                'Endereço',
                'Contato',
                'Setor',
                'Anotado por',
                'Data da SAT',
                'Hora',
                'Tipo de Serviço',
                'Equipamento',
                'Problema relatado',
                'Data da interv.',
                'Mod. equip.',
                'Nº série',
                'Tipo equip.',
                'Cód. Tipo',
                'Cód. Defeito',
                'Cód. Causa',
                'Cód. Solução',
            ];

            // IDs of all registered materials
            $materials = Material::withTrashed()->orderBy('description')->select('id', 'description', 'code')->get();

            // Add materials headers
            foreach ($materials as $key => $material) {
                array_push($csv_headers, $material->id . ' - ' . $material->completeDescription());
            }

            // Finish headers array
            array_push(
                $csv_headers,
                'Descrição do serviço',
                'Saída (ida)',
                'Chegada (ida)',
                'Início',
                'Término',
                'Saída (volta)',
                'Chegada (volta)',
                'Téc. 01',
                'Funç. téc. 01',
                'Téc. 02',
                'Funç. téc. 02',
                'Acomp. (cliente)',
                'Funç. acomp. (cliente)',
                'Contato acomp. (cliente)',
                'Data acomp. (cliente)',
            );

            fputcsv($file, $csv_headers, ';'); // Use semicolon as delimiter

            // Add rows wich the CSV data
            foreach ($orders as $order) {

                foreach ($order->notes as $key => $note) {

                    // Clean $note->services"
                    $services = trim($note->services); // Remove whitespaces
                    $services = str_replace(["\r", "\n"], ' ', $services); // Remove line breaks
                    $services = mb_convert_encoding($services, 'UTF-8', 'auto'); // UTF-8 encoding
                    $services = str_replace('"', '""', $services); // Escape double quotes
                    $services = str_replace(',', '-', $services); // Escape comma

                    // Clean $order->req_descr"
                    $req_descr = trim($order->req_descr); // Remove whitespaces
                    $req_descr = str_replace(["\r", "\n"], ' ', $req_descr); // Remove line breaks
                    $req_descr = mb_convert_encoding($req_descr, 'UTF-8', 'auto'); // UTF-8 encoding
                    $req_descr = str_replace('"', '""', $req_descr); // Escape double quotes
                    $req_descr = str_replace(',', '-', $req_descr); // Escape comma

                    // Clean $order->client->adress
                    $address = trim($order->client->address); // Remove whitespaces
                    $address = str_replace(["\r", "\n"], ' ', $address); // Remove line breaks
                    $address = mb_convert_encoding($address, 'UTF-8', 'auto'); // UTF-8 encoding
                    $address = str_replace('"', '""', $address); // Escape double quotes
                    $address = str_replace(',', '-', $address); // Escape comma

                    // Add CSV datas
                    $csv_datas = [
                        $order->id . '_' . $key + 1,
                        str_replace(',', '-', $order->client->name ?? ' '),
                        str_replace(',', '-', $order->client->unit ?? ' '),
                        $address ?? ' ',
                        str_replace(',', '-', $order->req_name ?? ' '),
                        str_replace(',', '-', $order->sector ?? ' '),
                        str_replace(',', '-', $order->user->name ?? ' '),
                        date('Y-m-d', strtotime($order->req_date) ?? ' '), // ISO 8601 format
                        date('H:i', strtotime($order->req_time) ?? ' '),
                        str_replace(',', '-', $order->type->description ?? ' '),
                        str_replace(',', '-', $order->equipment ?? ' '),
                        $req_descr ?? ' ',
                        date('Y-m-d', strtotime($note->date) ?? ' '), // ISO 8601 format
                        str_replace(',', '-', $note->equip_mod ?? ' '),
                        str_replace(',', '-', $note->equip_id ?? ' '),
                        str_replace(',', '-', $note->equip_type ?? ' '),
                        $note->type->id . ' - ' . str_replace(',', '-', $note->type->description ?? ' '),
                        $note->defect->id . ' - ' . str_replace(',', '-', $note->defect->description ?? ' '),
                        $note->cause->id . ' - ' . str_replace(',', '-', $note->cause->description ?? ' '),
                        $note->solution->id . ' - ' . str_replace(',', '-', $note->solution->description ?? ' '),
                    ];

                    // Add materials quantities
                    foreach ($materials as $key => $material) {
                        if (isset($note->materials)) {
                            foreach ($note->materials as $note_material) {
                                if ($material->id == $note_material->id) {
                                    array_push($csv_datas, $note_material->pivot->quantity);
                                    $mat_exists = true;
                                }
                            }
                        }

                        if (!isset($mat_exists)) {
                            array_push($csv_datas, 0);
                        } else {
                            if (!$mat_exists) {
                                array_push($csv_datas, 0);
                            }
                        }
                        $mat_exists = false;
                    }

                    // Finsh datas array
                    array_push(
                        $csv_datas,
                        $services,
                        $note->go_start ? date('H:i', strtotime($note->go_start)) : ' ',
                        $note->go_end ? date('H:i', strtotime($note->go_end)) : ' ',
                        $note->start ? date('H:i', strtotime($note->start)) : ' ',
                        $note->end ? date('H:i', strtotime($note->end)) : ' ',
                        $note->back_start ? date('H:i', strtotime($note->back_start)) : ' ',
                        $note->back_end ? date('H:i', strtotime($note->back_end)) : ' ',
                        str_replace(',', '-', $note->tecs[0]->user->name ?? ' '),
                        str_replace(',', '-', $note->tecs[0]->user->function ?? ' '),
                        str_replace(',', '-', $note->tecs[1]->user->name ?? ' '),
                        str_replace(',', '-', $note->tecs[1]->user->function ?? ' '),
                        str_replace(',', '-', $order->cl_name ?? ' '),
                        str_replace(',', '-', $order->cl_function ?? ' '),
                        str_replace(',', '-', $order->cl_contact ?? ' '),
                        $order->cl_date ? date('Y-m-d', strtotime($order->cl_date)) : ' ',
                    );

                    fputcsv($file, $csv_datas, ';');   // Use semicolon as delimiter
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Update business hours
    public function updateBusinessHours(Request $request)
    {
        try {
            Gate::authorize('is-main-adm');

            foreach ($request->hours as $dayIndex => $data) {
                DB::table('business_hours')->updateOrInsert(
                    ['day_of_week' => $dayIndex],
                    [
                        'start_time' => $data['start'] ?? '00:00',
                        'end_time'   => $data['end'] ?? '00:00',
                        'is_closed'  => isset($data['is_closed']), // true se o checkbox foi enviado
                        'updated_at' => now()
                    ]
                );
            }

            return redirect()->back()->with('message', 'Calendário atualizado!');
        } catch (\Exception $e) {
            logger_main('error', $e->getMessage());
            return redirect()->back()->with('message', 'Erro ao atualizar calendário.');
        }
    }
}
