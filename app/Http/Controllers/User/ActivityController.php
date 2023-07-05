<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\TableTaskResource;
use App\Models\TableTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();

        $activities = $user->tasks()
            ->select('*', DB::raw('DATE(opened_at) as date'))
            ->get()
            ->mapToGroups(function ($item) {
//                if ($item->date == '2023-06-27' or $item->date == '2023-05-02')
//                    return [
//                        '2023-05-02 => 2023-06-27' => $item
//                    ];
//                else
//                    return [
//                        $item->date => $item
//                    ];
                return [
                    Verta($item->date)->format('Y-m-d') => TableTaskResource::make($item),
//                    $item->date => TableTaskResource::make($item),
                ];
            });

        return $this->success(($activities));
    }

    public function show(TableTask $activity)
    {
        return $this->success(\App\Http\Resources\TableTaskResource::make($activity));
    }
}
