<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
  use HasFactory;

  protected $fillable = ['name', 'specialization_id', 'is_active'];

  public function specialization(): BelongsTo
  {
    return $this->belongsTo(Specialization::class);
  }

  public function facilities(): BelongsToMany
  {
    return $this->belongsToMany(Facility::class);
  }

  public function facilitiesByBranch(): BelongsToMany
  {
    return $this->belongsToMany(FacilityByBranch::class, 'facility_by_branch_product', 'product_id', 'facility_id');
  }

  public function scopeFilter($query)
  {
    if (request('specialization')) {
      $query->where('specialization_id', request('specialization'));
    }
    if (request('search')) {
      $query->where('name', 'like', '%' . request('search') . '%');
    }

    return $query;
  }
}
