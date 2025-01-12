<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kegiatan Posyandu Balita</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/tambah-kegiatan">Tambah Kegiatan</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            Kegiatan Posyandu Balita
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

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
                                ? "Hapus Peserta " + taskData.bayi.namabalita
                                : editMode
                                    ? "Perbarui data peserta " +
                                    taskData.bayi.namabalita
                                    : "Create New Task"
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="!deleteMode">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tinggi">Panjang atau tinggi (cm)</label>
                                    <input type="number" class="form-control" id="tinggi" step="0.01"
                                        placeholder="Masukan tinggi atau panjang peserta"
                                        v-model="taskData.panjang_badan" />
                                    <span class="text-danger" v-show="taskErrors.panjang_badan">Masukan tinggi atau
                                        panjang
                                        peserta</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="berat">Berat Badan (kg)</label>
                                    <input type="number" class="form-control" step="0.01" id="berat"
                                        placeholder="Masukan berat badan peserta" v-model="taskData.berat_badan" />
                                    <span class="text-danger" v-show="taskErrors.berat_badan">Masukan berat badan
                                        peserta</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="lila">Lingkar Kepala (cm)</label>
                                    <input type="number" class="form-control" id="lila" step="0.01"
                                        placeholder="Masukan Lingkar Kepala(cm)" v-model="taskData.lingkep_periksa" />
                                    <span class="text-danger" v-show="taskErrors.lingkep_periksa">Masukan lingkar
                                        periksa</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="Nama Balita">Pilih Jenis Imunisasi</label>
                                    <select class="form-control select2" style="width: 100%" id="my-select-multiple"
                                        multiple="multiple" v-model="taskData.imunisasi_id">
                                        <option disabled value="">
                                            - Pilih imunisasi -
                                        </option>
                                        <option v-for="task in imunisasis" :key="task.id" :value="task.id">
                                            {{ task.nama }}
                                        </option>
                                    </select>
                                    <span class="text-danger" v-show="taskErrors.imunisasi_id">Silahkan Pilih
                                        Imunisasi!</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea id="catatan" cols="30" rows="10" class="form-control"
                                v-model="taskData.catatan"></textarea>
                            <span class="text-danger" v-show="taskErrors.catatan">Masukan catatan</span>
                        </div>
                    </div>
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Apakah Anda Yakin Ingin Menghapus Peserta
                            {{ taskData.nama }}?
                        </h5>
                    </div>
                </div>
                <div class="modal-footer" v-if="!deleteMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="updateTask">
                        Perbarui Data
                    </button>
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

    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Tambahkan data terkait kegiatan</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="example" class="table table-striped table-bordered w-100">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nama Balita</th>
                                        <th class="text-center">
                                            Panjang Badan
                                        </th>
                                        <th class="text-center">Berat Badan</th>
                                        <th class="text-center">
                                            Lingkar Kepala
                                        </th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(task, index) in tasks" :key="index" class="text-center">
                                        <td class="text-center">
                                            {{ task.bayi.namabalita }}
                                        </td>
                                        <td class="text-center">
                                            {{ task.panjang_badan }} cm
                                        </td>
                                        <td class="text-center">
                                            {{ task.berat_badan }} kg
                                        </td>
                                        <td class="text-center">
                                            {{ task.lingkep_periksa }} cm
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

                <div class="card-footer text-center" v-show="stats.status === 'belum selesai'">
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-success btn-sm" @click="storeIdAndNavigate">
                                Tambah Peserta
                            </button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-danger btn-sm" @click="updateStatus">
                                Selesai
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "select2/dist/css/select2.css";
import "select2";
import "datatables.net";
import "datatables.net-bs4";
import Swal from "sweetalert2";

export default {
    data() {
        return {
            editMode: false,
            deleteMode: false,
            tasks: [],
            stats: {
                status: "",
            },
            imunisasis: [],
            taskData: {
                bayis_id: "",
                kegiatanposyandu_balita_id: "",
                panjang_badan: "",
                berat_badan: "",
                lingkep_periksa: "",
                catatan: "",
                imunisasi_id: "",
                id: this.$route.params.id,
                status_kegiatan: "selesai",
            },
            taskErrors: {
                bayis_id: false,
                kegiatanposyandu_balita_id: false,
                panjang_badan: false,
                berat_badan: false,
                lingkep_periksa: false,
                catatan: false,
                imunisasi_id: false,
            },
        };
    },
    methods: {
        showAlert() {
            Swal.fire({
                title: "Selesai",
                text: "Terimakasih Telah Melaksanakan Kegiatan!",
                icon: "success",
                confirmButtonText: "OK",
            }).then(this.$router.push("/"));
        },
        storeIdAndNavigate() {
            const kegiatanId = this.$route.params.id;
            console.log("Storing ID:", kegiatanId);
            if (kegiatanId) {
                localStorage.setItem("PassedId", kegiatanId);
                console.log(
                    "Current localStorage value for PassedId:",
                    localStorage.getItem("PassedId")
                );
            } else {
                console.error("No ID found to store.");
            }
            this.$router.push({ name: "tambah-peserta-balita" });
        },
        deleteTask() {
            axios
                .post(
                    window.url + "api/deletePesertaBalita/" + this.taskData.id
                )
                .then((response) => {
                    this.getPeserta();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;
            this.getImunisasis();
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        removeTask(task) {
            this.deleteMode = true;
            this.editMode = false;
            this.taskData = { ...task };
            this.taskData.id = task.id;
            axios
                .get(window.url + "api/getNamaBayi/" + this.taskData.id)
                .then((response) => {
                    this.taskData.nama = response.data;
                });
            $("#taskModal").modal("show");
        },
        getPeserta() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/getPesertaBalita/" + id)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#example")) {
                        $("#example").DataTable().destroy();
                    }
                    this.tasks = response.data;
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
        getImunisasis() {
            axios
                .get(window.url + "api/getImunisasiBayi/")
                .then((response) => {
                    this.imunisasis = response.data;
                    this.$nextTick(() => {
                        this.initializemultiSelect2();
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        initializemultiSelect2() {
            const vm = this;
            $("#my-select-multiple")
                .select2({
                    dropdownAutoWidth: true,
                    width: "100%",
                })
                .on("change", function () {
                    const selectedImunisasiId = $(this).val();
                    vm.taskData.imunisasi_id = selectedImunisasiId;
                    console.log(
                        "Selected Imunisasi  IDs (Multiple):",
                        selectedImunisasiId
                    );
                });
        },
        updateStatus() {
            axios
                .post(
                    window.url + "api/updateKegiatan/" + this.taskData.id,
                    this.task
                )
                .then(this.showAlert());
        },
        getStatus() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/getStatus/" + id)
                .then((response) => {
                    if (response.data.length > 0) {
                        this.stats.status = response.data[0].status_kegiatan;
                        // console.log("Status:", this.stats.status);
                    } else {
                        console.log("No data found");
                    }
                })
                .catch((error) => {
                    console.error("Error fetching status:", error);
                });
        },
        updateTask() {
            const kegiatanId = this.$route.params.id;
            this.taskData.kegiatanposyandu_balita_id = kegiatanId;

            // Validasi
            this.taskErrors.bayis_id = !this.taskData.bayis_id;
            this.taskErrors.lingkep_periksa = !this.taskData.lingkep_periksa;
            this.taskErrors.panjang_badan = !this.taskData.panjang_badan;
            this.taskErrors.berat_badan = !this.taskData.berat_badan;
            this.taskErrors.catatan = !this.taskData.catatan;

            if (
                !this.taskErrors.bayis_id &&
                !this.taskErrors.lingkep_periksa &&
                !this.taskErrors.panjang_badan &&
                !this.taskErrors.berat_badan &&
                !this.taskErrors.catatan
            ) {
                axios
                    .post(
                        window.url +
                        "api/updatePesertaBalita/" +
                        this.taskData.id,
                        this.taskData
                    )
                    .then((response) => {
                        this.getPeserta();
                    })
                    .catch((errors) => {
                        console.log(errors);
                    })
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            }
        },
        closeModal() {
            $("#taskModal").modal("hide");
        },
    },
    mounted() {
        this.getPeserta();
        this.getImunisasis();
        this.getStatus();
    },
};
</script>

<style scoped>
@media (max-width: 768px) {

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

    .breadcrumb-item.active {
        color: #6c757d;
        font-size: 13px;
    }

    table thead th:nth-child(4),
    table tbody td:nth-child(4) {
        display: none;
    }

    table td,
    table th {

        font-size: 11px;

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
}
</style>
