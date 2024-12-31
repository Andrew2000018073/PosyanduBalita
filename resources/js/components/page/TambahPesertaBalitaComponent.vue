<template>
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Tambah Peserta Posyandu Balita</h1>
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
                            Tambah Peserta Balita
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

    <div class="row">
        <div class="col">
            <div class="card card-primary ms-3">
                <div class="card-header">
                    <h3 class="card-title">Identitas Balita</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Nama Balita">Masukan Nama Balita</label>
                                <select class="form-control select2" style="width: 100%" id="my-select"
                                    v-model="bayis_id">
                                    <option disabled value="">
                                        - Masukan Nama Balita -
                                    </option>
                                    <option v-for="task in balitas" :key="task.id" :value="task.id"
                                        @click="getData(task.id)">
                                        {{ task.namabalita }}
                                    </option>
                                </select>
                                <span class="text-danger" v-show="inputErrors.bayis_id">Silahkan Pilih Peserta!</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tinggi">Panjang atau tinggi</label>
                                <input type="number" class="form-control" id="tinggi" step="0.01"
                                    placeholder="Masukan tinggi atau panjang peserta"
                                    v-model="inputData.panjang_badan" />
                                <span class="text-danger" v-show="inputErrors.panjang_badan">Masukan tinggi atau panjang
                                    peserta</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="berat">Berat Badan</label>
                                <input type="number" class="form-control" step="0.01" id="berat"
                                    placeholder="Masukan berat badan peserta" v-model="inputData.berat_badan" />
                                <span class="text-danger" v-show="inputErrors.berat_badan">Masukan berat badan
                                    peserta</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lingkep">Lingkar Kepala</label>
                                <input type="number" class="form-control" id="lingkep" step="0.01"
                                    placeholder="Masukan Lingkar Kepala(cm)" v-model="inputData.lingkep_periksa" />
                                <span class="text-danger" v-show="inputErrors.lingkep_periksa">Masukan lingkar
                                    Kepala</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Nama Balita">Pilih Jenis Imunisasi</label>
                                <select class="form-control select2" style="width: 100%" id="my-select-multiple"
                                    multiple="multiple" v-model="inputData.imunisasi_id">
                                    <option disabled value="">
                                        - Pilih imunisasi -
                                    </option>
                                    <option v-for="task in imunisasis" :key="task.id" :value="task.id">
                                        {{ task.nama }}
                                    </option>
                                </select>
                                <span class="text-danger" v-show="inputErrors.imunisasi_id">Silahkan Pilih
                                    Imunisasi!</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Jumlah vitamin">Konsumsi vitamin A</label>
                                <input type="number" class="form-control" id="kuartal_vitamin"
                                    placeholder="Masukan Nomor Kuartal" v-model="inputData.vitamin_kuartal" />
                                <span class="text-danger" v-show="inputErrors.vitamin_kuartal">Masukan Jumlah
                                    Penggunaan</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="Jumlah vitamin">Pemberian asi kuartal</label>
                                <input type="number" class="form-control" id="pemberian_asi_kuartal"
                                    placeholder="Masukan Nomor Kuartal" v-model="inputData.pemberian_asi_kuartal" />
                                <span class="text-danger" v-show="inputErrors.pemberian_asi_kuartal">Masukan Jumlah
                                    Pemberian</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="catatan">Catatan</label>
                        <textarea id="catatan" cols="30" rows="10" class="form-control"
                            v-model="inputData.catatan"></textarea>
                        <span class="text-danger" v-show="inputErrors.catatan">Masukan catatan</span>
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

export default {
    data() {
        return {
            imunisasis: [],
            balitas: [], // Menyimpan data dari API
            kegiatanId: "", // Declare kegiatanId
            inputData: {
                bayis_id: "",
                kegiatanposyandu_balita_id: "",
                panjang_badan: "",
                berat_badan: "",
                // hasil_penimbangan: '',
                lingkep_periksa: "",
                catatan: "",
                imunisasi_id: "",
                vitamin_kuartal: "",
                pemberian_asi_kuartal: "",
            },
            inputErrors: {
                bayis_id: false,
                kegiatanposyandu_balita_id: false,
                panjang_badan: false,
                berat_badan: false,
                // hasil_penimbangan: false,
                lingkep_periksa: false,
                catatan: false,
                imunisasi_id: false,
                vitamin_kuartal: false,
                pemberian_asi_kuartal: false,
            },
        };
    },
    mounted() {
        this.getBalitas();
        this.getImunisasis();
    },
    created() {
        this.kegiatanId = localStorage.getItem("PassedId"); // Set kegiatanId from localStorage
        console.log("ID from localStorage:", this.kegiatanId); // Log the ID
    },
    watch: {
        inputData: {
            handler(newData) {
                console.log("inputData updated:", newData);
            },
            deep: true, // Ensure Vue watches nested properties
        },
    },
    methods: {
        showAlert() {
            Swal.fire({
                title: "Berhasil",
                text: "Selamat Melaksanakan Kegiatan",
                icon: "success",
                confirmButtonText: "OK",
            });
        },
        getBalitas() {
            axios
                .get(window.url + "api/getpesertabalita/" + this.kegiatanId)
                .then((response) => {
                    this.balitas = response.data; // Save WUS data

                    this.$nextTick(() => {
                        this.initializeSelect2(); // Re-initialize select2 after the DOM is updated
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getImunisasis() {
            axios
                .get(window.url + "api/getImunisasiBayi/")
                .then((response) => {
                    this.imunisasis = response.data; // Save WUS data

                    this.$nextTick(() => {
                        this.initializemultiSelect2(); // Re-initialize select2 after the DOM is updated
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        initializeSelect2() {
            const vm = this;

            // Initialize single select
            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                    width: "100%", // Ensure proper dropdown width
                })
                .on("change", function () {
                    const selectedBalitasId = $(this).val();
                    vm.inputData.bayis_id = selectedBalitasId; // Update inputData
                    // console.log("Selected WUS ID (Single):", selectedBalitasId);

                    if (selectedBalitasId) {
                        vm.getData(selectedBalitasId); // Fetch data for the selected WUS ID
                    }
                });
        },
        initializemultiSelect2() {
            const vm = this;
            // Initialize multi select
            $("#my-select-multiple")
                .select2({
                    dropdownAutoWidth: true,
                    width: "100%", // Ensure proper dropdown width
                })
                .on("change", function () {
                    const selectedImunisasiId = $(this).val();
                    vm.inputData.imunisasi_id = selectedImunisasiId; // Update inputData for multiple WUS IDs
                    console.log(
                        "Selected Imunisasi  IDs (Multiple):",
                        selectedImunisasiId
                    );

                    // if (selectedBalitasIds && selectedBalitasIds.length > 0) {
                    //     vm.getData(selectedBalitasIds);  // Fetch data for the selected WUS IDs
                    // }
                });
        },
        getData(taskId) {
            console.log("Fetching data for Balitas ID:", taskId); // Check if this runs

            axios
                .get(`${window.url}api/databalita/${taskId}`)
                .then((response) => {
                    console.log("Received data:", response.data); // Log the response data

                    // Assuming response.data is an array and you need the first item
                    if (
                        Array.isArray(response.data) &&
                        response.data.length > 0
                    ) {
                        const data = response.data[response.data.length - 1];

                        // Only update the specific fields in taskData
                        if (data.pemberian_asi_kuartal > 5) {
                            this.inputData.pemberian_asi_kuartal = 1;
                        } else {
                            this.inputData.pemberian_asi_kuartal = data.pemberian_asi_kuartal + 1 || "";
                        }
                        if (data.vitamin_kuartal > 2) {
                            this.inputData.vitamin_kuartal = 1;
                        } else {
                            this.inputData.vitamin_kuartal = data.vitamin_kuartal + 1 || "";
                        }
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
        sendData() {
            this.inputData.kegiatanposyandu_balita_id = this.kegiatanId; // Use this.kegiatanId

            // Validate fields
            this.inputErrors.bayis_id = !this.inputData.bayis_id;
            this.inputErrors.lingkep_periksa = !this.inputData.lingkep_periksa;
            this.inputErrors.panjang_badan = !this.inputData.panjang_badan;
            this.inputErrors.berat_badan = !this.inputData.berat_badan;
            // this.inputErrors.hasil_penimbangan = !this.inputData.hasil_penimbangan;
            this.inputErrors.catatan = !this.inputData.catatan;
            this.inputErrors.imunisasi_id = !this.inputData.imunisasi_id;


            if (
                !this.inputErrors.bayis_id &&
                !this.inputErrors.lingkep_periksa &&
                !this.inputErrors.panjang_badan &&
                !this.inputErrors.berat_badan &&
                !this.inputErrors.catatan &&
                !this.inputErrors.imunisasi_id
            ) {
                axios
                    .post(window.url + "api/storePesertaBalita", this.inputData)
                    .then((response) => {
                        const createdId = response.data.id;
                        console.log("Created ID:", createdId);
                        console.log(response.data);
                        this.showAlert();
                        this.$router.push(
                            "/kegiatan-balita-active/" + this.kegiatanId
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
