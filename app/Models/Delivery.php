<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Delivery extends Model
{
    use HasFactory;

    /*
     * Add new package to deliveries
     */
    public static function add(array $data): int
    {
        return DB::table('deliveries')->insertGetId([
            'customer_id' => $data['customer_id'],
            'ttn'       => $data['delivery_ttn'],
            'width'     => $data['package_width'],
            'height'    => $data['package_height'],
            'length'    => $data['package_length'],
            'weight'    => $data['package_weight']
        ]);
    }

    /*
     * Add new customer if is not exists (checking by phone number)
     */
    public static function addCustomer(array $data): int
    {
        // checking is customer exists
        $id = DB::table('customers')->where('phone_number', $data['customer_phone_number'])->get('id')->toArray();

        // inserting if is not exists
        if (count($id) === 0) {
            return DB::table('customers')->insertGetId([
                'full_name'    => $data['customer_full_name'],
                'phone_number' => $data['customer_phone_number'],
                'address'      => $data['customer_address']
            ]);
        }

        // updating if exists
        DB::table('customers')->where('phone_number', $data['customer_phone_number'])->update([
            'full_name' => $data['customer_full_name'],
            'address'   => $data['customer_address']
        ]);
        return $id[0]->id;
    }

    /*
     * Get all info about one delivery
     */
    public static function get(int $id): array
    {
        return DB::table('deliveries')->select(['ttn', 'height', 'width', 'length', 'weight', 'full_name', 'phone_number', 'address'])
            ->join('customers', 'customers.id', '=', 'deliveries.customer_id')
            ->where('deliveries.id', $id)->get()->toArray();
    }

    public static function getByCustomer(string $phone_number): bool | array
    {
        $customer_id = DB::table('customers')->where('phone_number', $phone_number)->get('id')->toArray();

        if(count($customer_id) === 0)
        {
            return false;
        }

        return DB::table('deliveries')->select(['ttn', 'height', 'width', 'length', 'weight', 'full_name', 'phone_number', 'address'])
            ->join('customers', 'customers.id', '=', 'deliveries.customer_id')
            ->where('deliveries.customer_id', $customer_id[0]->id)->get()->toArray();
    }
}
