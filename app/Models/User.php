<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'mobile',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole($role)
    {
        return (bool)$this->roles()->where('name', $role)->count();
    }

    public function hasPermission($permission)
    {
        return (bool)$this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('name', $permission);
        })->count();
    }

    public function gym()
    {
        return $this->hasOne(Gym::class,'manager_id');
    }

    public function verificationCode()
    {
        return $this->hasMany(VerificationCode::class);
    }

    public function tasks()
    {
        return $this->hasMany(TableTask::class,'player_id');
    }

}
