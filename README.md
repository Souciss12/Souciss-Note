# SoucissNote

SoucissNote is an intuitive web note-taking application with Markdown.

## Dev

### 1. Copy .env file

```bash
cp .env.example .env
```

### 2. Download dependencies

```bash
composer install
```

### 3. Generate key

```bash
php artisan key:generate
```

### 4. Downaload javascript dependencies

```bash
npm install
```

### 5. Create database

```bash
php artisan migrate
```

### 6. Launch server

First terminal :

```bash
php artisan serve
```

Second terminal :

```bash
npm run dev
```

Go **http://127.0.0.1:8000**
