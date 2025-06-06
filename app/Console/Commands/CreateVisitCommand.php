<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visit;
use function Laravel\Prompts\text;
use function Laravel\Prompts\info;
use function Laravel\Prompts\error;

class CreateVisitCommand extends Command
{
    protected $signature = 'visit:create';
    protected $description = 'Create a new visit record';

    public function handle()
    {
        $name = text(
            label: 'What is the customer name?',
            required: true,
            validate: fn (string $value) => strlen($value) < 3 ? 'Name must be at least 3 characters.' : null
        );

        $email = text(
            label: 'What is the customer email?',
            required: true,
            validate: fn (string $value) => !filter_var($value, FILTER_VALIDATE_EMAIL) ? 'Invalid email format.' : null
        );

        $latitude = text(
            label: 'What is the latitude?',
            required: true,
            validate: fn (string $value) => !is_numeric($value) || $value < -90 || $value > 90 
                ? 'Latitude must be a number between -90 and 90.' 
                : null
        );

        $longitude = text(
            label: 'What is the longitude?',
            required: true,
            validate: fn (string $value) => !is_numeric($value) || $value < -180 || $value > 180
                ? 'Longitude must be a number between -180 and 180.'
                : null
        );

        try {
            $visit = Visit::create([
                'name' => $name,
                'email' => $email,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);

            info('Visit created successfully!');
            info("ID: {$visit->id}");
            info("Name: {$visit->name}");
            info("Email: {$visit->email}");
            info("Location: {$visit->latitude}, {$visit->longitude}");
        } catch (\Exception $e) {
            error('Error creating visit: ' . $e->getMessage());
        }
    }
}