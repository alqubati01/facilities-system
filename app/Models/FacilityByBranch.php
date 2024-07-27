<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityByBranch extends Model
{
  use HasFactory, SoftDeletes;

  protected $table = 'facilities_by_branches';

  public function branch(): BelongsTo
  {
    return $this->belongsTo(Branch::class);
  }

  public function unit(): BelongsTo
  {
    return $this->belongsTo(Unit::class);
  }

  public function currency(): BelongsTo
  {
    return $this->belongsTo(Currency::class);
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo(Type::class);
  }

  public function specialization(): BelongsTo
  {
    return $this->belongsTo(Specialization::class);
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  public function products(): BelongsToMany
  {
    return $this->belongsToMany(Product::class, 'facility_by_branch_product', 'facility_id', 'product_id');
  }

  public function status(): BelongsTo
  {
    return $this->belongsTo(Status::class);
  }

  public function createdBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by')->withTrashed();
  }

  public function updatedBy(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by')->withTrashed();
  }

  public function scopeFilter($query)
  {
    if (request('branch')) {
      $query->whereIn('branch_id', request('branch'));
    }
    if (request('unit')) {
      $query->whereIn('unit_id', request('unit'));
    }
    if (request('currency')) {
      $query->whereIn('currency_id', request('currency'));
    }
    if (request('specialization')) {
      $query->whereIn('specialization_id', request('specialization'));
    }
    if (request('category')) {
      $query->whereIn('category_id', request('category'));
    }
    if (request('product')) {
      $query->whereHas('products', function ($q) {
        $q->whereIn('id', request('product'));
      });
    }
    if (request('status')) {
      $query->whereIn('status_id', request('status'));
    }
    if (request('date')) {
      $date = explode(' - ', request('date'));
      $dateFrom = $date[0];
      $dateTo = $date[1];

      $query->where('date', '>=', $dateFrom)
        ->where('date', '<=', $dateTo);
    }
    if (request('search')) {
      $query->where('recipient', 'like', '%' . request('search') . '%')
        ->orWhere('amount', 'like', request('search'))
        ->orWhere('facility_number', 'like', request('search'));
    }

    return $query;
  }

  public function scopeFilterExport($query)
  {
    $queryString = request()->query('data');
    parse_str($queryString, $queryArray);

    if (array_key_exists('branch', $queryArray) && !empty($queryArray['branch'])) {
      $query->whereIn('branch_id', $queryArray['branch']);
    }
    if (array_key_exists('unit', $queryArray) && !empty($queryArray['unit'])) {
      $query->whereIn('unit_id', $queryArray['unit']);
    }
    if (array_key_exists('currency', $queryArray) && !empty($queryArray['currency'])) {
      $query->whereIn('currency_id', $queryArray['currency']);
    }
    if (array_key_exists('specialization', $queryArray) && !empty($queryArray['specialization'])) {
      $query->whereIn('specialization_id', $queryArray['specialization']);
    }
    if (array_key_exists('category', $queryArray) && !empty($queryArray['category'])) {
      $query->whereIn('category_id', $queryArray['category']);
    }
    if (array_key_exists('product', $queryArray) && !empty($queryArray['product'])) {
      $query->whereHas('products', function ($q) use ($queryArray) {
        $q->whereIn('id', $queryArray['product']);
      });
    }
    if (array_key_exists('status', $queryArray) && !empty($queryArray['status'])) {
      $query->whereIn('status_id', $queryArray['status']);
    }
    if (array_key_exists('date', $queryArray) && !empty($queryArray['date'])) {
      $date = explode(' - ', $queryArray['date']);
      $dateFrom = $date[0];
      $dateTo = $date[1];

      $query->where('date', '>=', $dateFrom)
        ->where('date', '<=', $dateTo);
    }
    if (array_key_exists('search', $queryArray) && !empty($queryArray['search'])) {
      $query->where('recipient', 'like', $queryArray['search'])
        ->orWhere('amount', 'like', $queryArray['search'])
        ->orWhere('facility_number', 'like', $queryArray['search']);
    }

    return $query;
  }
}
