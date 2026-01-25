<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $staff = User::whereIn('role', ['teller', 'manager', 'admin'])->get();

        // Create tickets for customers
        foreach ($customers as $customer) {
            $ticketCount = fake()->numberBetween(1, 3);

            for ($i = 0; $i < $ticketCount; $i++) {
                $ticket = Ticket::factory()->create([
                    'user_id' => $customer->id,
                    'status' => fake()->randomElement(['open', 'in_progress', 'resolved', 'closed']),
                ]);

                // Add comments to some tickets
                if (fake()->boolean(70)) { // 70% chance of having comments
                    $commentCount = fake()->numberBetween(1, 4);

                    for ($j = 0; $j < $commentCount; $j++) {
                        TicketComment::factory()->create([
                            'ticket_id' => $ticket->id,
                            'user_id' => fake()->randomElement([$customer->id, $staff->random()->id]),
                        ]);
                    }
                }

                // Assign staff to some tickets
                if ($ticket->status !== 'open' && fake()->boolean(60)) {
                    $ticket->update([
                        'assigned_to' => $staff->random()->id,
                    ]);
                }
            }
        }
    }
}
