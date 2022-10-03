<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;



class Exportar
{
    static public function xlsx_attach($headerData, $arrayData)
    {
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Gastos Resumen");
        
        $sheet->fromArray($headerData, null, 'A1');

        $sheet->getStyle('A1:AP1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFFFFF00');
        $sheet->getStyle('A1:AP1')->getFont()->setBold(true);

        $sheet->fromArray($arrayData, null, 'A2');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        return ob_get_clean();
    }
}
