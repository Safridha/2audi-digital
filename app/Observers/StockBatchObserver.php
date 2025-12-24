<?php

namespace App\Observers;

use App\Models\StockBatch;

class StockBatchObserver
{
    public function created(StockBatch $batch): void
    {
        $batch->bahan?->recalcStokFromBatches();
    }

    public function updated(StockBatch $batch): void
    {
        $batch->bahan?->recalcStokFromBatches();
    }

    public function deleted(StockBatch $batch): void
    {
        $batch->bahan?->recalcStokFromBatches();
    }
}
