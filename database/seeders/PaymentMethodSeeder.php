<?php

namespace Database\Seeders;

use App\Entities\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = json_decode(file_get_contents(database_path('seeders/jsons/payment_methods.json')), true);

        foreach ($paymentMethods as $paymentMethod) {
            $paymentMethodInstructions = $paymentMethod['payment_method_instructions'] ?? [];

            $paymentMethod = PaymentMethod::create([
                'type' => $paymentMethod['type'],
                'code' => $paymentMethod['code'],
                'name' => $paymentMethod['name'],
                'description' => $paymentMethod['description'],
                'is_enabled' => $paymentMethod['is_enabled'] ?? true,
                'xendit_code' => $paymentMethod['xendit_code'],
            ]);

            foreach ($paymentMethodInstructions as $instruction) {
                $paymentMethod->paymentMethodInstructions()->create($instruction);
            }
        }
    }
}
