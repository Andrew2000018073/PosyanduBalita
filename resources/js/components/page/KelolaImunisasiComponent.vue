<template>
    <!-- Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
        aria-hidden="true">
        <div :class="`modal-dialog modal-dialog-centered ${!deleteMode ? 'modal-lg' : 'modal-md'}`">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">
                        {{ deleteMode ? 'Hapus ' + taskData.nama : editMode ? 'Perbarui data ' + taskData.nama :
                            'Tambahkan Imunisasi ' }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="!deleteMode">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="namaposyandu">Nama Imunisasi/Vaksin</label>
                                    <input type="text" class="form-control" v-model="taskData.nama" />
                                    <span class="text-danger" v-if="taskErrors.nama">Data Belum Terisi!</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="Deskripsi">Deskripsi</label>
                                    <textarea rows="3" class="form-control" v-model="taskData.detail"></textarea>
                                    <span class="text-danger" v-if="taskErrors.detail">Data Belum Terisi!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Apakah Anda Yakin Ingin Menghapus Imunisasi/Vaksin {{ taskData.nama }}?
                        </h5>
                    </div>
                </div>
                <div class="modal-footer" v-if="!deleteMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        @click="closeModal">Close</button>
                    <button type="button" class="btn btn-primary" @click="editMode ? updateTask() : storeImunisasi()"
                        v-if="!deleteMode">
                        {{ editMode ? 'Perbarui Data' : 'Simpan Data' }}
                    </button>
                </div>

                <div class="modal-footer" v-if="deleteMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"
                        @click="closeModal">Close</button>
                    <button type="button" class="btn btn-danger" @click="deleteTask">Hapus Data</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelola Imunisasi Balita</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">Kelola Imunisasi</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">Data Imunisasi</div>
                <div class="card-body">
                    <table id="example" class="table table-striped table-bordered w-100" ref="imunisasiTable">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Imunisasi</th>
                                <th class="text-center">Detail</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in imunisasis" :key="index" class="text-center">
                                <td class="text-center">{{ task.nama }}</td>
                                <td class="text-center">{{ task.detail }}</td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col text-center ">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                @click="editTask(task)">
                                                Edit
                                            </button>
                                        </div>
                                        <div class="col text-center">
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
                    <button type="button" class="btn btn-primary" @click="createNewData">Tambah Data</button>
                </div>

            </div>

        </div>
    </div>

</template>

<script>
import axios from 'axios';
import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'datatables.net';
import 'datatables.net-bs4';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            editMode: false,
            deleteMode: false,
            imunisasis: [],
            taskData: {
                id: '',
                nama: '',
                detail: '',
            },
            taskErrors: {
                nama: false,
                detail: false,
            },
        }
    },
    mounted() {
        this.getImunisasi();
    },
    methods: {
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;
            this.taskData = { ...task };
            $('#taskModal').modal('show');
        },
        updateTask() {
            this.taskData.nama === '' ? this.taskErrors.nama = true : this.taskErrors.nama = false;
            this.taskData.detail === '' ? this.taskErrors.detail = true : this.taskErrors.detail = false;

            if (this.taskData.nama && this.taskData.detail) {
                axios.post(window.url + 'api/updateImunisasi/' + this.taskData.id, this.taskData)
                    .then(response => {
                        this.getImunisasi();
                    })
                    .catch(errors => {
                        console.log(errors);
                    })
                    .finally(() => {
                        $('#taskModal').modal('hide');
                    });
            }
        },
        removeTask(task) {
            this.deleteMode = true;
            this.editMode = false;
            this.taskData = { ...task };
            $('#taskModal').modal('show');
        },
        deleteTask() {
            axios.post(window.url + 'api/deleteImunisasi/' + this.taskData.id)
                .then(response => {
                    this.getImunisasi();
                })
                .catch(errors => {
                    console.log(errors);
                })
                .finally(() => {
                    $('#taskModal').modal('hide');
                });
        },
        createNewData() {
            this.$router.push({ name: 'tambah-imunisasi-balita' });
        },

        getImunisasi() {
            axios.get(window.url + 'api/getImunisasiBayi/')
                .then(response => {
                    if ($.fn.DataTable.isDataTable(this.$refs.imunisasiTable)) {
                        $(this.$refs.imunisasiTable).DataTable().destroy();
                    }
                    this.imunisasis = response.data;
                    setTimeout(() => {
                        if ($(this.$refs.imunisasiTable).length) {
                            $(this.$refs.imunisasiTable).DataTable({
                                pageLength: 10,
                                lengthChange: false,
                            });
                        }
                    }, 100);
                })
                .catch(errors => {
                    console.log(errors);
                });
        },


        closeModal() {
            $('#taskModal').modal('hide');
        }
    },
}
</script>


<style scoped>
@media (max-width: 768px) {

    table td,
    table th {
        padding: 4px 6px;
        font-size: 12px;
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
