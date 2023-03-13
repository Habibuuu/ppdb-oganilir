<div>
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Jalur mutasi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">jalur-mutasi-tidak-lulus</li>
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
                        <div class="table-responsive">
                            <table class="table table-striped" style="font-size: 12px;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>No Pendaftar</th>
                                        <th>Sekolah Asal</th>
                                        <th>Sk Mutasi Ortu</th>
                                        <th>Detail</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($data_mutasi as $key_data_mutasi)
                                        {{-- @dd($key_data_mutasi->mutasi) --}}
                                        <tr>
                                            <td>{{ $data_mutasi->firstItem() + $loop->index }}</td>
                                            <td>{{ $key_data_mutasi->nama_lengkap }}</td>
                                            <td>{{ $key_data_mutasi->no_pendaftaran }}</td>
                                            <td>{{ $key_data_mutasi->nama_sekolah }}</td>
                                            <td>
                                                <a href="{{ asset('storage/data_pendaftaran/' . $key_data_mutasi->mutasi->sk_mutasi_orang_tua) }}"
                                                    class="btn btn-primary btn-xs" target="_blank">lihat</a>
                                            </td>
                                            <td style="font-size: 10px;"><a
                                                    href="{{ route('siswa.mutasi.cetak.index', ['id_siswa' => $key_data_mutasi->id_siswa]) }}"
                                                    target="_blank" class="btn btn-primary btn-xs"><i
                                                        class="fa fa-eye"></i> Detail</a>
                                            </td>
                                            <td>
                                                <button wire:click.prevent="dataId({{ $key_data_mutasi->id }})"
                                                    type="button" data-bs-toggle="modal" data-original-title="test"
                                                    data-bs-target="#exampleModal" class="btn btn-primary btn-xs"><i
                                                        class="fa fa-repeat"></i> Pindahkan</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11">
                                                <center>BELUM ADA DATA</center>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <div wire:ignore.self class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Data</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal"
                                                arial-label="Close">
                                                <span aria-hidden="true close-btn">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin mengembalikan siswa ke tahap Verifikasi ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary close-btn"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="button" wire:click.prevent="returnVerifikasi()"
                                                class="btn btn-danger close-modal"
                                                data-dismiss="modal">Pindahkan</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            @if ($data_mutasi->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $data_mutasi->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
