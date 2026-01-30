<!-- Template Import Info Card - Dapat ditampilkan di Admin Dashboard -->
<div class="card border-info mt-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="bi bi-file-earmark-spreadsheet"></i> Fitur Template Import Data</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Kemudahan import data dalam jumlah besar menggunakan file template Excel (.xlsx).</p>
        
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-center">
                    <i class="bi bi-download" style="font-size: 2rem; color: #0d6efd;"></i>
                    <h6 class="mt-2">Download Template</h6>
                    <p class="small text-muted">Download template Excel siap pakai untuk setiap jenis data (Mesin, Line, Spare Part)</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="bi bi-pencil-square" style="font-size: 2rem; color: #28a745;"></i>
                    <h6 class="mt-2">Isi Data</h6>
                    <p class="small text-muted">Isi template dengan data Anda menggunakan Microsoft Excel atau aplikasi spreadsheet lainnya</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="bi bi-upload" style="font-size: 2rem; color: #ffc107;"></i>
                    <h6 class="mt-2">Upload & Selesai</h6>
                    <p class="small text-muted">Upload file Excel dan sistem akan memproses data otomatis</p>
                </div>
            </div>
        </div>

        <hr>

        <h6>Template Tersedia:</h6>
        <ul class="small">
            <li><strong>Template Mesin</strong> (.xlsx) - Untuk import data mesin baru (Nama, Line, Deskripsi, Status)</li>
            <li><strong>Template Line</strong> (.xlsx) - Untuk import data line/departemen (Nama, Deskripsi, Status)</li>
            <li><strong>Template Spare Part</strong> (.xlsx) - Untuk import data spare part (Nama, Kode, Deskripsi, Stok, Status)</li>
        </ul>

        <div class="mt-3">
            <a href="{{ asset('TEMPLATE_IMPORT_GUIDE.md') }}" class="btn btn-sm btn-outline-info" download>
                <i class="bi bi-file-text"></i> Panduan Lengkap
            </a>
            <span class="small text-muted ms-2">Format Excel 2007+ (.xlsx) • UTF-8 Encoding • PhpSpreadsheet</span>
        </div>
    </div>
</div>
