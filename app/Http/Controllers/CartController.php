<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;

// Giỏ hàng
class CartController extends Controller
{
    // 1. Thêm vào giỏ
    public function addToCart($id)
    {
        $ticket = Ticket::findOrFail($id);
        $cart = session()->get('cart', []);

        // Nếu vé đã có trong giỏ -> Tăng số lượng
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            // Nếu chưa -> Thêm mới
            $cart[$id] = [
                "name" => $ticket->name,
                "quantity" => 1,
                "price" => $ticket->price, // Mặc định lấy giá ngày thường
                "image" => $ticket->image_url
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm vé vào giỏ!');
    }

    // 2. Xem giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);

        // BỘ LỌC RÁC: Xóa sạch các phần tử lỗi (không có giá) bị kẹt từ trước
        foreach ($cart as $key => $item) {
            if (!is_array($item) || !isset($item['price'])) {
                unset($cart[$key]);
            }
        }
        session()->put('cart', $cart); // Lưu lại giỏ hàng sạch

        return view('cart.index', compact('cart'));
    }

    // 3. Xóa 1 vé
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]); // Xóa khỏi mảng
            session()->put('cart', $cart); // Lưu lại session
        }

        return redirect()->back()->with('success', 'Đã xóa vé khỏi giỏ hàng!');
    }

    // 4. Xóa sạch giỏ hàng
    public function clear(\Illuminate\Http\Request $request)
    {
        session()->forget(['cart', 'coupon', 'coupon_code', 'cart_created_at']);
        if ($request->query('redirect') === 'home') {
            return redirect()->route('home')->with('error', 'Lệnh đặt chỗ hết hạn, chỗ đã được phục hồi giữ chỗ.');
        }
        return redirect()->route('ticket.shop')->with('success', 'Đã xóa sạch giỏ hàng!');
    }

    // 5. Cập nhật số lượng (AJAX)
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart', []);

            // 1. CHỈ cập nhật nếu sản phẩm có thật
            if (isset($cart[$request->id])) {
                // Cập nhật số lượng mới
                $cart[$request->id]['quantity'] = $request->quantity;

                // Tính lại tiền Tạm tính (Subtotal) của riêng dòng vé này
                $price = $cart[$request->id]['price'] ?? 0;
                $subTotal = $price * $request->quantity;

                // 2. TÍNH LẠI MÃ GIẢM GIÁ (Dành riêng cho vé này)
                $discountAmount = 0;
                $couponCode = $cart[$request->id]['coupon_code'] ?? null;

                // Nếu vé này có xài mã giảm giá thì mới tính toán lại
                if ($couponCode) {
                    // Truy vấn DB để lấy % giảm giá hoặc số tiền cố định
                    $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

                    if ($coupon) {
                        // Tính tiền được giảm dựa trên SubTotal mới
                        if ($coupon->type == 'percent') {
                            $discountAmount = ($subTotal * $coupon->value) / 100;
                        } else {
                            $discountAmount = $coupon->value;
                        }

                        // Không cho phép số tiền được giảm vượt quá tiền gốc của vé
                        if ($discountAmount > $subTotal) {
                            $discountAmount = $subTotal;
                        }
                    }
                }

                // 3. Cập nhật lại số tiền giảm vào đúng cái vé đó trong Giỏ hàng
                $cart[$request->id]['discount'] = $discountAmount;

                // Lưu lại Session
                session()->put('cart', $cart);

                // Trả về JSON để Frontend tự Reload lại trang
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật giỏ hàng!'
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => 'Lỗi cập nhật']);
    }
}