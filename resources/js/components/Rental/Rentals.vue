<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Alquiler de Pel&iacute;culas</h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                                    <i class="fa fa-plus-square"></i>
                                    Nuevo Alquiler
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha Alquiler</th>
                                    <th>Fecha Devoluci&oacute;n</th>
                                    <th>Pel&iacute;cula</th>
                                    <th>Cliente</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="rental in rentals.data" :key="rental.id">

                                    <td>{{rental.id}}</td>
                                    <td>{{rental.delivery}}</td>
                                    <td>{{rental.entry}}</td>
                                    <td>{{rental.clients.name}}</td>
                                    <td>{{rental.movies.title}}</td>
                                    <td>{{rental.description | truncate(30, '...')}}</td>

                                    <td>

                                        <a href="#" @click="editModal(rental)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteRental(rental.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination :data="products" @pagination-change-page="getResults"></pagination>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" v-show="!editmode">Crear Nuevo Alquiler</h5>
                            <h5 class="modal-title" v-show="editmode">Actualizar Alquiler</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updateRental() : createRental()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Descripci&oacuten</label>
                                    <input v-model="form.description" type="text" name="description"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                                    <has-error :form="form" field="description"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Salida (YYYY-MM-DD)</label>
                                    <input v-model="form.delivery" type="text" name="delivery"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('delivery') }">
                                    <has-error :form="form" field="delivery"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Fecha Entrada (YYYY-MM-DD)</label>
                                    <input v-model="form.entry" type="text" name="entry"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('entry') }">
                                    <has-error :form="form" field="entry"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Cliente</label>
                                    <select class="form-control" v-model="form.client_id">
                                        <option
                                            v-for="(cat,index) in clients" :key="index"
                                            :value="index"
                                            :selected="index == form.client_id">{{ cat }}</option>
                                    </select>
                                    <has-error :form="form" field="client_id"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Pel&iacute;cula</label>
                                    <select class="form-control" v-model="form.movie_id">
                                        <option
                                            v-for="(cat,index) in movies" :key="index"
                                            :value="index"
                                            :selected="index == form.movie_id">{{ cat }}</option>
                                    </select>
                                    <has-error :form="form" field="movie_id"></has-error>
                                </div>
                            </div>
                            <div class="modal-footer">

                                <button v-show="editmode" type="submit" class="btn btn-success">Actualizar</button>
                                <button v-show="!editmode" type="submit" class="btn btn-primary">Crear</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import VueTagsInput from '@johmun/vue-tags-input';

export default {
    components: {
        VueTagsInput,
    },
    data () {
        return {
            editmode: false,
            rentals : {},
            form: new Form({
                id : '',
                delivery : '',
                entry: '',
                description: '',
                movie_id: '',
                client_id: '',
            }),
            clients: [],
            movies: [],
        }
    },
    methods: {

        getResults(page = 1) {

            this.$Progress.start();

            axios.get('api/rental?page=' + page).then(({ data }) => (this.rentals = data.data));

            this.$Progress.finish();
        },
        loadRentals(){

            // if(this.$gate.isAdmin()){
            axios.get("api/rental").then(({ data }) => (this.rentals = data.data));
            // }
        },
        loadClients(){
            axios.get("/api/client/list").then(({ data }) => (this.clients = data.data));
        },
        loadMovies(){
            axios.get("/api/movie/list").then(({ data }) => (this.movies = data.data));
        },
        editModal(rental){
            this.editmode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(rental);
        },
        newModal(){
            this.editmode = false;
            this.form.reset();
            $('#addNew').modal('show');
        },
        createRental(){
            this.$Progress.start();

            this.form.post('api/rental')
                .then((data)=>{
                    if(data.data.success){
                        $('#addNew').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: data.data.message
                        });
                        this.$Progress.finish();
                        this.loadRentals();

                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: '¡Ocurrió algún error! Inténtalo de nuevo'
                        });

                        this.$Progress.failed();
                    }
                })
                .catch(()=>{

                    Toast.fire({
                        icon: 'error',
                        title: '¡Ocurrió algún error! Inténtalo de nuevo'
                    });
                })
        },
        updateRental(){
            this.$Progress.start();
            this.form.put('api/rental/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                    this.$Progress.finish();

                    this.loadRentals();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

        },
        deleteRental(id){
            Swal.fire({
                title: 'Desea eliminar este Alquiler?',
                text: "No podrás revertir esto!",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si, Eliminar!'
            }).then((result) => {

                // Send request to the server
                if (result.value) {
                    this.form.delete('api/rental/'+id).then(()=>{
                        Swal.fire(
                            'Eliminado!',
                            'Alquiler Eliminado',
                            'success'
                        );
                        this.loadRentals();
                    }).catch((data)=> {
                        Swal.fire("Failed!", data.message, "warning");
                    });
                }
            })
        },

    },
    mounted() {

    },
    created() {
        this.$Progress.start();

        this.loadRentals();
        this.loadClients();
        this.loadMovies();

        this.$Progress.finish();
    },
    filters: {
        truncate: function (text, length, suffix) {
            return text.substring(0, length) + suffix;
        },
    },
    computed: {
        filteredItems() {
            return this.autocompleteItems.filter(i => {
                return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
            });
        },
    },
}
</script>
