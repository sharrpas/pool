<?php

namespace App\Services;

use App\Http\Resources\BuffetResource;
use Illuminate\Support\Carbon;

class GetBuffet
{
    public function buffets($task)
    {

        if ($task->buffet != null) {
            $task_buffets = array_map('intval', explode(',', $task->buffet));
            sort($task_buffets);

            $buffets = \App\Models\Buffet::query()->whereIn('id', $task_buffets)->get();
            $ids = $buffets->pluck('id')->toArray();
            $buffetsInArray = BuffetResource::collection($buffets);
            $finalBuffets = collect($task_buffets)->map(function ($id) use ($ids, $buffetsInArray) {
                return $buffetsInArray[array_search($id, array_values($ids))];
            });

            return $finalBuffets;
        }
        return null;

    }
}
