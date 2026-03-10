<?php

namespace App\Models;

use App\Class\Hours;
use App\Notifications\NewSampleNotification;
use App\Services\FileService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'order_type_id',
        'client_id',
        'sector',
        'tec_id',
        'user_id',
        'finished',
        'equipment',
        'req_name',
        'req_date',
        'req_time',
        'req_descr',
        'cl_name',
        'cl_function',
        'cl_contact',
        'cl_date',
        'cl_sign',
        'cl_sign_path',
        'is_emergency',
    ];

    protected $table = "orders";
    protected $primaryKey = "id";
    protected $hours;

    public function __construct()
    {
        $this->hours = new Hours();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function tec(): BelongsTo
    {
        return $this->belongsTo(Tec::class)->withTrashed();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(OrderType::class, 'order_type_id')->withTrashed();
    }

    // Null values will be replaced by - - : - - and the time will be formatted without seconds
    public function notes_time_format()
    {
        foreach ($this->notes as $note) {
            $note->go_start ? $note->go_start = date('H:i', strtotime($note->go_start)) : $note->go_start = ' - - : - -';
            $note->go_end ? $note->go_end = date('H:i', strtotime($note->go_end)) : $note->go_end = ' - - : - -';
            $note->start ? $note->start = date('H:i', strtotime($note->start)) : $note->start = ' - - : - -';
            $note->end ? $note->end = date('H:i', strtotime($note->end)) : $note->end = ' - - : - -';
            $note->back_start ? $note->back_start = date('H:i', strtotime($note->back_start)) : $note->back_start = ' - - : - -';
            $note->back_end ? $note->back_end = date('H:i', strtotime($note->back_end)) : $note->back_end = ' - - : - -';
        }
    }

    // Notification management when a Technical Assistance Request is opened by the client.
    public function notificationWhenOpenedByClient()
    {
        // Get all supervisors
        $supervisors = User::whereHas('sup')->get();

        // Notifica os supervisores sobre a SAT diferenciando se é emergencial ou não
        foreach ($supervisors as $sup) {
            $this->openedSatNotification($sup);
        }

        // Business hours or client not need to service in emergency, stop here
        if (!$this->hours->isEmergency() || !$this->is_emergency) {
            return;
        }

        Log::info("Send Emergency Alert: SAT #{$this->id} -  " . $this->client->name . " - Entrou na condição de emergência!");

        // Buscamos todos os técnicos que estão de plantão, vinculados a este cliente e que ainda não tem uma SAT de emergência atribuida
        $client = Client::with(['emergencyTecs' => function ($query) {
            $query->with('user')
                ->where('on_call', 1)
                ->where(function ($q) {
                    $q->whereNull('emergency_order_id')
                        ->orWhere('emergency_order_id', 0)
                        ->orWhere('emergency_order_id', '');
                });
        }])->find($this->client_id);

        foreach ($client->emergencyTecs as $tec) {
            // Atualizamos cada técnico para o estado de emergência
            // Tarefa corn do servidor chama o comando (EmergencyDaemon.php) para enviar a notificação a partir das colunas do tecnico atualizadas
            $tec->update([
                'emergency_order_id' => $this->id,
                'emergency_notification_pending' => true, // Loop notification activated
            ]);
        }
    }

    public function emergencySatNotification(User $notifiable): void
    {
        $notifiable->title = "SAT EMERGENCIAL Nº{$this->id} -  " . $this->client->name . " - ABERTA!";
        $notifiable->order_id = $this->id;
        $notifiable->type = 'emergency_info';
        $notifiable->message = $this->req_descr ?? 'Serviço de Emergência.';
        $notifiable->notify(new NewSampleNotification());
    }

    public function normalSatNotification(User $notifiable): void
    {
        $notifiable->title = "SAT {$this->id} - " . $this->client->name . " - aberta!";
        $notifiable->order_id = $this->id;
        $notifiable->type = 'sat_info';
        $notifiable->message = $this->req_descr ?? 'Atividade de manutenção!';
        $notifiable->notify(new NewSampleNotification());
    }

    // Notification that SAT was opened by client
    public function openedSatNotification(User $notifiable)
    {
        if (!$this->is_emergency || !$this->hours->isEmergency()) {
            return $this->normalSatNotification($notifiable);
        }

        // Notification will be emergency only if is out of business hours and is emergency field was checked by client
        return $this->emergencySatNotification($notifiable);
    }

    public function satNotification(User $notifiable)
    {
        $notifiable->title = "SAT {$this->id} - " . $this->client->name . " - aberta!";
        $notifiable->order_id = $this->id;
        $notifiable->type = 'info';
        $notifiable->message = $this->req_descr ?? 'Atividade de manutenção!';
        $notifiable->notify(new NewSampleNotification());
    }

    public function tecSatNotification(User $notifiable)
    {
        $notifier = Auth::user();

        $notifiable->title = 'SAT - ' . $this->id . ' - ' . $this->client->name . ' - atribuída pelo Supervisor ' . $notifier->getFullName() .  '!';
        $notifiable->order_id = $this->id;
        $notifiable->message = $this->req_descr ?? 'Atividade de manutenção!';
        $notifiable->notify(new NewSampleNotification());

        return true;
    }

    public function notifySupsThatTecGetEmergencySat($tec_id)
    {
        $tec = Tec::findOrFail($tec_id);

        $supervisors = User::whereHas('sup')->get();

        foreach ($supervisors as $sup) {
            $sup->title = "SAT {$this->id} - " . $this->client->name . " - visualizada pelo Técnico {$tec->user->name}!";
            $sup->order_id = $this->id;
            $sup->type = 'info';
            $sup->message = $this->req_descr ?? 'Atividade de manutenção!';
            $sup->notify(new NewSampleNotification());
        }
    }

    public function emergencySatTecNotification($tec_id)
    {
        $tec = Tec::find($tec_id);

        if (!$tec) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$tec_id} não encontrado.");
            return;
        }

        // Verifica se a SAT foi criada ou atualizada
        if (!$this->updated_at || !$this->created_at) {
            $this->update([
                'updated_at' => now(),
                'created_at' => now()
            ]);
        }

        // LIMITE DE 60 MINUTOS: Verifica se a SAT foi criada/atualizada há mais de uma hora e para de enviar notificações
        if ($this->updated_at->diffInMinutes(now()) > 60) {
            Log::info("Send Emergency Alert Stoped: Ciclo de notificações encerrado por tempo limite (60min) para a SAT #{$this->id}, SAT foi criada / atualizada a {$this->updated_at->diffInMinutes(now())}min.");

            // Opcional: Aqui você pode desativar a flag no banco para o card parar de ser emergência
            // ou apenas parar as notificações. Vamos apenas parar as notificações:
            $tec->update(['emergency_notification_pending' => false]);
            return;
        }

        if (!$tec->emergency_notification_pending) {
            Log::info("Send Emergency Alert Stoped: Técnico #{$tec_id} sem notificações emergenciais ativas no momento (provavelmente já abriu a SAT).");
            return;
        }

        if ($tec->emergency_this_id !== $this->id) {
            Log::info("Send Emergency Alert Stoped: SAT #{$this->id} não está ativa como emergencial para o técnico #{$tec_id}, SAT atual: #{$tec->emergency_this_id}.");
            return;
        }

        if (!$tec->on_call) {
            $tec->resetSatEmergencyCondition();
            Log::info("Send Emergency Alert Stoped: Técnico #{$tec_id} não está de plantão no momento.");
            return;
        }
        try {
            if ($notifiable = User::find($tec->user_id)) {
                // Preparamos os dados para a notificação
                $notifiable->title = '🚨 SAT EMERGENCIAL ' . $this->id . ' - ' . $this->client->name . ' - ABERTA!';
                $notifiable->order_id = $this->id;
                $notifiable->type = 'emergency';
                $notifiable->channel_id = 'emergency';
                $notifiable->message = $this->req_descr ?? 'Manutenção Urgente Pendente!';
                $notifiable->notify(new NewSampleNotification());
            }
        } catch (\Exception $e) {
            Log::error("Falha ao enviar push na emergência #{$this->id}: " . $e->getMessage());
            // Não damos 'return' aqui para que o finally agende a próxima tentativa
        }
    }

    public function finish(): bool
    {
        try {
            // 1. Limpa o estado de emergência dos técnicos
            Tec::where('emergency_order_id', $this->id)->update([
                'emergency_order_id' => null,
                'emergency_notification_pending' => false
            ]);

            // 2. Finaliza a SAT
            $this->finished = true;
            $order_finished = $this->save();

            // 3. CORREÇÃO AQUI: Busca o usuário através do Model Tec
            //$tec = Tec::with('user')->find($this->tec_id);
            //$order_tec_name = ($tec && $tec->user) ? $tec->user->name : 'Técnico Desconhecido';

            // 4. Notifica os supervisores
            // $supervisors = User::whereHas('sup')->get();

            // foreach ($supervisors as $sup) {
            //     $sup->title = "SAT {$this->id} - " . $this->client->name . " - finalizada por {$order_tec_name}!";
            //     $sup->order_id = $this->id;
            //     $sup->type = 'sat_finished';
            //     $sup->message = $this->req_descr ?? 'Atendimento concluído.';

            //     $sup->notify(new NewSampleNotification());
            // }

            return $order_finished;
        } catch (\Exception $e) {
            Log::error("Erro ao finalizar SAT #{$this->id}: " . $e->getMessage());
            return false;
        }
    }

    // Updates the tec_id of the order
    public function updateTecId($tec_id)
    {

        // Limpa o estado de emergência dos técnicos referente a esta SAT
        Tec::where('emergency_order_id', $this->id)->update([
            'emergency_notification_pending' => false,
            'emergency_order_id' => null,
        ]);

        try {
            $tec = Tec::find($tec_id);
            // Update the order
            $this->tec_id = $tec->id;
            $this->save();
        } catch (\Exception $e) {

            // SAT without technician
            $this->tec_id = 0;
            $this->save();
            return [
                'success' => true,
                'message' => "SAT {$this->id} ainda sem técnico atriubuído!"
            ];
        }

        if ($tec) {
            // Enviamos uma notificação para o técnico
            $notified = false;
            if ($notifiable = User::find($tec->user_id)) {
                $notified = $this->tecSatNotification($notifiable);
            }
        }

        if (!$notified) {
            return [
                'success' => true,
                'message' => "SAT {$this->id} atribuida ao técnico {$tec->user->name} com falha ao enviar notificação!"
            ];
        }

        return [
            'success' => true,
            'message' => "SAT {$this->id} atribuida ao técnico {$tec->user->name} e notificação enviada com sucesso!"
        ];
    }
    /**
     * Coleta todos os arquivos vinculados a todas as intervenções (notes) desta SAT.
     */
    public function files()
    {
        $order = Order::with('notes.files')->find($this->id);

        // Usamos o collapse() para transformar uma coleção de coleções em uma única lista plana
        return $order->notes->flatMap(function ($note) {
            return $note->files;
        });
    }

    /**
     * Filtra apenas o que é imagem de todas as notas da SAT.
     * Útil para a grade de fotos principal do relatório.
     */
    public function images()
    {
        return $this->files()->filter(function ($file) {
            return !Str::endsWith(strtolower($file->path), '.pdf');
        });
    }

    /**
     * Filtra apenas os PDFs de todas as notas da SAT.
     * Útil para a lógica de Merge no seu Controller.
     */
    public function pdfs()
    {
        return $this->files()->filter(function ($file) {
            return Str::endsWith(strtolower($file->path), '.pdf');
        });
    }

    public function mergePdfAttachment($path)
    {
        try {
            // 2. Inicia o Merger
            $oMerger = PDFMerger::init();
            $oMerger->addPDF($path, 'all');

            // 3. Busca todos os arquivos PDF anexados nas notas desta SAT
            $hasExtraPdfs = false;
            foreach ($this->notes as $note) {
                foreach ($note->files as $file) {
                    if (Str::endsWith(strtolower($file->path), '.pdf')) {
                        $filePath = public_path('storage/' . $file->path);
                        if (file_exists($filePath)) {
                            $oMerger->addPDF($filePath, 'all');
                            $hasExtraPdfs = true;
                        }
                    }
                }
            }

            // 4. Finalização
            $finalFilename = 'sat_' . $this->id . '_' . date('d_m_Y') . '.pdf';
            $finalPath = public_path('storage/' . $finalFilename);

            if ($hasExtraPdfs) {
                $oMerger->merge();
                $oMerger->save($finalPath);
            } else {
                // Se não tem PDFs extras, o arquivo final é o próprio mainPath
                rename($path, $finalPath);
            }
        } catch (\Exception $e) {
            Log::error('Error merging PDFs: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error merging PDFs: ' . $e->getMessage()
            ];
        }

        return [
            'success' => true,
            'finalFilename' => $finalFilename,
            'finalPath' => $finalPath
        ];
    }

    public function clearPreviousReportSessionVariables()
    {
        session()->forget('order_count_client_ids');
        session()->forget('order_client_ids');
        session()->forget('page');
        session()->forget('order_index');
        session()->forget('expected_pages');
    }

    public function deleteAllPdfsInStorageFolder()
    {
        // Delete all pdf files in storage folder----------------------------------------------------
        $files = glob(public_path('storage/*.pdf'));
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function completlyDelete(FileService $fileService): array
    {
        return DB::transaction(function () use ($fileService) {
            try {
                // 1. Limpa estado de emergência dos técnicos
                Tec::where('emergency_order_id', $this->id)->update([
                    'emergency_order_id' => null,
                    'emergency_notification_pending' => false
                ]);

                // 2. Apagar assinatura do CLIENTE (se existir na Order)
                if ($this->cl_sign_path && Storage::disk('public')->exists($this->cl_sign_path)) {
                    Storage::disk('public')->delete($this->cl_sign_path);
                }

                // 3. Itera sobre as notas
                foreach ($this->notes as $note) {
                    // Apaga fotos/PDFs anexados às notas (via FileService)
                    $fileService->deleteAllFiles($note);

                    // 4. Apagar assinaturas dos TÉCNICOS (estão na tabela pivô note_tec)
                    foreach ($note->tecs as $tec) {
                        if ($tec->pivot->signature_path && Storage::disk('public')->exists($tec->pivot->signature_path)) {
                            Storage::disk('public')->delete($tec->pivot->signature_path);
                        }
                    }

                    // Remove relações e notas
                    $note->tecs()->detach();
                    $note->materials()->detach();
                    $note->forceDelete();
                }

                // 5. Deleta a ordem principal
                $this->forceDelete();

                return [
                    'success' => true,
                    'message' => 'Solicitação de Assistência Técnica deletada com sucesso.'
                ];
            } catch (\Exception $e) {
                logger_main('error', 'Erro ao deletar SAT ' . $this->id . ': ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Erro ao deletar SAT: ' . $e->getMessage()
                ];
            }
        });
    }
}
