<template>
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <p style="color: white" class="nav-link">
                    Posyandu Kelapa Kampit
                </p>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->
</template>

<style>
.navbar {
    background-color: #3aafa9;
    height: auto;
}
</style>

<script>
export default {
    name: 'App',

    data() {
        return {
            loggedIn: null
        }
    },

    methods: {
        getLoggedIn() {
            this.loggedIn = localStorage.getItem("loggedIn")
        }
    },

    watch: {
        $route: {
            immediate: true,
            handler() {
                this.getLoggedIn()
            }
        }
    },

}
</script>
