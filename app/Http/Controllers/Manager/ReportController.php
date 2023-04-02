<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReportTableResource;
use App\Models\TableTask;
use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\CommonMark\Extension\Table\Table;

class ReportController extends Controller
{
    public function HoursPerTables(Request $request)
    {

        $gym = auth()->user()->gym()->first();
        return ReportTableResource::collection($gym->tables()->get());



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
}
