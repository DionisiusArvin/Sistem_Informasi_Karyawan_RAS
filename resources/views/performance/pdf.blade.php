<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report KPI Karyawan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h2 {
            margin-bottom: 2px;
        }

        .printed {
            font-size: 10px;
            color: #666;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px;
        }

        th {
            background-color: #e5e7eb;
            text-align: center;
            font-weight: bold;
        }

        .center { text-align: center; }
        .bold { font-weight: bold; }

        .status-box {
            text-align: center;
            font-weight: bold;
            padding: 4px;
        }
    </style>
</head>
<body>

<h2>Report KPI Karyawan</h2>
<div class="printed">Dicetak pada: {{ $printedAt }}</div>

<table>
    <thead>
        <tr>
            <th width="10%">Rank</th>
            <th width="30%">Nama</th>
            <th width="15%">Total Tugas</th>
            <th width="15%">Final KPI</th>
            <th width="30%">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($results as $row)
        <tr>
            <td class="center bold">{{ $row->rank }}</td>
            <td>{{ $row->name }}</td>
            <td class="center">{{ $row->total_tasks }}</td>
            <td class="center bold">{{ $row->final_score }}</td>

            <td class="status-box">
                {{ $periodeLabel }} <br>
                <span style="font-size:10px; font-weight:bold;">
                    @if(str_contains($row->badge, 'Top Performer'))
                        Top Performer
                    @else
                        Needs Improvement
                    @endif
                </span>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
