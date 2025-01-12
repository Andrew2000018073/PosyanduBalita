<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kegiatan Posyandu Wus</h1>
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
                            Kegiatan Posyandu Wus
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

    <!-- Alert -->
    <div class="row">
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
                <div class="card-body">
                    Silahkan meminta peserta untuk melakukan rujuk ke puskes
                    jika tanda peringatan
                    <i class="fi fi-sr-exclamation text-danger"></i>
                    muncul
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
        aria-hidden="true">
        <div :class="`modal-dialog modal-dialog-centered ${!editMode ? 'modal-md' : 'modal-lg'
            }`">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">
                        {{
                            deleteMode
                                ? "Hapus Peserta " + taskData.wus.nama
                                : editMode
                                    ? "Perbarui data peserta " + taskData.wus.nama
                                    : "Akhiri Kegiatan?"
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="editMode">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="Status">Status</label>
                                <div class="form-group">
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
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="totalPill">Total Pill Tambah Darah</label>
                                    <input type="number" class="form-control" v-model="taskData.tablettambah_darahs_kuartal
                                        " />
                                    <span class="text-danger" v-if="
                                        taskErrors.tablettambah_darahs_kuartal
                                    ">Data Belum Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lila">Lingkar Lengan</label>
                                    <input type="number" step="0.01" class="form-control"
                                        v-model="taskData.lila_periksa" />
                                    <span class="text-danger" v-if="taskErrors.lila_periksa">Data Belum Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lingkep">Lingkar Perut</label>
                                    <input type="number" step="0.01" class="form-control"
                                        v-model="taskData.lingkarperut_periksa" />
                                    <span class="text-danger" v-if="taskErrors.lingkarperut_periksa">Data Belum
                                        Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="Tensi">Tekanan Darah</label>

                                <div class="row">
                                    <div class="col-5">
                                        <div class="form-group">
                                            <input type="number" class="form-control" id="tensi" placeholder="minium"
                                                v-model="taskData.sistol" />
                                            <span class="text-danger" v-show="taskErrors.sistol">Masukan hasil
                                                pemerkisaan
                                                tensi</span>
                                        </div>
                                    </div>
                                    <div class="col-2 text-center">
                                        <div class="form-group">
                                            <p>/</p>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <div class="form-group">
                                            <input type="number" class="form-control" id="tensi" placeholder="maksimal"
                                                v-model="taskData.diastol" />
                                            <span class="text-danger" v-show="taskErrors.diastol">Masukan hasil
                                                pemerkisaan
                                                tensi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tinggifundus">Tinggi Fundus</label>
                                    <input type="number" step="0.01" class="form-control"
                                        v-model="taskData.tinggi_fundus" />
                                    <span class="text-danger" v-if="taskErrors.tinggi_fundus">Data Belum Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="imunisasi">Imunisasi</label>
                                    <input type="number" class="form-control" v-model="taskData.imunisasi_kehamilans_kuartal
                                        " />
                                    <span class="text-danger" v-if="
                                        taskErrors.imunisasi_kehamilans_kuartal
                                    ">Data Belum Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="vitamin">Vitamin</label>
                                    <input type="number" class="form-control" v-model="taskData.vitamin_kuartal" />
                                    <span class="text-danger" v-if="taskErrors.vitamin_kuartal">Data Belum
                                        Terisi!</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="keluhan">Keluhan</label>
                                    <textarea rows="3" class="form-control" v-model="taskData.keluhan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Apakah Anda Yakin Ingin Menghapus Peserta
                            {{ taskData.nama }}?
                        </h5>
                    </div>

                    <div v-if="selesaiMode">
                        <h5 class="text-center text-danger">
                            Apakah anda ingin mengakhiri kegiatan?
                        </h5>
                    </div>
                </div>
                <div class="modal-footer" v-if="editMode">
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
                <div class="modal-footer" v-if="selesaiMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="updateStatus">
                        Tandai Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel -->
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
                                        <th class="text-center">Nama WUS</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">
                                            Tekanan darah
                                        </th>
                                        <th class="text-center">
                                            Lingkar Lengan
                                        </th>
                                        <th class="text-center">
                                            Lingkar Perut
                                        </th>
                                        <th class="text-center">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(task, index) in tasks" :key="index" class="text-center">
                                        <td class="text-center">
                                            {{ task.wus.nama }}
                                        </td>
                                        <td class="text-center">
                                            {{ task.wus.statuswus }}
                                        </td>
                                        <td :class="`text-center ${tensinormal(task.sistol)
                                            ? 'text-danger'
                                            : 'text-primary'
                                            }`">
                                            {{ task.sistol }} /
                                            {{ task.diastol }} mmHg
                                            <i class="fi fi-sr-exclamation" v-show="tensinormal(task.sistol)
                                                "></i>
                                        </td>
                                        <td :class="`text-center ${iskek(task.lila_periksa)
                                            ? 'text-danger'
                                            : 'text-primary'
                                            }`">
                                            {{ task.lila_periksa }} cm
                                            <i class="fi fi-sr-exclamation" v-show="iskek(task.lila_periksa)
                                                "></i>
                                        </td>
                                        <td class="text-center">
                                            {{ task.lingkarperut_periksa }} cm
                                        </td>
                                        <td class="text-center">
                                            <div v-if="stats.status === 'belum selesai'">
                                                <div class="row">
                                                    <div class="col">
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            @click="editTask(task)">
                                                            Edit
                                                        </button>
                                                    </div>
                                                    <div class="col">
                                                        <button type="button" class="btn btn-danger btn-sm mt-2"
                                                            @click="removeTask(task)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="stats.status === 'selesai' && !iskader">
                                                <div class="row">
                                                    <div class="col">
                                                        <button type="button" class="btn btn-warning btn-sm"
                                                            @click="editTask(task)">
                                                            Edit
                                                        </button>
                                                    </div>
                                                    <div class="col">
                                                        <button type="button" class="btn btn-danger btn-sm mt-2"
                                                            @click="removeTask(task)">
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>


                                            </div>
                                            <div v-if="stats.status === 'selesai' && iskader">
                                                <p class="text-danger">Silahkan hubungi ketua kader atau koordinator
                                                    untuk mengubah data</p>
                                            </div>

                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center">Nama WUS</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">
                                            Tekanan darah
                                        </th>
                                        <th class="text-center">
                                            Lingkar Lengan
                                        </th>
                                        <th class="text-center">
                                            Lingkar Perut
                                        </th>
                                        <th class="text-center">
                                            Aksi</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center" v-show="stats.status === 'belum selesai'">
                    <div class="row">
                        <div class="col">
                            <button class="btn btn-success" @click="storeIdAndNavigate">
                                Tambah Peserta
                            </button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-danger" @click="kegiatanselesai">
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
import "datatables.net";
import "datatables.net-bs4";
import Swal from "sweetalert2";

export default {
    data() {
        return {
            selesaiMode: false,
            editMode: false,
            deleteMode: false,
            abnormaltensi: false,
            isKoordinator: false,
            isketuakader: false,
            iskader: false,
            tasks: [],
            stats: {
                status: "",
            },
            taskData: {
                wus_id: "",
                vitamin_kuartal: "",
                kegiatanposyandu_w_u_s_id: "",
                tablettambah_darahs_kuartal: "",
                imunisasi_kehamilans_kuartal: "",
                lila_periksa: "",
                lingkarperut_periksa: "",
                diastol: "",
                sistol: "",
                tinggi_fundus: "",
                keluhan: "",
                nama: "",
                statuswus: "",
                id: this.$route.params.id,
                status_kegiatan: "selesai",
            },
            taskErrors: {
                wus_id: false,
                vitamin_kuartal: false,
                tablettambah_darahs_kuartal: false,
                imunisasi_kehamilans_kuartal: false,
                lila_periksa: false,
                lingkarperut_periksa: false,
                tensi: false,
                tinggi_fundus: false,
                keluhan: false,
                statuswus: false,
                diastol: false,
                sistol: false,
            },
        };
    },
    methods: {
        async checkRole() {
            try {
                const token = localStorage.getItem('token');

                const response = await axios.get(window.url + 'api/user/roles', {
                    headers: {
                        Authorization: `Bearer ${token}`
                    }
                });

                const roles = response.data.roles;
                const permissions = response.data.permissions;

                console.log('Roles:', roles);
                console.log('Permissions:', permissions);

                if (roles.includes('koordinator')) {
                    console.log('User adalah Koordinator');
                    this.isKoordinator = true;
                } else if (roles.includes('ketua kader')) {
                    console.log('User adalah Ketua Kader');
                    this.isketuakader = true;
                } else if (roles.includes('kader')) {
                    console.log('User adalah Kader');
                    this.iskader = true;
                }
            } catch (error) {
                console.error('Gagal memeriksa role:', error.response?.data || error.message);
            }
        },
        iskek($event) {
            if ($event >= 23.5) {
                return false;
            } else {
                return true;
            }
        },
        tensinormal($event) {
            if ($event < 140 && $event >= 90) {
                return false;
            } else {
                return true;
            }
        },
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
            this.$router.push({ name: "tambah-peserta-wus" });
        },
        deleteTask() {
            axios
                .post(window.url + "api/deletePesertaWus/" + this.taskData.id)
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

        updateStatus() {
            $("#taskModal").modal("hide");
            axios
                .post(
                    window.url + "api/updateKegiatan/" + this.taskData.id,
                    this.task
                )
                .then(this.showAlert());
        },
        updateTask() {
            this.taskData.kegiatanposyandu_w_u_s_id = this.taskData.id;
            this.taskErrors.wus_id = !this.taskData.wus_id;
            this.taskErrors.diastol = !this.taskData.diastol;
            this.taskErrors.sistol = !this.taskData.sistol;
            this.taskErrors.lingkarperut_periksa =
                !this.taskData.lingkarperut_periksa;
            this.taskErrors.lila_periksa = !this.taskData.lila_periksa;
            this.taskErrors.keluhan = !this.taskData.keluhan;
            if (
                !this.taskErrors.wus_id &&
                !this.taskErrors.lila_periksa &&
                !this.taskErrors.lingkarperut_periksa &&
                !this.taskErrors.diastol &&
                !this.taskErrors.sistol &&
                !this.taskErrors.keluhan
            ) {
                axios
                    .post(
                        window.url + "api/updatePesertaWus/" + this.taskData.id,
                        this.taskData
                    )
                    .then((response) => {
                        this.getPeserta();
                    })
                    .catch((errors) => {
                        console.log(errors);
                    })
                    .then(
                        axios
                            .post(
                                window.url +
                                "api/updateWusPeserta/" +
                                this.taskData.wus_id,
                                this.taskData
                            )
                            .then((response) => {
                                const createdId = response.data.id;
                                console.log("Created ID:", createdId);
                                console.log(response.data);
                            })
                            .catch((errors) => {
                                console.log(errors);
                            })
                    )
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            }
        },
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;
            this.selesaiMode = false;
            this.taskData = { ...task };
            axios
                .get(window.url + "api/getNamaWus/" + this.taskData.id)
                .then((response) => {
                    if (response.data && response.data.wus) {
                        this.taskData.statuswus = response.data.wus.statuswus;
                        console.log(this.taskData.statuswus);
                    } else {
                        console.error("WUS data is undefined in the response.");
                    }
                })
                .catch((error) => {
                    console.error("Error fetching WUS data:", error);
                });

            $("#taskModal").modal("show");
        },
        kegiatanselesai() {
            this.deleteMode = false;
            this.editMode = false;
            this.selesaiMode = true;
            $("#taskModal").modal("show");
        },

        removeTask(task) {
            this.deleteMode = true;
            this.editMode = false;
            this.selesaiMode
            this.taskData = { ...task };
            this.taskData.id = task.id;
            axios
                .get(window.url + "api/getNamaWus/" + this.taskData.id)
                .then((response) => {
                    this.taskData.nama = response.data.wus.nama;
                });
            $("#taskModal").modal("show");
        },

        getStatus() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/getStatus/" + id)
                .then((response) => {
                    if (response.data.length > 0) {
                        this.stats.status = response.data[0].status_kegiatan;
                        console.log("Status:", this.stats.status);
                    } else {
                        console.log("No data found");
                    }
                })
                .catch((error) => {
                    console.error("Error fetching status:", error);
                });
        },

        getPeserta() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/getPesertaWus/" + id)
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
        closeModal() {
            $("#taskModal").modal("hide");
        },
    },
    mounted() {
        this.getPeserta();
        this.getStatus();
        this.checkRole();
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
        font-size: 11px;
    }

    .table-header {
        background-color: #28a745;
        color: white;
        padding: 6px 10px;
        text-align: center;
        font-weight: bold;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .card-body {
        font-size: 10px;
    }
}
</style>
