<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController; 
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ChatAIController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\TicketController as AdminTicketController;
use App\Http\Controllers\Admin\SlotController;

// --- QUY TRÌNH ĐẶT VÉ MỚI (Booking Flow) ---

// BƯỚC 1: Màn hình nhập thông tin & chọn ngày (Thay vì thêm thẳng vào giỏ)
Route::get('/booking/nhap-thong-tin/{id}', [CheckoutController::class, 'bookingForm'])->name('booking.form');

// BƯỚC 2: Xử lý thông tin từ Form -> Lưu vào Session Giỏ hàng -> Chuyển sang trang Review
Route::post('/booking/xac-nhan', [CheckoutController::class, 'confirmToCart'])->name('booking.confirm');

// BƯỚC 3: Màn hình Giỏ hàng (Đóng vai trò trang Review - Xem lại đơn)
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::get('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::patch('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update'); // Để sửa số lượng nếu muốn

// BƯỚC 4: Xử lý Thanh toán cuối cùng (Chốt đơn từ trang Giỏ hàng)
Route::post('/thanh-toan/chot-don', [CheckoutController::class, 'finalPayment'])->name('payment.final'); 


Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 1. KHU VỰC ADMIN (Đã gộp và sửa chuẩn)
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {

    

    // Middleware kiểm tra quyền Admin (Role = admin)
    Route::group([
        'middleware' => function ($request, $next) {
            if (auth()->user() && auth()->user()->role === 'admin') {
                return $next($request);
            }
            return redirect('/')->with('error', 'Bạn không có quyền truy cập vùng Admin!');
        }
    ], function () {

        // --- TÊN ROUTE CHUNG: admin. ---
        Route::name('admin.')->group(function () {

            // 1. Dashboard
            Route::get('/', [AdminController::class, 'index'])->name('dashboard');
            Route::get('/dashboard', [AdminController::class, 'index']); // Dự phòng

            // Quản lý khung giờ (Lịch)
            Route::get('/slots', [SlotController::class, 'index'])->name('slots.index');
    
           // API tạo lịch tự động hàng loạt (Sẽ làm ở bước sau)
            Route::post('/slots/generate', [SlotController::class, 'generate'])->name('slots.generate');

            // Thêm ca lẻ mới
            Route::post('/slots/store-single', [SlotController::class, 'storeSingle'])->name('slots.store-single');

            // Cập nhật (Sửa) ca
            Route::put('/slots/{id}', [SlotController::class, 'update'])->name('slots.update');

            // Xóa ca
            Route::delete('/slots/{id}', [SlotController::class, 'destroy'])->name('slots.destroy');
            
            // Lấy danh sách khách hàng đặt chỗ theo khung giờ
            Route::get('/slots/{id}/customers', [SlotController::class, 'getCustomers'])->name('slots.customers');

            // 2. QUẢN LÝ VÉ (SỬ DỤNG AdminTicketController MỚI)
            Route::prefix('tickets')->name('tickets.')->group(function () {
                Route::get('/', [AdminTicketController::class, 'index'])->name('index');
                Route::get('/create', [AdminTicketController::class, 'create'])->name('create'); // -> Trỏ đúng vào hàm có biến $categories
                Route::post('/store', [AdminTicketController::class, 'store'])->name('store');
                Route::get('/edit/{id}', [AdminTicketController::class, 'edit'])->name('edit');
                Route::put('/update/{id}', [AdminTicketController::class, 'update'])->name('update');
                Route::delete('/delete/{id}', [AdminTicketController::class, 'destroy'])->name('delete');
            });

            // 3. QUẢN LÝ ĐƠN HÀNG (Booking)
            Route::prefix('bookings')->name('bookings.')->group(function () {
                Route::get('/', [BookingController::class, 'index'])->name('index');
                Route::put('/update-status/{id}', [BookingController::class, 'updateStatus'])->name('updateStatus');
                Route::delete('/delete/{id}', [BookingController::class, 'destroy'])->name('delete');

            });

            // 4. CÁC MỤC CŨ (User, Contact) 
            // Quản lý người dùng
            Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
            Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');

            // Quản lý tin nhắn liên hệ
            Route::get('/contacts', [AdminController::class, 'listContacts'])->name('contacts');
            Route::delete('/contacts/{id}', [AdminController::class, 'deleteContact'])->name('contacts.delete');
            //Phản hồi khách hàng
            Route::post('/contacts/reply/{id}', [AdminController::class, 'replyContact'])->name('admin.contacts.reply');
            
            // 5. Quản lý Mã giảm giá
            Route::resource('coupons', CouponController::class);
        });
    });
});


/*
|--------------------------------------------------------------------------
| 2. CÁC ROUTE KHÁCH HÀNG (Client)
|--------------------------------------------------------------------------
*/

// Trang chủ & Sản phẩm
Route::get('/', [TicketController::class, 'index'])->name('home');
Route::get('/tro-choi/{id}', [TicketController::class, 'show'])->name('ticket.show');
Route::get('/danh-sach-ve', [TicketController::class, 'shop'])->name('ticket.shop');
Route::get('/gioi-thieu', [TicketController::class, 'about'])->name('about');

// Đặt vé (Booking phía khách)
Route::get('/dat-ve/{id}', [TicketController::class, 'create'])->name('booking.create');
Route::post('/dat-ve', [TicketController::class, 'store'])->name('booking.store');

// Giỏ hàng
Route::get('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::patch('/update-cart', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

// Liên hệ
Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
Route::post('/lien-he', [ContactController::class, 'send'])->name('contact.send');

// AI Chat
Route::post('/ai-chat', [ChatAIController::class, 'chat'])->name('ai.chat');

// Route lấy khung giờ cho AJAX (Đặt bên ngoài middleware auth để khách chưa log cũng thấy)
Route::get('/api/get-slots', [App\Http\Controllers\CheckoutController::class, 'getSlots'])->name('api.slots');

/*
|--------------------------------------------------------------------------
| 3. KHU VỰC ĐĂNG NHẬP / PROFILE / THANH TOÁN
|--------------------------------------------------------------------------
*/

// Khách chưa đăng nhập
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

});

// Khách ĐÃ đăng nhập
Route::middleware('auth')->group(function () {
    // Thanh toán
    Route::post('/thanh-toan/chot-don', [CheckoutController::class, 'finalPayment'])->name('payment.final');
    Route::post('/booking/xac-nhan', [CheckoutController::class, 'confirmToCart'])->name('booking.confirm');
    Route::get('/checkout/banking/{id}', [CheckoutController::class, 'banking'])->name('checkout.banking');
    Route::post('/checkout/check-coupon', [CheckoutController::class, 'checkCoupon'])->name('checkout.check_coupon');
    Route::get('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
    Route::post('/checkout/check-coupon', [CheckoutController::class, 'checkCoupon'])->name('check.coupon');
    // Hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::get('/profile/order/{id}', [ProfileController::class, 'showOrder'])->name('profile.order.detail');

    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});