<template>
    <div class="login-page">
        <div class="login-box">
            <div v-if="loginFailed" class="alert alert-danger text-center">
                Email atau Password Anda salah.
            </div>
            <div class="row">
                <div class="col">

                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <img class="text-center" :src="'/posdua/public/png/logo.png'" width="150" height="150"
                        alt="logo posyandu">
                </div>
                <div class="card-body login-card-body">
                    <p>Silahkan login sebelum menggunakan aplikasi.
                    </p>
                    <hr>
                    <form @submit.prevent="login">
                        <div class="form-group">
                            <label for="email">EMAIL</label>
                            <input id="email" type="email" class="form-control" v-model.trim="user.email"
                                placeholder="Masukkan Email" />
                            <div v-if="validation.email" class="mt-2 alert alert-danger p-1">
                                Masukkan Email yang valid.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password">PASSWORD</label>
                            <input id="password" type="password" class="form-control" v-model="user.password"
                                placeholder="Masukkan Password" />
                            <div v-if="validation.password" class="mt-2 alert alert-danger p-1">
                                Masukkan Password.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" :disabled="isLoading">
                            <span v-if="isLoading">Loading...</span>
                            <span v-else>LOGIN</span>
                        </button>
                    </form>
                </div>
                <div class="card-footer">
                    <p class="text-danger"> Jika anda tidak memiliki akun, silahkan menghubungi koordinator posyandu
                        atau ketua kader</p>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import axios from 'axios';

export default {
    name: 'Login',

    data() {
        return {
            isLoading: false, // untuk menampilkan loading state
            loginFailed: false, // state untuk login gagal
            validation: {}, // state validasi input
            user: {
                email: '',
                password: ''
            }
        };
    },

    methods: {
        async login() {
            this.isLoading = true;
            this.validation = {}; // Reset validation errors
            this.loginFailed = false;

            // Validasi input sebelum request
            if (!this.user.email) {
                this.validation.email = true;
            }
            if (!this.user.password) {
                this.validation.password = true;
            }

            if (Object.keys(this.validation).length > 0) {
                this.isLoading = false;
                return;
            }

            try {
                // Step 1: Ambil CSRF Cookie
                await axios.get(window.url + 'sanctum/csrf-cookie');

                // Step 2: Kirim data login dengan CSRF header
                const response = await axios.post(
                    window.url + 'api/login',
                    {
                        email: this.user.email,
                        password: this.user.password
                    },
                    {
                        headers: {
                            'X-XSRF-TOKEN': document.cookie
                                .split('; ')
                                .find(row => row.startsWith('XSRF-TOKEN='))
                                ?.split('=')[1] // Ambil nilai cookie XSRF-TOKEN
                        }
                    }
                );

                if (response.data.success) {
                    // Set localStorage
                    localStorage.setItem('loggedIn', 'true');
                    localStorage.setItem('token', response.data.token);

                    // Redirect ke dashboard
                    this.$router.push({ name: 'dashboard' });
                } else {
                    this.loginFailed = true;
                }
            } catch (error) {
                console.log('CSRF Cookie:', document.cookie);
                console.log('XSRF-TOKEN Header:', document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN=')));

                console.error('Login error:', error);
                this.loginFailed = true;
            } finally {
                this.isLoading = false;
            }
        }
    },



    mounted() {
        // Jika sudah login, redirect ke dashboard
        if (localStorage.getItem('loggedIn')) {
            this.$router.push({ name: 'dashboard' });
        }
    }
};
</script>

<style scoped>
/* Atur layout untuk halaman login */
.login-page {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-size: cover;
    /* Agar gambar menutupi seluruh layar */
    background-position: center center;
    /* Memusatkan gambar */
    background-repeat: no-repeat;
    /* Menghindari pengulangan gambar */
    background-image: url('/posdua/public/png/bglogin.jpg');
    background-color: #000000;
    margin: 0;
}

.card-header {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 150px;
    text-align: center;
}

.card-header img {
    margin: 0 auto;
    display: block;
}
</style>
