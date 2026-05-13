<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'mo',
        'number',
        'jo_date',
        'costing',
        'co',
        'port',
        'eta',
        'demurrage_date',
        'detention_date',
        'port_storage_date',
        'discharge_date',
        'status',
        'date_delivered',
        'consignee',
        'client_id',
        'shipper',
        'description',
        'origin',
        'po_number',
        'invoice_no',
        'vessel_voyage_no',
        'date_of_arrival',
        'bl_awb_no',
        'no_of_container',
        'shipping_lines',
        'no_of_packages',
        'kind_of_packages',
        'gross_weight',
        'no_of_cbm',
        'entry_no',
        'ctnr_deposit',
        'date_refunded',
        'remarks_location',
        'co_loader_forwarder',
        'assigned_user_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'jo_date' => 'date',
        'eta' => 'date',
        'demurrage_date' => 'date',
        'detention_date' => 'date',
        'port_storage_date' => 'date',
        'discharge_date' => 'date',
        'date_delivered' => 'date',
        'date_of_arrival' => 'date',
        'date_refunded' => 'date',
        'gross_weight' => 'decimal:2',
        'no_of_cbm' => 'decimal:2',
        'ctnr_deposit' => 'decimal:2',
    ];

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function attachments()
    {
        return $this->hasMany(JobOrderAttachment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function billingStatements()
    {
        return $this->hasMany(BillingStatement::class);
    }

    public function serviceInvoices()
    {
        return $this->hasMany(ServiceInvoice::class);
    }

    public function debitCreditNotes()
    {
        return $this->hasMany(DebitCreditNote::class);
    }
}
