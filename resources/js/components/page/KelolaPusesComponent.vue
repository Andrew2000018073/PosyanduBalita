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
                                ? "Hapus Pasangan ini? "
                                : editMode
                                    ? "Perbarui data " +
                                    taskData.nama_suami +
                                    " & " +
                                    taskData.wus.nama
                                    : "Error "
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="!deleteMode">
                        <div class="form-group">
                            <label for="lila">Lingkar Perut Suami (cm)</label>
                            <input type="number" min="0" max="1000" step="0.01" class="form-control" id="lila"
                                v-model="taskData.lingkar_perut_suami" placeholder="Masukan Lingkar Perut Suami (cm)" />
                            <span class="text-danger" v-show="taskErrors.lingkar_perut_suami">Silahkan masukan lingkar
                                perut!</span>
                        </div>
                        <div class="form-group">
                            <label for="ketuaKader">Jumlah anak (hidup)</label>
                            <input type="number" class="form-control" id="ketuaKader"
                                v-model="taskData.jumlah_anak_hidup" placeholder="Masukan jumlah anak" />
                            <span class="text-danger" v-show="taskErrors.jumlah_anak_hidup">Silahkan Masukan
                                Jumlah!</span>
                        </div>
                    </div>
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Hapus Pasangan {{ taskData.nama_suami }} &
                            {{ taskData.wus.nama }}
                        </h5>
                    </div>
                </div>
                <div class="modal-footer" v-if="!deleteMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="editMode ? updateTask() : storeImunisasi()"
                        v-if="!deleteMode">
                        {{ editMode ? "Perbarui Data" : "Simpan Data" }}
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

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelola Pasangan Usia Subur</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            Kelola Pasangan Usia Subur
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

    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">Data Pasangan Usia Subur</div>
                <div class="card-body">
                    <table id="example" class="table table-striped table-bordered w-100" ref="imunisasiTable">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Suami</th>
                                <th class="text-center">Nama Istri</th>
                                <th class="text-center">Jumlah Anak Hidup</th>
                                <th class="text-center">Lingkar Perut Suami</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in puses" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ task.nama_suami }}
                                </td>
                                <td class="text-center">{{ task.wus.nama }}</td>
                                <td class="text-center">
                                    {{ task.jumlah_anak_hidup }}
                                </td>
                                <td class="text-center">
                                    {{ task.lingkar_perut_suami }} cm
                                </td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col">
                                            <router-link :to="{
                                                path: 'detail-puses/:id',
                                                params: { id: task.id },
                                                name: 'detail-puses',
                                            }" class="btn btn-info btn-sm">
                                                Detail
                                            </router-link>
                                        </div>
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
                <div class="card-footer text-muted text-center">
                    <button type="button" class="btn btn-primary" @click="createNewData">
                        Tambah Data
                    </button>
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

export default {
    data() {
        return {
            editMode: false,
            deleteMode: false,
            puses: [],
            taskData: {
                id: "",
                jumlah_anak_hidup: "",
                lingkar_perut_suami: "",
            },
            taskErrors: {
                jumlah_anak_hidup: false,
                lingkar_perut_suami: false,
            },
        };
    },
    mounted() {
        this.getPuses();
    },
    methods: {
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        updateTask() {
            this.taskData.jumlah_anak_hidup === ""
                ? (this.taskErrors.jumlah_anak_hidup = true)
                : (this.taskErrors.jumlah_anak_hidup = false);
            this.taskData.lingkar_perut_suami === ""
                ? (this.taskErrors.lingkar_perut_suami = true)
                : (this.taskErrors.lingkar_perut_suami = false);

            if (
                this.taskData.jumlah_anak_hidup &&
                this.taskData.lingkar_perut_suami
            ) {
                axios
                    .post(
                        window.url + "api/updatePus/" + this.taskData.id,
                        this.taskData
                    )
                    .then((response) => {
                        this.getPuses();
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
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        deleteTask() {
            axios
                .post(window.url + "api/deletePus/" + this.taskData.id)
                .then((response) => {
                    this.getPuses();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
        createNewData() {
            this.$router.push({ name: "register-pus" });
            // this.editMode = false;
            // this.deleteMode = false;
            // this.taskData = { id: '', nama: '', detail: '' }; // Reset task data
            // this.taskErrors = { nama: false, detail: false }; // Reset errors
            // $('#taskModal').modal('show');
        },

        getPuses() {
            axios
                .get(window.url + "api/getPus/")
                .then((response) => {
                    // Hancurkan DataTable jika sudah diinisialisasi
                    if ($.fn.DataTable.isDataTable(this.$refs.imunisasiTable)) {
                        $(this.$refs.imunisasiTable).DataTable().destroy();
                    }

                    // Perbarui data
                    this.puses = response.data;

                    // Gunakan setTimeout untuk memberi waktu bagi DOM untuk diperbarui
                    setTimeout(() => {
                        // Periksa apakah tabel tersedia di DOM
                        if ($(this.$refs.imunisasiTable).length) {
                            $(this.$refs.imunisasiTable).DataTable({
                                pageLength: 10,
                                lengthChange: false,
                            });
                        }
                    }, 100); // Tunggu 100ms sebelum inisialisasi
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        closeModal() {
            $("#taskModal").modal("hide");
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
