<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LessonUploadVerifyTest extends TestCase
{
    private function makeMp4(string $path): void
    {
        $ftyp = "\x00\x00\x00\x1C" . "ftyp" . "isom" . "\x00\x00\x02\x00" . "isom" . "iso2" . "avc1" . "mp41";
        $mdat = "\x00\x00\x00\x08" . "free";
        file_put_contents($path, $ftyp . $mdat);
    }

    public function test_admin_ajax_upload_uses_readable_filename_and_learner_page_works(): void
    {
        $admin = User::create([
            'name' => 'Verify Admin',
            'email' => 'verify-admin-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $learner = User::create([
            'name' => 'Verify Learner',
            'email' => 'verify-learner-' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $category = Category::first() ?? Category::create(['name' => 'Verify Category', 'slug' => 'verify-category-' . uniqid()]);

        $course = Course::create([
            'category_id' => $category->id,
            'title' => 'Verify Course',
            'slug' => 'verify-course-' . uniqid(),
            'price_mad' => 250,
        ]);

        CoursePurchase::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'status' => 'paid',
            'purchased_at' => now(),
        ]);

        $mp4Path1 = __DIR__ . '/../../sample_verify1.mp4';
        $mp4Path2 = __DIR__ . '/../../sample_verify2.mp4';
        $this->makeMp4($mp4Path1);
        $this->makeMp4($mp4Path2);

        // --- AJAX upload of lesson 1 (as the admin would via the new form) ---
        $response = $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('admin.courses.lessons.store', $course), [
                'title' => 'Introduction to HTML',
                'description' => 'first lesson',
                'video' => new UploadedFile($mp4Path1, 'raw1.mp4', 'video/mp4', null, true),
            ]);

        $response->assertOk();
        $response->assertJsonStructure(['message', 'redirect']);

        $lesson1 = Lesson::where('course_id', $course->id)->where('title', 'Introduction to HTML')->first();
        $this->assertNotNull($lesson1);
        $this->assertStringContainsString('introduction-to-html-' . $lesson1->id, $lesson1->video_path);
        $this->assertStringNotContainsString('.mp4mp4', $lesson1->video_path);
        $this->assertTrue(Storage::disk('public')->exists($lesson1->video_path));

        // --- second lesson so we can test prev/next + AJAX fragment switching ---
        $response2 = $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('admin.courses.lessons.store', $course), [
                'title' => 'CSS Basics',
                'description' => 'second lesson',
                'video' => new UploadedFile($mp4Path2, 'raw2.mp4', 'video/mp4', null, true),
            ]);
        $response2->assertOk();
        $lesson2 = Lesson::where('course_id', $course->id)->where('title', 'CSS Basics')->first();
        $this->assertStringContainsString('css-basics-' . $lesson2->id, $lesson2->video_path);

        // --- Validation error path returns JSON 422, not a redirect ---
        $badResponse = $this->actingAs($admin)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->post(route('admin.courses.lessons.store', $course), [
                'title' => '',
                'description' => 'missing title and video',
            ]);
        $badResponse->assertStatus(422);
        $badResponse->assertJsonValidationErrors(['title', 'video']);

        // --- Learner page: video should appear, sidebar lists both lessons ---
        $learnResponse = $this->actingAs($learner)->get(route('courses.learn', [$course->slug, 'lesson' => $lesson1->id]));
        $learnResponse->assertOk();
        $learnResponse->assertSee('Introduction to HTML');
        $learnResponse->assertSee('CSS Basics');
        $learnResponse->assertSee(Storage::disk('public')->url($lesson1->video_path), false);
        $learnResponse->assertSee('id="videoArea"', false);
        $learnResponse->assertSee('data-next-lesson', false);

        // --- AJAX fragment fetch for lesson 2 (simulating the in-page JS switch) ---
        $fragmentResponse = $this->actingAs($learner)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('courses.learn', [$course->slug, 'lesson' => $lesson2->id]));
        $fragmentResponse->assertOk();
        $fragmentResponse->assertSee('CSS Basics');
        $fragmentResponse->assertSee(Storage::disk('public')->url($lesson2->video_path), false);

        fwrite(STDOUT, "\nVERIFY_RESULT lesson1_video={$lesson1->video_path} lesson2_video={$lesson2->video_path}\n");

        // cleanup
        Storage::disk('public')->delete($lesson1->video_path);
        Storage::disk('public')->delete($lesson2->video_path);
        $lesson1->delete();
        $lesson2->delete();
        CoursePurchase::where('course_id', $course->id)->delete();
        $course->delete();
        $admin->delete();
        $learner->delete();
        @unlink($mp4Path1);
        @unlink($mp4Path2);
    }
}
