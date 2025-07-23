<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrgBit | Information</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .scrollable-container {
            max-height: 600px;
            overflow-y: auto;
            padding-right: 10px;
        }
        .scrollable-container::-webkit-scrollbar {
            width: 8px;
        }
        .scrollable-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .scrollable-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .scrollable-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .event-card {
            width: 100%;
            max-width: 1045px;
            margin: 10px auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        .filter-button.selected {
            background-color: #35408e;
            color: white;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-[#f6f6f6] font-sans">

    <!-- Header -->
    <header class="bg-[#35408e] w-full fixed top-0 left-0 right-0 z-50">
        <div class="w-[97%] mx-auto flex items-center justify-between px-4 py-4">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <a href="{{ route('student-side.home') }}" class="text-white text-2xl font-bold">
                    <img src="{{ asset('storage/logos/Logo.png') }}" alt="Logo" class="h-20 w-20" />
                </a>
            </div>

            <!-- Right: Navigation Links -->
            <nav class="flex items-center space-x-12">
                <a href="{{ route('student-side.home') }}" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                    <i class="fas fa-home text-xl"></i>
                    <span class="mt-1 text-sm">Home</span>
                </a>
                <a href="{{ route('student-side.organizations') }}" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                    <i class="fas fa-users text-xl"></i>
                    <span class="mt-1 text-sm">Organizations</span>
                </a>
                <a href="{{ route('student-side.events') }}" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                    <i class="fas fa-calendar-alt text-xl"></i>
                    <span class="mt-1 text-sm">Events</span>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex flex-col items-center text-white hover:text-gray-400 transition duration-300">
                        <i class="fas fa-sign-out-alt text-xl"></i>
                        <span class="mt-1 text-sm">Log Out</span>
                    </button>
                </form>
            </nav>
        </div>
        <div class="bg-[#FFD700] w-full h-2 fixed top-25 left-0 right-0 z-40">
            <!-- Content for the gold container goes here -->
        </div>
    </header>

    <!-- Body -->
    <div class="pt-36 px-8 flex flex-wrap gap-10 justify-center">
        <!-- Main Content -->
        <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[1090px] h-max p-6">
            <h2 class="text-xl font-bold text-gray-800">About Us</h2>
            <p class="text-gray-600 mt-2">
                Welcome to OrgBit, your gateway to seamless organization management and event coordination.
            </p>
            <p class="text-gray-600 mt-2">
                Our platform empowers users to connect with organizations, explore opportunities, and participate in events that match their interests and aspirations.
            </p>
            <p class="text-gray-600 mt-2">
                Whether you're looking to expand your network, collaborate on projects, or stay updated with the latest events, OrgBit makes it simple and engaging.
            </p>

            <h2 class="text-xl font-bold text-gray-800 mt-6">Privacy Policy</h2>
            <p class="text-gray-600 mt-2">
                At OrgBit, we are committed to protecting your privacy. This Privacy Policy outlines how we collect, use, and safeguard your personal information when you use our platform.
            </p>
            <h3 class="text-lg font-semibold mt-4">1. Information We Collect</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>Personal details (name, email, contact number, etc.) provided during registration.</li>
                <li>Event participation details and interactions on the platform.</li>
                <li>Usage data, including device information and browsing activity.</li>
            </ul>

            <h3 class="text-lg font-semibold mt-4">2. How We Use Your Information</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>To provide and improve our services.</li>
                <li>To personalize your experience and recommend relevant events or organizations.</li>
                <li>To communicate updates, promotions, or important notifications.</li>
            </ul>

            <h2 class="text-xl font-bold text-gray-800 mt-6">Contact Us</h2>
            <p class="text-gray-600 mt-2">
                Have questions or need assistance? We’re here to help!
            </p>
            <p class="text-gray-600 mt-2">
                <strong>Email:</strong> support@orgbit.com
            </p>
            <p class="text-gray-600 mt-2">
                <strong>Contact:</strong> +63 912 345 6789
            </p>

            <h2 class="text-xl font-bold text-gray-800 mt-6">Terms and Conditions</h2>
            <p class="text-gray-600 mt-2">
                By accessing and using OrgBit, you agree to comply with the following terms and conditions.
            </p>
            <h3 class="text-lg font-semibold mt-4">1. Use of Service</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>You must be at least 18 years old or have parental consent to use the platform.</li>
                <li>You agree to provide accurate and up-to-date information.</li>
                <li>Unauthorized access or misuse of the platform is strictly prohibited.</li>
            </ul>
            <h3 class="text-lg font-semibold mt-4">2. User Conduct</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>Respectful and lawful interactions are expected from all users.</li>
                <li>Any misuse, spam, or violation of community guidelines may result in account suspension.</li>
            </ul>
            <h3 class="text-lg font-semibold mt-4">3. Intellectual Property</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>All content on OrgBit, including logos, text, and graphics, is owned by us and cannot be used without permission.</li>
            </ul>
            <h3 class="text-lg font-semibold mt-4">4. Liability Disclaimer</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>We are not responsible for any inaccuracies in event details or third-party interactions.</li>
                <li>Users participate in events at their own risk.</li>
            </ul>
            <h3 class="text-lg font-semibold mt-4">5. Modifications</h3>
            <ul class="list-disc pl-6 text-gray-700">
                <li>We reserve the right to update these terms at any time. Continued use of the platform implies acceptance of any modifications.</li>
            </ul>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Function to close modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Function to show error modal
        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        // Function to show success modal
        function showSuccessModal(message) {
            const successModal = document.createElement('div');
            successModal.id = 'successModal';
            successModal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
            successModal.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <i class="fas fa-check-circle w-12 h-12 text-green-500 mx-auto"></i>
                    <p class="text-lg font-semibold mt-4">${message}</p>
                    <button onclick="closeModal('successModal')" class="mt-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        Confirm
                    </button>
                </div>
            `;
            document.body.appendChild(successModal);
        }

        // Close success modal
        function closeSuccessModal() {
            const successModal = document.getElementById('successModal');
            if (successModal) {
                successModal.remove();
            }
        }
    </script>
</body>
</html>