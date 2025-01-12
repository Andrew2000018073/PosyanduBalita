<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Wanita Usia Subur</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            Tambah Wanita Usia Subur
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Pertama -->
    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Identitas</h3>
                </div>
                <!-- form start -->
                <div class="card-body">
                    <div class="form-group">
                        <label for="Nama">Nama</label>
                        <input type="text" class="form-control" placeholder="Masukan Nama" id="Nama"
                            v-model="taskData.nama" />
                        <span class="text-danger" v-show="taskErrors.nama">Masukan nama wanita usia subur</span>
                    </div>

                    <div class="form-group">
                        <label for="date">Tanggal Lahir</label>
                        <VueDatePicker v-model="taskData.date" :format="format" placeholder="Masukan Tanggal Lahir"
                            :enable-time-picker="false"></VueDatePicker>
                        <span class="text-danger" v-show="taskErrors.date">Masukan tanggal lahir</span>
                    </div>

                    <div class="form-group">
                        <label for="lingkep">Lingkar Lengan Wus (cm)</label>
                        <input type="number" min="0" max="1000" step="0.01" class="form-control" id="lingkep"
                            v-model="taskData.lila_wus" placeholder="Masukan Lingkar Kepala Wus (cm)" />
                        <span class="text-danger" v-show="taskErrors.lila_wus">Silahkan masukan lila!</span>
                    </div>

                    <div class="form-group">
                        <label for="Tempat Pelaksanaan">Tempat Pelaksanaan</label>
                        <select class="form-control select2" v-model="taskData.posyandus_id" style="width: 100%"
                            id="my-select">
                            <option disabled value="">
                                - Silahkan memilih tempat pelaksanaan -
                            </option>
                            <option v-for="task in tasks" :key="task.id" :value="task.id">
                                {{ task.nama_posyandu }}
                            </option>
                        </select>
                        <span class="text-danger" v-show="taskErrors.posyandus_id">Silahkan Pilih Tempat
                            Pelaksanaan!</span>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="maritalStatus" value="Menikah"
                                        v-model="selectedStatus" />
                                    <label class="form-check-label">Menikah</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="maritalStatus"
                                        value="Tidak Menikah" v-model="selectedStatus" />
                                    <label class="form-check-label">Tidak Menikah</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-center" v-show="selectedStatus === 'Tidak Menikah'">
                    <div class="row p-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary" @click="storeWUS">
                                Submit
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-secondary" @click="resetForm">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kondisi jika menikah -->
    <div class="row" v-if="selectedStatus === 'Menikah'">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Wus dalam kondisi menikah</h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="radio1" value="Hamil"
                                        v-model="statusHamil" />
                                    <label class="form-check-label">Hamil</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="radio1" value="Tidak Hamil"
                                        v-model="statusHamil" />
                                    <label class="form-check-label">Tidak Hamil</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kondisi jika tidak hamil -->
    <div class="row" v-if="statusHamil === 'Tidak Hamil' && selectedStatus === 'Menikah'">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Wus dalam kondisi Tidak Hamil</h3>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card-body">
                            <div class="form-group">
                                <select class="form-control" v-model="taskData.statuswus">
                                    <option disabled value="">
                                        -Silahkan Dipilih-
                                    </option>
                                    <option value="Nifas">Nifas</option>
                                    <option value="Tidak hamil & Menyusui">
                                        Menyusui
                                    </option>
                                    <option value="Tidak hamil & Tidak menyusui">
                                        Tidak Menyusui
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <div class="row">
                        <div class="col">
                            <button type="submit" class="btn btn-primary" @click="storeWUSA">
                                Submit
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-secondary" @click="resetForm">
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kondisi jika hamil -->
    <div class="row" v-if="statusHamil === 'Hamil' && selectedStatus === 'Menikah'">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Wus Kondisi dalam keadaan hamil</h3>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <label for="kehamilan_ke">Kehamilan Ke</label>
                        <input type="number" class="form-control" v-model="taskData.kehamilan_ke" />
                        <span class="text-danger" v-show="taskErrors.kehamilan_ke">Masukan data kehamilan</span>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_awal_hamil">Tanggal Awal Hamil</label>
                        <VueDatePicker v-model="taskData.tanggal_awal_hamil" :format="format"
                            placeholder="Masukan Tanggal terakhir datang bulan" :enable-time-picker="false">
                        </VueDatePicker>
                        <span class="text-danger" v-show="taskErrors.tanggal_awal_hamil">Masukan data tanggal awal
                            kehamilan</span>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row text-center">
                        <div class="col">
                            <button type="submit" class="btn btn-primary" @click="storeHamil">
                                Submit
                            </button>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-secondary" @click="resetForm">
                                Reset
                            </button>
                        </div>
                    </div>
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
import Swal from "sweetalert2";
import axios from "axios";

export default {
    components: {
        VueDatePicker,
    },
    data() {
        return {
            selectedStatus: null,
            statusHamil: null,
            tasks: [],
            format: "dd-MM-yyyy",
            taskData: {
                nama: "",
                tanggal_lahir: "",
                kehamilan_id: "",
                kehamilan_ke: "",
                tanggal_awal_hamil: "",
                tanggal_akhir_hamil: "",
                tanggal_daftar: "",
                stats: "",
                lila_wus: "",
                statuswus: "",
                posyandus_id: "",
                date: "",
            },
            taskErrors: {
                nama: false,
                tanggal_lahir: false,
                kehamilan_ke: false,
                tanggal_awal_hamil: false,
                lila_wus: false,
                date: false,
            },
        };
    },
    mounted() {
        this.getPosyandu();
    },
    beforeDestroy() {
        $("#my-select").select2("destroy");
    },
    methods: {
        formatDate(date) {
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
                    this.tasks = response.data;
                    this.initializeSelect2();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        initializeSelect2() {
            const vm = this;
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    vm.taskData.posyandus_id = $(this).val();
                });
        },
        storeWUS() {
            this.taskData.statuswus = "Tidak menikah";
            this.taskErrors.nama = !this.taskData.nama;
            this.taskErrors.lila_wus = !this.taskData.lila_wus;
            this.taskErrors.posyandus_id = !this.taskData.posyandus_id;
            this.taskErrors.date = !this.taskData.date;

            this.taskData.tanggal_lahir = this.formatDate(this.taskData.date);

            if (
                !this.taskErrors.nama &&
                !this.taskErrors.date &&
                !this.taskErrors.lila_wus
            ) {
                axios
                    .post(window.url + "api/storeWus", this.taskData)
                    .then((response) => {
                        this.showAlert().then(() => this.resetForm());
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            }
        },

        storeWUSA() {
            this.taskErrors.nama = !this.taskData.nama;
            this.taskErrors.date = !this.taskData.date;
            this.taskErrors.posyandus_id = !this.taskData.posyandus_id;
            this.taskErrors.date = !this.taskData.date;
            this.taskData.tanggal_lahir = this.formatDate(this.taskData.date);

            if (!this.taskErrors.nama && !this.taskErrors.date) {
                axios
                    .post(window.url + "api/storeWusMenyusui", this.taskData)
                    .then((response) => {
                        this.showAlert().then(() => this.resetForm());
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            }
        },

        storeHamil() {
            this.taskData.statuswus = "Hamil";
            this.taskErrors.nama = !this.taskData.nama;
            this.taskErrors.date = !this.taskData.date;
            this.taskErrors.kehamilan_ke = !this.taskData.kehamilan_ke;
            this.taskErrors.tanggal_awal_hamil =
                !this.taskData.tanggal_awal_hamil;
            this.taskErrors.posyandus_id = !this.taskData.posyandus_id;

            this.taskData.tanggal_lahir = this.formatDate(this.taskData.date);
            this.taskData.tanggal_awal_hamil = this.formatDate(
                this.taskData.tanggal_awal_hamil
            );

            axios
                .post(window.url + "api/storeKehamilan", this.taskData)
                .then((response) => {
                    const createdId = response.data.id;
                    console.log(response.data);
                })
                .catch((errors) => {
                    console.log(errors);
                });

            if (
                !this.taskErrors.nama &&
                !this.taskErrors.date &&
                !this.taskErrors.kehamilan_ke &&
                !this.taskErrors.tanggal_awal_hamil
            ) {
                axios
                    .post(window.url + "api/StoreWusHamil", this.taskData)
                    .then((response) => {
                        this.showAlert().then(() => this.resetForm());
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            }
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
                nama: "",
                tanggal_lahir: "",
                kehamilan_id: "",
                kehamilan_ke: "",
                tanggal_awal_hamil: "",
                tanggal_akhir_hamil: "",
                tanggal_daftar: "",
                stats: "",
            };
            this.selectedStatus = null;
            this.statusHamil = null;
        },
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
