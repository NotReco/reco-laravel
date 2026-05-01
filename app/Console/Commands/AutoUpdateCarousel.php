<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;

class AutoUpdateCarousel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'carousel:auto-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động càn quét và cập nhật lại 20 phim trên Carousel dự trên trending';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Đang dọn dẹp Carousel cũ...");
        Movie::where('is_featured', true)->update([
            'is_featured' => false,
            'featured_order' => 0
        ]);
        \App\Models\TvShow::where('is_featured', true)->update([
            'is_featured' => false,
            'featured_order' => 0
        ]);

        $this->info("Lấy danh sách 10 phim lẻ và 10 TV series Trending có đủ HÌNH/ẢNH...");
        
        $trendingMovies = Movie::whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->whereNotNull('trailer_url')
            ->orderByDesc('view_count')
            ->take(10)
            ->get();
            
        $trendingTvShows = \App\Models\TvShow::whereNotNull('backdrop')
            ->whereNotNull('poster')
            ->orderByDesc('view_count')
            ->take(10)
            ->get();
            
        // Gộp chung và sắp xếp lại theo view_count
        $allTrending = $trendingMovies->concat($trendingTvShows)->sortByDesc('view_count')->values();

        foreach ($allTrending as $index => $item) {
            $item->update([
                'is_featured' => true,
                'featured_order' => $index + 1,
            ]);
        }

        $this->info("Hoàn tất! Đã cập nhật Carousel thành công.");
    }
}
