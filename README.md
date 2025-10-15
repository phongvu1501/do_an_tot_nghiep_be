# 🍽️ Laravel Restaurant — Hệ thống đặt bàn & quản lý nhà hàng

## 🧱 1. Yêu cầu hệ thống

Trước khi bắt đầu, hãy đảm bảo bạn đã cài đặt:

- PHP >= 8.2  
- Composer  
- MySQL 
- Git

---

## 🚀 2. Cài đặt dự án lần đầu

### 📥 Bước 1: Clone dự án
```bash
git clone https://github.com/phongvu1501/do_an_tot_nghiep_be.git
cd do_an_tot_nghiep_be
```

### ⚙️ Bước 2: Cài đặt thư viện PHP
```
composer install
```

### 🧩 Bước 3: Tạo file môi trường .env
```
cp .env.example .env
```


Sau đó chỉnh lại thông tin database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ten_database
DB_USERNAME=root
DB_PASSWORD=
```

### 🔑 Bước 4: Tạo key cho ứng dụng
```
php artisan key:generate
```

### 🧰 Bước 5: Chạy migration và seed dữ liệu mẫu
```
php artisan migrate --seed
```

Lệnh này sẽ tạo toàn bộ bảng và chạy các seeder mẫu.

### 🧑‍💻 Bước 6: Chạy server
```
php artisan serve
```

Server chạy tại:
👉 http://127.0.0.1:8000


## 🔁 3. Khi kéo code mới (git pull) về

Mỗi lần có code mới, hãy chạy:

### 1️⃣ Lấy code mới nhất
```
git pull origin develop
```

### 2️⃣ Cập nhật thư viện PHP
```
composer install
```

### 3️⃣ Cập nhật cấu trúc DB (nếu có)
```
php artisan migrate
```

### 4️⃣ Seed lại dữ liệu (nếu có thay đổi)
```
php artisan db:seed
```

### 5️⃣ Xoá cache để tránh lỗi
```
php artisan optimize:clear
```
