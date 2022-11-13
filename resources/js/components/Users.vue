<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">

            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header">
                <h3 class="card-title">Lista de Usuarios</h3>

                <div class="card-tools">

                  <button type="button" class="btn btn-sm btn-primary" @click="newModal">
                      <i class="fa fa-plus-square"></i>
                      Nuevo Usuario
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Role</th>
                      <th>Nombre</th>
                      <th>Usuario</th>
                      <th>Email</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="user in users.data" :key="user.id">

                      <td>{{user.id}}</td>
                      <td >{{user.type}}</td>
                      <td >{{user.name}}</td>
                      <td >{{user.username}}</td>
                      <td >{{user.email}}</td>
                      <td>

                        <a href="#" @click="editModal(user)">
                            <i class="fa fa-edit blue"></i>
                        </a>
                        /
                        <a href="#" @click="deleteUser(user.id)">
                            <i class="fa fa-trash red"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                  <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>


        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" v-show="!editmode">Crear Nuevo Usuario</h5>
                    <h5 class="modal-title" v-show="editmode">Actualizar Usuario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- <form @submit.prevent="createUser"> -->

                <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input v-model="form.name" type="text" name="name"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Usuario</label>
                            <input v-model="form.username" type="text" name="username"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('username') }">
                            <has-error :form="form" field="username"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="text" name="email"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                            <has-error :form="form" field="email"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <input v-model="form.password" type="password" name="password"
                                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" autocomplete="false">
                            <has-error :form="form" field="password"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <option value="">Selecione Role</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Encargado">Encargado</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
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
    export default {
        data () {
            return {
                editmode: false,
                users : {},
                form: new Form({
                    id : '',
                    type : '',
                    name: '',
                    username: '',
                    email: '',
                    password: '',
                    email_verified_at: '',
                })
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();

                  axios.get('api/user?page=' + page).then(({ data }) => (this.users = data.data));

                  this.$Progress.finish();
            },
            updateUser(){
                this.$Progress.start();
                this.form.put('api/user/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();

                    this.loadUsers();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Desea eliminar este Usuario?',
                    text: "No podrás revertir esto!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Si, Eliminar!'
                    }).then((result) => {

                        // Send request to the server
                         if (result.value) {
                                this.form.delete('api/user/'+id).then(()=>{
                                        Swal.fire(
                                        'Eliminado!',
                                        'Usuario Eliminado',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.loadUsers();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },
          loadUsers(){
            this.$Progress.start();

            if(this.$gate.isAdmin()){
              axios.get("api/user").then(({ data }) => (this.users = data.data));
            }

            this.$Progress.finish();
          },

          createUser(){
              this.form.post('api/user')
              .then((response)=>{
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: response.data.message
                  });

                  this.$Progress.finish();
                  this.loadUsers();

              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: '¡Ocurrió algún error! Inténtalo de nuevo'
                  });
              })
          }

        },
        mounted() {

        },
        created() {

            this.$Progress.start();
            this.loadUsers();
            this.$Progress.finish();
        }
    }
</script>
