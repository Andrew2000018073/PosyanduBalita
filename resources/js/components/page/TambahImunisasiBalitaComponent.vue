<template>

    <!-- Content Header (Page header) -->
    <div class="content-header">
         <div class="container-fluid">
           <div class="row mb-2">
             <div class="col-sm-6">
               <h1 class="m-0">Kelola Imunisasi Balita</h1>
             </div>
             <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                 <li class="breadcrumb-item">
                   <router-link to="/">Dashboard</router-link>
                 </li>
                 <li class="breadcrumb-item active">
                    <router-link to="/kelola-imunisasi">Kelola Imunisasi</router-link>
                </li>
                 <li class="breadcrumb-item active">Tambah Imunisasi Balita</li>
               </ol>
             </div>
           </div>
         </div>
    </div>
    <!-- /.content-header -->

    <div class="row">
      <div class="col">
        <div class="card card-primary ms-3">
          <div class="card-header">
            <h3 class="card-title">Detail Imunisasi atau Vaksin</h3>
          </div>
          <!-- /.card-header -->
          <!-- form start -->

            <div class="card-body">
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
            <!-- /.card-body -->

            <div class="card-footer text-center">
              <button type="submit" class="btn btn-primary" @click="storeImunisasi">Submit</button>
            </div>

        </div>
      </div>
    </div>

</template>

<script>
import axios from 'axios';
import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
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
    methods: {
        showAlert() {
        Swal.fire({
            title: 'Berhasil',
            text: 'Data Imunisasi berasil ditambahkan',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(this.$router.push('/kelola-imunisasi'));
        },
        storeImunisasi() {
            // Validasi
            this.taskData.nama === '' ? this.taskErrors.nama = true : this.taskErrors.nama = false;
            this.taskData.detail === '' ? this.taskErrors.detail = true : this.taskErrors.detail = false;

            if (this.taskData.nama && this.taskData.detail) {
                axios.post(window.url + 'api/storeImunisasi', this.taskData)
                .then(response=>{
                console.log(response.data)
                }).catch(errors=>{
                    console.log(errors)}).then(this.showAlert());
                }
        },
    }
}

</script>


