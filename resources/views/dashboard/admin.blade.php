@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container py-5">
        <h2 class="mb-4 fw-bold text-dark">📊 Admin Dashboard Overview</h2>

        {{-- Summary Cards --}}
        <div class="row g-4">
            @php
                $cards = [
                    ['label' => 'Total Users', 'icon' => '👥', 'value' => $totalUsers, 'color' => 'text-primary'],
                    ['label' => 'Successful Logins Today', 'icon' => '✅', 'value' => $loginCount, 'color' => 'text-success'],
                    ['label' => 'Login Attempts Today', 'icon' => '❌', 'value' => $failedLoginCount, 'color' => 'text-danger'],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 rounded-4 p-3 h-100 bg-white animate__animated animate__fadeInUp">
                        <div class="d-flex align-items-center">
                            <span class="me-2 fs-4">{{ $card['icon'] }}</span>
                            <div>
                                <h6 class="text-muted mb-0">{{ $card['label'] }}</h6>
                                <h3 class="fw-bold {{ $card['color'] }} mt-1">{{ $card['value'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Mock Chart Section --}}
        <div class="mt-5">
            <h4 class="mb-3">📈 Login Trend ({{ $selectedDate }})</h4>
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <canvas id="LoginChart" height="100"></canvas>
            </div>
        </div>

        {{-- Date Input Form --}}
        <div class="mt-4 mb-5">
            <form action="{{ url()->current() }}" method="GET" class="d-flex justify-content-center">
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text">📅</span>
                    <input type="date" class="form-control" name="date" value="{{ $selectedDate }}">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
        
    </div>
</div>
@endsection

@push('scripts')
<script>
    const successfulData = @json($successfulData);
    const failedData = @json($failedData);

    const ctx = document.getElementById('LoginChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['12 AM', '1 AM', '2 AM', '3 AM', '4 AM', '5 AM', '6 AM', '7 AM', '8 AM', '9 AM', '10 AM', '11 AM', '12 PM', '1 PM', '2 PM', '3 PM', '4 PM', '5 PM', '6 PM', '7 PM', '8 PM', '9 PM', '10 PM', '11 PM'],
            datasets: [
                {
                    label: 'Successful Logins',
                    data: successfulData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Failed Logins',
                    data: failedData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush


