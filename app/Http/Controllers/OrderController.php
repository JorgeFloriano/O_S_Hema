<?php

namespace App\Http\Controllers;

use App\Http\Requests\FormFilterRequest;
use App\Http\Requests\FormOrderRequest;
use App\Models\Client;
use App\Models\NoteTec;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\Tec;
use App\Models\User;
use App\Class\Logger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Barryvdh\DomPDF\Facade\Pdf;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class OrderController extends Controller
{
    public readonly Order $os;
    public $m; // main administrator
    public $s; // supervisor
    public $a; // administrator
    public $t; // technician
    public $o; // technician on call
    public function __construct()
    {
        // Set a nem service order
        $this->os = new Order();

        // Set a logger
        $this->logger = new Logger();

        // user is admin main or not
        if (isset(auth()->user()->adm)) {
            $this->m = auth()->user()->adm()->first()->main;
        }

        // user is technician on call or not
        if (isset(auth()->user()->tec)) {
            $this->o = auth()->user()->tec()->first()->on_call;
        }

        // user is supervisor or not
        $this->s = auth()->user()->sup()->first();

        // user is administrator or not
        $this->a = auth()->user()->adm()->first();

        // user is technician or not
        $this->t = auth()->user()->tec()->first();
    }

    public function index()
    {
        // If user is not suprevisor or administrator, redirect to login
        if (!$this->s && !$this->a) {
            return view('login');
        }

        $clients = Client::select('id', 'name')->orderBy('name')->get();

        // create session variable wich contains 0 and all clients ids to validated in FormFilterRequest
        $cli_ids_array = $clients->pluck('id')->toArray();
        array_unshift($cli_ids_array, 0);
        session()->put('client_ids', $cli_ids_array);

        // get orders
        $orders = $this->os
            ->select('id', 'order_type_id', 'client_id', 'tec_id', 'req_date', 'finished')
            ->orderBy('id', 'desc')
            ->get();

        session()->put('ords', $orders);

        // create an array with the orders ids for generate the pdf
        $order_ids = $orders->pluck('id')->implode(',');

        // Verify if the list of orders is not empty and if all orders are finished to ability "Gerar pdf" button
        $finisheds = $orders->pluck('finished')->toArray();
        if (in_array(0, $finisheds) || count($finisheds) == 0) {
            $show_pdf_btn = 'disabled';
        }

        return view('order.orders_list', [
            'orders' => $orders,
            'order_ids' => $order_ids,
            'show_pdf_btn' => $show_pdf_btn ?? '',
            'tecs' => Tec::all(),
            'clients' => $clients,
            'main' => $this->m ?? null,
            'sup' => $this->s ?? null,
            'adm' => $this->a ?? null,
            'old_client' => 0,
            'old_finished' => 2,
            'date_s' => \Carbon\Carbon::now()->subMonth()->format('Y-m-d'),
            'date_e' => \Carbon\Carbon::now()->format('Y-m-d'),
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

        // create session variable wich contains 0 and all clients ids to validated in FormFilterRequest
        $cli_ids_array = Client::all()->pluck('id')->toArray();
        array_unshift($cli_ids_array, 0);
        session()->put('client_ids', $cli_ids_array);

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
            ->when($request->client, function ($query) use ($request) {
                $query->where('client_id', $request->client);
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
            ->select('id', 'order_type_id', 'client_id', 'tec_id', 'req_date', 'finished')
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
            $show_pdf_btn = 'disabled';
        }

        session()->put('ords', $orders);

        return view('order.orders_list', [
            'orders' => $orders,
            'ids' => $order_ids ?? 0,
            'show_pdf_btn' => $show_pdf_btn ?? '',
            'tecs' => Tec::all(),
            'clients' => Client::select('id', 'name')->orderBy('name')->get(),
            'main' => $this->m ?? null,
            'sup' => $this->s ?? null,
            'adm' => $this->a ?? null,
            'date_s' => $request->date_start,
            'date_e' => $request->date_end,
            'old_client' => $request->client ?? null,
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

        // If there is no contact name, then use the name of the client that was selected
        $cont_name_client = $request->req_name;
        if (!$request->req_name) {
            $cont_name_client = Client::find($request->client_id)->contact;
        }

        // If user that is creating the order is a technician, set the order tec_id to the tec_id itself
        $tec_id = null;
        if (isset(auth()->user()->tec)) {
            $tec_id = auth()->user()->tec->id;
        }

        //Create new order
        $created = $this->os->create([
            'client_id' => $request->client_id,
            'order_type_id' => $request->order_type_id,
            'sector' => $request->sector,
            'req_name' => $cont_name_client,
            'user_id' => auth()->user()->id,
            'tec_id' => $tec_id,
            'equipment' => $request->equipment,
            'req_date' => $request->req_date,
            'req_time' => $request->req_time,
            'req_descr' => $request->req_descr,
        ]);

        $msg = $created ? 'Ordem de serviço criada com sucesso.' : 'Erro ao criar ordem de serviço.';
        $route = $this->o && !$this->a ? 'notes.index' : 'orders.index';
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
        }

        return view('order.order_delete', ['order' => $order]);
    }

    // Shows the form to edit the order
    public function edit($order)
    {
        // Only administrators can edit orders, supervisors can just see them
        if (!$this->a && !$this->s) {
            return view('login');
        }

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

        $user = User::select('name')->find($order->user_id);

        // Remove seconds from requests time format
        $order->req_time = date_format(date_create($order->req_time), 'H:i');

        $disabled = $this->a ? '' : 'disabled';
        $title = $this->a ? 'Editar ' : 'Informações da ';

        return view('order.order_edit', [
            'order' => $order,
            'types' => $types,
            'clients' => $clients,
            'tecs' => $tecs,
            'user' => $user,
            'disabled' => $disabled,
            'title' => $title
        ]);
    }

    // Only administrators can update orders.
    public function update(FormOrderRequest $request, string $id)
    {
        if (!$this->a) {
            return view('login');
        }

        $request->validated();

        if ($request->client_id == '0') {
            return redirect()->back()->with('message', 'Selecione um cliente para prosseguir.');
        }

        $updated = $this->os->where('id', $id)->update($request->except(['_token', '_method', 'adm_id', 'tec_id']));

        $os = Order::find($id);
        $os->user_id = auth()->user()->id;
        $updated_adm = $os->save();

        $msg = $updated && $updated_adm ? 'Ordem de serviço atualizada com sucesso.' : 'Erro ao atualizar ordem de serviço.';
        return redirect()->back()->with('message', $msg);
    }

    // Only administrators can delete orders
    public function destroy(string $id)
    {
        if (!$this->a) {
            return view('login');
        }

        $order = $this->os->find($id);

        foreach ($order->notes as $key => $note) {
            foreach ($note->tecs as $key => $tec) {
                $note_tec = NoteTec::where('note_id', $note->id)->where('tec_id', $tec->id)->first();
                $note_tec->delete();
            }
            $note->delete();
        }

        $deleted = $order->delete();

        $msg = $deleted ? 'Ordem de serviço deletada com sucesso.' : 'Erro ao deletar ordem de serviço.';
        return redirect()->route('orders.index')->with('message', $msg);
    }

    public function finish($order)
    {
        if (!$this->t) {
            return view('login');
        }

        // Decrypt the order id
        try {
            $order = $this->os->find(Crypt::decryptString($order));
        } catch (DecryptException $e) {
            $this->logger->log('error', 'Decryption error (order/finish).');
            return redirect()->back()->with('error', 'Erro de desencriptação (order/finish).');
            die;
        }

        $order->finished = true;
        $updated = $order->save();

        $msg = $updated ? 'Ordem de serviço finalizada com sucesso.' : 'Erro ao finalizar Ordem de serviço.';
        return redirect()->back()->with('message', $msg);
    }

    public function reopen($id)
    {
        if (!$this->s) {
            return view('login');
        }

        if ($this->s->reopenOrder($id)) {
            return redirect()->route('orders.index')->with('message', 'Ordem de serviço reaberta com sucesso.');
        }
        return redirect()->route('orders.index')->with('message', 'Erro ao reabrir Ordem de Serviço.');
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

    // Starts the process of generating the report (generate front page)----------------------------------------------------------------------
    public function orders_pdf(Request $request)
    {
        if (!$this->a) {
            $this->logger->log('error', 'Error, access denied (order/orders_pdf), user is not an administrator.');
            return view('login');
        }

        if ($request->ids == 0 || $request->ids == '0') {
            $this->logger->log('error', 'Error, access denied (order/orders_pdf), no orders selected.');
            return redirect()->route('orders.index')->withErrors('Nenhum registro selecionado para gerar o relatório!');
        }

        // Orders will be ordered by client name
        $orders = Order::whereIn('id', explode(',', $request->ids))->with('client')->get();

        if ($orders->isEmpty() || !$orders) {
            $this->logger->log('error', 'Error (order/orders_pdf), error requesting order data.');
            return redirect()->route('orders.index')->withErrors('Erro ao requisar os dados das ordens de serviço!');
        }

        $orders = $orders->sortBy('client.name');

        // Clear session variables from the previous report-----------------------------------------------
        session()->forget('order_count_client_ids');
        session()->forget('order_client_ids');
        session()->forget('page');

        foreach ($orders as $order) {
            if (!$order->finished) {
                $this->logger->log('error', 'Error (order/orders_pdf), order is not finished.');
                return redirect()->route('orders.index')->withErrors('Não é possível gerar um relatório com ordens de serviço não finalizadas!');
            }
        }

        // Delete all pdf files in storage folder----------------------------------------------------
        $files = glob(public_path('storage/*.pdf'));
        foreach ($files as $file) {
            unlink($file);
        }


        // Group the orders by client---------------------------------------------------------------
        $ordersByClient = $orders->groupBy('client_id');

        // Expected number of pages of the final report for comparison with the real number of pages---------------------
        $pages = 1; // First page
        $clients = 1; // First client

        foreach ($ordersByClient as $orders) {
            $pages++;
            $clients++;

            foreach ($orders as $order) {
                $pages = $pages + count($order->notes);
            }
        }

        $resume_pages = ceil($clients / 30);

        session()->put('expected_pages', $pages + $resume_pages);

        // Create an array with the orders ids for each client and the client names-----------------------------------------------
        $i = 0;
        $order_client_ids = [];
        session()->put('dot', '');
        session()->put('order_count_client_ids', 0);
        session()->put('page', 1);

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
            'title' => $request->title ?? 'Relatório de Ordem de serviço',
        ])->setPaper('A4', 'portrait');

        $front_page = $pdf->save('storage/00_front_' . date('d_m_Y') . '.pdf');

        if (!$front_page) {
            $this->logger->log('error', 'Error (order/orders_pdf), error generating front page.');
            return redirect()->route('orders.index')->withErrors('Erro ao gerar capa do relatório !');
        }

        // Call the function to loop for each client and continue the report creation (pages and resume)----------------------------------
        return redirect()->route('orders.generate_report', ['msg' => 'continue']);
    }

    // Function to loop for each client continuing the report (pages and resume)--------------------------------------------------
    public function generate_report($msg)
    {

        if ($msg == 'continue') {
            if (session()->has('order_client_ids') || session()->has('order_count_client_ids')) {
                if (session('order_count_client_ids') < count(session('order_client_ids'))) {

                    // Get the orders for the current client
                    $orders = Order::whereIn('id', session('order_client_ids')[session('order_count_client_ids')]['orders'])->get();

                    if ($orders->isEmpty() || !$orders) {
                        $this->logger->log('error', 'Error (order/generate_report), error requesting order data.');
                        return redirect()->route('orders.index')->withErrors('Erro ao requisar os dados das ordens de serviço do cliente ' . session('order_client_ids')[session('order_count_client_ids')]['name'] . '!');
                    }

                    // Client id index to increment
                    session()->put('order_count_client_ids', session('order_count_client_ids') + 1);

                    // Generate the PDF for the orders of the current client-----------------------------------------------------------------------
                    $pdf = Pdf::loadView('order.report_parts.client', [
                        'orders' => $orders,
                    ])->setPaper('A4', 'portrait');

                    if (!$pdf) {
                        $this->logger->log('error', 'Error (order/generate_report), error generating '.$orders->first()->client->name.' client pages.');
                        return redirect()->route('orders.index')->withErrors('Erro ao gerar a paginas do cliente ' . $orders->first()->client->name . '!');
                    }

                    // Zero on left for the file name
                    $zero = '0';
                    if ($zero . session('order_count_client_ids') < 10) {
                        $zero = '0';
                    } else {
                        $zero = '';
                    }

                    $percentage = intdiv((session('order_count_client_ids') * 100), count(session('order_client_ids')));

                    if (session('dot') == '') {session()->put('dot', ' .');}
                    elseif (session('dot') == ' .') {session()->put('dot', ' . .');}
                    elseif (session('dot') == ' . .') {session()->put('dot', ' . . .');}
                    elseif (session('dot') == ' . . .') {session()->put('dot', '');}

                    // Saves current client orders with name organized numerically
                    $save = $pdf->save('storage/' . $zero . session('order_count_client_ids') . '_' . $orders->first()->client->name . '_' . date('d_m_Y') . '.pdf');

                    if (!$save) {
                        $this->logger->log('error', 'Error (order/generate_report), error saving ' . $orders->first()->client->name . ' client pages.');
                        return redirect()->route('orders.index')->withErrors('Erro ao salvar a paginas do cliente ' . $orders->first()->client->name . '!');
                    }

                    return view('order.generate_report', ['percentage' => $percentage])->with('message', 'Gerando relatório, aguarde...');
                }

                // Generate the PDF resume---------------------------------------------------------------------------------------
                $pdf = Pdf::loadView('order.report_parts.resume')->setPaper('A4', 'portrait');

                if (!$pdf) {
                    $this->logger->log('error', 'Error (order/generate_report), error generating resume.');
                    return redirect()->route('orders.index')->withErrors('Erro ao gerar o resumo do relatório !');
                }

                // Zero on left for the file name
                $zero = '0';
                if ($zero . session('order_count_client_ids') < 10) {
                    $zero = '0';
                } else {
                    $zero = '';
                }

                // Number for resume file name
                $number = session('order_count_client_ids') + 1;


                $save = $pdf->save('storage/' . $zero . $number . '_resume_' . date('d_m_Y') . '.pdf');

                if (!$save) {
                    $this->logger->log('error', 'Error (order/generate_report), error saving resume.');
                    return redirect()->route('orders.index')->withErrors('Erro ao salvar o resumo do relatório !');
                }

                $oMerger = PDFMerger::init();

                $files = glob(public_path('storage/*.pdf'));

                if (!$files) {
                    $this->logger->log('error', 'Error (order/generate_report), error grouping files.');
                    return redirect()->route('orders.index')->withErrors('Erro ao agrupar os arquivos !');
                }

                foreach ($files as $file) {
                    $oMerger->addPDF($file, 'all');
                }

                $oMerger->merge();
                $oMerger->save('relatório.pdf');

                $file = public_path('relatório.pdf');

                // Get the total number of pages of the "relatório.pdf"
                $totalPages = $this->a->fileCountPages($file);

                // If dont exists $totalPages, if $totalPages < 4, if $totalPages != $expected_pages or any session variable doesnt exists return error
                if (!isset($totalPages) 
                || $totalPages < 4 
                || $totalPages != session('expected_pages')
                || !session('order_count_client_ids')
                || !session('order_client_ids')
                || !session('page'))
                {
                    $this->logger->log('error', 'Error (order/generate_report), error finalizing report.');
                    return redirect()->route('orders.index')->withErrors('Erro ao finalizar o relatório !');
                }

                return $oMerger->stream('relatório.pdf');
            }

            return redirect()->route('orders.index')->withErrors('Erro sessão ao gerar o relatório !');
        }

        return redirect()->route('orders.index')->withErrors('Erro de parâmetro ao gerar o relatório !');
    }

    // Only main administrators or supervisors can change the on call technician
    public function ord_tec_update(Request $request)
    {
        if (!$this->s && !$this->m) {
            return view('login');
        }

        $ords = session('ords');

        foreach ($ords as  $ord) {
            if ($request->input('ord_' . $ord->id) != null) {
                $ord->tec_id = $request->input('ord_' . $ord->id);
                $ord = $ord->save();
                if (!$ord) {
                    return redirect()->route('orders.index')->with('message', 'Erro ao selecionar técnico.');
                }
            }
        }

        return redirect()->route('orders.index')->with('message', 'Técnico selecionado com sucesso.');
    }
}
