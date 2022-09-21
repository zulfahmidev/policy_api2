@php
// Create Image Blob
@endphp

<div class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
            <img src="{{ asset('dist/img/boxed-bg.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
            <div style="text-transform: capitalize" class="text-white">{{ auth()->user()->username }}</div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            

            @foreach (getSidebarMenu() as $item)
                @if (hasPermissionByName($item['permission']))
                    <li class="nav-item">
                        <a href="{{ route($item['route']) }}" class="nav-link @if (Route::current()->getName() == $item['route']) active @endif">
                            <i class="nav-icon fas fa-{{ $item['icon'] }}"></i>
                            <p>
                                {{ $item['name'] }}
                            </p>
                        </a>
                    </li>
                @endif

            @endforeach

            <li class="nav-header">LAINNYA</li>

            <li class="nav-item">
                <a href="{{ route('main.home') }}" class="nav-link">
                    <i class="nav-icon fas fa-home"></i>
                    <p>
                        HOME
                    </p>
                </a>
            </li>
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="btn btn-link text-left text-warning nav-link btn-secondary">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            KELUAR
                        </p>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->

</div>
