<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::truncate();
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "ANTID",
                "name"                => "Antidopping",
                "description"         => "Se verifica el consumo de droga en los candidatos",
                "price_list"          => 400,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "DEM",
                "name"                => "Demandas Laborales",
                "description"         => "",
                "price_list"          => 250,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "ESE",
                "name"                => "Estudio socioeconómico",
                "description"         => "Visita presencial al candidato en donde se valida la manera en la que vive. Se revisan referencias laborales, vecinales y personales. Registros en IMSS y de demandas laborales. Tiempo de respuesta de 5 días hábiles (puede variar por las referencias)",
                "price_list"          => 600,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "GAR",
                "name"                => "Garantía reclutamiento",
                "description"         => "ninguna, prueba",
                "price_list"          => 0,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "IVO",
                "name"                => "Inventario de Valores Organizacionales",
                "description"         => "",
                "price_list"          => 550,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "MED",
                "name"                => "Examen Médico",
                "description"         => "ver la salud general actual del candidato ,visión, chequeo general, antecedentes patologicos y no patologicos, antecedentes hereditarios",
                "price_list"          => 550,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "PSIC",
                "name"                => "Evaluación Psicométrica",
                "description"         => "Aplicación vía remota. Se necesita: Nombre completo y fecha de nacimiento",
                "price_list"          => 900,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "RECL",
                "name"                => "Reclutamiento y Selección de Personal",
                "description"         => "Entrevistas vía virtual. Evaluaciones psicométricas. Solicitud de referencias laborales. Documentación para expediente. Garantía de permanencia de 30/60 días.",
                "price_list"          => 10000,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "REF",
                "name"                => "Referencias Laborales con semanas cotizadas",
                "description"         => "Corroborar que la información presentada por el candidato, sea real y positiva de sus trabajos anteriores. Confirmación por el imms de información presentada, Documento emitidos por IMMS que verifica sus altas y bajas laborales",
                "price_list"          => 300,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "VERIT",
                "name"                => "Integridad y Estabilidad",
                "description"         => "Te da resultados en ambitos personales importantes para medir la integridad y estabilidad de una persona, dando como resultado tiempo de respuesta de esta prueba, comentarios especificos de cada tema, y un % de aceptación o alerta ( foco rojo), Habla de Estabilidad laboral, robo, alcohol, apuestas, droga, soborno, lealtad, buena impresión, personalidad y confiabilidad.",
                "price_list"          => 900,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
        Product::create([
                "unit_id"             => 1,
                "product_category_id" => 1,
                "product_type_code"   => "PHYSC",
                "code"                => "SOCI",
                "name"                => "Socioecnómico Virtual",
                "description"         => "Es una investigación del candidato, donde podemos evaluar los antecedentes del candidato o trabajador, como: -Historial laboral -Demandas -Análisis de deuda -Historial laboral y legal -Recomendación general",
                "price_list"          => 500,
                "flec_price"          => 1,
                "status"              => 1,
                "created_by"          => 0
            ]);
    }
}
