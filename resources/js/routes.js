export default [
    { path: '/dashboard', component: require('./components/Dashboard.vue').default },
    { path: '/profile', component: require('./components/Profile.vue').default },
    { path: '/users', component: require('./components/Users.vue').default },
    { path: '/clients', component: require('./components/client/Clients.vue').default },
    { path: '/movies', component: require('./components/movie/Movies.vue').default },
    { path: '/rentals', component: require('./components/rental/Rentals.vue').default },
    { path: '*', component: require('./components/NotFound.vue').default }
];
