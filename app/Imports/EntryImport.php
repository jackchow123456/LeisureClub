<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EntryImport implements ToCollection, ToModel, WithChunkReading
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        echo  1;
    }

    public function model(array $row)
    {
        return;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
