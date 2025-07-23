<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | OrgBit</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-gray-100 font-poppins">
<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="bg-[#35408e] text-white w-[350px] p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center">
                <a href="/" class="text-white text-2xl font-bold">
                    <img src="{{ asset('storage/logos/Logo.png') }}" alt="Logo" class="h-150 w-150" />
                </a>
                <h1 class="text-[24px] tracking-[15px] font-light mb-6 mt-6 ml-5">
                    ORGBIT
                </h1>
            </div>
            <hr class="border-b border-customAccentColor mt-5 mb-10">
            <nav>
                <ul>
                    <li class="mb-10 flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="layout-dashboard" class="mr-5 w-8 h-8"></i>Dashboard
                        </a>
                    </li>
                    <li class="mb-10 flex items-center">
                        <a href="{{ route('admin.events') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="calendar" class="mr-5 w-8 h-8"></i>Events
                        </a>
                    </li>
                    <li class="mb-10 flex items-center">
                        <a href="{{ route('admin.members') }}" class="flex items-center text-gray-400 hover:text-white text-[16px]">
                            <i data-lucide="users" class="mr-5 w-8 h-8"></i>Members
                        </a>
                    </li>
                    <li class="flex items-center">
                        <div class="absolute left-0 h-10 w-1 bg-white rounded-r-lg"></div>
                        <a href="{{ route('admin.settings') }}" class="flex items-center text-white underline text-[16px]">
                            <i data-lucide="settings" class="mr-5 w-8 h-8"></i>Settings
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <a href="{{ route('admin.settings') }}">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('storage/logos/' . $organization->org_file_path) }}" alt="Logo" class="h-14 w-14 rounded-full object-cover mr-3" />
                <p class="text-white text-[16px] font-semibold break-words w-[200px] mt-1">
                    @if(isset($organization))
                        {{ $organization->org_name }}
                    @else
                        Organization Not Found
                    @endif
                </p>
            </div>
        </a>
    </aside>

    <!-- Gold Container -->
    <div class="bg-[#FFD700] w-[5px] h-full"></div>

    <!-- Main Content -->
    <main class="flex-1 p-6 bg-[#f6f6f6]">
        <h2 class="text-[40px] font-bold">SETTINGS</h2>

        <!-- Organization Profile Section -->
        <h2 class="text-[24px] font-semibold mb-5 mt-5">Organization Profile</h2>
        <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
            <div class="flex items-center space-x-6">
                <!-- Organization Logo -->
                <div class="w-32 h-32 rounded-full overflow-hidden border-2 border-[#35408e] mr-5 ml-3">
                    <img src="{{ asset('storage/logos/' . $organization->org_file_path) }}" alt="Organization Logo" class="w-full h-full object-cover">
                </div>
                <!-- Organization Details -->
                <div class="flex-1">
                    <div class="space-y-4">
                        <div>
                            <p class="font-semibold text-[#3c763d]">Name:</p>
                            <p class="">{{ $organization->org_name }}</p>
                        </div>
                        <div>
                            <p class=" font-semibold text-[#3c763d]">Email:</p>
                            <p class="">{{ $organization->org_email }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-[#3c763d]">Category:</p>
                            <p class="">
                                @if($organization->category_id == 1)
                                    Internal
                                @elseif($organization->category_id == 2)
                                    External
                                @elseif($organization->category_id == 3)
                                    Institutional
                                @else
                                    Other
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class=" font-semibold text-[#3c763d]">Bio:</p>
                            <p class=" break-words">{{ $organization->org_bio }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Button -->
        <form action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button type="submit" class="text-white font-semibold border-2 rounded-md w-[130px] h-[30px] bg-[#35408e] hover:bg-[#2c3578]">
                Log Out
            </button>
        </form>
    </main>
</div>
<script>
    lucide.createIcons();
</script>
</body>
</html>