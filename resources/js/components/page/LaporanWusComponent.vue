<template>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col">
                    <select class="form-control select2 w-100" v-model="filters.posyandu_id" id="filter-posyandu">
                        <option value="">Semua Posyandu</option>
                        <option v-for="task in posyandus" :key="task.id" :value="task.id">
                            {{ task.nama_posyandu }}
                        </option>
                    </select>
                </div>

                <div class="col">
                    <VueDatePicker v-model="filters.tgl_kegiatan" :format="format" placeholder="Pilih Bulan Kegiatan"
                        :enable-time-picker="false" />
                </div>


            </div>
        </div>
    </nav>

    <!-- Button Tampil KEK -->
    <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#DafterKekModal">
        Tampilkan peserta KEK
    </button>


    <!-- Pie chart -->
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card border-primary mb-3">
                <div class="card-header">Pemetaan Status Wanita Usia Subur</div>
                <div class="card-body content-center">
                    <apexchart :width="350" :height="350" type="pie" :options="pieStatusOptions" :series="pieSeries">
                    </apexchart>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
        <div class="col-md-4">
            <div class="card border-primary mb-3">
                <div class="card-header">Perbandingan gemuk dan normal</div>
                <div class="card-body text-primary">
                    <apexchart type="pie" :width="350" :height="350" :options="pieGemukOptions"
                        :series="pieGemukSeries"></apexchart>
                </div>
            </div>
        </div>
    </div>

    <!-- Line chart -->
    <div class="row">
        <div class="col">
            <div class="card border-secondary">
                <div class="card-body">
                    <div id="chart">
                        <apexchart ref="lineKekChart" type="line" :options="lineKekChartOptions"
                            :series="lineKekSeries"></apexchart>
                    </div>
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

    <!-- Tabel WUS -->
    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header">Tabel Wus</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablewus" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Umur Peserta</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Lingkar Lengan</th>
                                    <th class="text-center">Posyandu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Umur Peserta</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Lingkar Lengan</th>
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
                                ? "Hapus Wus " + taskData.nama
                                : editMode
                                    ? "Perbarui Wus " + taskData.nama
                                    : "Error!"
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="!deleteMode">
                        <!-- Form content for editing -->
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="namaposyandu">Nama Wus</label>
                                    <input type="text" class="form-control" v-model="taskData.nama" />
                                    <span class="text-danger" v-if="taskErrors.nama">Data Belum Terisi!</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="date">Tanggal Lahir</label>
                                    <VueDatePicker v-model="taskData.tanggal_lahir" :format="format"
                                        :placeholder="taskData.tanggal_lahir" :enable-time-picker="false" />
                                    <span class="text-danger" v-show="taskErrors.tanggal_lahir">Masukan tanggal
                                        lahir</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="lila">Lingkar Kepala Wus (cm)</label>
                                    <input type="number" min="0" max="1000" step="0.01" class="form-control"
                                        v-model="taskData.lila_wus" placeholder="Masukan Lingkar Lengan Wus (cm)" />
                                    <span class="text-danger" v-show="taskErrors.lila_wus">Silahkan masukan
                                        lila!</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="lila">Status</label>
                                    <select class="form-control" v-model="taskData.statuswus">
                                        <option disabled value="">
                                            -Silahkan Dipilih-
                                        </option>
                                        <option value="Tidak menikah">
                                            Tidak menikah
                                        </option>
                                        <option value="Hamil">Hamil</option>
                                        <option value="Nifas">Nifas</option>
                                        <option value="Tidak hamil & Menyusui">
                                            Menyusui
                                        </option>
                                        <option value="Tidak hamil & Tidak menyusui">
                                            Tidak Menyusui
                                        </option>
                                    </select>
                                    <span class="text-danger" v-show="taskErrors.statuswus">Status wus tidak
                                        valid!</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="posyandu">Posyandu</label>
                            <select class="form-control select2" v-model="taskData.posyandus_id" style="width: 100%"
                                id="my-select">
                                <option v-for="task in tasks" :key="task.id" :value="task.id">
                                    {{ task.nama_posyandu }}
                                </option>
                            </select>

                            <span class="text-danger" v-if="taskErrors.posyandus_id">Silahkan pilih posyandu!</span>
                        </div>
                    </div>

                    <div v-else>
                        <p>
                            Apakah Anda yakin ingin menghapus data
                            <b>{{ taskData.nama }}</b>?
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Tutup
                    </button>
                    <button v-if="!deleteMode" type="button" class="btn btn-primary" @click="updateTask">
                        Simpan
                    </button>
                    <button v-else type="button" class="btn btn-danger" @click="deleteTask">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal tampil kek -->
    <div class="modal fade" id="DafterKekModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Daftar peserta terindikasi KEK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-show="modalkek.length == 0">Belum ada peserta terindikasi KEK"</div>
                    <div v-show="modalkek.length > 0">
                        <table id="tablekek" class="table table-striped table-bordered w-100" ref="tablekek">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama</th>
                                </tr>
                            </thead>
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
            wuses: [],
            status: [],
            posyandus: [],
            kek: [],
            modalkek: [],
            bandingkek: [],
            bandinggemuk: [],
            jumlahwus: null,
            deleteMode: false,
            editMode: false,
            taskData: {
                id: null,
                nama: "",

                statuswus: "",
                lila_wus: "",
                posyandus_id: "",
                tanggal_lahir: "",
            },
            taskErrors: {
                nama: false,
                tanggal_lahir: false,
                statuswus: false,
                lila_wus: false,
                posyandus_id: false,
            },
            filters: {
                posyandu_id: "",
                tgl_kegiatan: "",
            },
            lineKekSeries: [],
            lineKekChartOptions: {
                chart: {
                    height: 300,
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
                title: {
                    text: "Perkembangan kurang energi kronis dan normal",
                    align: "left",
                },
                grid: {
                    borderColor: "#e7e7e7",
                    row: { colors: ["#f3f3f3", "transparent"], opacity: 0.5 },
                },
                markers: { size: 1 },
                xaxis: {
                    categories: [],
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
                    '#003f5c',
                    '#58508d',
                    '#bc5090',
                    '#ff6361',
                    '#ffa600',
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
            pieKEKSeries: [0, 0],
            pieKEKOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["KEK", "Normal"],
                colors: ["rgb(238, 66, 102)", "rgb(255, 210, 63)"],
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
            pieGemukSeries: [0, 0],
            pieGemukOptions: {
                chart: {
                    type: "pie",
                    borderColor: "#000000",
                },
                labels: ["Gemuk", "Normal"],
                colors: ["rgb(134, 93, 255)", "rgb(227, 132, 255)"],
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
                labels: ["Lingkar Lengan", "Lingkar Perut", "Diastol", "Sistol", "Tinggi Fundus"],
                colors: [
                    "#6929c4",
                    "#1192e8",
                    "#005d5d",
                    "#9f1853",
                    "#ee538b",
                ],
                dataLabels: { enabled: true },
                stroke: {
                    curve: "smooth",
                    connectNulls: true,
                },
                grid: {
                    borderColor: "#e7e7e7",
                    row: { colors: ["#f3f3f3", "transparent"], opacity: 0.5 },
                },
                markers: { size: 1 },
                xaxis: {
                    categories: [],
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

    beforeDestroy() {
        if ($("#my-select").data("select2")) {
            $("#my-select").select2("destroy");
        }
    },

    methods: {
        formatDateForApi(date) {
            if (!date) return null;
            const d = new Date(date);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}-${month}-${year}`;
        },
        getPeriksa() {
            axios
                .get(
                    window.url +
                    "api/getPeriksaLast12Monthsall/"
                )
                .then((response) => {
                    const lila_periksa = [];
                    const lingkarperut_periksa = [];
                    const diastol = [];
                    const sistol = [];
                    const tinggi_fundus = [];
                    const months = [];
                    response.data.forEach((item) => {
                        lila_periksa.push(item.lila_periksa ?? 0);
                        lingkarperut_periksa.push(
                            item.lingkarperut_periksa ?? 0
                        );
                        diastol.push(item.diastol ?? 0);
                        sistol.push(item.sistol ?? 0);
                        tinggi_fundus.push(item.tinggi_fundus ?? 0);
                        months.push(item.month);
                    });
                    const reversedLila = lila_periksa.reverse();
                    const reversedLingkarPerut = lingkarperut_periksa.reverse();
                    const reversedDiastol = diastol.reverse();
                    const reversedSistol = sistol.reverse();
                    const reversedTinggiFundus = tinggi_fundus.reverse();
                    const reversedMonths = months.reverse();

                    this.lineperiksaSeries = [
                        { name: "Lingkar lengan", data: reversedLila },
                        { name: "Lingkar perut", data: reversedLingkarPerut },
                        { name: "Diastol", data: reversedDiastol },
                        { name: "Sistol", data: reversedSistol },
                        { name: "Tinggi fundus", data: reversedTinggiFundus },
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
        formatDate(date) {
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
                    this.posyandus = response.data;

                    this.initializeSelect2();
                })
                .catch((errors) => {
                    console.log(errors); // Tangani kesalahan jika ada
                });
        },
        initializeSelect2() {
            const vm = this;
            if ($.fn.select2) {
                if ($("#my-select").data("select2")) {
                    $("#my-select").select2("destroy");
                }
            }
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                    placeholder: "- Silahkan memilih tempat pelaksanaan -",
                    allowClear: true,
                })
                .on("change", function () {
                    vm.taskData.posyandus_id = $(this).val();
                    console.log(vm.taskData.posyandus_id);
                });
            if ($.fn.select2 && $("#filter-posyandu").data("select2")) {
                $("#filter-posyandu").select2("destroy");
            }
            $("#filter-posyandu")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    vm.filters.posyandu_id = $(this).val();
                    vm.applyFilters();
                });
        },

        getAge(dateString) {
            const [day, month, year] = dateString.split("-");
            const birthDate = new Date(`${year}-${month}-${day}`);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (
                monthDiff < 0 ||
                (monthDiff === 0 && today.getDate() < birthDate.getDate())
            ) {
                age--;
            }
            return age;
        },
        getStatus() {
            axios
                .get(window.url + "api/getWusStatus")
                .then((response) => {
                    this.status = response.data;
                    const {
                        tdmenikah,
                        hamil,
                        tdhamilsusu,
                        tdhamilanggur,
                        nifas,
                    } = this.status;
                    this.pieSeries = [
                        tdmenikah,
                        hamil,
                        tdhamilsusu,
                        tdhamilanggur,
                        nifas,
                    ];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getGemuk() {
            axios
                .get(window.url + "api/getGemuk")
                .then((response) => {
                    this.bandinggemuk = response.data;
                    let gemuk = 0;
                    let normal = 0;

                    this.bandinggemuk.forEach((item) => {
                        if (item.lingkarperut_periksa <= 80) {
                            normal += 1;
                        } else if (item.lingkarperut_periksa > 80) {
                            gemuk += 1;
                        }
                    });
                    this.pieGemukSeries = [gemuk, normal];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getGemukPos($id) {
            axios
                .get(window.url + "api/getGemukPos/" + $id)
                .then((response) => {
                    this.bandinggemuk = response.data;
                    let gemuk = 0;
                    let normal = 0;

                    this.bandinggemuk.forEach((item) => {
                        if (item.lingkarperut_periksa <= 80) {
                            normal += 1;
                        } else if (item.lingkarperut_periksa > 80) {
                            gemuk += 1;
                        }
                    });
                    this.pieGemukSeries = [gemuk, normal];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getGemukPosByDate($id, $date) {
            const $formattedDate = this.formatDateForApi($date);
            axios
                .get(window.url + "api/getGemukPosByDate/" + $id + "/" + $formattedDate)
                .then((response) => {
                    this.bandinggemuk = response.data;
                    let gemuk = 0;
                    let normal = 0;

                    this.bandinggemuk.forEach((item) => {
                        if (item.lingkarperut_periksa <= 80) {
                            normal += 1;
                        } else if (item.lingkarperut_periksa > 80) {
                            gemuk += 1;
                        }
                    });
                    this.pieGemukSeries = [gemuk, normal];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getPeriksaPos($id) {
            axios
                .get(
                    window.url +
                    "api/getPeriksaLast12Monthspos/" + $id
                )
                .then((response) => {
                    const lila_periksa = [];
                    const lingkarperut_periksa = [];
                    const diastol = [];
                    const sistol = [];
                    const tinggi_fundus = [];
                    const months = [];
                    response.data.forEach((item) => {
                        lila_periksa.push(item.lila_periksa ?? 0);
                        lingkarperut_periksa.push(
                            item.lingkarperut_periksa ?? 0
                        );
                        diastol.push(item.diastol ?? 0);
                        sistol.push(item.sistol ?? 0);
                        tinggi_fundus.push(item.tinggi_fundus ?? 0);
                        months.push(item.month);
                    });
                    const reversedLila = lila_periksa.reverse();
                    const reversedLingkarPerut = lingkarperut_periksa.reverse();
                    const reversedDiastol = diastol.reverse();
                    const reversedSistol = sistol.reverse();
                    const reversedTinggiFundus = tinggi_fundus.reverse();
                    const reversedMonths = months.reverse();
                    this.lineperiksaSeries = [
                        { name: "Lingkar lengan", data: reversedLila },
                        { name: "Lingkar perut", data: reversedLingkarPerut },
                        { name: "Diastol", data: reversedDiastol },
                        { name: "Sistol", data: reversedSistol },
                        { name: "Tinggi fundus", data: reversedTinggiFundus },
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
        getKek() {
            let tempModalKek = [];
            axios
                .get(window.url + "api/getKek")
                .then((response) => {
                    this.bandingkek = response.data;
                    let kek = 0;
                    let normal = 0;

                    this.bandingkek.forEach((item) => {
                        if (item.lila_periksa >= 23.5) {
                            normal += 1;
                        } else if (item.lila_periksa < 23.5) {
                            kek += 1;
                            tempModalKek.push(item);
                        }
                    });
                    this.modalkek = tempModalKek;
                    this.reinitializeDataModal(this.modalkek);
                    this.pieKEKSeries = [kek, normal];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getKekPos($id) {
            let tempModalKek = [];
            axios
                .get(window.url + "api/getKekPos/" + $id)
                .then((response) => {
                    this.bandingkek = response.data;
                    let kek = 0;
                    let normal = 0;

                    this.bandingkek.forEach((item) => {
                        if (item.lila_periksa >= 23.5) {
                            normal += 1;
                        } else if (item.lila_periksa < 23.5) {
                            kek += 1;
                            tempModalKek.push(item);
                        }
                    });
                    this.modalkek = tempModalKek;
                    this.reinitializeDataModal(this.modalkek);
                    this.pieKEKSeries = [kek, normal];
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        },
        getKekPosByDate($id, $tanggal) {
            let tempModalKek = [];
            axios
                .get(window.url + "api/getKekPos/" + $id)
                .then((response) => {
                    this.bandingkek = response.data;
                    let kek = 0;
                    let normal = 0;

                    this.bandingkek.forEach((item) => {
                        if (item.lila_periksa >= 23.5) {
                            normal += 1;
                        } else if (item.lila_periksa < 23.5) {
                            kek += 1;
                            tempModalKek.push(item);
                        }
                    });
                    this.modalkek = tempModalKek;
                    this.reinitializeDataModal(this.modalkek);
                    this.pieKEKSeries = [kek, normal];
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        },
        getStatusPos($id) {
            axios
                .get(window.url + "api/getWusStatusPos/" + $id)
                .then((response) => {
                    this.status = response.data;
                    const {
                        tdmenikah,
                        hamil,
                        tdhamilsusu,
                        tdhamilanggur,
                        nifas,
                    } = this.status;
                    this.pieSeries = [
                        tdmenikah,
                        hamil,
                        tdhamilsusu,
                        tdhamilanggur,
                        nifas,
                    ];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getStatusPosByDate($id, $tanggal) {
            axios
                .get(window.url + "api/getWusStatusPos/" + $id)
                .then((response) => {
                    this.status = response.data;
                    const {
                        tdmenikah,
                        hamil,
                        tdhamilsusu,
                        tdhamilanggur,
                        nifas,
                    } = this.status;
                    this.pieSeries = [
                        tdmenikah,
                        hamil,
                        tdhamilsusu,
                        tdhamilanggur,
                        nifas,
                    ];
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getJumlahWus() {
            axios
                .get(window.url + "api/getJumlahWus")
                .then((response) => {
                    this.jumlahwus = response.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getJumlahWusposyandu($id) {
            axios
                .get(window.url + "api/getJumlahWusPosyandu/" + $id)
                .then((response) => {
                    this.jumlahwus = response.data;
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getKekLast12Months() {
            axios
                .get(window.url + "api/getKekLast12Months/")
                .then((response) => {
                    const kekData = [];
                    const normalData = [];
                    const months = [];

                    response.data.forEach((item) => {
                        kekData.push(item.KEK);
                        normalData.push(item.Normal);
                        months.push(item.month);
                    });
                    const reversedKekData = kekData.reverse();
                    const reversedNormalData = normalData.reverse();
                    const reversedMonths = months.reverse();

                    this.lineKekSeries = [
                        { name: "KEK", data: reversedKekData },
                        { name: "Normal", data: reversedNormalData },
                    ];
                    this.lineKekChartOptions.xaxis.categories = reversedMonths;
                    this.$nextTick(() => {
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
        getKekLast12MonthsPos($id) {
            axios
                .get(window.url + "api/getKekLast12MonthsPos/" + $id)
                .then((response) => {
                    const kekData = [];
                    const normalData = [];
                    const months = [];
                    response.data.forEach((item) => {
                        kekData.push(item.KEK);
                        normalData.push(item.Normal);
                        months.push(item.month);
                    });
                    const reversedKekData = kekData.reverse();
                    const reversedNormalData = normalData.reverse();
                    const reversedMonths = months.reverse();
                    this.lineKekSeries = [
                        { name: "KEK", data: reversedKekData },
                        { name: "Normal", data: reversedNormalData },
                    ];
                    this.lineKekChartOptions.xaxis.categories = reversedMonths;
                    this.$nextTick(() => {
                        console.log("Chart Ref:", this.$refs.lineKekChart);
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
        closeModal() {
            $("#taskModal").modal("hide");
        },
        applyFilters() {
            const posyanduId = this.filters.posyandu_id;
            const tanggal = this.filters.tgl_kegiatan;

            if (posyanduId && tanggal) {
                // Filter berdasarkan posyandu dan tanggal
                this.getStatusPosByDate(posyanduId, tanggal);
                this.getKekPosByDate(posyanduId, tanggal);
                this.getGemukPosByDate(posyanduId, tanggal);
            }
            else if (posyanduId) {
                // Filter hanya berdasarkan posyandu
                this.getWusPosyandu(posyanduId);
                this.getJumlahWusposyandu(posyanduId);
                this.getStatusPos(posyanduId);
                this.getKekLast12MonthsPos(posyanduId);
                this.getKekPos(posyanduId);
                this.getGemukPos(posyanduId);
                this.getPeriksaPos(posyanduId);
            }
            else {
                // Tidak ada filter
                this.getKekLast12Months();
                this.getStatus();
                this.getWus();
                this.getJumlahWus();
                this.getKek();
                this.getGemuk();
                this.getPeriksa();
            }
        },

        resetDateFilter() {
            this.filters.tgl_kegiatan = null;
            this.applyFilters();
        },


        handleDateChange() {
            this.applyFilters();
        },

        getWus() {
            axios
                .get(window.url + "api/getWus")
                .then((response) => {
                    this.wuses = response.data;
                    this.reinitializeDataTable(this.wuses);
                    this.getPosyandu();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getWusPosyandu(posyanduId) {
            axios
                .get(window.url + "api/getWusPosyandu/" + posyanduId)
                .then((response) => {
                    this.wuses = response.data;
                    this.reinitializeDataTable(this.wuses);
                    console.log(this.wuses);
                    this.getPosyandu();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        reinitializeDataModal(data) {
            const plainData = JSON.parse(JSON.stringify(data));
            const table = $("#tablekek");
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }
            table.DataTable({
                data: plainData,
                processing: true,
                serverSide: false,
                columns: this.getDataModalColumns(),
            });
        },
        reinitializeDataTable(data) {
            const plainData = JSON.parse(JSON.stringify(data));
            const table = $("#tablewus");
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }
            table.DataTable({
                data: plainData,
                processing: true,
                serverSide: false,
                columns: this.getDataTableColumns(),
            });
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
                            ? `${this.getAge(row.tanggal_lahir)} tahun`
                            : "Data missing";
                    },
                },
                {
                    data: "statuswus",
                    name: "statuswus",
                    render: (data) =>
                        `<div class="text-center">${data || "N/A"}</div>`,
                },
                {
                    data: "lila_periksa",
                    name: "lila_periksa",
                    render: (data) =>
                        `<div class="text-center">${data ? `${data} cm` : "N/A"
                        }</div>`,
                },
                { data: "posyandu_nama", name: "posyandu_nama" },
                {
                    data: null,
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    render: (data, type, row) => {
                        if (!row.wus_id) {
                            console.error(
                                "Error: wus_id is missing in row data",
                                row
                            );
                            return "<div class='text-center'>Error: ID missing</div>";
                        }
                        return this.getActionButtons(row);
                    },
                },
            ];
        },
        getDataModalColumns() {
            return [
                { data: "nama", name: "nama" },
            ];
        },

        getActionButtons(row) {
            return `
        <div class="row">
            <div class="col-4">
                <button type="button" class="btn btn-primary btn-sm" onclick="window.navigateToDetail(${row.wus_id})">Detail</button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-warning btn-sm mx-1 edit-task-btn" data-id="${row.wus_id}">Edit</button>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-danger btn-sm remove-task-btn" data-id="${row.wus_id}">Delete</button>
            </div>
        </div>
    `;
        },
        attachEventListeners() {
            const table = $("#tablewus");

            table.on("click", ".edit-task-btn", this.handleEditClick);
            table.on("click", ".remove-task-btn", this.handleRemoveClick);
        },

        handleEditClick(event) {
            const id = $(event.currentTarget).data("id");
            const task = this.wuses.find((wus) => wus.wus_id === id);

            if (!id) {
                console.error("Edit button missing ID.");
                return;
            }
            if (task) {
                this.editTask(task);
            } else {
                console.error("Task not found for ID:", id);
            }
        },

        handleRemoveClick(event) {
            const id = $(event.currentTarget).data("id");
            console.log("Delete button clicked, ID:", id);

            if (!id) {
                console.error("Delete button missing ID.");
                return;
            }

            const task = this.wuses.find((wus) => wus.wus_id === id);
            if (task) {
                this.removeTask(task);
            } else {
                console.error("Task not found for ID:", id);
            }
        },

        removeTask(task) {
            this.deleteMode = true;
            this.taskData.id = task.wus_id;
            this.taskData.nama = task.nama;
            $("#taskModal").modal("show");
        },

        editTask(task) {
            console.log("Editing task:", task.posyandus_id);
            this.editMode = true;
            this.deleteMode = false;
            this.taskData = {
                ...task,
                tanggal_lahir: parse(task.tanggal_lahir, 'dd-MM-yyyy', new Date())
            };
            this.$nextTick(() => {
                $("#taskModal").modal("show");
                $("#taskModal").on("shown.bs.modal", () => {
                    this.initializeSelect2();
                });
            });
        },

        updateTask() {
            this.validateTask();
            console.log("Updating task with ID:", this.taskData.wus_id);

            if (this.isTaskValid()) {
                axios
                    .post(
                        `${window.url}api/updateWus/${this.taskData.wus_id}`,
                        this.taskData
                    )
                    .then(() => {
                        this.getWus();
                    })
                    .catch((errors) => {
                        console.error(errors);
                    })
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            } else {
                console.log("Task is not valid", this.taskErrors);
            }
        },

        validateTask() {
            this.taskErrors = {
                nama: !this.taskData.nama,
                tanggal_lahir: !this.taskData.tanggal_lahir,
                statuswus: !this.taskData.statuswus,
                lila_wus: !this.taskData.lila_wus,
                posyandus_id: !this.taskData.posyandus_id,
            };
            this.taskData.tanggal_lahir = this.formatDate(
                this.taskData.tanggal_lahir
            );
        },

        isTaskValid() {
            return !Object.values(this.taskErrors).includes(true);
        },

        deleteTask() {
            axios
                .post(`${window.url}api/deleteWus/${this.taskData.id}`)
                .then(() => {
                    this.getWus();
                })
                .catch((errors) => {
                    console.error(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
    },
    mounted() {
        this.getWus();
        this.getStatus();
        this.getJumlahWus();
        this.getPosyandu();
        this.getKekLast12Months();
        this.getKek();
        this.getGemuk();
        this.getPeriksa();
        // this.checktanggal();
        window.navigateToDetail = (wusId) => {
            this.$router.push({ path: `/detail-wus/${wusId}` });
        };
        const datepicker = this.$refs.datepicker; // Tambahkan ref="datepicker" di VueDatePicker
        if (datepicker) {
            datepicker.$on('clear', this.resetDateFilter);
        }
    },
    watch: {
        'filters.tgl_kegiatan': {
            handler(newVal) {
                this.handleDateChange();
            }
        }
    },
};
</script>
