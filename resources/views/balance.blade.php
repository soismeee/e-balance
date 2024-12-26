@extends('layout.app')
@push('css')

    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    
@endpush
@section('container')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <h4>Data Balance cairan</h4>
                    <div class="form-group">
                        <label>Cari data di tanggal tertentu</label>
                        <input type="date" class="form-control" name="tanggal" id="tanggal" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div id="data-balance" class="table-responsive">
                    <table class="table table-responsive">
                        <thead class="table-light">
                            <tr>
                                <td width="5%">No</td>
                                <td width="15%">Pasien</td>
                                <td width="10%">BB</td>
                                <td width="10%">Suhu badan</td>
                                <td width="12%">Cairan masuk</td>
                                <td width="12%">Cairan keluar</td>
                                <td width="15%">Total Balance</td>
                                <td width="15%">DateTime</td>
                                <td width="6%">#</td>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script>
        $(document).ready(function(){
            loading();
            loaddata();
        });

        function loading(){
            $('#data-balance table tbody').append(
                `
                <tr>
                    <td colspan="9" class="text-center" id="loading">Loading...</td>    
                </tr>
                `
            )
        }

        $(document).on('change', '#tanggal', function(e){
            $('#data-balance table tbody').empty();
            loading();
            loaddata();
        });

        function formatTanggal(tanggal) {
            if (!tanggal) return "Belum tersedia"; // Jika tanggal null atau undefined
            let date = new Date(tanggal); // Konversi string tanggal ke objek Date
            let day = String(date.getDate()).padStart(2, '0'); // Hari dengan 2 digit
            let month = String(date.getMonth() + 1).padStart(2, '0'); // Bulan dengan 2 digit
            let year = date.getFullYear(); // Tahun
            return `${day}/${month}/${year}`; // Format dd-mm-yyyy
        }

        function formatTime(dateString) {
            // Ubah string menjadi objek Date
            let date = new Date(dateString);

            // Ambil jam, menit, dan detik
            let hours = date.getHours();
            let minutes = date.getMinutes();
            let seconds = date.getSeconds();

            // Tambahkan angka nol di depan angka jika kurang dari 10
            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            // Format waktu dalam format HH:mm:ss
            return `${hours}:${minutes}:${seconds}`;
        }

        function loaddata(){
            $.ajax({
                url: "{{ url('getBalance') }}",
                type: "GET",
                data: {_token: "{{ csrf_token() }}", 'tanggal' : $('#tanggal').val()},
                success: function(response){
                    $('#loading').hide();
                    let data = response.data;
                    data.forEach((params, index) => {
                    $('#data-balance table tbody').append(
                        `
                        <tr>
                            <td>${index+1}</td>    
                            <td>${params.pasien} (${params.usia} tahun)</td>   
                            <td>${params.bb} Kg</td>   
                            <td>${params.suhu_badan} C</td>   
                            <td>
                                <strong>Total : ${params.cairan_masuk}</strong>
                                <br />
                                Infus : ${params.infus} <br />
                                Transfusi darah : ${params.transfusi_darah} <br />
                                Terapi : ${params.terapi} <br />
                                Makmin NGT : ${params.makan_minum_ngt} <br />
                            </td>   
                            <td>
                                <strong>Total : ${params.cairan_keluar}</strong>
                                <br />
                                Urin : ${params.urin} <br />
                                BAB : ${params.bab} <br />
                                muntah : ${params.muntah} <br />
                                Cairan NGT : ${params.cairan_ngt} <br />
                                Drainage : ${params.drainage} <br />
                            </td>   
                            <td>
                                <strong>Total : ${params.balance_cairan}</strong>
                                <br /><br />
                                IWL : ${params.iwl} <br />
                                Air Metabolisme : ${params.air_metabolisme}
                            </td>   
                            <td>
                                Tanggal : ${formatTanggal(params.created_at)} <br />
                                Pukul : ${formatTime(params.created_at)}
                                </td>   
                            <td>
                                <div class="btn-group">
                                    <a href="" class="btn btn-sm btn-danger hapus" data-id="${params.id}">Hapus</a>
                                </div>
                            </td>
                        </tr>
                        `
                    )
                    });
                },
                error: function(err){
                    $('#loading').show();
                    $('#loading').text(err.responseJSON.message);
                }
            });
        }

        $(document).on('click', '.hapus', function(e){
            e.preventDefault();
            let id = $(this).data('id');
            Swal.fire({
                title: "Yakin ingin menghapus ini?",
                text: "Data ini akan terhapus pada sistem!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('delBalance') }}/"+id,
                    data: { '_token': "{{ csrf_token() }}"},
                    success: function(response){
                    $('#data-balance table tbody').empty();
                    loading();
                    loaddata();
                    Swal.fire({
                        title: "Terhapus!",
                        text: response.message,
                        icon: "success"
                    });
                    },
                    error: function(err){
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: err.responseJSON.message,
                    });
                    }
                });
                }
            });
        });
    </script>
@endpush