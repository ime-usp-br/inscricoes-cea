<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        if (!Auth::user()->hasAnyRole(['Administrador', 'Secretaria'])) {
            abort(403);
        }

        $semester = Semester::getLatest();

        $applications = Application::whereBelongsTo($semester)
            ->where('deleted', false)
            ->with(['allApplicationFees', 'allProjectFees', 'complementaryFee'])
            ->get();

        $format = $request->query('format');
        if ($format) {
            return $this->export($applications, $format, $semester);
        }

        return view('financial_reports.index', compact('applications', 'semester'));
    }

    public function sync(Application $application)
    {
        if (!Auth::check() || !Auth::user()->hasAnyRole(['Administrador', 'Secretaria'])) {
            abort(403);
        }

        $updated = false;

        foreach ($application->allApplicationFees as $fee) {
            if (in_array($fee->statusBoletoBancario, ['E', 'V'])) {
                try {
                    $fee->atualizarSituacao();
                    $updated = true;
                } catch (\Exception $e) {
                    \Log::error("Erro SOAP ao atualizar boleto {$fee->id}: " . $e->getMessage());
                }
            }
        }

        foreach ($application->allProjectFees as $fee) {
            if (in_array($fee->statusBoletoBancario, ['E', 'V'])) {
                try {
                    $fee->atualizarSituacao();
                    $updated = true;
                } catch (\Exception $e) {
                    \Log::error("Erro SOAP ao atualizar boleto {$fee->id}: " . $e->getMessage());
                }
            }
        }

        if ($application->complementaryFee) {
            if (in_array($application->complementaryFee->statusBoletoBancario, ['E', 'V'])) {
                try {
                    $application->complementaryFee->atualizarSituacao();
                    $updated = true;
                } catch (\Exception $e) {
                    \Log::error("Erro SOAP ao atualizar boleto {$application->complementaryFee->id}: " . $e->getMessage());
                }
            }
        }

        if ($updated) {
            $application->refresh();
            $application->load(['allApplicationFees', 'allProjectFees', 'complementaryFee']);
        }

        return response()->json([
            'inscription' => $application->getAggregatedInscriptionFeeStatus(),
            'project' => $application->getAggregatedProjectFeeStatus(),
            'complementary' => $application->complementaryFee ? $application->complementaryFee->getStatus() : '—',
        ]);
    }

    private function export($applications, $format, $semester)
    {
        $data = $this->buildExportData($applications);
        $filename = 'relatorio_financeiro_' . $semester->year . '_' . str_replace(' ', '_', $semester->period);

        switch (strtolower($format)) {
            case 'csv':
                return $this->exportCsv($data, $filename);
            case 'excel':
                return $this->exportExcel($data, $filename);
            default:
                abort(400, 'Formato de exportação inválido.');
        }
    }

    private function buildExportData($applications)
    {
        $rows = [];
        foreach ($applications as $app) {
            $rows[] = [
                'Protocolo' => $app->protocol,
                'Modalidade' => $app->serviceType,
                'Pesquisador' => $app->projectResponsible,
                'CPF' => $app->CPFCNPJ,
                'E-mail' => $app->email,
                'Nome (Boleto)' => $app->bdName,
                'CPF/CNPJ (Boleto)' => $app->bdCpfCnpj,
                'Banco' => $app->bdBankName,
                'Agência' => $app->bdAgency,
                'Conta' => $app->bdAccount,
                'Tipo' => $app->bdType,
                'Recibo Reembolso' => $app->refundReceipt ?? '—',
                'Dados Reembolso' => $app->refundReceiptData ?? '—',
                'Taxa de Inscrição' => $app->getAggregatedInscriptionFeeStatus(),
                'Taxa de Projeto' => $app->getAggregatedProjectFeeStatus(),
                'Complemento' => $app->complementaryFee ? $app->complementaryFee->getStatus() : '—',
            ];
        }
        return $rows;
    }

    private function exportCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$filename}.csv",
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            if (!empty($data)) {
                fputcsv($file, array_keys($data[0]), ';');
                foreach ($data as $row) {
                    fputcsv($file, $row, ';');
                }
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportExcel($data, $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (!empty($data)) {
            $sheet->fromArray(array_keys($data[0]), null, 'A1');
            $sheet->fromArray($data, null, 'A2');
        }

        $writer = new Xlsx($spreadsheet);
        $tmpPath = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tmpPath);

        return response()->download($tmpPath, $filename . '.xlsx')->deleteFileAfterSend(true);
    }
}
