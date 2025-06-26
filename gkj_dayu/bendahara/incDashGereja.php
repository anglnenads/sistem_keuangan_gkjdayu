<?php
include_once("../_function_i/cConnect.php");
include_once("../_function_i/cView.php");
include_once("../_function_i/inc_f_object.php");

// Koneksi
$conn = new cConnect();
$conn->goConnect();

// Ambil tahun dan bulan aktif
if (isset($_POST['tahun'])) {
	$tahun_aktif = intval($_POST['tahun']);
	$_SESSION['tahun_aktif'] = $tahun_aktif; // simpan ke session
} else if (isset($_SESSION['tahun_aktif'])) {
	$tahun_aktif = $_SESSION['tahun_aktif']; // ambil dari session
} else {
	$tahun_aktif = date("Y"); // default tahun sekarang
}

if (isset($_POST['bulan'])) {
	$bulan_aktif = intval($_POST['bulan']);
	$_SESSION['bulan'] = $bulan_aktif; // Simpan bulan ke session
} elseif (isset($_SESSION['bulan'])) {
	$bulan_aktif = $_SESSION['bulan']; // Ambil bulan dari session
} else {
	$bulan_aktif = null; // Tidak ada bulan yang dipilih
}

$bulan_aktif = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? intval($_GET['bulan']) : null;

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

$bulan_text = $bulan_aktif ? $bulan_nama[$bulan_aktif] : '';

$penerimaan = isset($data['penerimaan']) ? $data['penerimaan'] : 0;
$pengeluaran = isset($data['pengeluaran']) ? $data['pengeluaran'] : 0;
$saldo = isset($data['saldo']) ? $data['saldo'] : 0;

// Format Rupiah
function formatRupiah($angka)
{
	return "Rp " . number_format($angka, 0, ',', '.');
}
?>


<!DOCTYPE html>
<html lang="en">

</html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
	<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
	<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
	<style>
		.custom-dark-blue {
			border-color: rgb(43, 80, 135) !important;
			color: rgb(33, 87, 168) !important;
		}

		.card-Kas {
			border: 1px solid rgba(0, 0, 0, 0.66);
			padding: 16px;
			border-radius: 12px;
			height: 100%;
			margin-right: -87px;
			margin-left: -37px;
			width: 115%;
		}

		.card-finance {
			background-color: rgba(161, 176, 204, 0.63);
			border-radius: 10px;
			padding: 13px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.18);
			display: flex;
			flex-direction: column;
			gap: 2px;
			transition: 0.68s;
			margin-left: -87px;
			max-width: 290px;
		}

		.card-finance:hover {
			box-shadow: 0 6px 12px rgba(0, 0, 0, 0.38);
		}

		.card-header {
			font-size: 1.1rem;
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 0
		}

		.card-header h5 {
			font-size: 18px;
			font-weight: bold;
			display: flex;
			flex-direction: column;
			gap: 4px;
			margin-bottom: 0;
		}

		.card-value {
			font-size: 24px;
			font-weight: bold;
			text-align: center;
		}


		.icon-income {
			color: rgb(26, 126, 26);
		}

		.icon-expense {
			color: rgb(240, 43, 17);
		}

		.icon-balance {
			size: 20px;
			color: rgb(24, 97, 119);
		}

		@media (max-width: 768px) {
			.row.align-items-start {
				flex-direction: column;
			}
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
						Dashboard Keuangan Gereja
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

	</div>

	<!-- Trend Prediksi -->
	<div class="row justify-content-center" style="margin: 20px;">
		<!-- Chart Penerimaan -->
		<div class="col-md-6">
			<div style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 16px; border-radius: 12px;">
				<h5 class="text-center">Trend Prediksi Penerimaan Gereja</h5>
				<canvas id="penerimaanChart" style="max-height: 300px; width: 100%;"></canvas>
				<div id="statusPenerimaan" class="mt-2 text-center fw-bold"></div>
			</div>
		</div>

		<!-- Chart Pengeluaran -->
		<div class="col-md-6">
			<div style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 16px; border-radius: 12px;">
				<h5 class="text-center">Trend Prediksi Pengeluaran Gereja</h5>
				<canvas id="pengeluaranChart" style="max-height: 300px; width: 100%;"></canvas>
				<div id="statusPengeluaran" class="mt-2 text-center fw-bold"></div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function () {
			$.ajax({
				url: 'chartTrend.php',
				method: 'GET',
				dataType: 'json',
				success: function (data) {
					if (!data || data.error) {
						console.error(data?.error || 'Data kosong');
						return;
					}

					let years = [], penerimaan = [], pengeluaran = [];
					let prediksiTahun = [], prediksiPenerimaan = [], prediksiPengeluaran = [];

					// Pastikan data ada sebelum pakai forEach
					if (Array.isArray(data.penerimaan)) {
						data.penerimaan.forEach(function (item) {
							years.push(item.tahun);
							penerimaan.push(item.nominal);
						});
					} else {
						console.warn('Data penerimaan tidak tersedia atau salah format.');
					}

					if (Array.isArray(data.pengeluaran)) {
						data.pengeluaran.forEach(function (item) {
							pengeluaran.push(item.nominal);
						});
					} else {
						console.warn('Data pengeluaran tidak tersedia atau salah format.');
					}

					// Validasi prediksi
					if (data.prediksi) {
						prediksiTahun.push(data.prediksi.tahun);
						prediksiPenerimaan.push(data.prediksi.penerimaan);
						prediksiPengeluaran.push(data.prediksi.pengeluaran);
					} else {
						console.warn('Data prediksi tidak tersedia.');
					}

					let penerimaanReg = data.model_regresi_nilai?.penerimaan || { slope: 0, intercept: 0 };
					let pengeluaranReg = data.model_regresi_nilai?.pengeluaran || { slope: 0, intercept: 0 };

					let nilaiPenerimaanAktual = data.nilai_aktual?.penerimaan || 0;
					let nilaiPengeluaranAktual = data.nilai_aktual?.pengeluaran || 0;

					// Opsi umum Chart
					let commonOptions = {
						responsive: true,
						scales: {
							x: { stacked: true, ticks: { align: 'center' } },
							y: {
								min: 700000000,
								ticks: {
									stepSize: 50000000,
									callback: function (value) {
										if (value >= 1000000000) return (value / 1000000000) + ' M';
										if (value >= 1000000) return (value / 1000000) + ' Jt';
										if (value >= 1000) return (value / 1000) + ' Rb';
										return value;
									}
								}
							}
						},
						plugins: {
							tooltip: {
								callbacks: {
									label: function (context) {
										let value = context.raw;
										return context.dataset.label + ': Rp ' + value.toLocaleString('id-ID');
									}
								}
							},
							legend: {
								display: true,
								position: 'top'
							}
						}
					};

					// Penerimaan Chart
					let ctxPenerimaan = document.getElementById('penerimaanChart').getContext('2d');
					new Chart(ctxPenerimaan, {
						type: 'bar',
						data: {
							labels: years.concat(prediksiTahun),
							datasets: [
								{
									label: 'Penerimaan',
									data: penerimaan.concat(null),
									backgroundColor: 'rgba(0, 128, 0, 0.62)',
									borderColor: 'rgba(0, 128, 0, 0.62)',
									borderWidth: 1
								},
								{
									label: 'Prediksi Penerimaan',
									data: years.map(() => null).concat(prediksiPenerimaan),
									backgroundColor: 'rgba(0, 128, 0, 0.25)',
									borderColor: 'rgba(0, 128, 0, 0.25)',
									borderWidth: 1
								},
								{
									label: 'Trendline Penerimaan',
									data: years.concat(prediksiTahun).map(t => Math.round(penerimaanReg.slope * t + penerimaanReg.intercept)),
									type: 'line',
									borderColor: 'rgba(0, 128, 0, 0.85)',
									borderDash: [5, 5],
									fill: false,
									pointRadius: 0,
									tension: 0
								}
							]
						},
						options: commonOptions
					});

					// Pengeluaran Chart
					let ctxPengeluaran = document.getElementById('pengeluaranChart').getContext('2d');
					new Chart(ctxPengeluaran, {
						type: 'bar',
						data: {
							labels: years.concat(prediksiTahun),
							datasets: [
								{
									label: 'Pengeluaran',
									data: pengeluaran.concat(null),
									backgroundColor: 'rgba(226, 26, 26, 0.62)',
									borderColor: 'rgba(226, 26, 26, 0.62)',
									borderWidth: 1
								},
								{
									label: 'Prediksi Pengeluaran',
									data: years.map(() => null).concat(prediksiPengeluaran),
									backgroundColor: 'rgba(226, 26, 26, 0.25)',
									borderColor: 'rgba(226, 26, 26, 0.25)',
									borderWidth: 1
								},
								{
									label: 'Trendline Pengeluaran',
									data: years.concat(prediksiTahun).map(t => Math.round(pengeluaranReg.slope * t + pengeluaranReg.intercept)),
									type: 'line',
									borderColor: 'rgba(226, 26, 26, 0.85)',
									borderDash: [5, 5],
									fill: false,
									pointRadius: 0,
									tension: 0
								}
							]
						},
						options: commonOptions
					});

					// Tampilkan Status
					tampilkanStatus("penerimaanChart", "Penerimaan", prediksiPenerimaan[0], nilaiPenerimaanAktual, "penerimaan");
					tampilkanStatus("pengeluaranChart", "Pengeluaran", prediksiPengeluaran[0], nilaiPengeluaranAktual, "pengeluaran");
				},
				error: function () {
					console.error('❌ Gagal mengambil data dari chartTrend.php');
				}
			});

			function tampilkanStatus(canvasId, label, prediksi, aktual, tipe) {
				let el = document.getElementById("status" + label);
				if (!el) return;

				if (aktual === null || aktual === undefined || aktual === 0) {
					el.innerHTML = `Data aktual <strong>${label.toLowerCase()}</strong> tahun ini belum tersedia.`;
					el.style.color = '#888';
					return;
				}

				let persen = Math.round((aktual / prediksi) * 10000) / 100;
				let status = "", warna = "";

				if (tipe === "penerimaan") {
					if (persen >= 100) {
						status = "Melebihi Target!";
						warna = "green";
					} else {
						status = "Belum Mencapai Target.";
						warna = "orange";
					}
				} else if (tipe === "pengeluaran") {
					if (persen >= 100) {
						status = "Pengeluaran Melebihi Prediksi!";
						warna = "red";
					} else if (persen >= 85) {
						status = "Pengeluaran Hampir Melebihi Batas!";
						warna = "orangered";
					} else if (persen >= 60) {
						status = "Pengeluaran Mendekati Batas Prediksi.";
						warna = "orange";
					} else {
						status = "Pengeluaran Masih Terkendali.";
						warna = "green";
					}
				}

				el.innerHTML = `Pencapaian ${label} Rp ${aktual.toLocaleString('id-ID')} (<strong>${persen}%</strong>) dari prediksi Rp ${prediksi.toLocaleString('id-ID')} <br><span style="color:${warna};">${status}</span>`;
			}

		});
	</script>


	<div class="container" style="margin-top: 20px;">
		<div class="row align-items-start g-3">
			<!-- Card Ringkasan Financial -->
			<div class="col-md-3 d-flex flex-column" style="gap: 69px;">

				<!-- Card Penerimaan -->
				<div class="card-finance">
					<div class="card-header">
						<h5><i class="fas fa-hand-holding-usd icon-income"></i> Penerimaan
							<span class="month-label"><?php echo $bulan_text . ' ' . $tahun_aktif; ?></span>
						</h5>
					</div>
					<p id="penerimaan-value" class="card-value">
						<?= formatRupiah($penerimaan) ?>
					</p>
				</div>

				<!-- Card Pengeluaran -->
				<div class="card-finance">
					<div class="card-header">
						<h5><i class="fas fa-credit-card icon-expense"></i> Pengeluaran
							<span class="month-label"><?php echo $bulan_text . ' ' . $tahun_aktif; ?></span>
						</h5>
					</div>
					<p id="pengeluaran-value" class="card-value">
						<?= formatRupiah($pengeluaran) ?>
					</p>
				</div>

				<!-- Card Saldo -->
				<div class="card-finance">
					<div class="card-header">
						<h5><i class="fas fa-wallet icon-balance"></i> Sisa Saldo
							<span class="month-label"><?php echo $bulan_text . ' ' . $tahun_aktif; ?></span>
						</h5>
					</div>
					<p id="saldo-value" class="card-value">
						<?= formatRupiah($saldo) ?>
					</p>
				</div>
			</div>

			<!-- Grafik Kas Gereja -->
			<div class="col-md-9">
				<div class="card-Kas">
					<div class="position-relative text-center mb-3">
						<h5 id="grafikTitle" class="m-0">
							Grafik Kas Gereja
							<?php echo !empty($bulan_text) ? $bulan_text . ' ' . $tahun_aktif : 'Bulan tidak valid ' . $tahun_aktif; ?>
						</h5>

						<a id="exportExcelBtn" href="#"
							class="btn btn-success position-absolute top-0 end-0 d-flex align-items-center"
							role="button" style="height: 150%; width: 70px;">
							<i class="bi bi-printer me-1"></i>.xls
						</a>
					</div>

					<!-- Dropdown Pilih Bulan -->
					<div class="text-center mb-3">
						<label for="monthDropdown">Pilih Bulan:</label>
						<select id="monthDropdown" class="form-control d-inline-block" style="width: auto;">
							<option value="">Semua Bulan</option>
							<?php
							foreach ($bulan_nama as $bulan_num => $bulan_label) {
								$selected = ($bulan_aktif == $bulan_num) ? 'selected' : '';
								echo "<option value=\"$bulan_num\" $selected>$bulan_label</option>";
							}
							?>
						</select>
						<script>
							document.getElementById('exportExcelBtn').addEventListener('click', function (e) {
								e.preventDefault();
								const selectedMonth = document.getElementById('monthDropdown').value;
								const selectedYear = document.getElementById('selected_year')?.value || '<?php echo $tahun_aktif; ?>';

								let url = `cetakExcelKasGereja.php?tahun_aktif=${selectedYear}`;
								if (selectedMonth) {
									url += `&bulan=${selectedMonth}`;
								}

								window.location.href = url;
							});

						</script>
					</div>
					<!-- Canvas Chart -->
					<canvas id="lineKasGereja" width="100%" height="50"></canvas>
				</div>
			</div>
		</div>
	</div>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const monthDropdown = document.getElementById('monthDropdown');
			const monthLabel = document.querySelectorAll('.month-label'); // Gunakan kelas untuk mencari bulan di card
			const yearDropdown = document.getElementById('selected_year');

			const monthNames = [
				"Januari", "Februari", "Maret", "April", "Mei", "Juni",
				"Juli", "Agustus", "September", "Oktober", "November", "Desember"
			];

			var ctx = document.getElementById('lineKasGereja').getContext('2d');
			var lineKasGerejaChart = new Chart(ctx, {
				type: 'line',
				data: {
					labels: monthNames,
					datasets: [{
						label: "Penerimaan",
						data: [],
						borderColor: 'green',
						backgroundColor: 'rgba(173, 255, 173, 0.25)',
						fill: true,
						tension: 0.1
					},
					{
						label: "Pengeluaran",
						data: [],
						borderColor: 'red',
						backgroundColor: 'rgba(248, 31, 31, 0.25)',
						fill: true,
						tension: 0.1
					}]
				},
				options: {
					responsive: true,
					plugins: {
						title: {
							display: true,
						},
						tooltip: {
							callbacks: {
								label: function (tooltipItem) {
									return tooltipItem.dataset.label + ': Rp ' + (tooltipItem.raw / 1).toLocaleString('id-ID');
								}
							}
						}
					},
					scales: {
						x: {
							title: {
								display: true,
							}
						},
						y: {
							title: {
								display: true,
							},
							ticks: {
								callback: function (value) {
									// Shorten y-axis values to JT (e.g., 900000000 -> 900JT)
									if (value >= 1000000) {
										return (value / 1000000).toLocaleString('id-ID') + ' Jt'; // "JT" for million
									}
									return 'Rp ' + value.toLocaleString('id-ID'); // Display values less than a million in the regular format
								}
							}
						}
					}
				}
			});

			// Fungsi untuk memperbarui bulan dan tahun pada card summary
			function updateBulanText() {
				const selectedMonth = monthDropdown.value;
				const selectedYear = yearDropdown.value || '<?php echo $tahun_aktif; ?>';
				let bulanText = selectedMonth ? monthNames[selectedMonth - 1] : '';

				// Update bulan di card-ringkasan
				monthLabel.forEach(label => {
					label.textContent = bulanText + ' ' + selectedYear;
				});

				// Update grafik dan ringkasan card
				loadChartData(selectedYear, selectedMonth || null);
				loadSummaryCard(selectedYear, selectedMonth || null);
			}

			function updateChartTitle(selectedMonth, selectedYear) {
				const chartTitle = selectedMonth ? monthNames[selectedMonth - 1] + ' ' + selectedYear : ' ' + selectedYear;
				document.getElementById('grafikTitle').textContent = 'Penerimaan dan Pengeluaran Gereja ' + chartTitle;
			}

			function loadChartData(selectedYear, selectedMonth) {
				const monthNames = [
					"Januari", "Februari", "Maret", "April", "Mei", "Juni",
					"Juli", "Agustus", "September", "Oktober", "November", "Desember"
				];

				let url = 'chartKasGereja.php?tahun_aktif=' + selectedYear;
				if (selectedMonth) {
					url += '&bulan=' + selectedMonth;
				}

				fetch(url)
					.then(response => response.json())
					.then(data => {
						if (data.error) {
							console.error(data.error);
							return;
						}

						if (selectedMonth) {
							// Tampilkan per minggu
							const maxWeeks = 5;
							const mingguLabels = Array.from({ length: maxWeeks }, (_, i) => `Minggu ${i + 1}`);
							lineKasGerejaChart.data.labels = mingguLabels;

							const penerimaanData = new Array(maxWeeks).fill(0);
							const pengeluaranData = new Array(maxWeeks).fill(0);

							data.data.forEach(item => {
								const index = item.minggu_ke - 1;
								if (index >= 0 && index < maxWeeks) {
									penerimaanData[index] = item.penerimaan;
									pengeluaranData[index] = item.pengeluaran;
								}
							});

							lineKasGerejaChart.data.datasets[0].data = penerimaanData;
							lineKasGerejaChart.data.datasets[1].data = pengeluaranData;
						} else {
							// Tampilkan per bulan
							lineKasGerejaChart.data.labels = monthNames;

							const penerimaanData = new Array(12).fill(0);
							const pengeluaranData = new Array(12).fill(0);

							data.data.forEach(item => {
								const index = item.bulan - 1;
								if (index >= 0 && index < 12) {
									penerimaanData[index] = item.penerimaan;
									pengeluaranData[index] = item.pengeluaran;
								}
							});

							lineKasGerejaChart.data.datasets[0].data = penerimaanData;
							lineKasGerejaChart.data.datasets[1].data = pengeluaranData;
						}

						lineKasGerejaChart.update();
						updateChartTitle(selectedMonth, selectedYear);
					})
					.catch(error => {
						console.error('Error fetching data:', error);
					});
			}


			//  load card data
			function loadSummaryCard(selectedYear, selectedMonth) {
				let url = 'chartRingkasanKas.php?tahun_aktif=' + selectedYear;
				if (selectedMonth) {
					url += '&bulan=' + selectedMonth;
				}

				fetch(url)
					.then(response => response.json())
					.then(data => {
						const penerimaan = data.penerimaan ?? 0;
						const pengeluaran = data.pengeluaran ?? 0;
						const saldo = data.saldo ?? 0;

						// Update value di kartu
						document.getElementById('penerimaan-value').textContent = formatRupiah(penerimaan);
						document.getElementById('pengeluaran-value').textContent = formatRupiah(pengeluaran);
						document.getElementById('saldo-value').textContent = formatRupiah(saldo);
					})
					.catch(error => {
						console.error('Error fetching summary data:', error);
					});
			}

			// Format angka jadi format Rupiah
			function formatRupiah(angka) {
				return 'Rp ' + angka.toLocaleString('id-ID');
			}

			updateBulanText();

			monthDropdown.addEventListener('change', updateBulanText);
			yearDropdown.addEventListener('change', updateBulanText);
		});
	</script>

	<div class="row justify-content-end" style="margin: 20px;">
		<!-- Total Anggaran dan Total Realisasi -->
		<div class="col-md-5">
			<div style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 16px; border-radius: 12px;">
				<div class="position-relative text-center mb-3">
					<h5 id="judulTotal" class="text-center">Rencana dan Realisasi Pengeluaran Bidang
						<?php echo !empty($bulan_text) ? $bulan_text . ' ' . $tahun_aktif : $tahun_aktif; ?>
					</h5>
					<div>
						<a id="exportAnggaranRealisasiBidang" href="#"
							class="btn btn-success position-absolute top-0 end-0 d-flex align-items-center"
							role="button" style="height: 75%;">
							<i class="bi bi-printer me-1"></i>.xls
						</a>
					</div>
				</div>

				<!-- Filter Bulan -->
				<div class="text-center mb-3">
					<label for="bulanSelectKomisi">Pilih Bulan:</label>
					<select id="bulanSelectKomisi" class="form-control d-inline-block" style="width: auto;">
						<option value="">Semua Bulan</option>
						<?php foreach ($bulan_nama as $bulan_num => $bulan_label): ?>
							<option value="<?= $bulan_num ?>" <?= ($bulan_aktif == $bulan_num) ? 'selected' : '' ?>>
								<?= $bulan_label ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<script>
					document.getElementById('exportAnggaranRealisasiBidang').addEventListener('click', function (e) {
						e.preventDefault();
						const selectedMonth = document.getElementById('bulanSelectKomisi').value;
						const selectedYear = document.getElementById('selected_year')?.value || '<?php echo $tahun_aktif; ?>';

						let url = `cetakExcelTotalAnggaranRealisasi.php?tahun_aktif=${selectedYear}`;
						if (selectedMonth) {
							url += `&bulan=${selectedMonth}`;
						}

						window.location.href = url;
					});

				</script>
				<!-- Chart Container -->
				<div style="width: 600px; height: 400px; width: 100%;">
					<canvas id="anggaranVsRealisasiChart" style="height: 600px; width: 1000px;"></canvas>
				</div>

			</div>
		</div>

		<script>
			let anggaranVsRealisasiChart;

			async function fetchChartData() {
				const bulan = document.getElementById('bulanSelectKomisi').value;
				const response = await fetch(`chartTotalAnggaranRealisasi.php?tahun=<?php echo $tahun_aktif; ?>&bulan=${bulan}`);
				const result = await response.json();
				return result.data;
			}

			function formatRupiah(angka) {
				return angka.toLocaleString('id-ID');
			}

			function breakLabel(label) {
				const words = label.split(" ");
				if (words.length <= 2) return label;
				const firstLine = words.slice(0, 2).join(" ");
				const secondLine = words.slice(2).join(" ");
				return firstLine + "\n" + secondLine;
			}

			async function createBarChart() {
				const data = await fetchChartData();

				const labels = data.map(item => breakLabel(item.nama_bidang));
				const anggaranData = data.map(item => item.total_anggaran);
				const realisasiData = data.map(item => item.total_realisasi);

				if (anggaranVsRealisasiChart) anggaranVsRealisasiChart.destroy();

				anggaranVsRealisasiChart = new Chart(document.getElementById('anggaranVsRealisasiChart'), {
					type: 'bar',
					data: {
						labels: labels,
						datasets: [
							{
								label: 'Rencana',
								data: anggaranData,
								backgroundColor: '#4e73df',
								borderColor: '#4e73df',
								barPercentage: 0.6,
								categoryPercentage: 0.7,
								barThickness: 15
							},
							{
								label: 'Realisasi',
								data: realisasiData,
								backgroundColor: '#a23e48',
								borderColor: '#a23e48',
								barPercentage: 0.6,
								categoryPercentage: 0.7,
								barThickness: 15
							}
						]
					},
					options: {
						responsive: true,
						maintainAspectRatio: false,
						indexAxis: 'x',

						plugins: {
							tooltip: {
								mode: 'index',
								intersect: false,
								callbacks: {
									title: (tooltipItems) => {
										const item = data[tooltipItems[0].dataIndex];
										return breakLabel(item.nama_bidang).split('\n');
									},
									label: (tooltipItem) => {
										const index = tooltipItem.dataIndex;
										const datasetLabel = tooltipItem.dataset.label;
										const value = tooltipItem.raw;
										const info = data[index];

										if (datasetLabel === 'Rencana') {
											return [
												`Rencana: Rp ${value.toLocaleString('id-ID')}`,
												`  - Dana Gereja : Rp ${info.anggaran_gereja.toLocaleString('id-ID')}`,
												`  - Dana Swadaya: Rp ${info.anggaran_swadaya.toLocaleString('id-ID')}`
											];
										}

										if (datasetLabel === 'Realisasi') {
											return [
												`Realisasi: Rp ${value.toLocaleString('id-ID')}`,
												`  - Dana Gereja : Rp ${info.realisasi_gereja.toLocaleString('id-ID')}`,
												`  - Dana Swadaya: Rp ${info.realisasi_swadaya.toLocaleString('id-ID')}`
											];
										}

										return `${datasetLabel}: Rp ${value.toLocaleString('id-ID')}`;
									},
									afterBody: (tooltipItems) => {
										const index = tooltipItems[0].dataIndex;
										const info = data[index];
										const totalAnggaran = info.anggaran_gereja + info.anggaran_swadaya;
										const totalRealisasi = info.realisasi_gereja + info.realisasi_swadaya;
										const persentase = totalAnggaran > 0 ? ((totalRealisasi / totalAnggaran) * 100).toFixed(2) : 0;

										return `Persentase Realisasi: ${persentase}%`;
									}
								}
							},
							legend: {
								display: true,
								position: 'top'
							}
						},
						scales: {
							y: {
								min: 10000,
								ticks: {
									callback: function (value) {
										return value >= 1_000_000 ? (value / 1_000_000).toFixed(0) + ' Jt' :
											value >= 1_000 ? (value / 1_000).toFixed(0) + ' Rb' : value;
									}
								}
							},
							x: {
								ticks: {
									callback: function (value) {
										const label = this.getLabelForValue(value);
										const words = label.split(" ");
										if (words.length <= 2) return label;
										const firstLine = words.slice(0, 2).join(" ");
										const secondLine = words.slice(2).join(" ");
										return [firstLine, secondLine];
									}
								}

							}
						}
					}
				});
			}

			function updateJudul() {
				const bulanSelect = document.getElementById('bulanSelectKomisi');
				const selectedBulan = bulanSelect.value;
				const bulanNama = [
					"", "Januari", "Februari", "Maret", "April", "Mei", "Juni",
					"Juli", "Agustus", "September", "Oktober", "November", "Desember"
				];
				const tahun = <?php echo $tahun_aktif; ?>;

				let judul = `Rencana dan Realisasi Pengeluaran<br>`;
				judul += `Bidang`;
				if (selectedBulan) {
					judul += ` ${bulanNama[parseInt(selectedBulan)]} ${tahun}`;
				} else {
					judul += ` ${tahun}`;
				}

				document.getElementById("judulTotal").innerHTML = judul;
			}

			window.onload = () => {
				updateJudul();
				createBarChart();
			};

			document.getElementById('bulanSelectKomisi').addEventListener('change', () => {
				updateJudul();
				createBarChart();
			});
		</script>

		<!-- Anggaran Realisasi Komisi -->
		<div class="col-md-7">
			<div style="border: 1px solid rgba(0, 0, 0, 0.66); padding: 16px; border-radius: 12px;">
				<div class="position-relative text-center mb-3">
					<h5 id="judulChartKomisi" class="m-0">
						Rencana dan Realisasi Pengeluaran Komisi
						<?php echo !empty($bulan_text) ? "$bulan_text $tahun_aktif" : $tahun_aktif; ?>
					</h5>

					<a id="exportAnggaranRealisasi" href="#"
						class="btn btn-success position-absolute top-0 end-0 d-flex align-items-center" role="button"
						style="height: 150%; width: 70px;">
						<i class="bi bi-printer me-1"></i>.xls
					</a>
				</div>

				<!-- Filter Bulan -->
				<div class="text-center mb-3">
					<label for="bulanFilterKomisi">Pilih Bulan:</label>
					<select id="bulanFilterKomisi" class="form-control d-inline-block" style="width: auto;">
						<option value="">Semua Bulan</option>
						<?php foreach ($bulan_nama as $bulan_num => $bulan_label): ?>
							<option value="<?= $bulan_num ?>" <?= ($bulan_aktif == $bulan_num) ? 'selected' : '' ?>>
								<?= $bulan_label ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<script>
					document.getElementById('exportAnggaranRealisasi').addEventListener('click', function (e) {
						e.preventDefault();
						const selectedMonth = document.getElementById('bulanFilterKomisi').value;
						const selectedYear = document.getElementById('selected_year')?.value || '<?php echo $tahun_aktif; ?>';

						let url = `cetakExcelAnggaranRealisasi.php?tahun_aktif=${selectedYear}`;
						if (selectedMonth) {
							url += `&bulan=${selectedMonth}`;
						}

						window.location.href = url;
					});

				</script>

				<!-- Chart -->
				<div style="width: 600px; height: 378px; width: 100%;">
					<canvas id="chartKomisi" style="height: 600px; width: 1000px;"></canvas>
				</div>

				<!-- Pagination -->
				<div class="d-flex justify-content-end mt-3" style="gap: 8px;">
					<button id="prevPageKomisi" class="btn btn-sm"
						style="background-color: #f5f5dc; color: black; border: 1px solid #ccc;">❮</button>
					<span id="pageInfoKomisi" class="align-self-center" style="font-size: 13px;"> </span>
					<button id="nextPageKomisi" class="btn btn-sm"
						style="background-color: #f5f5dc; color: black; border: 1px solid #ccc;">❯</button>
				</div>
			</div>
		</div>

		<script>
			const tahunAktif = <?= json_encode($tahun_aktif) ?>;
			let bulanAktif = <?= json_encode($bulan_aktif ?? null) ?>;
			let chartKomisi;
			let currentPage = 1;
			let totalPages = 1;

			async function loadChartData(page = 1) {
				currentPage = page;

				try {
					let url = `chartRealisasiAnggaran.php?tahun=${tahunAktif}&page=${page}`;
					if (bulanAktif) url += `&bulan=${bulanAktif}`;

					const response = await fetch(url);
					if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

					const result = await response.json();
					const bulanLabel = document.querySelector('#bulanFilterKomisi option:checked').textContent;
					const judul = `Rencana dan Realisasi Pengeluaran Komisi ${bulanAktif ? bulanLabel + ' ' + tahunAktif : tahunAktif}`;
					document.getElementById('judulChartKomisi').textContent = judul;

					if (!result.data || result.data.length === 0) {
						console.warn("Tidak ada data ditemukan.");
						if (chartKomisi) chartKomisi.destroy();
						document.getElementById('pageInfoKomisi').textContent = `Page ${currentPage} of ${totalPages}`;
						disableButtons();
						return;
					}

					totalPages = result.totalPages || 1;

					const labels = [];
					const anggaranData = [];
					const pengeluaranData = [];
					const tooltipsInfo = [];

					result.data.forEach(item => {
						labels.push(item.nama_komisi);
						anggaranData.push(parseInt(item.total_anggaran));
						pengeluaranData.push(parseInt(item.total_pengeluaran));
						tooltipsInfo.push({
							anggaranSwadaya: parseInt(item.total_dana_swadaya_anggaran),
							anggaranGereja: parseInt(item.total_dana_gereja_anggaran),
							realisasiSwadaya: parseInt(item.total_dana_swadaya_pengeluaran),
							realisasiGereja: parseInt(item.total_dana_gereja_pengeluaran),
							persentase: parseFloat(item.persentase_realisasi).toFixed(2)
						});
					});

					if (chartKomisi) chartKomisi.destroy();

					const ctx = document.getElementById('chartKomisi').getContext('2d');
					chartKomisi = new Chart(ctx, {
						type: 'bar',
						data: {
							labels: labels,
							datasets: [
								{
									label: 'Rencana',
									data: anggaranData,
									backgroundColor: 'rgba(54, 162, 235, 0.8)',
									barPercentage: 0.6,
									categoryPercentage: 0.7,
									barThickness: 10
								},
								{
									label: 'Realisasi',
									data: pengeluaranData,
									backgroundColor: 'rgba(255, 99, 132, 0.8)',
									barPercentage: 0.6,
									categoryPercentage: 0.7,
									barThickness: 10
								}
							]
						},
						options: {
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								tooltip: {
									mode: 'index',
									intersect: false,
									backgroundColor: 'rgba(0, 0, 0, 0.7)',
									titleColor: '#fff',
									callbacks: {
										label: context => {
											const datasetLabel = context.dataset.label;
											const value = context.parsed.y;
											const index = context.dataIndex;
											const info = tooltipsInfo[index];

											if (datasetLabel === 'Rencana') {
												return [
													`Rencana: Rp ${value.toLocaleString('id-ID')}`,
													`  - Dana Gereja : Rp ${info.anggaranGereja.toLocaleString('id-ID')}`,
													`  - Dana Swadaya: Rp ${info.anggaranSwadaya.toLocaleString('id-ID')}`,
												];
											}

											if (datasetLabel === 'Realisasi') {
												return [
													`Realisasi: Rp ${value.toLocaleString('id-ID')}`,
													`  - Dana Gereja : Rp ${info.realisasiGereja.toLocaleString('id-ID')}`,
													`  - Dana Swadaya: Rp ${info.realisasiSwadaya.toLocaleString('id-ID')}`,
												];
											}

											return `${datasetLabel}: Rp ${value.toLocaleString('id-ID')}`;
										},
										footer: tooltipItems => {
											const index = tooltipItems[0].dataIndex;
											const info = tooltipsInfo[index];
											return `Persentase Realisasi: ${info.persentase}%`;
										}
									}

								},
								legend: {
									labels: {
										color: '#333',
										font: { size: 12 }
									}
								}
							},
							scales: {
								y: {
									min: 10000,
									ticks: {
										callback: value => {
											if (value >= 1_000_000) return (value / 1_000_000).toFixed(0) + ' Jt';
											if (value >= 1_000) return (value / 1_000).toFixed(0) + ' Rb';
											return value;
										}
									}
								},
								x: {
									ticks: {
										autoSkip: false,
										maxRotation: 25,
										minRotation: 25,
										callback: function (value) {
											const label = this.getLabelForValue(value);
											const maxLength = 17; // panjang maksimal karakter label sebelum dipotong

											if (label.length > maxLength) {
												return label.substring(0, maxLength - 1) + '…';
											}
											return label;
										}
									}
								}
							}
						}
					});

					document.getElementById('pageInfoKomisi').textContent = `Page ${currentPage} of ${totalPages}`;
					disableButtons();
				} catch (error) {
					console.error('Gagal load data:', error);
					if (chartKomisi) chartKomisi.destroy();
				}
			}

			function disableButtons() {
				document.getElementById('prevPageKomisi').disabled = (currentPage <= 1);
				document.getElementById('nextPageKomisi').disabled = (currentPage >= totalPages);
			}

			document.getElementById('bulanFilterKomisi').addEventListener('change', function () {
				bulanAktif = this.value || null;
				loadChartData(1);
			});

			document.getElementById('prevPageKomisi').addEventListener('click', () => {
				if (currentPage > 1) loadChartData(currentPage - 1);
			});

			document.getElementById('nextPageKomisi').addEventListener('click', () => {
				loadChartData(currentPage + 1);
			});

			loadChartData();

		</script>
	</div>
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