<?php

namespace App\Imports;

use App\Models\Visit;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VisitsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Visit([
            'user_id' => Auth::id(),
            'name' => $row['nombre'] ?? $row['name'] ?? null,
            'email' => $row['email'] ?? null,
            'latitude' => $row['latitud'] ?? $row['latitude'] ?? null,
            'longitude' => $row['longitud'] ?? $row['longitude'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.name' => 'required|string',
            '*.email' => 'required|email',
            '*.latitude' => 'required|numeric',
            '*.longitude' => 'required|numeric',
        ];
    }
}