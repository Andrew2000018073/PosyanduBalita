<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Register PUS</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">Register PUS</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Identitas PUS</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="form-group">
                        <label for="namasuami">Nama Suami</label>
                        <input type="text" class="form-control" id="namasuami" v-model="taskData.nama_suami"
                            placeholder="Masukan Nama Suami" />
                        <span class="text-danger" v-show="taskErrors.nama_suami">Silahkan Pilih Nama Suami!</span>
                    </div>
                    <div class="form-group">
                        <label for="lila">Lingkar Perut Suami (cm)</label>
                        <input type="number" min="0" max="1000" step="0.01" class="form-control" id="lila"
                            v-model="taskData.lingkar_perut_suami" placeholder="Masukan Lingkar Perut Suami (cm)" />
                        <span class="text-danger" v-show="taskErrors.lingkar_perut_suami">Silahkan masukan lila!</span>
                    </div>


                    <div class="form-group">
                        <label for="Tempat Pelaksanaan">Nama Istri</label>
                        <select v-if="tasks.length" class="form-control select2" v-model="taskData.wus_id"
                            style="width: 100%;" id="my-select">
                            <option disabled value="">- Silahkan masukan nama istri -</option>
                            <option v-for="task in tasks" :key="task.wus_id" :value="task.wus_id">
                                {{ task.nama }}
                            </option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.wus_id">Silahkan Pilih WUS!</span>
                    </div>

                    <div class="form-group">
                        <label for="ketuaKader">Jumlah anak (hidup)</label>
                        <input type="number" class="form-control" id="ketuaKader" v-model="taskData.jumlah_anak_hidup"
                            placeholder="Masukan jumlah anak" />
                        <span class="text-danger" v-show="taskErrors.jumlah_anak_hidup">Silahkan Masukan Jumlah!</span>
                    </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" @click="createData">Submit</button>
                </div>

            </div>
        </div>
    </div>
</template>



<script>
import $ from 'jquery';
import 'select2/dist/css/select2.css';
import 'select2';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            tasks: [],
            taskData: {
                wus_id: '',
                nama_suami: '',
                jumlah_anak_hidup: '',
                lingkar_perut_suami: '',
            },
            taskErrors: {
                wus_id: '',
                nama_suami: '',
                jumlah_anak_hidup: '',
                lingkar_perut_suami: '',
            },
        };
    },
    methods: {
        getTasks() {
            axios.get(window.url + 'api/getWus')
                .then(response => {
                    this.tasks = response.data;
                    this.$nextTick(() => {
                        this.initializeSelect2();
                    });
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        createData() {
            this.taskErrors.wus_id = !this.taskData.wus_id;
            this.taskErrors.nama_suami = !this.taskData.nama_suami;
            this.taskErrors.jumlah_anak_hidup = !this.taskData.jumlah_anak_hidup;
            this.taskErrors.lingkar_perut_suami = !this.taskData.lingkar_perut_suami;

            if (!this.taskErrors.wus_id && !this.taskErrors.nama_suami && !this.taskErrors.jumlah_anak_hidup && !this.taskErrors.lingkar_perut_suami) {
                axios.post(window.url + 'api/storePus', this.taskData)
                    .then(response => {
                        this.showAlert().then(() => this.resetForm());
                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            }
        },
        initializeSelect2() {
            const vm = this;
            if ($('#my-select').length > 0) {
                $('#my-select').select2({
                    dropdownAutoWidth: true,
                }).on('change', function () {
                    vm.taskData.wus_id = $(this).val();
                });
            }
        },
        showAlert() {
            return Swal.fire({
                title: 'Sukses!',
                text: 'Data telah berhasil disimpan!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
            });
        },
        resetForm() {
            this.taskData = {
                wus_id: '',
                nama_suami: '',
                jumlah_anak_hidup: '',
                lingkar_perut_suami: '',
            };
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
    },
    mounted() {
        this.getTasks();
    },
    beforeDestroy() {
        if ($('#my-select').hasClass('select2-hidden-accessible')) {
            $('#my-select').select2('destroy');
        }
    },
};
</script>

<style scoped>
@media (max-width: 768px) {
    .content-header h1 {
        font-size: 1.8rem;
        margin: 0;
        font-size: 20px;
    }

    .content-header .breadcrumb {
        background-color: transparent;
        line-height: 1.8rem;
        margin-bottom: 0;
        padding: 0;
        font-size: 15px;
    }

    element {
        width: 50%;
    }

    table {
        font-size: 12px;
    }
}
</style>
