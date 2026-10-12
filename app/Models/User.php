<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function tests()
    {
        return $this->belongsToMany(Test::class)->withPivot('attempts','used_attempts','use_photo','use_train','current_percent','current_score','is_passed','photo_file')->as('parameters');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function filial()
    {
        return $this->belongsTo(Filial::class,'filial_id');
    }

    // public function people()
    // {
    //     if ($this->role_id == 2) 
    //     {
    //         $users =  User::where('filial_id',$this->filial_id)->latest()->paginate(20);
    //         $users->setPath('/api/result');
    //         return $users;
    //     }elseif ($this->role_id == 1)
    //     {
    //         $users = User::latest()->paginate(20);
    //         $users->setPath('/api/result');
    //         return $users;
    //     }
    // }

    public function people($search)
    {
        if ($this->role_id == 2) {
            $query = $this->where('filial_id', $this->filial_id)->latest();
        } elseif ($this->role_id == 1) {
            $query = $this;
        }

        if ($search) {
            $query = $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('lastname', 'LIKE', "%{$search}%")
                    ->orWhere('firstname', 'LIKE', "%{$search}%")
                    ->orWhere('middlename', 'LIKE', "%{$search}%")
                    ->orWhere('division', 'LIKE', "%{$search}%")
                    ->orWhere('job', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('login', 'LIKE', "%{$search}%");
            });
        }
   
        return $query->orderBy('id','desc')->paginate(12)->appends(['search' => $search]);
    }


    public function filials()
    {
        if ($this->role_id == 2) 
        {
            return $this->filial()->get();
        }elseif ($this->role_id == 1)
        {
            return Filial::all();
        }
    }

    public function roles()
    {
        if ($this->role_id == 2) 
        {
            return Role::where('id',3)->get();
        }elseif ($this->role_id == 1)
        {
            return Role::all();
        }
    }

    
}
