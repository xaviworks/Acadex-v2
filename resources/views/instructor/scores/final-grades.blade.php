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
    
        const printWindow = window.open('', '', 'width=900,height=650');
        printWindow.document.write(`
            <html>
                <head>
                    <style>
                        body {
                            font-family: 'Segoe UI', sans-serif;
                            margin: 40px;
                            color: #000;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        .banner {
                            width: 100%;
                            height: auto;
                            margin-bottom: 20px;
                        }
                        .header-content {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        h2 {
                            margin: 0 0 10px 0;
                        }
                        p {
                            margin: 0 0 15px 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 10px;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 8px 12px;
                            text-align: center;
                            font-size: 14px;
                        }
                        th {
                            background-color: #f0f0f0;
                        }
                        .text-start {
                            text-align: left;
                        }
                        .text-success {
                            color: green;
                        }
                    </style>
                </head>
                <body>
                    <img src="${bannerUrl}" alt="Banner Header" class="banner" onload="window.print(); window.close();">
                    <div class="header-content">
                    <h2>Final Grades Report</h2>
                    <p><strong>Subject:</strong> ${subject}</p>
                    </div>
                    ${content}
                </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endpush
