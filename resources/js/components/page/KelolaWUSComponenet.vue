<template>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Kelola WUS</h1>
                </div>
                <!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <router-link to="/">Dashboard</router-link>
                        </li>
                        <li class="breadcrumb-item active">Kelola WUS</li>
                    </ol>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>

    <!-- Tabel WUS -->
    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header">Tabel Wus</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tablewus" class="table table-striped table-bordered w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Umur Peserta</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Lingkar Lengan</th>
                                    <th class="text-center">Posyandu</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                                ? "Hapus Wus " + taskData.nama
                                : editMode
                                    ? "Perbarui Wus " + taskData.nama
                                    : "Error!"
                        }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="!deleteMode">
                        <!-- Form content for editing -->
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="namaposyandu">Nama Wus</label>
                                    <input type="text" class="form-control" v-model="taskData.nama" />
                                    <span class="text-danger" v-if="taskErrors.nama">Data Belum Terisi!</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="form-group">
                                    <label for="date">Tanggal Lahir</label>
                                    <VueDatePicker v-model="taskData.tanggal_lahir" :format="format"
                                        :placeholder="taskData.tanggal_lahir" :enable-time-picker="false" />
                                    <span class="text-danger" v-show="taskErrors.tanggal_lahir">Masukan tanggal
                                        lahir</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="lila">Lingkar Lengan Wus (cm)</label>
                                    <input type="number" min="0" max="1000" step="0.01" class="form-control"
                                        v-model="taskData.lila_wus" placeholder="Masukan Lingkar Lengan Wus (cm)" />
                                    <span class="text-danger" v-show="taskErrors.lila_wus">Silahkan masukan
                                        lila!</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="lila">Status</label>
                                    <select class="form-control" v-model="taskData.statuswus">
                                        <option disabled value="">
                                            -Silahkan Dipilih-
                                        </option>
                                        <option value="Tidak menikah">
                                            Tidak menikah
                                        </option>
                                        <option value="Hamil">Hamil</option>
                                        <option value="Nifas">Nifas</option>
                                        <option value="Tidak hamil & Menyusui">
                                            Menyusui
                                        </option>
                                        <option value="Tidak hamil & Tidak menyusui">
                                            Tidak Menyusui
                                        </option>
                                    </select>
                                    <span class="text-danger" v-show="taskErrors.statuswus">Status wus tidak
                                        valid!</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="posyandu">Posyandu</label>
                            <select class="form-control select2" v-model="taskData.posyandus_id" style="width: 100%"
                                id="my-select">
                                <option v-for="task in tasks" :key="task.id" :value="task.id">
                                    {{ task.nama_posyandu }}
                                </option>
                            </select>

                            <span class="text-danger" v-if="taskErrors.posyandus_id">Silahkan pilih posyandu!</span>
                        </div>
                    </div>

                    <div v-else>
                        <p>
                            Apakah Anda yakin ingin menghapus data
                            <b>{{ taskData.nama }}</b>?
                        </p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">
                        Tutup
                    </button>
                    <button v-if="!deleteMode" type="button" class="btn btn-primary" @click="updateTask">
                        Simpan
                    </button>
                    <button v-else type="button" class="btn btn-danger" @click="deleteTask">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import ApexCharts from "vue3-apexcharts";
import axios from "axios";
import Swal from "sweetalert2";
import "datatables.net";
import "datatables.net-bs4";
import $ from "jquery";
import "select2/dist/css/select2.css";
import "select2";
import { filter } from "lodash";
import { max } from "lodash";
import { parse } from 'date-fns';

export default {
    components: {
        VueDatePicker,
        apexchart: ApexCharts,
    },
    data() {
        return {
            format: "dd-MM-yyyy",
            date: null,
            wuses: [],
            status: [],
            posyandus: [],
            kek: [],
            modalkek: [],
            bandingkek: [],
            bandinggemuk: [],
            jumlahwus: null,
            deleteMode: false,
            editMode: false,
            taskData: {
                id: null,
                nama: "",

                statuswus: "",
                lila_wus: "",
                posyandus_id: "",
                tanggal_lahir: "",
            },
            taskErrors: {
                nama: false,
                tanggal_lahir: false,
                statuswus: false,
                lila_wus: false,
                posyandus_id: false,
            }
        };
    },
    mounted() {
        this.getWus();
        this.getPosyandu();
        window.navigateToDetail = (wusId) => {
            this.$router.push({ path: `/detail-wus/${wusId}` });
        };
    },
    beforeDestroy() {
        if ($("#my-select").data("select2")) {
            $("#my-select").select2("destroy");
        }
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
            if ($.fn.select2) {
                if ($("#my-select").data("select2")) {
                    $("#my-select").select2("destroy");
                }
            }

            $("#my-select")
                .select2({
                    dropdownAutoWidth: true,
                    placeholder: "- Silahkan memilih tempat pelaksanaan -",
                    allowClear: true,
                })
                .on("change", function () {
                    vm.taskData.posyandus_id = $(this).val();
                    console.log(vm.taskData.posyandus_id);
                });
        },

        getAge(dateString) {
            const [day, month, year] = dateString.split("-");
            const birthDate = new Date(`${year}-${month}-${day}`);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            if (
                monthDiff < 0 ||
                (monthDiff === 0 && today.getDate() < birthDate.getDate())
            ) {
                age--;
            }
            return age;
        },
        closeModal() {
            $("#taskModal").modal("hide");
        },

        getWus() {
            axios
                .get(window.url + "api/getWus")
                .then((response) => {
                    this.wuses = response.data;
                    this.reinitializeDataTable(this.wuses);
                    this.getPosyandu();
                })
                .catch((errors) => {
                    console.log(errors);
                });
        },

        reinitializeDataModal(data) {
            const plainData = JSON.parse(JSON.stringify(data));
            const table = $("#tablekek");
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }
            table.DataTable({
                data: plainData,
                processing: true,
                serverSide: false,
                columns: this.getDataModalColumns(),
            });
        },

        reinitializeDataTable(data) {
            const plainData = JSON.parse(JSON.stringify(data));
            const table = $("#tablewus");
            if ($.fn.DataTable.isDataTable(table)) {
                table.DataTable().clear().destroy();
            }
            table.DataTable({
                data: plainData,
                processing: true,
                serverSide: false,
                columns: this.getDataTableColumns(),
            });
            this.attachEventListeners();
        },

        getDataTableColumns() {
            return [
                { data: "nama", name: "nama" },
                {
                    data: "tanggal_lahir",
                    name: "tanggal_lahir",
                    render: (data, type, row) => {
                        return row.tanggal_lahir
                            ? `${this.getAge(row.tanggal_lahir)} tahun`
                            : "Data missing";
                    },
                },
                {
                    data: "statuswus",
                    name: "statuswus",
                    render: (data) =>
                        `<div class="text-center">${data || "N/A"}</div>`,
                },
                {
                    data: "lila_periksa",
                    name: "lila_periksa",
                    render: (data) =>
                        `<div class="text-center">${data ? `${data} cm` : "N/A"}</div>`,
                },
                { data: "posyandu_nama", name: "posyandu_nama" },
                {
                    data: null,
                    name: "aksi",
                    orderable: false,
                    searchable: false,
                    render: (data, type, row) => {
                        if (!row.wus_id) {
                            console.error(
                                "Error: wus_id is missing in row data",
                                row
                            );
                            return "<div class='text-center'>Error: ID missing</div>";
                        }
                        return this.getActionButtons(row);
                    },
                },
            ];
        },

        getDataModalColumns() {
            return [
                { data: "nama", name: "nama" },
            ];
        },

        getActionButtons(row) {
            return `
        <div class="row ">
            <div class="col text-center">
                <button type="button" class="btn btn-warning edit-task-btn btn-sm" data-id="${row.wus_id}">Edit</button>
            </div>
            <div class="col text-center">
                <button type="button" class="btn btn-danger  remove-task-btn btn-sm" data-id="${row.wus_id}">Delete</button>
            </div>
        </div>
    `;
        },
        attachEventListeners() {
            const table = $("#tablewus");

            table.on("click", ".edit-task-btn", this.handleEditClick);
            table.on("click", ".remove-task-btn", this.handleRemoveClick);
        },

        handleEditClick(event) {
            const id = $(event.currentTarget).data("id");
            const task = this.wuses.find((wus) => wus.wus_id === id);

            if (!id) {
                console.error("Edit button missing ID.");
                return;
            }
            if (task) {
                this.editTask(task);
            } else {
                console.error("Task not found for ID:", id);
            }
        },

        handleRemoveClick(event) {
            const id = $(event.currentTarget).data("id");
            console.log("Delete button clicked, ID:", id);

            if (!id) {
                console.error("Delete button missing ID.");
                return;
            }

            const task = this.wuses.find((wus) => wus.wus_id === id);
            if (task) {
                this.removeTask(task);
            } else {
                console.error("Task not found for ID:", id);
            }
        },

        removeTask(task) {
            this.deleteMode = true;
            this.taskData.id = task.wus_id;
            this.taskData.nama = task.nama;
            $("#taskModal").modal("show");
        },

        editTask(task) {
            console.log("Editing task:", task.posyandus_id);
            this.editMode = true;
            this.deleteMode = false;
            this.taskData = {
                ...task,
                tanggal_lahir: parse(task.tanggal_lahir, 'dd-MM-yyyy', new Date())
            };
            this.$nextTick(() => {
                $("#taskModal").modal("show");
                $("#taskModal").on("shown.bs.modal", () => {
                    this.initializeSelect2();
                });
            });
        },

        updateTask() {
            this.validateTask();
            console.log("Updating task with ID:", this.taskData.wus_id);
            if (this.isTaskValid()) {
                axios
                    .post(
                        `${window.url}api/updateWus/${this.taskData.wus_id}`,
                        this.taskData
                    )
                    .then(() => {
                        this.getWus();
                    })
                    .catch((errors) => {
                        console.error(errors);
                    })
                    .finally(() => {
                        $("#taskModal").modal("hide");
                    });
            } else {
                console.log("Task is not valid", this.taskErrors);
            }
        },

        validateTask() {
            this.taskErrors = {
                nama: !this.taskData.nama,
                tanggal_lahir: !this.taskData.tanggal_lahir,
                statuswus: !this.taskData.statuswus,
                lila_wus: !this.taskData.lila_wus,
                posyandus_id: !this.taskData.posyandus_id,
            };
            this.taskData.tanggal_lahir = this.formatDate(
                this.taskData.tanggal_lahir
            );
        },

        isTaskValid() {
            return !Object.values(this.taskErrors).includes(true);
        },

        deleteTask() {
            axios
                .post(`${window.url}api/deleteWus/${this.taskData.id}`)
                .then(() => {
                    this.getWus();
                })
                .catch((errors) => {
                    console.error(errors);
                })
                .finally(() => {
                    $("#taskModal").modal("hide");
                });
        },
    },
};
</script>

<style scoped>
@media (max-width: 768px) {

    table td,
    table th {
        padding: 4px 6px;
        font-size: 12px;
    }

    .table-header {
        background-color: #28a745;
        color: white;
        padding: 6px 10px;
        text-align: center;
        font-weight: bold;
    }

    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .col .col-md-3 .col-sm-6 .col-xs-3 .col-12 {
        max-width: 50%;
    }

    .col-12 {
        -ms-flex: 0 0 100%;
        flex: 0 0 100%;
        max-width: 50%;
    }

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
