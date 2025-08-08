<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link " href="{{ route('admin.home') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <li class="nav-heading">Information Maktab</li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.sectors.index') }}">
          <i class="bi bi-map-fill"></i>
          <span>Sektor</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.coordinators.index') }}">
          <i class="bi bi-person"></i>
          <span>Coordinator</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.maktabs.index') }}">
          <i class="bi bi-pin-map-fill"></i>
          <span>Location Maktab</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.groups.index') }}">
          <i class="bi bi-bookmark-plus-fill"></i>
          <span>Assign Maktab</span>
        </a>
      </li>
    </ul>
  </aside>