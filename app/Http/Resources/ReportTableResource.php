<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ReportTableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
//        return parent::toArray($request);
        return [
            'id' => $this->id,
            'gym_id' => $this->gym_id,
            'name' => $this->name,
            'pic' => $this->pic,

            'tasks' => ReportTaskResource::collection($this->tasks()
                ->select(
                    DB::raw('DATE(opened_at) as date'),
                    DB::raw('count(*) as count'),
                    DB::raw('table_id'),
                    DB::raw("SUM(price_so_far) AS sum_price_so_far"),
                    DB::raw("opened_at"),
                    DB::raw("closed_at"),

                )
                ->where('opened_at','>',Carbon::now()->subMonth(1))
                ->groupBy('table_id')
                ->groupBy('date')
                ->get())

            ];
    }
}
