# 🎬 AzuriDB — Product Design & Feature Analysis

> **Purpose**: Extract reusable design patterns, UI ideas, and feature concepts from the AzuriDB movie review website project for adaptation in a Laravel-based movie review portal.

---

## 1. Website Page Structure

The site has **31 pages** across **24 route groups**. Below is the full sitemap with purpose:

### Core Content Pages

| Page | Route | Purpose |
|------|-------|---------|
| **Home** | `/` | Hero carousel (5 trending films), 6 movie sections (trending, popular movies, popular TV, upcoming, top rated, editor picks), forum threads preview |
| **Movie Detail** | `/movies/[id]` | Cinematic hero banner, dual rating system (local + TMDB), cast grid, photo gallery, trailer, reviews, sidebar info |
| **TV Detail** | `/tv/[id]` | Identical layout to Movie Detail but adds seasons carousel, episode counts, broadcast network info |
| **Explore** | `/explore` | Search + genre filter pills + paginated movie/TV/person grid |
| **Person Detail** | `/person/[id]` | Actor/director profile with biography, personal info sidebar, filmography credits |

### User & Social Pages

| Page | Route | Purpose |
|------|-------|---------|
| **Login / Register** | `/login`, `/register` | Split-panel auth with tabs, password strength indicator, terms agreement |
| **My Profile** | `/profile` | Banner, avatar, badges, skills, stats, favorites grid, review history |
| **Public Profile** | `/@username` | Same layout as My Profile but read-only for visitors |
| **User Profile** | `/user/[id]` | Alternative user view by numeric ID |
| **My List** | `/my-list` | Tabbed interface: ❤️ Favorites vs 📝 Watchlist, with movies & TV separated |
| **Settings** | `/settings` | Account settings (handled by client component) |
| **Messages** | `/messages` | Direct messaging system between users |

### Community Pages

| Page | Route | Purpose |
|------|-------|---------|
| **Forum Index** | `/forum` | Community discussion board |
| **Forum Thread** | `/forum/[id]` | Individual thread with replies |

### Admin Panel (7 pages)

| Page | Route | Purpose |
|------|-------|---------|
| **Dashboard** | `/admin` | Stats overview (users, reviews, avg score, forum activity, pending reports), recent reviews list |
| **Movies** | `/admin/movies` | Movie management |
| **Reviews** | `/admin/reviews` | Review moderation |
| **Users** | `/admin/users` | User management with edit modal |
| **Categories** | `/admin/categories` | Forum category management |
| **Reports** | `/admin/reports` | User report handling |
| **Moderation** | `/admin/moderation` | Content moderation tools |

### Static Pages
`/about`, `/blog`, `/careers`, `/contact`, `/support`, `/partners`, `/terms`, `/privacy`, `/cookie`

---

## 2. UI / Design Patterns

### 2.1 Typography System
Uses **4 Google Fonts** mapped to CSS variables:

| Variable | Font | Usage |
|----------|------|-------|
| `--font-playfair` | Playfair Display (serif) | Page titles, hero text, headings |
| `--font-dm-sans` | Outfit (sans) | Body text, general UI |
| `--font-montserrat` | Montserrat | Bold accents, section titles |
| `--font-inter` | Inter | UI elements, forms |

> **💡 Laravel Idea**: Define these in `resources/css/app.css` and use `font-family: var(--font-playfair)` throughout Blade templates.

### 2.2 Navbar ([Navbar.tsx](file:///d:/xampp/htdocs/azuridb/components/Navbar.tsx))
- **Scroll-reactive**: Adds `nav-scrolled` class when `scrollY > 20` (glass morphism / backdrop blur)
- **Live search**: Debounced 200ms AJAX search with instant dropdown showing poster, title, year, rating, and media type badges
- **Enter key** redirects to full explore page with query
- **Conditional auth links**: Shows "Danh sách", "Tin nhắn", "Cài đặt" only when logged in
- **Admin link**: Visible only for `role === 'ADMIN'`
- **Right section**: Search bar → Cinema finder button → Messages icon → Notifications bell → Avatar → Logout
- **Mobile**: Hamburger toggle with overlay + slide-in side menu with icons

### 2.3 Hero Carousel ([HeroCarousel.tsx](file:///d:/xampp/htdocs/azuridb/components/HeroCarousel.tsx))
- **5-slide auto-advancing** carousel (5s interval), pauses when trailer is open
- **Full-bleed backdrop** images from TMDB with overlay gradient
- **Content layer**: Type badge (Phim Điện Ảnh / Phim Bộ), title, metadata (year, rating, vote count), rating score + popularity index, overview, and action buttons
- **Floating poster** beside the text content
- **Controls**: Prev/Next arrows, dot indicators, **thumbnail strip** at bottom showing poster previews
- **Progress bar**: Animated CSS bar synced to timer
- **Trailer Modal**: Opens YouTube embed in a cinematic portal modal with blurred backdrop, ambient glow effects, and movie info bar

### 2.4 Movie Cards ([MovieSection.tsx](file:///d:/xampp/htdocs/azuridb/components/MovieSection.tsx))
- **Horizontal scroll** layout with `scroll-snap-type: x mandatory`
- **5 items per page** with pagination dots (width animates from 8px to 24px when active)
- **Fade overlays** on left/right edges
- Each card shows:
  - Poster image
  - Hover overlay
  - **Rank number** (e.g., #1, #2)
  - **Star rating** badge
  - **Media type tag** (🎬 Điện Ảnh / 📺 Phim Bộ)
  - Title and release year

### 2.5 Movie Detail Page Layout
- **Cinematic hero**: Full-bleed backdrop with poster + info overlay
- **Badges row**: Certification, year, runtime
- **Genre pills**: Clickable links to `/explore?genre=`
- **Expandable text** for overviews (show more/less)
- **Action buttons**: Watch Trailer, Watchlist (📝), Favorite (❤️)
- **Dual rating cards** with SVG ring progress (AzuriDB community score vs TMDB score)
- **Content area** (2-column: main + sidebar):
  - Main: Director/Writer crew bar, Cast grid (12 actors with photo + character), Photo gallery (horizontal scroll), Review section
  - Sidebar: Tech specs table, Embedded trailer, Keywords tags, Share/external links (TMDB, IMDB)

### 2.6 Review System UI ([ReviewForm.tsx](file:///d:/xampp/htdocs/azuridb/components/ReviewForm.tsx))
- **10-star interactive rating** with hover preview
- **Color-coded stars** by score range:
  - 1-4 → Red `#E63946` ("Rất tệ" / "Tệ")
  - 5-6 → Orange `#d97b2a` ("Trung bình" / "Khá")
  - 7-8 → Teal `#2A9D8F` ("Tốt" / "Rất tốt")
  - 9-10 → Gold `#f5c518` ("Xuất sắc" / "Kiệt tác")
- **Score display**: X/10 with Vietnamese label
- **Textarea** with character counter (500 max)
- **Success/error feedback** messages
- **Login prompt** for guests: "Đăng nhập để viết đánh giá"
- **Review cards**: Avatar + colored score badge (high/mid/low class) + author + time ago + content

### 2.7 Authentication UI ([AuthPage.tsx](file:///d:/xampp/htdocs/azuridb/components/AuthPage.tsx))
- **Split-panel design**: Left panel = decorative background, Right panel = form
- **Tab switcher** between Login and Register
- **Password strength meter**: 4-segment bar (Weak → Medium → Good → Strong) with color feedback
- **Password confirmation** with real-time match indicator (✓ / ✕)
- **Show/hide password** toggle (eye icon)
- **Terms agreement** checkbox with links

### 2.8 Footer
- **Conditional CTA**: Shows "Bắt đầu ngay" registration banner **only for guests**
- **4-column links**: Suggestions, Resources, Company, Legal
- **Watermark logo** at bottom

### 2.9 Notification System ([NotificationDropdown.tsx](file:///d:/xampp/htdocs/azuridb/components/NotificationDropdown.tsx))
- **Bell icon** with unread count badge (max "9+")
- **Dropdown** with notification types: REPLY, MENTION, MESSAGE, BADGE, SYSTEM
- **Mark all as read** / mark individual
- **Auto-refresh** every 30 seconds
- **Time ago** formatting (vừa xong, Xp, Xh, Xd)

---

## 3. Feature Breakdown

### 3.1 Core Features

| Feature | Details |
|---------|---------|
| **TMDB Integration** | Fetches trending, popular, upcoming, top-rated content from TMDB API. Displays poster, backdrop, cast, crew, photos, trailer, keywords |
| **Dual Rating System** | Community ratings (stored locally) + TMDB ratings displayed side-by-side with SVG ring progress |
| **User Reviews** | 10-point scale with content text, color-coded by quality tier |
| **Favorites** | Toggle ❤️ on movie/TV detail pages, persisted to user profile as JSON array |
| **Watchlist** | Toggle 📝 bookmark on movie/TV detail pages, separate from favorites |
| **My List** | Unified view of Favorites & Watchlist with tab switcher, separated by Movies vs TV |
| **Live Search** | Debounced navbar search with instant poster+title+year+rating dropdown results |
| **Genre Filtering** | Pill-style genre tags on Explore page, each links to filtered results |
| **Pagination** | Simple prev/next pagination with page counter |
| **Public Profiles** | `/@username` routes with SEO metadata generation |
| **Direct Messages** | User-to-user messaging system |
| **Notifications** | In-app notifications with type-based icons and auto-refresh |
| **Forum** | Category-based discussion threads with reply counts |
| **Cinema Finder** | Modal showing nearby cinemas by Vietnamese city with tags (IMAX, 4DX, Dolby) |
| **Trailer Playback** | YouTube embeds in cinematic modal with backdrop blur and ambient glow |

### 3.2 Admin Features

| Feature | Details |
|---------|---------|
| **Dashboard** | Stats widgets: total users, reviews, avg score, forum threads/replies, pending reports |
| **User Management** | Edit user modal for admin-level user modifications |
| **Review Moderation** | Searchable review list with score dot visualization |
| **Report Handling** | Pending report queue |
| **Category Management** | Forum category CRUD |
| **Content Moderation** | System-level moderation tools |

### 3.3 Profile Features

| Feature | Details |
|---------|---------|
| **Edit Profile Modal** | Change name, bio, avatar (upload), banner (upload or gradient presets), genre skills (max 5) |
| **Banner Styles** | 4 preset gradients (Aurora Warm, Deep Ocean, Neon Night, Soft Purple) or custom image upload |
| **Badges** | User achievement badges displayed on profile |
| **Skills/Interests** | Selectable genre tags: Hành động, Tâm lý, Kinh dị, Sci-Fi, Anime, etc. |
| **Stats** | Total reviews, average score |

---

## 4. UX Flow

### 4.1 Movie Discovery Flow
```
Homepage → See Hero Carousel (5 trending films)
         → Scroll through 6 content sections
         → Click movie card
         → Movie Detail page
         → Read info, watch trailer, read reviews
         → Favorite / Add to Watchlist
         → Write own review (if logged in)
```

### 4.2 Search & Explore Flow
```
Navbar → Type in search box
       → Instant dropdown with poster results
       → Click result → Movie/TV/Person detail
       → OR press Enter → Full Explore page with grid
       → Filter by genre pills
       → Paginate through results
```

### 4.3 Social Engagement Flow
```
Read reviews on movie page → Visit reviewer's profile (/@username)
→ See their favorites, review history, badges
→ Go to Forum → Browse/create discussion threads
→ Receive notifications for replies/mentions
→ Direct message other users
```

### 4.4 Content Management Flow (Admin)
```
Login as Admin → /admin sidebar navigation
→ Dashboard (overview stats)
→ Manage reviews/users/categories/reports
→ Search within admin panel
→ Edit user roles/details via modal
```

---

## 5. Reusable Ideas

### 5.1 UI Layout Ideas
- **Split hero section**: Backdrop image + floating poster + text overlay + rating rings — very cinematic feel
- **Horizontal scroll movie sections** with snap pagination dots
- **Tab-based toggle** for Favorites vs Watchlist (simple, elegant)
- **Color-coded ratings**: Different colors for different quality tiers makes scanning very fast
- **Conditional footer CTA**: Show registration banner to guests only — smart conversion tactic
- **Cinema/theater finder**: Unique regional feature that connects online reviews with real-world action

### 5.2 Feature Concepts
- **Dual scoring system**: Your own community score alongside an external API score (TMDB) — adds credibility
- **Expandable text component**: Great for long overviews/bios — keeps pages clean
- **Profile customization**: Banner gradients, skills/interests, badges — gamification elements
- **"Time ago" timestamps**: Much friendlier than raw dates
- **Genre skill tags** on profiles: Shows what genres a reviewer specializes in

### 5.3 Interaction Patterns
- **Debounced live search** with poster previews in dropdown — extremely engaging
- **Toast notifications** for async actions (review submitted, favorited, etc.)
- **Scroll-reactive navbar** with glassmorphism — premium feel
- **Password strength meter** with real-time feedback during registration
- **10-point rating with Vietnamese labels** (Kiệt tác, Xuất sắc, etc.) — contextual feedback

### 5.4 Content Organization
- **6 homepage sections** with different content strategies: trending (engagement), popular (social proof), upcoming (anticipation), top rated (quality), editor picks (editorial curation)
- **Forum preview on homepage**: Bridges content consumption with community participation
- **Person pages**: Actor/director profiles enrich the content ecosystem beyond just movies

---

## 6. Adaptation for Laravel

### 6.1 Page → Route + Controller Mapping

```
GET  /                          → HomeController@index
GET  /movies/{id}               → MovieController@show
GET  /tv/{id}                   → TvController@show
GET  /person/{id}               → PersonController@show
GET  /explore                   → ExploreController@index (?q=, ?genre=, ?page=)
GET  /login                     → AuthController@showLogin
POST /login                     → AuthController@login
GET  /register                  → AuthController@showRegister
POST /register                  → AuthController@register
POST /logout                    → AuthController@logout
GET  /profile                   → ProfileController@index (auth required)
GET  /@{username}               → ProfileController@public
GET  /my-list                   → MyListController@index (?tab=favorites|watchlist)
GET  /settings                  → SettingsController@index
GET  /messages                  → MessageController@index
GET  /forum                     → ForumController@index
GET  /forum/{id}                → ForumController@show
GET  /admin                     → Admin\DashboardController@index (admin middleware)
GET  /admin/users               → Admin\UserController@index
GET  /admin/reviews             → Admin\ReviewController@index
GET  /admin/categories          → Admin\CategoryController@index
GET  /admin/reports             → Admin\ReportController@index
```

### 6.2 Blade Component Architecture

```
layouts/
  app.blade.php              ← Main layout (fonts, navbar, footer, toast)
  admin.blade.php            ← Admin layout (sidebar + main content)

components/
  navbar.blade.php           ← Nav with conditional auth links
  footer.blade.php           ← Footer with conditional guest CTA
  hero-carousel.blade.php    ← 5-slide hero with Alpine.js
  movie-section.blade.php    ← Horizontal scroll section ($title, $subtitle, $items)
  movie-card.blade.php       ← Individual movie poster card
  review-form.blade.php      ← Star rating form (Alpine.js for interactivity)
  review-card.blade.php      ← Individual review display
  favorite-button.blade.php  ← Toggle heart (Livewire or AJAX)
  watchlist-button.blade.php ← Toggle bookmark (Livewire or AJAX)
  trailer-modal.blade.php    ← YouTube embed modal
  notification-dropdown.blade.php
  edit-profile-modal.blade.php
  expandable-text.blade.php  ← Alpine.js show more/less
  toast.blade.php            ← Flash message toasts
```

### 6.3 Key Implementation Concepts

**Hero Carousel** → Alpine.js with `x-data="{ current: 0, ... }"`, CSS transitions, and `setInterval`

**Live Search** → Alpine.js `x-model` + `@input.debounce.200ms` + [fetch('/api/search?q=...')](file:///d:/xampp/htdocs/azuridb/components/NotificationDropdown.tsx#23-36) endpoint

**Star Rating** → Alpine.js `x-data="{ score: 0, hover: 0 }"`, loop 1-10 stars, color logic in a JS helper

**Favorites/Watchlist** → Laravel AJAX routes (`POST /api/favorites`, `POST /api/watchlist`) with auth middleware, toggle logic in controller

**TMDB Integration** → Create a `TmdbService` class in `app/Services/TmdbService.php` wrapping HTTP client calls, cache responses for performance

**Admin Panel** → Use Laravel middleware group + separate `admin.blade.php` layout with icon sidebar using Blade components

---

## 7. Design Improvement Suggestions

### Inspired by Letterboxd
- **Film diary**: Let users log when they watched a film with date + quick rating
- **Activity feed**: Show recent reviews/likes from people you follow
- **Lists feature**: User-created curated lists (e.g., "Best Horror 2025") beyond just favorites/watchlist
- **Half-star ratings**: Allow 0.5 increments (5-star scale) for more nuanced scoring
- **Review likes**: Let users upvote helpful reviews

### Inspired by Rotten Tomatoes
- **Critic vs Audience split**: Separate aggregate score for "expert" reviewers vs general users
- **Certified Fresh badge**: Visual badge when a movie meets review threshold criteria
- **Consensus statement**: AI-generated summary of what reviewers agree on

### Inspired by TMDB
- **Contributions system**: Let users submit corrections/additions to movie data
- **Seasonal content sections**: "Awards Season", "Summer Blockbusters", "Holiday Picks"
- **Collection pages**: Group franchise movies together (e.g., MCU, Star Wars)

### General Modern UI Ideas
- **Dark/Light mode toggle**: The project already uses CSS variables — perfect for theme switching
- **Skeleton loading states**: Show animated placeholders while content loads (already partially used)
- **Infinite scroll** as alternative to pagination on Explore page
- **Movie comparison tool**: Compare ratings/revenue/runtime of 2+ movies side by side
- **Review spoiler tags**: Let users mark parts of their review as spoilers
- **Watch party feature**: Schedule a virtual watch event with discussion thread
- **Achievement system**: Award badges for milestones (10 reviews, 50 favorites, etc.)
- **Regional content**: Cinema finder is great — extend with showtimes API integration
- **Social sharing cards**: Generate Open Graph images with movie poster + your rating for social media sharing
