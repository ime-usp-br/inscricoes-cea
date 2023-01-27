<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use App\Models\Application;


class FillPDFController extends Controller
{
    public function process(Request $request, $protocol)
    {
        $filePath = public_path("timbrado-cea.pdf");

        $outputFilePath = public_path("inscricao-$protocol.pdf");

        $application = Application::where("protocol", $protocol)->first();

        $this->fillPDF($filePath, $outputFilePath, $application);
        return response()->file($outputFilePath);
    }

    public function fillPDF($file, $outputFile, $application)
    {
        
        $fpdi = new FPDI;
        // merger operations
        $count = $fpdi->setSourceFile($file);
        for ($i=1; $i<=$count; $i++) {
            $template   = $fpdi->importPage($i);
            $size       = $fpdi->getTemplateSize($template);
            $fpdi->AddPage($size['orientation'], array($size['width'], $size['height']));
            $fpdi->useTemplate($template);
            $left = 15;
            $top = 345;
            $text = "Protocolo: $application->protocol zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz zzzzzzzzzzzzzz zz zzzzzzzzzzzzz zzzzzzzzzzzzzzzzzzzzzzzzzzz zzzzzzzzzzzzzzzzzzzzzz zzzzzzzzzzzzzzzzzz zzzzzzzzz";
            $fpdi->SetMargins($left, $top); 
            $fpdi->SetFont("helvetica", "", 12);
            //$fpdi->ln();
          //  $fpdi->Text($left,$top,$text);
          //  $fpdi->MultiCell(100, 10, $text);
            $fpdi->Write(5, $text);
          
          
        }
        return $fpdi->Output($outputFile, 'F');
    }
}
