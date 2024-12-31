<template>
    <!-- Card Tabel daftar alat Kontrasepsi -->
    <div class="row">
        <div class="col">
            <div class="card card-primary">
                <div class="card-header">Tambah Kontrasepsi</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="Kontrasepsi">Pilih Alat Kontrasepsi</label>
                        <select
                            class="form-control select2"
                            style="width: 100%"
                            id="my-select-multiple"
                            multiple="multiple"
                            v-model="taskData.kontrasepsi_id"
                        >
                            <option disabled value="">
                                - Pilih Alat Kontrasepsi -
                            </option>
                            <option
                                v-for="task in kontrasepsis"
                                :key="task.id"
                                :value="task.id"
                            >
                                {{ task.nama }}
                            </option>
                        </select>
                        <span
                            class="text-danger"
                            v-show="taskErrors.kontrasepsi_id"
                            >Silahkan Pilih Alat Kontrasepsi!</span
                        >
                    </div>
                </div>
                <div class="card-footer text-muted text-center">
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="StoreKontrasepsi"
                    >
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import "select2/dist/css/select2.css";
import "select2";
import Swal from "sweetalert2";

export default {
    data() {
        return {
            taskData: {
                kontrasepsi_id: [], // Change to array
                pus_id: localStorage.getItem("PassedId"),
            },
            taskErrors: {
                kontrasepsi_id: false,
            },
            kontrasepsis: [],
        };
    },
    methods: {
        showAlert() {
            Swal.fire({
                title: "Berhasil",
                text: "Alat kontrasepsi berhasil ditambahkan",
                icon: "success",
                confirmButtonText: "OK",
            });
        },
        initializemultiSelect2() {
            const vm = this;
            $("#my-select-multiple")
                .select2({
                    dropdownAutoWidth: true,
                    width: "100%",
                })
                .on("change", function () {
                    vm.taskData.kontrasepsi_id = $(this).val();
                    console.log(
                        "Selected Kontrasepsi IDs (Multiple):",
                        $(this).val()
                    );
                });
        },
        StoreKontrasepsi() {
            this.taskErrors.kontrasepsi_id =
                !this.taskData.kontrasepsi_id.length; // Check if array is empty
            if (!this.taskErrors.kontrasepsi_id) {
                axios
                    .post(
                        window.url + "api/storepemakaiankontrasepsi",
                        this.taskData
                    )
                    .then((response) => {
                        const createdId = response.data.id;
                        console.log("Created ID:", createdId);
                        console.log(response.data);
                        this.showAlert();
                        this.$router.push("/detail-puses/" + this.pus_id); // Use this.kegiatanId
                    })
                    .catch((errors) => {
                        console.log(errors);
                    });
            }
        },
        getKontrasepsi() {
            axios
                .get(window.url + "api/getalatkontrasepsi")
                .then((response) => {
                    this.kontrasepsis = response.data;
                    console.log("Kontrasepsis fetched:", this.kontrasepsis); // Debugging
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },
    },
    mounted() {
        this.getKontrasepsi();
        this.initializemultiSelect2(); // Initialize here as well
    },
    created() {
        this.pus_id = localStorage.getItem("PassedId");
        console.log("ID from localStorage:", this.pus_id);
    },
};
</script>
