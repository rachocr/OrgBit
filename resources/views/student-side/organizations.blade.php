<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OrgBit | Organizations</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"/>
    <link rel="icon" href="{{ asset('storage/logos/Logo.png') }}" type="image/png">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .filter-button.selected {
            background-color: #35408e;
            color: white;
        }
        .hidden {
            display: none;
        }
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

        .organization-card {
            width: 100%;
            max-width: 1045px;
            margin: 10px auto;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
    </style>
</head>
<body class="font-sans bg-[#f6f6f6]">
    <!-- Header -->
    <header class="bg-[#35408e] w-full fixed top-0 left-0 right-0 z-50">
        <div class="w-[97%] mx-auto flex items-center justify-between px-4 py-4">
            <div class="flex items-center">
                <a href="{{ route('student-side.home') }}" class="text-white text-2xl font-bold">
                    <img src="{{ asset('storage/logos/Logo.png') }}" alt="Logo" class="h-20 w-20" />
                </a>
            </div>

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
        <div class="flex flex-col space-y-5">
            <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[1090px] h-max pb-5">
                <div class="">
                    <p class="text-[24px] p-6 font-bold">Organizations ({{ $organizations->count() }})</p>
                    <hr class="mx-[24px] border-t-2 border-[#dddddd]">
                </div>
                <div class="scrollable-container">
                    @foreach ($organizations as $org)
                        <div class="organization-card flex items-center justify-between">
                            <div class="flex items-center gap-x-5">
                                <a>
                                    <img src="{{ asset('storage/logos/' . $org->org_file_path) }}" alt="organization logo" class="w-16 h-16 rounded-full">
                                </a>
                                <h1 class="text-lg font-semibold">{{ $org->org_name }}</h1>
                            </div>
                            <div>
                                <button class="join-button bg-[#ffffff] border border-1 border-[#35408e] rounded-md w-[150px] h-[30px] mt-2 
                                            hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200" 
                                        data-org-id="{{ $org->org_id }}">
                                    @if ($org->has_joined)
                                        Already Joined
                                    @elseif ($org->has_requested)
                                        Requested to Join
                                    @else
                                        Join Organization
                                    @endif
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[400px] h-max p-6">
            <p class="text-xl font-bold text-gray-800">About</p>
            <p class="text-gray-600 mt-2">
                Welcome to Orbit, your gateway to seamless organization management and event coordination.
            </p>
            <p class="text-gray-600 mt-2">
                Our platform empowers users to connect with organizations, explore opportunities, and participate in events that match their interests and aspirations.
            </p>
            <p class="text-gray-600 mt-2">
                Whether you're looking to expand your network, collaborate on projects, or stay updated with the latest events, Orbit makes it simple and engaging.
            </p>

            <p class="text-lg font-bold text-gray-800 mt-6">Contact</p>
            <p class="text-gray-600">Have questions or need assistance? Reach out to us!</p><br>
            <p class="text-gray-600"><strong>Email:</strong> support@orgbit.com</p>
            <p class="text-gray-600"><strong>Contact:</strong> +63 912 345 6789</p>
        </div>
    </div>

    <!-- Join Organization Modal -->
    <div id="joinOrgModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[24px] font-semibold">Join Organization</h2>
                <button onclick="closeModal('joinOrgModal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="orgDetails">
                <hr>
                <br>
                <p class="text-lg font-semibold" id="orgName"></p>
                <br>
                <p><strong>Description:</strong> <span id="orgDescription"></span></p>
            </div>
            <div class="mt-4">
                <label class="flex items-center">
                    <input type="checkbox" id="orgConfirmationCheckbox" class="mr-2">
                    <span>I confirm that I want to join this organization.</span>
                </label>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeModal('joinOrgModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                <button id="confirmJoinOrgButton" class="bg-[#35408e] text-white px-4 py-2 rounded hover:bg-[#2c3579]">Join Organization</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto"></i>
            <p id="successMessage" class="text-lg font-semibold mt-4">Success!</p>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto"></i>
            <p id="errorMessage" class="text-lg font-semibold mt-4">Error!</p>
            <button onclick="closeModal('errorModal')" class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                Confirm
            </button>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
    // Function to close modal
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Function to show success modal
    function showSuccessModal(message) {
        document.getElementById('successMessage').textContent = message;
        document.getElementById('successModal').classList.remove('hidden');
        setTimeout(() => {
            closeModal('successModal');
        }, 2000);
    }

    // Function to show error modal
    function showErrorModal(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.remove('hidden');
    }

    // Function to update the button state for organizations
    function updateOrgButtonState(buttonSelector, positionId) {
        const button = document.querySelector(buttonSelector);
        if (button) {
            if (positionId > 1) {
                // Member (position_id = 2, 3, or 4)
                button.disabled = true;
                button.textContent = 'Already Joined';
                button.classList.remove('bg-[#ffffff]', 'border-[#35408e]', 'hover:bg-[#35408e]', 'hover:border-[#35408e]', 'hover:text-[#ffffff]');
                button.classList.add('bg-[#dddddd]', 'cursor-not-allowed', 'text-[#666666]', 'border-[#dddddd]');
            } else if (positionId === 1) {
                // Requested to join (position_id = 1)
                button.disabled = true;
                button.textContent = 'Requested to Join';
                button.classList.remove('bg-[#ffffff]', 'border-[#35408e]', 'hover:bg-[#35408e]', 'hover:border-[#35408e]', 'hover:text-[#ffffff]');
                button.classList.add('bg-[#dddddd]', 'cursor-not-allowed', 'text-[#666666]', 'border-[#dddddd]');
            } else {
                // Not a member (position_id = null)
                button.disabled = false;
                button.textContent = 'Join Organization';
                button.classList.remove('bg-[#dddddd]', 'cursor-not-allowed', 'text-[#666666]', 'border-[#dddddd]');
                button.classList.add('bg-[#ffffff]', 'border-[#35408e]', 'hover:bg-[#35408e]', 'hover:border-[#35408e]', 'hover:text-[#ffffff]');
            }
        }
    }

    // Check organization membership status for all organizations on page load
    document.querySelectorAll('.join-button').forEach(async (button) => {
        const orgId = button.dataset.orgId;

        try {
            const response = await fetch(`/check-organization-membership/${orgId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            if (!response.ok) {
                throw new Error('Failed to check organization membership status.');
            }

            const data = await response.json();

            if (data.success) {
                updateOrgButtonState(`.join-button[data-org-id="${orgId}"]`, data.position_id);
            }
        } catch (error) {
            console.error('Error checking organization membership:', error);
        }
    });

    // Event delegation for Join Organization buttons
    document.addEventListener('click', async (event) => {
        if (event.target.classList.contains('join-button')) {
            const button = event.target;
            const orgId = button.dataset.orgId;

            // Check the current state of the button
            if (button.textContent === 'Join Organization') {
                // Open the modal for confirmation
                document.getElementById('joinOrgModal').classList.remove('hidden');

                // Fetch organization details
                try {
                    const response = await fetch(`/fetch-organization-details/${orgId}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Failed to fetch organization details.');
                    }

                    const orgData = await response.json();

                    // Populate the modal with organization details
                    document.getElementById('orgName').textContent = orgData.org_name;
                    document.getElementById('orgDescription').textContent = orgData.org_bio || 'No description available.';

                    // Set the organization ID in the modal's confirm button
                    document.getElementById('confirmJoinOrgButton').dataset.orgId = orgId;
                } catch (error) {
                    console.error('Error fetching organization details:', error);
                    showErrorModal('An error occurred while fetching organization details.');
                }
            }
        }
    });

    // Confirm Join Organization
    document.getElementById('confirmJoinOrgButton').addEventListener('click', async () => {
        const orgId = document.getElementById('confirmJoinOrgButton').dataset.orgId;
        const confirmationCheckbox = document.getElementById('orgConfirmationCheckbox');

        if (!confirmationCheckbox.checked) {
            alert('Please confirm that you want to join this organization.');
            return;
        }

        try {
            const response = await fetch(`/join-organization/${orgId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to join the organization.');
            }

            const data = await response.json();

            if (data.success) {
                // Update the button state to "Requested to Join"
                updateOrgButtonState(`.join-button[data-org-id="${orgId}"]`, 1);

                // Show success message
                showSuccessModal('Your request to join the organization has been submitted. You are now under review.');

                // Close the modal
                closeModal('joinOrgModal');
            } else {
                showErrorModal(data.message || 'Failed to join the organization.');
            }
        } catch (error) {
            console.error('Error joining organization:', error);
            showErrorModal('An error occurred while joining the organization.');
        }
    });
</script>
</body>
</html>