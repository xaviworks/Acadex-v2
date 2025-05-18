@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <h1 class="h4 fw-bold mb-4">📈 Final Grades</h1>

    {{-- Subject Selection + Print Button --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <form method="GET" action="{{ route('instructor.final-grades.index') }}" class="d-flex align-items-center gap-2 flex-wrap">
            <label class="form-label fw-medium mb-0">Select Subject:</label>
            <select name="subject_id" class="form-select w-auto" onchange="this.form.submit()">
                <option value="">-- Choose Subject --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->subject_code }} - {{ $subject->subject_description }}
                    </option>
                @endforeach
            </select>
        </form>

        @if(!empty($finalData) && count($finalData) > 0)
            <button onclick="printTable()" class="btn btn-success">
                🖨️ Print Table
            </button>
        @endif
    </div>

    {{-- Generate Final Grades --}}
    @if(request('subject_id') && empty($finalData))
        <form method="POST" action="{{ route('instructor.final-grades.generate') }}" class="mb-4">
            @csrf
            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">
            <button type="submit" class="btn btn-success px-4 shadow-sm">
                🔄 Generate Final Grades
            </button>
        </form>
    @endif

    {{-- Final Grades Table --}}
    @if(!empty($finalData) && count($finalData) > 0)
        <div class="card shadow-sm border-0" id="print-area">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="text-start">Student Name</th>
                            <th>Prelim</th>
                            <th>Midterm</th>
                            <th>Prefinal</th>
                            <th>Final</th>
                            <th class="text-primary">Final Average</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($finalData as $data)
                            <tr class="hover-shadow-sm">
                                <td class="fw-semibold text-start">
                                    {{ $data['student']->first_name }} {{ $data['student']->last_name }}
                                </td>
                                <td class="text-center">{{ isset($data['prelim']) ? (int) round($data['prelim']) : '–' }}</td>
                                <td class="text-center">{{ isset($data['midterm']) ? (int) round($data['midterm']) : '–' }}</td>
                                <td class="text-center">{{ isset($data['prefinal']) ? (int) round($data['prefinal']) : '–' }}</td>
                                <td class="text-center">{{ isset($data['final']) ? (int) round($data['final']) : '–' }}</td>
                                <td class="text-center fw-bold text-success">
                                    {{ isset($data['final_average']) ? (int) round($data['final_average']) : '–' }}
                                </td>
                                <td class="text-center">
                                    @if(isset($data['remarks']))
                                        @if(strtolower($data['remarks']) === 'passed')
                                            <span class="badge bg-success px-3 py-1">Passed</span>
                                        @elseif(strtolower($data['remarks']) === 'failed')
                                            <span class="badge bg-danger px-3 py-1">Failed</span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-1">{{ $data['remarks'] }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">–</span>
                                    @endif
                                </td>                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        @if(request('subject_id'))
            <div class="alert alert-warning text-center mt-5 rounded-3">
                No students or grades found for the selected subject.
            </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
    function printTable() {
        const content = document.getElementById('print-area').innerHTML;
        const subject = document.querySelector("select[name='subject_id']").selectedOptions[0].text;
        const bannerUrl = "{{ asset('images/banner-header.png') }}";
        
        // Count passed and failed students
        const passedStudents = document.querySelectorAll('.badge.bg-success').length;
        const failedStudents = document.querySelectorAll('.badge.bg-danger').length;
        const totalStudents = passedStudents + failedStudents;
        const passRate = totalStudents > 0 ? ((passedStudents / totalStudents) * 100).toFixed(1) : 0;

        // Get current academic period
        @php
            $activePeriod = \App\Models\AcademicPeriod::find(session('active_academic_period_id'));
            $semesterLabel = '';
            if($activePeriod) {
                switch ($activePeriod->semester) {
                    case '1st':
                        $semesterLabel = 'First';
                        break;
                    case '2nd':
                        $semesterLabel = 'Second';
                        break;
                    case 'Summer':
                        $semesterLabel = 'Summer';
                        break;
                }
            }
        @endphp
        const academicPeriod = "{{ $activePeriod ? $activePeriod->academic_year : '' }}";
        const semester = "{{ $semesterLabel }}";
        const currentDate = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    
        const printWindow = window.open('', '', 'width=900,height=650');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Grade Report - ${subject}</title>
                    <style>
                        @media print {
                            @page {
                                size: portrait;
                                margin: 0.5in;
                            }
                        }
                        
                        body {
                            font-family: 'Arial', sans-serif;
                            margin: 0;
                            padding: 20px;
                            color: #333;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                            line-height: 1.6;
                        }

                        .banner {
                            width: 100%;
                            max-height: 130px;
                            object-fit: contain;
                            margin-bottom: 15px;
                        }

                        .header-content {
                            margin-bottom: 20px;
                        }

                        .report-title {
                            font-size: 22px;
                            font-weight: bold;
                            text-align: center;
                            margin: 15px 0;
                            text-transform: uppercase;
                            letter-spacing: 2px;
                            color: #2c3e50;
                            border-bottom: 2px solid #2c3e50;
                            padding-bottom: 8px;
                        }

                        .metadata {
                            text-align: right;
                            font-size: 12px;
                            color: #666;
                            margin-bottom: 20px;
                        }

                        .header-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 25px;
                            background-color: #fff;
                        }

                        .header-table td {
                            padding: 10px 15px;
                            border: 1px solid #dee2e6;
                        }

                        .header-label {
                            font-weight: bold;
                            width: 150px;
                            background-color: #f8f9fa;
                            color: #2c3e50;
                        }

                        .header-value {
                            font-family: 'Arial', sans-serif;
                        }

                        .stats-container {
                            background-color: #f8f9fa;
                            border: 1px solid #dee2e6;
                            border-radius: 4px;
                            margin-bottom: 20px;
                            padding: 10px;
                            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                        }

                        .stats-title {
                            font-weight: 600;
                            text-transform: uppercase;
                            margin-bottom: 8px;
                            font-size: 11px;
                            color: #2c3e50;
                            border-bottom: 1px solid #dee2e6;
                            padding-bottom: 4px;
                        }

                        .stats-grid {
                            display: grid;
                            grid-template-columns: repeat(3, 1fr);
                            gap: 10px;
                        }

                        .stat-item {
                            background-color: #fff;
                            padding: 8px;
                            border-radius: 3px;
                            border: 1px solid #dee2e6;
                            text-align: center;
                        }

                        .stat-label {
                            font-size: 10px;
                            color: #666;
                            margin-bottom: 2px;
                            letter-spacing: 0.5px;
                        }

                        .stat-value {
                            font-size: 14px;
                            font-weight: bold;
                        }

                        .passed-count { color: #28a745; }
                        .failed-count { color: #dc3545; }
                        .total-count { color: #17a2b8; }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                            border: 2px solid #dee2e6;
                            background-color: #fff;
                            margin-top: 20px;
                        }

                        th, td {
                            border: 1px solid #dee2e6;
                            padding: 12px;
                            font-size: 12px;
                        }

                        th {
                            background-color: #f8f9fa;
                            font-weight: bold;
                            text-transform: uppercase;
                            text-align: center;
                            color: #2c3e50;
                        }

                        tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }

                        tr:hover {
                            background-color: #f2f2f2;
                        }

                        td {
                            text-align: center;
                        }

                        td:first-child {
                            text-align: left;
                            font-weight: 500;
                        }

                        .text-success {
                            color: #28a745;
                            font-weight: bold;
                        }

                        .badge {
                            padding: 5px 10px;
                            border-radius: 4px;
                            font-size: 11px;
                            font-weight: bold;
                            text-transform: uppercase;
                            letter-spacing: 0.5px;
                        }

                        .bg-success {
                            background-color: #d4edda;
                            color: #155724;
                            border: 1px solid #c3e6cb;
                        }

                        .bg-danger {
                            background-color: #f8d7da;
                            color: #721c24;
                            border: 1px solid #f5c6cb;
                        }

                        .footer {
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 1px solid #dee2e6;
                            font-size: 12px;
                            color: #666;
                            text-align: center;
                        }
                    </style>
                </head>
                <body>
                    <img src="${bannerUrl}" alt="Banner Header" class="banner">
                    
                    <div class="header-content">
                        <div class="report-title">Report of Grades</div>
                        
                        <table class="header-table">
                            <tr>
                                <td class="header-label">Course Code:</td>
                                <td class="header-value">${subject.split(' - ')[0]}</td>
                                <td class="header-label">Units:</td>
                                <td class="header-value">{{ $subjects->first()->units ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="header-label">Description:</td>
                                <td class="header-value">${subject.split(' - ')[1]}</td>
                                <td class="header-label">Semester:</td>
                                <td class="header-value">${semester}</td>
                            </tr>
                            <tr>
                                <td class="header-label">Course/Section:</td>
                                <td class="header-value">{{ $subjects->first()->course->course_code ?? 'N/A' }}</td>
                                <td class="header-label">School Year:</td>
                                <td class="header-value">${academicPeriod}</td>
                            </tr>
                        </table>

                        <div class="stats-container">
                            <div class="stats-title">Class Performance Summary</div>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-label">PASSED STUDENTS</div>
                                    <div class="stat-value passed-count">${passedStudents}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">FAILED STUDENTS</div>
                                    <div class="stat-value failed-count">${failedStudents}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">PASSING RATE</div>
                                    <div class="stat-value total-count">${passRate}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${content}

                    <div class="footer">
                        This is a computer-generated document. No signature is required.
                        <br>
                        Printed via ACADEX - Academic Grade System
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        
        // Wait for resources to load then print
        setTimeout(() => {
            printWindow.print();
        }, 500);
    }
</script>
@endpush
