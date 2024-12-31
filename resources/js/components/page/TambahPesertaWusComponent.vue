<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Kegiatan Posyandu Wus</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/tambah-kegiatan">Tambah Kegiatan</router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            Tambah Peserta Wus
                        </li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Konten identitas -->
    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Identitas WUS</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Tempat Pelaksanaan">Masukan Nama WUS</label>
                                <select class="form-control select2" style="width: 100%" id="my-select"
                                    v-model="taskData.wus_id">
                                    <option disabled value="">
                                        - Masukan Nama WUS -
                                    </option>
                                    <option v-for="task in wuses" :key="task.id" :value="task.id"
                                        @click="getData(task.id)">
                                        {{ task.nama }}
                                    </option>
                                </select>
                                <span class="text-danger" v-show="taskErrors.wus_id">Silahkan Pilih Peserta!</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="Status">Status</label>
                            <div class="form-group">
                                <select class="form-control" v-model="taskData.statuswus">
                                    <option disabled value="">
                                        -Silahkan Dipilih-
                                    </option>
                                    <option value="Tidak menikah" @click="shownon">
                                        Tidak menikah
                                    </option>
                                    <option value="Hamil" @click="showhamil">
                                        Hamil
                                    </option>
                                    <option value="Nifas" @click="shownifas">
                                        Nifas
                                    </option>
                                    <option value="Tidak hamil & Menyusui" @click="shownon">
                                        Menyusui
                                    </option>
                                    <option value="Tidak hamil & Tidak menyusui" @click="shownon">
                                        Tidak Menyusui
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="Tensi">Tekanan Darah</label>

                            <div class="row">
                                <div class="col-5">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="tensi" placeholder="Sistol/Atas"
                                            v-model="taskData.sistol" />
                                        <span class="text-danger" v-show="taskErrors.sistol">Masukan hasil pemerkisaan
                                            tensi</span>
                                    </div>
                                </div>
                                <div class="col-2 text-center">
                                    <div class="form-group">
                                        <p>/</p>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <div class="form-group">
                                        <input type="number" class="form-control" id="tensi" placeholder="Diastol/Bawah"
                                            v-model="taskData.diastol" />
                                        <span class="text-danger" v-show="taskErrors.diastol">Masukan hasil pemerkisaan
                                            tensi</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lila">Lingkar Lengan</label>
                                <input type="number" class="form-control" id="lila" step="0.01"
                                    placeholder="Masukan Lingkar Lengan(cm)" v-model="taskData.lila_periksa" />
                                <span class="text-danger" v-show="taskErrors.lila_periksa">Masukan lingkar
                                    periksa</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lingkar_perut">Lingkar Perut</label>
                                <input type="number" class="form-control" id="lingkar_perut" step="0.01"
                                    placeholder="Masukan Linkgar Perut (cm)" v-model="taskData.lingkarperut_periksa" />
                                <span class="text-danger" v-show="taskErrors.lingkarperut_periksa">Masukan lingkar perut
                                    periksa</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    <!-- Hamil -->
    <div class="row" v-show="PregnantStatus">
        <div class="col">
            <div class="card card-success ms-3">
                <div class="card-header">
                    <h3 class="card-title">Wus Hamil</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="kehamilan_ke">Hamil baru?</label>
                                <input type="checkbox" class="ml-2" v-model="hamilbaru" />
                            </div>
                        </div>
                        <div class="row" v-show="hamilbaru">
                            <div class="col-md-2">
                                <label for="kehamilan_ke">Kehamilan Ke</label>
                            </div>
                            <div class="col">
                                <p class="text-danger">
                                    Kosongkan jika sudah pernah mendaftar hamil
                                </p>
                            </div>
                        </div>

                        <input v-show="hamilbaru" type="number" class="form-control" v-model="taskData.kehamilan_ke" />
                        <span class="text-danger" v-show="taskErrors.kehamilan_ke">Masukan data kehamilan</span>
                    </div>

                    <div class="form-group" v-show="hamilbaru">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="tanggal_awal_hamil">Tanggal Awal Hamil</label>
                            </div>
                            <div class="col">
                                <p class="text-danger">
                                    Kosongkan jika sudah pernah mendaftar hamil
                                </p>
                            </div>
                        </div>
                        <VueDatePicker v-model="taskData.tanggal_awal_hamil" :format="format"
                            placeholder="Masukan Tanggal terakhir datang bulan" :enable-time-picker="false">
                        </VueDatePicker>
                        <span class="text-danger" v-show="taskErrors.tanggal_awal_hamil">Masukan data tanggal awal
                            kehamilan</span>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="imunisasiStatus">Imunisasi ke?</label>
                                <div class="from-group">
                                    <input max="5" type="number" placeholder="Masukan Normol Kuartal"
                                        class="form-control" v-model="taskData.imunisasi_kehamilans_kuartal
                                            " />
                                </div>
                                <span class="text-danger" v-show="taskErrors.imunisasi_kehamilans_kuartal
                                    ">Masukan Jumlah Penggunaan</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tinggi_fundus">Tinggi Fundus</label>
                                <input type="number" class="form-control" id="desa" step="0.01"
                                    placeholder="Masukan Tinggi Fundus (cm)" v-model="taskData.tinggi_fundus" />
                                <span class="text-danger" v-show="taskErrors.tinggi_fundus">Masukan tinggi fundus</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="TotalpilTB">Total Pill Tambah Darah</label>
                                <input type="number" class="form-control" id="pilTB"
                                    placeholder="Masukan total penggunaan pil tambah darah" v-model="taskData.tablettambah_darahs_kuartal
                                        " />
                                <span class="text-danger" v-show="taskErrors.tablettambah_darahs_kuartal
                                    ">Masukan total pill yang digunakan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    <!-- Nifas -->
    <div class="row" v-show="NifasStatus">
        <div class="col">
            <div class="card card-success ms-3">
                <div class="card-header">
                    <h3 class="card-title">Wus Nifas</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="kuartal_vitamin">Vitamin</label>
                                <input type="number" class="form-control" id="kuartal_vitamin"
                                    placeholder="Masukan Nomor Kuartal" v-model="taskData.vitamin_kuartal" />
                                <span class="text-danger" v-show="taskErrors.vitamin_kuartal">Masukan Jumlah
                                    Penggunaan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div class="row">
        <div class="col">
            <div class="card card-danger ms-3">
                <div class="card-header">
                    <h3 class="card-title">Keluhan</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="keluhan">Keluhan</label>
                                <textarea id="keluhan" cols="30" rows="10" class="form-control"
                                    v-model="taskData.keluhan"></textarea>
                                <span class="text-danger" v-show="taskErrors.keluhan">Masukan Keluhan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" @click="sendData">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import $ from "jquery";
import "select2/dist/css/select2.css";
import "select2";
import axios from "axios";
import Swal from "sweetalert2";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

export default {
    components: {
        VueDatePicker,
    },
    data() {
        return {
            PregnantStatus: false,
            NormalStatus: false,
            NifasStatus: false,
            UseImunisasi: false,
            UseVitamin: false,
            hamilbaru: false,
            wuses: [], // Menyimpan data dari API
            posyandus: [],
            format: "dd-MM-yyyy",
            kegiatanId: "", // Declare kegiatanId
            taskData: {
                wus_id: "",
                vitamin_kuartal: "",
                kegiatanposyandu_w_u_s_id: "",
                tablettambah_darahs_kuartal: "",
                imunisasi_kehamilans_kuartal: "",
                lila_periksa: "",
                lingkarperut_periksa: "",
                tinggi_fundus: "",
                // hasil_penimbangan: '',
                keluhan: "",
                statuswus: "",
                kehamilan_ke: "",
                tanggal_awal_hamil: "",
                diastol: "",
                sistol: "",
            },
            taskErrors: {
                wus_id: false,
                vitamin_kuartal: false,
                tablettambah_darahs_kuartal: false,
                imunisasi_kehamilans_kuartal: false,
                lila_periksa: false,
                lingkarperut_periksa: false,
                tinggi_fundus: false,
                // hasil_penimbangan: false,
                keluhan: false,
                statuswus: false,
                diastol: false,
                sistol: false,
            },
        };
    },
    mounted() {
        console.log("Route ID:", this.$route.params.id);
        this.getPosyandus();
        this.getWus();
    },
    created() {
        this.kegiatanId = localStorage.getItem("PassedId"); // Set kegiatanId from localStorage
        console.log("ID from localStorage:", this.kegiatanId); // Log the ID
    },

    watch: {
        taskData: {
            handler(newData) {
                console.log("taskData updated:", newData);
            },
            deep: true, // Ensure Vue watches nested properties
        },
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
        showAlert() {
            Swal.fire({
                title: "Berhasil",
                text: "Selamat Melaksanakan Kegiatan",
                icon: "success",
                confirmButtonText: "OK",
            });
        },
        shownon() {
            this.PregnantStatus = false;
            this.NormalStatus = true;
            this.NifasStatus = false;
        },
        showhamil() {
            this.PregnantStatus = true;
            this.NormalStatus = false;
            this.NifasStatus = false;
        },
        shownifas() {
            this.PregnantStatus = false;
            this.NormalStatus = false;
            this.NifasStatus = true;
        },
        getPosyandus() {
            axios
                .get(window.url + "api/getKegiatanActive/" + this.kegiatanId)
                .then((response) => {
                    this.posyandus = response.data; // Save Posyandu data
                    // console.log(this.posyandus);
                    this.$nextTick(() => {
                        this.initializeSelect2(); // Re-initialize select2 after the DOM is updated
                    });
                    // Call getWus after posyandus has been set
                    this.getWus();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        getWus() {
            // Check if posyandus is an array and contains at least one item
            if (Array.isArray(this.posyandus) && this.posyandus.length > 0) {
                const posyanduId = this.posyandus[0].posyandu_id; // Access the first item’s posyandu_id
                axios
                    .get(window.url + "api/getWusPeserta/" + posyanduId)
                    .then((response) => {
                        this.wuses = response.data; // Save WUS data
                        this.$nextTick(() => {
                            this.initializeSelect2(); // Re-initialize select2 after the DOM is updated
                        });
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            } else {
                console.log(
                    "No posyandus available or posyandus not properly initialized."
                );
            }
        },

        getData(taskId) {
            console.log("Fetching data for WUS ID:", taskId); // Check if this runs

            axios
                .get(`${window.url}api/datawus/${taskId}`)
                .then((response) => {
                    console.log("Received data:", response.data); // Log the response data

                    // Assuming response.data is an array and you need the first item
                    if (
                        Array.isArray(response.data) &&
                        response.data.length > 0
                    ) {
                        const data = response.data[response.data.length - 1];

                        // Only update the specific fields in taskData
                        this.taskData.tablettambah_darahs_kuartal =
                            data.tablettambah_darahs_kuartal + 1 || "";
                        if (data.imunisasi_kehamilans_kuartal >= 5) {
                            this.taskData.imunisasi_kehamilans_kuartal = 1;
                        } else {
                            this.taskData.imunisasi_kehamilans_kuartal =
                                data.imunisasi_kehamilans_kuartal + 1 || "";
                        }
                        this.taskData.vitamin_kuartal =
                            data.vitamin_kuartal + 1 || "";
                    } else {
                        console.error(
                            "Unexpected response format:",
                            response.data
                        );
                    }
                })
                .catch((errors) => {
                    // Only update the specific fields in taskData
                    this.taskData.tablettambah_darahs_kuartal = "";
                    this.taskData.imunisasi_kehamilans_kuartal = "";
                    this.taskData.vitamin_kuartal = "";
                });
        },
        initializeSelect2() {
            const vm = this;
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                })
                .on("change", function () {
                    const selectedWusId = $(this).val();
                    vm.taskData.wus_id = selectedWusId;

                    console.log("Selected WUS ID:", selectedWusId); // Check if select2 is working

                    if (selectedWusId) {
                        vm.getData(selectedWusId); // Fetch data for the selected WUS ID
                    }
                });
        },

        sendData() {
            this.taskData.kegiatanposyandu_w_u_s_id = this.kegiatanId; // Use this.kegiatanId

            // if(this.NormalStatus===true){

            // }
            this.taskErrors.wus_id = !this.taskData.wus_id;
            this.taskErrors.diastol = !this.taskData.diastol;
            this.taskErrors.sistol = !this.taskData.sistol;
            this.taskErrors.lingkarperut_periksa =
                !this.taskData.lingkarperut_periksa;
            this.taskErrors.lila_periksa = !this.taskData.lila_periksa;
            this.taskErrors.keluhan = !this.taskData.keluhan;

            if (this.PregnantStatus === true) {
                this.taskErrors.tinggi_fundus = !this.taskData.tinggi_fundus;
                this.taskErrors.tablettambah_darahs_kuartal =
                    !this.taskData.tablettambah_darahs_kuartal;
                this.taskErrors.imunisasi_kehamilans_kuartal =
                    !this.taskData.imunisasi_kehamilans_kuartal;
            } else if (this.NifasStatus === true) {
                this.taskErrors.vitamin_kuartal =
                    !this.taskData.vitamin_kuartal;
            } else {
                this.taskData.tinggi_fundus = "";
                this.taskData.tablettambah_darahs_kuartal = "";
                this.taskData.imunisasi_kehamilans_kuartal = "";
                this.taskData.vitamin_kuartal = "";
            }

            axios
                .post(
                    window.url + "api/updateWusPeserta/" + this.taskData.wus_id,
                    this.taskData
                )
                .then((response) => {
                    const createdId = response.data.id;
                    console.log("Created ID:", createdId);
                    console.log(response.data);
                })
                .catch((errors) => {
                    console.log(errors);
                });
            if (this.PregnantStatus === true) {
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
            } else {
                console.log("Tidak Hamil");
            }

            if (
                !this.taskErrors.wus_id &&
                !this.taskErrors.lila_periksa &&
                !this.taskErrors.lingkarperut_periksa &&
                !this.taskErrors.diastol &&
                !this.taskErrors.sistol &&
                !this.taskErrors.keluhan
            ) {
                axios
                    .post(window.url + "api/storePesertaWus", this.taskData)
                    .then((response) => {
                        const createdId = response.data.id;
                        console.log("Created ID:", createdId);
                        console.log(response.data);
                        this.showAlert();
                        this.$router.push(
                            "/kegiatan-wus-active/" + this.kegiatanId
                        ); // Use this.kegiatanId
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            } else {
                console.log("Form validation failed.");
            }
        },
    },
};
</script>
