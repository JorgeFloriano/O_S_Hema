<?php

namespace App\Http\Controllers\Api\Team;

use App\Class\TextFormat;
use App\Class\ResponseJson;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SatTeamApiController extends Controller
{
    public $can;
    public $text;
    public readonly User $auth;

    public function __construct()
    {
        $this->can = new ResponseJson();

        // Class with text format functions
        $this->text = new TextFormat;

        $this->auth = Auth::user();
    }
    /**
     * Display a listing of the resource.
     */
    public function apiIndex(Request $request)
    {
        // Get orders with relationships
        // The user data (id, name, surname) is now automatically loaded
        //$order->tec->user will contain only id, name, surname

        //Check if user is has permission to see Sat
        if (!$this->auth->hasPermission('sats', 1)) {
            logger_main('error', 'Usuário sem permissão para ver listagem de SATs.');
            return $this->can->array(false, 'Usuário sem permissão para ver listagem de SATs.', 403);
        }

        // Tratamento do tec_id
        if ($request->tec_id === '0') {
            $tec_selected = '0';
        } elseif ($request->tec_id == null) {
            $tec_selected = null;
        } else {
            $tec_selected = $request->tec_id;
        }

        // ATENÇÃO: Atribuindo o encadeamento à variável $query
        $query = Order::query()
            ->with(['client:id,name', 'tec.user:id,name', 'type:id,description']) // Eager loading essencial para o Mobile
            ->when($request->client_id, function ($q) use ($request) {
                $q->where('client_id', $request->client_id);
            })
            ->when($tec_selected && $tec_selected !== '0', function ($q) use ($request) {
                $q->where('tec_id', $request->tec_id);
            })
            ->when($tec_selected === '0', function ($q) {
                // Usa um agrupamento where para não quebrar outros filtros com o orWhere
                $q->where(function ($sub) {
                    $sub->where('tec_id', '0')->orWhereNull('tec_id');
                });
            })
            ->when($request->date_type == 'last_note_date', function ($q) use ($request) {
                // Garante que a SAT tenha notas se o filtro for por nota
                $q->whereHas('notes', function ($sub) use ($request) {
                    if ($request->filled('date_start')) {
                        $sub->where('date', '>=', $request->date_start);
                    }
                    if ($request->filled('date_end')) {
                        $sub->where('date', '<=', $request->date_end);
                    }
                });
            }, function ($q) use ($request) {
                // Caso contrário (order_open_date), filtra na tabela orders
                if ($request->filled('date_start')) {
                    $q->where('req_date', '>=', $request->date_start);
                }
                if ($request->filled('date_end')) {
                    $q->where('req_date', '<=', $request->date_end);
                }
            })
            ->when($request->has('finished') && $request->finished != 2, function ($q) use ($request) {
                $q->where('finished', $request->finished);
            });

        // Executa a busca
        $orders = $query->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished', 'is_emergency')
            ->whereNull('deleted_at') // Adicione esta linha explicitamente;
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        return response()->json([
            'orders' => $orders, // Collection é convertida automaticamente para array JSON
        ]);
    }

    public function search(Request $request)
    {
        //Check if user is has permission to see Sat
        if (!$this->auth->hasPermission('sats', 1)) {
            logger_main('error', 'Usuário sem permissão para procurar SAT.');
            return $this->can->array(false, 'Usuário sem permissão para procurar SAT.', 403);
        }

        $validated = $request->validate([
            'search' => 'required|numeric|max:999999999|min:1',
        ]);

        // get orders
        $orders = Order::with(['client:id,name', 'tec.user:id,name', 'type:id,description'])
            ->select('id', 'order_type_id', 'req_descr', 'req_name', 'equipment', 'sector', 'client_id', 'user_id', 'tec_id', 'req_date', 'req_time', 'finished', 'is_emergency')
            ->where('id', $validated['search'])
            ->whereNull('deleted_at') // Adicione esta linha explicitamente
            ->get();

        return response()->json([
            'orders' => $orders, // Collection é convertida automaticamente para array JSON
        ]);
    }

    public function update_tec(Request $request, $id)
    {
        // Check if user is a supervisor that can attach a technician
        if (!$this->auth->hasPermission('attach_tec')) {
            logger_main('error', 'Usuário sem permissão para anexar técnico.');
            return $this->can->array(false, 'Usuário sem permissão para anexar técnico.', 403);
        }

        // Get the order
        $order = Order::findOrFail($id);

        // Update the order tec_id
        return response()->json($order->updateTecId($request->tec_id));
    }

    public function reopen($id)
    {
        // Check if user is a supervisor that can attach a technician
        if (!$this->auth->hasPermission('reopen_sat')) {
            logger_main('error', 'Usuário sem permissão para reabrir Solicitação de Assistência Técnica.');
            return $this->can->array(false, 'Usuário sem permissão para reabrir Solicitação de Assistência Técnica.', 403);
        }

        if ($this->auth->reopenOrder($id)) {
            return $this->can->array(true, 'Solicitação de Assistência Técnica reaberta com sucesso.', 200);
        }

        logger_main('error', 'Erro ao reabrir Solicitação de Assistência Técnica.');
        return $this->can->array(false, 'Erro ao reabrir Solicitação de Assistência Técnica.', 500);
    }

    public function download_pdf($id)
    {
        // Check if user is a supervisor that can attach a technician
        if (!$this->auth->hasPermission('sats', 1)) {
            logger_main('error', 'Usuário sem permissão para fazer download da SAT.');
            return $this->can->array(false, 'Usuário sem permissão para fazer download da SAT.', 403);
        }

        // Get the order
        $order = Order::findOrFail($id);

        if (!$order->finished) {
            logger_main('error', 'Error order is not finished.');
            return $this->can->array(false, 'Não é possível gerar um relatório com SAT não finalizada!', 403);
        }

        $order->clearPreviousReportSessionVariables();

        $order->deleteAllPdfsInStorageFolder();

        try {
            // Generate the PDF for the order of the current client
            $pdf = Pdf::loadView('order.report_parts.client', [
                'order' => $order,
            ])->setPaper('A4', 'portrait');

            // 2. Limpa qualquer lixo que o PHP tenha colocado no buffer de saída
            if (ob_get_length()) ob_clean();

            $filename = 'sat_' . $order->id . '_' . date('d_m_Y') . '.pdf';
            $path = public_path('storage/' . $filename);

            $pdf->save($path);

            // 2. Tenta fazer o merge com anexos PDF (usando seu método do Model)
            $merge = $order->mergePdfAttachment($path);

            if (!$merge['success']) {
                if (file_exists($path)) unlink($path);
                logger_main('error', $merge['message']);
                return $this->can->array(false, $merge['message'], 403);
            }

            $finalPath = $merge['finalPath'];
            $finalFilename = $merge['finalFilename'];

            if (file_exists($finalPath)) {
                // Retorna o download e deleta após o envio
                return response()->download($finalPath, $finalFilename, [
                    'Content-Type' => 'application/pdf',
                ])->deleteFileAfterSend(false);
            }

            return $this->can->array(false, 'Arquivo não encontrado no servidor.', 404);
        } catch (\Exception $e) {
            logger_main('error', 'Falha crítica no PDF: ' . $e->getMessage());
            return $this->can->array(false, 'Erro interno ao gerar PDF.', 500);
        }
    }

    // Only administrators can delete orders
    public function destroy(string $id)
    {
        // Check if user is a supervisor that can attach a technician
        if (!$this->auth->hasPermission('sats', 2)) {
            logger_main('error', 'Usuário sem permissão para deletar Solicitação de Assistência Técnica.');
            return $this->can->array(false, 'Usuário sem permissão para deletar Solicitação de Assistência Técnica.', 403);
        }

        $order = Order::findOrFail($id);

        if (!$order) {
            return $this->can->array(false, 'Solicitação de Assistência Técnica não encontrada.', 404);
        }

        // Instanciamos o FileService para passar para o método de deleção
        $fileService = app(FileService::class);

        // Chamamos o método passando o serviço necessário
        $deleted = $order->completlyDelete($fileService);

        if (!$deleted['success']) {
            logger_main('error', $deleted['message']);
            return $this->can->array(false, $deleted['message'], 500);
        }
        return $this->can->array(true, $deleted['message'], 200);
    }
}