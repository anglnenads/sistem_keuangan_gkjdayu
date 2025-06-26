<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/inc_f_object.php");

// Koneksi
$conn = new cConnect();
$conn->goConnect();

// Tahun aktif
if (isset($_POST['tahun'])) {
    $tahun_aktif = intval($_POST['tahun']);
    $_SESSION['tahun_aktif'] = $tahun_aktif; // simpan ke session
} else if (isset($_SESSION['tahun_aktif'])) {
    $tahun_aktif = $_SESSION['tahun_aktif']; // ambil dari session
} else {
    $tahun_aktif = date("Y"); // default tahun sekarang
}

$bulan_nama = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$bulan_aktif = isset($_POST['bulan']) ? intval($_POST['bulan']) : null;
$bulan_text = $bulan_aktif ? $bulan_nama[$bulan_aktif] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .custom-dark-blue {
            border-color: rgb(43, 80, 135) !important;
            color: rgb(33, 87, 168) !important;
        }
    </style>
</head>

<body>
    <div>
        <div class="row mx-2">
            <div class="col-md-12">
                <div style="margin-top: 30px; margin-bottom: 30px; text-align: center;">
                    <h2 style="
                        margin: 0;
                        font-weight: 700;
                        font-size: 2.2rem;
                        color:rgba(2, 15, 20, 0.62);
                        letter-spacing: 1px;
                        text-shadow: 1px 1px 3px rgba(0,0,0,0.1);">
                        Dashboard Keuangan Bidang Komisi
                    </h2>
                    <p style="
                        margin-top: 8px;
                        font-size: 1.1rem;
                        color: #6c757d;
                        font-style: italic;">
                        Tahun <?php echo $tahun_aktif; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center" style="margin-top: 20px;">
            <div class="section">
                <form id="filterForm">
                    <div class="horizontal" style="height: 80px;">
                        <div class="form-group" style="width:100%; margin-left: 30px;">
                            <label for="bidang">Bidang</label>
                            <select style="width:90%" id="bidang" name="bidang" required>
                                <option value="">-- Pilih Bidang --</option>
                                <?php
                                $sql = "SELECT id_bidang, nama_bidang FROM bidang";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id_bidang'] . "'>" . $row['nama_bidang'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>Data tidak tersedia</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="width:100%">
                            <label for="komisi">Komisi</label>
                            <select style="width:90%" name="komisi" id="komisi" required>
                                <option value="">-- Pilih Komisi --</option>
                            </select>
                        </div>
                        <div class="form-group" style="width:50%">
                            <label for="start_month">Periode Mulai</label>
                            <select id="start_month" name="start_month" required>
                                <?php
                                $bulan = [
                                    1 => 'Januari',
                                    2 => 'Februari',
                                    3 => 'Maret',
                                    4 => 'April',
                                    5 => 'Mei',
                                    6 => 'Juni',
                                    7 => 'Juli',
                                    8 => 'Agustus',
                                    9 => 'September',
                                    10 => 'Oktober',
                                    11 => 'November',
                                    12 => 'Desember'
                                ];
                                foreach ($bulan as $key => $namaBulan) {
                                    echo "<option value='$key'>$namaBulan</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="width:50%">
                            <label for="end_month">Periode Akhir</label>
                            <select id="end_month" name="end_month" required>
                                <?php
                                foreach ($bulan as $key => $namaBulan) {
                                    echo "<option value='$key' " . ($key == 12 ? 'selected' : '') . ">$namaBulan</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" style="width:20%; margin-top:30px;">
                            <button type="submit" class="btn btn-primary">Tampilkan</button>
                        </div>
                    </div>
                </form>

                <br>
                <span style="color: red; font-style: italic; font-size: 15px;"> *Pilih Bidang dan Komisi Terlebih
                    Dahulu </span>
            </div>


            <div
                style="font-size:15px; font-weight: normal; display: flex; justify-content: center; align-items: center; gap: 100px; border: 1px solid rgba(0, 0, 0, 0.19); border-radius: 10px; padding: 10px; width: fit-content; margin: 0 auto; margin-top: 10px">
                <div id="satu">
                    <span>Bidang: </span><span class="isi" style="font-weight: bold;"></span>
                </div>
                <div id="dua">
                    <span>Komisi: </span><span class="isi" style="font-weight: bold;"></span>
                </div>
                <div id="tiga">
                    <span>Periode: </span><span class="isi" style="font-weight: bold;"></span>
                </div>
            </div>
        </div>
        <br>
    </div>

    <div class="row justify-content-center" style="margin: 20px; padding: 0 15px;">
        <div class="col-md-10>
                <div id=" judulKas" class="border-container"
            style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 20px; border-radius: 10px;">
            <!-- Kas Saldo Bidang Komisi -->
            <div class="position-relative text-center mb-3">
                <h5 class="m-0">
                    Kas dan Saldo Bidang Komisi
                    <?php echo !empty($bulan_text) ? $bulan_text . ' ' . $tahun_aktif : $tahun_aktif; ?>
                </h5>
                <a id="exportKasBidKom" href="#"
                    class="btn btn-success position-absolute top-0 end-0 d-flex align-items-center" role="button"
                    style="height: 150%; width: 70px;">
                    <i class="bi bi-printer me-1"></i>.xls
                </a>
            </div>
            <div class="text-center" style="font-size: 15px;">
                <span id="judulKas1"></span>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <canvas id="penerimaanPengeluaranChart"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="saldoChart"></canvas>
                </div>
            </div>
            <div id="totals" style="text-align: center; margin-top: 20px;">
                <h5 id="totalPenerimaan" style="font-size: 18px;">Total Penerimaan: Rp 0<br>
                    <small>(Dana Swadaya: Rp 0 | Dana Gereja: Rp 0)</small>
                </h5>
                <br>
                <h5 id="totalPengeluaran" style="font-size: 18px;">Total Pengeluaran: Rp 0<br>
                    <small>(Dana Swadaya: Rp 0 | Dana Gereja: Rp 0)</small>
                </h5>
                <br>
                <h5 id="totalSaldo" style="font-size: 18px;">Saldo: Rp 0<br>
                    <small>(Dana Swadaya: Rp 0 | Dana Gereja: Rp 0)</small>
                </h5>
            </div>
        </div>
    </div>

    <div class="row justify-content-center" style="margin: 20px; padding: 0 4px;">
        <!-- Pengeluaran Komisi -->
        <div class="col-md-5">
            <div style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 16px; border-radius: 12px;">
                <div class="position-relative text-center mb-3">
                    <h5 id="judulKomisi" class="m-0">
                        5 Pengeluaran Tertinggi Komisi
                        <?php echo !empty($bulan_text) ? $bulan_text . ' ' . $tahun_aktif : '' . $tahun_aktif; ?>
                    </h5>
                    <a id="exportPengeluaran" href="#"
                        class="btn btn-success position-absolute top-0 end-0 d-flex align-items-center" role="button"
                        style="height: 150%; width: 70px;">
                        <i class="bi bi-printer me-1"></i>.xls
                    </a>
                </div>
                <div class="text-center" ; style="font-size: 15px;">
                    <span id="judulKomisi1"></span>
                </div>
                <br>
                <!-- Chart -->
                <div style="width: 600px; height: 350px; width: 100%;">
                    <canvas id="pengeluaranKomisi" style="height: 510px; width: 910px;"></canvas>
                </div>

            </div>
        </div>

        <!-- AnggaranRealisasiKomisiBidang -->
        <div class="col-md-7">
            <div style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 16px; border-radius: 12px;">
                <div class="position-relative text-center mb-3">
                    <h5 id="judulKegiatan" class="m-0">
                        Rencana dan Realisasi Pengeluaran Komisi
                        <?php echo !empty($bulan_text) ? $bulan_text . ' ' . $tahun_aktif : '' . $tahun_aktif; ?>
                    </h5>
                    <a id="exportPengeluaranKegiatan" href="#"
                        class="btn btn-success position-absolute top-0 end-0 d-flex align-items-center" role="button"
                        style="height: 150%; width: 70px;">
                        <i class="bi bi-printer me-1"></i>.xls
                    </a>
                </div>
                <div class="text-center" ; style="font-size: 15px;">
                    <span id="judulKegiatan1"></span>
                </div>
                <br>

                <!-- Chart -->
                <div style="width: 600px; height: 349px; width: 100%;">
                    <canvas id="pengeluaranKegiatanKomisi" style="margin-top: 3px;"></canvas>
                </div>

            </div>
        </div>
    </div>
    </div>

    <script>
        $(document).ready(function () {
            var penerimaanPengeluaranChart;
            var saldoChart;
            var pengeluaranKegiatanKomisi;
            var pengeluaranKomisi;
            let myChartKegiatan = null;
            let chartKomisi;
            const ctxKegiatan = document.getElementById('pengeluaranKegiatanKomisi').getContext('2d');
            const monthDropdownKegiatanKomisi = $("#monthDropdownKegiatanKomisi");
            const tahunKegiatan = <?php echo $tahun_aktif; ?>;
            const limitKegiatan = 16;
            let pageKegiatan = 1;
            let totalPagesKegiatan = 1;
            let bulanAktifKegiatan = "";

            let currentFilter = {
                id_bidang: '',
                id_komisi: '',
                start_month: '',
                end_month: ''
            };

            // Ambil komisi berdasarkan bidang
            $('#bidang').on('change', function () {
                var bidangId = $(this).val();
                $('#komisi').html('<option value="">-- Pilih Komisi --</option>');
                if (bidangId) {
                    $.ajax({
                        type: "POST",
                        url: "../_function_i/ambilData.php",
                        data: { bidang: bidangId },
                        success: function (data) {
                            $('#komisi').html(data);
                        }
                    });
                }
            });

            // Submit form
            var tahun = <?php echo $tahun_aktif; ?>; 
            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                var id_bidang = $('#bidang').val();
                var id_komisi = $('#komisi').val();
                var start_month = $('#start_month').val();
                var end_month = $('#end_month').val();

                if (!id_bidang || !id_komisi) {
                    alert('Pilih Bidang dan Komisi terlebih dahulu');
                    return;
                }

                currentFilter = {
                    id_bidang,
                    id_komisi,
                    start_month,
                    end_month
                };

                pageKegiatan = 1;
                bulanAktifKegiatan = "";

                //Untuk KasSaldoBidKom
                $.ajax({
                    url: 'chartKasSaldoBidKom.php',
                    method: 'GET',
                    data: {
                        id_bidang: id_bidang,
                        id_komisi: id_komisi,
                        start_month: start_month,
                        end_month: end_month,
                        tahun: tahun
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (!response.data) {
                            alert('Tidak ada data');
                            return;
                        }

                        // Ambil nama bidang dan komisi dari option yang dipilih
                        var namaBidang = $('#bidang option:selected').text();
                        var namaKomisi = $('#komisi option:selected').text();

                        // Nama bulan
                        var namaBulanStart = $('#start_month option:selected').text();
                        var namaBulanEnd = $('#end_month option:selected').text();

                        // Gabungkan untuk header
                        var satu = namaBidang;
                        var dua = namaKomisi;
                        var tiga = namaBulanStart + '-' + namaBulanEnd;
                        // Update teks h5
                        $('#satu .isi').text(satu);
                        $('#dua .isi').text(dua);
                        $('#tiga .isi').text(tiga);
                        $('#judulKomisi1').html(` ${satu} (${tiga})`);
                        $('#judulKegiatan1').html(` ${satu} (${tiga})`);
                        $('#judulKas1').html(` ${satu} - ${dua} periode (${tiga})`);

                        var penerimaan = response.data.penerimaan || [];
                        var pengeluaran = response.data.pengeluaran || [];
                        var saldo = response.data.saldo || [];

                        var labels = [];
                        var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        for (let i = parseInt(start_month); i <= parseInt(end_month); i++) {
                            labels.push(monthNames[i - 1]);
                        }

                        var penerimaanData = [];
                        var pengeluaranData = [];
                        var saldoData = [];

                        for (let i = parseInt(start_month); i <= parseInt(end_month); i++) {
                            let pnr = penerimaan.find(p => p.bulan == i);
                            let png = pengeluaran.find(p => p.bulan == i);
                            let sld = saldo.find(p => p.bulan == i);

                            penerimaanData.push(pnr ? parseInt(pnr.total) : 0);
                            pengeluaranData.push(png ? parseInt(png.total) : 0);
                            saldoData.push(sld ? parseInt(sld.saldo) : 0);
                        }

                        // Destroy chart lama
                        if (penerimaanPengeluaranChart) penerimaanPengeluaranChart.destroy();
                        if (saldoChart) saldoChart.destroy();

                        // Line Chart
                        penerimaanPengeluaranChart = new Chart(document.getElementById('penerimaanPengeluaranChart'), {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [
                                    {
                                        label: 'Penerimaan',
                                        data: penerimaanData,
                                        borderColor: 'green',
                                        backgroundColor: 'green',
                                        fill: false,
                                        tension: 0
                                    },
                                    {
                                        label: 'Pengeluaran',
                                        data: pengeluaranData,
                                        borderColor: 'red',
                                        backgroundColor: 'red',
                                        fill: false,
                                        tension: 0
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Penerimaan dan Pengeluaran'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function (tooltipItem) {
                                                var datasetIndex = tooltipItem.datasetIndex;
                                                var index = tooltipItem.dataIndex;

                                                var bulanIndex = start_month - 1 + index; 

                                                // Fungsi format uang Indonesia
                                                function formatRupiah(angka) {
                                                    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                                }

                                                if (datasetIndex === 0) { // Penerimaan
                                                    var penerimaanBulan = penerimaan.find(p => p.bulan == (bulanIndex + 1)) || { dana_gereja: 0, dana_swadaya: 0, total: 0 };
                                                    return [
                                                        'Total Penerimaan: ' + formatRupiah(penerimaanBulan.total),
                                                        'Dana Gereja: ' + formatRupiah(penerimaanBulan.dana_gereja),
                                                        'Dana Swadaya: ' + formatRupiah(penerimaanBulan.dana_swadaya)
                                                    ];
                                                } else if (datasetIndex === 1) { // Pengeluaran
                                                    var pengeluaranBulan = pengeluaran.find(p => p.bulan == (bulanIndex + 1)) || { dana_gereja: 0, dana_swadaya: 0, total: 0 };
                                                    return [
                                                        'Total Pengeluaran: ' + formatRupiah(pengeluaranBulan.total),
                                                        'Dana Gereja: ' + formatRupiah(pengeluaranBulan.dana_gereja),
                                                        'Dana Swadaya: ' + formatRupiah(pengeluaranBulan.dana_swadaya)
                                                    ];
                                                }
                                                return '';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            callback: function (value) {
                                                const absValue = Math.abs(value);
                                                let formatted = '';

                                                if (absValue >= 1_000_000_000) {
                                                    formatted = (value / 1_000_000_000).toFixed(0) + ' M';
                                                } else if (absValue >= 1_000_000) {
                                                    formatted = (value / 1_000_000).toFixed(0) + ' Jt';
                                                } else if (absValue >= 1_000) {
                                                    formatted = (value / 1_000).toFixed(0) + ' Rb';
                                                } else {
                                                    formatted = value.toString();
                                                }

                                                return formatted;
                                            }
                                        }
                                    }
                                }
                            }
                        });

                        // Bar Chart (Horizontal)
                        saldoData = labels.map((_, i) => {
                            const bulanKe = parseInt(start_month) + i;
                            const data = saldo.find(s => s.bulan == bulanKe);
                            return data ? data.saldo : 0;
                        });

                        saldoChart = new Chart(document.getElementById('saldoChart'), {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Saldo',
                                    data: saldoData,
                                    backgroundColor: 'purple'
                                }]
                            },
                            options: {
                                responsive: true,
                                indexAxis: 'y',
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Sisa Saldo'
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function (context) {
                                                var index = context.dataIndex;
                                                var bulanIndex = parseInt(start_month) + index;

                                                var saldoBulan = saldo.find(p => p.bulan == bulanIndex) || {
                                                    saldo: 0,
                                                    dana_gereja: 0,
                                                    dana_swadaya: 0,
                                                    tambah_gereja: 0,
                                                    tambah_swadaya: 0,
                                                    kurang_gereja: 0,
                                                    kurang_swadaya: 0
                                                };

                                                return [
                                                    'Total Saldo: Rp ' + saldoBulan.saldo.toLocaleString('id-ID'),
                                                    'Dana Gereja: Rp ' + saldoBulan.dana_gereja.toLocaleString('id-ID'),
                                                    'Dana Swadaya: Rp ' + saldoBulan.dana_swadaya.toLocaleString('id-ID'),
                                                ];
                                            }

                                        }
                                    },
                                    datalabels: {
                                        anchor: 'center',
                                        align: 'center',
                                        color: 'black',
                                        font: {
                                            weight: 'bold'
                                        },
                                        formatter: function (value) {
                                            if (value === 0) {
                                                return '0';
                                            } else {
                                                return '';
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        ticks: {
                                            autoSkip: false,
                                            maxRotation: 0,
                                            minRotation: 0,
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    x: {
                                        ticks: {
                                            callback: function (value, index, ticks) {
                                                const absValue = Math.abs(value);
                                                if (absValue >= 1_000_000_000) {
                                                    return (value / 1_000_000_000).toFixed(0) + ' M';
                                                } else if (absValue >= 1_000_000) {
                                                    return (value / 1_000_000).toFixed(0) + ' Jt';
                                                } else if (absValue >= 1_000) {
                                                    return (value / 1_000).toFixed(0) + ' Rb';
                                                } else {
                                                    return value.toString();
                                                }
                                            },
                                            autoSkip: true, 
                                            maxTicksLimit: 6,
                                        }
                                    }

                                }

                            },
                            plugins: [ChartDataLabels] 
                        });

                        // Totalan Saldo + Breakdown
                        var totalPenerimaan = penerimaanData.reduce((a, b) => a + b, 0);
                        var totalPengeluaran = pengeluaranData.reduce((a, b) => a + b, 0);

                        var totalPenerimaanSwadaya = penerimaan.reduce((acc, p) => acc + (p.dana_swadaya || 0), 0);
                        var totalPenerimaanGereja = penerimaan.reduce((acc, p) => acc + (p.dana_gereja || 0), 0);

                        var totalPengeluaranSwadaya = pengeluaran.reduce((acc, p) => acc + (p.dana_swadaya || 0), 0);
                        var totalPengeluaranGereja = pengeluaran.reduce((acc, p) => acc + (p.dana_gereja || 0), 0);

                        var saldoTerakhirObj = saldo.length ? saldo[saldo.length - 1] : {
                            saldo: 0,
                            dana_swadaya: 0,
                            dana_gereja: 0
                        };


                        var totalSaldo = saldoTerakhirObj.saldo || 0;
                        var totalSaldoSwadaya = saldoTerakhirObj.dana_swadaya || 0;
                        var totalSaldoGereja = saldoTerakhirObj.dana_gereja || 0;

                        $('#totalPenerimaan').html(
                            `Total Penerimaan: Rp ${totalPenerimaan.toLocaleString('id-ID')}<br>
                            <small>(Dana Swadaya: Rp ${totalPenerimaanSwadaya.toLocaleString('id-ID')} | Dana Gereja: Rp ${totalPenerimaanGereja.toLocaleString('id-ID')})</small>`
                        );

                        $('#totalPengeluaran').html(
                            `Total Pengeluaran: Rp ${totalPengeluaran.toLocaleString('id-ID')}<br>
                            <small>(Dana Swadaya: Rp ${totalPengeluaranSwadaya.toLocaleString('id-ID')} | Dana Gereja: Rp ${totalPengeluaranGereja.toLocaleString('id-ID')})</small>`
                        );

                        $('#totalSaldo').html(
                            `Saldo: Rp ${totalSaldo.toLocaleString('id-ID')}<br>
                            <small>(Dana Swadaya: Rp ${totalSaldoSwadaya.toLocaleString('id-ID')} | Dana Gereja: Rp ${totalSaldoGereja.toLocaleString('id-ID')})</small>`
                        );

                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', xhr.responseText);
                        alert('Terjadi kesalahan: ' + error);
                    }
                });

                // ajax pengeluaran tertinggi
                $.ajax({
                    url: 'chartPengeluaranKomisi.php',
                    method: 'GET',
                    data: {
                        id_bidang: id_bidang,
                        id_komisi: id_komisi,
                        start_month: start_month,
                        end_month: end_month,
                        tahun: tahun
                    },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response); 

                        if (!response.data || response.data.length === 0) {
                            if (typeof chartKomisi !== 'undefined' && chartKomisi) {
                                chartKomisi.destroy();
                                chartKomisi = null;
                            }

                            const ctxKomisi = document.getElementById('pengeluaranKomisi').getContext('2d');
                            ctxKomisi.clearRect(0, 0, ctxKomisi.canvas.width, ctxKomisi.canvas.height);

                            return;
                        }

                        // Ambil nama bidang dan komisi dari option yang dipilih
                        var namaBidang = $('#bidang option:selected').text();
                        var namaKomisi = $('#komisi option:selected').text();

                        // Menyiapkan data untuk chart
                        var labels = [];
                        var data = [];
                        var tooltipDetails = [];

                        // Sorting berdasarkan total pengeluaran (descending)
                        response.data.sort(function (a, b) {
                            return b.total_pengeluaran - a.total_pengeluaran; // descending sort
                        });

                        response.data.forEach(function (item) {
                            labels.push(item.nama_komisi); // Nama komisi
                            data.push(item.total_pengeluaran / 1000000); // Total pengeluaran dalam juta

                            // Data untuk tooltip
                            tooltipDetails.push({
                                total: item.total_pengeluaran,
                                swadaya: item.dana_swadaya,
                                gereja: item.dana_gereja
                            });
                        });

                        // Menambahkan data ke chart
                        var ctxKomisi = document.getElementById('pengeluaranKomisi').getContext('2d');

                        if (typeof chartKomisi !== 'undefined') {
                            chartKomisi.destroy(); // Hapus chart lama jika ada
                        }

                        chartKomisi = new Chart(ctxKomisi, {
                            type: 'bar', // Bar chart
                            data: {
                                labels: labels,  // Nama komisi
                                datasets: [{
                                    label: 'Total Pengeluaran',
                                    data: data,  // Total pengeluaran (dalam juta)
                                    backgroundColor: 'rgba(237, 189, 203, 0.7)',
                                }]
                            },
                            options: {
                                indexAxis: 'y', // Menampilkan label di sumbu Y
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    tooltip: {
                                        callbacks: {
                                            label: function (tooltipItem) {
                                                const index = tooltipItem.dataIndex;
                                                const detail = tooltipDetails[index];

                                                function formatRupiah(value) {
                                                    return 'Rp ' + Number(value).toLocaleString('id-ID', {
                                                        minimumFractionDigits: 0,
                                                        maximumFractionDigits: 0
                                                    });
                                                }

                                                return [
                                                    `Total Pengeluaran: ${formatRupiah(detail.total)}`,
                                                    `Dana Swadaya: ${formatRupiah(detail.swadaya)}`,
                                                    `Dana Gereja: ${formatRupiah(detail.gereja)}`
                                                ];
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                        },
                                        ticks: {
                                            callback: function (value) {
                                                return value + ' Jt'; // Menambahkan 'Jt' untuk juta
                                            }
                                        }
                                    },
                                    y: {
                                        ticks: {
                                            callback: function (value) {
                                                const label = this.getLabelForValue(value);
                                                const maxLength = 15;
                                                if (label.length > maxLength) {
                                                    const words = label.split(' ');
                                                    let lines = [];
                                                    let currentLine = '';

                                                    words.forEach(word => {
                                                        if ((currentLine + word).length <= maxLength) {
                                                            currentLine += (currentLine ? ' ' : '') + word;
                                                        } else {
                                                            if (currentLine) lines.push(currentLine);
                                                            currentLine = word;
                                                        }
                                                    });

                                                    if (currentLine) lines.push(currentLine);
                                                    return lines;
                                                } else {
                                                    return label;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching data: ", status, error);
                    }
                });

                // Ajax untuk realisasi anggaran bidang komisi
                updateJudulKegiatan();
                loadChartKegiatan(id_bidang, id_komisi, start_month, end_month);

                function loadChartKegiatan(id_bidang, id_komisi, start_month, end_month, bulan = "") {
                    bulanAktifKegiatan = bulan;
                    $.ajax({
                        url: "chartAnggaranRealisasiBidKom.php",
                        method: "GET",
                        dataType: "json",
                        data: {
                            tahun: tahunKegiatan,
                            bulan: bulanAktifKegiatan,
                            limit: limitKegiatan,
                            page: pageKegiatan,
                            id_bidang,
                            id_komisi,
                            start_month,
                            end_month
                        },
                        success: function (response) {
                            if (!response.data || response.data.length === 0) {
                                alert('Belum ada data');
                                if (myChartKegiatan) myChartKegiatan.destroy();
                                $("#pageInfoKegiatanKomisi").text("");
                                return;
                            }

                            const labels = response.data.map(item => item.komisi);
                            const anggaranData = response.data.map(item => item.anggaran);
                            const pengeluaranData = response.data.map(item => item.pengeluaran);

                            totalPagesKegiatan = response.totalPages;
                            updatePaginationKegiatan();

                            if (myChartKegiatan) myChartKegiatan.destroy();

                            myChartKegiatan = new Chart(ctxKegiatan, {
                                type: 'bar',
                                data: {
                                    labels,
                                    datasets: [
                                        {
                                            label: 'Rencana',
                                            data: anggaranData,
                                            backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                            yAxisID: 'y',
                                            barPercentage: 0.6,
                                            categoryPercentage: 0.7,
                                            barThickness: 10
                                        },
                                        {
                                            label: 'Realisasi',
                                            data: pengeluaranData,
                                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                                            yAxisID: 'y',
                                            barPercentage: 0.6,
                                            categoryPercentage: 0.7,
                                            barThickness: 10
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    interaction: { mode: 'index', intersect: false },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function (context) {
                                                    const index = context.dataIndex;
                                                    const item = response.data[index];
                                                    if (context.dataset.label === 'Rencana') {
                                                        return [
                                                            `Rencana: ${formatRupiah(item.anggaran)}`,
                                                            `  - Dana Swadaya: ${formatRupiah(item.dana_swadaya_anggaran)}`,
                                                            `  - Dana Gereja: ${formatRupiah(item.dana_gereja_anggaran)}`
                                                        ];
                                                    } else {
                                                        return [
                                                            `Realisasi: ${formatRupiah(item.pengeluaran)}`,
                                                            `  - Dana Swadaya: ${formatRupiah(item.dana_swadaya_pengeluaran)}`,
                                                            `  - Dana Gereja: ${formatRupiah(item.dana_gereja_pengeluaran)}`
                                                        ];
                                                    }
                                                },
                                                footer: function (tooltipItems) {
                                                    const index = tooltipItems[0].dataIndex;
                                                    const item = response.data[index];
                                                    return `Persentase Realisasi: ${item.persentase_realisasi.toFixed(2)}%`;
                                                }
                                            }
                                        }
                                    },
                                    scales: {
                                        x: {
                                            ticks: {
                                                autoSkip: false,
                                                maxRotation: 25,
                                                minRotation: 25,
                                                callback: function (value) {
                                                    const label = this.getLabelForValue(value);
                                                    const maxLength = 17;
                                                    return label.length > maxLength ? label.substring(0, maxLength - 1) + '…' : label;
                                                },
                                            }
                                        },
                                        y: {
                                            min: 10000,
                                            ticks: {
                                                callback: function (value) {
                                                    if (value >= 1_000_000) return (value / 1_000_000).toLocaleString('id-ID') + ' Jt';
                                                    if (value >= 1_000) return (value / 1_000).toLocaleString('id-ID') + ' Rb';
                                                    return value.toLocaleString('id-ID');
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        },
                        error: function (xhr, status, error) {
                            console.error("Gagal load data:", error);
                        }
                    });
                }

                function formatRupiah(value) {
                    return new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                }


                function updatePaginationKegiatan() {
                    $("#pageInfoKegiatanKomisi").text(`Page ${pageKegiatan} of ${totalPagesKegiatan}`);
                    $("#prevBtnKegiatanKomisi").prop('disabled', pageKegiatan <= 1);
                    $("#nextBtnKegiatanKomisi").prop('disabled', pageKegiatan >= totalPagesKegiatan);
                }

                function updateJudulKegiatan() {
                    const bulanKegiatan = $("#monthDropdownKegiatanKomisi").val();
                    const bulanNama = [
                        '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];

                    let judul = 'Rencana dan Realisasi Pengeluaran Per Komisi ';
                    if (bulanKegiatan) {
                        judul += bulanNama[bulanKegiatan] + ' ';
                    }
                    judul += tahunKegiatan;

                    $("#judulKegiatan").text(judul);
                }

                $("#monthDropdownKegiatanKomisi").change(function () {
                    if (!currentFilter.id_bidang || !currentFilter.id_komisi) {
                        alert("Silakan pilih bidang dan komisi terlebih dahulu.");
                        return;
                    }

                    bulanAktifKegiatan = $(this).val();
                    pageKegiatan = 1;
                    loadChartKegiatan(
                        currentFilter.id_bidang,
                        currentFilter.id_komisi,
                        currentFilter.start_month,
                        currentFilter.end_month,
                        bulanAktifKegiatan
                    );
                    updateJudulKegiatan();
                });

                // Pagination buttons
                $("#prevBtnKegiatanKomisi").click(function () {
                    if (pageKegiatan > 1) {
                        pageKegiatan--;
                        loadChartKegiatan(
                            currentFilter.id_bidang,
                            currentFilter.id_komisi,
                            currentFilter.start_month,
                            currentFilter.end_month,
                            bulanAktifKegiatan
                        );
                        updateJudulKegiatan();
                    }
                });

                $("#nextBtnKegiatanKomisi").click(function () {
                    if (pageKegiatan < totalPagesKegiatan) {
                        pageKegiatan++;
                        loadChartKegiatan(
                            currentFilter.id_bidang,
                            currentFilter.id_komisi,
                            currentFilter.start_month,
                            currentFilter.end_month,
                            bulanAktifKegiatan
                        );
                        updateJudulKegiatan();
                    }
                });


            });
        });
    </script>
    <!-- javascript -->
    <script>
        const tahunAktif = <?= json_encode($tahun_aktif) ?>;
    </script>
    <script>
        function getFilterURL(baseUrl) {
            const start = document.getElementById('start_month').value;
            const end = document.getElementById('end_month').value;
            const bidang = document.getElementById('bidang').value;
            const komisi = document.getElementById('komisi').value;
            const tahun = tahunAktif; 

            if (!start || !end || !bidang || !komisi) {
                alert("Lengkapi semua filter terlebih dahulu.");
                return null;
            }

            return `${baseUrl}?tahun=${tahun}&start=${start}&end=${end}&bidang=${bidang}&komisi=${komisi}`;
        }
        document.getElementById('exportKasBidKom').addEventListener('click', function (e) {
            e.preventDefault();
            const url = getFilterURL('cetakExcelKasBidKom.php');
            if (url) window.location.href = url;
        });
        document.getElementById('exportPengeluaranKegiatan').addEventListener('click', function (e) {
            e.preventDefault();
            const url = getFilterURL('cetakExcelAnggaranRealisasiBidangKomisi.php');
            if (url) window.location.href = url;
        });

        document.getElementById('exportPengeluaran').addEventListener('click', function (e) {
            e.preventDefault();
            const url = getFilterURL('cetakExcelPengeluaranKomisi.php');
            if (url) window.location.href = url;
        });
    </script>
    <script>
        document.getElementById("filterForm").addEventListener("submit", function (e) {
            const startMonth = parseInt(document.getElementById("start_month").value);
            const endMonth = parseInt(document.getElementById("end_month").value);

            if (startMonth === endMonth) {
                e.preventDefault();
                alert("Periode mulai dan akhir tidak boleh di bulan yang sama.");
            }
        });
    </script>

</body>
<footer style="
    margin-top: 40px;
    padding: 15px 0;
    text-align: center;
    background-color: #f8f9fa;
    color: #6c757d;
    font-size: 0.95rem;
   ">
    &copy; 2025 GKJ Dayu. All rights reserved.
</footer>

</html>