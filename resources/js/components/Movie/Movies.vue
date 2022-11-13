<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Pel&iacute;culas</h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                                    <i class="fa fa-plus-square"></i>
                                    Nueva Pel&iacute;cula
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>T&iacute;tulo</th>
                                    <th>Categor&iacute;a</th>
                                    <th>A&ntilde;o</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="movie in movies.data" :key="movie.id">

                                    <td>{{movie.id}}</td>
                                    <td>{{movie.title}}</td>
                                    <td>{{movie.category}}</td>
                                    <td>{{movie.year}}</td>
                                    <td>{{movie.stock}}</td>
                                    <td>

                                        <a href="#" @click="editModal(movie)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteMovie(movie.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination :data="movies" @pagination-change-page="getResults"></pagination>
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
                            <h5 class="modal-title" v-show="!editmode">Crear Nueva Pel&iacute;cula</h5>
                            <h5 class="modal-title" v-show="editmode">Actualizar PEl&iacute;cula</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updateMovie() : createMovie()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>T&iacute;tulo</label>
                                    <input v-model="form.title" type="text" name="title"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                                    <has-error :form="form" field="title"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Categor&iacute;a</label>
                                    <input v-model="form.category" type="text" name="category"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('category') }">
                                    <has-error :form="form" field="category"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>A&ntilde;o</label>
                                    <input v-model="form.year" type="number" name="year"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('year') }">
                                    <has-error :form="form" field="year"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Stock</label>
                                    <input v-model="form.stock" type="number" name="stock"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('stock') }">
                                    <has-error :form="form" field="stock"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Descripci&oacute;n</label>
                                    <input v-model="form.description" type="text" name="description"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('description') }">
                                    <has-error :form="form" field="description"></has-error>
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
            movies : {},
            form: new Form({
                id : '',
                title : '',
                category: '',
                description: '',
                year: '',
                stock: '',
            })
        }
    },
    methods: {

        getResults(page = 1) {

            this.$Progress.start();

            axios.get('api/movie?page=' + page).then(({ data }) => (this.movies = data.data));

            this.$Progress.finish();
        },
        loadMovies(){

            // if(this.$gate.isAdmin()){
            axios.get("api/movie").then(({ data }) => (this.movies = data.data));
            // }
        },
        editModal(movie){
            this.editmode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(movie);
        },
        newModal(){
            this.editmode = false;
            this.form.reset();
            $('#addNew').modal('show');
        },
        createMovie(){
            this.$Progress.start();

            this.form.post('api/movie')
                .then((data)=>{
                    if(data.data.success){
                        $('#addNew').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: data.data.message
                        });
                        this.$Progress.finish();
                        this.loadMovies();

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
        updateMovie(){
            this.$Progress.start();
            this.form.put('api/movie/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                    this.$Progress.finish();

                    this.loadMovies();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

        },
        deleteMovie(id){
            Swal.fire({
                title: 'Desea eliminar esta Pelicula?',
                text: "No podrás revertir esto!",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si, Eliminar!'
            }).then((result) => {

                // Send request to the server
                if (result.value) {
                    this.form.delete('api/movie/'+id).then(()=>{
                        Swal.fire(
                            'Eliminado!',
                            'Pelicula Eliminada',
                            'success'
                        );
                        this.loadMovies();
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
