<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Semester;
use App\Models\Application;
use App\Models\BankSlip;
use App\Mail\NotifyPaymentFailure;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegenerateAndNotifyPaymentFailure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cea:regenerate-and-notify {--force : Run without asking for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate unpaid bank slips for the current semester and notify applicants via email.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $semester = Semester::getInEnrollmentPeriod();

        if (!$semester) {
            $this->error("Nenhum semestre com inscrições abertas encontrado.");
            
            if ($this->confirm('Deseja usar o último semestre cadastrado?')) {
                $semester = Semester::getLatest();
            } else {
                return 0;
            }
        }

        $this->info("Semestre selecionado: {$semester->year}.{$semester->period}");

        // Filter applications that have a bank slip for "Taxa de Inscrição"
        // And the bank slip status is NOT 'P' (Pago)
        // Note: Using 'has' with callback to filter relations
        $applications = Application::where('semesterID', $semester->id)
            ->whereHas('applicationFee', function ($query) {
                $query->where('statusBoletoBancario', '!=', 'P')
                      // Optionally exclude 'C' (Cancelado) if we don't want to spam cancelled ones,
                      // but user said "boletos vencidos", which might still be 'E' or 'V'.
                      // Let's assume 'status != P' covers everything that needs paying.
                      ->where('relativoA', 'Taxa de Inscrição');
            })
            ->with('applicationFee')
            ->get();

        $count = $applications->count();
        $this->info("Encontradas {$count} inscrições com boletos não pagos.");

        if ($count == 0) {
            return 0;
        }

        if (!$this->option('force') && !$this->confirm("Deseja regenerar boletos e enviar emails para estas {$count} pessoas?")) {
            return 0;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($applications as $app) {
            try {
                $this->info("Processing App ID: {$app->id}");
                $oldBoleto = $app->applicationFee;

                // 1. Cancel Old Boleto if exists and not already cancelled
                // FIXME: NuSOAP crashes when cancellation fails (SOAP Fault). skipping cancellation to ensure new boleto generation.
                /*
                if ($oldBoleto && $oldBoleto->statusBoletoBancario != 'C') {
                    $oldBoleto->cancelarBoleto();
                }
                */

                // 2. Generate New Boleto
                $valor = ($app->serviceType == 'Consulta') ? '140.00' : '80.00';
                
                // Using 'Taxa de Inscrição' as standard now
                $newBoleto = BankSlip::gerarBoletoRegistrado($app, $valor, 0, "Taxa de Inscrição");

                if (!$newBoleto) {
                    Log::error("Falha ao regenerar boleto para App ID: {$app->id}");
                    $this->error("\nFalha ao regenerar boleto para {$app->bdName} (App ID: {$app->id})");
                    continue;
                }

                $app->applicationFee()->save($newBoleto);

                // 3. Fetch PDF
                $pdfContent = $newBoleto->obterBoletoPDF();

                if (!$pdfContent) {
                    Log::error("Falha ao obter PDF do novo boleto ID: {$newBoleto->id}");
                    $this->error("\nFalha ao obter PDF para {$app->bdName}");
                    continue;
                }
                
                // 4. Send Email
                $mail = Mail::to($app->email);
                
                // Add BCCs
                $bcc = [];
                if (env('MAIL_CEA')) $bcc[] = env('MAIL_CEA');
                if (env('MAIL_DEV_TEST')) $bcc[] = env('MAIL_DEV_TEST');
                
                if (!empty($bcc)) {
                    $mail->bcc($bcc);
                }

                $mail->queue(new NotifyPaymentFailure($app, $pdfContent));

            } catch (\Exception $e) {
                Log::error("Erro no comando cea:regenerate-and-notify para App ID {$app->id}: " . $e->getMessage());
                $this->error("\nErro processando {$app->bdName}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nProcesso concluído.");
        return 0;
    }
}
