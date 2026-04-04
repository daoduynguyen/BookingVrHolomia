<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationLandingController;
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
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SettingsController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Admin\AdminSettingsController;

Route::prefix('chi-nhanh/{subdomain}')->group(function () {
    // Trang chủ + chi tiết vé
    Route::get('/', [App\Http\Controllers\BranchController::class, 'index'])->name('branch.home');
    Route::get('/tro-choi/{id}', [App\Http\Controllers\BranchController::class, 'detail'])->name('branch.detail');

    Route::get('/gioi-thieu', [App\Http\Controllers\BranchController::class, 'about'])->name('branch.about');
    Route::get('/lien-he', [App\Http\Controllers\BranchController::class, 'contact'])->name('branch.contact');
    Route::post('/lien-he', [App\Http\Controllers\BranchController::class, 'contactSend'])->name('branch.contact.send')->middleware('throttle:5,1');

    // Đăng nhập / Đăng ký
    Route::get('/dang-nhap', [App\Http\Controllers\BranchController::class, 'showLogin'])->name('branch.login');
    Route::post('/dang-nhap', [App\Http\Controllers\BranchController::class, 'login'])->name('branch.login.post');
    Route::get('/dang-ky', [App\Http\Controllers\BranchController::class, 'showRegister'])->name('branch.register');
    Route::post('/dang-ky', [App\Http\Controllers\BranchController::class, 'register'])->name('branch.register.post');
    Route::post('/dang-xuat', [App\Http\Controllers\BranchController::class, 'logout'])->name('branch.logout');

    // Đặt vé (booking form)
    Route::get('/dat-ve/{id}', [App\Http\Controllers\BranchController::class, 'bookingForm'])->name('branch.booking.form');
    Route::post('/them-gio-hang', [App\Http\Controllers\BranchController::class, 'addToCart'])->name('branch.cart.add');
    Route::get('/them-gio-hang-nhanh/{id}', [App\Http\Controllers\BranchController::class, 'quickAddToCart'])->name('branch.cart.add.quick');

    // Giỏ hàng + Thanh toán
    Route::get('/gio-hang', [App\Http\Controllers\BranchController::class, 'cart'])->name('branch.cart');
    Route::get('/gio-hang/xoa/{id}', [App\Http\Controllers\BranchController::class, 'removeCart'])->name('branch.cart.remove');
    Route::get('/gio-hang/xoa-het', [App\Http\Controllers\BranchController::class, 'emptyCart'])->name('branch.cart.empty');
    Route::patch('/gio-hang/cap-nhat', [App\Http\Controllers\BranchController::class, 'updateCart'])->name('branch.cart.update');
    Route::post('/thanh-toan', [App\Http\Controllers\BranchController::class, 'checkout'])->name('branch.checkout');
    Route::get('/thanh-toan/chuyen-khoan/{id}', [App\Http\Controllers\BranchController::class, 'bankingPaymentPage'])->name('branch.payment.banking');

    // Các route công khai cần subdomain
    Route::get('/slots', [App\Http\Controllers\BranchController::class, 'getSlots'])->name('branch.slots');
    Route::get('/api/order-status/{id}', [App\Http\Controllers\CheckoutController::class, 'orderStatus']);

    Route::get('/yeu-thich', [App\Http\Controllers\BranchController::class, 'wishlist'])->name('branch.wishlist')->middleware('auth');
    Route::get('/cai-dat', [App\Http\Controllers\BranchController::class, 'settings'])->name('branch.settings')->middleware('auth');
    Route::post('/cai-dat/cap-nhat-ui', [App\Http\Controllers\BranchController::class, 'updateUISettings'])->name('branch.settings.ui')->middleware('auth');
    Route::post('/cai-dat/doi-mat-khau', [App\Http\Controllers\BranchController::class, 'updatePassword'])->name('branch.settings.password')->middleware('auth');

    Route::get('/danh-sach-ve', [App\Http\Controllers\BranchController::class, 'shop'])->name('branch.shop');

    Route::get('/co-so/{slug}', [LocationLandingController::class, 'show'])->name('location.landing');
    Route::get('/he-thong-co-so', [LocationLandingController::class, 'index'])->name('location.index');

    // Tài khoản (cần đăng nhập)
    Route::middleware(['auth'])->group(function () {
        Route::get('/tai-khoan', [App\Http\Controllers\BranchController::class, 'profile'])->name('branch.profile');
        Route::post('/tai-khoan/cap-nhat', [App\Http\Controllers\BranchController::class, 'updateProfile'])->name('branch.profile.update');
        Route::post('/tai-khoan/avatar', [App\Http\Controllers\BranchController::class, 'updateAvatar'])->name('branch.profile.avatar');
        Route::get('/tai-khoan/don-hang/{orderId}', [App\Http\Controllers\BranchController::class, 'orderDetail'])->name('branch.profile.order.detail');
    });
});

/*
|--------------------------------------------------------------------------
| SEPAY WEBHOOK — không cần auth, không cần CSRF
|--------------------------------------------------------------------------
*/
// Chỉ khai báo 1 lần (bỏ bản trùng ở cuối file cũ)
Route::post('/webhook/sepay', [WalletController::class, 'sepayWebhook'])
    ->name('webhook.sepay');
// Khi đã cài middleware sepay.ip thì thêm: ->middleware('sepay.ip');

/*
|--------------------------------------------------------------------------
| 1. KHU VỰC ADMIN
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {

    Route::name('admin.')->group(function () {

        // Dashboard
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'index']);
        Route::get('/export/orders', [AdminController::class, 'exportOrders'])->name('export.orders');

        // Quản lý khung giờ
        Route::get('/slots', [SlotController::class, 'index'])->name('slots.index');
        Route::post('/slots/generate', [SlotController::class, 'generate'])->name('slots.generate');
        Route::post('/slots/store-single', [SlotController::class, 'storeSingle'])->name('slots.store-single');
        Route::put('/slots/{id}', [SlotController::class, 'update'])->name('slots.update');
        Route::delete('/slots/{id}', [SlotController::class, 'destroy'])->name('slots.destroy');
        Route::get('/slots/{id}/customers', [SlotController::class, 'getCustomers'])->name('slots.customers');

        // Quản lý vé — dùng AdminTicketController
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/', [AdminTicketController::class, 'index'])->name('index');
            Route::get('/create', [AdminTicketController::class, 'create'])->name('create');
            Route::post('/store', [AdminTicketController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AdminTicketController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AdminTicketController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AdminTicketController::class, 'destroy'])->name('delete');
        });

        // Xóa ảnh gallery — dùng AdminTicketController (đã fix từ TicketController)
        Route::delete('/tickets/{id}/remove-gallery-image', [AdminTicketController::class, 'removeGalleryImage'])
            ->name('tickets.remove_gallery');

        // Quản lý đơn hàng
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::put('/update-status/{id}', [BookingController::class, 'updateStatus'])->name('updateStatus');
            Route::delete('/delete/{id}', [BookingController::class, 'destroy'])->name('delete');
        });

        // Quản lý người dùng
        Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
        Route::delete('/users/delete/{id}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::post('/users/{id}/ban', [AdminController::class, 'toggleBan'])->name('users.ban');
        Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.updateRole');

        // Quản lý liên hệ
        Route::get('/contacts', [AdminController::class, 'listContacts'])->name('contacts');
        Route::delete('/contacts/{id}', [AdminController::class, 'deleteContact'])->name('contacts.delete');
        Route::post('/contacts/reply/{id}', [AdminController::class, 'replyContact'])->name('contacts.reply');

        // Mã giảm giá & Cơ sở
        Route::resource('coupons', CouponController::class);
        Route::resource('locations', LocationController::class)->except(['show']);

        // Quản lý đánh giá
        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('delete');
            Route::post('/{id}/approve', [ReviewController::class, 'approve'])->name('approve');
        });

        // Audit log — chỉ super_admin
        Route::get('/audit-log', function () {
            abort_unless(auth()->user()->role === 'super_admin', 403);
            $logs = \App\Models\AdminLog::with('admin')
                ->orderBy('created_at', 'desc')
                ->paginate(50);
            return view('admin.audit_log', compact('logs'));
        })->name('audit_log');

        
Route::get('/settings', [AdminSettingsController::class, 'adminIndex'])->name('settings.index');
Route::post('/settings/update', [AdminSettingsController::class, 'adminUpdate'])->name('settings.update');
     
});
});
/*
|--------------------------------------------------------------------------
| 2. CÁC ROUTE KHÁCH HÀNG (Client)
|--------------------------------------------------------------------------
*/

// Đổi ngôn ngữ
Route::get('/lang/{locale}', [ProfileController::class, 'switchLanguage'])->name('lang.switch');

// Trang chủ & Sản phẩm — dùng TicketController (client), KHÔNG phải AdminTicketController
Route::get('/', [TicketController::class, 'index'])->name('home');
Route::get('/tro-choi/{id}', [TicketController::class, 'show'])->name('ticket.show');
Route::get('/gioi-thieu', [TicketController::class, 'about'])->name('about');
Route::get('/danh-sach-ve', [TicketController::class, 'shop'])->name('ticket.shop');



// Giỏ hàng — dùng DELETE thay GET để tránh prefetch/crawler xóa nhầm
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::delete('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/gio-hang/xoa-het', [CartController::class, 'clear'])->name('cart.clear');
Route::patch('/gio-hang/cap-nhat', [CartController::class, 'update'])->name('cart.update');
Route::get('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('cart.add');

// Booking flow
Route::get('/booking/nhap-thong-tin/{id}', [CheckoutController::class, 'bookingForm'])->name('booking.form');
Route::post('/booking/xac-nhan', [CheckoutController::class, 'confirmToCart'])->name('booking.confirm');

// Liên hệ
Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
Route::post('/lien-he', [ContactController::class, 'send'])->name('contact.send')
    ->middleware('throttle:5,1');

// AI Chat
Route::post('/ai-chat', [ChatAIController::class, 'chat'])->name('ai.chat')
    ->middleware('throttle:20,1');

// API nội bộ
Route::get('/api/check-order-status/{id}', [CheckoutController::class, 'checkOrderStatus'])->middleware('auth');
Route::get('/api/order-status/{id}', [CheckoutController::class, 'orderStatus']);
Route::get('/api/get-slots', [CheckoutController::class, 'getSlots'])->name('api.get_slots');

// Trang FAQ & Yêu thích (public)
Route::get('/huong-dan', function () {
    return view('faq');
})->name('faq');

// Quét QR vé
Route::get('/scan-ticket/{id}', [ProfileController::class, 'scanTicket'])->name('ticket.scan');

// Trang đặt vé thành công
Route::get('/dat-ve-thanh-cong', [CheckoutController::class, 'bookingSuccess'])->name('booking.success');

/*
|--------------------------------------------------------------------------
| 3. KHU VỰC ĐĂNG NHẬP / ĐĂNG KÝ (Guest only)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('guest')->group(function () {
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| 4. THANH TOÁN (Guest + Auth)
|--------------------------------------------------------------------------
*/

// finalPayment yêu cầu đăng nhập để tránh đơn hàng không có chủ sở hữu
Route::post('/thanh-toan/chot-don', [CheckoutController::class, 'finalPayment'])
    ->name('payment.final');

Route::get('/thanh-toan/chuyen-khoan/{id}', [CheckoutController::class, 'bankingPaymentPage'])->name('payment.banking');
// Route checkout.banking đã được hợp nhất vào payment.banking ở trên (tránh trùng lặp)
Route::get('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
Route::post('/checkout/check-coupon', [CheckoutController::class, 'checkCoupon'])->name('check.coupon');

/*
|--------------------------------------------------------------------------
| 5. KHU VỰC NGƯỜI DÙNG ĐÃ ĐĂNG NHẬP
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Email verification
    Route::get('/email/verify', function () {
        return view('auth.verify_email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')
            ->with('success', '✅ Email đã được xác nhận! Chào mừng bạn đến Holomia VR.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Email xác nhận đã được gửi lại!');
    })->middleware('throttle:3,1')->name('verification.send');

    // Ví Holomia
    Route::get('/vi/nap-tien', [WalletController::class, 'topupPage'])->name('wallet.topup');
    Route::get('/vi/kiem-tra-nap', [WalletController::class, 'checkTopupStatus'])->name('wallet.check');

    // Hoàn tiền
    Route::get('/order/refund/{id}', [CheckoutController::class, 'refundOrder'])->name('order.refund');

    // Hồ sơ cá nhân
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::get('/profile/order/{id}', [ProfileController::class, 'showOrder'])->name('profile.order.detail');

    // Wishlist
    Route::post('/wishlist/toggle/{id}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');

    // Cài đặt
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/ui', [SettingsController::class, 'saveUI'])->name('settings.ui');
    Route::post('/settings/language', [SettingsController::class, 'saveLanguage'])->name('settings.language');
    Route::post('/settings/notifications', [SettingsController::class, 'saveNotifications'])->name('settings.notifications');
    Route::post('/settings/privacy', [SettingsController::class, 'savePrivacy'])->name('settings.privacy');
    Route::post('/settings/payment', [SettingsController::class, 'savePayment'])->name('settings.payment');
    Route::post('/settings/accessibility', [SettingsController::class, 'saveAccessibility'])->name('settings.accessibility');
    Route::post('/settings/password', [SettingsController::class, 'savePassword'])->name('settings.password');
    Route::post('/settings/logout-all', [SettingsController::class, 'logoutAllDevices'])->name('settings.logout_all');
    Route::post('/settings/delete-account', [SettingsController::class, 'deleteAccount'])->name('settings.delete_account');
    Route::post('/settings/unlink-social', [SettingsController::class, 'unlinkSocial'])->name('settings.unlink_social');

    // Đăng xuất
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Đánh giá
    Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

});

/*
|--------------------------------------------------------------------------
| 6. SOCIAL LOGIN
|--------------------------------------------------------------------------
*/
Route::get('auth/{provider}/redirect', [\App\Http\Controllers\SocialLoginController::class, 'redirect'])->name('social.redirect');
Route::get('auth/{provider}/callback', [\App\Http\Controllers\SocialLoginController::class, 'callback'])->name('social.callback');
