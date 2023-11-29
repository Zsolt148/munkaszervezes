<?php

namespace App\Services\Tasks;

use App\Models\Task;
use OpenSpout\Common\Entity\Style\Style;
use Spatie\SimpleExcel\SimpleExcelWriter;

class TaskExportService
{
    public function export(string $filename, array $ids = []): SimpleExcelWriter
    {
        $writer = SimpleExcelWriter::streamDownload($filename);

        $tasks = Task::query()
            ->whereIn('id', $ids)
            ->with('createdBy', 'role', 'responsible', 'parent', 'park')
            ->latest()
            ->get();

        $style = (new Style())->setShouldWrapText();

        $writer->setHeaderStyle($style)->addHeader([
            '#',
            'létrehozta',
            'park',
            'csoport',
            'felelős',
            'priorítás',
            'ticket típusa',
            'státusz',
            'név',
            'leírás',
            'határidő',
            'dátum',
            'becsült munkaidő',
            'utazási idő',
            'elvégezve',
            'létrehozva',
        ]);

        $i = 0;
        foreach ($tasks as $task) {
            $i++;

            /** @var Task $task */
            $writer->addRow([
                $task->id,
                $task->createdBy->name,
                $task->park->name,
                $task->role ? trans('role.'.$task->role->name) : '',
                $task->responsible ? $task->responsible->name : '',
                trans('tasks.'.$task->priority),
                trans('tasks.'.$task->task_type),
                trans('tasks.'.$task->status),
                $task->name,
                $task->description,
                $task->deadline,
                $task->date ?: '',
                $task->estimated_hour,
                $task->travel_time,
                $task->done_at ? $task->done_at->format('Y.m.d') : '',
                $task->created_at->format('Y.m.d'),
            ]);

            if ($i % 1000 === 0) {
                flush(); // Flush the buffer every 1000 rows
            }
        }

        return $writer;
    }
}
