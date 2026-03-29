@extends('layouts.guest')

@section('content')
<div class="container d-flex align-items-center justify-content-center py-5" style="min-height: 100vh; background: #f4f7fe;">
    <div class="card border-0 shadow-lg animate-up" style="width: 100%; max-width: 600px; border-radius: 30px; overflow: hidden;">
        <div class="p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="icon-box mb-3 mx-auto shadow-sm"><i class="fa-solid fa-cloud-arrow-up text-primary fs-3"></i></div>
                <h3 class="fw-bold">Tambah Koleksi</h3>
                <p class="text-muted small">Lengkapi data untuk menambah buku baru</p>
            </div>

            <form action="{{ route('buku.simpan') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">Judul Buku</label>
                        <input type="text" name="Judul" class="form-control custom-input" placeholder="Masukkan judul..." required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Penulis</label>
                        <input type="text" name="Penulis" class="form-control custom-input" placeholder="Nama Penulis" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Penerbit</label>
                        <input type="text" name="Penerbit" class="form-control custom-input" placeholder="Penerbit" required>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label small fw-bold">Tahun Terbit</label>
                        <input type="number" name="TahunTerbit" class="form-control custom-input" placeholder="2024" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted">Kategori Buku</label>
                        <select name="KategoriID" class="form-select custom-input">
                            <option value="">-- Pilih Kategori --</option>
                            @php 
                                $daftarKategori = [];
                                if(class_exists('App\Models\KategoriBuku')) {
                                    $daftarKategori = \App\Models\KategoriBuku::all();
                                }
                            @endphp
                            @foreach($daftarKategori as $kat)
                                <option value="{{ $kat->KategoriID }}">
                                    {{ $kat->NamaKategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold text-muted">Jumlah Stok</label>
                        <input type="number" name="Stok" class="form-control custom-input" placeholder="Contoh: 10" required min="1">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label small fw-bold">Cover Buku</label>
                        <div class="upload-zone p-4 text-center border-dashed rounded-4 mb-2">
                            <input type="file" name="Cover" id="coverInput" class="d-none" accept="image/*">
                            <label for="coverInput" style="cursor: pointer;" class="w-100">
                                <div id="previewContainer" class="mb-2 d-none">
                                    <img id="imagePreview" src="#" alt="Preview" class="rounded-3 shadow-sm" style="max-height: 150px;">
                                </div>
                                <div id="uploadPlaceholder">
                                    <i class="fa-regular fa-image fs-1 text-muted mb-2"></i>
                                    <p class="small text-muted mb-0">Klik untuk pilih gambar cover</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-3 fw-bold btn-grad border-0 shadow">Simpan Koleksi</button>
                    <a href="{{ route('dashboard') }}" class="btn btn-light py-2 text-muted fw-bold border-0">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .icon-box { width: 60px; height: 60px; background: #eef4ff; border-radius: 18px; display: flex; align-items: center; justify-content: center; }
    .custom-input { border-radius: 12px; border: 2px solid #f1f3f9; padding: 12px; font-size: 0.9rem; }
    .custom-input:focus { border-color: #007bff; box-shadow: none; background: #fff; }
    .border-dashed { border: 2px dashed #dee2e6; transition: 0.3s; }
    .border-dashed:hover { border-color: #007bff; background: #f8fbff; }
    .btn-grad { background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border-radius: 15px; transition: 0.3s; }
    .btn-grad:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,123,255,0.3); }
    .animate-up { animation: fadeInUp 0.8s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
    const coverInput = document.getElementById('coverInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    coverInput.onchange = evt => {
        const [file] = coverInput.files
        if (file) {
            imagePreview.src = URL.createObjectURL(file)
            previewContainer.classList.remove('d-none');
            uploadPlaceholder.classList.add('d-none');
        }
    }
</script>
@endsection