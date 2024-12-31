<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Pengguna</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/kelola-pengguna">Kelola Pengguna</router-link>
                        </li>
                        <li class="breadcrumb-item active">Tambah Pengguna</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Identitas Posyandu</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="form-group">
                        <label for="nama_pengguna">Nama Pengguna</label>
                        <input type="text" class="form-control" id="nama_pengguna" placeholder="Masukan Nama Pengguna"
                            v-model="user.name" />
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
                        <span class="text-danger" v-show="userserrors.password_confirmation">Password tidak sama atau
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
                <!-- /.card-body -->

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" @click="createUser">Submit</button>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            user: {
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                role: '', // Role yang dipilih
            },
            userserrors: {
                name: false,
                email: false,
                password: false,
                password_confirmation: false,
                role: false,
            },
            roles: [], // Data roles
            error: null,
        };
    },
    methods: {
        showAlert() {
            Swal.fire({
                title: 'Berhasil',
                text: 'Data pengguna berhasil ditambahkan',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        },
        async fetchRoles() {
            try {
                const response = await axios.get(window.url + 'api/roles'); // Endpoint roles
                this.roles = response.data.roles;
            } catch (error) {
                console.error('Gagal mengambil roles:', error);
            }
        },
        async createUser() {
            this.userserrors.name = !this.user.name;
            this.userserrors.email = !this.user.email;
            this.userserrors.password = !this.user.password;
            this.userserrors.password_confirmation = !this.user.password_confirmation;
            this.userserrors.role = !this.user.role;

            if (Object.values(this.userserrors).some((err) => err)) {
                console.error('Form belum valid');
                return;
            }

            try {
                const response = await axios.post(window.url + 'api/users', this.user); // Endpoint store user
                console.log('Pengguna berhasil ditambahkan:', response.data);

                this.showAlert();
                this.$router.push({ name: 'kelola-pengguna' }); // Redirect ke halaman pengelolaan pengguna
            } catch (error) {
                console.error('Gagal menambahkan pengguna:', error.response?.data);
                this.error = error.response?.data?.errors || 'Gagal menambahkan pengguna';
            }
        },
    },
    mounted() {
        this.fetchRoles(); // Ambil daftar role saat komponen dimuat
    },
};
</script>

<style>
.error {
    color: red;
}
</style>
