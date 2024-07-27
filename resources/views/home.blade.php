@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts.layoutMaster')

@section('title', 'الصفحة الرئيسية')

@section('content')
  <div class="row gy-4">
    <!-- Cards with basics info -->
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="avatar me-3">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-poll mdi-24px"> </i>
              </div>
            </div>
            <div class="card-info">
              <div class="d-flex align-items-center">
                <h4 class="mb-0">{{ $number_of_facilities_by_management }}</h4>
              </div>
              <small>تسهيلات عن طريق الإدارة</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="avatar me-3">
              <div class="avatar-initial bg-label-success rounded">
                <i class="mdi mdi-trending-up mdi-24px"> </i>
              </div>
            </div>
            <div class="card-info">
              <div class="d-flex align-items-center">
                <h4 class="mb-0">{{ $new_facilities }}</h4>
              </div>
              <small>تسهيلات جديدة عن طريق الإدارة</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="avatar me-3">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-poll mdi-24px"> </i>
              </div>
            </div>
            <div class="card-info">
              <div class="d-flex align-items-center">
                <h4 class="mb-0">{{ $number_of_facilities_by_branches }}</h4>
              </div>
              <small>تسهيلات عن طريق الفروع</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="avatar me-3">
              <div class="avatar-initial bg-label-info rounded">
                <i class="mdi mdi-pill mdi-24px"> </i>
              </div>
            </div>
            <div class="card-info">
              <div class="d-flex align-items-center">
                <h4 class="mb-0">{{ $products_count }}</h4>
              </div>
              <small>عدد الأصناف</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Cards with few info -->

    <!-- Bar Charts -->
    <div class="col-xl-8 col-12 mb-4">
      <div class="card">
        <div class="card-header header-elements">
          <h5 class="card-title mb-0">إحصائية التسهيلات عن طريق الإدارة</h5>
        </div>
        <div class="card-body">
          <canvas id="facilities_by_management" class="chartjs" data-height="400"></canvas>
        </div>
      </div>
    </div>
    <!-- /Bar Charts -->

    <!-- Doughnut Chart -->
    <div class="col-lg-4 col-12 mb-4">
      <div class="card">
        <h5 class="card-header">إحصائية التسهيلات حسب العملات</h5>
        <div class="card-body">
          <canvas id="facilities_by_management_currencies" class="chartjs mb-4" data-height="350"></canvas>
          <ul class="doughnut-legend d-flex justify-content-around ps-0 mb-2 pt-1">
            <li class="ct-series-0 d-flex flex-column">
              <h5 class="mb-0">يمني</h5>
              <span
                class="badge badge-dot my-2 cursor-pointer rounded-pill"
                style="background-color: rgb(102, 110, 232); width: 35px; height: 6px"></span>
              @if(!empty($facilities_by_management_currency_count[0]))
                <div class="text-muted">{{ round($facilities_by_management_currency_count[0] / $number_of_facilities_by_date * 100, 2) }} %</div>
              @endif
            </li>
            <li class="ct-series-1 d-flex flex-column">
              <h5 class="mb-0">دولار</h5>
              <span
                class="badge badge-dot my-2 cursor-pointer rounded-pill"
                style="background-color: rgb(40, 208, 148); width: 35px; height: 6px"></span>
              @if(!empty($facilities_by_management_currency_count[1]))
                <div class="text-muted">{{ round($facilities_by_management_currency_count[1] / $number_of_facilities_by_date * 100, 2) }} %</div>
              @endif
            </li>
            <li class="ct-series-2 d-flex flex-column">
              <h5 class="mb-0">سعودي</h5>
              <span
                class="badge badge-dot my-2 cursor-pointer rounded-pill"
                style="background-color: rgb(253, 172, 52); width: 35px; height: 6px"></span>
              @if(!empty($facilities_by_management_currency_count[2]))
                <div class="text-muted">{{ round($facilities_by_management_currency_count[2] / $number_of_facilities_by_date * 100, 2) }} %</div>
              @endif
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- /Doughnut Chart -->

    <!-- Bar Charts -->
    <div class="col-xl-12 col-12 mb-4">
      <div class="card">
        <div class="card-header header-elements">
          <h5 class="card-title mb-0">إحصائية التسهيلات عن طريق الفروع</h5>
        </div>
        <div class="card-body">
          <canvas id="facilities_by_branches" class="chartjs" data-height="400"></canvas>
        </div>
      </div>
    </div>
    <!-- /Bar Charts -->

    <div class="col-lg-12 col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="fw-semibold">
            التسهيلات الجديدة
          </h4>
        </div>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
            <tr class=" fs-5">
              <th class="">
                <input class="form-check-input" type="checkbox" name="" id="">
              </th>
              <th class="fw-bold fs-6">الرقم</th>
              <th class="fw-bold fs-6">التسهيل</th>
              <th class="fw-bold fs-6">الفرع</th>
              <th class="fw-bold fs-6">التاريخ</th>
              <th class="fw-bold fs-6">المبلغ</th>
              <th class="fw-bold fs-6">العملة</th>
              <th class="fw-bold fs-6">الوحدة</th>
              <th class="fw-bold fs-6">الحالة</th>
              <th class="fw-bold fs-6">الإجراءات</th>
            </tr>
            </thead>
            <tbody class="table-border-bottom-0">
            @forelse($facilities as $facility)
              <tr>
                <th class="">
                  <input class="form-check-input" type="checkbox" name="" id="">
                </th>
                <td><span class="fw-medium">{{ $facility->facility_number }}</span></td>
                <td>
                  <a href="{{ route('facilities.show', ['facility' => $facility->id]) }}" class="text-body fw-medium">
                    {{ \Illuminate\Support\Str::limit($facility->recipient, 30) }}
                  </a>
                </td>
                <td><span class="fw-medium">{{ $facility->branch->name }}</span></td>
                <td><span class="fw-medium">{{ $facility->date }}</span></td>
                <td><span class="fw-medium">{{ number_format($facility->amount) }}  {{ $facility->currency->symbol }}</span></td>
                <td><span class="fw-medium">{{ $facility->currency->name }}</span></td>
                <td><span class="fw-medium">{{ $facility->unit->name }}</span></td>
                <td>
                  @if($facility->status_id == 1)
                    <span class="badge rounded-pill bg-label-primary"> {{ $facility->status->name }} </span>
                  @elseif($facility->status_id == 2)
                    <span class="badge rounded-pill bg-label-warning"> {{ $facility->status->name }} </span>
                  @elseif($facility->status_id == 3)
                    <span class="badge rounded-pill bg-label-success"> {{ $facility->status->name }} </span>
                  @elseif($facility->status_id == 4)
                    <span class="badge rounded-pill bg-label-danger"> {{ $facility->status->name }} </span>
                  @else
                    <span class="badge rounded-pill bg-label-secondary"> {{ $facility->status->name }} </span>
                  @endif
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn btn-label-primary px-2 py-2" data-bs-toggle="dropdown">
                      <i class="mdi mdi-dots-horizontal"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('facilities.show', ['facility' => $facility->id]) }}"
                      ><i class="mdi mdi-eye-outline me-1"></i>عرض التسهيل</a>
                      <a class="dropdown-item" href="{{ route('facilities.exportFacility', ['facility' => $facility->id]) }}"
                      ><i class="mdi mdi-download-outline me-1"></i>تنزيل التسهيل</a>
                      @can('edit facility')
                        @if($facility->status_id != 2 && $facility->status_id != 3 && $facility->status_id != 4)
                          <a class="dropdown-item" href="{{ route('facilities.edit', ['facility' => $facility->id]) }}"
                          ><i class="mdi mdi-pencil-outline me-1"></i>تعديل التسهيل</a>
                        @elseif(Auth::user()->roles[0]->id == 1)
                          <a class="dropdown-item" href="{{ route('facilities.edit', ['facility' => $facility->id]) }}"
                          ><i class="mdi mdi-pencil-outline me-1"></i>تعديل التسهيل</a>
                        @endif
                      @endcan
                      @can('delete facility')
                      <form  action="{{ route('facilities.destroy', ['facility' => $facility->id]) }}" method="POST">
                        @method('DELETE')
                        @csrf

                        <button type="submit" class="dropdown-item"
                                onclick="return confirm('هل أنت متأكد من أنك تريد حذف هذا التسهيل؟')"><i class="mdi mdi-trash-can-outline me-1"></i>حذف التسهيل</button>
                      </form>
                      @endcan
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9">
                  لم يتم العثور على تسهيلات
                </td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="mb-3"></div>
      {{ $facilities->links() }}
    </div>
  </div>
@endsection

@section('page-script')
<script>
  var number_of_facilities_by_management = JSON.parse('{!! json_encode($number_of_facilities_by_management) !!}');
  var number_of_facilities_by_branches = JSON.parse('{!! json_encode($number_of_facilities_by_branches) !!}');
  // facilities chart by management
  var facilities_by_management_branch_name = JSON.parse('{!! json_encode($facilities_by_management_branch_name) !!}');
  var facilities_by_management_branch_count = JSON.parse('{!! json_encode($facilities_by_management_branch_count) !!}');
  // facilities chart by currencies
  var facilities_by_management_currency_name = JSON.parse('{!! json_encode($facilities_by_management_currency_name) !!}');
  var facilities_by_management_currency_count = JSON.parse('{!! json_encode($facilities_by_management_currency_count) !!}');
  // facilities chart by branch
  var facilities_by_branches_branch_name = JSON.parse('{!! json_encode($facilities_by_branches_branch_name) !!}');
  var facilities_by_branches_branch_count = JSON.parse('{!! json_encode($facilities_by_branches_branch_count) !!}');
</script>
<!-- Vendors JS -->
<script src="{{ asset(mix('assets/vendor/libs/chartjs/chartjs.js')) }}"></script>
<!-- Page JS -->
<script src="{{ asset(mix('assets/js/homeChart.js')) }}"></script>
@endsection
