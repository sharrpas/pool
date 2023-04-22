<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportTableResource;
use App\Models\TableTask;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\Table\Table;

class ReportController extends Controller
{
    public function HoursPerTables(Request $request)
    {

        $gym = auth()->user()->gym()->first();
        $date = CarbonPeriod::dates(now()->subMonth(1), now())->toArray();
        $dates = [];
        $lableData = [];
        for ($i = 0; $i < count($date); $i++) {
            $dates[$i] = $date[$i]->format('Y-m-d');
            $lableData[$dates[$i]] = "0";
            $dates[$i] = Verta($dates[$i])->format('Y-m-d');
        }


        $tables = ReportTableResource::collection($gym->tables()->get());


        $mappedcollection = $tables->map(function ($table, $key) use ($lableData) {
            $tasks = $table->tasks()->select(
                DB::raw('DATE(opened_at) as date'),
//                DB::raw('count(*) as count'),
                DB::raw('table_id'),
                DB::raw("SUM(duration) AS sum_duration"),
            )
                ->where('opened_at', '>', Carbon::now()->subMonth(1))
                ->groupBy('table_id')
                ->groupBy('date')
                ->get();

            $tasks->map(function ($task, $key) use (&$lableData) {
                $lableData[$task->date] =  (string)round(($task->sum_duration)/60,1);
            });
            return [
                'label' => $table->name,
                'data' => array_values($lableData),
                'borderWidth' => 0,
                'pointRadius' => 18,
                'tension' => 0.2,
            ];
        });
        return $this->success([
            'labels' => $dates,
            'datasets' => $mappedcollection
        ]);

    }

    public function Income()
    {
        $gym = auth()->user()->gym()->first();

        $sum7days = TableTask::query()
            ->select(
                DB::raw('DATE(opened_at) as date'),
                DB::raw("SUM(price_so_far) AS day_income")
            )
            ->whereIn('table_id', $gym->tables()->select('id'))
            ->where('opened_at', '>', Carbon::now()->subDay(7))
            ->groupBy('date')
            ->orderBy('opened_at')
            ->get();

        $sum7daysTOTAL = TableTask::query()
            ->select(DB::raw("SUM(price_so_far) AS sum_7days_income"))
            ->whereIn('table_id', $gym->tables()->select('id'))
            ->where('opened_at', '>', Carbon::now()->subDay(7))
            ->first();

        $sum1monthTOTAL = TableTask::query()
            ->select(DB::raw("SUM(price_so_far) AS sum_month_income"))
            ->whereIn('table_id', $gym->tables()->select('id'))
            ->where('opened_at', '>', Carbon::now()->subMonth(1))
            ->first();


        return $this->success([
            'sum_7days_income' => $sum7daysTOTAL->sum_7days_income,
            'sum_month_income' => $sum1monthTOTAL->sum_month_income,
            'income_7days' => $sum7days
        ]);
    }
}
