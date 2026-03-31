<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Provider;
use App\Models\Offer;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $providers = Provider::all();
        $offers = Offer::all();

        if ($users->isEmpty() || $providers->isEmpty()) {
            return;
        }

        $comments = [
            'خدمة ممتازة جداً وأنصح الجميع به.',
            'الطبيب متعاون جداً والمركز نظيف.',
            'تجربة رائعة، الأسعار مناسبة والخدمة سريعة.',
            'شكراً لكم على حسن التعامل.',
            'مركز طبي متميز وكادر عمل محترف.',
            'العرض كان حقيقياً واستفدت من الخصم بشكل كبير.',
            'أفضل تجربة طبية مررت بها، دقة في المواعيد.',
            'خدمة جيدة ولكن الانتظار كان طويلاً نوعاً ما.',
            'أنصح بالتعامل مع هذا الطبيب، خبرة كبيرة.',
            'المركز يوفر خصوصية تامة وراحة للمريض.'
        ];

        foreach ($providers as $provider) {
            // Create 2-4 reviews for each provider
            $numReviews = rand(2, 4);
            $randomUsers = $users->random(min($numReviews, $users->count()));

            foreach ($randomUsers as $user) {
                // Determine if this review is linked to an offer of that provider
                $offer = $offers->where('provider_id', $provider->id)->random() ?? null;

                Review::create([
                    'user_id' => $user->id,
                    'provider_id' => $provider->id,
                    'offer_id' => $offer?->id,
                    'rating' => rand(4, 5), // Higher ratings for seeders
                    'comment' => $comments[array_rand($comments)],
                    'status' => 'approved'
                ]);
            }
        }
    }
}
