<template>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelola Posyandu</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">Kelola Posyandu</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

    <!-- Modal Data -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
        aria-hidden="true">
        <div :class="`modal-dialog modal-dialog-centered ${!deleteMode ? 'modal-lg' : 'modal-md'
            }`">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">
                        {{
                            deleteMode
                                ? "Hapus Posyandu " + taskData.nama_posyandu
                                : editMode
                                    ? "Perbarui Posyandu " + taskData.nama_posyandu
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
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="namaposyandu">Nama Posyandu</label>
                                    <input type="text" class="form-control" v-model="taskData.nama_posyandu" />
                                    <span class="text-danger" v-if="taskErrors.nama_posyandu">Data Belum Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ketuaKader">Ketua Kader</label>
                                    <input type="text" class="form-control" v-model="taskData.ketua_kader" />
                                    <span class="text-danger" v-if="taskErrors.ketua_kader">Data Belum Terisi!</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="desa">Desa</label>
                                    <input type="text" class="form-control" v-model="taskData.desa" />
                                    <span class="text-danger" v-if="taskErrors.desa">Data Belum Terisi!</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="alamatLengkap">Alamat</label>
                                    <textarea rows="3" class="form-control"
                                        v-model="taskData.alamat_lengkap"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Apakah Anda Yakin Ingin Menghapus Posyandu
                            {{ taskData.nama_posyandu }}?
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

    <!-- Modal Aksi -->
    <div class="modal fade" tabindex="-1" role="dialog" id="aksiModal">
        <div class="modal-dialog  modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lakukan aksi pada {{ taskData.nama_posyandu }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Lakukan aksi pada posyandu ini</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" @click="editTask">
                        Edit
                    </button>

                    <button type="button" class="btn btn-danger btn-sm" @click="removeTask">
                        Delete
                    </button>

                    <router-link :to="`/detail-posyandu/${id}`" class="btn btn-info btn-sm mx-1" data-dismiss="modal">
                        Detail
                    </router-link>

                    <button type="button" class="btn btn-secondary btn-sm mx-1" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- RIL content -->
    <div class="card card-success">
        <div class="card-header">Tabel Posyandu</div>
        <div class="card-body">
            <div class="row">
                <div class="table-responsive">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Ketua Kader</th>
                                <th class="text-center">Nama Posyandu</th>
                                <th class="text-center">Desa</th>
                                <th class="text-center">Alamat Lengkap</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in tasks" :key="index" class="text-center">
                                <td>{{ task.ketua_kader }}</td>
                                <td>{{ task.nama_posyandu }}</td>
                                <td>{{ task.desa }}</td>
                                <td>{{ task.alamat_lengkap }}</td>
                                <td class="text-center">
                                    <div v-if="iskader">
                                        <div class="row">
                                            <div class="col">
                                                <router-link :to="{
                                                    path: 'detail-posyandu/:id',
                                                    params: { id: task.id },
                                                    name: 'detail-posyandu',
                                                }" class="btn btn-info btn-sm mx-1">
                                                    Detail
                                                </router-link>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="isKoordinator || isketuakader">
                                        <div class="row">
                                            <div class="col">
                                                <button class="btn btn-primary btn-sm mx-1"
                                                    @click="aksiTask(task)">Aksi</button>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer" v-if="isKoordinator || isketuakader">
            <div class="col">
                <router-link to="tambah-posyandu">
                    <button type="button" class="btn btn-block btn-success btn-sm">
                        Tambah Posyandu
                    </button>
                </router-link>
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

export default {
    data() {
        return {
            editMode: false,
            deleteMode: false,
            tasks: [],
            id: null,
            taskData: {
                id: null,
                ketua_kader: "",
                nama_posyandu: "",
                desa: "",
                alamat_lengkap: "",
            },
            taskErrors: {
                ketua_kader: false,
                nama_posyandu: "",
                desa: false,
                alamat_lengkap: false,
            },
            isKoordinator: false,
            isketuakader: false,
            iskader: false
        };
    },
    methods: {
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

                console.log('Roles:', roles);
                console.log('Permissions:', permissions);

                // Contoh penggunaan role
                if (roles.includes('koordinator')) {
                    console.log('User adalah Koordinator');
                    this.isKoordinator = true; // Set state untuk role koordinator
                } else if (roles.includes('ketua kader')) {
                    console.log('User adalah Ketua Kader');
                    this.isketuakader = true; // Set state untuk role ketua kader
                } else if (roles.includes('kader')) {
                    console.log('User adalah Kader');
                    this.iskader = true; // Set state untuk role kader
                }
            } catch (error) {
                console.error('Gagal memeriksa role:', error.response?.data || error.message);
            }
        },
        getTasks() {
            axios
                .get(window.url + "api/getPosyandu")
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

        updateTask() {
            this.taskData.ketua_kader === ""
                ? (this.taskErrors.ketua_kader = true)
                : (this.taskErrors.ketua_kader = false);
            this.taskData.desa === ""
                ? (this.taskErrors.desa = true)
                : (this.taskErrors.desa = false);
            this.taskData.alamat_lengkap === ""
                ? (this.taskErrors.alamat_lengkap = true)
                : (this.taskErrors.alamat_lengkap = false);

            if (
                this.taskData.ketua_kader &&
                this.taskData.desa &&
                this.taskData.alamat_lengkap
            ) {
                axios
                    .post(
                        window.url + "api/updatePosyandu/" + this.taskData.id,
                        this.taskData
                    )
                    .then((response) => {
                        this.getTasks();
                    })
                    .catch((errors) => {
                        console.log(errors);
                    })
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            }
        },

        detailTask(task) {
            this.taskData.id = task.id;
            axios.get(window.url + "api/detail-posyandu/" + this.taskData.id);
        },

        aksiTask(task) {
            this.taskData = { ...task }; // U
            this.id = task.id;
            console.log(this.taskData);
            $("#aksiModal").modal("show");
        },

        editTask() {
            this.editMode = true;
            this.deleteMode = false;
            // this.taskData = { ...task }; // Use spread operator to copy the task data
            $("#aksiModal").modal("hide");
            $("#taskModal").modal("show");
        },

        removeTask() {
            this.deleteMode = true;
            this.editMode = false;
            $("#aksiModal").modal("hide");
            $("#taskModal").modal("show");
        },

        deleteTask() {
            axios
                .post(window.url + "api/deletePosyandu/" + this.taskData.id)
                .then((response) => {
                    this.getTasks();
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
    },
    mounted() {
        this.getTasks();
        this.checkRole();
    },
};
</script>

<style scoped>
@media (max-width: 768px) {

    table thead th:nth-child(4),
    table tbody td:nth-child(4) {
        display: none;
        /* Sembunyikan kolom ke-4 */
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
