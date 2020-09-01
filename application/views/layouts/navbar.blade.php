<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

   <li class="nav-item">
    <a href="{{ base_url('/') }}" class="nav-link">
      <i class="nav-icon fas fa-circle"></i>
      <p>
        Dashboard
      </p>
    </a>
  </li>

  <li class="">

  </li>
  @if ($account['role_id'] == 1)
  <li class="nav-item">
    <a href="{{ base_url('pengguna') }}" class="nav-link">
      <i class="nav-icon fas fa-circle"></i>
      <p>
        Pengguna
      </p>
    </a>
  </li>
  <li class="nav-item">
    <a href="{{ base_url('client') }}" class="nav-link">
      <i class="nav-icon fas fa-circle"></i>
      <p>
        Client
      </p>
    </a>
  </li>
  <li class="nav-item">
    <a href="{{ base_url('transaksi') }}" class="nav-link">
      <i class="nav-icon fas fa-circle"></i>
      <p>
        Transaksi
      </p>
    </a>
  </li>
  

  @elseif ($account['role_id'] == 3) 
  <li class="nav-item">
    <a href="{{ base_url('client-transaksi') }}" class="nav-link">
      <i class="nav-icon fas fa-circle"></i>
      <p>
        Transaksi Ku
      </p>
    </a>
  </li>
  <li class="nav-item">
    <a href="{{ base_url('client-profile') }}" class="nav-link">
      <i class="nav-icon fas fa-circle"></i>
      <p>
        Profile Ku
      </p>
    </a>
  </li>

  @endif
</ul>
</nav>