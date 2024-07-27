@php
$configData = Helper::appClasses();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  @if(!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      <img src="{{ asset('img/gp1.png') }}" alt="" width="34" height="34">
      <span class="app-brand-text demo menu-text fw-bold ms-2">الشركة العالمية</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M11.4854 4.88844C11.0081 4.41121 10.2344 4.41121 9.75715 4.88844L4.51028 10.1353C4.03297 10.6126 4.03297 11.3865 4.51028 11.8638L9.75715 17.1107C10.2344 17.5879 11.0081 17.5879 11.4854 17.1107C11.9626 16.6334 11.9626 15.8597 11.4854 15.3824L7.96672 11.8638C7.48942 11.3865 7.48942 10.6126 7.96672 10.1353L11.4854 6.61667C11.9626 6.13943 11.9626 5.36568 11.4854 4.88844Z" fill="currentColor" fill-opacity="0.6" />
        <path d="M15.8683 4.88844L10.6214 10.1353C10.1441 10.6126 10.1441 11.3865 10.6214 11.8638L15.8683 17.1107C16.3455 17.5879 17.1192 17.5879 17.5965 17.1107C18.0737 16.6334 18.0737 15.8597 17.5965 15.3824L14.0778 11.8638C13.6005 11.3865 13.6005 10.6126 14.0778 10.1353L17.5965 6.61667C18.0737 6.13943 18.0737 5.36568 17.5965 4.88844C17.1192 4.41121 16.3455 4.41121 15.8683 4.88844Z" fill="currentColor" fill-opacity="0.38" />
      </svg>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @can('manage dashboard')
    <li class="menu-item">
      <a href="{{ route('home') }}" class="menu-link">
        <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
        <div>الرئيسية</div>
      </a>
    </li>
    @endcan

    @can('create facility')
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-plus-circle-outline"></i>
        <div>إضافة تسهيل</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('facilities.create') }}" class="menu-link">
            <div>عن طريق الإدارة</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('facilitiesByBranch.create') }}" class="menu-link">
            <div>عن طريق الفرع</div>
          </a>
        </li>
      </ul>
    </li>
    @endcan

    @can('show facility')
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-file-outline"></i>
        <div>التسهيلات</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('facilities.index') }}" class="menu-link">
            <div>عن طريق الإدارة</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('facilitiesByBranch.index') }}" class="menu-link">
            <div>عن طريق الفرع</div>
          </a>
        </li>
      </ul>
    </li>
    @endcan

    @can('show reports')
      <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
          <i class="menu-icon tf-icons mdi mdi-chart-box-outline"></i>
          <div>التقارير</div>
        </a>
        <ul class="menu-sub">
          <li class="menu-item">
            <a href="{{ route('reports.index') }}" class="menu-link">
              <div>عن طريق الإدارة</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="{{ route('reportsByBranch.index') }}" class="menu-link">
              <div>عن طريق الفرع</div>
            </a>
          </li>
        </ul>
      </li>
    @endcan

    @can('manage users')
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
        <div>المستخدمين</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('users.index') }}" class="menu-link">
            <div>المستخدمين</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('roles.index') }}" class="menu-link">
            <div>الأدوار</div>
          </a>
        </li>
      </ul>
    </li>
    @endcan

    @can('manage settings')
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons mdi mdi-cog-outline"></i>
        <div>الإعدادات</div>
      </a>
      <ul class="menu-sub">
        <li class="menu-item">
          <a href="{{ route('branches.index') }}" class="menu-link">
            <div>الفروع</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('types.index') }}" class="menu-link">
            <div>الأنواع</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('currencies.index') }}" class="menu-link">
            <div>العملات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('categories.index') }}" class="menu-link">
            <div>الفئات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('specializations.index') }}" class="menu-link">
            <div>التخصصات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('products.index') }}" class="menu-link">
            <div>الأصناف</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('units.index') }}" class="menu-link">
            <div>الوحدات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('statuses.index') }}" class="menu-link">
            <div>الحالات</div>
          </a>
        </li>
        <li class="menu-item">
          <a href="{{ route('firewalls.index') }}" class="menu-link">
            <div>IPs</div>
          </a>
        </li>
      </ul>
    </li>
    @endcan
  </ul>
</aside>
