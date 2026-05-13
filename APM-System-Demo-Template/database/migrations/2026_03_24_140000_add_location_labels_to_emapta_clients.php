<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $updates = [
            [
                'old_name' => 'EMAPTA PHILIPPINES INC',
                'address' => '5F PNB MAKATI BUILDING 6754 AYALA AVENUE SAN LORENZOMAKATI CITY 1226',
                'new_name' => 'EMAPTA PHILIPPINES INC (PNB Makati)',
            ],
            [
                'old_name' => 'EMAPTA PHILIPPINES INC.',
                'address' => 'U-ABCD 4F EQUITABLE TOWER 8751PASEO DE ROXAS, BEL - AIR MAKATI1226 PHILIPPINES',
                'new_name' => 'EMAPTA PHILIPPINES INC. (Equitable Tower Makati)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
                'address' => '2ND FLR 6780 AYALA AVENUE BDG AYALAMAKATI CITY 1226',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC (6780 Ayala 2F Makati)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
                'address' => 'NEUTRINUS INFORMATION TECHNOLOGY CENTER, PILANDO BLDG. 254 MAGSAYSAYBAGUIO CITY 2600',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC (Neutrinus Baguio)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
                'address' => '7TH FL. AYALA AVENUE BLDG. 6780 AYALA AVE MAKATI CITY 1200 PHILIPPINES',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC (6780 Ayala 7F Makati)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
                'address' => '6F ASIAN STAR BLDG ASIAN DRIVE FILINVEST CITY ALABANGMUNTINLUPA 1780',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC (Asian Star Alabang)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
                'address' => '5TH FLR ST FRANCIS SQUARE BLDGORTIGAS BRGY WACK WACK MANDALUYONGCITY MANDALUYONG CITY 1555',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC (St. Francis Square Mandaluyong)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
                'address' => '7TH FLOOR JAKA BLDG.,6780AYALA AVENUE MAKATI CITY1220 PHILIPPINES',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC. (Jaka Building Makati)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
                'address' => 'PENTHOUSE GOODLAND BLDG. 377 SEN GIL PUYAT AVE. MAKATI CITY 1225 PHILIPPINES',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC. (Goodland Makati)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
                'address' => 'ORIENT SQUARE I.T. BLDG., UNIT 201 EMERALD AVENUEPASIG CITY 1600',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC. (Orient Square Pasig)',
            ],
            [
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
                'address' => '12F IBM PLAZA BLDG EASTWOOD CITY CYBERPARK BAGUMBAYANQUEZON CITY 1110',
                'new_name' => 'EMAPTA VERSATILE SERVICES INC. (IBM Plaza Eastwood)',
            ],
        ];

        foreach ($updates as $update) {
            DB::table('clients')
                ->where('name', $update['old_name'])
                ->where('address', $update['address'])
                ->update(['name' => $update['new_name']]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $updates = [
            [
                'current_name' => 'EMAPTA PHILIPPINES INC (PNB Makati)',
                'address' => '5F PNB MAKATI BUILDING 6754 AYALA AVENUE SAN LORENZOMAKATI CITY 1226',
                'old_name' => 'EMAPTA PHILIPPINES INC',
            ],
            [
                'current_name' => 'EMAPTA PHILIPPINES INC. (Equitable Tower Makati)',
                'address' => 'U-ABCD 4F EQUITABLE TOWER 8751PASEO DE ROXAS, BEL - AIR MAKATI1226 PHILIPPINES',
                'old_name' => 'EMAPTA PHILIPPINES INC.',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC (6780 Ayala 2F Makati)',
                'address' => '2ND FLR 6780 AYALA AVENUE BDG AYALAMAKATI CITY 1226',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC (Neutrinus Baguio)',
                'address' => 'NEUTRINUS INFORMATION TECHNOLOGY CENTER, PILANDO BLDG. 254 MAGSAYSAYBAGUIO CITY 2600',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC (6780 Ayala 7F Makati)',
                'address' => '7TH FL. AYALA AVENUE BLDG. 6780 AYALA AVE MAKATI CITY 1200 PHILIPPINES',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC (Asian Star Alabang)',
                'address' => '6F ASIAN STAR BLDG ASIAN DRIVE FILINVEST CITY ALABANGMUNTINLUPA 1780',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC (St. Francis Square Mandaluyong)',
                'address' => '5TH FLR ST FRANCIS SQUARE BLDGORTIGAS BRGY WACK WACK MANDALUYONGCITY MANDALUYONG CITY 1555',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC. (Jaka Building Makati)',
                'address' => '7TH FLOOR JAKA BLDG.,6780AYALA AVENUE MAKATI CITY1220 PHILIPPINES',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC. (Goodland Makati)',
                'address' => 'PENTHOUSE GOODLAND BLDG. 377 SEN GIL PUYAT AVE. MAKATI CITY 1225 PHILIPPINES',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC. (Orient Square Pasig)',
                'address' => 'ORIENT SQUARE I.T. BLDG., UNIT 201 EMERALD AVENUEPASIG CITY 1600',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
            ],
            [
                'current_name' => 'EMAPTA VERSATILE SERVICES INC. (IBM Plaza Eastwood)',
                'address' => '12F IBM PLAZA BLDG EASTWOOD CITY CYBERPARK BAGUMBAYANQUEZON CITY 1110',
                'old_name' => 'EMAPTA VERSATILE SERVICES INC.',
            ],
        ];

        foreach ($updates as $update) {
            DB::table('clients')
                ->where('name', $update['current_name'])
                ->where('address', $update['address'])
                ->update(['name' => $update['old_name']]);
        }
    }
};
