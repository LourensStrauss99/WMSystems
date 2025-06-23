{{-- filepath: resources/views/jobcards/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobcard {{ $jobcard->jobcard_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        
        /* Jobcard Info */
        .jobcard-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }
        
        .info-section {
            flex: 1;
        }
        
        .info-section h3 {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .info-row {
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            display: inline-block;
            width: 100px;
        }
        
        .info-value {
            color: #333;
        }
        
        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-assigned { background: #fff3cd; color: #856404; }
        .status-in-progress { background: #d1ecf1; color: #0c5460; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-invoiced { background: #cce7ff; color: #004085; }
        
        /* Work Details */
        .work-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        
        .work-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
        }
        
        .work-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .work-text {
            color: #333;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        
        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background: white;
        }
        
        .table th {
            background: #007bff;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .table tbody tr:hover {
            background: #e9ecef;
        }
        
        /* Summary */
        .summary-box {
            background: #e7f3ff;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .summary-title {
            font-size: 14px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #495057;
        }
        
        .summary-value {
            color: #333;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
        }
        
        /* No data message */
        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        /* Print styles */
        @media print {
            body { print-color-adjust: exact; }
            .container { padding: 10px; }
            .page-break { page-break-before: always; }
        }
        
        /* Responsive adjustments for PDF */
        .info-section {
            margin-right: 20px;
        }
        
        .info-section:last-child {
            margin-right: 0;
        }
        
        /* Two column layout for larger content */
        .two-column {
            display: flex;
            gap: 20px;
        }
        
        .column {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">WorkFlow Management System</div>
            <div class="document-title">JOBCARD</div>
        </div>

        <!-- Jobcard Information -->
        <div class="jobcard-info">
            <div class="info-section">
                <h3>Jobcard Details</h3>
                <div class="info-row">
                    <span class="info-label">Jobcard #:</span>
                    <span class="info-value">{{ $jobcard->jobcard_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($jobcard->job_date)->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="status-badge status-{{ str_replace(' ', '-', strtolower($jobcard->status)) }}">
                        {{ ucfirst($jobcard->status) }}
                    </span>
                </div>
                @if($jobcard->category)
                <div class="info-row">
                    <span class="info-label">Category:</span>
                    <span class="info-value">{{ $jobcard->category }}</span>
                </div>
                @endif
            </div>
            
            <div class="info-section">
                <h3>Client Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $jobcard->client->name }} {{ $jobcard->client->surname }}</span>
                </div>
                @if($jobcard->client->email)
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value">{{ $jobcard->client->email }}</span>
                </div>
                @endif
                @if($jobcard->client->telephone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $jobcard->client->telephone }}</span>
                </div>
                @endif
                @if($jobcard->client->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    <span class="info-value">{{ $jobcard->client->address }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Work Details -->
        <div class="work-section">
            <div class="section-title">Work Details</div>
            
            @if($jobcard->work_request)
            <div class="work-content">
                <div class="work-title">Work Request:</div>
                <div class="work-text">{{ $jobcard->work_request }}</div>
            </div>
            @endif
            
            @if($jobcard->special_request)
            <div class="work-content">
                <div class="work-title">Special Instructions:</div>
                <div class="work-text">{{ $jobcard->special_request }}</div>
            </div>
            @endif
            
            @if($jobcard->work_done)
            <div class="work-content">
                <div class="work-title">Work Completed:</div>
                <div class="work-text">{{ $jobcard->work_done }}</div>
            </div>
            @endif
        </div>

        <!-- Employees Section -->
        @if($jobcard->employees->count() > 0)
        <div class="work-section">
            <div class="section-title">Assigned Employees</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Employee Name</th>
                        <th>Hours Worked</th>
                        <th>Role/Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobcard->employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->pivot->hours_worked ?? 0 }} hours</td>
                        <td>{{ $employee->role ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Inventory Section -->
        @if($jobcard->inventory->count() > 0)
        <div class="work-section">
            <div class="section-title">Materials & Inventory Used</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Item Name</th>
                        <th>Description</th>
                        <th>Quantity Used</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobcard->inventory as $item)
                    <tr>
                        <td>{{ $item->short_code ?? 'N/A' }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->short_description ?? 'N/A' }}</td>
                        <td>{{ $item->pivot->quantity ?? 0 }}</td>
                        <td>{{ $item->unit ?? 'pcs' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary -->
        <div class="summary-box">
            <div class="summary-title">Jobcard Summary</div>
            <div class="summary-row">
                <span class="summary-label">Total Hours Worked:</span>
                <span class="summary-value">{{ $totalHours }} hours</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Inventory Items:</span>
                <span class="summary-value">{{ $totalInventoryItems }} items</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Employees Assigned:</span>
                <span class="summary-value">{{ $jobcard->employees->count() }} person(s)</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Current Status:</span>
                <span class="summary-value">{{ ucfirst($jobcard->status) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }}</p>
            <p>WorkFlow Management System - Jobcard Report</p>
        </div>
    </div>
</body>
</html>