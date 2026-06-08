<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Barangay Information System' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --success: #48bb78;
            --danger: #f56565;
            --warning: #ed8936;
            --info: #4299e1;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f7fafc; }

        /* Sidebar */
        .sidebar { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); min-height: 100vh; color: white; padding: 0; width: 250px; flex-shrink: 0; }
        .sidebar .logo { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar .nav-section { padding: .5rem 1.5rem .2rem; font-size:10px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; color:rgba(255,255,255,.45); margin-top:.5rem; }
        .sidebar .nav-link { color: rgba(255,255,255,.8); padding: .65rem 1.5rem; border-left: 3px solid transparent; transition: all .2s ease; text-decoration: none; display: flex; align-items: center; gap: .5rem; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background-color: rgba(255,255,255,.12); border-left-color: white; }
        .sidebar .nav-link .badge { margin-left: auto; }

        /* Topbar */
        .topbar { background: white; box-shadow: 0 2px 4px rgba(0,0,0,.08); padding: .85rem 2rem; display: flex; justify-content: space-between; align-items: center; }
        .main-content { padding: 1.75rem 2rem; }

        /* Cards */
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.09); border-radius: .6rem; margin-bottom: 1.25rem; }
        .card-header { background: white; border-bottom: 1px solid #f1f5f9; }

        /* Buttons */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--secondary); border-color: var(--secondary); }

        /* Badges */
        .badge { padding: .45rem .7rem; font-weight: 500; }

        /* Status dot helper */
        .status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }

        @stack('styles')
    </style>
</head>
<body>
<div class="d-flex">
    @auth
    <nav class="sidebar d-flex flex-column">
        <div class="logo">
            <h5 class="mb-0 fw-bold"><i class="bi bi-building"></i> BMIS</h5>
            <small style="color:rgba(255,255,255,.6); font-size:11px;">Barangay Management System</small>
        </div>

        <ul class="nav flex-column flex-grow-1 py-2">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   href="{{ route('dashboard') }}">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>

            @if(auth()->user()->isResident())
                {{-- ──── Resident Nav ──── --}}
                <li><div class="nav-section">Self-Service</div></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clearances.*') ? 'active' : '' }}"
                       href="{{ route('clearances.index') }}">
                        <i class="bi bi-file-text"></i> My Clearances
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('resident.blotters.*') ? 'active' : '' }}"
                       href="{{ route('resident.blotters.index') }}">
                        <i class="bi bi-journal-text"></i> My Blotters
                    </a>
                </li>
                <li><div class="nav-section">Information</div></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}"
                       href="{{ route('announcements.index') }}">
                        <i class="bi bi-megaphone"></i> Announcements
                    </a>
                </li>
            @endif

            @if(auth()->user()->isAdmin())
                {{-- ──── Admin Nav ──── --}}
                <li><div class="nav-section">Account Management</div></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.accounts.*') ? 'active' : '' }}"
                       href="{{ route('admin.accounts.index') }}">
                        <i class="bi bi-person-check"></i> Account Approvals
                        @php $pending = \App\Models\User::where('status','pending_verification')->where('role','resident')->count(); @endphp
                        @if($pending > 0)
                            <span class="badge bg-warning text-dark">{{ $pending }}</span>
                        @endif
                    </a>
                </li>

                <li><div class="nav-section">Census & Records</div></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('census.*') ? 'active' : '' }}"
                       href="{{ route('census.index') }}">
                        <i class="bi bi-clipboard2-data"></i> Census Intake
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('residents.*') ? 'active' : '' }}"
                       href="{{ route('residents.index') }}">
                        <i class="bi bi-people"></i> Residents
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('households.*') ? 'active' : '' }}"
                       href="{{ route('households.index') }}">
                        <i class="bi bi-houses"></i> Households
                    </a>
                </li>

                <li><div class="nav-section">Requests</div></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('clearances.admin') ? 'active' : '' }}"
                       href="{{ route('clearances.admin') }}">
                        <i class="bi bi-file-check"></i> Clearances
                        @php $pendingClearances = \App\Models\Clearance::where('status','pending')->count(); @endphp
                        @if($pendingClearances > 0)
                            <span class="badge bg-danger">{{ $pendingClearances }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('blotters.*') ? 'active' : '' }}"
                       href="{{ route('blotters.index') }}">
                        <i class="bi bi-journal"></i> Blotters
                        @php $pendingBlotters = \App\Models\Blotter::where('status','pending_review')->count(); @endphp
                        @if($pendingBlotters > 0)
                            <span class="badge bg-danger">{{ $pendingBlotters }}</span>
                        @endif
                    </a>
                </li>

                <li><div class="nav-section">Information & Reports</div></li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                       href="{{ route('reports.index') }}">
                        <i class="bi bi-bar-chart"></i> Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('announcements.*') ? 'active' : '' }}"
                       href="{{ route('announcements.index') }}">
                        <i class="bi bi-megaphone"></i> Announcements
                    </a>
                </li>
            @endif
        </ul>

        {{-- Sign Out only --}}
        <div style="border-top:1px solid rgba(255,255,255,.15); padding:.75rem 1.25rem;">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link text-white w-100 text-start p-0"
                        style="font-size:13px;opacity:.8;">
                    <i class="bi bi-box-arrow-right"></i> Sign Out
                </button>
            </form>
        </div>
    </nav>
    @endauth

    <div class="flex-grow-1" style="min-width:0;">
        {{-- Topbar --}}
        <div class="topbar">
            <h6 class="mb-0 fw-bold" style="color:#1e293b;">{{ $title ?? 'Barangay Information System' }}</h6>
            @auth
            <div class="d-flex align-items-center gap-3">
                {{-- Status badge for residents --}}
                @if(auth()->user()->isResident())
                    @php $status = auth()->user()->status; @endphp
                    <span class="badge {{ $status === 'active' ? 'bg-success' : ($status === 'pending_verification' ? 'bg-warning text-dark' : 'bg-danger') }}">
                        {{ str_replace('_',' ', ucfirst($status)) }}
                    </span>
                @endif
                {{-- User avatar + name + role --}}
                <div class="d-flex align-items-center gap-2">
                    <div style="width:34px;height:34px;border-radius:50%;
                                background:linear-gradient(135deg,#667eea,#764ba2);
                                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-person-fill" style="color:#fff;font-size:15px;"></i>
                    </div>
                    <div style="line-height:1.2;">
                        <div style="font-size:13px;font-weight:700;color:#0f172a;">{{ auth()->user()->name }}</div>
                        <div style="font-size:11px;color:#64748b;">{{ ucfirst(auth()->user()->role) }}</div>
                    </div>
                </div>
            </div>
            @endauth
        </div>

        {{-- Main content --}}
        <div class="main-content">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
