<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\DailyTask;
use App\Models\TaskActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class DailyTaskController extends Controller
{
    /* ================= CREATE FORM ================= */
    public function create(Task $task)
    {
        return view('daily-tasks.create', [
            'task' => $task,
        ]);
    }

    /* ================= STORE ================= */
    public function store(Request $request, Task $task)
    {
        $dailyTaskOptionsByCategory = [
            'PBG' => [
                'Data Umum' => [
                    'Data Persetujuan Lingkungan (mengikuti peraturan perundangan yang berlaku)',
                    'Data Siteplan yang telah disetujui Pemerintah Daerah Setempat',
                    'Data Penyedia Jasa Perencana',
                    'Data Intensitas Bangunan (KKPR/KRK)',
                    'Data Identitas Pemilik Bangunan (KTP/KITAS)',
                ],
                'Data Teknis Arsitektur' => [
                    'Rekomendasi Peil Banjir',
                    'Spesifikasi Teknis Arsitektur Bangunan',
                    'Gambar Rencana Detail Bangunan',
                    'Gambar Rencana Tampak Bangunan',
                    'Gambar Rencana Potongan Bangunan',
                    'Gambar Rencana Denah Bangunan',
                    'Gambar Rencana Tapak Bangunan',
                    'Gambar Situasi',
                ],
                'Data Teknis Struktur' => [
                    'Spesifikasi Teknis Struktur Bangunan',
                    'Perhitungan Teknis Struktur',
                    'Gambar Rencana Dan Detail Teknis Tangga',
                    'Gambar Rencana Dan Detail Teknis Pelat Lantai',
                    'Gambar Rencana Dan Detail Teknis Penutup',
                    'Gambar Rencana Dan Detail Teknis Rangka Atap',
                    'Gambar Rencana Dan Detail Teknis Balok',
                    'Gambar Rencana Dan Detail Teknis Kolom',
                    'Gambar Rencana Dan Detail Teknis Fondasi dan sloof',
                ],
                'Data Teknis MEP' => [
                    'Spesifikasi Teknis Mekanikal, Elektrikal, dan Plambing',
                    'Perhitungan Teknis Mekanikal, Elektrikal, dan Plambing',
                    'Gambar Rencana Dan Detail Pengelolaan Air Limbah',
                    'Gambar Rencana Dan Detail Pengelolaan Air Bersih',
                    'Gambar Rencana Dan Detail Pencahayaan Umum, dan Pencahanyaan Khusus',
                    'Gambar Rencana Dan Detail Sumber Listrik, dan Jaringan Listrik',
                ],
                'Data Tambahan' => [
                    'Gambar Sederhana Batas Tanah',
                    'Hasil Penyelidikan Tanah',
                    'Peil Banjir',
                ],
                'Upload' => [
                    'Upload semua dokumen ke sistem',
                ],
            ],
            'SLF' => [
                'Data Umum' => [
                    'Data Penyedia Jasa Pengkaji Teknis',
                    'Laporan Pemeriksaan Kelaikan Fungsi Bangunan',
                    'Surat Pernyataan Kelaikan Fungsi',
                    'Data Intensitas Bangunan (KKPR/KRK)',
                    'Data Identitas Pemilik Bangunan (KTP/KITAS)',
                ],
                'Data Teknis Arsitektur' => [
                    'Rekomendasi Peil Banjir',
                    'Gambar Detail Bangunan',
                    'Gambar Tampak Bangunan',
                    'Gambar Potongan Bangunan',
                    'Gambar Denah Bangunan',
                    'Gambar Tapak Bangunan',
                    'Spesifikasi Teknis Arsitektur Bangunan',
                    'Gambar Situasi',
                ],
                'Data Teknis Struktur' => [
                    'Gambar Dan Detail Teknis Penutup',
                    'Gambar Dan Detail Teknis Rangka Atap',
                    'Gambar Dan Detail Teknis Balok',
                    'Gambar Dan Detail Teknis Kolom',
                    'Gambar Dan Detail Teknis Fondasi dan sloof',
                    'Spesifikasi Teknis Struktur Bangunan',
                    'Perhitungan Teknis Struktur',
                ],
                'Data Teknis MEP' => [
                    'Gambar Dan Detail Pengelolaan Air Limbah',
                    'Gambar Dan Detail Pengelolaan Air Bersih',
                    'Gambar Dan Detail Pencahayaan Umum, dan Pencahanyaan Khusus',
                    'Gambar Dan Detail Sumber Listrik, dan Jaringan Listrik',
                    'Spesifikasi Teknis Mekanikal, Elektrikal, dan Plambing',
                    'Perhitungan Teknis Mekanikal, Elektrikal, dan Plambing',
                ],
                'Upload' => [
                    'Upload semua dokumen ke sistem',
                ],
            ],
            'PERENCANAAN' => [
                'Paving' => [
                    'Survey' => [
                        'Survey Lapangan',
                    ],
                    'Gambar Kerja' => [
                        'Layout Eksisting',
                        'Detail Eksisting',
                        'Layout Rencana',
                        'Detail Rencana',
                        'Detail Potongan Rencana',
                    ],
                    'Engineering Estimate' => [
                        'Pembuatan Item RAB',
                        'Perhitungan Volume',
                        'Pembuatan Analisa',
                        'Penentuan Harga Bahan',
                        'Setting RAB sesuai Pagu',
                        'Time Schedule',
                    ],
                    'BOQ' => [
                        'Setting BOQ dari Engineering Estimate',
                    ],
                    'Rencana Kerja dan Syarat2 Teknis' => [
                        'Pembuatan Check List RKS',
                        'Pembuatan RKS dari database yang ada',
                        'Pembuatan RKS baru',
                        'Spesifikasi Teknis',
                    ],
                    'Dokumen Teknis' => [
                        'Rencana Kerja & Syarat-syarat Teknis',
                        'Spesifikasi Teknis',
                        'Metodologi Pelaksanaan Pekerjaan',
                        'Pembuatan SMKK Konsultan',
                        'Pembuatan SMKK Kontaktor',
                    ],
                    'Harga Perkiraan Sendiri' => [
                        'Pembuatan HPS',
                    ],
                    'Laporan' => [
                        'Pembuatan Laporan Pendahuluan',
                        'Pembuatan Laporan Prarencana',
                        'Pembuatan Laporan Antara',
                        'Pembuatan Laporan Akhir',
                    ],
                    'Finalisasi Dokumen Perencanaan' => [
                        'Print dokumen gambar',
                        'Print dokumen EE',
                    ],
                ],
            ],
        ];
        $templateCategory = $task->project->category ?? null;
        if ($templateCategory === 'PERENCANAAN' && ($task->jenis_tugas ?? null) === 'Paving') {
            $taskName = trim((string) ($task->name ?? ''));
            $pavingMainTasks = [
                'Survey',
                'Gambar Kerja',
                'Engineering Estimate',
                'BOQ',
                'Rencana Kerja dan Syarat2 Teknis',
                'Dokumen Teknis',
                'Harga Perkiraan Sendiri',
                'Laporan',
                'Finalisasi Dokumen Perencanaan',
            ];

            $baseTaskName = $taskName;
            $sortedBases = collect($pavingMainTasks)
                ->filter()
                ->sortByDesc(fn ($item) => strlen($item))
                ->values();

            foreach ($sortedBases as $base) {
                if (Str::startsWith(Str::lower($taskName), Str::lower($base))) {
                    $baseTaskName = $base;
                    break;
                }
            }

            if (strpos($taskName, ' - ') !== false) {
                $baseTaskName = explode(' - ', $taskName, 2)[0];
            }

            $dailyTaskOptions = $dailyTaskOptionsByCategory['PERENCANAAN']['Paving'][$baseTaskName] ?? [];
        } else {
            $dailyTaskOptions = $dailyTaskOptionsByCategory[$templateCategory][$task->jenis_tugas] ?? [];
        }
        $showTemplate = !empty($dailyTaskOptions);

        if ($showTemplate) {
            $manualName = trim((string) $request->input('manual_name', ''));
            $selectedName = trim((string) $request->input('name', ''));
            $flatOptions = array_is_list($dailyTaskOptions)
                ? $dailyTaskOptions
                : collect($dailyTaskOptions)->flatten(1)->values()->all();

            $nameRule = $flatOptions
                ? 'nullable|string|in:' . implode(',', $flatOptions)
                : 'nullable|string|max:255';

            $validated = $request->validate([
                'name' => $nameRule,
                'manual_name' => 'nullable|string|max:255',
                'due_date' => 'required|date',
                'description' => 'nullable|string',
                'weight' => 'required|integer|min:1|max:10',
            ]);

            if ($selectedName === '' && $manualName === '') {
                return back()->withErrors([
                    'name' => 'Pilih tugas harian atau isi judul manual.'
                ])->withInput();
            }

            if ($selectedName !== '' && $manualName !== '') {
                $name = trim($selectedName . ' ' . $manualName);
            } elseif ($selectedName !== '') {
                $name = $selectedName;
            } else {
                $name = $manualName;
            }

            $projectItemId = null;
        } else {
            $validated = $request->validate([
                'project_item_id' => 'required|exists:project_items,id',
                'due_date' => 'required|date',
                'description' => 'nullable|string',
                'weight' => 'required|integer|min:1|max:10',
            ]);

            $projectItemId = $validated['project_item_id'];
            $name = \App\Models\ProjectItem::find($validated['project_item_id'])->name;
        }

        DailyTask::create([
            'task_id' => $task->id,
            'project_id' => $task->project_id,
            'project_item_id' => $projectItemId,
            'name' => $name,
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? null,
            'weight' => $validated['weight'],
            'status' => 'Belum Dikerjakan',
            'progress' => 0,
        ]);

        return back()->with('success', 'Daily Task berhasil dibuat.');
    }

    /* ================= UPLOAD FORM ================= */
    public function uploadForm(DailyTask $dailyTask)
    {
        return view('daily-tasks.upload', compact('dailyTask'));
    }

    /* ================= HANDLE UPLOAD ================= */
    public function handleUpload(Request $request, DailyTask $dailyTask)
    {
        $request->validate([
            'file' => 'nullable|file|max:10240',
            'link_url' => 'nullable|url',
            'notes' => 'nullable|string',
            'progress_percent' => 'required|numeric|min:0|max:100',
        ]);

        if (!$request->file && !$request->link_url) {
            return back()->withErrors([
                'file' => 'Isi minimal file atau link.'
            ]);
        }

        $requiresDailyProgress = Carbon::parse($dailyTask->due_date)
            ->startOfDay()
            ->gt(now()->startOfDay()->addDay());

        if ($requiresDailyProgress) {
            $alreadySubmittedToday = $dailyTask->activities()
                ->where('user_id', auth()->id())
                ->where('activity_type', 'upload_pekerjaan')
                ->whereDate('created_at', now()->toDateString())
                ->exists();

            if ($alreadySubmittedToday) {
                return back()->withErrors([
                    'progress_percent' => 'Progres harian untuk tugas ini sudah dikirim hari ini.'
                ]);
            }
        }

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('task_uploads', 'public');
        }

        // 🔥 SIMPAN AKTIVITAS
        \App\Models\TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id'       => auth()->id(),
            'activity_type' => 'upload_pekerjaan',
            'file_path'     => $filePath,
            'link_url'      => $request->link_url,
            'notes'         => $request->notes,
            'progress_percent' => $request->progress_percent,
        ]);

        // 🔥 INI YANG SELAMA INI HILANG
        $dailyTask->update([
            'status' => 'Menunggu Validasi'
        ]);

        return redirect()
            ->route('division-tasks.index')
            ->with('success', 'Pekerjaan berhasil diupload, menunggu validasi.');
    }


    /* ================= APPROVE ================= */
    public function approve(DailyTask $dailyTask)
    {
        if (!Gate::allows('validate-task', $dailyTask)) {
            abort(403);
        }

        $completionStatus = Carbon::now()->startOfDay()
            ->lte(Carbon::parse($dailyTask->due_date))
            ? 'tepat_waktu'
            : 'terlambat';

        $dailyTask->update([
            'status' => 'Selesai',
            'completion_status' => $completionStatus,
            'progress' => 100,
        ]);

        return back()->with('success', 'Pekerjaan disetujui.');
    }

    /* ================= REJECT ================= */
    public function reject(Request $request, DailyTask $dailyTask)
    {
        if (!Gate::allows('validate-task', $dailyTask)) {
            abort(403);
        }

        TaskActivity::create([
            'daily_task_id' => $dailyTask->id,
            'user_id' => Auth::id(),
            'activity_type' => 'permintaan_revisi',
            'notes' => $request->input('revision_notes', 'Revisi diperlukan.'),
        ]);

        $dailyTask->update([
            'status' => 'Revisi'
        ]);

        return back()->with('success', 'Tugas dikembalikan untuk revisi.');
    }

    /* ================= DELETE ================= */
    public function destroy(DailyTask $dailyTask)
    {
        if (Auth::user()->role !== 'kepala_divisi') {
            abort(403);
        }

        $dailyTask->delete();

        return back()->with('success', 'Tugas harian dihapus.');
    }

    /* ================= DOWNLOAD ================= */
    public function download(DailyTask $dailyTask)
    {
        $lastUpload = $dailyTask->activities()
            ->where('activity_type', 'upload_pekerjaan')
            ->latest()
            ->first();

        if (!$lastUpload || !$lastUpload->file_path) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download(
            storage_path('app/public/' . $lastUpload->file_path)
        );
    }
    /* ================= UPDATE ================= */
    public function update(Request $request, DailyTask $dailyTask)
    {
    if (auth()->user()->role !== 'kepala_divisi') {
        abort(403);
    }

    $dailyTaskOptionsByCategory = [
        'PBG' => [
            'Data Umum' => [
                'Data Persetujuan Lingkungan (mengikuti peraturan perundangan yang berlaku)',
                'Data Siteplan yang telah disetujui Pemerintah Daerah Setempat',
                'Data Penyedia Jasa Perencana',
                'Data Intensitas Bangunan (KKPR/KRK)',
                'Data Identitas Pemilik Bangunan (KTP/KITAS)',
            ],
            'Data Teknis Arsitektur' => [
                'Rekomendasi Peil Banjir',
                'Spesifikasi Teknis Arsitektur Bangunan',
                'Gambar Rencana Detail Bangunan',
                'Gambar Rencana Tampak Bangunan',
                'Gambar Rencana Potongan Bangunan',
                'Gambar Rencana Denah Bangunan',
                'Gambar Rencana Tapak Bangunan',
                'Gambar Situasi',
            ],
            'Data Teknis Struktur' => [
                'Spesifikasi Teknis Struktur Bangunan',
                'Perhitungan Teknis Struktur',
                'Gambar Rencana Dan Detail Teknis Tangga',
                'Gambar Rencana Dan Detail Teknis Pelat Lantai',
                'Gambar Rencana Dan Detail Teknis Penutup',
                'Gambar Rencana Dan Detail Teknis Rangka Atap',
                'Gambar Rencana Dan Detail Teknis Balok',
                'Gambar Rencana Dan Detail Teknis Kolom',
                'Gambar Rencana Dan Detail Teknis Fondasi dan sloof',
            ],
            'Data Teknis MEP' => [
                'Spesifikasi Teknis Mekanikal, Elektrikal, dan Plambing',
                'Perhitungan Teknis Mekanikal, Elektrikal, dan Plambing',
                'Gambar Rencana Dan Detail Pengelolaan Air Limbah',
                'Gambar Rencana Dan Detail Pengelolaan Air Bersih',
                'Gambar Rencana Dan Detail Pencahayaan Umum, dan Pencahanyaan Khusus',
                'Gambar Rencana Dan Detail Sumber Listrik, dan Jaringan Listrik',
            ],
            'Data Tambahan' => [
                'Gambar Sederhana Batas Tanah',
                'Hasil Penyelidikan Tanah',
                'Peil Banjir',
            ],
            'Upload' => [
                'Upload semua dokumen ke sistem',
            ],
        ],
        'SLF' => [
            'Data Umum' => [
                'Data Penyedia Jasa Pengkaji Teknis',
                'Laporan Pemeriksaan Kelaikan Fungsi Bangunan',
                'Surat Pernyataan Kelaikan Fungsi',
                'Data Intensitas Bangunan (KKPR/KRK)',
                'Data Identitas Pemilik Bangunan (KTP/KITAS)',
            ],
            'Data Teknis Arsitektur' => [
                'Rekomendasi Peil Banjir',
                'Gambar Detail Bangunan',
                'Gambar Tampak Bangunan',
                'Gambar Potongan Bangunan',
                'Gambar Denah Bangunan',
                'Gambar Tapak Bangunan',
                'Spesifikasi Teknis Arsitektur Bangunan',
                'Gambar Situasi',
            ],
            'Data Teknis Struktur' => [
                'Gambar Dan Detail Teknis Penutup',
                'Gambar Dan Detail Teknis Rangka Atap',
                'Gambar Dan Detail Teknis Balok',
                'Gambar Dan Detail Teknis Kolom',
                'Gambar Dan Detail Teknis Fondasi dan sloof',
                'Spesifikasi Teknis Struktur Bangunan',
                'Perhitungan Teknis Struktur',
            ],
            'Data Teknis MEP' => [
                'Gambar Dan Detail Pengelolaan Air Limbah',
                'Gambar Dan Detail Pengelolaan Air Bersih',
                'Gambar Dan Detail Pencahayaan Umum, dan Pencahanyaan Khusus',
                'Gambar Dan Detail Sumber Listrik, dan Jaringan Listrik',
                'Spesifikasi Teknis Mekanikal, Elektrikal, dan Plambing',
                'Perhitungan Teknis Mekanikal, Elektrikal, dan Plambing',
            ],
            'Upload' => [
                'Upload semua dokumen ke sistem',
            ],
        ],
        'PERENCANAAN' => [
            'Paving' => [
                'Survey' => [
                    'Survey Lapangan',
                ],
                'Gambar Kerja' => [
                    'Layout Eksisting',
                    'Detail Eksisting',
                    'Layout Rencana',
                    'Detail Rencana',
                    'Detail Potongan Rencana',
                ],
                'Engineering Estimate' => [
                    'Pembuatan Item RAB',
                    'Perhitungan Volume',
                    'Pembuatan Analisa',
                    'Penentuan Harga Bahan',
                    'Setting RAB sesuai Pagu',
                    'Time Schedule',
                ],
                'BOQ' => [
                    'Setting BOQ dari Engineering Estimate',
                ],
                'Rencana Kerja dan Syarat2 Teknis' => [
                    'Pembuatan Check List RKS',
                    'Pembuatan RKS dari database yang ada',
                    'Pembuatan RKS baru',
                    'Spesifikasi Teknis',
                ],
                'Dokumen Teknis' => [
                    'Rencana Kerja & Syarat-syarat Teknis',
                    'Spesifikasi Teknis',
                    'Metodologi Pelaksanaan Pekerjaan',
                    'Pembuatan SMKK Konsultan',
                    'Pembuatan SMKK Kontaktor',
                ],
                'Harga Perkiraan Sendiri' => [
                    'Pembuatan HPS',
                ],
                'Laporan' => [
                    'Pembuatan Laporan Pendahuluan',
                    'Pembuatan Laporan Prarencana',
                    'Pembuatan Laporan Antara',
                    'Pembuatan Laporan Akhir',
                ],
                'Finalisasi Dokumen Perencanaan' => [
                    'Print dokumen gambar',
                    'Print dokumen EE',
                ],
            ],
        ],
    ];
    $templateCategory = $dailyTask->task->project->category ?? null;
    if ($templateCategory === 'PERENCANAAN' && ($dailyTask->task->jenis_tugas ?? null) === 'Paving') {
        $dailyTaskOptions = $dailyTaskOptionsByCategory['PERENCANAAN']['Paving'][$dailyTask->task->name] ?? [];
    } else {
        $dailyTaskOptions = $dailyTaskOptionsByCategory[$templateCategory][$dailyTask->task->jenis_tugas] ?? [];
    }
    $showTemplate = !empty($dailyTaskOptions);

    if ($showTemplate) {
        $manualName = trim((string) $request->input('manual_name', ''));
        $flatOptions = array_is_list($dailyTaskOptions)
            ? $dailyTaskOptions
            : collect($dailyTaskOptions)->flatten(1)->values()->all();

        if ($manualName !== '') {
            $validated = $request->validate([
                'manual_name' => 'required|string|max:255',
                'due_date' => 'required|date',
                'description' => 'nullable|string',
                'weight' => 'required|integer|min:1|max:10',
            ]);
            $name = $validated['manual_name'];
        } else {
            $nameRule = $flatOptions
                ? 'required|string|in:' . implode(',', $flatOptions)
                : 'required|string|max:255';
            $validated = $request->validate([
                'name' => $nameRule,
                'due_date' => 'required|date',
                'description' => 'nullable|string',
                'weight' => 'required|integer|min:1|max:10',
            ]);
            $name = $validated['name'];
        }

        $dailyTask->update([
            'project_item_id' => null,
            'name' => $name,
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? null,
            'weight' => $validated['weight'],
        ]);
    } else {
        $validated = $request->validate([
            'project_item_id' => 'required|exists:project_items,id',
            'due_date' => 'required|date',
            'description' => 'nullable|string',
            'weight' => 'required|integer|min:1|max:10',
        ]);

        $dailyTask->update([
            // name ikut item pekerjaan (biar konsisten)
            'project_item_id' => $validated['project_item_id'],
            'name' => \App\Models\ProjectItem::find($validated['project_item_id'])->name,
            'due_date' => $validated['due_date'],
            'description' => $validated['description'] ?? null,
            'weight' => $validated['weight'],
        ]);
    }

    return back()->with('success', 'Daily Task berhasil diperbarui.');
    }

    /* ================= CLAIM ================= */
    public function take(DailyTask $dailyTask)
    {
    if ($dailyTask->assigned_to_staff_id !== null) {
        return back()->with('error', 'Tugas sudah diambil.');
    }

    $dailyTask->update([
        'assigned_to_staff_id' => auth()->id(),
        'status' => 'Belum Dikerjakan',
    ]);

    return back()->with('success', 'Tugas berhasil diambil.');
    }
    /* ================= SHOW UPLOAD FORM ================= */
    public function showUploadForm(DailyTask $dailyTask)
    {
    return view('daily-tasks.upload', compact('dailyTask'));
    }


}
