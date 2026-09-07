<div class="sidebar">
  <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
    <div class="image">
      <div class="rounded-circle bg-primary text-white font-weight-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 13px;">
        LA
      </div>
    </div>
    <div class="info ml-2">
      <a href="javascript:void(0)" class="d-block font-weight-bold">{{ auth()->user()->name ?? 'Lab Administrator' }}</a>
      <small class="text-muted" style="font-size: 11px;">{{ auth()->user()->lab?->name ?? 'Diagnostic Center' }}</small>
    </div>
  </div>

  <nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
      <li class="nav-header text-uppercase text-muted small font-weight-bold" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Main Operations</li>

      <li class="nav-item">
        <a href="{{ route('labadmin.index') }}" class="nav-link {{ request()->routeIs('labadmin.index') ? 'active' : '' }}">
          <i class="nav-icon fas fa-chart-line"></i>
          <p>Lab Dashboard</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.bills') }}" class="nav-link {{ request()->routeIs('labadmin.bills', 'labadmin.deletedbills') ? 'active' : '' }}">
          <i class="nav-icon fas fa-receipt"></i>
          <p>Receipts & Invoices</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labinvestigation.index') }}" class="nav-link {{ request()->routeIs('labinvestigation.*') ? 'active' : '' }}">
          <i class="nav-icon fas fa-microscope"></i>
          <p>Investigation Pipeline</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
          <i class="nav-icon fas fa-file-medical-alt"></i>
          <p>Pathology Reports</p>
        </a>
      </li>

      <li class="nav-header text-uppercase text-muted small font-weight-bold mt-2" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Laboratory Directory</li>

      <li class="nav-item">
        <a href="{{ route('labadmin.labtests') }}" class="nav-link {{ request()->routeIs('labadmin.labtests') ? 'active' : '' }}">
          <i class="nav-icon fas fa-vials"></i>
          <p>Predefined Diagnostic Tests</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.packages') }}" class="nav-link {{ request()->routeIs('labadmin.packages', 'labadmin.addpackage') ? 'active' : '' }}">
          <i class="nav-icon fas fa-cubes"></i>
          <p>Health Packages & Profiles</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.patients') }}" class="nav-link {{ request()->routeIs('labadmin.patients', 'labadmin.editpatient') ? 'active' : '' }}">
          <i class="nav-icon fas fa-user-injured"></i>
          <p>Patients Directory</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.doctors') }}" class="nav-link {{ request()->routeIs('labadmin.doctors', 'labadmin.adddoctor') ? 'active' : '' }}">
          <i class="nav-icon fas fa-user-md"></i>
          <p>Referring Doctors</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.labs') }}" class="nav-link {{ request()->routeIs('labadmin.labs', 'labadmin.addlabtolab', 'labadmin.speciallabrates') ? 'active' : '' }}">
          <i class="nav-icon fas fa-handshake"></i>
          <p>Lab-to-Lab (B2B)</p>
        </a>
      </li>

      <li class="nav-header text-uppercase text-muted small font-weight-bold mt-2" style="font-size: 10px; letter-spacing: 0.08em; padding: 0.5rem 1rem;">Administration</li>

      <li class="nav-item">
        <a href="{{ route('labadmin.labusers') }}" class="nav-link {{ request()->routeIs('labadmin.labusers', 'labadmin.addlabuser') ? 'active' : '' }}">
          <i class="nav-icon fas fa-users-cog"></i>
          <p>Staff & Tech Users</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.lastdayreport') }}" class="nav-link {{ request()->routeIs('labadmin.lastdayreport') ? 'active' : '' }}">
          <i class="nav-icon fas fa-calendar-day"></i>
          <p>Daily Register Summary</p>
        </a>
      </li>

      <li class="nav-item">
        <a href="{{ route('labadmin.settings') }}" class="nav-link {{ request()->routeIs('labadmin.settings') ? 'active' : '' }}">
          <i class="nav-icon fas fa-sliders-h"></i>
          <p>Branding & Lab Settings</p>
        </a>
      </li>
    </ul>
  </nav>
</div>
