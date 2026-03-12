# RecoDB — Phân Tích Project & Kế Hoạch Triển Khai

> Tài liệu đánh giá project hiện tại và lập kế hoạch triển khai RecoDB theo Blueprint.

---

## PHẦN 1 — PHÂN TÍCH PROJECT HIỆN TẠI

### 1.1 Tổng quan

| Hạng mục | Số lượng | Chất lượng |
|----------|---------|------------|
| **Models** | 12 files | ⭐⭐⭐⭐⭐ Xuất sắc |
| **Controllers** | 5 public + 10 Auth | ⭐⭐⭐ Tốt nhưng thiếu nhiều |
| **Services** | 1 (TmdbService) | ⭐⭐⭐⭐⭐ Xuất sắc |
| **Middleware** | 1 (CheckRole) | ⭐⭐⭐⭐⭐ Xuất sắc |
| **Enums/Traits** | 2 (UserRole + HasSlug) | ⭐⭐⭐⭐⭐ Xuất sắc |
| **Migrations** | 14 files | ⭐⭐⭐⭐⭐ Hoàn chỉnh |
| **Seeders** | 4 files | ⭐⭐⭐⭐ Tốt |
| **Views** | ~20 files | ⭐⭐⭐ Có nền tảng, cần mở rộng nhiều |
| **Config** | tmdb.php | ⭐⭐⭐⭐⭐ Xuất sắc |
| **Routes** | 34 lines web.php | ⭐⭐ Rất sơ khai |

---

### 1.2 ✅ Những phần NÊN GIỮ NGUYÊN (không cần sửa)

Backend foundation rất chắc chắn, giữ nguyên 100%:

| File/Module | Lý do giữ nguyên |
|-------------|------------------|
| `app/Models/*` (12 files) | Well-structured: relationships, scopes, casts, helpers đầy đủ. `Movie` có `recalculateRating()`, `Review` có `published()`/`fullReview()`/`quickRating()` scopes, `User` có comprehensive role system + 2FA |
| `app/Enums/UserRole.php` | Hierarchical role system hoàn chỉnh: `level()`, `isAtLeast()`, `label()`, `color()` |
| `app/Traits/HasSlug.php` | Auto-slug generation with uniqueness check, route model binding, update detection |
| `app/Http/Middleware/CheckRole.php` | Hierarchical role checking with 'staff' alias |
| `app/Services/TmdbService.php` | Comprehensive: retry logic, fallback language, image URL helpers, 9 API methods |
| `config/tmdb.php` | Complete config: image sizes, language settings |
| `app/Http/Controllers/Auth/*` (10 files) | Full Breeze auth + 2FA — hoạt động tốt |
| `routes/auth.php` | Auth routes hoàn chỉnh |
| `app/Notifications/TwoFactorCode.php` | 2FA notification |
| `database/migrations/*` (14 files) | DB schema hoàn chỉnh, không cần thay đổi |
| `database/seeders/*` (4 files) | Test data seeding works well |
| `database/factories/UserFactory.php` | Standard factory |

---

### 1.3 🔧 Những phần NÊN REFACTOR (giữ + cải tiến)

| File/Module | Hiện trạng | Cần cải tiến |
|-------------|-----------|--------------|
| `HomeController.php` (71 lines) | Có hero, trending, nowPlaying, latestReviews, genres | Thêm: popular, upcoming, top rated, editor picks (theo blueprint 6 sections) |
| `MovieController.php` (99 lines) | Có index (search/filter/sort) + show (detail) | Đã đầy đủ logic, chỉ cần bổ sung view counting |
| `ReviewController.php` (51 lines) | Chỉ có `store()` | Cần thêm: `update()`, `destroy()`, `index()` và moderation workflow |
| `ProfileController.php` (61 lines) | Breeze mặc định (edit/update/destroy) | Cần rebuild: public profile, favorites, reviews history, follow stats |
| `routes/web.php` (34 lines) | Rất sơ khai: home, movies, review store, profile edit | Cần mở rộng đáng kể theo blueprint |
| `resources/views/home.blade.php` (414 lines) | Hero carousel + 3 sections + genre chips | Refactor thành components; thêm sections còn thiếu |
| `resources/views/movies/show.blade.php` (17KB) | Detail page có cast, trailer, reviews | Refactor UI; thêm dual rating, sidebar info, photo gallery |
| `resources/views/partials/navbar.blade.php` (25KB) | Navbar lớn, có search chức năng | Refactor: thêm live search dropdown, notifications bell |
| `resources/views/partials/footer.blade.php` (9KB) | Footer đầy đủ | Thêm conditional guest CTA |
| `resources/views/layouts/main.blade.php` (39 lines) | Layout clean: Vite + Google Fonts + navbar/footer | Tốt, chỉ cần thêm toast container |
| `resources/css/app.css` (113 lines) | Custom Tailwind: dark theme, btn, card, glass, animations | Mở rộng: thêm rating color classes |
| `tailwind.config.js` (50 lines) | Dark palette + accent colors + fonts | Thêm font Playfair Display, Montserrat; thêm breakpoints |
| `resources/views/components/movie-card.blade.php` | Card cơ bản | Cải thiện: thêm rank number, media type badge, hover actions |

---

### 1.4 🗑️ Những phần NÊN XÓA/THAY THẾ

| File/Module | Lý do xóa |
|-------------|-----------|
| `resources/views/welcome.blade.php` | Laravel default, không sử dụng |
| `resources/views/dashboard.blade.php` | Placeholder đơn giản, sẽ được thay bằng admin dashboard |
| `resources/views/layouts/guest.blade.php` | Breeze default, sẽ redesign auth pages theo split-panel |
| `resources/views/components/` (auth-session-status, danger-button, input-error, input-label, modal, primary-button, secondary-button, text-input) | Breeze defaults, sẽ thay bằng custom design system hoặc refactor cho phù hợp |
| `resources/views/profile/edit.blade.php` + `profile/partials/*` | Breeze default profile, sẽ rebuild hoàn toàn |
| `resources/views/movies/index.blade.php` | Cần rebuild thành Explore page theo blueprint |

---

## PHẦN 2 — QUYẾT ĐỊNH CHIẾN LƯỢC

### 🔰 Đề xuất: **Phương án B — Refactor / Tái chế**

#### Lý do:

```
┌──────────────────────────────────────────────────────────────────────┐
│  Backend (GIỮ NGUYÊN)          │  Frontend (CẢI TIẾN + BỔ SUNG)    │
│  ─────────────────────         │  ──────────────────────────────    │
│  ✅ 12 models hoàn chỉnh       │  🔧 Refactor views hiện có        │
│  ✅ TmdbService đầy đủ         │  🔧 Tách views thành components   │
│  ✅ Auth + 2FA hoạt động       │  ➕ Thêm views mới (profile,     │
│  ✅ Role system + middleware   │     explore, admin, forum, etc.)   │
│  ✅ 14 migrations OK           │  ➕ Thêm controllers mới          │
│  ✅ Seeders hoạt động          │  ➕ Thêm routes                   │
│  ✅ Config TMDB cấu hình tốt   │  🔧 Cải thiện CSS design system   │
└──────────────────────────────────────────────────────────────────────┘
```

**Tóm lại:**
- Backend chiếm ~60% công việc đã xong — xóa là lãng phí lớn
- Frontend cần 70% công việc mới — nhưng nền tảng (layout, design system, carousel) đã có
- Rủi ro rebuild > refactor vì database đang ổn định, relationships đã test qua seeder
- Refactor cho phép phát triển incremental — mỗi bước đều có kết quả chạy được

---

## PHẦN 3 — KẾ HOẠCH TRIỂN KHAI RECODB

### Bước 1 — Chuẩn hóa Project ⏱️ ~1-2 ngày

**Mục tiêu:** Dọn dẹp, sắp xếp lại cấu trúc, chuẩn bị nền tảng.

| Công việc | Chi tiết |
|-----------|----------|
| Xóa files thừa | `welcome.blade.php`, `dashboard.blade.php`, Breeze default components |
| Chuẩn hóa routes | Tổ chức `web.php` theo 4 groups: public / auth / verified / admin |
| Thêm admin layout | Tạo `layouts/admin.blade.php` với sidebar navigation |
| Thêm fonts | Import Playfair Display, Montserrat vào layout + tailwind config |
| Cập nhật CSS | Thêm rating color classes, thêm utility classes cho blueprint |
| Toast component | Tạo `components/toast.blade.php` cho flash messages |

---

### Bước 2 — Component System ⏱️ ~2-3 ngày

**Mục tiêu:** Tách UI thành reusable Blade components.

| Component mới | Props | Mô tả |
|---------------|-------|--------|
| `hero-carousel` | `$movies` | Tách từ home.blade.php, standalone component |
| `movie-section` | `$title`, `$subtitle`, `$items`, `$seeAllUrl` | Horizontal scroll section template |
| `review-card` | `$review` | Tách từ home.blade.php review section |
| `review-form` | `$movieId` | 10-star rating form với Alpine.js |
| `star-rating` | `$rating`, `$max` | Star display (full/half/empty) |
| `rating-ring` | `$score`, `$total`, `$label` | SVG circular progress |
| `genre-pills` | `$genres`, `$selectedId` | Clickable genre filter pills |
| `favorite-button` | `$movieId`, `$isFavorited` | AJAX toggle heart |
| `watchlist-button` | `$movieId`, `$isWatchlisted` | AJAX toggle bookmark |
| `trailer-modal` | `$youtubeUrl` | YouTube embed modal |
| `expandable-text` | `$text`, `$maxLength` | Show more/less |
| `spoiler-toggle` | `$content` | Blur/reveal spoiler |
| `person-card` | `$person`, `$role` | Actor/director card |
| `notification-dropdown` | — | Bell + dropdown |
| `search-dropdown` | — | Live search results |

---

### Bước 3 — Trang Nội Dung Chính ⏱️ ~3-4 ngày

| Trang | Controller action cần | View cần |
|-------|----------------------|----------|
| **Homepage** | Refactor `HomeController@index` — thêm popular, upcoming, topRated, editorPicks | Refactor `home.blade.php` — dùng `movie-section` component cho mỗi section |
| **Explore** | Refactor `MovieController@index` | Rebuild `movies/index.blade.php` → `explore.blade.php` với genre pills + grid |
| **Movie Detail** | Giữ `MovieController@show`, thêm view counting | Refactor `movies/show.blade.php` — thêm dual rating, photo gallery, sidebar |
| **Person Detail** | **Mới:** `PersonController@show` | **Mới:** `person/show.blade.php` — bio, filmography |
| **Search** | Thêm `SearchController@index` (AJAX) | **Mới:** `search-dropdown` component + explore full-page |

---

### Bước 4 — Hệ thống Review ⏱️ ~2-3 ngày

| Công việc | Chi tiết |
|-----------|----------|
| Mở rộng `ReviewController` | Thêm `update()`, `destroy()`, `index()` |
| Review Form component | 10-star với color-coded feedback, character counter, spoiler checkbox |
| Review Card component | Avatar + score badge (color-coded) + content + spoiler toggle |
| Review moderation | Views cho admin: danh sách pending, approve/reject actions |
| Like review (AJAX) | Route + controller method cho like/unlike toggle |
| Comment system | `CommentController` — CRUD + threaded replies + AJAX load |

---

### Bước 5 — User Features ⏱️ ~3-4 ngày

| Công việc | Chi tiết |
|-----------|----------|
| **Profile page** | Rebuild `ProfileController` — show profile với banner, avatar, stats, tabs |
| **Public profile** | Route `/@{username}` hoặc `/user/{user}` — read-only view |
| **Favorites** | `FavoriteController` — toggle + "My List" tab |
| **Watchlist** | `WatchlistController` — CRUD với status (want_to_watch, watching, watched) |
| **My List page** | Tab switcher: Favorites vs Watchlist |
| **Follow system** | `FollowController` — follow/unfollow + counts |
| **Notifications** | `NotificationController` — CRUD + bell dropdown + mark read |
| **Settings** | `SettingsController` — change password, preferences |

---

### Bước 6 — Community Features ⏱️ ~2-3 ngày

| Công việc | Chi tiết |
|-----------|----------|
| **Forum** | `ForumController` — categories + threads + replies |
| **Direct Messages** | `MessageController` — user-to-user messaging |
| **Activity Feed** | Show recent activity of followed users |

---

### Bước 7 — Admin Panel ⏱️ ~3-4 ngày

| Trang | Controller | Chức năng |
|-------|-----------|-----------|
| **Dashboard** | `Admin\DashboardController` | Stats widgets, charts, recent activity |
| **Movies** | `Admin\MovieController` | CRUD phim, TMDB sync |
| **Reviews** | `Admin\ReviewController` | Moderation: approve/reject/hide |
| **Users** | `Admin\UserController` | List, edit role, ban/unban |
| **Categories** | `Admin\CategoryController` | Forum categories CRUD |
| **Reports** | `Admin\ReportController` | Report queue + resolution |

---

## PHẦN 4 — KIẾN TRÚC LARAVEL ĐỀ XUẤT

### 4.1 Controllers

```
app/Http/Controllers/
├── HomeController.php          ← Homepage data
├── MovieController.php         ← Explore + Movie detail
├── PersonController.php        ← Person detail + filmography [MỚI]
├── ReviewController.php        ← Review CRUD [MỞ RỘNG]
├── CommentController.php       ← Comment CRUD [MỚI]
├── ProfileController.php       ← Profile show/edit [REBUILD]
├── FavoriteController.php      ← Favorite toggle [MỚI]
├── WatchlistController.php     ← Watchlist CRUD [MỚI]
├── FollowController.php        ← Follow/unfollow [MỚI]
├── NotificationController.php  ← Notifications [MỚI]
├── SearchController.php        ← Live search API [MỚI]
├── ForumController.php         ← Forum threads [MỚI]
├── MessageController.php       ← Direct messages [MỚI]
├── Auth/                       ← 10 auth controllers [GIỮ NGUYÊN]
└── Admin/                      ← Admin namespace [MỚI]
    ├── DashboardController.php
    ├── MovieController.php
    ├── ReviewController.php
    ├── UserController.php
    ├── CategoryController.php
    └── ReportController.php
```

### 4.2 Models

```
app/Models/           ← GIỮ NGUYÊN TẤT CẢ
├── Movie.php         (139 lines)
├── Person.php        (63 lines)
├── Genre.php         (23 lines)
├── Review.php        (109 lines)
├── Comment.php       (59 lines)
├── Like.php          (24 lines)
├── CommentLike.php   (?)
├── Follow.php        (24 lines)
├── Watchlist.php     (41 lines)
├── Tag.php           (35 lines)
├── ChatMessage.php   (36 lines)
└── User.php          (218 lines)
```

### 4.3 Services

```
app/Services/
├── TmdbService.php           ← GIỮ NGUYÊN
├── MovieService.php          ← [MỚI] Business logic tách từ controllers
├── ReviewService.php         ← [MỚI] Review moderation workflow
└── NotificationService.php   ← [MỚI] Notification dispatch logic
```

### 4.4 Blade Components

```
resources/views/
├── layouts/
│   ├── main.blade.php         ← GIỮ + mở rộng (thêm toast, Playfair font)
│   └── admin.blade.php        ← MỚI
├── components/
│   ├── hero-carousel.blade.php   ← MỚI (tách từ home)
│   ├── movie-section.blade.php   ← MỚI
│   ├── movie-card.blade.php      ← REFACTOR
│   ├── review-card.blade.php     ← MỚI
│   ├── review-form.blade.php     ← MỚI
│   ├── person-card.blade.php     ← MỚI
│   ├── star-rating.blade.php     ← MỚI
│   ├── rating-ring.blade.php     ← MỚI
│   ├── genre-pills.blade.php     ← MỚI
│   ├── favorite-button.blade.php ← MỚI
│   ├── watchlist-button.blade.php← MỚI
│   ├── trailer-modal.blade.php   ← MỚI (tách từ home)
│   ├── notification-dropdown.blade.php ← MỚI
│   ├── search-dropdown.blade.php ← MỚI
│   ├── expandable-text.blade.php ← MỚI
│   ├── spoiler-toggle.blade.php  ← MỚI
│   ├── toast.blade.php           ← MỚI
│   └── user-avatar.blade.php    ← MỚI
├── partials/
│   ├── navbar.blade.php          ← REFACTOR
│   └── footer.blade.php         ← REFACTOR
├── home.blade.php                ← REFACTOR (dùng components)
├── explore.blade.php             ← MỚI (thay movies/index)
├── movies/
│   └── show.blade.php            ← REFACTOR
├── person/
│   └── show.blade.php            ← MỚI
├── profile/
│   ├── show.blade.php            ← MỚI
│   ├── edit.blade.php            ← REBUILD
│   └── my-list.blade.php        ← MỚI
├── auth/                         ← REDESIGN (split-panel)
├── forum/                        ← MỚI
│   ├── index.blade.php
│   └── show.blade.php
├── messages/
│   └── index.blade.php           ← MỚI
└── admin/                        ← MỚI
    ├── dashboard.blade.php
    ├── movies/
    ├── reviews/
    ├── users/
    ├── categories/
    └── reports/
```

### 4.5 Routes

```
routes/web.php — Tổ chức theo 4 groups:

// ═══ GROUP 1: PUBLIC ═══
GET  /                        → HomeController@index
GET  /explore                 → MovieController@index
GET  /movies/{movie}          → MovieController@show
GET  /person/{person}         → PersonController@show
GET  /api/search              → SearchController@search  (AJAX)
GET  /forum                   → ForumController@index
GET  /forum/{thread}          → ForumController@show

// ═══ GROUP 2: AUTH (guest middleware) ═══
→ routes/auth.php (GIỮ NGUYÊN)

// ═══ GROUP 3: AUTH + VERIFIED ═══
POST   /movies/{movie}/review   → ReviewController@store
PUT    /reviews/{review}        → ReviewController@update
DELETE /reviews/{review}        → ReviewController@destroy
POST   /api/favorites/toggle    → FavoriteController@toggle
POST   /api/watchlist/toggle    → WatchlistController@toggle
POST   /api/likes/toggle        → LikeController@toggle
POST   /api/follow/toggle       → FollowController@toggle
POST   /comments                → CommentController@store
GET    /profile                 → ProfileController@show
GET    /profile/edit            → ProfileController@edit
PATCH  /profile                 → ProfileController@update
GET    /my-list                 → WatchlistController@myList
GET    /notifications           → NotificationController@index
GET    /messages                → MessageController@index
GET    /settings                → SettingsController@index
GET    /@{username}             → ProfileController@public

// ═══ GROUP 4: AUTH + ADMIN ═══
GET  /admin                    → Admin\DashboardController@index
... (CRUD routes cho movies, reviews, users, categories, reports)
```

### 4.6 Assets

```
resources/
├── css/
│   └── app.css        ← GIỮ + mở rộng design system
├── js/
│   └── app.js         ← Thêm Alpine.js plugins nếu cần
public/
├── images/            ← Logo, favicons
└── build/             ← Vite compiled assets
```

---

## PHẦN 5 — DANH SÁCH TASK THỰC TẾ

### Phase 1: MVP (Bước 1-4)

**Chuẩn hóa:**
- [ ] Xóa `welcome.blade.php`, `dashboard.blade.php`
- [ ] Xóa Breeze default components (giữ lại `movie-card`)
- [ ] Cập nhật `tailwind.config.js` — thêm Playfair Display, Montserrat
- [ ] Cập nhật `resources/css/app.css` — thêm rating color classes
- [ ] Tổ chức lại `routes/web.php` theo 4 groups
- [ ] Thêm toast component vào layout

**Components:**
- [ ] Tạo `components/hero-carousel.blade.php` (tách từ home)
- [ ] Tạo `components/movie-section.blade.php`
- [ ] Refactor `components/movie-card.blade.php` (thêm rank, badge)
- [ ] Tạo `components/review-card.blade.php`
- [ ] Tạo `components/review-form.blade.php` (10-star, Alpine.js)
- [ ] Tạo `components/star-rating.blade.php`
- [ ] Tạo `components/genre-pills.blade.php`
- [ ] Tạo `components/trailer-modal.blade.php` (tách từ home)
- [ ] Tạo `components/expandable-text.blade.php`
- [ ] Tạo `components/toast.blade.php`

**Trang chính:**
- [ ] Refactor `HomeController@index` — thêm 3 sections còn thiếu
- [ ] Refactor `home.blade.php` — sử dụng components
- [ ] Rebuild `movies/index.blade.php` → `explore.blade.php`
- [ ] Refactor `movies/show.blade.php` — thêm dual rating, sidebar
- [ ] Tạo `PersonController@show`
- [ ] Tạo `person/show.blade.php`
- [ ] Refactor `partials/navbar.blade.php` — thêm live search dropdown
- [ ] Refactor `partials/footer.blade.php` — thêm guest CTA

**Review system:**
- [ ] Mở rộng `ReviewController` (update, destroy)
- [ ] Hoàn thiện review-form component
- [ ] Tạo `CommentController` + routes
- [ ] Tạo comment views (threaded)
- [ ] Tạo like toggle route + controller

### Phase 2: User Features (Bước 5)

- [ ] Rebuild `ProfileController` — show + public
- [ ] Tạo `profile/show.blade.php` (banner, avatar, stats, tabs)
- [ ] Route `/@{username}` cho public profile
- [ ] Tạo `FavoriteController` + toggle route
- [ ] Tạo `components/favorite-button.blade.php`
- [ ] Tạo `WatchlistController` + toggle route + my-list page
- [ ] Tạo `components/watchlist-button.blade.php`
- [ ] Tạo `FollowController` + follow/unfollow
- [ ] Tạo `NotificationController` + bell dropdown
- [ ] Tạo `components/notification-dropdown.blade.php`
- [ ] Tạo `SettingsController` + settings view

### Phase 3: Community + Admin (Bước 6-7)

- [ ] Tạo `ForumController` + views (index, show)
- [ ] Tạo `MessageController` + messaging views
- [ ] Tạo `layouts/admin.blade.php`
- [ ] Tạo `Admin\DashboardController` + dashboard view
- [ ] Tạo `Admin\MovieController` + CRUD views
- [ ] Tạo `Admin\ReviewController` + moderation views
- [ ] Tạo `Admin\UserController` + management views
- [ ] Tạo `Admin\ReportController` + report views
- [ ] Redesign auth pages (login/register split-panel)

---

## PHẦN 6 — RỦI RO VÀ LƯU Ý

### 6.1 Lỗi kiến trúc cần tránh

| Rủi ro | Biện pháp phòng tránh |
|--------|----------------------|
| **Fat Controllers** | Tạo Service classes (`MovieService`, `ReviewService`) cho business logic phức tạp |
| **N+1 Query** | Luôn dùng `with()` eager loading cho relationships. Dùng `withCount()`, `withAvg()` |
| **Hard-coded strings** | Dùng constants/enums cho status values (review status, watchlist status) |
| **Inline validation** | Dùng Form Request classes thay vì validate trong controller |
| **Duplicated views** | Tách thành Blade components — không copy-paste HTML |
| **Missing auth checks** | Dùng middleware groups + Policy classes cho authorization |

### 6.2 Code Smell cần tránh

- ❌ **Không đặt logic trong views** — Blade chỉ hiển thị, không xử lý data
- ❌ **Không gọi API TMDB trong views** — Luôn qua `TmdbService` trong controllers
- ❌ **Không hard-code TMDB image URLs** — Dùng `TmdbService@posterUrl()`, `backdropUrl()`
- ❌ **Không inline CSS** — Dùng Tailwind classes hoặc `@layer components` trong CSS
- ❌ **Không inline JavaScript dài** — Tách Alpine.js logic phức tạp ra file JS riêng

### 6.3 Giữ Project dễ bảo trì

- ✅ **Naming convention nhất quán** — `snake_case` routes, `PascalCase` controllers, `kebab-case` views
- ✅ **Component-first** — Mỗi element UI nhỏ nhất cũng nên là Blade component
- ✅ **Comment code Vietnamese** — Giữ comments tiếng Việt cho phù hợp context chuyên đề
- ✅ **Git commit thường xuyên** — Mỗi bước hoàn thành = 1 commit có ý nghĩa
- ✅ **Test seeders sau mỗi migration** — Đảm bảo `php artisan migrate:fresh --seed` luôn hoạt động

### 6.4 Performance

- ✅ Cache TMDB API responses (`Cache::remember()`)
- ✅ Lazy loading images (`loading="lazy"`)
- ✅ Paginate lists (20 items/page)
- ✅ AJAX cho interactive elements (like, favorite, watchlist, search)
- ✅ Vite compiled assets (không CDN cho production)

---

## KẾT LUẬN

### Chiến lược: **Refactor + Mở rộng** (Phương án B)

Project hiện tại có **nền tảng backend ~60% hoàn chỉnh** và **frontend ~25% hoàn chỉnh**. Rebuild từ đầu sẽ lãng phí 12 models, TmdbService, auth system, role system, migrations, và seeders đều đã test và hoạt động.

### Ưu tiên triển khai:

1. **Phase 1 (MVP):** Components → Homepage → Explore → Movie Detail → Review System
2. **Phase 2:** Profile → Favorites → Watchlist → Follow → Notifications
3. **Phase 3:** Forum → Messages → Admin Panel → Auth Redesign

Mỗi phase đều cho ra sản phẩm chạy được, có thể demo cho chuyên đề thực tập.
