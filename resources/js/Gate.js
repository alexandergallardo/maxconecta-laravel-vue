export default class Gate{

    constructor(user){
        this.user = user;
    }

    isAdmin(){
        return this.user.type === 'Administrador';
    }

    isUser(){
        return this.user.type === 'Encargado';
    }

    isAdminOrUser(){
        if(this.user.type === 'Encargado' || this.user.type === 'Administrador'){
            return true;
        }
    }
}

