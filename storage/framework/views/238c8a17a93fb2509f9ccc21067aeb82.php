<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />

    <link rel="icon" href="https://fav.farm/🍳" />
    <title>Masuk — Buku Kas Dapur MBG</title>
    <meta name="description" content="Masuk ke aplikasi Buku Kas Dapur Program Makan Gratis." />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet" />

    <?php echo $__env->make('komponen.tailwind', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <style>
      /* subtle paper texture background */
      body {
        background-color: #EDEFE2;
        background-image:
          repeating-linear-gradient(
            0deg,
            transparent,
            transparent 27px,
            rgba(199,196,172,0.35) 28px
          );
      }
      input:-webkit-autofill {
        -webkit-box-shadow: 0 0 0 40px #F7F6ED inset !important;
        -webkit-text-fill-color: #22301F !important;
      }
    </style>
</head>
<body class="font-sans text-ink min-h-screen antialiased flex items-center justify-center p-4">

    <div class="w-full max-w-sm animate-fadein">

        
        <div class="flex flex-col items-center mb-8">
            <div class="size-14 rounded-xl bg-green-deep text-mustard flex items-center justify-center font-display font-bold text-xl -rotate-3 shadow-lg mb-3 select-none">
                MBG
            </div>
            <h1 class="font-display font-bold text-2xl text-green-deep">Buku Kas Dapur</h1>
            <p class="text-sm text-muted font-mono mt-0.5">Program Makan Gratis</p>
        </div>

        
        <div class="bg-card border border-paperline rounded-2xl shadow-md overflow-hidden">

            
            <div class="h-1.5 w-full bg-linear-to-r from-green-deep via-mustard to-rust"></div>

            <div class="p-7">
                <h2 class="font-display font-semibold text-lg text-green-deep mb-1">Selamat Datang</h2>
                <p class="text-xs text-muted mb-6">Masukkan kredensial Anda untuk melanjutkan.</p>

                
                <?php if($errors->any()): ?>
                <div class="mb-5 bg-rust/10 border border-rust/30 text-rust rounded-lg px-4 py-3 text-xs flex items-start gap-2">
                    <svg class="size-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <ul class="list-none space-y-0.5">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
                <?php endif; ?>

                
                <?php if(session('status')): ?>
                <div class="mb-5 bg-green/10 border border-green/30 text-green-deep rounded-lg px-4 py-3 text-xs">
                    <?php echo e(session('status')); ?>

                </div>
                <?php endif; ?>

                <form id="form-login" method="POST" action="<?php echo e(route('login.proses')); ?>" class="space-y-5">
                    <?php echo csrf_field(); ?>

                  
<div>
    <label for="username" class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5">
        Username atau Email
    </label>
    <div class="relative">
        <span class="absolute inset-y-0 left-3 flex items-center text-muted pointer-events-none">
            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </span>
        <input
            id="username"
            name="username"
            type="text"
            required
            autofocus
            value="<?php echo e(old('username')); ?>"
            placeholder="admin_dapur atau admin@mbg.com"
            class="w-full pl-9 pr-4 py-2.5 bg-card border border-paperline rounded-lg text-sm text-ink placeholder-muted/60
                   focus:outline-hidden focus:border-green focus:ring-2 focus:ring-green/20
                   transition-all duration-200
                   <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rust ring-2 ring-rust/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
        />
    </div>
    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1.5 text-xs text-rust"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

                    
                    <div>
                        <label for="password" class="block text-xs font-semibold text-muted uppercase tracking-wider mb-1.5">
                            Password
                        </label>
                        <div class="relative" x-data="{ tampil: false }">
                            <span class="absolute inset-y-0 left-3 flex items-center text-muted pointer-events-none">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <input
                                id="password"
                                name="password"
                                :type="tampil ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••"
                                class="w-full pl-9 pr-10 py-2.5 bg-card border border-paperline rounded-lg text-sm text-ink placeholder-muted/60
                                       focus:outline-hidden focus:border-green focus:ring-2 focus:ring-green/20
                                       transition-all duration-200
                                       <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-rust ring-2 ring-rust/20 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            />
                            
                            <button
                                type="button"
                                @click="tampil = !tampil"
                                class="absolute inset-y-0 right-3 flex items-center text-muted hover:text-ink transition-colors"
                                tabindex="-1"
                                aria-label="Tampilkan atau sembunyikan password"
                            >
                                <svg x-show="!tampil" class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg x-show="tampil" x-cloak class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1.5 text-xs text-rust"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <button
                        id="btn-login"
                        type="submit"
                        class="w-full mt-1 bg-green-deep hover:bg-green text-mustard font-display font-semibold
                               py-2.5 rounded-lg text-sm shadow-sm
                               focus:outline-hidden focus:ring-2 focus:ring-green/40
                               transition-all duration-200 active:scale-[0.98] flex items-center justify-center gap-2"
                    >
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Masuk ke Sistem
                    </button>
                </form>
            </div>
        </div>

        
        <p class="text-center text-[11px] text-muted font-mono mt-6">
            &copy; <?php echo e(date('Y')); ?> Buku Kas Dapur MBG
        </p>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html><?php /**PATH D:\kuliah\MKI\Projek\mbg2-main-revisi (7)\mbg2-main\resources\views\auth\login.blade.php ENDPATH**/ ?>