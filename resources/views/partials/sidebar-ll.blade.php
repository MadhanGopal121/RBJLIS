<div class="sidebar">
  <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
    <div class="image">
      <div class="rounded-circle bg-success text-white font-weight-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 13px;">
        B2B
      </div>
    </div>
    <div class="info ml-2">
      <a href="javascript:void(0)" class="d-block font-weight-bold">{{ auth()->user()->name ?? 'B2B Partner' }}</a>
      <small class="text-muted" style="font-size: 11px;">Outsource Client Portal</small>
    </div>
  </div>

  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-header text-uppercase text-muted small font-weight-bold" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Client Menu</li>

      <li class="nav-item">
        <a href="{{ route('ll.index') }}" class="nav-link {{ request()->routeIs('ll.index') ? 'active' : '' }}">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>B2B Dashboard</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('ll.createbill') }}" class="nav-link {{ request()->routeIs('ll.createbill') ? 'active' : '' }}">
          <i class="nav-icon fas fa-plus-circle"></i>
          <p>Order Investigation</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('ll.reports') }}" class="nav-link {{ request()->routeIs('ll.reports') ? 'active' : '' }}">
          <i class="nav-icon fas fa-file-medical-alt"></i>
          <p>Patient Reports</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('ll.payments') }}" class="nav-link {{ request()->routeIs('ll.payments') ? 'active' : '' }}">
          <i class="nav-icon fas fa-wallet"></i>
          <p>Settlements & Ledger</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('ll.ratecard') }}" class="nav-link {{ request()->routeIs('ll.ratecard') ? 'active' : '' }}">
          <i class="nav-icon fas fa-tags"></i>
          <p>Wholesale Rate Card</p>
        </a>
      </li>
    </ul>
  </nav>
</div>
