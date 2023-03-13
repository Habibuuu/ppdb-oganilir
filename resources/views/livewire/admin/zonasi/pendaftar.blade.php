<div>
    <div class="container-fluid">
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3>Jalur Zonasi</h3>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                        <li class="breadcrumb-item">jalur-zonasi-pendaftaran</li>
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
                                        <th>Lat & Long</th>
                                        <th>Jarak</th>
                                        <th>Usia KK</th>
                                        <th>Detail</th>
                                        <th>Posisi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @forelse ($data_zonasi as $key_data_zonasi)
                                        {{-- @dd($key_data_zonasi->zonasi) --}}
                                        <tr>
                                            <td>{{ $data_zonasi->firstItem() + $loop->index }}</td>
                                            <td>{{ $key_data_zonasi->nama_lengkap }}</td>
                                            <td>{{ $key_data_zonasi->no_pendaftaran }}</td>
                                            <td>{{ $key_data_zonasi->nama_sekolah }}</td>
                                            <td>
                                                {{-- {{ $key_data_zonasi->latitude_siswa }}, <br>
                                                {{ $key_data_zonasi->longitude_siswa }} <br> --}}
                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $key_data_zonasi->latitude_siswa }},{{ $key_data_zonasi->longitude_siswa }}"
                                                    target="_blank" class="btn btn-primary btn-xs"><i
                                                        class="fa fa-map-marker"></i> Lihat</a>
                                            </td>
                                            <td>{{ $key_data_zonasi->jarak }} meter</td>
                                            <td>{{ $key_data_zonasi->usia_kk }}</td>
                                            <td>
                                                <a href="{{ route('siswa.zonasi.cetak.index', ['id_siswa' => $key_data_zonasi->id_siswa]) }}"
                                                    target="_blank" class="btn btn-primary btn-xs"><i
                                                        class="fa fa-eye"></i>  </a>
                                            </td>
                                            <td>
                                                @if ($key_data_zonasi->status == 'diverifikasi')
                                                    <button type="button" class="btn btn-success btn-xs"><i
                                                            class="fa fa-check"></i> Diverifikasi</button>
                                                @elseif($key_data_zonasi->status == 'ditolak')
                                                    <button type="button" class="btn btn-danger btn-xs"><i
                                                            class="fa fa-times"></i> Ditolak</button>
                                                @elseif ($key_data_zonasi->status == 'menunggu')
                                                    <button type="button" class="btn btn-warning btn-xs"><i
                                                            class="fa fa-clock-o"></i> Menunggu</button>
                                                @elseif ($key_data_zonasi->status == 'diterima')
                                                    <button type="button" class="btn btn-primary btn-xs"><i
                                                            class="fa fa-check"></i> Diterima</button>
                                                @endif

                                            </td>
                                            <td>
                                                @php
                                                    if ($key_data_zonasi->status != 'menunggu') {
                                                        $status = 'disabled';
                                                    }else{
                                                        $status = '';
                                                    }
                                                @endphp
                                                <button
                                                    wire:click.prevent="dataId({{ $key_data_zonasi->id_pendaftaran }})" {{ $status }}
                                                    type="button" data-bs-toggle="modal" data-original-title="test"
                                                    data-bs-target="#ModalVerifikasi" class="btn btn-warning btn-xs"><i
                                                        class="fa fa-check-square"></i> Verifikasi</button>
                                                <button
                                                    wire:click.prevent="dataId({{ $key_data_zonasi->id_pendaftaran }})" {{ $status }}
                                                    type="button" data-bs-toggle="modal" data-original-title="test"
                                                    data-bs-target="#ModalTolak" class="btn btn-danger btn-xs">
                                                    <i class="fa fa-times"></i> Tolak</button>
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
                            <div wire:ignore.self class="modal fade" id="ModalVerifikasi" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Konfirmasi Verifikasi</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true close-btn">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menyetujui data siswa ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary close-btn"
                                                data-dismiss="modal">Batal</button>
                                            <button type="button" wire:click.prevent="verifikasi()"
                                                class="btn btn-warning close-modal"
                                                data-dismiss="modal">Verifikasi</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div wire:ignore.self class="modal fade" id="ModalTolak" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Tolak Verifikasi</h5>
                                            <button type="button" class="close" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true close-btn">×</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menolak data siswa ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary close-btn"
                                                data-dismiss="modal">Batal</button>
                                            <button type="button" wire:click.prevent="verifikasiDitolak()"
                                                class="btn btn-warning close-modal" data-dismiss="modal">Tolak</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            @if ($data_zonasi->hasPages())
                                <div class="d-flex justify-content-center">
                                    {{ $data_zonasi->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="lampiranModal" tabindex="-1" role="dialog"
        aria-labelledby="editExampleLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">File Persyaratan</h5>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        @php
                            $arr_pendaftaran = $file_persyaratan;
                            $potong_awal = substr($arr_pendaftaran, 2);
                            $potong_akhir = substr($potong_awal, 0, -2);
                            // @dd($potong_akhir);
                            $persyaratan_sekolah = explode('","', $potong_akhir);
                        @endphp
                        <table class="table table-striped">
                            <tr>
                                <th>File Persyaratan</th>
                                <th>Lihat</th>
                            </tr>
                            @foreach ($persyaratan_sekolah as $key => $value)
                                <tr>
                                    <td>
                                        <li>{{ $value }}</li>
                                    </td>
                                    <td>
                                        <a class="btn btn-info btn-xs"
                                            href="{{ asset('storage/data_pendaftaran/' . $value) }}"
                                            target="_blank">Lihat</a>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
