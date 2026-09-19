<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Admin' }} — VexaHost</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono-code { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full antialiased bg-white text-slate-900" x-data="{ sidebarOpen: false, userDropdown: false }">
    <div class="h-screen w-full flex flex-col md:flex-row overflow-hidden bg-white">

        <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/30 md:hidden" @click="sidebarOpen = false" style="display:none;"></div>

        <!-- Sidebar (Wide w-80, Stationary on Desktop, No Category Headings) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-80 bg-white border-r border-slate-200 flex flex-col transition-transform duration-200 md:static md:inset-auto md:h-full md:shrink-0 md:translate-x-0">

            <!-- Logo (No border-b) -->
            <div class="h-16 flex items-center justify-between px-6 pt-2 shrink-0">
                <a href="{{ route('admin.index') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="VexaHost" class="h-10 w-auto object-contain shrink-0">
                    <span class="text-xl font-bold text-slate-900">VexaHost</span>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">Admin</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Nav Links (Large Font text-base, Generous padding, No Category Headings) -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.index') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Ringkasan</span>
                </a>

                <a href="{{ route('admin.payments') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.payments*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <span>Verifikasi Pembayaran</span>
                    </div>
                    @php $pendingPaymentCount = \App\Models\Order::where('status', 'pending')->count(); @endphp
                    @if($pendingPaymentCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $pendingPaymentCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.billing.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.billing*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                        <span>Billing Center</span>
                    </div>
                    @php $pendingRefundCount = $adminPendingRefunds ?? 0; @endphp
                    @if($pendingRefundCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $pendingRefundCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.gateways.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.gateways*') || request()->routeIs('admin.webhooks*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <span>Payment Gateway</span>
                    </div>
                    @php $failedWebhookCount = $adminFailedWebhooks ?? 0; @endphp
                    @if($failedWebhookCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $failedWebhookCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.orders') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.orders') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <span>Antrean Order</span>
                    </div>
                    @php $pendingCount = $adminPendingOrders ?? \App\Models\Order::where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $pendingCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.fulfillment.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.fulfillment*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        <span>Papan Fulfillment</span>
                    </div>
                    @php $workingOrders = $adminFulfillmentWorking ?? 0; @endphp
                    @if($workingOrders > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $workingOrders }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.supplier.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.supplier*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pembelian Supplier</span>
                    </div>
                    @php $supplierUrgent = $adminSupplierUrgent ?? 0; @endphp
                    @if($supplierUrgent > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $supplierUrgent }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.shopee') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.shopee') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 53 61" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26.372522 5.4266937c4.902289 0 8.898217 4.6522033 9.085166 10.4757833H17.287569c.186949-5.82358 4.182877-10.4757833 9.084953-10.4757833M51.743379 16.997353c0-.604706-.487007-1.094876-1.087549-1.094876H38.87847c-.28896-7.6892751-5.777492-13.8205815-12.505948-13.8205815-6.728244 0-12.216776 6.1313064-12.505736 13.8205815l-11.7942195.000215c-.5913649.01075-1.0674873.496831-1.0674873 1.094661 0 .02858.00107.05695.0032.08488h-.008323l1.6812616 37.061399c.0002134.103148.00405.207156.011738.311809.00171.02364.00363.04706.00555.07048l.00363.07822.00405.0041c.2554543 2.578923 2.1270784 4.656071 4.6720177 4.751913l.00576.0056H44.796175c.01771.000215.03543.00043.05314.00043.01771 0 .03543-.000215.05314-.00043h.0796l.0017-.0015c2.589329-.0707 4.686743-2.176859 4.908265-4.787585l.0013-.0013.0017-.03503c.0021-.02751.0041-.0548.0058-.0823.0041-.06576.0068-.1313.0079-.196412l1.83449-37.207738h-.0013c.0011-.0187.0015-.03761.0015-.05652" fill="#EE4D2D" fill-rule="evenodd"/>
                        <path d="M35.67174 44.953764c-.333349 2.751051-2.000311 4.954341-4.582384 6.057598-1.437971.614592-3.36871.946386-4.896954.842163-2.384027-.09111-4.623787-.670894-6.688335-1.730742-.737553-.378855-1.837052-1.135276-2.68131-1.843776-.213839-.179005-.239235-.293758-.09774-.494467.0764-.115182.217254-.322983.528622-.779199.45158-.661654.507921-.744602.558713-.822178.14448-.221769.379233-.241109.610785-.05888.02433.01891.02433.01891.04268.03331.03799.02944.03799.02944.12762.09907.0907.0707.14448.112389.166248.12872 2.226529 1.743851 4.819699 2.749547 7.437625 2.850117 3.642304-.04964 6.261511-1.687335 6.730804-4.202004.516031-2.767598-1.656504-5.158274-5.907033-6.490821-1.329344-.416676-4.689518-1.761687-5.309053-2.12507-2.909447-1.707104-4.269736-3.943058-4.076384-6.704854.296216-3.828306 3.850167-6.683579 8.340785-6.702705 2.008208-.0041 4.012147.413238 5.937338 1.224457.681638.28731 1.898727.949608 2.318936 1.263351.242009.177716.289813.384872.151095.60836-.07747.12958-.205515.335017-.475482.763298l-.003.0047c-.355331.564092-.366428.581714-.447952.713657-.140852.214463-.306459.234448-.56042.07328-2.060067-1.383907-4.34379-2.080157-6.855437-2.130442-3.126914.06189-5.470605 1.922856-5.624689 4.45794-.04097 2.289677 1.676352 3.961324 5.385881 5.23585 7.529819 2.419687 10.411309 5.25648 9.869029 9.729248" fill="#FFFFFF"/>
                    </svg>
                    <span>Proses Shopee</span>
                </a>

                <a href="{{ route('admin.instances') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.instances') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                    <span>Cloud Instances</span>
                </a>

                <a href="{{ route('admin.packages.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.packages*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span>Paket Produk</span>
                </a>

                <a href="{{ route('admin.monitoring') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.monitoring') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <span class="w-5 h-5 rounded-full border-2 border-slate-700 flex items-center justify-center"><span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span></span>
                    <span>Monitoring</span>
                </a>

                <a href="{{ route('admin.reports') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.reports*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <span class="w-5 h-5 flex items-end gap-0.5"><span class="w-1.5 h-2 bg-slate-700"></span><span class="w-1.5 h-3.5 bg-slate-700"></span><span class="w-1.5 h-5 bg-slate-700"></span></span>
                    <span>Reports</span>
                </a>

                <a href="{{ route('admin.customers') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.customers') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Pelanggan</span>
                </a>

                <a href="{{ route('admin.tickets') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.tickets*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Tiket Bantuan</span>
                    </div>
                    @php $openTicketCount = $adminOpenTickets ?? \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count(); @endphp
                    @if($openTicketCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $openTicketCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.abuse.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.abuse*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0l-7.1 12.25A2 2 0 004.99 19z"/></svg>
                        <span>Kasus Pelanggaran</span>
                    </div>
                    @php $openAbuseCount = $adminOpenAbuse ?? 0; @endphp
                    @if($openAbuseCount > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $openAbuseCount }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.broadcast.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.broadcast*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <span>Broadcast</span>
                </a>

                <a href="{{ route('admin.templates.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.templates*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Template Surel</span>
                </a>

                <a href="{{ route('admin.maintenance.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.maintenance*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Jadwal Maintenance</span>
                    </div>
                    @php $runningWindows = $adminRunningMaintenance ?? 0; @endphp
                    @if($runningWindows > 0)
                        <span class="text-xs font-bold bg-black text-white px-2 py-0.5 rounded">{{ $runningWindows }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.audit.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.audit*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Audit Log</span>
                </a>

                <a href="{{ route('admin.settings.index') }}"
                   class="flex items-center gap-3.5 px-4 py-3 rounded-lg text-base transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-slate-100 text-slate-900 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium' }}">
                    <svg class="w-5 h-5 text-slate-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    <span>Pengaturan Sistem</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content (No border-b lines under titles, Independent Scroll) -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-y-auto">
            <!-- Header (NO border-b) -->
            <header class="h-16 shrink-0 bg-white flex items-center justify-between px-6 sm:px-10">
                <div class="flex items-center gap-3">
                    @if(isset($backUrl))
                        <a href="{{ $backUrl }}" class="p-1.5 -ml-1 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors flex items-center justify-center" title="{{ $backLabel ?? 'Kembali' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        </a>
                    @endif
                    <h1 class="text-base font-bold text-slate-900">{{ $headerTitle ?? 'Admin' }}</h1>
                </div>

                <div class="flex items-center gap-3">
                    <div class="relative" @click.away="userDropdown = false">
                        <button @click="userDropdown = !userDropdown" class="flex items-center gap-1.5 p-1 rounded-lg hover:bg-slate-100 transition-colors focus:outline-none" title="{{ auth()->user()->full_name ?? auth()->user()->username }}">
                            <div class="rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 shrink-0"
                                 style="width: 32px; height: 32px; min-width: 32px; min-height: 32px; border-radius: 9999px; aspect-ratio: 1 / 1;">
                                <svg class="text-slate-700 shrink-0" style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="userDropdown" x-transition class="absolute right-0 mt-2 w-60 bg-white border border-slate-200 rounded-lg shadow-lg py-1.5 z-50 text-sm" style="display:none;">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center gap-3">
                                <div class="rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 shrink-0"
                                     style="width: 40px; height: 40px; min-width: 40px; min-height: 40px; border-radius: 9999px; aspect-ratio: 1 / 1;">
                                    <svg class="text-slate-700 shrink-0" style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 truncate">{{ auth()->user()->full_name ?? auth()->user()->username }} (Admin)</p>
                                    <p class="text-xs text-slate-500 font-mono-code truncate">{{ auth()->user()->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('dashboard.index') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Client Portal</a>
                            <a href="{{ route('home') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50">Halaman Utama</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Keluar</button>
                            </form>
                        </div>
                    </div>

                    <!-- Mobile Hamburger Button on the Right -->
                    <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100 -mr-1.5" aria-label="Buka Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </header>

            <!-- Alerts -->
            <div class="px-6 sm:px-10 pt-2">
                @if(session('success'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 4000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border border-emerald-200 bg-emerald-50 p-3 rounded-lg text-sm text-emerald-800 mb-4 flex items-center justify-between shadow-xs">
                        <span>{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-800 p-1 text-base leading-none">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="border border-rose-200 bg-rose-50 p-3 rounded-lg text-sm text-rose-800 mb-4 flex items-center justify-between shadow-xs">
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-800 p-1 text-base leading-none">&times;</button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="border border-rose-200 bg-rose-50 p-4 rounded-lg text-sm text-rose-800 mb-4 shadow-xs">
                        <div class="font-bold mb-1">Terjadi kesalahan input:</div>
                        <ul class="list-disc pl-5 space-y-1 text-xs text-rose-700">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Main Page Body -->
            <main class="flex-1 px-6 sm:px-10 pb-12 pt-2">
                @yield('content')
            </main>

            <!-- Footer (No border-t) -->
            <footer class="py-4 px-6 sm:px-10 text-xs text-slate-400 shrink-0">
                &copy; 2026 VexaHost Admin
            </footer>
        </div>
    </div>
    @include('partials.invoice-modal')
</body>
</html>
