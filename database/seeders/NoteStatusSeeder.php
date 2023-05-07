<?php

namespace Database\Seeders;

use App\Models\NoteStatus;
use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Database\Seeder;

class NoteStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NoteStatus::truncate();
        NoteStatus::create([
            "name"        => "Búsqueda",
            "description" => "Búsqueda",
            "note_type"   => Quotation::class,
            "color"       => "#ffd966",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Candidato seleccionado" ,
            "description" => "Candidato seleccionado",
            "note_type"   => Quotation::class,
            "color"       => "#00ff00",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Cancelado" ,
            "description" => "Cancelado",
            "note_type"   => Quotation::class,
            "color"       => "#ff0000",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Stand by" ,
            "description" => "Stand by",
            "note_type"   => Quotation::class,
            "color"       => "#666666",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Contratado" ,
            "description" => "Contratado",
            "note_type"   => Quotation::class,
            "color"       => "#6aa84f",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        // Orders
        NoteStatus::create([
            "name"        => "Búsqueda",
            "description" => "Búsqueda",
            "note_type"   => Order::class,
            "color"       => "#8950FC",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Candidato seleccionado" ,
            "description" => "Candidato seleccionado",
            "note_type"   => Order::class,
            "color"       => "#3699ff",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Cancelado" ,
            "description" => "Cancelado",
            "note_type"   => Order::class,
            "color"       => "#F64E60",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Stand by" ,
            "description" => "Stand by",
            "note_type"   => Order::class,
            "color"       => "#FFA800",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Contratado" ,
            "description" => "Contratado",
            "note_type"   => Order::class,
            "color"       => "#1BC5BD",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Entrevista Cliente" ,
            "description" => "Entrevista Cliente",
            "note_type"   => Order::class,
            "color"       => "#00ffff",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Entrevista Cliente" ,
            "description" => "Entrevista Cliente",
            "note_type"   => Order::class,
            "color"       => "#00ffff",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        NoteStatus::create([
            "name"        => "Revisión CV" ,
            "description" => "Revisión CV",
            "note_type"   => Order::class,
            "color"       => "#b6d7a8",
            "status"      => 1,
            "created_by"  => 1,
            "order"       => 1
        ]);
        
        
    }
}
