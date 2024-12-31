<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Posyandu</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            <router-link to="/kelola-posyandu">Kelola Posyandu</router-link>
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

    <!-- Alert -->
    <div class="row" v-show="isKek">
        <div class="col">
            <div class="card card-warning">
                <div class="card-header">
                    Peringatan <i class="fi fi-ss-siren-on"></i>

                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <!-- /.card-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body">Segera lakukan tindakan PMT pemulihan</div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
        aria-hidden="true">
        <div :class="`modal-dialog modal-dialog-centered ${!deleteMode ? 'modal-lg' : 'modal-md'
            }`">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">
                        Hapus Kegiatan ini?
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Apakah Anda Yakin Ingin Menghapus Kegiatan Ini?
                        </h5>
                    </div>
                </div>
                <div class="modal-footer" v-if="deleteMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteTask">
                        Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Posyandu -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">Informasi Posyandu</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">Nama Posyandu</div>
                        <div class="col-1">:</div>
                        <div class="text-left col-4">
                            {{ taskData.nama_posyandu }}
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-5">Ketua Kader</div>
                        <div class="col-1">:</div>
                        <div class="col">
                            {{ taskData.ketua_kader }}
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-5">Desa</div>
                        <div class="col-1">:</div>
                        <div class="col-4">
                            {{ taskData.desa }}
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-5">Alamat Lengkap</div>
                        <div class="col-1">:</div>
                        <div class="col-4">
                            {{ taskData.alamat_lengkap }}
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-5">
                            Jumlah Peserta WUS Pada Posyandu
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-4">
                            {{ jumlahwus }}
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-5">
                            Jumlah Peserta Balita Pada Posyandu
                        </div>
                        <div class="col-1">:</div>
                        <div class="col-4">
                            {{ jumlahbalita }}
                        </div>
                    </div>
                </div>
                <div class="card-footer" v-if="isKoordinator || isketuakader">
                    <div>
                        <select v-model="selectedYear" @change="exportData">
                            <option value="" disabled selected>Eksport Data</option>
                            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>
                </div>


                <!-- /.card-body -->
            </div>
        </div>

        <div class="col-md-4 banding">
            <div class="card border-primary mb-3">
                <div class="card-header">
                    Perbandingan kurang energi kronis dan normal
                </div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="350" :height="350" :options="pieKEKOptions" :series="pieKEKSeries">
                    </apexchart>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik perbandingan peserta -->
    <div class="row">
        <div class="col">
            <div class="card card-danger">
                <div class="card-header">
                    Perbandingan Peserta pada tahun ini
                </div>
                <apexchart type="bar" height="350" :options="chartOptions" :series="series" />
            </div>
        </div>
    </div>

    <!-- Line chart -->
    <div class="row">
        <div class="col">
            <div class="card card-info">
                <div class="card-header">
                    Perkembangan kurang energi kronis dan normal
                </div>
                <div class="card-body">
                    <div id="chart">
                        <apexchart ref="lineKekChart" type="line" :options="lineKekChartOptions"
                            :series="lineKekSeries"></apexchart>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel kegiatan -->
    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header">Tabel Kegiatan</div>
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal Kegiatan</th>

                                <th class="text-center">Tipe Kegiatan</th>
                                <th class="text-center">Total Peserta</th>
                                <th class="text-center">Detail</th>
                                <th class="text-center">Status Kegiatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in kegiatan" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ data.tgl_kegiatan }}
                                </td>
                                <td>{{ data.tipe_kegiatan }}</td>
                                <td class="text-center">
                                    <div v-if="data.tipe_kegiatan == 'WUS'">
                                        {{ data.periksawus_count }}
                                    </div>
                                    <div v-if="data.tipe_kegiatan == 'Balita'">
                                        {{ data.periksa_balita_count }}
                                    </div>
                                </td>
                                <td>{{ data.detail }}</td>
                                <td>{{ data.status_kegiatan }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-primary btn-sm" @click="redirect(data)">
                                        Detail
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm mt-2" @click="removeTask(data)"
                                        v-if="isKoordinator || isketuakader">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center">Tanggal Kegiatan</th>
                                <th class="text-center">Tipe Kegiatan</th>
                                <th class="text-center">Total Peserta</th>
                                <th class="text-center">Detail</th>
                                <th class="text-center">Status Kegiatan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import "datatables.net";
import "datatables.net-bs4";
import ApexCharts from "vue3-apexcharts";
import $ from "jquery";

export default {
    components: {
        apexchart: ApexCharts,
    },
    data() {
        return {
            selectedYear: "",
            years: Array.from({ length: new Date().getFullYear() - 2019 }, (_, i) => 2020 + i),
            baseUrl: window.url,
            bandingkek: [],
            targetPeserta: "",
            banyakPeserta: [],
            jumlahwus: "",
            jumlahbalita: "",
            kegiatan: [],
            deleteMode: false,
            isKek: false,
            isKoordinator: false,
            isketuakader: false,
            iskader: false,
            taskData: {
                id: "",
                ketua_kader: "",
                nama_posyandu: "",
                desa: "",
                alamat_lengkap: "",
            },
            series: [], // Data untuk ditampilkan di chart
            chartOptions: {
                chart: {
                    type: "bar",
                },
                xaxis: {
                    categories: [
                        "Januari",
                        "Februari",
                        "Maret",
                        "April",
                        "Mei",
                        "Juni",
                        "Juli",
                        "Agustus",
                        "September",
                        "Oktober",
                        "November",
                        "Desember",
                    ],
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            position: "top", // Posisi label data
                        },
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val;
                    },
                },
            },
            pieKEKSeries: [0, 0],
            pieKEKOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["KEK", "Normal"],
                colors: ["rgb(255,215,0)", "rgb(192,192,192))"],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: false,
                },
            },
            // Data and options for the Line chart
            lineKekSeries: [],
            lineKekChartOptions: {
                chart: {
                    height: 350,
                    type: "line",
                    dropShadow: {
                        enabled: true,
                        color: "#000",
                        top: 18,
                        left: 7,
                        blur: 10,
                        opacity: 0.2,
                    },
                    zoom: { enabled: false },
                    toolbar: { show: false },
                },
                colors: ["#77B6EA", "#545454"],
                dataLabels: { enabled: true },
                stroke: { curve: "smooth" },
                grid: {
                    borderColor: "#e7e7e7",
                    row: { colors: ["#f3f3f3", "transparent"], opacity: 0.5 },
                },
                markers: { size: 1 },
                xaxis: {
                    categories: [], // Initially empty
                    title: { text: "Month" },
                },
                yaxis: {
                    title: { text: "Jumlah" },
                    min: 0,
                },
                legend: {
                    position: "top",
                    horizontalAlign: "right",
                    floating: true,
                    offsetY: -25,
                    offsetX: -5,
                },
            },
        };
    },

    methods: {
        exportData() {
            const idPosyandu = this.$route.params.id; // Asumsi idPosyandu dari route
            const url = window.url + `api/export/posyandu/${idPosyandu}?tahun=${this.selectedYear}`;
            window.location.href = url; // Redirect ke endpoint download
        },
        async checkRole() {
            try {
                const token = localStorage.getItem('token'); // Ambil token dari localStorage

                const response = await axios.get(window.url + 'api/user/roles', {
                    headers: {
                        Authorization: `Bearer ${token}` // Tambahkan token ke header
                    }
                });

                const roles = response.data.roles; // Dapatkan role dari response
                const permissions = response.data.permissions; // Dapatkan permissions

                // console.log('Roles:', roles);
                // console.log('Permissions:', permissions);

                // Contoh penggunaan role
                if (roles.includes('koordinator')) {
                    // console.log('User adalah Koordinator');
                    this.isKoordinator = true; // Set state untuk role koordinator
                } else if (roles.includes('ketua kader')) {
                    // console.log('User adalah Ketua Kader');
                    this.isketuakader = true; // Set state untuk role ketua kader
                } else if (roles.includes('kader')) {
                    // console.log('User adalah Kader');
                    this.iskader = true; // Set state untuk role kader
                }
            } catch (error) {
                console.error('Gagal memeriksa role:', error.response?.data || error.message);
            }
        },
        getData() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/detail-posyandu/" + id)
                .then((response) => {
                    this.taskData = response.data; // Store the received data in taskData
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                });
        },

        redirect(task) {
            if (task.tipe_kegiatan === "WUS") {
                this.$router.push("/kegiatan-wus-active/" + task.id);
            } else if (task.tipe_kegiatan === "Balita") {
                this.$router.push("/kegiatan-balita-active/" + task.id);
            }
        },
        getJumlahWus() {
            axios
                .get(
                    window.url +
                    "api/getJumlahWusPosyandu/" +
                    this.$route.params.id
                )
                .then((response) => {
                    this.jumlahwus = response.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getJumlahBalita() {
            axios
                .get(
                    window.url +
                    "api/getJumlahBalitaPosyandu/" +
                    this.$route.params.id
                )
                .then((response) => {
                    this.jumlahbalita = response.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        getKegiatan() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/getKegiatan/" + id)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#example")) {
                        $("#example").DataTable().destroy();
                    }
                    this.kegiatan = response.data;
                    this.$nextTick(() => {
                        $("#example").DataTable({
                            pageLength: 10,
                            lengthChange: false,
                            destroy: true,
                        });
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        removeTask(data) {
            this.deleteMode = true;
            this.taskData.id = data.id;
            $("#taskModal").modal("show");
        },

        deleteTask() {
            axios
                .post(window.url + "api/deleteKegiatan/" + this.taskData.id)
                .then((response) => {
                    this.getKegiatan();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },

        closeModal() {
            $("#taskModal").modal("hide");
        },
        getAbsensi() {
            axios
                .get(
                    window.url +
                    "api/getPesertabulanan/" +
                    this.$route.params.id
                )
                .then((response) => {
                    const banyakPeserta = response.data; // Store fetched data

                    // Prepare data for the chart
                    this.series = [
                        {
                            name: "Realisasi Peserta WUS",
                            data: banyakPeserta.map((item) => ({
                                x: this.chartOptions.xaxis.categories[
                                    item.bulan - 1
                                ], // Menggunakan nama bulan
                                y: item.real_peserta_wus,
                                goals: [
                                    {
                                        name: "Target WUS",
                                        value: item.target_peserta_wus,
                                        strokeHeight: 5,
                                        strokeColor: "#775DD0",
                                    },
                                ],
                            })),
                        },
                        {
                            name: "Realisasi Peserta Balita",
                            data: banyakPeserta.map((item) => ({
                                x: this.chartOptions.xaxis.categories[
                                    item.bulan - 1
                                ], // Menggunakan nama bulan
                                y: item.real_peserta_balita,
                                goals: [
                                    {
                                        name: "Target Balita",
                                        value: item.target_peserta_balita,
                                        strokeHeight: 5,
                                        strokeColor: "#775DD0",
                                    },
                                ],
                            })),
                        },
                    ];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getKek() {
            axios
                .get(window.url + "api/getKekPos/" + this.$route.params.id)
                .then((response) => {
                    this.bandingkek = response.data;
                    let kek = 0;
                    let normal = 0;

                    // console.log("Data WUS:", this.bandingkek); // Log data yang diterima

                    this.bandingkek.forEach((item) => {
                        console.log(
                            `WUS ID: ${item.wus_id}, Lila Periksa: ${item.lila_periksa}`
                        ); // Log untuk setiap item
                        if (item.lila_periksa >= 23.5) {
                            normal += 1;
                        } else if (item.lila_periksa < 23.5) {
                            kek += 1;
                        }
                        if (kek > 0) {
                            this.isKek = true;
                        }
                    });
                    this.pieKEKSeries = [kek, normal];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getKekLast12MonthsPos() {
            axios
                .get(
                    window.url +
                    "api/getKekLast12MonthsPos/" +
                    this.$route.params.id
                )
                .then((response) => {
                    const kekData = [];
                    const normalData = [];
                    const months = [];

                    response.data.forEach((item) => {
                        kekData.push(item.KEK);
                        normalData.push(item.Normal);
                        months.push(item.month);
                    });

                    // Reverse the data arrays
                    const reversedKekData = kekData.reverse();
                    const reversedNormalData = normalData.reverse();
                    const reversedMonths = months.reverse();

                    this.lineKekSeries = [
                        { name: "KEK", data: reversedKekData },
                        { name: "Normal", data: reversedNormalData },
                    ];

                    // Update the chart options
                    this.lineKekChartOptions.xaxis.categories = reversedMonths;

                    this.$nextTick(() => {
                        // console.log("Chart Ref:", this.$refs.lineKekChart); // Cek apakah ref chart ada
                        if (this.$refs.lineKekChart) {
                            this.$refs.lineKekChart.updateOptions(
                                this.lineKekChartOptions
                            );
                        }
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
    },

    mounted() {
        this.getData();
        this.getKegiatan();
        this.getJumlahWus();
        this.getJumlahBalita();
        this.getAbsensi();
        this.getKek();
        this.getKekLast12MonthsPos();
        this.checkRole();
    },
};
</script>

<style scoped>
@media (max-width: 768px) {

    .card,
    .card-primary {
        font-size: 12px;
    }

    table thead th:nth-child(3),
    table tbody td:nth-child(3) {
        display: none;
    }

    table thead th:nth-child(4),
    table tbody td:nth-child(4) {
        display: none;
    }

    table td,
    table th {
        padding: 4px 6px;
        /* Atur padding sel agar lebih kecil */
        font-size: 12px;
        /* Perkecil ukuran font */
    }

    .table-header {
        background-color: #28a745;
        /* Hijau sesuai tema */
        color: white;
        padding: 6px 10px;
        text-align: center;
        font-weight: bold;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }


    .col-md-4,
    .banding {
        display: none;
    }

    .apexcharts-text tspan {
        font-family: inherit;
        font-size: 10px;
    }

    .apexcharts-bar-area {
        font-size: 9px;
        color: black;
    }
}
</style>
