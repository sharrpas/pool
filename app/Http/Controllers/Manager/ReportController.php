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
        for ($i = 0; $i < count($date); $i++) {
            $dates[$i] = $date[$i]->format('Y-m-d');
        }

        return $this->success([
            'dates' => $dates,
            'tables' => ReportTableResource::collection($gym->tables()->get())]);


//        $validated_data = Validator::make($request->all(), [
//            'start_at' => 'required|date_format:Y-m-d H:i:s',
//            'end_at' => 'required|date_format:Y-m-d H:i:s',
//        ]);
//
//        if ($validated_data->fails())
//            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors());

//        $tables = $gym->tables()->select(['id', 'gym_id', 'name', 'pic'])
//            ->with(['tasks' => function ($query) use ($request) {
//                $query->whereBetween('opened_at', [$request->start_at, $request->end_at]);
//            }])
//            ->withCount([
//                'tasks AS price_so_far_sum' => function ($query) {
//                    $query->select(DB::raw("SUM(price_so_far)"));
//                }
//            ])
//            ->get();
//
//        $rr = TableTask::query()
//            ->select(
//                DB::raw('DATE(opened_at) as date'),
//                DB::raw('count(*) as views'),
//                DB::raw('table_id'),
//                DB::raw("SUM(price_so_far) AS sum_price_so_far"))
//            ->whereIn('table_id',$gym->tables()->select('id'))
//            ->whereBetween('opened_at', [$request->start_at, $request->end_at])
//            ->groupBy('table_id')
//            ->groupBy('date')
//            ->get();

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
