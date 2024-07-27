<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FacilitiesByBranchExport implements FromView, WithEvents
{
  private $facilities;

  public function __construct($facilities)
  {
    $this->facilities = $facilities;
  }

  /**
   * @return View
   */
  public function view(): View
  {
    return view('content.export.facilitiesByBranch', ['facilities' => $this->facilities]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function(AfterSheet $event) {
        $event->sheet->getDelegate()->setRightToLeft(true);
      },
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      // Style the first row as bold text.
      1    => ['font' => ['bold' => true]],
    ];
  }
}

