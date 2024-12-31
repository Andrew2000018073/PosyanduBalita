<template>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo
    <a href="index3.html" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a> -->

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <!-- <img src="{{ asset('lte/dist/img/logo.png') }}" class="img-circle elevation-2" alt="User Image"> -->
                </div>
                <div class="info">
                    <router-link to="/" class="d-block">Puskesmas Kelapa Kampit</router-link>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                    data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                    <li class="nav-header">Posyandu</li>
                    <li class="nav-item">
                        <router-link to="/kelola-posyandu" class="nav-link">
                            <i class="nav-icon fi fi-ss-hospital"></i>
                            <p class="ml-2">Kelola Posyandu</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/tambah-kegiatan" class="nav-link">
                            <i class="fi fi-ss-add-document"></i>
                            <p class="ml-2">Tambah Kegiatan</p>
                        </router-link>
                    </li>

                    <li class="nav-header">Register</li>
                    <li class="nav-item">
                        <router-link to="/register-wus" class="nav-link">
                            <i class="nav-icon fi fi-sr-woman-head"></i>
                            <p class="ml-2">Registrasi WUS</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/register-pus" class="nav-link">
                            <i class="nav-icon fi fi-bs-venus-mars"></i>
                            <p class="ml-2">Registrasi PUS</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/register-bayi" class="nav-link">
                            <i class="nav-icon fi fi-br-child-head"></i>
                            <p class="ml-2">Register Bayi</p>
                        </router-link>
                    </li>

                    <li class="nav-header">Alat</li>
                    <li class="nav-item" v-if="isKoordinator == true || isketuakader == true">
                        <router-link to="/kelola-imunisasi" class="nav-link">
                            <i class="fi fi-ss-pills"></i>
                            <p class="ml-2">Kelola Imunisasi</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="isKoordinator == true">
                        <router-link to="/kelola-pengguna" class="nav-link">
                            <i class="fi fi-sr-users-alt"></i>
                            <p class="ml-2">Kelola Pengguna</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/kelola-wus" class="nav-link">
                            <i class="fi fi-bs-woman-head"></i>
                            <p class="ml-2">Kelola WUS</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/kelola-balita" class="nav-link">
                            <i class="fi fi-ss-baby"></i>
                            <p class="ml-2">Kelola Balita</p>
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link to="/kelola-pus" class="nav-link">
                            <i class="fi fi-ss-people"></i>
                            <p class="ml-2">Kelola Pus</p>
                        </router-link>
                    </li>

                    <li class="nav-header" v-if="isKoordinator == true || isketuakader == true">Laporan</li>
                    <li class="nav-item" v-if="isKoordinator == true || isketuakader == true">
                        <router-link to="/laporan-wus" class="nav-link">
                            <i class="fi fi-sr-newspaper"></i>
                            <p class="ml-2">Laporan WUS</p>
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="isKoordinator == true || isketuakader == true">
                        <router-link to="/laporan-balita" class="nav-link">
                            <i class="fi fi-sr-file-medical-alt"></i>
                            <p class="ml-2">Laporan Balita</p>
                        </router-link>
                    </li>

                    <li class="nav-header">Akun</li>
                    <li class="nav-item">
                        <router-link to="/" @click="logout" class="nav-link">
                            <i class="fi fi-bs-sign-out-alt"></i>
                            <p class="ml-2">Logout</p>
                        </router-link>

                    </li>

                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>
</template>

<style>
@import url("https://cdn-uicons.flaticon.com/2.5.1/uicons-bold-rounded/css/uicons-bold-rounded.css");
@import url("https://cdn-uicons.flaticon.com/2.5.1/uicons-solid-rounded/css/uicons-solid-rounded.css");
@import url("https://cdn-uicons.flaticon.com/2.5.1/uicons-bold-straight/css/uicons-bold-straight.css");
@import url("https://cdn-uicons.flaticon.com/2.5.1/uicons-solid-straight/css/uicons-solid-straight.css");
@import url("https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css");
@import url("https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-chubby/css/uicons-solid-chubby.css");
@import url("https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css");
</style>

<script>

import axios from 'axios'

export default {
    name: 'Dashboard',

    data() {
        return {
            //state loggedIn with localStorage
            loggedIn: localStorage.getItem('loggedIn'),
            //state token
            token: localStorage.getItem('token'),
            //state user logged In
            user: [],
            isKoordinator: false,
            isketuakader: false,
            iskader: false
        }
    },
    created() {
        axios.get(window.url + 'api/user', { headers: { 'Authorization': 'Bearer ' + this.token } })
            .then(response => {
                // console.log(response)
                this.user = response.data // assign response to state user
            })
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

                // console.log('Roles:', roles);
                // console.log('Permissions:', permissions);

                // Contoh penggunaan role
                if (roles.includes('koordinator')) {
                    // console.log('User adalah Koordinator');
                    this.isKoordinator = true; // Set state untuk role koordinator
                } else if (roles.includes('ketua kader')) {
                    // console.log('User adalah Ketua Kader');
                    this.isketuakader = true; // Set state untuk role ketua kader
                } else if (roles.includes('kader')) {
                    // console.log('User adalah Kader');
                    this.iskader = true; // Set state untuk role kader
                }
            } catch (error) {
                console.error('Gagal memeriksa role:', error.response?.data || error.message);
            }
        },
        logout() {
            axios.post(window.url + 'api/logout', {}, {
                headers: { 'Authorization': 'Bearer ' + this.token }
            })
                .then(() => {
                    // Hapus localStorage
                    localStorage.removeItem("loggedIn");
                    localStorage.removeItem("token");

                    // Redirect ke halaman login
                    return this.$router.push({ name: 'login' });
                })
                .catch(error => {
                    console.error('Logout error:', error.response.data);
                });
        }
    },

    //check user logged in or not
    mounted() {
        if (!this.loggedIn) {
            return this.$router.push({ name: 'login' })
        }

        this.checkRole();
    }
}
</script>
