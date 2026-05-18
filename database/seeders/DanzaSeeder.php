<?php
namespace Database\Seeders;

use App\Models\Danza;
use Illuminate\Database\Seeder;

class DanzaSeeder extends Seeder
{
    public function run(): void
    {
        $danzas = [
            [
                'nom_danza' => 'Morenada',
                'clasificacion' => 'Pesada',
                'descripcion' => 'Danza emblemática de los Andes, caracterizada por su elegancia y el uso de matracas.'
            ],
            [
                'nom_danza' => 'Caporales',
                'clasificacion' => 'Liviana',
                'descripcion' => 'Danza ágil inspirada en el capataz de los yungas, famosa por sus saltos y cascabeles.'
            ],
            [
                'nom_danza' => 'Diablada',
                'clasificacion' => 'Sátira',
                'descripcion' => 'Representación de la lucha entre el bien y el mal con máscaras espectaculares.'
            ],
            [
                'nom_danza' => 'Tinkus',
                'clasificacion' => 'Autóctona',
                'descripcion' => 'Encuentro ritual que simboliza la fuerza y la fertilidad de la tierra.'
            ],
            [
                'nom_danza' => 'Tobas',
                'clasificacion' => 'Liviana',
                'descripcion' => 'Danza guerrera que representa a las etnias del Chaco y la Amazonía.'
            ],
            [
                'nom_danza' => 'Kullawada',
                'clasificacion' => 'Salón',
                'descripcion' => 'Danza de los hilanderos con trajes elegantes cargados de pedrería.'
            ]
        ];

        foreach ($danzas as $danza) {
            Danza::create($danza);
        }
    }
}