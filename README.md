# Laravel Virtual Account API

This is a Laravel-based API that allows users to:

- Register an account
- Login and receive an authentication token
- View and update their profile
- Create a virtual account via SafeHaven API
- Handle SafeHaven webhook events

## System Requirements

Ensure your system meets the following requirements:

- **PHP 8.2.2** or higher
- **Laravel 10**
- **Composer**
- **Docker & Docker Compose**
- **MySQL 8.0**
- **Postman or cURL** (for API testing)

## Installation Guide

Follow these steps to set up the project:

1. **Clone the repository**
   ```sh
   git clone https://github.com/your-repo-name.git
   cd your-repo-name
   ```

2. **Set up environment variables**
   ```sh
   cp .env.example .env
   ```
   - Update `.env` with database credentials.

3. **Build and start Docker containers**
   ```sh
   docker-compose up --build -d
   ```

4. **Run migrations**
   ```sh
   docker exec -it laravel_app php artisan migrate
   ```

5. **Generate an application key**
   ```sh
   docker exec -it laravel_app php artisan key:generate
   ```

6. **Start the Laravel application**
   ```sh
   docker exec -it laravel_app php artisan serve --host=0.0.0.0 --port=8000
   ```

## API Endpoints

### 1️⃣ User Registration
**Endpoint:** `POST /api/auth/register`
**Payload:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "johndoe@example.com",
  "password": "SecurePassword123"
}
```
**Response:**
```json
{
  "message": "User registered successfully",
  "token": "your-access-token"
}
```

### 2️⃣ User Login
**Endpoint:** `POST /api/auth/login`
**Payload:**
```json
{
  "email": "johndoe@example.com",
  "password": "SecurePassword123"
}
```
**Response:**
```json
{
  "message": "Login successful",
  "token": "your-access-token"
}
```

### 3️⃣ View User Profile
**Endpoint:** `GET /api/auth/profile`
**Headers:**
```json
{
  "Authorization": "Bearer your-access-token"
}
```
**Response:**
```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "johndoe@example.com"
}
```

### 4️⃣ Update User Profile
**Endpoint:** `PUT /api/auth/profile`
**Headers:**
```json
{
  "Authorization": "Bearer your-access-token"
}
```
**Payload:**
```json
{
  "first_name": "John",
  "last_name": "UpdatedDoe"
}
```
**Response:**
```json
{
  "message": "Profile updated successfully"
}
```

### 5️⃣ Create Virtual Account
**Endpoint:** `POST /api/virtual-account`
**Headers:**
```json
{
  "Authorization": "Bearer your-access-token"
}
```
**Response:**
```json
{
  "message": "Virtual account created successfully",
  "virtualAccountNumber": "1234567890"
}
```

### 6️⃣ SafeHaven Webhook Handling
**Endpoint:** `POST /api/webhooks/safehaven`
**Payload:**
```json
{
  "event": "transaction_successful",
  "data": {
    "accountNumber": "1234567890",
    "amount": 10000,
    "transactionId": "txn_123456"
  }
}
```
**Response:**
```json
{
  "message": "Webhook processed successfully"
}
```

## Validation Implementation

- **Registration & Login:** Ensures required fields are provided and password security checks are enforced.
- **Profile Updates:** Ensures fields are not left empty and only valid values are accepted.
- **Virtual Account Creation:** Checks if user already has an account before sending the request.
- **Webhook Handling:** Logs incoming requests and verifies required fields.

## Debugging & Logging

If you encounter issues, check logs with:
```sh
docker exec -it laravel_app tail -f storage/logs/laravel.log
```
Or inspect specific containers:
```sh
docker logs laravel_app
```

## Deployment Guide

To publish your Dockerized Laravel application:

1. **Build the Docker image**
   ```sh
   docker build -t your-app-name .
   ```
2. **Push the image to Docker Hub**
   ```sh
   docker tag your-app-name your-dockerhub-username/your-app-name
   docker push your-dockerhub-username/your-app-name
   ```
3. **Deploy on a cloud server (e.g., AWS, DigitalOcean, etc.)**
   ```sh
   docker run -d -p 80:8000 your-dockerhub-username/your-app-name
   ```

---

## 🔗 Repository Link
https://github.com/heasypharbs/xpay.git

## 🔗 Demonstration Link
https://youtu.be/L9-bLY4Tvuw