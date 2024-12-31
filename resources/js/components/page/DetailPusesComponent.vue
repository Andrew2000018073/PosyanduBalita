<template>
    <!-- Modal -->
    <div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel"
        aria-hidden="true">
        <div :class="`modal-dialog modal-dialog-centered ${!deleteMode ? 'modal-lg' : 'modal-md'
            }`">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModalLabel">
                        {{
                            deleteMode
                                ? "Hapus data anak "
                                : editMode
                                    ? "Perbarui data anak " + taskData.namabalita
                                    : hapusKontrasepsi
                                        ? "Hapus Alat Kontrasepsi " +
                                        taskData.nama_kontrasepsi
                                        : "Selesai memakai alat kontrasepsi ini?"
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form or content based on deleteMode -->
                    <div v-if="editMode">
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <VueDatePicker v-model="taskData.tanggal_lahir" :format="format" placeholder="dd-mm-yyyy"
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
                    <div v-if="deleteMode">
                        <h5 class="text-center text-danger">
                            Hapus data {{ taskData.namabalita }} ?
                        </h5>
                    </div>
                    <div v-if="hapusKontrasepsi">
                        <h5 class="text-center text-danger">
                            Hapus data {{ taskData.nama_kontrasepsi }} ?
                        </h5>
                    </div>
                    <div v-if="selesaiKontrasepsi">
                        <h5 class="text-center text-danger">
                            Selesai menggunakan
                            {{ taskData.nama_kontrasepsi }} ?
                        </h5>
                    </div>
                </div>
                <!-- Edit Mode -->
                <div class="modal-footer" v-if="!deleteMode && editMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" @click="editMode ? updateTask() : storeImunisasi()"
                        v-if="!deleteMode">
                        {{ editMode ? "Perbarui Data" : "Simpan Data" }}
                    </button>
                </div>
                <!-- Delete Mode -->
                <div class="modal-footer" v-if="deleteMode">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteTask">
                        Hapus Data
                    </button>
                </div>
                <!-- Delete kontrasepsi -->
                <div class="modal-footer" v-if="hapusKontrasepsi">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="deleteKontrasepsi">
                        Hapus Data
                    </button>
                </div>
                <div class="modal-footer" v-if="selesaiKontrasepsi">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Close
                    </button>
                    <button type="button" class="btn btn-danger" @click="selesaiGuna">
                        Berhenti Menggunakan
                    </button>
                </div>
                <!-- Add Mode -->
            </div>
        </div>
    </div>

    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Detail Pasangan Usia Subur</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item">
                            <router-link to="/kelola-pus">Kelola Pus</router-link>
                        </li>
                        <li class="breadcrumb-item active">Detail Pasangan</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

    <!-- Card Informasi Personal -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Informasi PUS</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-4">Nama Suami</div>
                        <div class="col-1">:</div>
                        <div class="text-left col">
                            {{ informasis.nama_suami }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Nama Istri</div>
                        <div class="col-1">:</div>
                        <div class="col">
                            {{ informasis.wus?.nama }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Jumlah Anak Hidup</div>
                        <div class="col-1">:</div>
                        <div class="col">
                            {{ informasis.jumlah_anak_hidup }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">Jumlah Anak Terdaftar</div>
                        <div class="col-1">:</div>
                        <div class="col">
                            {{ banyakanakpus }}
                        </div>
                    </div>
                </div>
                <div class="card-footer"></div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>

    <!-- Card Tabel Anak terdaftar -->
    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">
                    Data Anak yang terdaftar dalam posyandu
                </div>
                <div class="card-body">
                    <table id="tablebayis" class="table table-striped table-bordered w-100" ref="imunisasiTable">
                        <thead>
                            <tr>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Umur</th>
                                <th class="text-center">Berat badan lahir</th>
                                <th class="text-center">Panjang badan lahir</th>
                                <th class="text-center">
                                    Lingkar kepala lahir
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in bayis" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ task.namabalita }}
                                </td>
                                <td class="text-center">
                                    {{ getAgeInMonths(task.tanggal_lahir) }}
                                    bulan
                                </td>
                                <td class="text-center">
                                    {{ task.beratbadan_lahir }} kg
                                </td>
                                <td class="text-center">
                                    {{ task.panjangbadan_lahir }} cm
                                </td>
                                <td class="text-center">
                                    {{ task.likep_lahir }} cm
                                </td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                @click="editTask(task)">
                                                Edit
                                            </button>
                                        </div>
                                        <div class="col">
                                            <button type="button" class="btn btn-danger btn-sm"
                                                @click="removeTask(task)">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Tabel daftar alat Kontrasepsi -->
    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">Data penggunaan alat kontrasepsi</div>
                <div class="card-body">
                    <table id="tablekontrasepsis" class="table table-striped table-bordered w-100"
                        ref="tablekontrasepsis">
                        <thead>
                            <tr>
                                <th class="text-center">Kontrasepsi ID</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">
                                    Tanggal Pertama Pakai
                                </th>
                                <th class="text-center">
                                    Tanggal Berhenti Pakai
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(task, index) in pakai" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ task.nama_kontrasepsi }}
                                </td>
                                <td :class="`text-center ${task.status.trim() === 'Sedang dipakai'
                                        ? 'text-primary'
                                        : 'text-danger'
                                    }`">
                                    {{ task.status }}
                                </td>
                                <td class="text-center">
                                    {{ task.tanggal_pertama_pakai }}
                                </td>
                                <td class="text-center">
                                    {{
                                        task.tanggal_berhenti_pakai != null
                                            ? task.tanggal_berhenti_pakai
                                            : "Masih dipakai"
                                    }}
                                </td>
                                <td class="text-center">
                                    <div class="row">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-success btn-sm mx-1"
                                                @click="selesaiPakai(task)">
                                                Selesai Pakai
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button" class="btn btn-danger btn-sm mr-5"
                                                @click="removeAlat(task)">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-muted text-center">
                    <button type="button" class="btn btn-primary" @click="storeIdAndNavigate">
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import "datatables.net";
import "datatables.net-bs4";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import "select2/dist/css/select2.css";
import "select2";

export default {
    components: {
        VueDatePicker,
    },
    data() {
        return {
            kegiatan: [],
            bayis: [],
            informasis: [],
            pakai: [],
            format: "dd-MM-yyyy",
            deleteMode: false,
            editMode: false,
            hapusKontrasepsi: false,
            selesaiKontrasepsi: false,
            banyakanakpus: 0,
            taskData: {
                id: "",
                tanggal_lahir: "",
                beratbadan_lahir: "",
                panjangbadan_lahir: "",
                likep_lahir: "",
                posyandus_id: "",
                date: "",
                kontrasepsi_id: "",
                pus_id: this.$route.params.id,
            },
            taskErrors: {
                jumlah_anak_hidup: false,
                lingkar_perut_suami: false,
                tanggal_lahir: false,
                beratbadan_lahir: false,
                panjangbadan_lahir: false,
                likep_lahir: false,
                posyandus_id: false,
                kontrasepsi_id: false,
            },
        };
    },

    methods: {
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
        formatDate(date) {
            // Function to format the date as dd-mm-yyyy
            const [year, month, day] = new Date(date)
                .toISOString()
                .split("T")[0]
                .split("-");
            return `${day}-${month}-${year}`;
        },
        editTask(task) {
            this.editMode = true;
            this.deleteMode = false;
            this.hapusKontrasepsi = false;
            this.selesaiKontrasepsi = false;
            this.taskData = { ...task };

            // Convert tanggal_lahir to Date object if it's a string
            if (typeof this.taskData.tanggal_lahir === "string") {
                const [day, month, year] =
                    this.taskData.tanggal_lahir.split("-");
                this.taskData.tanggal_lahir = new Date(
                    `${year}-${month}-${day}`
                );
            }

            this.taskData.posyandus_id = task.posyandus_id || ""; // Set the selected posyandu ID
            $("#taskModal").modal("show");
            this.$nextTick(() => {
                $("#posyandu")
                    .val(this.taskData.posyandus_id)
                    .trigger("change"); // Set value in Select2
            });

            $("#taskModal").modal("show");

            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },

        selesaiPakai(task) {
            this.deleteMode = false;
            this.editMode = false;
            this.hapusKontrasepsi = false;
            this.selesaiKontrasepsi = true;
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        updateTask() {
            this.taskData.tanggal_lahir === ""
                ? (this.taskErrors.tanggal_lahir = true)
                : (this.taskErrors.tanggal_lahir = false);
            this.taskData.panjangbadan_lahir === ""
                ? (this.taskErrors.panjangbadan_lahir = true)
                : (this.taskErrors.panjangbadan_lahir = false);
            this.taskData.beratbadan_lahir === ""
                ? (this.taskErrors.beratbadan_lahir = true)
                : (this.taskErrors.beratbadan_lahir = false);
            this.taskData.likep_lahir === ""
                ? (this.taskErrors.likep_lahir = true)
                : (this.taskErrors.likep_lahir = false);
            this.taskData.posyandus_id === ""
                ? (this.taskErrors.posyandus_id = true)
                : (this.taskErrors.posyandus_id = false);

            this.taskData.tanggal_lahir = this.formatDate(
                this.taskData.tanggal_lahir
            ); // Date will be in selected format

            if (
                this.taskData.tanggal_lahir &&
                this.taskData.panjangbadan_lahir &&
                this.taskData.beratbadan_lahir &&
                this.taskData.likep_lahir &&
                this.taskData.posyandus_id
            ) {
                axios
                    .post(
                        window.url + "api/updateBayis/" + this.taskData.id,
                        this.taskData
                    )
                    .then((response) => {
                        this.getBayis();
                    })
                    .catch((errors) => {
                        console.log(errors);
                    })
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            }
        },
        selesaiGuna() {
            axios
                .post(
                    window.url + "api/selesaiPakai/" + this.taskData.id,
                    this.taskData
                )
                .then((response) => {
                    this.getPakai();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
        removeTask(task) {
            this.deleteMode = true;
            this.editMode = false;
            this.hapusKontrasepsi = false;
            this.selesaiKontrasepsi = false;
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        removeAlat(task) {
            this.deleteMode = false;
            this.editMode = false;
            this.hapusKontrasepsi = true;
            this.selesaiKontrasepsi = false;
            this.taskData = { ...task };
            $("#taskModal").modal("show");
        },
        deleteTask() {
            axios
                .post(window.url + "api/deleteBayi/" + this.taskData.id)
                .then((response) => {
                    this.getBayis();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
        deleteKontrasepsi() {
            axios
                .post(
                    window.url +
                    "api/deleteKontrasepsiPakai/" +
                    this.taskData.id
                )
                .then((response) => {
                    this.getPakai();
                })
                .catch((errors) => {
                    console.log(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
        getData() {
            const id = this.$route.params.id;
            axios
                .get(window.url + "api/detail-puses/" + id)
                .then((response) => {
                    this.informasis = response.data; // Store the received data in taskData
                })
                .catch((error) => {
                    console.error("Error fetching data:", error);
                });
        },
        getBayis() {
            axios
                .get(window.url + "api/getAnak/" + this.$route.params.id)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#tablebayis")) {
                        $("#tablebayis").DataTable().destroy();
                    }
                    this.bayis = response.data;
                    this.banyakanakpus = this.bayis.length;
                    this.$nextTick(() => {
                        $("#tablebayis").DataTable({
                            pageLength: 10,
                            lengthChange: false,
                            destroy: true,
                        });
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
        getAgeInMonths(dateString) {
            // Split the date string by '-'
            const [day, month, year] = dateString.split("-");

            // Create a new Date object using the extracted year, month, and day
            const birthDate = new Date(`${year}-${month}-${day}`);
            const today = new Date();

            // Calculate the difference in years and months
            const yearsDiff = today.getFullYear() - birthDate.getFullYear();
            const monthsDiff = today.getMonth() - birthDate.getMonth();

            // Calculate the total age in months
            let ageInMonths = yearsDiff * 12 + monthsDiff;

            // Adjust if the current day of the month is earlier than the birth date's day
            if (today.getDate() < birthDate.getDate()) {
                ageInMonths--;
            }

            return ageInMonths;
        },
        closeModal() {
            $("#taskModal").modal("hide");
        },
        getPosyandu() {
            axios
                .get(window.url + "api/getPosyandu")
                .then((response) => {
                    this.posyandus = response.data;
                    this.$nextTick(() => {
                        $("#posyandu")
                            .val(this.taskData.posyandus_id)
                            .trigger("change"); // Set initial value
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        TambahKontrasepsi() {
            this.deleteMode = false;
            this.editMode = false;
            $("#taskModal").modal("show");
            this.$nextTick(() => {
                this.initializeSelect2();
            });
        },
        getPakai() {
            axios
                .get(window.url + "api/getAlatpakai/" + this.$route.params.id)
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#tablekontrasepsis")) {
                        $("#tablekontrasepsis").DataTable().destroy();
                    }
                    this.pakai = response.data;
                    this.$nextTick(() => {
                        $("#tablekontrasepsis").DataTable({
                            pageLength: 10,
                            lengthChange: false,
                            destroy: true,
                        });
                    });
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        removeKontrasepsi(rowIndex) {
            console.log("Row index:", rowIndex);
            this.taskData = { ...this.pakai[rowIndex] }; // Get the data for the row
            this.deleteMode = true; // Set delete mode
            this.editMode = false; // Ensure edit mode is off
            this.hapusKontrasepsi = false; // Ensure hapusKontrasepsi is off
            $("#taskModal").modal("show"); // Show the modal
        },
        storeIdAndNavigate() {
            const pusId = this.$route.params.id; // Get the ID from the route parameters
            console.log("Storing ID:", pusId); // Log the ID to ensure it’s correct

            // Check if pusId is defined before storing
            if (pusId) {
                localStorage.setItem("PassedId", pusId); // Store the ID
                console.log(
                    "Current localStorage value for PassedId:",
                    localStorage.getItem("PassedId")
                ); // Verify it's stored correctly
            } else {
                console.error("No ID found to store."); // Error handling if ID is not found
            }

            this.$router.push({ name: "tambah-kontrasepsi" }); // Navigate to the next page
        },
    },

    mounted() {
        this.getData();
        this.getBayis();
        this.getPosyandu();
        this.getPakai();
    },
};
</script>
