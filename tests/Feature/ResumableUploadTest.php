<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UploadSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class ResumableUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        config(['resumable_uploads.chunk_size' => 1024 * 1024]); // 1 Mo par chunk
        config(['resumable_uploads.max_file_size' => 50 * 1024 * 1024]);
    }

    public function test_unauthenticated_cannot_init()
    {
        $response = $this->postJson('/uploads/resumable/init', [
            'filename' => 'test.pdf',
            'filesize' => 1048576,
        ]);

        $response->assertStatus(401);
    }

    public function test_init_success()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/uploads/resumable/init', [
            'filename' => 'document.pdf',
            'filesize' => 1500000,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['upload_id', 'chunk_size', 'total_chunks', 'status'])
                 ->assertJson([
                     'status' => 'pending',
                     'total_chunks' => 2 // 1.5MB / 1MB ceil = 2
                 ]);

        $this->assertDatabaseHas('upload_sessions', [
            'uuid' => $response->json('upload_id'),
            'user_id' => $user->id,
            'original_name' => 'document.pdf',
        ]);
    }

    public function test_upload_chunk_and_complete()
    {
        $user = User::factory()->create();

        $init = $this->actingAs($user)->postJson('/uploads/resumable/init', [
            'filename' => 'test_file.txt',
            'filesize' => 1500,
        ]);

        $uuid = $init->json('upload_id');
        $chunk = UploadedFile::fake()->create('chunk.part', 1.5); // 1.5 Ko

        $chunkResp = $this->actingAs($user)->postJson("/uploads/resumable/{$uuid}/chunk", [
            'chunk' => $chunk,
            'chunk_index' => 0,
            'total_chunks' => 1,
            'total_size' => 1500,
            'filename' => 'test_file.txt',
        ]);

        $chunkResp->assertStatus(200)->assertJson(['is_complete' => true]);

        $completeResp = $this->actingAs($user)->postJson("/uploads/resumable/{$uuid}/complete", [
            'filename' => 'test_file.txt',
            'filesize' => 1500,
        ]);

        $completeResp->assertStatus(200)->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('files', [
            'original_name' => 'test_file.txt',
            'user_id' => $user->id,
        ]);

        $session = UploadSession::where('uuid', $uuid)->first();
        $this->assertEquals('completed', $session->status);
    }

    public function test_reject_dangerous_extension()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/uploads/resumable/init', [
            'filename' => 'shell.php',
            'filesize' => 1024,
        ]);

        $response->assertStatus(422);
    }
}
