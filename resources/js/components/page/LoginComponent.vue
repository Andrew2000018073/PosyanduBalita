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
            isLoading: false,
            loginFailed: false,
            validation: {},
            user: {
                email: '',
                password: ''
            }
        };
    },

    methods: {
        async login() {
            this.isLoading = true;
            this.validation = {};
            this.loginFailed = false;
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
                await axios.get(window.url + 'sanctum/csrf-cookie');
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
                                ?.split('=')[1]
                        }
                    }
                );

                if (response.data.success) {
                    localStorage.setItem('loggedIn', 'true');
                    localStorage.setItem('token', response.data.token);
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
