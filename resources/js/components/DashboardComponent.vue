<template>
    <div class="row mt-3">
        <div class="col-md-3 col-sm-6 col-xs-3 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fi fi-br-prescription-bottle-alt"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Jumlah Kegiatan Bulan ini</span>
                    <span class="info-box-number">{{ jumlahkegiatan }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-3 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fi fi-ss-marriage-proposal"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Jumlah Pus</span>
                    <span class="info-box-number">{{ jumlahpus }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-3 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fi fi-sr-woman-head"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Jumlah Wus</span>
                    <span class="info-box-number">{{ jumlahwus }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-3 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fi fi-ss-baby"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Jumlah Balita</span>
                    <span class="info-box-number">{{ jumlahbalita }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>

    <!-- Tabel kegiatan -->
    <div class="row">
        <div class="col">
            <div class="card card-success">
                <div class="card-header">Tabel Kegiatan</div>
                <div class="table-responsive mt-2 mb-2">
                    <table id="tablekegiatan" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 20%;">Tanggal Kegiatan</th>
                                <th class="text-center" style="width: 20%;">Tipe Kegiatan</th>
                                <th class="text-center" style="width: 15%;">Total Peserta</th>
                                <th class="text-center" style="width: 25%;">Detail</th>
                                <th class="text-center" style="width: 20%;">Status Kegiatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(data, index) in kegiatan" :key="index" class="text-center">
                                <td class="text-center">
                                    {{ data.tgl_kegiatan }}
                                </td>
                                <td>{{ data.tipe_kegiatan }}</td>
                                <td class="text-center">
                                    <div v-if="data.tipe_kegiatan == 'WUS'">
                                        {{ data.periksawus_count }}
                                    </div>
                                    <div v-if="data.tipe_kegiatan == 'Balita'">
                                        {{ data.periksa_balita_count }}
                                    </div>
                                </td>
                                <td>{{ data.detail }}</td>
                                <td>{{ data.status_kegiatan }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import axios from "axios";
import "datatables.net";
import "datatables.net-bs4";
import ApexCharts from "vue3-apexcharts";
import $ from "jquery";

export default {
    components: {
        apexchart: ApexCharts,
    },
    data() {
        return {
            jumlahbalita: 0,
            jumlahwus: 0,
            jumlahpus: 0,
            jumlahkegiatan: 0,
            kegiatan: [],
            isKoordinator: false,
        };
    },


    methods: {
        getJumlahKegiatanbulanini() {
            axios.get(window.url + 'api/getJumlahKegiatanbulanini')
                .then(response => {
                    this.jumlahkegiatan = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        getJumlahBalita() {
            axios.get(window.url + 'api/getJumlahBalita')
                .then(response => {
                    this.jumlahbalita = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        getJumlahWus() {
            axios.get(window.url + 'api/getJumlahWus')
                .then(response => {
                    this.jumlahwus = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        getJumlahPus() {
            axios.get(window.url + 'api/getJumlahPus')
                .then(response => {
                    this.jumlahpus = response.data;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        getKegiatan() {
            axios
                .get(window.url + "api/getKegiatanlast5/")
                .then((response) => {
                    if ($.fn.DataTable.isDataTable("#tablekegiatan")) {
                        $("#tablekegiatan").DataTable().destroy();
                    }
                    this.kegiatan = response.data; // Ambil 5 data terakhir
                    this.$nextTick(() => {
                        $("#tablekegiatan").DataTable({
                            lengthChange: false,
                            paging: false,
                            searching: false,
                            info: false,
                            destroy: true,
                            ordering: false,
                            responsive: true
                        });
                    });
                })
                .catch((errors) => {
                    console.error(errors);
                });
        }

    },
    mounted() {
        this.getJumlahBalita();
        this.getJumlahWus();
        this.getJumlahPus();
        this.getJumlahKegiatanbulanini();
        this.getKegiatan();

    }
};

</script>

<style scoped>
@media (max-width: 768px) {

    table thead th:nth-child(4),
    table tbody td:nth-child(4) {
        display: none;
        /* Sembunyikan kolom ke-4 */
    }

    table td,
    table th {
        padding: 4px 6px;
        /* Atur padding sel agar lebih kecil */
        font-size: 11px;
        /* Perkecil ukuran font */
    }

    .table-header {
        background-color: #28a745;
        /* Hijau sesuai tema */
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

    .info-box .info-box-icon {
        border-radius: .25rem;
        -ms-flex-align: center;
        align-items: center;
        display: -ms-flexbox;
        display: flex;
        font-size: 1.875rem;
        -ms-flex-pack: center;
        justify-content: center;
        text-align: center;
        width: 40px;
    }

    .info-box .info-box-text,
    .info-box .progress-description {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 11px;
    }
}
</style>
