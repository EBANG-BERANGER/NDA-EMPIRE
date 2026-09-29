<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Wig;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SalonTest extends TestCase
{
    use RefreshDatabase;

    private function client(array $attrs = []): User
    {
        return User::forceCreate(['name' => 'Aline', 'email' => uniqid().'@test.rw', 'phone' => '+250788000000', 'password' => 'secret123'] + $attrs);
    }

    private function nextOpenDay(): CarbonImmutable
    {
        $d = CarbonImmutable::tomorrow();
        while (! config('salon.hours')[$d->isoWeekday()]) {
            $d = $d->addDay();
        }

        return $d;
    }

    public function test_pages_render(): void
    {
        foreach (['/', '/perruques', '/reserver', '/realisations', '/connexion', '/inscription'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->actingAs($this->client())->get('/mon-compte')->assertOk();
    }

    public function test_english_switch_translates_and_sticks_to_the_account(): void
    {
        $user = $this->client();
        $this->actingAs($user)->get('/?lang=en')->assertSee('Book an appointment')->assertSee('Wig install (lace frontal)');
        $this->assertSame('en', $user->fresh()->locale);
        $this->get('/')->assertSee('lang="en"', false);
    }

    public function test_a_slot_cannot_be_double_booked(): void
    {
        $service = Service::first(); // seeded, 90 min
        $day = $this->nextOpenDay();
        $at = $day->setTime(10, 0);

        $this->actingAs($this->client())->post('/reserver', ['service_id' => $service->id, 'starts_at' => $at->format('Y-m-d H:i')])
            ->assertRedirect('/mon-compte');

        // Same start and an overlapping start are both refused.
        foreach (['10:00', '11:00'] as $time) {
            $this->actingAs($this->client())->post('/reserver', ['service_id' => $service->id, 'starts_at' => $day->format('Y-m-d').' '.$time])
                ->assertSessionHasErrors('starts_at');
        }
        $this->assertSame(1, Booking::count());

        $slots = array_map(fn ($s) => $s->format('H:i'), Booking::availableSlots($service, $day));
        $this->assertNotContains('10:00', $slots);
        $this->assertNotContains('09:00', $slots); // 09:00–10:30 would overlap
        $this->assertContains('11:30', $slots);
        $this->assertNotContains('18:00', $slots); // would end after closing
    }

    public function test_admin_area_is_admin_only(): void
    {
        config(['salon.admin_email' => 'niomba@test.rw']);
        $this->post('/inscription', ['name' => 'X', 'phone' => '1', 'email' => 'NIOMBA@test.rw', 'password' => 'secret123', 'password_confirmation' => 'secret123'])
            ->assertSessionHasErrors('email');

        $this->actingAs($this->client())->get('/admin')->assertForbidden();
        $this->actingAs($this->client(['is_admin' => true]))->get('/admin')->assertOk();
    }

    public function test_try_on_stores_private_result_visible_only_to_owner(): void
    {
        Storage::fake('local');
        Storage::fake('public');
        config(['salon.gemini_key' => 'test-key']);
        Http::fake(['generativelanguage.googleapis.com/*' => Http::response(['candidates' => [['content' => ['parts' => [['inlineData' => ['mimeType' => 'image/png', 'data' => base64_encode('png-bytes')]]]]]]])]);

        $wig = Wig::create(['name' => 'Body wave', 'price' => 150000, 'image_path' => UploadedFile::fake()->image('w.jpg')->store('wigs', 'public')]);
        $user = $this->client();

        $this->actingAs($user)->post('/cabine/photo', ['selfie' => UploadedFile::fake()->image('me.jpg')])->assertRedirect('/perruques');
        $this->actingAs($user)->post("/perruques/{$wig->id}/essayer")->assertRedirect();

        $tryon = $user->tryons()->firstOrFail();
        $this->assertSame('png-bytes', Storage::disk('local')->get($tryon->result_path));
        $this->actingAs($user)->get("/essais/{$tryon->id}")->assertOk();
        $this->actingAs($this->client())->get("/essais/{$tryon->id}")->assertForbidden();
    }
}
