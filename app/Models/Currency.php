<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'code', 'symbol', 'is_active'];

  public function facilities(): HasMany
  {
    return $this->hasMany(Facility::class);
  }

  public function facilitiesByBranch(): HasMany
  {
    return $this->hasMany(FacilityByBranch::class);
  }

  public function scopeFilter($query)
  {
    if (request('search')) {
      $query->where('name', 'like', '%' . request('search') . '%');
    }

    return $query;
  }
}
