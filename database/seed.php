<?php

require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Models\Volet;
use App\Models\Activity;
use App\Models\Partner;
use App\Models\Testimonial;
use App\Models\Post;
use App\Models\Campaign;
use App\Models\Student;
use App\Models\Inscription;
use App\Services\AuthService;
use Illuminate\Database\Capsule\Manager as Capsule;

try {
    Capsule::connection()->getPdo();
} catch (\Throwable $e) {
    echo "Database connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

Capsule::statement('SET FOREIGN_KEY_CHECKS=0');
Capsule::table('inscriptions')->truncate();
Capsule::table('campaigns')->truncate();
Capsule::table('posts')->truncate();
Capsule::table('testimonials')->truncate();
Capsule::table('partners')->truncate();
Capsule::table('activities')->truncate();
Capsule::table('volets')->truncate();
Capsule::table('students')->truncate();
Capsule::table('users')->truncate();
Capsule::statement('SET FOREIGN_KEY_CHECKS=1');

// Users
$user = User::create([
    'name' => 'Gérard Nishimwe',
    'email' => 'admin@birashobokacenter.org',
    'password' => AuthService::hashPassword('admin123'),
]);
AuthService::createTokenForUser($user);

// Secondary user
$user2 = User::create([
    'name' => 'giraso',
    'email' => 'giraso.pro@gmail.com',
    'password' => AuthService::hashPassword('password'),
]);
AuthService::createTokenForUser($user2);

// Volets
$volet1 = Volet::create([
    'name' => 'Education',
    'slogan' => 'Learn and grow',
    'subtitle' => 'Youth programs',
    'description' => 'Programs for young learners',
    'target' => 'young',
    'place' => 'Kigali',
]);

$volet2 = Volet::create([
    'name' => 'Women Empowerment',
    'slogan' => 'Strong women, strong nation',
    'subtitle' => 'Skills for women',
    'description' => 'Programs for women',
    'target' => 'women',
    'place' => 'Butare',
]);

// Activities
$activity1 = Activity::create(['volet_id' => $volet1->id, 'title' => 'Coding Bootcamp', 'description' => 'Learn to code']);
$activity2 = Activity::create(['volet_id' => $volet2->id, 'title' => 'Entrepreneurship', 'description' => 'Business skills for women']);

// Partners
Partner::create(['name' => 'Global NGO', 'volet_id' => null]);
Partner::create(['name' => 'Local School', 'volet_id' => $volet1->id]);

// Testimonials
Testimonial::create(['activity_id' => $activity1->id, 'name' => 'Alice', 'photo' => null, 'content' => 'Great program']);

// Posts
Post::create(['volet_id' => $volet1->id, 'title' => 'New Session', 'description' => 'Enroll now', 'published_at' => date('Y-m-d H:i:s')]);

// Campaigns
$campaign = Campaign::create([
    'volet_id' => $volet1->id,
    'activity_id' => $activity1->id,
    'edition' => '2026',
    'title' => 'Summer Coding',
    'description' => 'Summer coding camp',
    'registration_start' => date('Y-m-d', strtotime('+1 week')),
    'registration_end' => date('Y-m-d', strtotime('+2 weeks')),
    'start_date' => date('Y-m-d', strtotime('+3 weeks')),
    'end_date' => date('Y-m-d', strtotime('+4 weeks')),
    'place' => $volet1->place,
]);

// Students
$student = Student::create(['name' => 'Student One', 'email' => 'student1@example.com']);

// Inscription
Inscription::create(['campaign_id' => $campaign->id, 'student_id' => $student->id, 'status' => 'pending']);

echo "Seeding complete." . PHP_EOL;
