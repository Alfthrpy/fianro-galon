<?php 
include "nav.php"; 
?>
<div class="container-fluid py-3">
    <?php
    require_once __DIR__ . '/../config/db.php';

    $query_chart = $conn->query("
    SELECT 
        DATE(tanggal) AS tgl,
        SUM(dibayar) + SUM(hutang) AS total_pendapatan,
        SUM(hutang) AS total_hutang
    FROM transaksi
    WHERE YEAR(tanggal) = YEAR(CURDATE()) AND MONTH(tanggal) = MONTH(CURDATE())
    GROUP BY DATE(tanggal)
    ORDER BY tgl ASC
    ");

    $initial_labels = [];
    $initial_data_pendapatan = [];
    $initial_data_hutang = [];

    while ($row = $query_chart->fetch_assoc()) {
        $initial_labels[] = date('d M Y', strtotime($row['tgl']));
        $initial_data_pendapatan[] = $row['total_pendapatan'];
        $initial_data_hutang[] = $row['total_hutang'];
    }
    ?>

    <div class="alert alert-primary border-0" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Selamat datang!</strong> Anda berada di halaman dashboard.
    </div>

    <div class="card mt-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-md-0">Grafik Keuangan</h5>
            <div class="btn-group flex-wrap" role="group" id="filter-buttons">
                <button type="button" class="btn btn-sm btn-outline-primary active" data-filter="bulan">Bulan Ini</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-filter="minggu">Minggu Ini</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-filter="hari">Hari Ini</button>
                <button type="button" class="btn btn-sm btn-outline-primary" data-filter="tahun">Tahun Ini</button>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

</div>

<style>
  .chart-container {
    position: relative;
    width: 100%;
    min-height: 250px;
    height: auto;
  }

  @media (max-width: 576px) {
    #filter-buttons {
      flex-wrap: wrap;
      gap: 4px;
    }
    #filter-buttons .btn {
      flex: 1 1 45%;
      font-size: 12px;
      padding: 4px 6px;
    }
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    var ctx = document.getElementById('myChart').getContext('2d');

    // Gradient untuk chart
    var gradientPendapatan = ctx.createLinearGradient(0, 0, 0, 300);
    gradientPendapatan.addColorStop(0, 'rgba(94, 114, 228, 0.5)');
    gradientPendapatan.addColorStop(1, 'rgba(94, 114, 228, 0)');

    var gradientHutang = ctx.createLinearGradient(0, 0, 0, 300);
    gradientHutang.addColorStop(0, 'rgba(245, 54, 92, 0.5)');
    gradientHutang.addColorStop(1, 'rgba(245, 54, 92, 0)');

    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($initial_labels); ?>,
            datasets: [{
                label: "Total Pendapatan",
                borderColor: '#5e72e4',
                backgroundColor: gradientPendapatan,
                data: <?php echo json_encode($initial_data_pendapatan); ?>,
                fill: true,
                tension: 0.3
            }, {
                label: "Total Hutang",
                borderColor: '#f5365c',
                backgroundColor: gradientHutang,
                data: <?php echo json_encode($initial_data_hutang); ?>,
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });

    // --- Fungsi updateChart ---
    async function updateChart(filter) {
        try {
            const response = await fetch(`pages/get_chart_data.php?filter=${filter}`);
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const newData = await response.json();

            myChart.data.labels = newData.labels;
            myChart.data.datasets[0].data = newData.data_pendapatan;
            myChart.data.datasets[1].data = newData.data_hutang;
            myChart.update();

        } catch (error) {
            console.error('Gagal mengambil data chart:', error);
        }
    }

    document.getElementById('filter-buttons').addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            this.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const filter = event.target.dataset.filter;
            updateChart(filter);
        }
    });
</script>

<?php include "base/footer.php"; ?>
