<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Clientes</span>
                            <span class="info-box-number">{{ clients }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-film"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Pel&iacute;culas en Stock</span>
                            <span class="info-box-number">{{ movies }}</span>
                        </div>
                    </div>
                </div>

                <div class="clearfix hidden-md-up"></div>

                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-shopping-cart"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total Alquiler</span>
                            <span class="info-box-number">{{ rentals }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>
</template>

<script>
    export default {
        data () {
            return {
                clients: '0',
                movies: '0',
                rentals: '0',
            }
        },
        methods: {
            async loadRentals(){
                await axios.get("api/rental/countRentals")
                    .then(response => {
                        this.rentals = response.data.data
                    })
                    .catch(error=>{
                        this.rentals = '0'
                    })
            },
            async loadClients(){
                await axios.get("api/client/countClients")
                    .then(response => {
                        this.clients = response.data.data
                    })
                    .catch(error=>{
                        this.clients = '0';
                    })
            },
            async loadMovies(){
                await axios.get("api/movie/countMovies")
                    .then(response => {
                        this.movies = response.data.data
                    })
                    .catch(error=>{
                        this.movies = '0'
                    })
            },
        },
        created() {

        },
        mounted() {
            this.loadRentals();
            this.loadClients();
            this.loadMovies();
        },
    }
</script>

