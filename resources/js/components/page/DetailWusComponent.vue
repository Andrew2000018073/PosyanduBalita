<template>
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
                                    ? "Perbarui data anak " + taskData.namabalita
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
                    <div v-if="editMode">
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <VueDatePicker v-model="taskData.tanggal_lahir" :format="format" placeholder="dd-mm-yyyy"
                                :enable-time-picker="false"></VueDatePicker>
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
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Hapus data {{ taskData.namabalita }} ?
                        </h5>
                    </div>
                    <div v-if="hapusKontrasepsi">
                        <h5 class="text-center text-danger">
                            Hapus data {{ taskData.nama_kontrasepsi }} ?
                        </h5>
                    </div>
                    <div v-if="selesaiKontrasepsi">
                        <h5 class="text-center text-danger">
                            Selesai menggunakan
                            {{ taskData.nama_kontrasepsi }} ?
                        </h5>
                    </div>
                </div>
                <!-- Edit Mode -->
                <div class="modal-footer" v-if="!deleteMode && editMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="editMode ? updateTask() : storeImunisasi()"
                        v-if="!deleteMode">
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
                <!-- Delete kontrasepsi -->
                <div class="modal-footer" v-if="hapusKontrasepsi">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteKontrasepsi">
                        Hapus Data
                    </button>
                </div>
                <div class="modal-footer" v-if="selesaiKontrasepsi">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="selesaiGuna">
                        Berhenti Menggunakan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kehamilan -->
    <div class="modal fade" id="hamilModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
        aria-hidden="true">
        <div :class="`modal-dialog modal-dialog-centered ${!deleteHamil ? 'modal-lg' : 'modal-md'
            }`">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">
                        {{
                            deleteHamil
                                ? "Hapus data Kehamilan ke-" +
                                hamil.kehamilan_ke
                                : "Perbarui data kehamilan ke-" +
                                hamil.kehamilan_ke
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on editHamil or deleteHamil -->
                    <div v-if="editHamil">
                        <div class="form-group">
                            <label for="tanggal_awal_hamil">Tanggal Awal Hamil</label>
                            <VueDatePicker v-model="hamil.tanggal_awal_hamil" :format="format"
                                placeholder="Tanggal Awal Hamil" :enable-time-picker="false"></VueDatePicker>
                            <span class="text-danger" v-show="hamilErrors.tanggal_awal_hamil">Silahkan masukan tanggal
                                awal hamil!</span>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_daftar">Tanggal Daftar</label>
                            <VueDatePicker v-model="hamil.tanggal_daftar" :format="format" placeholder="Tanggal Daftar"
                                :enable-time-picker="false"></VueDatePicker>
                            <span class="text-danger" v-show="hamilErrors.tanggal_daftar">Silahkan masukan tanggal
                                daftar</span>
                        </div>
                        <div class="form-group">
                            <label for="kehamilan_ke">Nomor kehamilan</label>
                            <input v-model="hamil.kehamilan_ke" type="number" class="form-control" id="kehamilan_ke"
                                placeholder="Masukan nomor kehamilan" />
                            <span class="text-danger" v-show="hamilErrors.kehamilan_ke">Silahkan masukan nomor
                                kehamilan</span>
                        </div>
                    </div>
                    <div v-if="deleteHamil">
                        <h5 class="text-center text-danger">
                            Hapus data hamil?
                        </h5>
                    </div>
                </div>
                <!-- Edit Mode -->
                <div class="modal-footer" v-if="editHamil">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="updateHamil">
                        Perbarui Data
                    </button>
                </div>
                <!-- Delete Mode -->
                <div class="modal-footer" v-if="deleteHamil">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="hapusHamil">
                        Hapus Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Informasi Personal -->
    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi WUS</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">Nama Wus</div>
                        <div class="col-1">:</div>
                        <div class="text-left col">
                            {{ informasis.nama }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Umur</div>
                        <div class="col-1">:</div>
                        <div class="col">
                            {{
                                informasis.tanggal_lahir
                                    ? getAge(informasis.tanggal_lahir)
                                    : "N/A"
                            }}
                            tahun
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-4">StatusWus</div>
                        <div class="col-1">:</div>
                        <div class="col">
                            {{ informasis.statuswus }}
                        </div>
                    </div>
                </div>
                <div class="card-footer"></div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    <!-- Card Tabel Anak terdaftar -->
    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">
                    Data Anak yang terdaftar dalam posyandu
                </div>
                <div class="card-body">
                    <table id="tablebayis" class="table table-striped table-bordered w-100" ref="imunisasiTable">
                        <thead>
                            <tr>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Umur</th>
                                <th class="text-center">Berat badan lahir</th>
                                <th class="text-center">Panjang badan lahir</th>
                                <th class="text-center">
                                    Lingkar kepala lahir
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in bayis" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ task.namabalita }}
                                </td>
                                <td class="text-center">
                                    {{ getAgeInMonths(task.tanggal_lahir) }}
                                    bulan
                                </td>
                                <td class="text-center">
                                    {{ task.beratbadan_lahir }} kg
                                </td>
                                <td class="text-center">
                                    {{ task.panjangbadan_lahir }} cm
                                </td>
                                <td class="text-center">
                                    {{ task.likep_lahir }} cm
                                </td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                @click="editTask(task)">
                                                Edit
                                            </button>
                                        </div>
                                        <div class="col">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                @click="removeTask(task)">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tabel Kehamilan -->
    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">
                    Data kehamilan yang terdaftar dalam posyandu
                </div>
                <div class="card-body">
                    <table id="tablehamil" class="table table-striped table-bordered w-100" ref="tablehamil">
                        <thead>
                            <tr>
                                <th class="text-center">Kehamilan Ke</th>
                                <th class="text-center">Tanggal Awal Hamil</th>
                                <th class="text-center">Tanggal Daftar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in kehamilan" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ task.kehamilan_ke }}
                                </td>
                                <td class="text-center">
                                    {{ task.tanggal_awal_hamil }}
                                </td>
                                <td class="text-center">
                                    {{ task.tanggal_daftar }}
                                </td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-warning btn-sm mx-1"
                                                @click="editKehamil(task)">
                                                Edit
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-danger btn-sm mr-5"
                                                @click="removeHamil(task)">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                        <apexchart ref="lineperiksaChart" type="line" :options="lineperiksaOptions"
                            :series="lineperiksaSeries"></apexchart>
                    </div>
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
import moment from "moment";
import { update } from "lodash";

export default {
    components: {
        VueDatePicker,
        apexchart: ApexCharts,
    },
    data() {
        return {
            kegiatan: [],
            bayis: [],
            informasis: [],
            pakai: [],
            kehamilan: [],
            format: "dd-MM-yyyy",
            deleteMode: false,
            editMode: false,
            deleteHamil: false,
            editHamil: false,
            hapusKontrasepsi: false,
            selesaiKontrasepsi: false,
            banyakanakpus: 0,
            pusid: "",
            taskData: {
                id: "",
                tanggal_lahir: "",
                beratbadan_lahir: "",
                panjangbadan_lahir: "",
                likep_lahir: "",
                posyandus_id: "",
                date: "",
                kontrasepsi_id: "",
                pus_id: this.$route.params.id,
            },
            taskErrors: {
                jumlah_anak_hidup: false,
                lingkar_perut_suami: false,
                tanggal_lahir: false,
                beratbadan_lahir: false,
                panjangbadan_lahir: false,
                likep_lahir: false,
                posyandus_id: false,
                kontrasepsi_id: false,
            },
            hamil: {
                kehamilan_ke: "",
                tanggal_awal_hamil: "",
                tanggal_daftar: "",
            },
            hamilErrors: {
                kehamilan_ke: false,
                tanggal_awal_hamil: false,
                tanggal_daftar: false,
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
                colors: [
                    "#F8B500",
                    "#3d85c6",
                    "#393E46",
                    "#D2FF72",
                    "#15B392",
                    "#FD8B51",
                ],
                dataLabels: { enabled: true },
                stroke: {
                    curve: "smooth",
                    connectNulls: true,
                },
                title: {
                    text: "Rata-rata pemeriksaan WUS",
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
        };
    },

    methods: {
        getAge(dateString) {
            if (!dateString || typeof dateString !== "string") {
                console.error("Invalid date string:", dateString);
                return "N/A";
            }
            const [day, month, year] = dateString.split("-");
            if (!day || !month || !year) {
                console.error("Incomplete date:", dateString);
                return "N/A";
            }
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

        initializeSelect2() {
            const vm = this;
            if (
                $.fn.select2 &&
                $("#my-select").hasClass("select2-hidden-accessible")
            ) {
                $("#my-select").select2("destroy");
            }
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    vm.taskData.pus_id = $(this).val();
                });
            $("#posyandu")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    vm.taskData.posyandus_id = $(this).val();
                });
        },
        formatDate(date) {
            const [year, month, day] = new Date(date)
                .toISOString()
                .split("T")[0]
                .split("-");
            return `${day}-${month}-${year}`;
        },
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;
            this.hapusKontrasepsi = false;
            this.selesaiKontrasepsi = false;
            this.taskData = { ...task };
            if (typeof this.taskData.tanggal_lahir === "string") {
                const [day, month, year] =
                    this.taskData.tanggal_lahir.split("-");
                this.taskData.tanggal_lahir = new Date(
                    `${year}-${month}-${day}`
                );
            }

            this.taskData.posyandus_id = task.posyandus_id || "";
            $("#taskModal").modal("show");
            this.$nextTick(() => {
                $("#posyandu")
                    .val(this.taskData.posyandus_id)
                    .trigger("change");
            });

            $("#taskModal").modal("show");

            this.$nextTick(() => {
                this.initializeSelect2();
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
            );

            if (
                this.taskData.tanggal_lahir &&
                this.taskData.panjangbadan_lahir &&
                this.taskData.beratbadan_lahir &&
                this.taskData.likep_lahir &&
                this.taskData.posyandus_id
            ) {
                axios
                    .post(
                        window.url + "api/updateBayis/" + this.taskData.id,
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
        removeTask(task) {
            this.deleteMode = true;
            this.editMode = false;
            this.hapusKontrasepsi = false;
            this.selesaiKontrasepsi = false;
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        deleteTask() {
            axios
                .post(window.url + "api/deleteBayi/" + this.taskData.id)
                .then((response) => {
                    this.getBayis();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
        getData() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/detail-wus/" + id)
                .then((response) => {
                    this.informasis = response.data;
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                });
        },
        getpusid() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/getPusid/" + id)
                .then((response) => {
                    this.pusid = response.data;
                    this.getBayis();
                })
                .catch((error) => {
                    console.error("Error fetching Pusid data:", error);
                });
        },
        getBayis() {
            axios
                .get(window.url + "api/getAnak/" + this.pusid)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#tablebayis")) {
                        $("#tablebayis").DataTable().destroy();
                    }

                    this.bayis = response.data;
                    this.banyakanakpus = this.bayis.length;

                    this.$nextTick(() => {
                        $("#tablebayis").DataTable({
                            pageLength: 10,
                            lengthChange: false,
                            destroy: true,
                        });
                    });
                })
                .catch((error) => {
                    console.error("Error fetching Bayis data:", error);
                });
        },
        getHamil() {
            axios
                .get(window.url + "api/getHamil/" + this.$route.params.id)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#tablehamil")) {
                        $("#tablehamil").DataTable().destroy();
                    }
                    this.kehamilan = response.data;
                    this.$nextTick(() => {
                        $("#tablehamil").DataTable({
                            pageLength: 10,
                            lengthChange: false,
                        });
                    });
                })
                .catch((error) => {
                    console.error("Error fetching Hamil data:", error);
                });
        },

        getAgeInMonths(tanggal) {
            const lahir = tanggal;
            const [day, month, year] = lahir.split("-");
            const birthDate = new Date(`${year}-${month}-${day}`);
            const today = new Date();
            const yearsDiff = today.getFullYear() - birthDate.getFullYear();
            const monthsDiff = today.getMonth() - birthDate.getMonth();
            let ageInMonths = yearsDiff * 12 + monthsDiff;
            if (today.getDate() < birthDate.getDate()) {
                ageInMonths--;
            }
            return ageInMonths;
        },
        closeModal() {
            $("#taskModal").modal("hide");
        },
        getPosyandu() {
            axios
                .get(window.url + "api/getPosyandu")
                .then((response) => {
                    this.posyandus = response.data;
                    this.$nextTick(() => {
                        $("#posyandu")
                            .val(this.taskData.posyandus_id)
                            .trigger("change"); // Set initial value
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        TambahKontrasepsi() {
            this.deleteMode = false;
            this.editMode = false;
            $("#taskModal").modal("show");
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
        getPakai() {
            axios
                .get(window.url + "api/getAlatpakai/" + this.$route.params.id)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#tablekontrasepsis")) {
                        $("#tablekontrasepsis").DataTable().destroy();
                    }
                    this.pakai = response.data;
                    this.$nextTick(() => {
                        $("#tablekontrasepsis").DataTable({
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
        storeIdAndNavigate() {
            const pusId = this.$route.params.id;
            console.log("Storing ID:", pusId);
            if (pusId) {
                localStorage.setItem("PassedId", pusId);
                console.log(
                    "Current localStorage value for PassedId:",
                    localStorage.getItem("PassedId")
                );
            } else {
                console.error("No ID found to store.");
            }
            this.$router.push({ name: "tambah-kontrasepsi" });
        },
        getPeriksa() {
            axios
                .get(
                    window.url +
                    "api/getPeriksaLast12Months/" +
                    this.$route.params.id
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
        editKehamil(task) {
            this.editHamil = true;
            this.deleteHamil = false;
            this.hamil = {
                ...task,
                tanggal_awal_hamil: task.tanggal_awal_hamil
                    ? moment(task.tanggal_awal_hamil, "DD-MM-YYYY").toDate()
                    : null,
                tanggal_daftar: task.tanggal_daftar
                    ? moment(task.tanggal_daftar, "DD-MM-YYYY").toDate()
                    : null,
            };
            $("#hamilModal").modal("show");
        },
        updateHamil() {
            this.hamil.tanggal_awal_hamil = moment(
                this.hamil.tanggal_awal_hamil
            ).format("DD-MM-YYYY");
            this.hamil.tanggal_daftar = moment(
                this.hamil.tanggal_daftar
            ).format("DD-MM-YYYY");
            axios
                .post(
                    window.url + "api/updateHamil/" + this.hamil.id,
                    this.hamil
                )
                .then((response) => {
                    console.log("Data berhasil diperbarui:", response);
                    this.getHamil();
                })
                .catch((error) => {
                    console.log("Error saat memperbarui data:", error);
                })
                .finally(() => {
                    $("#hamilModal").modal("hide");
                });
        },
        removeHamil(task) {
            this.deleteHamil = true;
            this.editHamil = false;
            this.hamil = { ...task }; $("#hamilModal").modal("show");
        },
        hapusHamil() {
            axios
                .post(window.url + "api/deleteHamil/" + this.hamil.id)
                .then((response) => {
                    this.getHamil();
                    console.log("Data berhasil dihapus:", response);
                })
                .catch((errors) => {
                    console.log("Error saat menghapus data:", errors);
                })
                .finally(() => {
                    $("#hamilModal").modal("hide");
                });
        },
    },

    mounted() {
        this.getData();
        this.getpusid();
        this.getBayis();
        this.getPosyandu();
        this.getPakai();
        this.getPeriksa();
        this.getHamil();
    },
};
</script>
