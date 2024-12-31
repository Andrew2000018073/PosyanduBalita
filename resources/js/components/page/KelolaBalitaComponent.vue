<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelola Balita</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">Kelola Balita</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

    <!-- Tabel Balitas -->
    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header">Tabel Balita</div>
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
            jumlahwus: null,
            deleteMode: false,
            editMode: false,
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
        };
    },
    mounted() {
        this.getBayis();
        this.getPosyandu(); // Fetch tasks when component is mounted
    },
    beforeDestroy() {
        if ($("#posyandu").data("select2")) {
            $("#posyandu").select2("destroy");
        }
    },

    methods: {
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
                    this.posyandus = response.data; // Simpan data ke properti tasks
                    this.initializeSelect2(); // Initialize select2 after data is loaded
                })
                .catch((errors) => {
                    console.log(errors); // Tangani kesalahan jika ada
                });
        },
        initializeSelect2() {
            const vm = this;

            // Destroy instance jika Select2 sudah diinisialisasi sebelumnya
            if ($.fn.select2) {
                if ($("#posyandu").data("select2")) {
                    $("#posyandu").select2("destroy");
                }
            }

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
        },

        closeModal() {
            $("#taskModal").modal("hide");
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
            <div class="col text-center">
                <button type="button" class="btn btn-warning edit-task-btn btn-sm" data-id="${row.balitas_id}">Edit</button>
            </div>
            <div class="col text-center">
                <button type="button" class="btn btn-danger  remove-task-btn btn-sm" data-id="${row.balitas_id}">Delete</button>
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

<style scoped>
@media (max-width: 768px) {




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

    .col .col-md-3 .col-sm-6 .col-xs-3 .col-12 {
        max-width: 50%;
    }

    .col-12 {
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 50%;
    }

    .content-header h1 {
        font-size: 1.8rem;
        margin: 0;
        font-size: 20px;
    }

    .content-header .breadcrumb {
        background-color: transparent;
        line-height: 1.8rem;
        margin-bottom: 0;
        padding: 0;
        font-size: 15px;
    }

    element {
        width: 50%;
    }

    table {
        font-size: 12px;
    }
}
</style>
