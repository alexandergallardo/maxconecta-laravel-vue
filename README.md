
# Laravel + Vue + AdminLTE 3

## About Repository

A very simple Laravel 8 + Vue 2 + AdminLTE 3 based template for SPA Application.

## Tech Specification

- Laravel 8
- Vue 2 + VueRouter + vue-progressbar + sweetalert2 + laravel-vue-pagination
- Laravel Passport
- Admin LTE 3 + Bootstrap 4 + Font Awesome 5
- PHPUnit Test Case/Test Coverage

## Features

- Modal based Create+Edit, List with Pagination, Delete with Sweetalert
- Login
- Client Management
- Movie Management
- Rental Management
- User Management
- Frontend and Backend User ACL with Gate Policy (type: Administradot/Encargado)
- Simple Static Dashboard
- Developer Options for OAuth Clients and Personal Access Token

## Installation

- git clone https://github.com/alexandergallardo/maxconecta-laravel-vue.git
- cd maxconecta-laravel-vue/
- composer install
- cp .env.example .env
- Update `.env` and set your database credentials
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- php artisan passport:install
- npm install
- npm run dev
- php artisan serve

#### Url:  http://localhost:8000
```bash
Credenciales Administrador
User : admin@admin.com
Password:  password
```
```bash
Credenciales Encargado
User : encargado@admin.com
Password:  password
```

## Endpoint

#### Users
```bash
GET 
	http://localhost:8000/api/user
	http://localhost:8000/api/user/{id}
DELETE 
	http://localhost:8000/api/user/{id}
POST 
	http://localhost:8000/api/user
	Body
	{
		"name" : "name",
		"username" : "username",
		"type" : "role",
		"email" : "email",
		"password" : "password"
	}
PUT 
	http://localhost:8000/api/user/{id}
	Body
	{
		"name" : "name",
		"username" : "username",
		"type" : "role",
		"email" : "email",
		"password" : ""   (Si no se actualizara el password, no se envia o se envia vacio
	}
```

#### Clients
```bash
GET 
	http://localhost:8000/api/client
	http://localhost:8000/api/client/{id}
DELETE 
	http://localhost:8000/api/client/{id}
POST 
	http://localhost:8000/api/client
	Body
	{
		"name" : "",
		"lastname" : "",
		"identification" : "",
		"description" : ""
	}
PUT 
	http://localhost:8000/api/client/{id}
	Body
	{
		"name" : "",
		"lastname" : "",
		"identification" : "",
		"description" : ""
	}
```

#### Movies
```bash
GET 
	http://localhost:8000/api/movie
	http://localhost:8000/api/movie/{id}
DELETE 
	http://localhost:8000/api/movie/{id}
POST 
	http://localhost:8000/api/movie
	Body
	{
		"title" : "",
		"category" : "",
		"description" : "",
		"year" : 2022,
		"stock" : 1
	}
PUT 
	http://localhost:8000/api/movie/{id}
	Body
	{
		"title" : "",
		"category" : "",
		"description" : "",
		"year" : 2022,
		"stock" : 1
	}
```

#### Rentals
```bash
GET 
	http://localhost:8000/api/rental

	http://localhost:8000/api/rental/{id}
DELETE 
	http://localhost:8000/api/rental/{id}
POST 
	http://localhost:8000/api/rental
	Body
	{
		"delivery" : "2022-11-11",
		"client_id" : 0,
		"movie_id" : 0,
		"description" : ""
	}
PUT 
	http://localhost:8000/api/rental/{id}
	Body
	{
		"entry" : "2022-11-11",
		"delivery" : "2022-11-11",
		"client_id" : 0,
		"movie_id" : 0,
		"description" : ""
	}
```
