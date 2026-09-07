<div class="sidebar">
  <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
    <div class="image">
      <div class="rounded-circle bg-danger text-white font-weight-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 13px;">
        SA
      </div>
    </div>
    <div class="info ml-2">
      <a href="javascript:void(0)" class="d-block font-weight-bold">{{ auth()->user()->name ?? 'Super Administrator' }}</a>
      <small class="text-muted text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">Global Superadmin</small>
    </div>
  </div>

  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-header text-uppercase text-muted small font-weight-bold" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Core Navigation</li>
      
      <li class="nav-item">
        <a href="{{ route('superadmin.index') }}" class="nav-link {{ request()->routeIs('superadmin.index') ? 'active' : '' }}">
          <i class="nav-icon fas fa-chart-pie"></i>
          <p>Superadmin Overview</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('superadmin.lablist') }}" class="nav-link {{ request()->routeIs('superadmin.lablist', 'superadmin.addlab') ? 'active' : '' }}">
          <i class="nav-icon fas fa-hospital-alt"></i>
          <p>Diagnostic Labs</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('superadmin.subscriptions') }}" class="nav-link {{ request()->routeIs('superadmin.subscriptions') ? 'active' : '' }}">
          <i class="nav-icon fas fa-shield-alt"></i>
          <p>Subscriptions & Licenses</p>
        </a>
      </li>

      <li class="nav-header text-uppercase text-muted small font-weight-bold mt-2" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Master Catalogs</li>

      <li class="nav-item">
        <a href="{{ route('superadmin.tests') }}" class="nav-link {{ request()->routeIs('superadmin.tests', 'superadmin.addtest') ? 'active' : '' }}">
          <i class="nav-icon fas fa-vials"></i>
          <p>Master Diagnostic Tests</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('superadmin.departments') }}" class="nav-link {{ request()->routeIs('superadmin.departments', 'superadmin.adddepartment') ? 'active' : '' }}">
          <i class="nav-icon fas fa-network-wired"></i>
          <p>Departments</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('superadmin.roles') }}" class="nav-link {{ request()->routeIs('superadmin.roles') ? 'active' : '' }}">
          <i class="nav-icon fas fa-user-shield"></i>
          <p>Global Roles & Scope</p>
        </a>
      </li>
    </ul>
  </nav>
</div>
