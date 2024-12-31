<template>
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Register Bayi</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">Register Bayi</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Identitas Balita/Bayi</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="form-group">
                        <label for="Nama_Balita">Nama Balita</label>
                        <input type="text" class="form-control" id="namabalita" placeholder="Masukan Nama Balita"
                            v-model="taskData.namabalita" />
                        <span class="text-danger" v-show="taskErrors.namabalita">Silahkan masukan NAMA BALITA!</span>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <VueDatePicker v-model="taskData.date" :format="format" placeholder="Masukan Tanggal Lahir"
                            :enable-time-picker="false"></VueDatePicker>
                        <span class="text-danger" v-show="taskErrors.tanggal_lahir">Silahkan masukan TANGGAL
                            LAHIR!</span>
                    </div>

                    <div class="form-group">
                        <label for="tinggi_badan">Panjang badan lahir(cm)</label>
                        <input v-model="taskData.panjangbadan_lahir" type="number" min="0" max="1000" step="0.01"
                            class="form-control" id="tinggi_badan" placeholder="Masukan Panjang Badan(cm)" />
                        <span class="text-danger" v-show="taskErrors.panjangbadan_lahir">Silahkan masukan PANJANG
                            BADAN!</span>
                    </div>
                    <div class="form-group">
                        <label for="berat">Berat berat badan lahir(kg)</label>
                        <input v-model="taskData.beratbadan_lahir" type="number" min="0" max="1000" step="0.01"
                            class="form-control" id="berat" placeholder="Masukan berat badan(kg)" />
                        <span class="text-danger" v-show="taskErrors.beratbadan_lahir">Silahkan masukan BERAT
                            BADAN!</span>
                        <!-- <span class="text-danger">Silahkan masukan lila!</span> -->
                    </div>
                    <div class="form-group">
                        <label for="lila">Lingkar kepala lahir(cm)</label>
                        <input v-model="taskData.likep_lahir" type="number" min="0" max="1000" step="0.01"
                            class="form-control" id="lila" placeholder="Masukan Lingkar Kepala Bayi(cm)" />
                        <span class="text-danger" v-show="taskErrors.likep_lahir">Silahkan masukan LINGKAR
                            LENGAN!</span>
                    </div>
                    <div class="form-group">
                        <label for="NamaAyahIbu">Jenis Kelamin</label>
                        <select class="form-control select2" style="width: 100%" v-model="taskData.jenis_kelamin">
                            <option disabled value="">
                                - Silahkan masukan jenis kelamin-
                            </option>
                            <option value="Laki-laki">
                                laki-laki
                            </option>
                            <option value="Perempuan">
                                Perempuan
                            </option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.jenis_kelamin">Silahkan Pilih Jenis Kelamin!</span>
                    </div>

                    <div class="form-group">
                        <label for="NamaAyahIbu">Nama Ayah & Ibu</label>
                        <select class="form-control select2" id="my-select" style="width: 100%"
                            v-model="taskData.pus_id">
                            <option disabled value="">
                                - Silahkan masukan nama orang tua-
                            </option>
                            <option v-for="task in ortu" :key="task.id" :value="task.id">
                                {{ task.nama_suami }} & {{ task.wus.nama }}
                            </option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.pus_id">Silahkan Pilih ORTU!</span>
                    </div>

                    <div class="form-group">
                        <label for="Tempat Pelaksanaan">Tempat Pelaksanaan</label>
                        <select class="form-control select2" v-model="taskData.posyandus_id" style="width: 100%"
                            id="posyandu">
                            <option disabled value="">
                                - Silahkan memilih tempat pelaksanaan -
                            </option>
                            <option v-for="task in posyandus" :key="task.id" :value="task.id">
                                {{ task.nama_posyandu }}
                            </option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.posyandus_id">Silahkan Pilih Tempat
                            Pelaksanaan!</span>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" @click="createData">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import $ from "jquery";
import "select2/dist/css/select2.css";
import "select2";
import axios from "axios";
import Swal from "sweetalert2";

export default {
    components: {
        VueDatePicker,
    },
    data() {
        return {
            posyandus: [],
            format: "dd-MM-yyyy",
            ortu: [],
            ibu: [], // Storing tasks from API
            taskData: {
                pus_id: "",
                namabalita: "",
                tanggal_lahir: "",
                beratbadan_lahir: "",
                panjangbadan_lahir: "",
                likep_lahir: "",
                posyandus_id: "",
                date: "",
                jenis_kelamin: "",
            },
            taskErrors: {
                pus_id: false,
                namabalita: false,
                tanggal_lahir: false,
                beratbadan_lahir: false,
                panjangbadan_lahir: false,
                likep_lahir: false,
                posyandus_id: false,
                date: false,
                jenis_kelamin: false,
            },
        };
    },
    methods: {
        formatDate(date) {
            // Function to format the date as dd-mm-yyyy
            const [year, month, day] = new Date(date)
                .toISOString()
                .split("T")[0]
                .split("-");
            return `${day}-${month}-${year}`;
        },

        getPosyandu() {
            axios
                .get(window.url + "api/getPosyandu")
                .then((response) => {
                    this.posyandus = response.data; // Simpan data ke properti tasks
                    this.initializeSelect2(); // Initialize select2 after data is loaded
                })
                .catch((errors) => {
                    console.log(errors); // Tangani kesalahan jika ada
                });
        },

        getPus() {
            axios
                .get(window.url + "api/getPus")
                .then((response) => {
                    this.ortu = response.data;
                    // Reinitialize Select2 after the options have been updated
                    this.$nextTick(() => {
                        this.initializeSelect2();
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        createData() {
            this.taskErrors.pus_id = !this.taskData.pus_id;
            this.taskErrors.namabalita = !this.taskData.namabalita;
            this.taskErrors.beratbadan_lahir = !this.taskData.beratbadan_lahir;
            this.taskErrors.panjangbadan_lahir =
                !this.taskData.panjangbadan_lahir;
            this.taskErrors.likep_lahir = !this.taskData.likep_lahir;
            this.taskErrors.date = !this.taskData.date;
            this.taskErrors.jenis_kelamin = !this.taskData.jenis_kelamin;

            this.taskData.tanggal_lahir = this.formatDate(this.taskData.date); // Date will be in selected format

            if (
                !this.taskErrors.pus_id &&
                !this.taskErrors.date &&
                !this.taskErrors.beratbadan_lahir &&
                !this.taskErrors.panjangbadan_lahir &&
                !this.taskErrors.likep_lahir
                && !this.taskErrors.jenis_kelamin
            ) {
                axios
                    .post(window.url + "api/storeBayi", this.taskData)
                    .then((response) => {
                        this.showAlert().then(() => this.resetForm());
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            }
        },
        initializeSelect2() {
            const vm = this;

            // Check if Select2 is already initialized, if so, destroy it
            if (
                $.fn.select2 &&
                $("#my-select").hasClass("select2-hidden-accessible")
            ) {
                $("#my-select").select2("destroy");
            }

            // Initialize Select2 again
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    // Update Vue model when Select2 value changes
                    vm.taskData.pus_id = $(this).val();
                });
            $("#posyandu")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    // Update Vue model when Select2 value changes
                    vm.taskData.posyandus_id = $(this).val();
                });
        },
        showAlert() {
            return Swal.fire({
                title: "Sukses!",
                text: "Data telah berhasil disimpan!",
                icon: "success",
                timer: 1500,
                showConfirmButton: false,
            });
        },
        resetForm() {
            this.taskData = {
                pus_id: "",
                namabalita: "",
                tanggal_lahir: "",
                beratbadan_lahir: "",
                panjangbadan_lahir: "",
                panjangbadan_lahir: "",
            };

            // Reinitialize Select2 after resetting form
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
    },
    mounted() {
        this.getPus();
        this.getPosyandu();
    },
    beforeDestroy() {
        // Destroy Select2 to avoid memory leaks
        $("#my-select").select2("destroy");
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
