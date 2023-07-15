<?php

namespace App\Http\Controllers\Manager;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\String\u;

class UserController extends Controller
{
    public function index()
    {
        $users = User::query()->whereHas('roles', function ($q) {
            $q->where('name', 'user');
        })->select(['name', 'username'])->get();
        //todo  need pagination ????
        return $this->success(UserResource::collection($users));
    }

    public function table_for_user(Table $table,Request $request)
    {
        if ($table->gym()->first()->manager_id != auth()->user()->id)
            return $this->error(Status::AUTHENTICATION_FAILED);

        $validated_data = Validator::make($request->all(), [
            'username' => 'string',   //|exists:App\Models\User,username
        ]);

        if ($validated_data->fails())
            return $this->error(Status::VALIDATION_FAILED, $validated_data->errors()->first());

        if (!$task = $table->tasks()->where('closed_at', null)->first())
            return $this->error(Status::NOT_FOUND, 'میز هنوز باز نشده');

        if (!$request->username) {
            $task->update([
                'player_id' => null,
            ]);
            return $this->success('میز '.$table->name.' برای بازیکن مهمان تعریف شد');
        }

        if (!$user = User::query()->where('username',$request->username)->first())
            return $this->error(Status::NOT_FOUND,'نام کاربری مورد نظر پیدا نشد');

        if (!$user->hasRole('user'))
            return $this->error(Status::NOT_FOUND,'نام کاربری مورد نظر در لیست کاربران پیدا نشد');

        if ($user->tasks()->where('closed_at',null)->first())
            return $this->error(Status::NOT_FOUND,'بازیکن مورد نظر در حال حاضر یک میز فعال دارد');

        $task->update([
            'player_id' => $user->id,
        ]);

        return $this->success('میز '.$table->name.' برای بازیکن '.$user->name.' تعریف شد');
    }
}
