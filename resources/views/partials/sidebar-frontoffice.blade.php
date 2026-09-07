<div class="sidebar">
  @php
    $roleId = auth()->user()->role_id ?? 3;
    $initials = 'ST';
    if ($roleId == 3) $initials = 'FO';
    elseif ($roleId == 4) $initials = 'LT';
    elseif ($roleId == 6) $initials = 'AP';
    elseif ($roleId == 7) $initials = 'PA';
  @endphp

  <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
    <div class="image">
      <div class="rounded-circle bg-info text-white font-weight-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 13px;">
        {{ $initials }}
      </div>
    </div>
    <div class="info ml-2">
      <a href="javascript:void(0)" class="d-block font-weight-bold">{{ auth()->user()->name ?? 'Staff User' }}</a>
      <small class="text-muted text-uppercase" style="font-size: 10px; letter-spacing: 0.05em;">{{ auth()->user()->role->name ?? 'Front Office' }}</small>
    </div>
  </div>

  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      @if($roleId == 3)
        <li class="nav-header text-uppercase text-muted small font-weight-bold" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Front Desk</li>

        <li class="nav-item">
          <a href="{{ route('frontoffice.index') }}" class="nav-link {{ request()->routeIs('frontoffice.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-plus"></i>
            <p>New Patient Booking</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('frontoffice.bills') }}" class="nav-link {{ request()->routeIs('frontoffice.bills') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-invoice"></i>
            <p>Receipts & Invoices</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('frontoffice.labpayment') }}" class="nav-link {{ request()->routeIs('frontoffice.labpayment') ? 'active' : '' }}">
            <i class="nav-icon fas fa-money-check-alt"></i>
            <p>B2B Lab Settlements</p>
          </a>
        </li>
      @endif

      @if(in_array($roleId, [4, 5, 6, 7]))
        <li class="nav-header text-uppercase text-muted small font-weight-bold" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Laboratory Workflow</li>

        @if($roleId == 4)
          <li class="nav-item">
            <a href="{{ route('labinvestigation.collectsample') }}" class="nav-link {{ request()->routeIs('labinvestigation.collectsample') ? 'active' : '' }}">
              <i class="nav-icon fas fa-vial"></i>
              <p>Phlebotomy & Collector</p>
            </a>
          </li>
        @endif

        <li class="nav-item">
          <a href="{{ route('labinvestigation.index') }}" class="nav-link {{ request()->routeIs('labinvestigation.index') ? 'active' : '' }}">
            <i class="nav-icon fas fa-microscope"></i>
            <p>Investigation Worklist</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('labinvestigation.pendingtest') }}" class="nav-link {{ request()->routeIs('labinvestigation.pendingtest') ? 'active' : '' }}">
            <i class="nav-icon fas fa-edit"></i>
            <p>Pending Result Entry</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('labinvestigation.processedtests') }}" class="nav-link {{ request()->routeIs('labinvestigation.processedtests') ? 'active' : '' }}">
            <i class="nav-icon fas fa-clipboard-check"></i>
            <p>Processed Queue</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="{{ route('labinvestigation.approvedtests') }}" class="nav-link {{ request()->routeIs('labinvestigation.approvedtests') ? 'active' : '' }}">
            <i class="nav-icon fas fa-stamp"></i>
            <p>Approved & Signed</p>
          </a>
        </li>
      @endif

      <li class="nav-header text-uppercase text-muted small font-weight-bold mt-2" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Reports & Account</li>

      <li class="nav-item">
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
          <i class="nav-icon fas fa-file-pdf"></i>
          <p>Search Pathology Reports</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.index', 'profile') ? 'active' : '' }}">
          <i class="nav-icon fas fa-user-circle"></i>
          <p>My Profile</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('password.change') }}" class="nav-link {{ request()->routeIs('password.change', 'password.changepassword') ? 'active' : '' }}">
          <i class="nav-icon fas fa-key"></i>
          <p>Change Password</p>
        </a>
      </li>
    </ul>
  </nav>
</div>
