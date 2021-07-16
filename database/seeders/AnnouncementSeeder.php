<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Announcement::insert([
        [
          'title' => 'Countries of the World',
          'description' => 'Learn countries of the world, their nationalities and what languages they speak.',
          'video' => null,
          'video_iframe' => '<iframe src="https://player.vimeo.com/video/92060045?color=fdeb1d&amp;title=0" frameborder="0" allowfullscreen="allowfullscreen" id="fitvid0"></iframe>',
          'is_show_in_home' => true,
          'teaching_language_id' => 1,
        ],
        [
            'title' => 'Countries of the World',
            'description' => 'Learn countries of the world, their nationalities and what languages they speak.',
            'video' => null,
            'video_iframe' => '<iframe src="https://www.youtube.com/embed/OmWHlGKa-E8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            'is_show_in_home' => true,
            'teaching_language_id' => 2,
        ]
      ]);
    }
}
