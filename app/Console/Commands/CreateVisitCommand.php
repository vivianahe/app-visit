<?php

namespace App\Console\Commands;

use App\Models\Visit;
use Illuminate\Console\Command;

class CreateVisitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visit:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear una nueva visita solicitando nombre, correo, latitud y longitud';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('¿Cuál es el nombre de la visita?');
        $email = $this->ask('¿Cuál es el correo electrónico de la visita?');
        $latitude = $this->ask('¿Cuál es la latitud de la visita?');
        $longitude = $this->ask('¿Cuál es la longitud de la visita?');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('El correo electrónico no es válido.');
            return;
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            $this->error('La latitud y longitud deben ser números.');
            return;
        }

        $visit = Visit::create([
            'name' => $name,
            'email' => $email,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $this->info("Visita creada exitosamente: {$visit->name}");
        }
}
