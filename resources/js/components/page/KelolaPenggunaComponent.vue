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
                                ? "Hapus Data " + user.name
                                : editMode
                                    ? "Perbarui peserta " + user.name
                                    : "Errors"
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
                            <label for="nama_pengguna">Nama Pengguna</label>
                            <input type="text" class="form-control" id="nama_pengguna"
                                placeholder="Masukan Nama Pengguna" v-model="user.name" />
                            <span class="text-danger" v-show="userserrors.name">Masukan nama pengguna</span>
                        </div>

                        <div class="form-group">
                            <label for="email">email</label>
                            <input type="email" class="form-control" id="email" placeholder="Masukan email"
                                v-model="user.email" />
                            <span class="text-danger" v-show="userserrors.email">Masukan nama ketua kader</span>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Masukan Password"
                                v-model="user.password" />
                            <span class="text-danger" v-show="userserrors.password">Masukan pasword</span>
                        </div>
                        <div class="form-group">
                            <label for="confirmation-password">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirmation-password"
                                placeholder="Konfirmasi password" v-model="user.password_confirmation" />
                            <span class="text-danger" v-show="userserrors.password_confirmation">Password tidak sama
                                atau
                                kosong</span>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select v-model="user.role" class="form-control">
                                <option disabled value="">- Silahkan memilih jenis role pada pengguna -</option>
                                <option v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name }}
                                </option>
                            </select>
                            <span class="text-danger" v-show="userserrors.role">Pilih role </span>
                        </div>
                    </div>
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Apakah anda yakin ingin menghapus data
                            {{ user.name }}?
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

    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelola Pengguna</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            Kelola Pengguna
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

    <!-- RIL content -->
    <div class="row">
        <div class="col">
            <div class="card card-primary mt-3">
                <div class="card-header">
                    Data Pengguna
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableuser" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">email</th>
                                    <th class="text-center">Peran</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(task, index) in users" :key="index" class="text-center">
                                    <td>{{ task.name }}</td>
                                    <td>{{ task.email }}</td>
                                    <td>{{ task.role }}</td>
                                    <td class="text-center">
                                        <div>
                                            <div class="row">
                                                <div class="col" @click="editTask(task)">
                                                    <button type="button" class="btn btn-warning btn-sm">
                                                        Edit
                                                    </button>
                                                </div>
                                                <div class="col" @click="removeTask(task)">
                                                    <button type="button" class="btn btn-danger btn-sm">
                                                        Delete
                                                    </button>
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
        </div>
    </div>

    <div class="row mb-3">
        <div class="col"></div>
        <div class="col">
            <router-link to="tambah-pengguna">
                <button type="button" class="btn btn-block btn-success btn-lg">
                    Tambah Pengguna
                </button>
            </router-link>
        </div>
        <div class="col"></div>
    </div>
</template>


<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import "datatables.net";
import "datatables.net-bs4";

export default {
    data() {
        return {
            deleteMode: false,
            editMode: false,
            users: [],
            editData: [],
            user: {
                id: '',
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: '',
            },
            userserrors: {
                name: false,
                email: false,
                password: false,
                password_confirmation: false,
                role: false,
            },
            roles: [],
            error: null,
        }
    },
    methods: {
        removeTask(task) {
            this.deleteMode = true;
            this.user.id = task.id;
            this.user.name = task.name;
            $("#taskModal").modal("show");
        },
        deleteTask() {
            axios
                .post(window.url + "api/deleteUser/" + this.user.id)
                .then((response) => {
                    this.getpengguna();
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
            this.user = { ...task };
            $("#taskModal").modal("show");
        },
        updateTask() {
            console.log(this.user.id);
            this.userserrors.name = !this.user.name;
            this.userserrors.email = !this.user.email;
            this.userserrors.password = !this.user.password;
            this.userserrors.password_confirmation = !this.user.password_confirmation;
            this.userserrors.role = !this.user.role;

            if (
                this.user.name &&
                this.user.email &&
                this.user.password &&
                this.user.password_confirmation &&
                this.user.role
            ) {
                axios
                    .put(window.url + "api/updatePeserta/" + this.user.id, this.user)
                    .then((response) => {
                        this.getpengguna();
                        this.closeModal();
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            }
        },

        async fetchRoles() {
            try {
                const response = await axios.get(window.url + '/api/roles');
                this.roles = response.data.roles;
            } catch (error) {
                console.error('Gagal mengambil roles:', error);
            }
        },

        getpengguna() {
            axios.get(window.url + 'api/getusers').then((response) => {
                console.log(response.data);
                if ($.fn.DataTable.isDataTable("#tableuser")) {
                    $("#tableuser").DataTable().destroy();
                }
                this.users = response.data.users;
                this.$nextTick(() => {
                    $("#tableuser").DataTable({
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
        this.getpengguna();
        this.fetchRoles();
    }

}
</script>
