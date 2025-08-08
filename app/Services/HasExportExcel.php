<?php

namespace App\Services;

use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

trait HasExportExcel
{
    /**
     * @param array $customAttributes
     * @param callable|null $builder
     * @param callable|null $callback
     * @return void
     */
    public function exportToExcel($filename = null, array $customAttributes = [], callable $builder = null, callable $callback = null): void
    {
        $className = $this->customExportFilename() ?? Str::plural(Str::afterLast(static::class, '\\'));
        
        if(empty($filename)) {
            $filename = $className .'.xlsx';
        } else {
            $filename = $filename .'.xlsx';
        }

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'f5412d'], 
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Teks di tengah secara horizontal
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, // Teks di tengah secara vertikal
            ],
        ];
        
        $collections = $builder
            ? $builder($this->query())->get()
            : $this->query()->get();

        $attributes = empty($customAttributes)
            ? $this->attributes
            : $customAttributes;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);

        $alpha = 'A';
        foreach ($attributes as $key => $value) {
            $sheet->setCellValue($alpha .'1', $value);
             // Menghitung panjang teks dan menentukan lebar kolom
            $textLength = strlen($value);
            $columnWidth = max(16, $textLength + 2); // Minimal lebar kolom adalah 10, tambahkan 2 untuk margin
            // end
            $sheet->getColumnDimension($alpha)->setWidth($columnWidth);
            $sheet->getColumnDimension($alpha)->setAutoSize(true);
            $sheet->getStyle($alpha . '1')->applyFromArray($headerStyle);
            $alpha++;
        }

        $alpha = 'A';
        $rowNo = 2;
        // dd($collections);
        foreach ($collections as $index => $collection) {
            foreach ($attributes as $key => $value) {
                if ($callback) {
                    $val = $callback($collection, $key, $value, $index);
                } else {
                    if ($key === 'created_at' || $key === 'updated_at') {
                        $val = formatSetTimezone($collection->$key);
                    } else {
                        $val = $collection->$key;
                    }
                }

                $cellCoordinate = $alpha++ . $rowNo;
                $sheet->setCellValue($cellCoordinate, $val);

                if ($val == 'REJECT') {
                    $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('d1313a');
                    $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else if($val == 'PENDING') {
                    $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('e39e15');
                    $sheet->getStyle($cellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->getStyle($cellCoordinate)->getFont()->getColor()->setRGB('0c0c1b');
                }

                $statusColumn = 'H';
                $statusCellCoordinate = $statusColumn . $rowNo;

                if($collection['rfid_code'] != null && $collection['is_printed'] == 1) {
                    $sheet->getStyle($statusCellCoordinate)->getFont()->getColor()->setRGB('54d79d');
                    $sheet->getStyle($statusCellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else if ($collection['rfid_code'] != null && $collection['is_printed'] != 1) {
                    $sheet->getStyle($statusCellCoordinate)->getFont()->getColor()->setRGB('3778e9');
                    $sheet->getStyle($statusCellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else if ($collection['rfid_code'] == null && $collection['is_printed'] == 1) {
                    $sheet->getStyle($statusCellCoordinate)->getFont()->getColor()->setRGB('FFFF00');
                    $sheet->getStyle($statusCellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                } else {
                    $sheet->getStyle($statusCellCoordinate)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $sheet->getColumnDimension($alpha)->setAutoSize(true);
            }
            $alpha = 'A';
            $rowNo++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
        $writer->save('php://output');
    }

    /**
     * @param array $customAttributes
     * @param callable|null $builder
     * @param callable|null $callback
     */
    public function exportToExcelCustom($filename = null, array $customAttributes = [], callable $builder = null, callable $callback = null): void
    {
        $className = $this->customExportFilename() ?? Str::plural(Str::afterLast(static::class, '\\'));
        
        if(empty($filename)) {
            $filename = $className .'.xlsx';
        } else {
            $filename = $filename .'.xlsx';
        }

        $collections = $builder
            ? $builder($this->query())->get()
            : $this->query()->get();

        $attributes = empty($customAttributes)
            ? $this->attributes
            : $customAttributes;

        Cell\Cell::setValueBinder(new SetTimezoneBinder);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);

        $callback($sheet, $attributes, $collections);

        // styling header
        $headerStyleArray = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00d086'], 
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray($headerStyleArray);

        foreach (range('A', $highestColumn) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // end styling header

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
        $writer->save('php://output');
    }

    /**
     * @return mixed
     */
    public function customExportFilename()
    {
        return null;
    }
}