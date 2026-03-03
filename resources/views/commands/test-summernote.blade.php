@extends('layouts.app')

@section('title', 'Test Summernote - Sistem Laporan Maintenance')

@section('content')
<div class="card">
    <div class="card-header">
        <h4 class="mb-0">Test Summernote Editor</h4>
    </div>
    <div class="card-body">
        <p class="text-muted">Halaman ini untuk test apakah Summernote berhasil ter-load</p>
        
        <hr>

        <form method="POST" action="javascript:void(0)">
            <div class="mb-3">
                <label for="test_content" class="form-label">Test Summernote</label>
                <textarea class="form-control summernote" id="test_content" name="test_content" placeholder="Coba ketik di sini...">Ini adalah test summernote editor</textarea>
            </div>

            <button type="button" class="btn btn-primary" onclick="getContent()">
                <i class="bi bi-eye"></i> Lihat Content
            </button>
        </form>

        <hr>

        <div id="result" style="display:none;" class="mt-3">
            <h5>Content Output:</h5>
            <div class="bg-light p-3 rounded" id="output"></div>
        </div>
    </div>
</div>

<script>
function getContent() {
    const content = $('#test_content').summernote('code');
    $('#output').html(content);
    $('#result').show();
}

// Debug info
window.addEventListener('load', function() {
    console.log('=== DEBUG INFO ===');
    console.log('jQuery version:', typeof $ !== 'undefined' ? 'Loaded' : 'NOT Loaded');
    console.log('Summernote version:', typeof $.summernote !== 'undefined' ? 'Loaded' : 'NOT Loaded');
    console.log('Summernote elements on page:', $('.summernote').length);
    console.log('=== END DEBUG ===');
});
</script>
@endsection
