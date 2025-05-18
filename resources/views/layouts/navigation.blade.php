<header class="px-4 py-3 shadow-sm d-flex justify-content-between align-items-center transition-all" style="background-color: var(--dark-green); color: white;">
    <!-- Left: Current Academic Period -->
    <div class="d-flex align-items-center">
        <h1 class="fs-5 fw-semibold mb-0 d-flex align-items-center">
            <i class="bi bi-calendar-event me-2"></i>
            @php
                $activePeriod = \App\Models\AcademicPeriod::find(session('active_academic_period_id'));
            @endphp
            @if($activePeriod)
                @php
                    $semesterLabel = '';
                    $academicYear = $activePeriod->academic_year;
        
                    switch ($activePeriod->semester) {
                        case '1st':
                            $semesterLabel = 'First Semester';
                            break;
                        case '2nd':
                            $semesterLabel = 'Second Semester';
                            break;
                        case 'Summer':
                            $semesterLabel = 'Summer';
                            break;
                        default:
                            $semesterLabel = 'Unknown Semester';
                            break;
                    }
        
                    if ($activePeriod->semester != 'Summer') {
                        list($startYear, $endYear) = explode('-', $academicYear);
                    }
                @endphp
                
                <span class="badge bg-success bg-opacity-25 px-3 py-2 rounded-pill">
                    @if($activePeriod->semester != 'Summer')
                        {{ $semesterLabel }} - AY {{ $startYear }} - {{ $endYear }}
                    @else
                        {{ $semesterLabel }} - AY {{ $academicYear }}
                    @endif
                </span>
            @else
                <span class="badge bg-success bg-opacity-25 px-3 py-2 rounded-pill">Dashboard</span>
            @endif
        </h1>    
    </div>

    <!-- Right: Profile Dropdown -->
    @php
        $nameParts = explode(' ', Auth::user()->name);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[count($nameParts) - 1] ?? '';
        $displayName = $firstName . ' ' . $lastName;
    @endphp
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle hover-lift" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="position-relative">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=259c59&color=fff"
                     alt="avatar"
                     class="rounded-circle me-2 border border-2 border-success"
                     width="38"
                     height="38">
                <span class="position-absolute bottom-0 end-0 bg-success rounded-circle border border-white" style="width: 10px; height: 10px;"></span>
            </div>
            <div class="d-flex flex-column ms-2">
                <span class="fw-medium">{{ $displayName }}</span>
                <small class="text-success">Online</small>
            </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg" style="min-width: 280px;" aria-labelledby="profileDropdown">
            <li class="px-3 py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($displayName) }}&background=259c59&color=fff"
                         alt="avatar"
                         class="rounded-circle me-3"
                         width="45"
                         height="45">
                    <div class="d-flex flex-column">
                        <span class="fw-semibold text-dark">{{ $displayName }}</span>
                        <span class="text-muted small text-truncate" style="max-width: 180px;">{{ Auth::user()->email }}</span>
                    </div>
                </div>
            </li>
            <li>
                <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('profile.edit') }}">
                    <i class="bi bi-person-gear me-2 text-muted"></i>
                    <span>Profile Settings</span>
                </a>
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item d-flex align-items-center py-2 px-3 text-danger" type="submit">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        <span>Sign Out</span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
