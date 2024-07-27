<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasPermissionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasPermissionsTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'job_title',
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

  protected $with = ['roles'];

  public function facilities(): HasMany
  {
    return $this->hasMany(Facility::class);
  }

  public function facilitiesByBranch(): HasMany
  {
    return $this->hasMany(FacilityByBranch::class);
  }

  public function roles(): BelongsToMany
  {
    return $this->belongsToMany(Role::class, 'users_roles');
  }

  public function permissions(): BelongsToMany
  {
    return $this->belongsToMany(Permission::class, 'users_permissions');
  }

  public function branches(): BelongsToMany
  {
    return $this->belongsToMany(Branch::class);
  }

  public function scopeFilter($query)
  {
    if (request('search_text')) {
      $query->where('name', 'like', '%' . request('search_text') . '%')
        ->orWhere('username', 'like', '%' . request('search_text') . '%')
        ->orWhere('email', 'like', '%' . request('search_text') . '%');
    }
    if (request('role')) {
      $query->whereHas('roles', function ($q) {
        $q->where('id', request('role'));
      });
    }

    return $query;
  }
}
