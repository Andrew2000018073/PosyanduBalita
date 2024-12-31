<template>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="row w-100">

                <div class="col-lg-6 col-md-5 col-sm-6">
                    <select class="form-control select2 w-100" v-model="filters.posyandu_id" id="filter-posyandu">
                        <option value="">Semua Posyandu</option>
                        <option v-for="task in posyandus" :key="task.id" :value="task.id">
                            {{ task.nama_posyandu }}
                        </option>
                    </select>

                </div>


                <div class="col-lg-6 col-md-5 col-sm-6">
                    <select name=" filtergizi" id="filtergizi" class="form-control select2 w-100">
                        <option value="">Tampilkan daftar anak</option>
                        <option value="bbskurang">Anak Berat Badan Sangat Kurang</option>
                        <option value="bbkurang">Anak Berat Badan Kurang</option>
                        <option value="resikobblebih">Anak Dengan Resiko Berat Badan Lebih</option>
                        <option value="sangatPendek">Anak Sangat Pendek</option>
                        <option value="pendek">Anak Pendek</option>
                        <option value="giziBuruk">Anak Gizi Buruk</option>
                        <option value="giziKurang">Anak Gizi Kurang</option>
                        <option value="resikogzlebih">Anak Beresiko Gizi Lebih</option>
                        <option value="giziLebih">Anak Gizi Lebih</option>
                        <option value="obesitas">Anak Obesitas</option>
                    </select>
                </div>

            </div>
        </div>
    </nav>
    <!-- Pie chart BBU -->
    <div class="row mt-3">
        <div class="col-md-4 col-lg-6">
            <div class="card border-primary mb-3">
                <div class="card-header">
                    Perbandingan Gizi anak laki-laki terbaru
                </div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="500" :options="pieBBULOptions" :series="pieBBULSeries">
                    </apexchart>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-6">
            <div class="card border-primary mb-3">
                <div class="card-header">Perbandingan Gizi anak Perempuan terbaru</div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="500" :options="pieBBUPOptions" :series="pieBBUPSeries">
                    </apexchart>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie chart TBU -->
    <div class="row mt-3">
        <div class="col-md-4 col-lg-6">
            <div class="card border-primary mb-3">
                <div class="card-header">
                    Perbandingan Tinggi anak laki-laki terbaru
                </div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="500" :options="pieTBULOptions" :series="pieTBULSeries">
                    </apexchart>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-6">
            <div class="card border-primary mb-3">
                <div class="card-header">Perbandingan Tinggi anak Perempuan terbaru</div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="500" :options="pieTBUPOptions" :series="pieTBUPSeries">
                    </apexchart>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie chart BB/TB -->
    <div class="row mt-3">
        <div class="col-md-4 col-lg-6">
            <div class="card border-primary mb-3">
                <div class="card-header">
                    Perbandingan Gizi anak laki-laki terbaru
                </div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="500" :options="pieBBTBLOptions" :series="pieBBTBLSeries">
                    </apexchart>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-lg-6">
            <div class="card border-primary mb-3">
                <div class="card-header">Perbandingan Gizi anak Perempuan terbaru</div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="500" :options="pieBBTBPOptions" :series="pieBBTBPSeries">
                    </apexchart>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Pemeriksaan Perbulan -->
    <div class="row">
        <div class="col">
            <div class="card border-primary">

                <div class="card-body">
                    <div id="chart">
                        <apexchart ref="lineperiksaChart" type="line" :options="lineperiksaOptions"
                            :series="lineperiksaSeries"></apexchart>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Balitas -->
    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header">Tabel Wus</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablebayis" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Umur Peserta</th>
                                    <th class="text-center">Jenis Kelamin</th>
                                    <th class="text-center">Posyandu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Umur Peserta</th>
                                    <th class="text-center">Jenis Kelamin</th>
                                    <th class="text-center">Posyandu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
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
                        {{
                            deleteMode
                                ? "Hapus data anak "
                                : editMode
                                    ? "Perbarui data anak " + taskData.nama
                                    : hapusKontrasepsi
                                        ? "Hapus Alat Kontrasepsi " +
                                        taskData.nama_kontrasepsi
                                        : "Selesai memakai alat kontrasepsi ini?"
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="editMode && !deleteMode">
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <VueDatePicker v-model="taskData.tanggal_lahir" :format="'dd-MM-yyyy'"
                                :placeholder="'Silahkan masukan tanggal lahir'" :enable-time-picker="false">
                            </VueDatePicker>
                            <span class="text-danger" v-show="taskErrors.tanggal_lahir">Silahkan masukan TANGGAL
                                LAHIR!</span>
                        </div>

                        <div class="form-group">
                            <label for="tinggi_badan">Panjang badan lahir(cm)</label>
                            <input v-model="taskData.panjangbadan_lahir" type="number" min="0" max="1000" step="0.01"
                                class="form-control" id="tinggi_badan" placeholder="Masukan Panjang Badan(cm)" />
                            <span class="text-danger" v-show="taskErrors.panjangbadan_lahir">Silahkan masukan PANJANG
                                BADAN!</span>
                        </div>
                        <div class="form-group">
                            <label for="berat">Berat berat badan lahir(kg)</label>
                            <input v-model="taskData.beratbadan_lahir" type="number" min="0" max="1000" step="0.01"
                                class="form-control" id="berat" placeholder="Masukan berat badan(kg)" />
                            <span class="text-danger" v-show="taskErrors.beratbadan_lahir">Silahkan masukan BERAT
                                BADAN!</span>
                            <!-- <span class="text-danger">Silahkan masukan lila!</span> -->
                        </div>
                        <div class="form-group">
                            <label for="lila">Lingkar kepala lahir(cm)</label>
                            <input v-model="taskData.likep_lahir" type="number" min="0" max="1000" step="0.01"
                                class="form-control" id="lila" placeholder="Masukan Lingkar Kepala Bayi(cm)" />
                            <span class="text-danger" v-show="taskErrors.likep_lahir">Silahkan masukan LINGKAR
                                LENGAN!</span>
                        </div>
                        <div class="form-group">
                            <label for="Tempat Pelaksanaan">Tempat Pelaksanaan</label>
                            <select class="form-control select2" v-model="taskData.posyandus_id" style="width: 100%"
                                id="posyandu">
                                <option disabled value="">
                                    - Silahkan memilih tempat pelaksanaan -
                                </option>
                                <option v-for="task in posyandus" :key="task.id" :value="task.id">
                                    {{ task.nama_posyandu }}
                                </option>
                            </select>
                            <span class="text-danger" v-show="taskErrors.posyandus_id">Silahkan Pilih Tempat
                                Pelaksanaan!</span>
                        </div>
                    </div>
                    <div v-if="deleteMode && !editMode">
                        <h5 class="text-center text-danger">
                            Hapus data {{ taskData.nama }} ?
                        </h5>
                    </div>
                </div>
                <!-- Edit Mode -->
                <div class="modal-footer" v-if="!deleteMode && editMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="updateTask()" v-if="!deleteMode">
                        {{ editMode ? "Perbarui Data" : "Simpan Data" }}
                    </button>
                </div>
                <!-- Delete Mode -->
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

    <!-- Modal tampil sangat pendek -->
    <div class="modal fade" id="DaftarModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Daftar peserta terindikasi {{
                            resikobblebihMode
                                ? "Resiko Berat Badan Lebih"
                                : sangatPendekMode
                                    ? "Anak Sangat Pendek"
                                    : pendekMode
                                        ? "Anak Pendek"
                                        : bbskurangMode
                                            ? "Berat Sangat Kurang"
                                            : bbkurangMode
                                                ? "Berat Badan Kurang"
                                                : giziburukMode
                                                    ? "Gizi Buruk"
                                                    : gizikurangMode
                                                        ? "Gizi Kurang"
                                                        : resikogzlebihMode
                                                            ? "Resiko Gizi Lebih"
                                                            : giziLebihMode
                                                                ? "Gizi Lebih"
                                                                : obesitasMode
                                                                    ? "Obesitas"
                                                                    : "Error!"
                        }}
                    </h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-show="modaltampildata.length == 0">Belum ada peserta terindikasi</div>
                    <div v-show="modaltampildata.length > 0">
                        <table id="tablemodal" class="table table-striped table-bordered w-100" ref="imunisasiTable">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="card-footer">
                    <p class="text-center text-danger">Segera lakukan PMT pemulihan pada peserta yang terdaftar</p>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ApexCharts from "vue3-apexcharts";
import axios from "axios";
import Swal from "sweetalert2";
import "datatables.net";
import "datatables.net-bs4";
import $ from "jquery";
import "select2/dist/css/select2.css";
import "select2";
import { filter } from "lodash";
import { max } from "lodash";
import { parse } from 'date-fns';


export default {
    components: {
        VueDatePicker,
        apexchart: ApexCharts,
    },
    data() {
        return {
            format: "dd-MM-yyyy",
            date: null,
            balitas: [],
            status: [],
            posyandus: [],
            modaltampildata: [],
            modaltampildatapendek: [],
            modaltampildatabbskurang: [],
            modaltampildatabbkurang: [],
            modaltampildatasangatpendek: [],
            modalresikobblebih: [],
            modalgiziburuk: [],
            modalgizikurang: [],
            modalresikogzlebih: [],
            modalgiziLebih: [],
            modalobesitas: [],
            bandingberat: [],
            bandingtinggi: [],
            bandinggizi: [],
            jumlahwus: null,
            deleteMode: false,
            editMode: false,
            bbskurangMode: false,
            bbkurangMode: false,
            sangatpendekMode: false,
            pendekMode: false,
            resikobblebihMode: false,
            giziburukMode: false,
            gizikurangMode: false,
            resikogzlebihMode: false,
            gizilebihMode: false,
            obesitasMode: false,
            taskData: {
                id: "",
                tanggal_lahir: "",
                beratbadan_lahir: "",
                panjangbadan_lahir: "",
                likep_lahir: "",
                posyandus_id: "",
                date: "",
                balitas_id: "",
            },
            taskErrors: {
                jumlah_anak_hidup: false,
                lingkar_perut_suami: false,
                tanggal_lahir: false,
                beratbadan_lahir: false,
                panjangbadan_lahir: false,
                likep_lahir: false,
                posyandus_id: false,
            },
            filters: {
                posyandu_id: "",
            },
            // Data and options for the Pie chart
            pieSeries: [0, 0, 0, 0, 0],
            pieStatusOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                responsive: [
                    {
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300,
                            },
                            legend: {
                                position: "bottom",
                            },
                        },
                    },
                ],
                labels: [
                    "Tidak Menikah",
                    "Hamil",
                    "Menyusui",
                    "Menikah & tidak menyusui",
                    "Nifas",
                ],
                colors: [
                    "rgb(177, 214, 144)",
                    "rgb(254, 236, 55)",
                    "rgb(255, 162, 76)",
                    "rgb(255, 119, 183)",
                    "rgb(242, 191, 7)",
                ],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "bottom",
                },
            },

            pieBBULSeries: [0, 0, 0, 0],
            pieBBULOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Berat Badan Sangat Kurang", " Berat Badan Kurang", "Berat Badan Normal", "Resiko Berat Badan Lebih"],
                colors: ['#003f5c', '#7a5195', '#ef5675', '#ffa600'],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "right",
                },
            },
            pieBBUPSeries: [0, 0, 0, 0],
            pieBBUPOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Berat Badan Sangat Kurang", " Berat Badan Kurang", "Berat Badan Normal", "Resiko Berat Badan Lebih"],
                colors: ['#d81b60', '#039be5', '#ffb300', '#616161'],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "right",
                },
            },
            pieTBULSeries: [0, 0, 0, 0],
            pieTBULOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Sangat Pendek", "Pendek", "Normal", "Tinggi"],
                colors: ['#d81b60', '#039be5', '#ffb300', '#616161'],

                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "right",
                },
            },
            pieTBUPSeries: [0, 0, 0, 0],
            pieTBUPOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Sangat Pendek", "Pendek", "Normal", "Tinggi"],
                colors: ['#003f5c', '#7a5195', '#ef5675', '#ffa600'],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "right",
                },
            },
            pieBBTBLSeries: [0, 0, 0, 0, 0, 0],
            pieBBTBLOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Gizi Buruk", "Gizi Kurang", "Gizi Baik", "Beresiko Gizi Lebih", "Gizi Lebih", "Obesitas"],
                colors: ['#003f5c', '#7a5195', '#3d85c6', '#ffa600', '#7ED4AD', '#FF4191'],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "right",
                },
            },
            pieBBTBPSeries: [0, 0, 0, 0, 0],
            pieBBTBPOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Gizi Buruk", "Gizi Kurang", "Gizi Baik", "Beresiko Gizi Lebih", "Gizi Lebih", "Obesitas"],
                colors: ['#003f5c', '#7a5195', '#3d85c6', '#ffa600', '#7ED4AD', '#FF4191'],
                plotOptions: {
                    pie: {
                        expandOnClick: true,
                    },
                },
                legend: {
                    show: true,
                    position: "right",
                },
            },

            lineperiksaSeries: [],
            lineperiksaOptions: {
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
                title: {
                    text: "Rata-rata hasil periksa",
                    align: "left",
                },
                labels: ["Panjang Badan", "Berat Badan", "Llingkar Kepala"],
                colors: [
                    "#6929c4",
                    "#1192e8",
                    "#005d5d",
                ],
                dataLabels: { enabled: true },
                stroke: {
                    curve: "smooth",
                    connectNulls: true, // Menambahkan connectNulls
                },
                grid: {
                    borderColor: "#e7e7e7",
                    row: { colors: ["#f3f3f3", "transparent"], opacity: 0.5 },
                },
                markers: { size: 1 },
                xaxis: {
                    categories: [], // Kosong awalnya
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
    mounted() {
        this.getBayis();
        this.getPosyandu(); // Fetch tasks when component is mounted
        this.kasihPosyandu();
        this.getBeratBadanUmur();
        this.gettinggibadanumur();
        this.getPeriksa();
        this.getGizi();
        this.initializeSelect2(); // Initialize select2 after data is loaded
        window.navigateToDetail = (wusId) => {
            this.$router.push({ path: `/detail-wus/${wusId}` });
        };
    },
    beforeDestroy() {
        if ($("#my-select").data("select2")) {
            $("#my-select").select2("destroy");
        }
        if ($("#posyandu").data("select2")) {
            $("#posyandu").select2("destroy");
        }
    },

    methods: {
        getPeriksa() {
            axios
                .get(
                    window.url +
                    "api/getPeriksaLast12Monthsbalita/"
                )
                .then((response) => {
                    const panjang_badan = [];
                    const berat_badan = [];
                    const lingkep_periksa = [];
                    const months = [];

                    // Ubah nilai null menjadi 0 dan tambahkan ke array masing-masing
                    response.data.forEach((item) => {
                        panjang_badan.push(item.panjang_badan ?? 0);
                        berat_badan.push(item.berat_badan ?? 0);
                        lingkep_periksa.push(item.lingkep_periksa ?? 0);
                        months.push(item.month);
                    });

                    // Balik data agar urutan bulan benar
                    const reversedPanjang = panjang_badan.reverse();
                    const reversedBerat = berat_badan.reverse();
                    const reversedLingkep = lingkep_periksa.reverse();
                    const reversedMonths = months.reverse();

                    this.lineperiksaSeries = [
                        { name: "Panjang Badan", data: reversedPanjang },
                        { name: "Berat Badan", data: reversedBerat },
                        { name: "Lingkar Perut", data: reversedLingkep },
                    ];

                    this.lineperiksaOptions.xaxis.categories = reversedMonths;

                    this.$nextTick(() => {
                        if (this.$refs.lineperiksaChart) {
                            this.$refs.lineperiksaChart.updateOptions(
                                this.lineperiksaOptions
                            );
                        } else {
                            console.error(
                                "Chart ref not found or not initialized"
                            );
                        }
                    });
                })
                .catch((errors) => {
                    console.error("Error fetching data:", errors);
                });
        },
        getPeriksaPOS($id) {
            axios
                .get(
                    window.url +
                    "api/getPeriksaLast12MonthsbalitaPOS/" + $id
                )
                .then((response) => {
                    const panjang_badan = [];
                    const berat_badan = [];
                    const lingkep_periksa = [];
                    const months = [];

                    // Ubah nilai null menjadi 0 dan tambahkan ke array masing-masing
                    response.data.forEach((item) => {
                        panjang_badan.push(item.panjang_badan ?? 0);
                        berat_badan.push(item.berat_badan ?? 0);
                        lingkep_periksa.push(item.lingkep_periksa ?? 0);
                        months.push(item.month);
                    });

                    // Balik data agar urutan bulan benar
                    const reversedPanjang = panjang_badan.reverse();
                    const reversedBerat = berat_badan.reverse();
                    const reversedLingkep = lingkep_periksa.reverse();
                    const reversedMonths = months.reverse();

                    this.lineperiksaSeries = [
                        { name: "Panjang Badan", data: reversedPanjang },
                        { name: "Berat Badan", data: reversedBerat },
                        { name: "Lingkar Perut", data: reversedLingkep },
                    ];

                    this.lineperiksaOptions.xaxis.categories = reversedMonths;

                    this.$nextTick(() => {
                        if (this.$refs.lineperiksaChart) {
                            this.$refs.lineperiksaChart.updateOptions(
                                this.lineperiksaOptions
                            );
                        } else {
                            console.error(
                                "Chart ref not found or not initialized"
                            );
                        }
                    });
                })
                .catch((errors) => {
                    console.error("Error fetching data:", errors);
                });
        },
        reinitializeDataModal(data) {
            const plainData = JSON.parse(JSON.stringify(data));

            const table = $("#tablemodal");


            // Clear and destroy existing DataTable if present
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }

            // Initialize DataTable
            table.DataTable({
                data: plainData,
                processing: true,
                serverSide: false,
                columns: this.getDataModalColumns(),
            });

            $("#DaftarModal").modal("show");
        },

        getDataModalColumns() {
            return [
                { data: "nama", name: "nama" },
            ];
        },
        formatDate(date) {
            // Function to format the date as dd-mm-yyyy
            const [year, month, day] = new Date(date)
                .toISOString()
                .split("T")[0]
                .split("-");
            return `${day}-${month}-${year}`;
        },
        getPosyandu() {
            axios
                .get(window.url + "api/getPosyandu")
                .then((response) => {
                    this.tasks = response.data; // Simpan data ke properti tasks
                })
                .catch((errors) => {
                    console.log(errors); // Tangani kesalahan jika ada
                });
        },
        initializeSelect2() {
            const vm = this;

            // Destroy instance jika Select2 sudah diinisialisasi sebelumnya
            if ($.fn.select2) {
                if ($("#my-select").data("select2")) {
                    $("#my-select").select2("destroy");
                }
                if ($("#posyandu").data("select2")) {
                    $("#posyandu").select2("destroy");
                }
            }

            // Inisialisasi ulang my-select
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                    placeholder: "- Pilih opsi -",
                })
                .on("change", function () {
                    vm.taskData.someOtherField = $(this).val(); // Update model Vue sesuai kebutuhan
                });

            // Inisialisasi ulang posyandu
            $("#posyandu")
                .select2({
                    dropdownAutoWidth: true,
                    placeholder: "- Silahkan memilih tempat pelaksanaan -",
                    allowClear: true,
                })
                .on("change", function () {
                    vm.taskData.posyandus_id = $(this).val(); // Update model Vue saat Select2 berubah
                });

            if ($.fn.select2 && $("#filter-posyandu").data("select2")) {
                $("#filter-posyandu").select2("destroy");
            }

            // Initialize Select2
            $("#filter-posyandu")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    vm.filters.posyandu_id = $(this).val();
                    vm.applyFilters(); // Call applyFilters directly on change
                });

            if ($.fn.select2 && $("#filtergizi").data("select2")) {
                $("#filtergizi").select2("destroy");
            }

            // Initialize Select2
            $("#filtergizi")
                .select2({
                    placeholder: "Tampilkan daftar anak",
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    const selectedValue = $(this).val();
                    vm.handleFilterChange(selectedValue); // Pass selected value to handler
                });
        },

        handleFilterChange(selectedValue) {
            this.resetModes();

            // Tentukan modal data berdasarkan nilai pilihan
            switch (selectedValue) {
                case "sangatPendek":
                    this.sangatPendekMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modaltampildatasangatpendek;
                    break;
                case "pendek":
                    this.pendekMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modaltampildatapendek;
                    break;
                case "resikobblebih":
                    this.resikobblebihMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modalresikobblebih;
                    break;
                case "bbskurang":
                    this.bbskurangMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modaltampildatabbskurang;
                    break;
                case "bbkurang":
                    this.bbkurangMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modaltampildatabbkurang;
                    break;
                case "giziLebih":
                    this.giziLebihMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modalgiziLebih;
                    break;
                case "obesitas":
                    this.obesitasMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modalobesitas;
                    break;
                case "giziBuruk":
                    this.giziburukMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modalgiziburuk;
                    break;
                case "giziKurang":
                    this.gizikurangMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modalgizikurang;
                    break;
                case "resikogzlebih":
                    this.resikogzlebihMode = true;
                    this.modaltampildata = null;
                    this.modaltampildata = this.modalresikogzlebih;
                    break;
                default:
                    this.modaltampildata = null;
                    break;
            }

            // Tampilkan data modal jika ada
            if (this.modaltampildata) {
                this.reinitializeDataModal(this.modaltampildata);
            }
        },
        resetModes() {
            // Reset semua mode dengan satu langkah
            this.bbskurangMode = false,
                this.bbkurangMode = false,
                this.sangatpendekMode = false,
                this.pendekMode = false,
                this.resikobblebihMode = false,
                this.giziburukMode = false,
                this.gizikurangMode = false,
                this.resikogzlebihMode = false,
                this.gizilebihMode = false,
                this.obesitasMode = false;
        },
        getAge(dateString) {
            // Split the date string by '-'
            const [day, month, year] = dateString.split("-");

            // Create a new Date object using the extracted year, month, and day
            const birthDate = new Date(`${year}-${month}-${day}`);
            const today = new Date();

            // Calculate the difference in years and months
            const yearsDiff = today.getFullYear() - birthDate.getFullYear();
            const monthsDiff = today.getMonth() - birthDate.getMonth();

            // Calculate the total age in months
            let ageInMonths = yearsDiff * 12 + monthsDiff;

            // Adjust if the current day of the month is earlier than the birth date's day
            if (today.getDate() < birthDate.getDate()) {
                ageInMonths--;
            }

            return ageInMonths;
        },
        getBeratBadanUmur() {
            this.modaltampildatabbskurang = [];
            this.modaltampildatabbkurang = [];
            this.modalresikobblebih = [];
            axios
                .get(window.url + "api/getberatbadanumur")
                .then((response) => {

                    // Pastikan data adalah array
                    const data = response.data;
                    if (!Array.isArray(data)) {
                        console.error("Data yang diterima bukan array:", data);
                        return;
                    }

                    // Simpan data ke variabel Vue
                    this.bandingberat = data;

                    // Inisialisasi kategori untuk laki-laki dan perempuan
                    let giziBurukL = 0, giziKurangL = 0, giziBaikL = 0, giziLebihL = 0;
                    let giziBurukP = 0, giziKurangP = 0, giziBaikP = 0, giziLebihP = 0;

                    // Iterasi data untuk menghitung jumlah berdasarkan kategori
                    this.bandingberat.forEach((item) => {

                        // Debugging: cek properti setiap item

                        // Pastikan ada data yang valid
                        if (!item.jenis_kelamin || !item.jenis) {
                            console.warn("Data tidak lengkap untuk item:", item);
                            return; // Lewati item jika tidak lengkap
                        }
                        if (item.jenis_kelamin === "Laki-laki") {
                            switch (item.jenis) {
                                case "Berat Badan Sangat Kurang":
                                    giziBurukL++;
                                    this.modaltampildatabbskurang.push(item);
                                    break;
                                case "Berat Badan Kurang":
                                    giziKurangL++;
                                    this.modaltampildatabbkurang.push(item);
                                    break;
                                case "Berat Badan Normal":
                                    giziBaikL++;
                                    break;
                                case "Resiko Berat Badan Lebih":
                                    giziLebihL++;
                                    this.modalresikobblebih.push(item);
                                    break;
                                default:
                                    console.log("Jenis tidak dikenali (Laki-laki):", item.jenis);
                            }
                        } else if (item.jenis_kelamin === "Perempuan") {
                            switch (item.jenis) {
                                case "Berat Badan Sangat Kurang":
                                    giziBurukP++;
                                    this.modaltampildatabbskurang.push(item);
                                    break;
                                case "Berat Badan Kurang":
                                    giziKurangP++;
                                    this.modaltampildatabbkurang.push(item);
                                    break;
                                case "Berat Badan Normal":
                                    giziBaikP++;
                                    break;
                                case "Resiko Berat Badan Lebih":
                                    giziLebihP++;
                                    this.modalresikobblebih.push(item);
                                    break;
                                default:
                                    console.log("Jenis tidak dikenali (Perempuan):", item.jenis);
                            }
                        } else {
                            console.log("Jenis kelamin tidak dikenali:", item.jenis_kelamin);
                        }
                    });


                    // Masukkan hasil ke dalam pie chart series
                    this.pieBBULSeries = [giziBurukL, giziKurangL, giziBaikL, giziLebihL];
                    this.pieBBUPSeries = [giziBurukP, giziKurangP, giziBaikP, giziLebihP];
                })
                .catch((errors) => {
                    console.error("Error saat mengambil data:", errors);
                });
        },
        getberatbadanumurPos(posyanduId) {
            this.modaltampildatabbskurang = [];
            this.modaltampildatabbkurang = [];
            this.modalresikobblebih = [];
            axios
                .get(window.url + "api/getberatbadanumurpos/" + posyanduId)
                .then((response) => {

                    // Pastikan data adalah array
                    const data = response.data;
                    if (!Array.isArray(data)) {
                        console.error("Data yang diterima bukan array:", data);
                        return;
                    }

                    // Simpan data ke variabel Vue
                    this.bandingberat = data;

                    // Inisialisasi kategori untuk laki-laki dan perempuan
                    let giziBurukL = 0, giziKurangL = 0, giziBaikL = 0, giziLebihL = 0;
                    let giziBurukP = 0, giziKurangP = 0, giziBaikP = 0, giziLebihP = 0;

                    // Iterasi data untuk menghitung jumlah berdasarkan kategori
                    this.bandingberat.forEach((item) => {

                        // Pastikan ada data yang valid
                        if (!item.jenis_kelamin || !item.jenis) {
                            console.warn("Data tidak lengkap untuk item:", item);
                            return; // Lewati item jika tidak lengkap
                        }
                        if (item.jenis_kelamin === "Laki-laki") {
                            switch (item.jenis) {
                                case "Berat Badan Sangat Kurang":
                                    giziBurukL++;
                                    this.modaltampildatabbskurang.push(item);
                                    break;
                                case "Berat Badan Kurang":
                                    giziKurangL++;
                                    this.modaltampildatabbkurang.push(item);
                                    break;
                                case "Berat Badan Normal":
                                    giziBaikL++;
                                    break;
                                case "Resiko Berat Badan Lebih":
                                    giziLebihL++;
                                    this.modalresikobblebih.push(item);
                                    break;
                                default:
                                    console.log("Jenis tidak dikenali (Laki-laki):", item.jenis);
                            }
                        } else if (item.jenis_kelamin === "Perempuan") {
                            switch (item.jenis) {
                                case "Berat Badan Sangat Kurang":
                                    giziBurukP++;
                                    this.modaltampildatabbskurang.push(item);
                                    break;
                                case "Berat Badan Kurang":
                                    giziKurangP++;
                                    this.modaltampildatabbkurang.push(item);
                                    break;
                                case "Berat Badan Normal":
                                    giziBaikP++;
                                    break;
                                case "Resiko Berat Badan Lebih":
                                    giziLebihP++;
                                    this.modalresikobblebih.push(item);
                                    break;
                                default:
                                    console.log("Jenis tidak dikenali (Perempuan):", item.jenis);
                            }
                        } else {
                            console.log("Jenis kelamin tidak dikenali:", item.jenis_kelamin);
                        }
                    });


                    // Masukkan hasil ke dalam pie chart series
                    this.pieBBULSeries = [giziBurukL, giziKurangL, giziBaikL, giziLebihL];
                    this.pieBBUPSeries = [giziBurukP, giziKurangP, giziBaikP, giziLebihP];
                })
                .catch((errors) => {
                    console.error("Error saat mengambil data:", errors);
                });
        },

        gettinggibadanumur() {
            this.modaltampildatasangatpendek = [];
            this.modaltampildatapendek = [];
            axios
                .get(window.url + "api/gettinggibadanumur")
                .then((response) => {
                    // console.log("Data API diterima:", response.data);
                    const data = response.data;

                    if (!Array.isArray(data)) {
                        console.error("Data yang diterima bukan array:", data);
                        return;
                    }

                    this.bandingberat = data;

                    let sangatPendekL = 0, pendekL = 0, normalL = 0, tinggiL = 0;
                    let sangatPendekP = 0, pendekP = 0, normalP = 0, tinggiP = 0;

                    this.bandingberat.forEach((item) => {
                        // console.log("Item saat ini:", item);

                        if (!item.jenis_kelamin || !item.jenis) {
                            console.warn("Data tidak lengkap:", item);
                            return;
                        }

                        if (item.jenis_kelamin === "Laki-laki") {
                            switch (item.jenis) {
                                case "Sangat Pendek": sangatPendekL++;
                                    this.modaltampildatasangatpendek.push(item); break;
                                case "Pendek": pendekL++;
                                    this.modaltampildatapendek.push(item); break;
                                case "Normal": normalL++; break;
                                case "Tinggi": tinggiL++; break;
                                default: console.log("Jenis tidak dikenali (Laki-laki):", item.jenis);
                            }
                        } else if (item.jenis_kelamin === "Perempuan") {
                            switch (item.jenis) {
                                case "Sangat Pendek": sangatPendekP++;
                                    this.modaltampildatasangatpendek.push(item); break;
                                case "Pendek": pendekP++;
                                    this.modaltampildatapendek.push(item); break;
                                case "Normal": normalP++; break;
                                case "Tinggi": tinggiP++; break;
                                default: console.log("Jenis tidak dikenali (Perempuan):", item.jenis);
                            }
                        } else {
                            console.warn("Jenis kelamin tidak dikenali:", item.jenis_kelamin);
                        }
                    });


                    // console.log("Hasil Laki-laki:", sangatPendekL, pendekL, normalL, tinggiL);
                    // console.log("Hasil Perempuan:", sangatPendekP, pendekP, normalP, tinggiP);

                    this.pieTBULSeries = [sangatPendekL, pendekL, normalL, tinggiL];
                    this.pieTBUPSeries = [sangatPendekP, pendekP, normalP, tinggiP];

                    if (!this.pieTBULSeries.some(value => value > 0)) {
                        console.error("Data series laki-laki kosong atau tidak valid:", this.pieTBULSeries);
                    }

                    if (!this.pieTBUPSeries.some(value => value > 0)) {
                        console.error("Data series perempuan kosong atau tidak valid:", this.pieTBUPSeries);
                    }
                })
                .catch((errors) => {
                    console.error("Error saat mengambil data:", errors);
                });
        },
        gettinggibadanumurPos(posyanduId) {
            this.modaltampildatasangatpendek = [];
            this.modaltampildatapendek = [];
            axios
                .get(window.url + "api/gettinggibadanumurpos/" + posyanduId)
                .then((response) => {
                    // console.log("Data API diterima:", response.data);
                    const data = response.data;

                    if (!Array.isArray(data)) {
                        console.error("Data yang diterima bukan array:", data);
                        return;
                    }

                    this.bandingberat = data;

                    let sangatPendekL = 0, pendekL = 0, normalL = 0, tinggiL = 0;
                    let sangatPendekP = 0, pendekP = 0, normalP = 0, tinggiP = 0;

                    this.bandingberat.forEach((item) => {
                        // console.log("Item saat ini:", item);

                        if (!item.jenis_kelamin || !item.jenis) {
                            console.warn("Data tidak lengkap:", item);
                            return;
                        }

                        if (item.jenis_kelamin === "Laki-laki") {
                            switch (item.jenis) {
                                case "Sangat Pendek": sangatPendekL++;
                                    this.modaltampildatasangatpendek.push(item); break;
                                case "Pendek": pendekL++;
                                    this.modaltampildatapendek.push(item); break;
                                case "Normal": normalL++; break;
                                case "Tinggi": tinggiL++; break;
                                default: console.log("Jenis tidak dikenali (Laki-laki):", item.jenis);
                            }
                        } else if (item.jenis_kelamin === "Perempuan") {
                            switch (item.jenis) {
                                case "Sangat Pendek": sangatPendekP++;
                                    this.modaltampildatasangatpendek.push(item); break;
                                case "Pendek": pendekP++;
                                    this.modaltampildatapendek.push(item); break;
                                case "Normal": normalP++; break;
                                case "Tinggi": tinggiP++; break;
                                default: console.log("Jenis tidak dikenali (Perempuan):", item.jenis);
                            }
                        } else {
                            console.warn("Jenis kelamin tidak dikenali:", item.jenis_kelamin);
                        }
                    });


                    // console.log("Hasil Laki-laki:", sangatPendekL, pendekL, normalL, tinggiL);
                    // console.log("Hasil Perempuan:", sangatPendekP, pendekP, normalP, tinggiP);

                    // console.log(this.modaltampildata);
                    this.pieTBULSeries = [sangatPendekL, pendekL, normalL, tinggiL];
                    this.pieTBUPSeries = [sangatPendekP, pendekP, normalP, tinggiP];

                    if (!this.pieTBULSeries.some(value => value > 0)) {
                        console.error("Data series laki-laki kosong atau tidak valid:", this.pieTBULSeries);
                    }

                    if (!this.pieTBUPSeries.some(value => value > 0)) {
                        console.error("Data series perempuan kosong atau tidak valid:", this.pieTBUPSeries);
                    }
                })
                .catch((errors) => {
                    console.error("Error saat mengambil data:", errors);
                });
        },

        getGizi() {
            this.modalgiziburuk = [];
            this.modalgizikurang = [];
            this.modalresikogzlebih = [];
            this.modalgiziLebih = [];
            this.modalobesitas = [];
            axios
                .get(window.url + "api/getberatbadantinggi")
                .then((response) => {
                    // console.log("Data API diterima:", response.data);
                    const data = response.data;

                    if (!Array.isArray(data)) {
                        console.error("Data yang diterima bukan array:", data);
                        return;
                    }

                    this.bandinggizi = data;

                    let giziburukL = 0, gizikurangL = 0, normalL = 0, bgizilebihL = 0, gizilebihL = 0, obesitasL = 0;
                    let giziburukP = 0, giziKurangP = 0, normalP = 0, bgizilebihP = 0, gizilebihP = 0, obesitasP = 0;

                    this.bandinggizi.forEach((item) => {
                        // console.log("Item saat ini:", item);

                        if (!item.jenis_kelamin || !item.jenis) {
                            console.warn("Data tidak lengkap:", item);
                            return;
                        }

                        if (item.jenis_kelamin === "Laki-laki") {
                            switch (item.jenis) {
                                case "Gizi Buruk": giziburukL++;
                                    this.modalgiziburuk.push(item); break;
                                case "Gizi Kurang": gizikurangL++;
                                    this.modalgizikurang.push(item); break;
                                case "Gizi Baik": normalL++; break;
                                case "Beresiko Gizi Lebih": bgizilebihL++;
                                    this.modalresikogzlebih.push(item); break;
                                case "Gizi Lebih": gizilebihL++;
                                    this.modalgiziLebih.push(item); break;
                                case "Obesitas": obesitasL++;
                                    this.modalobesitas.push(item); break;
                                default: console.log("Jenis tidak dikenali (Laki-laki):", item.jenis);
                            }
                        } else if (item.jenis_kelamin === "Perempuan") {
                            switch (item.jenis) {
                                case "Gizi Buruk": giziburukP++;
                                    this.modalgiziburuk.push(item); break;
                                case "Gizi Kurang": giziKurangP++;
                                    this.modalgizikurang.push(item); break;
                                case "Gizi Baik": normalP++; break;
                                case "Beresiko Gizi Lebih": bgizilebihP++;
                                    this.modalresikogzlebih.push(item); break;
                                case "Gizi Lebih": gizilebihP++;
                                    this.modalgiziLebih.push(item); break;
                                case "Obesitas": obesitasP++;
                                    this.modalobesitas.push(item); break;
                                default: console.log("Jenis tidak dikenali (Perempuan):", item.jenis);
                            }
                        } else {
                            console.warn("Jenis kelamin tidak dikenali:", item.jenis_kelamin);
                        }
                    });


                    // console.log("Hasil Laki-laki:", giziburukL, gizikurangL, normalL, gizilebihL);
                    // console.log("Hasil Perempuan:", giziburukP, giziKurangP, normalP, gizilebihP);

                    this.pieBBTBLSeries = [giziburukL, gizikurangL, normalL, bgizilebihL, gizilebihL, obesitasL];
                    this.pieBBTBPSeries = [giziburukP, giziKurangP, normalP, bgizilebihP, gizilebihP, obesitasP];

                    if (!this.pieTBULSeries.some(value => value > 0)) {
                        console.error("Data series laki-laki kosong atau tidak valid:", this.pieTBULSeries);
                    }

                    if (!this.pieTBUPSeries.some(value => value > 0)) {
                        console.error("Data series perempuan kosong atau tidak valid:", this.pieTBUPSeries);
                    }
                })
                .catch((errors) => {
                    console.error("Error saat mengambil data:", errors);
                });
        },
        getGiziPos($id) {
            this.modalgiziburuk = [];
            this.modalgizikurang = [];
            this.modalresikogzlebih = [];
            this.modalgiziLebih = [];
            this.modalobesitas = [];
            axios
                .get(window.url + "api/getberatbadantinggiPos/" + $id)
                .then((response) => {
                    // console.log("Data API diterima:", response.data);
                    const data = response.data;

                    if (!Array.isArray(data)) {
                        console.error("Data yang diterima bukan array:", data);
                        return;
                    }

                    this.bandinggizi = data;

                    let giziburukL = 0, gizikurangL = 0, normalL = 0, bgizilebihL = 0, gizilebihL = 0, obesitasL = 0;
                    let giziburukP = 0, giziKurangP = 0, normalP = 0, bgizilebihP = 0, gizilebihP = 0, obesitasP = 0;

                    this.bandinggizi.forEach((item) => {
                        // console.log("Item saat ini:", item);

                        if (!item.jenis_kelamin || !item.jenis) {
                            console.warn("Data tidak lengkap:", item);
                            return;
                        }

                        if (item.jenis_kelamin === "Laki-laki") {
                            switch (item.jenis) {
                                case "Gizi Buruk": giziburukL++;
                                    this.modalgiziburuk.push(item); break;
                                case "Gizi Kurang": gizikurangL++;
                                    this.modalgizikurang.push(item); break;
                                case "Gizi Baik": normalL++; break;
                                case "Beresiko Gizi Lebih": bgizilebihL++;
                                    this.modalresikogzlebih.push(item); break;
                                case "Gizi Lebih": gizilebihL++;
                                    this.modalgiziLebih.push(item); break;
                                case "Obesitas": obesitasL++;
                                    this.modalobesitas.push(item); break;
                                default: console.log("Jenis tidak dikenali (Laki-laki):", item.jenis);
                            }
                        } else if (item.jenis_kelamin === "Perempuan") {
                            switch (item.jenis) {
                                case "Gizi Buruk": giziburukP++;
                                    this.modalgiziburuk.push(item); break;
                                case "Gizi Kurang": giziKurangP++;
                                    this.modalgizikurang.push(item); break;
                                case "Gizi Baik": normalP++; break;
                                case "Beresiko Gizi Lebih": bgizilebihP++;
                                    this.modalresikogzlebih.push(item); break;
                                case "Gizi Lebih": gizilebihP++;
                                    this.modalgiziLebih.push(item); break;
                                case "Obesitas": obesitasP++;
                                    this.modalobesitas.push(item); break;
                                default: console.log("Jenis tidak dikenali (Perempuan):", item.jenis);
                            }
                        } else {
                            console.warn("Jenis kelamin tidak dikenali:", item.jenis_kelamin);
                        }
                    });


                    // console.log("Hasil Laki-laki:", giziburukL, gizikurangL, normalL, gizilebihL);
                    // console.log("Hasil Perempuan:", giziburukP, giziKurangP, normalP, gizilebihP);

                    this.pieBBTBLSeries = [giziburukL, gizikurangL, normalL, bgizilebihL, gizilebihL, obesitasL];
                    this.pieBBTBPSeries = [giziburukP, giziKurangP, normalP, bgizilebihP, gizilebihP, obesitasP];

                    if (!this.pieTBULSeries.some(value => value > 0)) {
                        console.error("Data series laki-laki kosong atau tidak valid:", this.pieTBULSeries);
                    }

                    if (!this.pieTBUPSeries.some(value => value > 0)) {
                        console.error("Data series perempuan kosong atau tidak valid:", this.pieTBUPSeries);
                    }
                })
                .catch((errors) => {
                    console.error("Error saat mengambil data:", errors);
                });
        },
        closeModal() {
            $("#taskModal").modal("hide");
        },

        kasihPosyandu() {
            axios
                .get(window.url + "api/getPosyandu")
                .then((response) => {
                    this.posyandus = response.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        applyFilters() {
            if (this.filters.posyandu_id) {
                const posyanduId = this.filters.posyandu_id;
                // console.log("Posyandu ID:", posyanduId);
                this.getBayisPosyandu(posyanduId);
                this.getberatbadanumurPos(posyanduId);
                this.gettinggibadanumurPos(posyanduId);
                this.getPeriksaPOS(posyanduId);
                this.getGiziPos(posyanduId);
            } else {
                this.getBayis();
                this.getBeratBadanUmur();
                this.gettinggibadanumur();
                this.getPeriksa();
                this.getGizi();
            }
        },

        getBayis() {
            axios
                .get(window.url + "api/getBayi")
                .then((response) => {
                    this.balitas = response.data; // Set data
                    this.reinitializeDataTable(this.balitas); // Pass the fetched data
                    this.getPosyandu();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getBayisPosyandu(posyanduId) {
            axios
                .get(window.url + "api/getBayiPosyandu/" + posyanduId)
                .then((response) => {
                    this.balitas = response.data; // Set data
                    this.reinitializeDataTable(this.balitas); // Pass the fetched data
                    this.getPosyandu();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        reinitializeDataTable(data) {
            const plainData = JSON.parse(JSON.stringify(data));

            const table = $("#tablebayis");

            // Clear and destroy existing DataTable if present
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }

            // Initialize DataTable
            table.DataTable({
                data: plainData,
                processing: true,
                serverSide: false,
                columns: this.getDataTableColumns(),
            });

            // Attach event listeners
            this.attachEventListeners();
        },
        getDataTableColumns() {
            return [
                { data: "nama", name: "nama" },
                {
                    data: "tanggal_lahir",
                    name: "tanggal_lahir",
                    render: (data, type, row) => {
                        return row.tanggal_lahir
                            ? `${this.getAge(row.tanggal_lahir)} bulan`
                            : "Data missing";
                    },
                },
                {
                    data: "jenis_kelamin",
                    name: "jenis_kelamin",
                    render: (data) =>
                        `<div class="text-center">${data || "N/A"}</div>`,
                },
                { data: "posyandu_nama", name: "posyandu_nama" },
                {
                    data: null,
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    render: (data, type, row) => {
                        if (!row.balitas_id) {
                            console.error(
                                "Error: balitas_id is missing in row data",
                                row
                            );
                            return "<div class='text-center'>Error: ID missing</div>";
                        }
                        return this.getActionButtons(row);
                    },
                },
            ];
        },
        getActionButtons(row) {
            return `
        <div class="row text-center">
            <div class="col">
                <button type="button" class="btn btn-warning btn-sm mx-1 edit-task-btn" data-id="${row.balitas_id}">Edit</button>
            </div>
            <div class="col">
                <button type="button" class="btn btn-danger btn-sm remove-task-btn" data-id="${row.balitas_id}">Delete</button>
            </div>
        </div>
    `;
        },
        attachEventListeners() {
            const table = $("#tablebayis");

            table.on("click", ".edit-task-btn", this.handleEditClick);
            table.on("click", ".remove-task-btn", this.handleRemoveClick);
        },
        handleEditClick(event) {
            const id = $(event.currentTarget).data("id");

            if (!id) {
                console.error("Edit button missing ID.");
                return;
            }

            const task = this.balitas.find((bayis) => bayis.balitas_id === id);

            if (task) {
                this.editTask(task);
            } else {
                console.error("Task not found for ID:", id);
            }
        },
        handleRemoveClick(event) {
            const id = $(event.currentTarget).data("id");

            if (!id) {
                console.error("Delete button missing ID.");
                return;
            }

            const task = this.balitas.find((wus) => wus.balitas_id === id);
            if (task) {
                this.removeTask(task);
            } else {
                console.error("Task not found for ID:", id);
            }
        },
        removeTask(task) {
            this.deleteMode = true;
            this.editMode = false;
            this.taskData.id = task.balitas_id;
            this.taskData.nama = task.nama;
            $("#taskModal").modal("show");
        },
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;

            // Konversi tanggal dari string ke Date object
            this.taskData = {
                ...task,
                tanggal_lahir: parse(task.tanggal_lahir, 'dd-MM-yyyy', new Date())
            };
            this.$nextTick(() => {
                $("#taskModal").modal("show");
                $("#taskModal").on("shown.bs.modal", () => {
                    this.initializeSelect2(); // Pastikan dipanggil setelah modal ditampilkan
                });
            });
        },
        updateTask() {
            this.taskData.tanggal_lahir === ""
                ? (this.taskErrors.tanggal_lahir = true)
                : (this.taskErrors.tanggal_lahir = false);
            this.taskData.panjangbadan_lahir === ""
                ? (this.taskErrors.panjangbadan_lahir = true)
                : (this.taskErrors.panjangbadan_lahir = false);
            this.taskData.beratbadan_lahir === ""
                ? (this.taskErrors.beratbadan_lahir = true)
                : (this.taskErrors.beratbadan_lahir = false);
            this.taskData.likep_lahir === ""
                ? (this.taskErrors.likep_lahir = true)
                : (this.taskErrors.likep_lahir = false);
            this.taskData.posyandus_id === ""
                ? (this.taskErrors.posyandus_id = true)
                : (this.taskErrors.posyandus_id = false);

            this.taskData.tanggal_lahir = this.formatDate(
                this.taskData.tanggal_lahir
            ); // Date will be in selected format

            if (
                this.taskData.tanggal_lahir &&
                this.taskData.panjangbadan_lahir &&
                this.taskData.beratbadan_lahir &&
                this.taskData.likep_lahir &&
                this.taskData.posyandus_id
            ) {
                axios
                    .post(
                        `${window.url}api/updateBayis/${this.taskData.balitas_id}`,
                        this.taskData
                    )
                    .then((response) => {
                        this.getBayis();
                    })
                    .catch((errors) => {
                        console.log(errors);
                    })
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            }
        },
        isTaskValid() {
            return !Object.values(this.taskErrors).includes(true);
        },
        deleteTask() {
            axios
                .post(`${window.url}api/deleteBayi/${this.taskData.id}`)
                .then(() => {
                    this.getBayis();
                })
                .catch((errors) => {
                    console.error(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
    },
};
</script>
