import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import $ from 'jquery';
import 'select2';
import 'select2/dist/css/select2.css';
import ApexCharts from 'apexcharts';

import { createApp } from 'vue'; // Vue 3
import { createRouter, createWebHistory } from 'vue-router'; // Vue Router 3
import { createStore } from 'vuex'; // Vuex 4 untuk Vue 3

import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

// Import komponen
import ExampleComponent from './components/DashboardComponent.vue';
import AboutComponent from './components/AboutComponent.vue';
import NavbarComponent from './components/NavbarComponent.vue';
import SidebarComponent from './components/SidebarComponent.vue';
import RegisterWUSComponent from './components/page/RegisterWUSComponent.vue';
import RegisterPusComponent from './components/page/RegisterPusComponent.vue';
import RegisterBayiComponent from './components/page/RegisterBayiComponent.vue';
import TestingComponent from './components/page/TestingComponent.vue';
import KelolaPosyanduComponent from './components/page/KelolaPosyanduComponent.vue';
import TambahPosyanduComponent from './components/page/TambahPosyanduComponent.vue';
import TambahKegiatanComponent from './components/page/TambahKegiatanComponent.vue';
import DetailPosyanduComponent from './components/page/DetailPosyanduComponent.vue';
import KegiatanWusActiveComponent from './components/page/KegiatanWusActiveComponent.vue';
import TambahPesertaWusComponent from './components/page/TambahPesertaWusComponent.vue';
import KegiatanBalitaActiveComponent from './components/page/KegiatanBalitaActiveComponent.vue';
import TambahPesertaBalitaComponent from './components/page/TambahPesertaBalitaComponent.vue';
import KelolaWUSComponent from './components/page/KelolaWUSComponenet.vue';
import KelolaBalitaComponent from './components/page/KelolaBalitaComponent.vue';
import KelolaImunisasiComponent from './components/page/KelolaImunisasiComponent.vue';
import TambahImunisasiBalitaComponent from './components/page/TambahImunisasiBalitaComponent.vue';
import KelolaPusesComponent from './components/page/KelolaPusesComponent.vue';
import DetailPusesComponent from './components/page/DetailPusesComponent.vue';
import TambahKontrasepsiComponent from './components/page/TambahKontrasepsiComponent.vue';
import DetailWusComponent from './components/page/DetailWusComponent.vue';
import LoginComponent from './components/page/LoginComponent.vue';
import KelolaPenggunaComponent from './components/page/KelolaPenggunaComponent.vue';
import TambahPenggunaComponent from './components/page/TambahPenggunaComponent.vue';
import LaporanWusComponent from './components/page/LaporanWusComponent.vue';
import LaporanBalitaComponent from './components/page/LaporanBalitaComponent.vue';
import DashboardComponent from './components/DashboardComponent.vue';

// Konfigurasi Routes
const routes = [
    { path: '/login', component: LoginComponent, name: 'login', meta: { hideLayout: true } },
    { path: '/', component: DashboardComponent, name: 'dashboard', meta: { requiresAuth: true } },
    { path: '/kelola-pengguna', name: 'kelola-pengguna', component: KelolaPenggunaComponent, meta: { requiresAuth: true } },
    { path: '/tambah-pengguna', component: TambahPenggunaComponent, meta: { requiresAuth: true } },
    { path: '/about', component: AboutComponent },
    { path: '/register-wus', component: RegisterWUSComponent, meta: { requiresAuth: true } },
    { path: '/register-bayi', component: RegisterBayiComponent, meta: { requiresAuth: true } },
    { path: '/register-pus', name: 'register-pus', component: RegisterPusComponent, meta: { requiresAuth: true } },
    { path: '/testing', component: TestingComponent },
    { path: '/kelola-posyandu', component: KelolaPosyanduComponent, meta: { requiresAuth: true } },
    { path: '/tambah-posyandu', component: TambahPosyanduComponent, meta: { requiresAuth: true } },
    { path: '/tambah-kegiatan', component: TambahKegiatanComponent, meta: { requiresAuth: true } },
    { path: '/detail-posyandu/:id', name: 'detail-posyandu', component: DetailPosyanduComponent, meta: { requiresAuth: true } },
    { path: '/detail-puses/:id', name: 'detail-puses', component: DetailPusesComponent, meta: { requiresAuth: true } },
    { path: '/kegiatan-wus-active/:id', name: 'kegiatan-wus-active', component: KegiatanWusActiveComponent, meta: { requiresAuth: true } },
    { path: '/kegiatan-balita-active/:id', name: 'kegiatan-balita-active', component: KegiatanBalitaActiveComponent, meta: { requiresAuth: true } },
    { path: '/tambah-peserta-wus', name: 'tambah-peserta-wus', component: TambahPesertaWusComponent, meta: { requiresAuth: true } },
    { path: '/tambah-peserta-balita', name: 'tambah-peserta-balita', component: TambahPesertaBalitaComponent, meta: { requiresAuth: true } },
    { path: '/kelola-wus', name: 'kelola-wus', component: KelolaWUSComponent, meta: { requiresAuth: true } },
    { path: '/kelola-balita', name: 'kelola-balita', component: KelolaBalitaComponent, meta: { requiresAuth: true } },
    { path: '/kelola-imunisasi', name: 'kelola-imunisasi', component: KelolaImunisasiComponent, meta: { requiresAuth: true } },
    { path: '/tambah-imunisasi-balita', name: 'tambah-imunisasi-balita', component: TambahImunisasiBalitaComponent, meta: { requiresAuth: true } },
    { path: '/kelola-pus', name: 'kelola-pus', component: KelolaPusesComponent, meta: { requiresAuth: true } },
    { path: '/tambah-kontrasepsi', name: 'tambah-kontrasepsi', component: TambahKontrasepsiComponent, meta: { requiresAuth: true } },
    { path: '/detail-wus/:id', name: 'detail-wus', component: DetailWusComponent, meta: { requiresAuth: true } },
    { path: '/laporan-wus', name: 'laporan-wus', component: LaporanWusComponent, meta: { requiresAuth: true } },
    { path: '/laporan-balita', name: 'laporan-balita', component: LaporanBalitaComponent, meta: { requiresAuth: true } },
];

// Konfigurasi Router
const router = createRouter({
    history: createWebHistory('/posdua/public/'), // Perbarui sesuai base URL jika diperlukan
    routes,
});

// Navigation Guards
router.beforeEach((to, from, next) => {
    const isLoggedIn = localStorage.getItem('loggedIn'); // Cek status login dari localStorage

    if (to.matched.some(record => record.meta.requiresAuth)) {
        // Jika rute memerlukan autentikasi
        if (!isLoggedIn) {
            return next({ name: 'login' }); // Arahkan ke login jika belum login
        }
    }

    if (to.name === 'login' && isLoggedIn) {
        // Jika sudah login, arahkan ke dashboard
        return next({ name: 'dashboard' });
    }

    next(); // Lanjutkan ke rute berikutnya
});

// Konfigurasi Vuex Store
const store = createStore({
    state: {
        PassedId: null,
    },
    mutations: {
        setPassedId(state, id) {
            state.PassedId = id;
        },
    },
});



// Membuat Aplikasi Vue
const app = createApp({
    data() {
        return {
            isLoginPage: false, // Default false
        };
    },
    watch: {
        // Pantau perubahan rute untuk memperbarui status halaman login
        '$route'(to) {
            this.checkLoginPage(to);
        },
    },
    mounted() {
        // Periksa status halaman login saat pertama kali dimuat
        this.checkLoginPage(this.$route);
    },
    methods: {
        checkLoginPage(route) {
            this.isLoginPage = route.name === 'login'; // Sesuaikan dengan nama rute login Anda
        },
    },
});

// Plugin dan Komponen
app.use(router); // Pasang router
app.use(store); // Pasang Vuex store

// Daftarkan Komponen
app.component('dashboard-component', DashboardComponent);
app.component('nav-component', NavbarComponent);
app.component('sidebar-component', SidebarComponent);

window.url = '/posdua/public/'

// Mount ke elemen #app
app.mount('#app');


