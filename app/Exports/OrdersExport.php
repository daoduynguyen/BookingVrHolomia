<?php
namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdersExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private ?string $startDate = null,
        private ?string $endDate = null,
        private ?int $locationId = null,
    ) {}

    public function query()
    {
        return Order::with(['orderItems', 'location'])
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->when($this->locationId, fn($q) => $q->where('location_id', $this->locationId))
            ->where('status', 'paid');
    }

    public function headings(): array
    {
        return ['Mã đơn', 'Khách hàng', 'SĐT', 'Cơ sở', 'Ngày đặt', 'Tổng tiền', 'Trạng thái'];
    }

    public function map($row): array
    {
        return [
            '#' . $row->id,
            $row->customer_name,
            $row->customer_phone,
            $row->location?->name ?? '—',
            $row->booking_date,
            number_format($row->total_amount),
            $row->status,
        ];
    }
}
