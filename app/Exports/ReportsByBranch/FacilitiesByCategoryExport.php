<?php

namespace App\Exports\ReportsByBranch;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class FacilitiesByCategoryExport implements FromView, WithEvents
{
  private $results;
  private $groupBy;

  public function __construct($results, $groupBy)
  {
    $this->results = $results;
    $this->groupBy = $groupBy;
  }

  /**
   * @return View
   */
  public function view(): View
  {
    return view('content.export.reportsByBranch.facilitiesByCategory', ['results' => $this->results, 'groupBy' => $this->groupBy]);
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function(AfterSheet $event) {
        $event->sheet->getDelegate()->setRightToLeft(true);
      },
    ];
  }
}
