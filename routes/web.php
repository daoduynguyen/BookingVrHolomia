<?php
use App\Http\Controllers\Admin\AdminChatbotController;
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

    Route::get('/thong-tin', [App\Http\Controllers\BranchController::class, 'info'])->name('branch.info');
    Route::post('/lien-he', [App\Http\Controllers\BranchController::class, 'contactSend'])->name('branch.contact.send')->middleware('throttle:5,1');

    // Đăng nhập / Đăng ký
    Route::get('/dang-nhap', [App\Http\Controllers\BranchController::class, 'showLogin'])->name('branch.login');
    Route::post('/dang-nhap', [App\Http\Controllers\BranchController::class, 'login'])->name('branch.login.post');
    Route::get('/dang-ky', [App\Http\Controllers\BranchController::class, 'showRegister'])->name('branch.register');
    Route::post('/dang-ky', [App\Http\Controllers\BranchController::class, 'register'])->name('branch.register.post');
    Route::post('/dang-xuat', [App\Http\Controllers\BranchController::class, 'logout'])->name('branch.logout');

    //Vocher
    Route::post('/apply-coupon', [App\Http\Controllers\BranchController::class, 'applyCoupon'])->name('branch.coupon.apply');
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

        // Hoàn tiền và Đánh giá (Branch)
        Route::post('/order/refund/{id}', [App\Http\Controllers\CheckoutController::class, 'refundOrder'])->name('branch.order.refund');
        Route::post('/reviews', [App\Http\Controllers\ReviewController::class, 'store'])->name('branch.reviews.store');
    });
});

Route::get('/test-mail', function () {
    try {
        $order = \App\Models\Order::latest()->first();
        if (!$order)
            return "Order not found";

        \Illuminate\Support\Facades\Mail::to('test@example.com')->send(new \App\Mail\BookingConfirmedMail($order));
        return "Gửi mail thành công cho đơn " . $order->id . "!";
    } catch (\Exception $e) {
        return "Lỗi: " . $e->getMessage() . " ở dòng " . $e->getLine() . " file " . $e->getFile();
    }
});

Route::get('/add-money', function () {
    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user) {
        $user->increment('balance', 5000000);
        return "Đại gia đã được buff 5,000,000 VNĐ! Số dư ví hiện tại là: " . number_format($user->balance) . " VNĐ. Hãy cẩn thận khi tiêu tiền!";
    }
    return "Vui lòng đăng nhập trước!";
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

    // Quản lý Chatbot AI
    Route::prefix('chatbot')->name('chatbot.')->group(function() {
        Route::get('/', [AdminChatbotController::class, 'index'])->name('index');
     
        // Quản lý Cache Q&A
        Route::prefix('cache')->name('cache.')->group(function () {
            Route::get('/',                     [AdminChatbotController::class, 'cacheIndex'])->name('index');
            Route::patch('/{id}/approve',       [AdminChatbotController::class, 'approveCache'])->name('approve');
            Route::get('/{id}/edit',            [AdminChatbotController::class, 'editCache'])->name('edit');
            Route::put('/{id}',                 [AdminChatbotController::class, 'updateCache'])->name('update');
            Route::delete('/{id}',              [AdminChatbotController::class, 'destroyCache'])->name('destroy');
        });
     
        // Quản lý Knowledge Base
        Route::prefix('knowledge')->name('knowledge.')->group(function () {
            Route::get('/',                     [AdminChatbotController::class, 'knowledgeIndex'])->name('index');
            Route::get('/create',               [AdminChatbotController::class, 'createKnowledge'])->name('create');
            Route::post('/',                    [AdminChatbotController::class, 'storeKnowledge'])->name('store');
            Route::get('/{id}/edit',            [AdminChatbotController::class, 'editKnowledge'])->name('edit');
            Route::put('/{id}',                 [AdminChatbotController::class, 'updateKnowledge'])->name('update');
            Route::delete('/{id}',              [AdminChatbotController::class, 'destroyKnowledge'])->name('destroy');
            Route::patch('/{id}/toggle',        [AdminChatbotController::class, 'toggleKnowledge'])->name('toggle');
        });
 });
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

        Route::get('/users/edit/{id}', [AdminController::class, 'editUser'])->name('users.edit');
        Route::put('/users/update/{id}', [AdminController::class, 'updateUser'])->name('users.update');

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
Route::get('/thong-tin', [TicketController::class, 'info'])->name('info');
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
// Liên hệ (POST)
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
// Public token-based scan (fallback for email QR)
Route::get('/scan-ticket/token/{token}', [ProfileController::class, 'scanByToken'])->name('ticket.scan.token');

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

// Admin settings QR update route
Route::post('/admin/settings/qr', [\App\Http\Controllers\Admin\AdminSettingsController::class, 'adminUpdateQr'])
    ->name('admin.settings.qr.update')
    ->middleware('auth', 'can:access-admin');

/*
|--------------------------------------------------------------------------
| 4. THANH TOÁN (Guest + Auth)
|--------------------------------------------------------------------------
*/

// payment.final đã được chuyển xuống middleware auth ở dưới

Route::get('/thanh-toan/chuyen-khoan/{id}', [CheckoutController::class, 'bankingPaymentPage'])->name('payment.banking');
// Route checkout.banking đã được hợp nhất vào payment.banking ở trên (tránh trùng lặp)
Route::get('/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
Route::post('/checkout/check-coupon', [CheckoutController::class, 'checkCoupon'])->name('check.coupon');
// Thanh toán chốt đơn
Route::post('/thanh-toan/chot-don', [CheckoutController::class, 'finalPayment'])
    ->name('payment.final');

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
    Route::post('/order/refund/{id}', [CheckoutController::class, 'refundOrder'])->name('order.refund');

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

/*
|--------------------------------------------------------------------------
| 7. POS — Bán vé tại quầy
|--------------------------------------------------------------------------
*/
Route::prefix('chi-nhanh/{subdomain}/pos')
    ->middleware(['auth', 'pos.role'])
    ->group(function () {

    // Dashboard
    Route::get('/', [\App\Http\Controllers\PosController::class, 'dashboard'])
        ->name('pos.dashboard');

    // Bán vé
    Route::get('/ban-ve/{slotId}', [\App\Http\Controllers\PosController::class, 'saleForm'])
        ->name('pos.sale.form');
    Route::post('/ban-ve', [\App\Http\Controllers\PosController::class, 'storeSale'])
        ->name('pos.sale.store');
    Route::get('/ban-ve-thanh-cong/{orderId}', [\App\Http\Controllers\PosController::class, 'saleSuccess'])
        ->name('pos.sale.success');

    // Slot detail + polling API
    Route::get('/slot/{slotId}', [\App\Http\Controllers\PosController::class, 'slotDetail'])
        ->name('pos.slot.detail');
    Route::get('/slot/{slotId}/status', [\App\Http\Controllers\PosController::class, 'slotStatus'])
        ->name('pos.slot.status');

    // Check-in QR
    Route::get('/check-in', [\App\Http\Controllers\PosController::class, 'checkinForm'])
        ->name('pos.checkin');
    Route::post('/check-in', [\App\Http\Controllers\PosController::class, 'checkinProcess'])
        ->name('pos.checkin.process');

    // Thiết bị VR
    Route::get('/thiet-bi', [\App\Http\Controllers\PosController::class, 'devices'])
        ->name('pos.devices');
    Route::post('/thiet-bi/{deviceId}/trang-thai', [\App\Http\Controllers\PosController::class, 'updateDeviceStatus'])
        ->name('pos.device.status');

    // Lịch sử giao dịch
    Route::get('/lich-su', [\App\Http\Controllers\PosController::class, 'history'])
        ->name('pos.history');

    // Huỷ vé
    Route::post('/huy-ve/{orderId}', [\App\Http\Controllers\PosController::class, 'cancelOrder'])
        ->name('pos.cancel');

    // Tìm khách hàng (AJAX)
    Route::get('/tim-khach', [\App\Http\Controllers\PosController::class, 'findCustomer'])
        ->name('pos.find.customer');

    // Ca làm việc
    Route::get('/mo-ca', [\App\Http\Controllers\PosShiftController::class, 'openForm'])
        ->name('pos.shift.open.form');
    Route::post('/mo-ca', [\App\Http\Controllers\PosShiftController::class, 'open'])
        ->name('pos.shift.open');
    Route::get('/dong-ca', [\App\Http\Controllers\PosShiftController::class, 'closeForm'])
        ->name('pos.shift.close.form');
    Route::post('/dong-ca', [\App\Http\Controllers\PosShiftController::class, 'close'])
        ->name('pos.shift.close');
    Route::get('/bao-cao-ca/{shiftId}', [\App\Http\Controllers\PosShiftController::class, 'report'])
        ->name('pos.shift.report');
});