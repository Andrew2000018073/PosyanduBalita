<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Kegiatan Posyandu</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/kelola-posyandu">Kelola Posyandu</router-link>
                        </li>
                        <li class="breadcrumb-item active">Tambah Kegiatan Posyandu</li>
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
                    <h3 class="card-title">Tambahkan data terkait kegiatan</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="form-group">
                        <label for="Tempat Pelaksanaan">Tempat Pelaksanaan</label>
                        <select class="form-control select2" v-model="taskData.posyandu_id" style="width: 100%;"
                            id="my-select">
                            <option disabled value="">- Silahkan memilih tempat pelaksanaan -</option>
                            <option v-for="task in tasks" :key="task.id" :value="task.id">
                                {{ task.nama_posyandu }}
                            </option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.posyandu_id">Silahkan Pilih Tempat
                            Pelaksanaan!</span>
                    </div>
                    <div class="form-group">
                        <label for="Tipe Kegiatan">Tipe Kegiatan</label>
                        <select class="form-control" v-model="taskData.tipe_kegiatan" style="width: 100%;">
                            <option disabled value="">- Silahkan memilih jenis kegiatan -</option>
                            <option value="Balita">Balita</option>
                            <option value="WUS">WUS</option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.tipe_kegiatan">Silahkan Pilih Tipe Kegiatan!</span>
                    </div>

                    <div class="form-group">
                        <label for="tanggal">Tanggal Pelaksanaan</label>
                        <VueDatePicker v-model="taskData.date" :format="format" placeholder="Masukan Tanggal Kegiatan"
                            :enable-time-picker="false"></VueDatePicker>
                        <span class="text-danger" v-show="taskErrors.date">Silahkan Pilih Tanggal Kegiatan!</span>

                    </div>

                    <div class="form-group">
                        <label for="alamatLengkap">Detail</label>
                        <textarea id="alamatLengkap" cols="30" rows="10" class="form-control"
                            v-model="taskData.detail"></textarea>
                        <span class="text-danger" v-show="taskErrors.detail">Silahkan Masukan Detail Terkait
                            Kegiatan!</span>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" @click="sendData">Submit</button>
                </div>

            </div>
        </div>
    </div>

</template>

<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    display: flex;
    align-items: center;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    display: flex;
    align-items: center;
    padding-left: 8px;
    height: auto;
    line-height: normal;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.select2-selection__arrow {
    height: 100%;
    display: flex;
    align-items: center;
}
</style>

<script>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import $ from 'jquery';
import 'select2/dist/css/select2.css';
import 'select2';
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref } from 'vue';

export default {
    components: {
        VueDatePicker,
    },
    data() {
        return {
            date: null,
            format: 'dd-MM-yyyy',
            tasks: [],
            taskData: {
                posyandu_id: '',
                status_kegiatan: '',
                detail: '',
                tgl_kegiatan: '',
                tipe_kegiatan: '',
                date: '',
            },
            taskErrors: {
                posyandu_id: false,
                status_kegiatan: false,
                detail: false,
                tgl_kegiatan: false,
                tipe_kegiatan: false,
                date: false,
            },
        };
    },
    methods: {
        formatDate(date) {
            const [year, month, day] = new Date(date).toISOString().split('T')[0].split('-');
            return `${day}-${month}-${year}`;
        },
        showAlert() {
            Swal.fire({
                title: 'Berhasil',
                text: 'Selamat Melaksanakan Kegiatan',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        },
        getTasks() {
            axios.get(window.url + 'api/getPosyandu')
                .then(response => {
                    this.tasks = response.data;
                    this.initializeSelect2();
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        initializeSelect2() {
            const vm = this;
            $('#my-select').select2({
                dropdownAutoWidth: true,
            }).on('change', function () {
                vm.taskData.posyandu_id = $(this).val();
            });
        },
        sendData() {

            this.taskErrors.posyandu_id = !this.taskData.posyandu_id;
            this.taskErrors.tipe_kegiatan = !this.taskData.tipe_kegiatan;
            this.taskErrors.date = !this.taskData.date;
            this.taskErrors.detail = !this.taskData.detail;

            this.taskData.status_kegiatan = 'belum selesai';
            this.taskData.tgl_kegiatan = this.formatDate(this.taskData.date);



            if (!this.taskErrors.posyandu_id && !this.taskErrors.tipe_kegiatan &&
                !this.taskErrors.date && !this.taskErrors.detail) {
                axios.post(window.url + 'api/storeKegiatan', this.taskData)
                    .then(response => {
                        const createdId = response.data.id;
                        this.showAlert();
                        if (this.taskData.tipe_kegiatan === 'WUS') {
                            this.$router.push('/kegiatan-wus-active/' + createdId);
                        } else {
                            this.$router.push('/kegiatan-balita-active/' + createdId);
                        }
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            } else {
                console.log("Form validation failed.");
            }
        }
    },
    mounted() {
        this.getTasks();
    },
    beforeDestroy() {
        $('#my-select').select2('destroy');
    }
};
</script>
