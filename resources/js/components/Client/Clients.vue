<template>
    <section class="content">
        <div class="container-fluid">
            <div class="row">

                <div class="col-12">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lista de Clientes</h3>

                            <div class="card-tools">

                                <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                                    <i class="fa fa-plus-square"></i>
                                    Nuevo cliente
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Identificación</th>
                                    <th>Nombres</th>
                                    <th>Apellidos</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="client in clients.data" :key="client.id">

                                    <td>{{client.id}}</td>
                                    <td>{{client.identification}}</td>
                                    <td>{{client.name}}</td>
                                    <td>{{client.lastname}}</td>
                                    <td>

                                        <a href="#" @click="editModal(client)">
                                            <i class="fa fa-edit blue"></i>
                                        </a>
                                        /
                                        <a href="#" @click="deleteClient(client.id)">
                                            <i class="fa fa-trash red"></i>
                                        </a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <pagination :data="clients" @pagination-change-page="getResults"></pagination>
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
                            <h5 class="modal-title" v-show="!editmode">Crear Nuevo Cliente</h5>
                            <h5 class="modal-title" v-show="editmode">Actualizar Cliente</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <form @submit.prevent="editmode ? updateClient() : createClient()">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Nombres</label>
                                    <input v-model="form.name" type="text" name="name"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                                    <has-error :form="form" field="name"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Apellidos</label>
                                    <input v-model="form.lastname" type="text" name="lastname"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('lastname') }">
                                    <has-error :form="form" field="lastname"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Identificaci&oacute;n</label>
                                    <input v-model="form.identification" type="text" name="identification"
                                           class="form-control" :class="{ 'is-invalid': form.errors.has('identification') }">
                                    <has-error :form="form" field="identification"></has-error>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
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
            clients : {},
            form: new Form({
                id : '',
                name : '',
                lastname: '',
                description: '',
                identification: '',
            })
        }
    },
    methods: {

        getResults(page = 1) {

            this.$Progress.start();

            axios.get('api/client?page=' + page).then(({ data }) => (this.clients = data.data));

            this.$Progress.finish();
        },
        loadClients(){

            // if(this.$gate.isAdmin()){
            axios.get("api/client").then(({ data }) => (this.clients = data.data));
            // }
        },
        editModal(client){
            this.editmode = true;
            this.form.reset();
            $('#addNew').modal('show');
            this.form.fill(client);
        },
        newModal(){
            this.editmode = false;
            this.form.reset();
            $('#addNew').modal('show');
        },
        createClient(){
            this.$Progress.start();

            this.form.post('api/client')
                .then((data)=>{
                    if(data.data.success){
                        $('#addNew').modal('hide');

                        Toast.fire({
                            icon: 'success',
                            title: data.data.message
                        });
                        this.$Progress.finish();
                        this.loadClients();

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
        updateClient(){
            this.$Progress.start();
            this.form.put('api/client/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.data.message
                    });
                    this.$Progress.finish();

                    this.loadClients();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

        },
        deleteClient(id){
            Swal.fire({
                title: 'Desea eliminar este Cliente?',
                text: "No podrás revertir esto!",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Si, Eliminar!'
            }).then((result) => {

                // Send request to the server
                if (result.value) {
                    this.form.delete('api/client/'+id).then(()=>{
                        Swal.fire(
                            'Eliminado!',
                            'Cliente Eliminado',
                            'success'
                        );
                        // Fire.$emit('AfterCreate');
                        this.loadClients();
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

        this.loadClients();

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
