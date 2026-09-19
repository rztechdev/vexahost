<div class="border border-slate-200 rounded-lg p-6 space-y-6">
    {{-- 1. VPS Name & Akses Root --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center">1</span>
            <h2 class="font-semibold text-slate-900">Nama Server &amp; Kredensial</h2>
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                Hostname / VPS Name <span class="text-rose-600">*</span>
            </label>
            <input type="text" name="hostname" x-model="vpsName" required minlength="3" maxlength="63" pattern="[A-Za-z0-9][A-Za-z0-9-]*" placeholder="misal: web-production-01" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 text-sm font-mono-code focus:border-black focus:ring-1 focus:ring-black" autocomplete="off">
            <p class="text-xs text-slate-500 mt-1">Wajib diisi. Gunakan huruf, angka, dan tanda hubung saja.</p>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                    <span>Root Password Server</span>
                    <span class="text-rose-600">*</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-600 font-semibold border border-rose-200">Wajib</span>
                </label>
                <span class="text-xs text-slate-400 font-mono-code">Min. 8 karakter</span>
            </div>
            <div class="relative">
                <input :type="showRootPassword ? 'text' : 'password'" 
                       name="root_password" 
                       x-model="rootPassword" 
                       required 
                       minlength="8" 
                       placeholder="Masukkan password root VPS Anda" 
                       class="w-full pl-3 pr-10 py-2.5 rounded-lg border border-slate-200 text-sm font-mono-code focus:border-black focus:ring-1 focus:ring-black" 
                       autocomplete="new-password">
                <button type="button" 
                        @click="showRootPassword = !showRootPassword" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                        tabindex="-1">
                    <svg x-show="!showRootPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showRootPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-slate-500 mt-1">Password ini wajib diisi dan akan digunakan untuk akses SSH user root ke server Anda. Harap simpan dengan baik.</p>
        </div>
    </div>

    {{-- 2. Paket VPS Dipilih --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center">2</span>
            <h2 class="font-semibold text-slate-900">Paket VPS Dipilih</h2>
        </div>
        <div class="rounded-lg border border-black bg-slate-50 p-4 flex items-center justify-between gap-4">
            <div>
                <p class="font-semibold text-slate-900" x-text="specs[selectedSpec] ? specs[selectedSpec].name : 'Paket VPS'"></p>
                <p class="text-xs text-slate-500 mt-1" x-text="specs[selectedSpec] ? specs[selectedSpec].cpu + ' vCPU · ' + specs[selectedSpec].ram + ' GB RAM · ' + specs[selectedSpec].disk + ' GB SSD · ' + specs[selectedSpec].bandwidth + ' Mbps' : ''"></p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-slate-900 font-mono-code" x-text="formatRupiah(currentBaseMonthlyPrice)"></p>
                <p class="text-xs text-slate-500">/bulan</p>
            </div>
        </div>
        <input type="hidden" name="vps_spec_id" :value="selectedSpec">
        <input type="hidden" name="billing_cycle" value="monthly">
    </div>

    {{-- 3. Pilih Provider --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center">3</span>
            <h2 class="font-semibold text-slate-900">Pilih Provider</h2>
        </div>
        <div class="grid gap-3" :class="(canUseProvider('tencent') && canUseProvider('cloudeka')) ? 'sm:grid-cols-2' : 'grid-cols-1'">
            <button type="button" 
                    x-show="canUseProvider('tencent')"
                    @click="selectProvider('tencent')" 
                    :class="provider === 'tencent' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300'" 
                    class="p-4 rounded-lg border text-left transition-all flex items-start gap-3.5">
                <img src="{{ asset('images/logos/providers/tencent.svg') }}" alt="Tencent Cloud" class="w-8 h-8 object-contain shrink-0 mt-0.5" />
                <div>
                    <p class="font-semibold text-sm text-slate-900">Tencent Cloud</p>
                    <p class="text-xs text-slate-500 mt-1">Indonesia dan Singapore</p>
                </div>
            </button>
            <button type="button" 
                    x-show="canUseProvider('cloudeka')"
                    @click="selectProvider('cloudeka')" 
                    :class="provider === 'cloudeka' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300'" 
                    class="p-4 rounded-lg border text-left transition-all flex items-start gap-3.5">
                <img src="{{ asset('images/logos/providers/cloudeka.svg') }}" alt="Cloudeka by Lintasarta" class="w-8 h-8 object-contain shrink-0 mt-0.5" />
                <div>
                    <p class="font-semibold text-sm text-slate-900">Cloudeka by Lintasarta</p>
                    <p class="text-xs text-slate-500 mt-1">Indonesia (Jakarta)</p>
                </div>
            </button>
        </div>
        <input type="hidden" name="provider" :value="provider">
    </div>

    {{-- 4. Datacenter & OS --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center">4</span>
            <h2 class="font-semibold text-slate-900">Datacenter & OS</h2>
        </div>
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Lokasi Datacenter</label>
                <div class="grid sm:grid-cols-2 gap-3">
                    <label x-show="provider === 'tencent'" class="p-3 rounded-lg border cursor-pointer flex items-center justify-between" :class="datacenter === 'singapore' ? 'border-black bg-slate-50' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="datacenter_location" value="singapore" x-model="datacenter" class="sr-only">
                        <span><b class="block text-sm">Singapore</b><small class="text-xs text-slate-500">Rute internasional</small></span>
                    </label>
                    <label class="p-3 rounded-lg border cursor-pointer flex items-center justify-between" :class="datacenter === 'indonesia' ? 'border-black bg-slate-50' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="datacenter_location" value="indonesia" x-model="datacenter" class="sr-only">
                        <span><b class="block text-sm">Indonesia (Jakarta)</b><small class="text-xs text-slate-500">Untuk pengguna di Indonesia</small></span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Operating System</label>
                <select name="os" x-model="os" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm bg-white">
                    <template x-for="[value, label] in Object.entries(operatingSystems[provider])" :key="value">
                        <option :value="value" x-text="label"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    {{-- 5. Pilih Stack Server --}}
    <div>
        <div class="flex items-center gap-3 mb-4">
            <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-semibold flex items-center justify-center">5</span>
            <h2 class="font-semibold text-slate-900">Pilih Stack Server</h2>
        </div>
        <div class="grid sm:grid-cols-3 gap-3 mb-4">
            <button type="button" @click="selectStack('none')" :class="stackType === 'none' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300'" class="p-4 rounded-lg border text-left">
                <p class="font-semibold text-sm">Tanpa Control Panel</p>
                <p class="text-xs text-slate-500 mt-1">Server bersih, akses penuh</p>
            </button>
            <button type="button" @click="selectStack('control_panel')" :class="stackType === 'control_panel' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300'" class="p-4 rounded-lg border text-left">
                <p class="font-semibold text-sm">Control Panel</p>
                <p class="text-xs text-slate-500 mt-1">Kelola server lebih mudah</p>
            </button>
            <button type="button" @click="selectStack('application')" :class="stackType === 'application' ? 'border-black bg-slate-50 ring-1 ring-black' : 'border-slate-200 hover:border-slate-300'" class="p-4 rounded-lg border text-left">
                <p class="font-semibold text-sm">Aplikasi</p>
                <p class="text-xs text-slate-500 mt-1">Deploy stack siap pakai</p>
            </button>
        </div>
        <div x-show="stackType === 'control_panel'" class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            @foreach(['coolify' => 'Coolify', 'dokploy' => 'Dokploy', 'aapanel' => 'aaPanel', 'cloudpanel' => 'CloudPanel', 'docker' => 'Docker', 'cyberpanel' => 'CyberPanel', 'hestiacp' => 'HestiaCP'] as $value => $label)
                <button type="button" @click="controlPanel = '{{ $value }}'" 
                        :class="controlPanel === '{{ $value }}' ? 'border-black bg-slate-50 ring-1 ring-black shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'" 
                        class="p-3 rounded-lg border text-left flex items-center gap-3 transition-all min-h-[56px]">
                    <img src="{{ asset('images/logos/panels/' . $value . '.svg') }}" alt="{{ $label }}" class="w-6 h-6 object-contain shrink-0" />
                    <span class="text-sm font-semibold text-slate-900 leading-tight">{{ $label }}</span>
                </button>
            @endforeach
        </div>
        <div x-show="stackType === 'application'" class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            @foreach(['hermes_agent' => 'Hermes Agent', 'openclaw' => 'OpenClaw', 'omniroute' => 'OmniRoute', '9router' => '9router', 'agent_zero' => 'Agent Zero', 'n8n' => 'n8n', 'ollama' => 'Ollama', 'anythingllm' => 'AnythingLLM', 'librechat' => 'LibreChat', 'vscode_server' => 'Visual Studio Code Server', 'gitea_forgejo' => 'Gitea / Forgejo', 'uptime_kuma' => 'Uptime Kuma', 'netdata_beszel' => 'Netdata / Beszel', 'wordpress' => 'WordPress', 'ghost' => 'Ghost', 'strapi_directus' => 'Strapi / Directus', 'prestashop_bagisto' => 'PrestaShop / Bagisto'] as $value => $label)
                <button type="button" @click="controlPanel = '{{ $value }}'" 
                        :class="controlPanel === '{{ $value }}' ? 'border-black bg-slate-50 ring-1 ring-black shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white'" 
                        class="p-3 rounded-lg border text-left flex items-center gap-3 transition-all min-h-[56px]">
                    <img src="{{ asset('images/logos/apps/' . $value . '.svg') }}" alt="{{ $label }}" class="w-6 h-6 object-contain shrink-0" />
                    <span class="text-sm font-semibold text-slate-900 leading-tight">{{ $label }}</span>
                </button>
            @endforeach
        </div>
        <input type="hidden" name="control_panel" :value="controlPanel">
    </div>
</div>
