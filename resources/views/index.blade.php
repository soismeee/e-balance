@extends('layout.app')
@push('css')
    
    <link href="/assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

@endpush
@section('container')
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h3>Form Balance Cairan</h3>
                <form id="form-balance" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="no_rm">Nomor RM</label>
                                <input type="text" class="form-control" name="no_rm" id="no_rm" placeholder="Masukan nomor RM">
                            </div>
                            <div class="form-group mb-3">
                                <label for="pasien">Pasien</label>
                                <input type="text" class="form-control" name="pasien" id="pasien" placeholder="Masukan nama pasien">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="bb">Berat Badan (kg):</label>
                                <input type="number" step="0.01" class="form-control" id="bb" name="bb" required placeholder="Beran badan pasien">
                            </div>
                            <div class="form-group mb-3">
                                <label for="usia">Usia (tahun):</label>
                                <input type="number" step="1" class="form-control" id="usia" name="usia" required placeholder="Umur pasien">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <hr />
                        </div>
                        <div class="col-lg-6">
                            <h4>Cairan Masuk</h4>
                            <div class="form-group mb-3">
                                <label for="infus">Infus per 24 jam (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="infus" name="infus" required placeholder="Masukan jumlah cairan infus">
                            </div>
                            <div class="form-group mb-3">
                                <label for="transfusi_darah">Transfusi Darah per 24 jam (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="transfusi_darah" name="transfusi_darah" placeholder="Masukan jumlah cairan transfusi darah">
                            </div>
                            <div class="form-group mb-3">
                                <label for="terapi">Terapi: Drip/Injeksi (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="terapi" name="terapi" placeholder="Masukan jumlah cairan terapi">
                            </div>
                            <div class="form-group mb-3">
                                <label for="makan_minum_ngt">Makan/Minum via oral/NGT (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="makan_minum_ngt" name="makan_minum_ngt" placeholder="Masukan jumlah cairan makan minum NGT">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4>Cairang keluar</h4>
                            <div class="form-group mb-3">
                                <label for="suhu_badan">Suhu Badan (°C):</label>
                                <input type="number" step="0.1" class="form-control" id="suhu_badan" name="suhu_badan" required placeholder="Masukan suhu badan">
                            </div>
                            <div class="form-group mb-3">
                                <label for="urin">Urin per 24 jam (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="urin" name="urin" placeholder="Masukan jumlah urin">
                            </div>
                            <div class="form-group mb-3">
                                <label for="bab">BAB per 24 jam (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="bab" name="bab" placeholder="Masukan jumlah BAB">
                            </div>
                            <div class="form-group mb-3">
                                <label for="muntah">Muntah (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="muntah" name="muntah" placeholder="Masukan jumlah muntah">
                            </div>
                            <div class="form-group mb-3">
                                <label for="cairan_ngt">Cairan via NGT (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="cairan_ngt" name="cairan_ngt" placeholder="Masukan jumlah cairan via NGT">
                            </div>
                            <div class="form-group mb-3">
                                <label for="drainage">Drainage (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="drainage" name="drainage" placeholder="Masukan jumlah cairan drainage">
                            </div>
                            <div class="form-group mb-3">
                                <label for="perdarahan">Perdarahan (cc):</label>
                                <input type="number" step="0.01" class="form-control" id="perdarahan" name="perdarahan" placeholder="Masukan jumlah cairan perdarahan">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="tombol">Hitung Balance Cairan</button>
                </form>
                <div style="margin-top: 20px;">
                    <h4>Balance Cairan sebesar : <span id="hasil"></span></h4>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="/assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script>

        function telepon(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57))
                return false;
            return true;
        }

        $('#form-balance').on('submit', function(event){
            event.preventDefault();
            $('#tombol').html('Loading...');
            $.ajax({
                type: "POST",
                url: "{{ url('/save') }}",
                data: $('#form-balance').serialize(),
                success: function(response){
                    $('#tombol').html("Hitung Balance Cairan");
                    $('#form-balance')[0].reset();
                    $('#hasil').text(response.data);
                },
                error: function(err){
                    $('#tombol').html("Hitung Balance Cairan");
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: err.responseJSON.message,
                    });
                }
            });
        });
    </script>
@endpush