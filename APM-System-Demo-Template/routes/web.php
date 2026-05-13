<?php

use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\LeaveRequestController;
use App\Http\Controllers\Admin\PayslipController as AdminPayslipController;
use App\Http\Controllers\Admin\CashAdvanceSummaryController;
use App\Http\Controllers\Admin\CashAdvanceLiquidationController as AdminCashAdvanceLiquidationController;
use App\Http\Controllers\Admin\CashAdvanceItemController as AdminCashAdvanceItemController;
use App\Http\Controllers\Admin\CashAdvancePaymentController;
use App\Http\Controllers\Admin\CashAdvanceRequestController;
use App\Http\Controllers\Admin\CostSheetController;
use App\Http\Controllers\Admin\RecordMonitoringController;
use App\Http\Controllers\Admin\ReimbursableVoucherController;
use App\Http\Controllers\CashAdvanceController;
use App\Http\Controllers\CashAdvanceLiquidationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileCorrectionController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\TimeOffController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\LiveUpdateController;
use App\Http\Controllers\Admin\AccessControlController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\UserActivityLogController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/support', [SupportTicketController::class, 'create'])->name('support.create');
Route::post('/support', [SupportTicketController::class, 'store'])->name('support.store');

Route::middleware(['auth', 'email.otp'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'update'])->name('onboarding.update');
});

Route::middleware(['auth', 'email.otp', 'onboarding', 'activity.log'])->group(function () {
    Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-profile', [ProfileController::class, 'show'])->name('my-profile')->middleware('access:my-profile');
    Route::post('/my-profile/photo', [ProfileController::class, 'updatePhoto'])->name('my-profile.photo');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::get('/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::get('/change-password', [PasswordController::class, 'edit'])->name('change-password.edit');
    Route::put('/change-password', [PasswordController::class, 'update'])->name('change-password.update');
    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips')->middleware('access:payslips');
    Route::get('/time-off', [TimeOffController::class, 'index'])->name('timeoff')->middleware('access:leave-form');
    Route::post('/time-off', [TimeOffController::class, 'store'])->name('timeoff.store')->middleware('access:leave-form');
    Route::get('/cash-advances', [CashAdvanceController::class, 'index'])->name('cash-advances')->middleware('access:cash-advances');
    Route::post('/cash-advances', [CashAdvanceController::class, 'store'])->name('cash-advances.store')->middleware('access:cash-advances');
    Route::post('/cash-advances/liquidations', [CashAdvanceLiquidationController::class, 'store'])->name('cash-advances.liquidations.store')->middleware('access:cash-advances');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing')->middleware('access:billing');
    Route::get('/billing/storage', [BillingController::class, 'masterStorage'])->name('billing.storage')->middleware('access:billing');
    Route::get('/billing/create', [BillingController::class, 'create'])->name('billing.create')->middleware('access:billing');
    Route::match(['post', 'put'], '/billing/draft', [BillingController::class, 'draft'])->name('billing.draft')->middleware('access:billing');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store')->middleware('access:billing');
    Route::get('/billing/documents/{billingStatement}/edit', [BillingController::class, 'edit'])->name('billing.edit')->middleware('access:billing');
    Route::put('/billing/documents/{billingStatement}', [BillingController::class, 'update'])->name('billing.update')->middleware('access:billing');
    Route::get('/billing/documents', [BillingController::class, 'documents'])->name('billing.documents')->middleware('access:billing');
    Route::get('/billing/documents/{billingStatement}/pdf', [BillingController::class, 'downloadPdf'])->name('billing.pdf')->middleware('access:billing');
    Route::post('/billing/documents/{billingStatement}/attachments', [BillingController::class, 'storeBillingStatementAttachments'])->name('billing.attachments.store')->middleware('access:billing');
    Route::get('/billing/documents/{billingStatement}', [BillingController::class, 'show'])->name('billing.show')->middleware('access:billing');
    Route::get('/service-invoices', [BillingController::class, 'serviceIndex'])->name('service-invoices')->middleware('access:billing');
    Route::get('/service-invoices/create', [BillingController::class, 'createService'])->name('service-invoices.create')->middleware('access:billing');
    Route::match(['post', 'put'], '/service-invoices/draft', [BillingController::class, 'draftService'])->name('service-invoices.draft')->middleware('access:billing');
    Route::post('/service-invoices', [BillingController::class, 'storeService'])->name('service-invoices.store')->middleware('access:billing');
    Route::get('/service-invoices/documents', [BillingController::class, 'serviceDocuments'])->name('service-invoices.documents')->middleware('access:billing');
    Route::get('/service-invoices/documents/{serviceInvoice}/pdf', [BillingController::class, 'downloadServicePdf'])->name('service-invoices.pdf')->middleware('access:billing');
    Route::post('/service-invoices/documents/{serviceInvoice}/attachments', [BillingController::class, 'storeServiceInvoiceAttachments'])->name('service-invoices.attachments.store')->middleware('access:billing');
    Route::get('/service-invoices/documents/{serviceInvoice}', [BillingController::class, 'showService'])->name('service-invoices.show')->middleware('access:billing');
    Route::get('/service-invoices/documents/{serviceInvoice}/edit', [BillingController::class, 'editService'])->name('service-invoices.edit')->middleware('access:billing');
    Route::put('/service-invoices/documents/{serviceInvoice}', [BillingController::class, 'updateService'])->name('service-invoices.update')->middleware('access:billing');
    Route::get('/billing/notes', [BillingController::class, 'notes'])->name('billing.notes')->middleware('access:billing');
    Route::get('/billing/notes/create', [BillingController::class, 'createNote'])->name('billing.notes.create')->middleware('access:billing');
    Route::post('/billing/notes', [BillingController::class, 'storeNote'])->name('billing.notes.store')->middleware('access:billing');
    Route::get('/billing/notes/documents', [BillingController::class, 'noteDocuments'])->name('billing.notes.documents')->middleware('access:billing');
    Route::post('/billing/notes/{debitCreditNote}/attachments', [BillingController::class, 'storeDebitCreditNoteAttachments'])->name('billing.notes.attachments.store')->middleware('access:billing');
    Route::get('/billing/notes/{debitCreditNote}/edit', [BillingController::class, 'editNote'])->name('billing.notes.edit')->middleware('access:billing');
    Route::put('/billing/notes/{debitCreditNote}', [BillingController::class, 'updateNote'])->name('billing.notes.update')->middleware('access:billing');
    Route::get('/billing/notes/{debitCreditNote}', [BillingController::class, 'showNote'])->name('billing.notes.show')->middleware('access:billing');
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents')->middleware('access:documents');
    Route::get('/profile-corrections', [ProfileCorrectionController::class, 'index'])->name('profile-corrections.index')->middleware('access:profile-corrections');
    Route::post('/profile-corrections', [ProfileCorrectionController::class, 'store'])->name('profile-corrections.store')->middleware('access:profile-corrections');
    Route::get('/support/ticket', [SupportTicketController::class, 'createEmployee'])->name('support.employee');
    Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');
    Route::get('/live/approvals-stamp', [LiveUpdateController::class, 'approvalStamp'])->name('live.approvals-stamp');
    Route::post('/job-orders/{jobOrder}/attachments', [JobOrderController::class, 'storeSavedAttachments'])->name('job-orders.attachments.store')->middleware('access:job-orders');
    Route::post('/job-orders/{jobOrder}/server-scans', [JobOrderController::class, 'attachServerScan'])->name('job-orders.server-scans.attach')->middleware('access:job-orders');
    Route::get('/job-orders/{jobOrder}/package', [JobOrderController::class, 'downloadPackage'])->name('job-orders.package.download')->middleware('access:job-orders');
    Route::post('/job-orders/{jobOrder}/package/email', [JobOrderController::class, 'emailPackage'])->name('job-orders.package.email')->middleware('access:job-orders');
    Route::get('/job-orders/check-number', [JobOrderController::class, 'checkNumber'])->name('job-orders.check-number')->middleware('access:job-orders');
    Route::resource('job-orders', JobOrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update'])->middleware('access:job-orders');
});

Route::prefix('documentation')->name('documentation.')->middleware(['auth', 'email.otp', 'onboarding', 'activity.log'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents')->middleware('access:documents');
});

Route::prefix('operations')->name('operations.')->middleware(['auth', 'email.otp', 'onboarding', 'activity.log'])->group(function () {
    Route::post('/job-orders/{jobOrder}/attachments', [JobOrderController::class, 'storeSavedAttachments'])->name('job-orders.attachments.store')->middleware('access:job-orders');
    Route::post('/job-orders/{jobOrder}/server-scans', [JobOrderController::class, 'attachServerScan'])->name('job-orders.server-scans.attach')->middleware('access:job-orders');
    Route::get('/job-orders/{jobOrder}/package', [JobOrderController::class, 'downloadPackage'])->name('job-orders.package.download')->middleware('access:job-orders');
    Route::post('/job-orders/{jobOrder}/package/email', [JobOrderController::class, 'emailPackage'])->name('job-orders.package.email')->middleware('access:job-orders');
    Route::get('/job-orders/check-number', [JobOrderController::class, 'checkNumber'])->name('job-orders.check-number')->middleware('access:job-orders');
    Route::resource('job-orders', JobOrderController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update'])->middleware('access:job-orders');
});

Route::prefix('billing')->name('billing.')->middleware(['auth', 'email.otp', 'onboarding', 'activity.log'])->group(function () {
    Route::get('/service-invoices', [BillingController::class, 'serviceIndex'])->name('service-invoices')->middleware('access:billing');
    Route::get('/service-invoices/create', [BillingController::class, 'createService'])->name('service-invoices.create')->middleware('access:billing');
    Route::match(['post', 'put'], '/service-invoices/draft', [BillingController::class, 'draftService'])->name('service-invoices.draft')->middleware('access:billing');
    Route::post('/service-invoices', [BillingController::class, 'storeService'])->name('service-invoices.store')->middleware('access:billing');
    Route::get('/service-invoices/documents', [BillingController::class, 'serviceDocuments'])->name('service-invoices.documents')->middleware('access:billing');
    Route::get('/service-invoices/documents/{serviceInvoice}/pdf', [BillingController::class, 'downloadServicePdf'])->name('service-invoices.pdf')->middleware('access:billing');
    Route::post('/service-invoices/documents/{serviceInvoice}/attachments', [BillingController::class, 'storeServiceInvoiceAttachments'])->name('service-invoices.attachments.store')->middleware('access:billing');
    Route::get('/service-invoices/documents/{serviceInvoice}', [BillingController::class, 'showService'])->name('service-invoices.show')->middleware('access:billing');
    Route::get('/service-invoices/documents/{serviceInvoice}/edit', [BillingController::class, 'editService'])->name('service-invoices.edit')->middleware('access:billing');
    Route::put('/service-invoices/documents/{serviceInvoice}', [BillingController::class, 'updateService'])->name('service-invoices.update')->middleware('access:billing');
});

Route::prefix('accounting')->name('accounting.')->middleware(['auth', 'email.otp', 'onboarding', 'activity.log'])->group(function () {
    Route::get('/cash-advances', [CashAdvanceRequestController::class, 'index'])->name('cash-advances.index')->middleware('access:admin-cash-advance-approvals');
    Route::post('/cash-advances', [CashAdvanceRequestController::class, 'store'])->name('cash-advances.store')->middleware('access:admin-cash-advance-approvals,admin-ca-summary');
    Route::delete('/cash-advances/{cashAdvanceRequest}', [CashAdvanceRequestController::class, 'destroy'])->name('cash-advances.destroy')->middleware('access:admin-ca-summary');
    Route::patch('/cash-advances/{cashAdvanceRequest}', [CashAdvanceRequestController::class, 'update'])->name('cash-advances.update')->middleware('access:admin-cash-advance-approvals');
    Route::patch('/cash-advances/{cashAdvanceRequest}/personal-paid', [CashAdvanceRequestController::class, 'updatePersonalPaid'])->name('cash-advances.personal-paid')->middleware('access:admin-cash-advance-approvals,admin-ca-summary');
    Route::get('/cash-advances/liquidation-approvals', [AdminCashAdvanceLiquidationController::class, 'index'])->name('cash-advances.liquidations.approvals')->middleware('access:admin-cash-advance-approvals,admin-liquidation-approvals');
    Route::patch('/cash-advances/liquidations/{cashAdvanceLiquidation}/review', [AdminCashAdvanceLiquidationController::class, 'review'])->name('cash-advances.liquidations.review')->middleware('access:admin-cash-advance-approvals,admin-liquidation-approvals');
    Route::get('/cash-advances/summary', [CashAdvanceSummaryController::class, 'index'])->name('cash-advances.summary')->middleware('access:admin-ca-summary');
    Route::post('/cash-advances/summary/import', [CashAdvanceSummaryController::class, 'importLedger'])->name('cash-advances.summary.import')->middleware('access:admin-ca-summary');
    Route::post('/cash-advances/liquidations', [AdminCashAdvanceLiquidationController::class, 'store'])->name('cash-advances.liquidations.store')->middleware('access:admin-ca-summary');
    Route::patch('/cash-advances/liquidations/{cashAdvanceLiquidation}', [AdminCashAdvanceLiquidationController::class, 'update'])->name('cash-advances.liquidations.update')->middleware('access:admin-ca-summary');
    Route::delete('/cash-advances/liquidations/{cashAdvanceLiquidation}', [AdminCashAdvanceLiquidationController::class, 'destroy'])->name('cash-advances.liquidations.destroy')->middleware('access:admin-ca-summary');
    Route::delete('/cash-advances/{cashAdvanceRequest}/liquidations/grouped', [AdminCashAdvanceLiquidationController::class, 'destroyGrouped'])->name('cash-advances.liquidations.destroy-grouped')->middleware('access:admin-ca-summary');
    Route::patch('/cash-advances/items/{cashAdvanceItem}', [AdminCashAdvanceItemController::class, 'update'])->name('cash-advances.items.update')->middleware('access:admin-ca-summary');
    Route::get('/cash-advances/payments', [CashAdvancePaymentController::class, 'index'])->name('cash-advances.payments')->middleware('access:admin-ca-payments');
    Route::patch('/cash-advances/payments/{cashAdvanceRequest}', [CashAdvancePaymentController::class, 'update'])->name('cash-advances.payments.update')->middleware('access:admin-ca-payments');
    Route::get('/container-deposits', [BillingController::class, 'containerDeposits'])->name('container-deposits')->middleware('access:admin-container-deposits');
    Route::get('/reimbursable-vouchers', [ReimbursableVoucherController::class, 'index'])->name('reimbursable-vouchers.index')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers/create', [ReimbursableVoucherController::class, 'create'])->name('reimbursable-vouchers.create')->middleware('access:admin-ca-summary');
    Route::post('/reimbursable-vouchers', [ReimbursableVoucherController::class, 'store'])->name('reimbursable-vouchers.store')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers/{reimbursableVoucher}/edit', [ReimbursableVoucherController::class, 'edit'])->name('reimbursable-vouchers.edit')->middleware('access:admin-ca-summary');
    Route::put('/reimbursable-vouchers/{reimbursableVoucher}', [ReimbursableVoucherController::class, 'update'])->name('reimbursable-vouchers.update')->middleware('access:admin-ca-summary');
    Route::patch('/reimbursable-vouchers/{reimbursableVoucher}/cancel', [ReimbursableVoucherController::class, 'cancel'])->name('reimbursable-vouchers.cancel')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers/{reimbursableVoucher}', [ReimbursableVoucherController::class, 'show'])->name('reimbursable-vouchers.show')->middleware('access:admin-ca-summary');
    Route::get('/cost-sheets', [CostSheetController::class, 'index'])->name('cost-sheets.index')->middleware('access:admin-cost-sheets');
    Route::get('/cost-sheets/create', [CostSheetController::class, 'create'])->name('cost-sheets.create')->middleware('access:admin-cost-sheets');
    Route::get('/cost-sheets/service-invoice-summary', [CostSheetController::class, 'serviceInvoiceSummary'])->name('cost-sheets.service-invoice-summary')->middleware('access:admin-cost-sheets');
    Route::get('/cost-sheets/sales-report', [CostSheetController::class, 'salesReport'])->name('cost-sheets.sales-report')->middleware('access:admin-cost-sheet-sales-report');
    Route::get('/record-monitoring', [RecordMonitoringController::class, 'index'])->name('record-monitoring.index')->middleware('access:admin-record-monitoring');
    Route::get('/record-monitoring/create', [RecordMonitoringController::class, 'create'])->name('record-monitoring.create')->middleware('access:admin-record-monitoring');
    Route::post('/record-monitoring', [RecordMonitoringController::class, 'store'])->name('record-monitoring.store')->middleware('access:admin-record-monitoring');
    Route::get('/record-monitoring/{recordMonitoringEntry}/edit', [RecordMonitoringController::class, 'edit'])->name('record-monitoring.edit')->middleware('access:admin-record-monitoring');
    Route::put('/record-monitoring/{recordMonitoringEntry}', [RecordMonitoringController::class, 'update'])->name('record-monitoring.update')->middleware('access:admin-record-monitoring');
    Route::patch('/record-monitoring/{recordMonitoringEntry}/quick-update', [RecordMonitoringController::class, 'quickUpdate'])->name('record-monitoring.quick-update')->middleware('access:admin-record-monitoring');
    Route::post('/record-monitoring/import', [RecordMonitoringController::class, 'importWorkbook'])->name('record-monitoring.import')->middleware('access:admin-record-monitoring');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'email.otp', 'onboarding', 'activity.log', 'admin.audit'])->group(function () {
    Route::get('/access-control', [AccessControlController::class, 'index'])->name('access-control.index');
    Route::get('/access-control/{user}', [AccessControlController::class, 'edit'])->name('access-control.edit');
    Route::put('/access-control/{user}', [AccessControlController::class, 'update'])->name('access-control.update');
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index')->middleware('access:admin-reports');
    Route::get('/activity-logs', [UserActivityLogController::class, 'index'])->name('activity-logs.index')->middleware('access:admin-reports');
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index')->middleware('access:admin-clients');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create')->middleware('access:admin-clients');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store')->middleware('access:admin-clients');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit')->middleware('access:admin-clients');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update')->middleware('access:admin-clients');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy')->middleware('access:admin-clients');
    Route::post('/clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore')->middleware('access:admin-clients');
    Route::get('/support-tickets', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('support-tickets.index')->middleware('access:admin-support-tickets');
    Route::get('/support-tickets/{supportTicket}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('support-tickets.show')->middleware('access:admin-support-tickets');
    Route::patch('/support-tickets/{supportTicket}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'update'])->name('support-tickets.update')->middleware('access:admin-support-tickets');
    Route::get('/profile-corrections', [\App\Http\Controllers\Admin\ProfileCorrectionController::class, 'index'])->name('profile-corrections.index')->middleware('access:admin-profile-corrections');
    Route::patch('/profile-corrections/{profileCorrectionRequest}', [\App\Http\Controllers\Admin\ProfileCorrectionController::class, 'update'])->name('profile-corrections.update')->middleware('access:admin-profile-corrections');
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index')->middleware('access:admin-employees');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create')->middleware('access:admin-employees');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store')->middleware('access:admin-employees');
    Route::get('/employees/{user}', [EmployeeController::class, 'show'])->name('employees.show')->middleware('access:admin-employees');
    Route::get('/employees/{user}/edit', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('access:admin-employees');
    Route::put('/employees/{user}', [EmployeeController::class, 'update'])->name('employees.update')->middleware('access:admin-employees');
    Route::post('/employees/{user}/send-password-reset', [EmployeeController::class, 'sendPasswordResetLink'])->name('employees.send-password-reset')->middleware('access:admin-employees');
    Route::get('/payslips', [AdminPayslipController::class, 'index'])->name('payslips.index')->middleware('access:admin-payslips');
    Route::get('/payslips/user/{user}', [AdminPayslipController::class, 'showUser'])->name('payslips.user')->middleware('access:admin-payslips');
    Route::get('/payslips/create', [AdminPayslipController::class, 'create'])->name('payslips.create')->middleware('access:admin-payslips');
    Route::post('/payslips', [AdminPayslipController::class, 'store'])->name('payslips.store')->middleware('access:admin-payslips');
    Route::get('/payslips/{payslip}/edit', [AdminPayslipController::class, 'edit'])->name('payslips.edit')->middleware('access:admin-payslips');
    Route::put('/payslips/{payslip}', [AdminPayslipController::class, 'update'])->name('payslips.update')->middleware('access:admin-payslips');
    Route::get('/time-off', [LeaveRequestController::class, 'index'])->name('timeoff.index')->middleware('access:admin-leave-approvals');
    Route::patch('/time-off/{leaveRequest}', [LeaveRequestController::class, 'update'])->name('timeoff.update')->middleware('access:admin-leave-approvals');
    Route::get('/cash-advances', [CashAdvanceRequestController::class, 'index'])->name('cash-advances.index')->middleware('access:admin-cash-advance-approvals');
    Route::post('/cash-advances', [CashAdvanceRequestController::class, 'store'])->name('cash-advances.store')->middleware('access:admin-cash-advance-approvals,admin-ca-summary');
    Route::delete('/cash-advances/{cashAdvanceRequest}', [CashAdvanceRequestController::class, 'destroy'])->name('cash-advances.destroy')->middleware('access:admin-ca-summary');
    Route::patch('/cash-advances/{cashAdvanceRequest}', [CashAdvanceRequestController::class, 'update'])->name('cash-advances.update')->middleware('access:admin-cash-advance-approvals');
    Route::patch('/cash-advances/{cashAdvanceRequest}/personal-paid', [CashAdvanceRequestController::class, 'updatePersonalPaid'])->name('cash-advances.personal-paid')->middleware('access:admin-cash-advance-approvals,admin-ca-summary');
    Route::get('/cash-advances/liquidation-approvals', [AdminCashAdvanceLiquidationController::class, 'index'])->name('cash-advances.liquidations.approvals')->middleware('access:admin-cash-advance-approvals,admin-liquidation-approvals');
    Route::patch('/cash-advances/liquidations/{cashAdvanceLiquidation}/review', [AdminCashAdvanceLiquidationController::class, 'review'])->name('cash-advances.liquidations.review')->middleware('access:admin-cash-advance-approvals,admin-liquidation-approvals');
    Route::get('/cash-advances/summary', [CashAdvanceSummaryController::class, 'index'])->name('cash-advances.summary')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers', [ReimbursableVoucherController::class, 'index'])->name('reimbursable-vouchers.index')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers/create', [ReimbursableVoucherController::class, 'create'])->name('reimbursable-vouchers.create')->middleware('access:admin-ca-summary');
    Route::post('/reimbursable-vouchers', [ReimbursableVoucherController::class, 'store'])->name('reimbursable-vouchers.store')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers/{reimbursableVoucher}/edit', [ReimbursableVoucherController::class, 'edit'])->name('reimbursable-vouchers.edit')->middleware('access:admin-ca-summary');
    Route::put('/reimbursable-vouchers/{reimbursableVoucher}', [ReimbursableVoucherController::class, 'update'])->name('reimbursable-vouchers.update')->middleware('access:admin-ca-summary');
    Route::patch('/reimbursable-vouchers/{reimbursableVoucher}/cancel', [ReimbursableVoucherController::class, 'cancel'])->name('reimbursable-vouchers.cancel')->middleware('access:admin-ca-summary');
    Route::get('/reimbursable-vouchers/{reimbursableVoucher}', [ReimbursableVoucherController::class, 'show'])->name('reimbursable-vouchers.show')->middleware('access:admin-ca-summary');
    Route::get('/cost-sheets', [CostSheetController::class, 'index'])->name('cost-sheets.index')->middleware('access:admin-cost-sheets');
    Route::get('/cost-sheets/create', [CostSheetController::class, 'create'])->name('cost-sheets.create')->middleware('access:admin-cost-sheets');
    Route::get('/cost-sheets/service-invoice-summary', [CostSheetController::class, 'serviceInvoiceSummary'])->name('cost-sheets.service-invoice-summary')->middleware('access:admin-cost-sheets');
    Route::get('/cost-sheets/sales-report', [CostSheetController::class, 'salesReport'])->name('cost-sheets.sales-report')->middleware('access:admin-cost-sheet-sales-report');
    Route::get('/record-monitoring', [RecordMonitoringController::class, 'index'])->name('record-monitoring.index')->middleware('access:admin-record-monitoring');
    Route::get('/record-monitoring/create', [RecordMonitoringController::class, 'create'])->name('record-monitoring.create')->middleware('access:admin-record-monitoring');
    Route::post('/record-monitoring', [RecordMonitoringController::class, 'store'])->name('record-monitoring.store')->middleware('access:admin-record-monitoring');
    Route::get('/record-monitoring/{recordMonitoringEntry}/edit', [RecordMonitoringController::class, 'edit'])->name('record-monitoring.edit')->middleware('access:admin-record-monitoring');
    Route::put('/record-monitoring/{recordMonitoringEntry}', [RecordMonitoringController::class, 'update'])->name('record-monitoring.update')->middleware('access:admin-record-monitoring');
    Route::patch('/record-monitoring/{recordMonitoringEntry}/quick-update', [RecordMonitoringController::class, 'quickUpdate'])->name('record-monitoring.quick-update')->middleware('access:admin-record-monitoring');
    Route::post('/record-monitoring/import', [RecordMonitoringController::class, 'importWorkbook'])->name('record-monitoring.import')->middleware('access:admin-record-monitoring');
    Route::post('/cash-advances/summary/import', [CashAdvanceSummaryController::class, 'importLedger'])->name('cash-advances.summary.import')->middleware('access:admin-ca-summary');
    Route::post('/cash-advances/liquidations', [AdminCashAdvanceLiquidationController::class, 'store'])->name('cash-advances.liquidations.store')->middleware('access:admin-ca-summary');
    Route::patch('/cash-advances/liquidations/{cashAdvanceLiquidation}', [AdminCashAdvanceLiquidationController::class, 'update'])->name('cash-advances.liquidations.update')->middleware('access:admin-ca-summary');
    Route::delete('/cash-advances/liquidations/{cashAdvanceLiquidation}', [AdminCashAdvanceLiquidationController::class, 'destroy'])->name('cash-advances.liquidations.destroy')->middleware('access:admin-ca-summary');
    Route::delete('/cash-advances/{cashAdvanceRequest}/liquidations/grouped', [AdminCashAdvanceLiquidationController::class, 'destroyGrouped'])->name('cash-advances.liquidations.destroy-grouped')->middleware('access:admin-ca-summary');
    Route::patch('/cash-advances/items/{cashAdvanceItem}', [AdminCashAdvanceItemController::class, 'update'])->name('cash-advances.items.update')->middleware('access:admin-ca-summary');
    Route::get('/cash-advances/payments', [CashAdvancePaymentController::class, 'index'])->name('cash-advances.payments')->middleware('access:admin-ca-payments');
    Route::patch('/cash-advances/payments/{cashAdvanceRequest}', [CashAdvancePaymentController::class, 'update'])->name('cash-advances.payments.update')->middleware('access:admin-ca-payments');
    Route::view('/reports', 'admin.reports')->name('reports')->middleware('access:admin-reports');
});

require __DIR__.'/auth.php';
