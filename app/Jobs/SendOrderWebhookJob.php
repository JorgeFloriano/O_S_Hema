<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendOrderWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    // Tenta 5 vezes antes de mandar para a tabela de falhas
    public $tries = 15; 
    public $backoff = 60; // Espera 60 segundos entre cada tentativa

    public function __construct(Order $order)
    {
        // Usamos o model para pegar os dados atualizados
        $this->order = $order->load('client:id,name');
        $this->queue = 'webhooks';
    }

    public function retryUntil()
    {
        return now()->addHours(1); // Tenta re-executar por até 1 hora se o worker cair
    }

    public function handle()
    {

        Log::info("--- Executando Job de Webhook, informações da SAT #{$this->order->id} enviadas para #{config('services.url.webhook')} ---");

        $url = config('services.url.webhook');

        // Montamos o texto exatamente como o João Paulo pediu
        $payload = [
            "data" => [
                [
                    "SAT" => (string) $this->order->id,
                    "Cliente" => $this->order->client->name ?? 'Não encontrado',
                    "Servico" => $this->order->type->description ?? 'Não relatado',
                    "Setor" => $this->order->sector ?? 'Não relatado',
                    "Data do acionamento" => $this->order->req_date ? date('d/m/y', strtotime($this->order->req_date)) : '',
                    "Hora do acionamento" => $this->order->req_time ? date('H:i', strtotime($this->order->req_time)) : '',
                    "Problema relatado" => $this->order->req_descr ?? 'Não especificado'
                ]
            ]
        ];

        $response = Http::post($url, $payload);

        if ($response->failed()) {
            Log::error("Falha ao enviar Webhook SAT #{$this->order->id}: " . $response->body());
            throw new \Exception("Erro no Webhook n8n");
        }
    }
}
