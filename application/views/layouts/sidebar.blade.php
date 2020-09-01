<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  {{-- <li class="nav-item has-treeview menu-open">
    <a href="{{ base_url('assets/template/#') }}" class="nav-link active">
      <i class="nav-icon fas fa-tachometer-alt"></i>
      <p>
        Dashboard
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="{{ base_url('assets/template/./index.html') }}" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Dashboard v1</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ base_url('assets/template/./index2.html') }}" class="nav-link active">
          <i class="far fa-circle nav-icon"></i>
          <p>Dashboard v2</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ base_url('assets/template/./index3.html') }}" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Dashboard v3</p>
        </a>
      </li>
    </ul>
  </li> --}}
  <li class="nav-item">
    <a href="{{ base_url('assets/template/pages/widgets.html') }}" class="nav-link active">
      <i class="nav-icon fas fa-tachometer-alt"></i>
      <p>
        Dashboard
      </p>
    </a>
  </li>
  @php
    echo '<pre>';
    die(var_dump($account));
  @endphp
  @if ($account['role_id'] == 1)
  <li class="nav-item">
    <a href="{{ base_url('pengguna') }}" class="nav-link">
      <i class="nav-icon fas fa-th"></i>
      <p>
        Pengguna
      </p>
    </a>
  </li>
  @endif
  <li class="nav-item">
    <a href="{{ base_url('client') }}" class="nav-link">
      <i class="nav-icon fas fa-th"></i>
      <p>
        Client
      </p>
    </a>
  </li>
  <li class="nav-item">
    <a href="{{ base_url('transaksi') }}" class="nav-link">
      <i class="nav-icon fas fa-th"></i>
      <p>
        Transaksi
      </p>
    </a>
  </li>
</ul>