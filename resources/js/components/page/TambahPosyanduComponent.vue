<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Tambah Posyandu</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item">
                <router-link to="/">Dashboard</router-link>
              </li>
              <li class="breadcrumb-item">
                <router-link to="/kelola-posyandu">Kelola Posyandu</router-link>
              </li>
              <li class="breadcrumb-item active">Tambah Posyandu</li>
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
                <label for="nama_posyandu">Nama Posyandu</label>
                <input
                  type="text"
                  class="form-control"
                  id="ketunama_posyandu"
                  placeholder="Masukan Nama Posyandu"
                  v-model="taskData.nama_posyandu"
                />
                <span class="text-danger" v-show="taskErrors.nama_posyandu">Masukan nama posyandu</span>
              </div>

              <div class="form-group">
                <label for="ketuaKader">Nama Ketua Kader</label>
                <input
                  type="text"
                  class="form-control"
                  id="ketuaKader"
                  placeholder="Masukan Nama Ketua Kader"
                  v-model="taskData.ketua_kader"
                />
                <span class="text-danger" v-show="taskErrors.ketua_kader">Masukan nama ketua kader</span>
              </div>

              <div class="form-group">
                <label for="desa">Desa</label>
                <input
                  type="text"
                  class="form-control"
                  id="desa"
                  placeholder="Masukan Nama Desa"
                  v-model="taskData.desa"
                />
                <span class="text-danger" v-show="taskErrors.desa">Masukan nama desa</span>
              </div>

              <div class="form-group">
                <label for="alamatLengkap">Alamat Lengkap</label>
                <textarea
                  id="alamatLengkap"
                  cols="30"
                  rows="10"
                  class="form-control"
                  v-model="taskData.alamat_lengkap"
                ></textarea>
                <span class="text-danger" v-show="taskErrors.alamat_lengkap">Masukan Alamat Lengkap</span>
              </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer text-center">
              <button type="submit" class="btn btn-primary" @click="storePosyandu">Submit</button>
            </div>

        </div>
      </div>
    </div>
  </template>


<script>
import axios from 'axios';
import $ from 'jquery';
import Swal from 'sweetalert2';
export default {

  data() {
    return {
      taskData: {
        ketua_kader: '',
        nama_posyandu: '',
        desa: '',
        alamat_lengkap: '',
      },
      taskErrors: {
        ketua_kader: false,
        nama_posyandu: false,
        desa: false,
        alamat_lengkap: false
      },
    };
  },
  methods:{
    showAlert() {
      Swal.fire({
        title: 'Berhasil',
        text: 'Data Posyandu Berhasil Ditambahkan',
        icon: 'success',
        confirmButtonText: 'OK'
      }).then(this.$router.push('/kelola-posyandu'));
    },


    storePosyandu(){
        this.taskData.ketua_kader ==''? this.taskErrors.ketua_kader=true : this.taskErrors.ketua_kader=false
        this.taskData.desa ==''? this.taskErrors.desa=true : this.taskErrors.desa=false
        this.taskData.alamat_lengkap ==''? this.taskErrors.alamat_lengkap=true : this.taskErrors.alamat_lengkap=false

        if(this.taskData.ketua_kader && this.taskData.desa && this.taskData.alamat_lengkap){
            axios.post(window.url+'api/storePosyandu', this.taskData).then(response=>{
                console.log(response.data)
            }).catch(errors=>{
                console.log(errors)}).then(this.showAlert())
        }
    },

  }
}
</script>

<style>

</style>
