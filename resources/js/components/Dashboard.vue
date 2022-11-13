<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row ">
                <!--div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Clientes</span>
                            <span class="info-box-number"></span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-thumbs-up"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Pel&iacute;culas</span>
                            <span class="info-box-number"></span>
                        </div>
                    </div>
                </div>

                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Alquiler</span>
                            <span class="info-box-number"></span>
                        </div>
                    </div>
                </div-->

            </div>
        </div>

    </section>
</template>

<script>
    export default {
        data () {
            return {
                clients: [],
                movies: [],
                rentals: [],
            }
        },
        methods: {
            prepareComponent() {
                this.loadRentals();
                this.loadClients();
                this.loadMovies();
            },
            loadRentals(){
                axios.get("api/rental/list").then(({ data }) => (this.rentals = data.data.size ));
            },
            loadClients(){
                axios.get("/api/client/list").then(({data}) => (this.clients = data.data.size ));

            },
            loadMovies(){
                axios.get("/api/movie/list").then(({ data }) => (this.movies = data.data));
            },
        },
        created() {
            this.prepareComponent();
        },
        mounted() {
        },
    }
</script>

