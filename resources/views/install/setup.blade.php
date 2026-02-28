<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <title>{{ __('setup.title') }}</title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
</head>

<body class="bg-white text-zinc-900 selection:bg-zinc-100 antialiased font-sans">
    <div class="max-w-2xl mx-auto py-20 px-6 font-light">
        <header class="mb-16">
            <span class="text-[10px] font-mono text-zinc-300 uppercase tracking-[0.5em]">{{ __('setup.installation') }}</span>
            <h1 class="text-4xl mt-4 font-display italic">{{ __('setup.header') }}</h1>
        </header>

        <div id="error-message" class="hidden mb-8 p-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded">
        </div>

        <form id="install-form" action="{{ route('install.do-setup') }}" class="space-y-12">
            @csrf
            <input type="hidden" name="language" value="{{ app()->getLocale() }}">

            <section class="space-y-6">
                <h3 class="text-xs uppercase tracking-widest text-zinc-400">01. {{ __('setup.database') }}</h3>
                <div class="grid grid-cols-2 gap-8">
                    <input type="text" name="db_host" placeholder="{{ __('setup.db_host') }}" value="127.0.0.1" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                    <input type="text" name="db_port" placeholder="{{ __('setup.db_port') }}" value="3306" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <input type="text" name="db_database" placeholder="{{ __('setup.db_database') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                    <input type="text" name="db_username" placeholder="{{ __('setup.db_username') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                </div>
                <div class="grid grid-cols-2 gap-8">
                    <input type="password" name="db_password" placeholder="{{ __('setup.db_password') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                </div>
            </section>

            <div class="flex items-center gap-3 py-4 border-t border-zinc-50 mt-8">
                <input type="hidden" name="import_demo" value="0">
                <input type="checkbox" name="import_demo" id="import_demo" value="1"
                    class="w-4 h-4 accent-black cursor-pointer">
                <label for="import_demo" class="text-[11px] uppercase tracking-[0.2em] text-zinc-400 cursor-pointer">
                    {{ __('setup.import_demo') }}
                </label>
            </div>

            <section class="space-y-6">
                <h3 class="text-xs uppercase tracking-widest text-zinc-400">02. {{ __('setup.site_admin') }}</h3>
                <input type="text" name="site_name" placeholder="{{ __('setup.site_name') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">

                <div class="grid grid-cols-2 gap-8">
                    <input type="email" name="admin_email" placeholder="{{ __('setup.admin_email') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                    <input type="text" name="login_path" placeholder="{{ __('setup.login_path') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
                </div>
                <input type="password" name="admin_password" placeholder="{{ __('setup.admin_password') }}" class="w-full border-b border-zinc-100 py-2 outline-none focus:border-black transition">
            </section>

            <button type="submit" id="submit-btn" class="w-full bg-black text-white py-4 text-[10px] tracking-[0.4em] uppercase hover:bg-zinc-800 transition disabled:bg-zinc-400 disabled:cursor-not-allowed">
                {{ __('setup.complete_installation') }}
            </button>
        </form>

        <script>
            document.getElementById('install-form').addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const btn = document.getElementById('submit-btn');
                const errorDiv = document.getElementById('error-message');
                const btnOriginalText = btn.textContent;

                btn.disabled = true;
                btn.textContent = `{{ __('setup.installing') }}`;
                errorDiv.classList.add('hidden');

                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {

                        const result = await response.json();

                        console.log(result);

                        window.location.href = result.redirect;

                    } else {
                        const data = await response.json();
                        let errors = [];
                        if (data.errors) {
                            for (let field in data.errors) {
                                errors.push(data.errors[field].join(', '));
                            }
                        } else if (data.message) {
                            errors.push(data.message);
                        }
                        errorDiv.textContent = errors.join(' | ');
                        errorDiv.classList.remove('hidden');
                        btn.disabled = false;
                        btn.textContent = btnOriginalText;
                    }
                } catch (error) {
                    errorDiv.textContent = `{{ __('setup.connection_failed') }}`;
                    errorDiv.classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = btnOriginalText;
                }
            });
        </script>
    </div>
</body>

</html>
