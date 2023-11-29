<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Services\Tasks\TaskExportService;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Style\Style;
use Spatie\SimpleExcel\SimpleExcelWriter;

class TaskExportController extends Controller
{
    protected array $ids;

    public function __invoke(Request $request)
    {
        $this->ids = $request->get('ids') ?? [];

        switch ($request->get('tab')) {
            case 0:
                return $this->all();
            case 1:
                return $this->done();
            case 2:
                return $this->leaves();
        }
    }

    protected function all()
    {
        return app(TaskExportService::class)->export('feladatok.xlsx', $this->ids)->toBrowser();
    }

    protected function done()
    {
        return app(TaskExportService::class)->export('elvegzett-feladatok.xlsx', $this->ids)->toBrowser();
    }

    protected function leaves()
    {
        $leaves = Leave::query()
            ->with('admin')
            ->whereIn('id', $this->ids)
            ->latest()
            ->get();

        $style = (new Style())->setShouldWrapText();
        $writer = SimpleExcelWriter::streamDownload('szabadsag.xlsx');

        $writer->setHeaderStyle($style)->addHeader([
            '#',
            'név',
            'típus',
            'dátum',
            'nap',
            'státusz',
        ]);

        $i = 0;
        foreach ($leaves as $leave) {
            $i++;

            $writer->addRow([
                $leave->id,
                $leave->admin->name,
                trans(Leave::TYPES[$leave->type]),
                $leave->date->translatedFormat('Y. M d.'),
                $leave->day,
                $leave->status_text,
            ]);

            if ($i % 1000 === 0) {
                flush(); // Flush the buffer every 1000 rows
            }
        }

        return $writer->toBrowser();
    }
}
