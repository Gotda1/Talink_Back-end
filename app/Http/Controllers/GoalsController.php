<?php

namespace App\Http\Controllers;

use App\Http\Requests\GoalRequest;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoalsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $years = Goal::selectRaw("DISTINCT(year_numb) AS year_numb")
                            ->selectRaw("IF (year_numb = YEAR(NOW()), 1,0) AS is_current")
                            ->orderBy("year_numb")
                            ->with(["months_goals" => function ($query){
                                        $query->select("year_numb","month_numb","amount");
                                        $query->orderBy('month_numb','ASC');
                                    }])->get();
            return $this->successResponse([
                "years" => $years
            ]);
        } catch (\Throwable $th) {
            return $this->failedResponse($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {            
            for ($i=1; $i <=12 ; $i++) { 
                Goal::create([
                    "year_numb"  => $request->year_numb,
                    "month_numb" => $i
                ]);
            }
            
            $year = Goal::selectRaw("year_numb")
                    ->with(["months_goals" => function ($query){
                                $query->select("year_numb","month_numb","amount");
                                $query->orderBy('month_numb','ASC');
                            }])->where("year_numb", $request->year_numb)
                            ->first();

            return $this->successResponse([
                "year" => $year
            ]);
        } catch (\Throwable $th) {
            return $this->failedResponse($th);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            foreach ($request->months as $goal) {
                Log::info($goal);
                $goalSave = Goal::where("year_numb", $id)
                            ->where("month_numb", $goal["month_numb"])
                            ->first();
                Log::info($goalSave);
                $goalSave->amount = $goal["amount"];
                $goalSave->save();
            }

            return $this->successResponse([
                "year" => $id
            ]);
        } catch (\Throwable $th) {
            return $this->failedResponse($th);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
