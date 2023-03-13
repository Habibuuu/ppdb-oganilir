<div>
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Pendaftaran</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">Jalur Zonasi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        @if ($kontrol_jalur->count() < 1)
                            <center><b>Perhatian !</b> Jaluar Zonasi belum Dibuka</center>
                            <center>
                                <p>Pendaftaran Online dibuka tanggal
                                    {{ date('d', strtotime($tanggal_buka->tanggal_buka)) }} -
                                    {{ date('d F Y', strtotime($tanggal_buka->tanggal_tutup)) }}</p>
                            </center>
                        @elseif ($status_pendaftaran == 'sudah')
                            <center><b>Perhatian !</b> Anda Sudah Mendaftar Jalur Zonasi</center> <br>
                            <center>
                                <a href="{{ route('siswa.zonasi.cetak.index') }}" class="btn btn-primary">Cetak Kartu
                                    Pendaftaran</a>
                            </center>
                        @else
                            <form class="row g-3 needs-validation">
                                <div class="col-md-12">
                                    <label class="col-form-label">Pilih Sekolah</label>
                                    <select wire:model="id_sekolah" class="js-example-basic-single col-sm-12 @error('id_sekolah') is-invalid @enderror"
                                        id="id_sekolah">
                                        <option value="">--Pilih Sekolah--</option>
                                        @foreach ($daftar_sekolah as $key_sekolah)
                                            <option value="{{ $key_sekolah->id }}">
                                                {{ $key_sekolah->nama_sekolah }}</option>
                                        @endforeach
                                    </select>
                                    @error('id_sekolah')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label>Latitude</label>
                                    <input type="text"
                                        class="form-control @error('latitude_siswa') is-invalid @enderror"
                                        placeholder="Contoh : -3.259305" autofocus wire:model="latitude_siswa">
                                    @error('latitude_siswa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label>Longitude</label>
                                    <input type="text"
                                        class="form-control @error('longitude_siswa') is-invalid @enderror"
                                        placeholder="Contoh : 104.6510159" autofocus wire:model="longitude_siswa">
                                    @error('longitude_siswa')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label>Jarak</label>
                                    <input type="text" class="form-control" placeholder="otomatis by sistem"
                                        autofocus wire:model="jarak" disabled>
                                    <small>Jarak diatas menggunakan satuan meter</small>
                                    @error('jarak')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label>Usia Kartu Keluarga</label>
                                    <input type="text" class="form-control @error('usia_kk') is-invalid @enderror"
                                        autofocus wire:model="usia_kk" placeholder="Contoh : 2 Tahun, 7 Bulan">
                                    @error('usia_kk')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label>File Kartu Keluarga</label>
                                    <input type="file"
                                        class="form-control @error('file_kartu_keluarga') is-invalid @enderror"
                                        wire:model="file_kartu_keluarga">
                                    @error('file_kartu_keluarga')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div wire:loading wire:target="surat_pengantar_sd">Uploading...</div>
                                </div>
                                <div class="col-md-6">
                                    <label>Surat Pengantar SD/MI</label>
                                    <input type="file"
                                        class="form-control @error('surat_pengantar_sd') is-invalid @enderror"
                                        wire:model="surat_pengantar_sd">
                                    @error('surat_pengantar_sd')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <div wire:loading wire:target="surat_pengantar_sd">Uploading...</div>
                                </div>

                                @if ($persyaratan->count() > 0)
                                    <hr>
                                    <label for="">PERSYARATAN WAJIB <b
                                            style="color: black;">{{ $pesan_persyaratan }}</b></label>
                                    @foreach ($persyaratan as $key_persyaratan)
                                        @php
                                            $nama_surat = $key_persyaratan->nama_surat;
                                            $new_nama_surat = strtr($nama_surat, '_', ' ');

                                        @endphp
                                        <div class="col-md-4">
                                            <label>{{ ucwords($new_nama_surat) }}</label>
                                            <div wire:loading wire:target="dynamicMapping.{{ $nama_surat }}">
                                                Uploading...</div>
                                            <input type="file" wire:model="dynamicMapping.{{ $nama_surat }}"
                                                class="form-control" placeholder="Masukkan Foto">
                                        </div>
                                    @endforeach

                                    {{-- {{ $pesan_persyaratan }} --}}
                                @endif

                                <div class="col-md-12">
                                    {{-- <button type="button" class="btn btn-primary" wire:click="store">Daftar</button> --}}
                                    {{-- <button class="btn btn-primary" wire:click.prevent="store"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>Daftar</span>
                                        <span wire:loading>Proses Upload..</span>
                                    </button> --}}

                                    <div class="col-md-12">
                                        <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                            data-original-title="test" data-bs-target="#exampleModal"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove>Daftar</span>
                                            <span wire:loading>Proses Upload..</span>
                                            {{-- <button type="button" class="btn btn-primary" wire:click="store">Daftar</button> --}}
                                    </div>

                                </div>

                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" wire:ignore.self id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel2">Informasi</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Periksa kembali data anda sebelum mengirim formulir, data yang sudah terkirim tidak dapat
                        diperbarui !</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">batal</button>
                    <button class="btn btn-warning" type="submit" wire:click.prevent="store">Daftar</button>
                </div>
            </div>
        </div>
    </div>
</div>



@push('scripts')
    <script>
        $(document).ready(function() {
            $('#id_sekolah').select2().on('change', function(e) {
                @this.set('id_sekolah', e.target.value);
            });

            window.livewire.on('reinitializeSelect2', () => {
                $('#id_sekolah').select2().on('change', function(e) {
                    @this.set('id_sekolah', e.target.value);
                });
            });
        });
    </script>
@endpush
