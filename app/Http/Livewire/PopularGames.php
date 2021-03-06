<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Carbon\Carbon;

class PopularGames extends Component
{
    public $popularGames = [];

    public function loadPopularGames()
    {
        $before = Carbon::now()->subMonths(2)->timestamp;
        $after = Carbon::now()->addMonths(2)->timestamp;

        $this->popularGames = Cache::remember('popular-games', 10, function () use($before, $after) {
            return Http::withHeaders(config("services.igdb"))
                ->withOptions([
                    'body' => "
                        fields name, cover.url, first_release_date, popularity, platforms.abbreviation, rating, slug;
                        where platforms = (48,49,130,6)
                        & (first_release_date >= {$before}
                        & first_release_date < {$after}); 
                        sort popularity desc;
                        limit 12;
                    " 
                ])->get('https://api-v3.igdb.com/games/')
                ->json();
        });

    }

    public function render()
    {
        return view('livewire.popular-games');
    }
}
