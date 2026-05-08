# RecoDB 🎬

Nền tảng Thông tin, Đánh giá và Đề xuất Điện ảnh.

## Tính năng nổi bật ✨

- **Kho dữ liệu khổng lồ:** Tự động lấy dữ liệu phim lẻ, phim bộ, diễn viên từ [TMDb API](https://developer.themoviedb.org/docs).
- **Cộng đồng điện ảnh:** Đánh giá phim, bình luận, tạo danh sách yêu thích và tham gia diễn đàn thảo luận.
- **Hệ thống Gamification:** Thực hiện nhiệm vụ, kiếm điểm kinh nghiệm, mở khóa danh hiệu và khung viền ảnh đại diện.
- **Giao diện hiện đại:** Thiết kế tối giản sang trọng. Thao tác mượt mà tức thời nhờ AJAX và Smart Modals.
- **Phân quyền chặt chẽ:** Tích hợp Control Panel và Admin Panel. Hệ thống quản trị chia cấp độ: Super Admin, Admin, Moderator.

## Công nghệ sử dụng 🛠

- **Backend:** Laravel 12.x, PHP 8.x
- **Frontend:** Tailwind CSS, Alpine.js, Blade Components
- **Database:** MySQL
- **Tích hợp:** TMDb API, Spatie Permission

## Cài đặt 🚀

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

> Lưu ý: Cần đăng ký và thêm `TMDB_API_KEY` vào file `.env` để tính năng tự động thêm phim hoạt động.

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
