<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class GenerateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:generate 
                            {count=100 : Number of products to generate} 
                            {--chunk=1000 : Insert records in chunks to avoid memory issues}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate fake products using the ProductFactory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = $this->argument('count');
        $chunkSize = min($this->option('chunk'), 100); // Lotes pequeños (ej. 100)

        $this->info("Generating {$count} products with Observers (chunks of {$chunkSize})...");

        $remaining = $count;
        while ($remaining > 0) {
            $currentChunk = min($chunkSize, $remaining);

            // Usamos create() para cada lote (dispara Observers)
            Product::factory()->count($currentChunk)->create();

            $remaining -= $currentChunk;
            $this->info("Inserted {$currentChunk} products (remaining: {$remaining})");
        }

        $this->info("✅ {$count} products generated with Observers!");
    }
}
