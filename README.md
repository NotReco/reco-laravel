# RecoDB 🎬

Nền tảng Thông tin, Đánh giá và Đề xuất Điện ảnh.

## Tính năng nổi bật ✨

- **Kho dữ liệu khổng lồ:** Tự động lấy (cào) dữ liệu Phim, Series, Diễn viên từ [TMDb API](https://developer.themoviedb.org/docs).
- **Cộng đồng điện ảnh:** Đánh giá phim (Review), bình luận, tạo danh sách yêu thích (Watchlist) và tham gia diễn đàn thảo luận.
- **Hệ thống Gamification:** Thực hiện nhiệm vụ (Quests), kiếm điểm kinh nghiệm, mở khóa Danh hiệu (Titles) và Khung viền Avatar.
- **Giao diện hiện đại (UI/UX):** Giao diện Dark mode tối giản sang trọng. Thao tác mượt mà tức thời nhờ AJAX và Smart Modals (không cần load lại trang).
- **Phân quyền chặt chẽ:** Tích hợp Control Panel và Admin Panel chuyên nghiệp. Hệ thống quản trị chia cấp độ (Super Admin, Admin, Mod) bằng Spatie Permissions.

## Công nghệ sử dụng 🛠

- **Backend:** Laravel 12.x (PHP 8.x)
- **Frontend:** Tailwind CSS, Alpine.js, Blade Components
- **Database:** MySQL
- **Integrations & Packages:** TMDb API (The Movie Database), Spatie Permission (Quản lý phân quyền)

## Cài đặt (Local Development) 🚀

**1. Clone dự án**

```bash
git clone https://github.com/your-username/reco-laravel.git
cd reco-laravel
```

**2. Cài đặt thư viện**

```bash
composer install
npm install
```

**3. Cấu hình môi trường**

```bash
cp .env.example .env
php artisan key:generate
```

_Lưu ý: Bạn cần đăng ký và thêm `TMDB_API_KEY` vào file `.env` để tính năng tự động thêm phim hoạt động._

**4. Khởi tạo Database**

```bash
php artisan migrate --seed
```

**5. Khởi động Server**
Mở 2 terminal song song và chạy:

```bash
npm run dev
```

```bash
php artisan serve
```

Truy cập: `http://localhost:8000`
