<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | OrgBit</title>
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
    </style>
</head>
<body class=" font-sans">
    <!-- Header -->
    <header class="bg-[#35408e] w-full fixed top-0 left-0 right-0 z-50">
        <div class="w-[97%] mx-auto flex items-center justify-between px-4 py-4">
            <!-- Logo -->
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
    <div class="pt-36 px-8 flex flex-wrap gap-10 justify-center bg-[#f6f6f6]">
        <!-- Left Container -->
        <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[400px] h-max flex flex-col space-y-2 p-5">
            <p class="text-lg font-semibold pb-3">{{ $student->student_name }}</p>
            <p>Student Number: {{ $student->student_id }}</p>
            <p>Course: {{ $student->course->course_name }} - {{ $student->course->specialization }}</p>
            <p>College: {{ $student->college->college_name }}</p>
            <hr class="my-5 w-[350px]">
            
            <p>Joined Organizations:</p>
            @php
                $joinedOrganizations = $student->members->whereIn('position_id', [2, 3, 4]);
            @endphp

            @if($student->members->whereIn('position_id', [2, 3, 4])->isNotEmpty())
                <ul class="list-disc list-inside pl-2">
                    @foreach($student->members->whereIn('position_id', [2, 3, 4]) as $member)
                        <li>{{ $member->organization->org_name ?? 'Unknown Organization' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No organizations joined</p>
            @endif
        </div>

        <!-- Middle Container -->
        <div class="flex flex-col space-y-5">
            <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[800px] h-[60px] space-x-5 pt-3 pl-5">
                <!-- Filter Section -->
                <button class="filter-button border-[#35408e] w-[150px] border-2 rounded-md h-[30px] hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200 selected" data-filter="all">All</button>
                <button class="filter-button border-[#35408e] w-[150px] border-2 rounded-md h-[30px] hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200" data-filter="posts">Posts</button>
                <button class="filter-button border-[#35408e] w-[150px] border-2 rounded-md h-[30px] hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200" data-filter="images">Images</button>
            </div>

            <!-- Posts Section -->
            <div id="posts-container" class="w-[800px]">
                @foreach ($posts as $post)
                    <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 mb-5">
                        <div class="flex flex-row mt-5 ml-5">
                            <!-- Organization Logo -->
                            <div class="mr-5">
                                <a href="">
                                    <img src="{{ asset('storage/logos/' . $post->organization->org_file_path) }}" alt="Organization Logo" class="w-20 h-20 rounded-full object-cover border-2 border-[#dddddd] flex-shrink-0 max-w-[80px] max-h-[80px]">
                                </a>
                            </div>
                            <div class="mt-4 break-words w-[500px]">
                                <p class="font-bold">{{ $post->organization->org_name }}</p>
                                <p>{{ $post->post_date_time->diffForHumans() }}</p>
                            </div>
                            <div class="align-right">
                            <button class="join-event-button bg-[#ffffff] border-1 border border-[#35408e] rounded-md w-[150px] h-[30px] mt-4 
                                hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200" 
                                data-post-id="{{ $post->post_id }}" 
                                data-event-id="{{ $post->event->event_id }}">
                                Join Event
                            </button>
                            </div>
                        </div>
                        <div class="caption p-5">
                            <p class="text-[22px]">{{ $post->event->event_name }}</p>
                            <br>
                            <p class="break-words">{{ $post->post_content }}</p>
                        </div>
                        @if ($post->media)
                            <div class="flex justify-center items-center">
                                <div class="border rounded-md mt-5 mb-5 media-container">
                                    <img src="{{ asset('storage/posts/' . $post->media->file_name) }}" alt="Event" class="media-container">
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Container -->
        <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 w-[400px] h-max flex flex-col space-y-2 p-5">
            <p class="text-lg font-semibold pb-3">More Organizations</p>
            <hr class="my-5 w-[350px] mb-2">
            <div class="flex flex-col">
                @foreach ($organizations as $org)
                    <div class="flex space-x-2 m-5 mt-2 items-start">
                        <a href="">
                            <img src="{{ asset('storage/logos/' . $org->org_file_path) }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-[#dddddd] flex-shrink-0 max-w-[80px] max-h-[80px]">
                        </a>
                        <div class="flex flex-col mt-1">
                            <a class="break-words whitespace-normal max-w-[250px] text-sm" href="">
                                {{ $org->org_name }}
                            </a>
                            <button class="text-sm join-button bg-[#ffffff] border border-1 border-[#35408e] rounded-md w-[150px] h-[30px] mt-2 
                                        hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200" 
                                    data-org-id="{{ $org->org_id }}">
                                Join Organization
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Links Section (Below Right Container) -->
        <div class="w-[400px] flex flex-col items-center text-center text-sm mt-5">
            <div class="flex flex-wrap justify-center gap-4 text-gray-500">
                <a href="{{ route('student.information') }}" class="hover:underline">About Us</a>
                <a href="{{ route('student.information') }}"   class="hover:underline">Policy</a>
                <a href="{{ route('student.information') }}"  class="hover:underline">Contact Us</a>
                <a href="{{ route('student.information') }}" class="hover:underline">Terms & Conditions</a>
            </div>
            <p class="text-gray-500 mt-2 ">© 2025 OrgBit. All rights reserved.</p>
        </div>
    </div>

    <!-- Join Event Modal -->
    <div id="joinEventModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[24px] font-semibold">Join Event</h2>
                <button onclick="closeModal('joinEventModal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="eventDetails">
                <!-- Event details will be dynamically inserted here -->
            </div>
            <div class="mt-4">
                <label class="flex items-center">
                    <input type="checkbox" id="confirmationCheckbox" class="mr-2">
                    <span>I confirm that I want to join this event.</span>
                </label>
            </div>
            <div class="mt-6 flex justify-end space-x-4">
                <button onclick="closeModal('joinEventModal')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                <button id="confirmJoinButton" class="bg-[#35408e] text-white px-4 py-2 rounded hover:bg-[#2c3579]">Join Event</button>
            </div>
        </div>
    </div>

    <!-- Join Organization Confirmation Modal -->
    <div id="joinOrgModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white p-6 rounded-lg w-[500px]">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-[24px] font-semibold">Join Organization</h2>
                <button onclick="closeModal('joinOrgModal')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="orgDetails">
                <!-- Organization details will be dynamically inserted here -->
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
        console.log(`Closing modal with ID: ${modalId}`); // Debugging log
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
    // Function to update the button state for events
    function updateEventButtonState(buttonSelector, hasJoined, eventEnded) {
        const button = document.querySelector(buttonSelector);
        if (button) {
            if (eventEnded) {
                // Event has ended
                button.disabled = true;
                button.textContent = 'Event Ended';
                button.classList.remove('bg-[#ffffff]', 'border-[#35408e]', 'hover:bg-[#35408e]', 'hover:border-[#35408e]', 'hover:text-[#ffffff]');
                button.classList.add('bg-[#dddddd]', 'cursor-not-allowed', 'text-[#666666]');
            } else if (hasJoined) {
                // Already joined the event
                button.disabled = true;
                button.textContent = 'Already Joined';
                button.classList.remove('bg-[#ffffff]', 'border-[#35408e]', 'hover:bg-[#35408e]', 'hover:border-[#35408e]', 'hover:text-[#ffffff]');
                button.classList.add('bg-[#dddddd]', 'cursor-not-allowed', 'text-[#666666]');
            } else {
                // Not joined the event
                button.disabled = false;
                button.textContent = 'Join Event';
                button.classList.remove('bg-[#dddddd]', 'cursor-not-allowed', 'text-[#666666]');
                button.classList.add('bg-[#ffffff]', 'border-[#35408e]', 'hover:bg-[#35408e]', 'hover:border-[#35408e]', 'hover:text-[#ffffff]');
            }
        }
    }

    // Check participation status for all events on page load
    document.querySelectorAll('.join-event-button').forEach(async (button) => {
        const eventId = button.dataset.eventId;

        try {
            const response = await fetch(`/check-event-participation/${eventId}`, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });

            if (!response.ok) {
                throw new Error('Failed to check event participation status.');
            }

            const data = await response.json();
            console.log('Event Participation Response:', data); // Debugging log

            if (data.success) {
                // Update the button state for events
                updateEventButtonState(
                    `.join-event-button[data-event-id="${eventId}"]`,
                    data.hasJoined,
                    data.eventEnded // Pass eventEnded flag
                );
            }
        } catch (error) {
            console.error('Error checking event participation:', error);
        }
    });

    // Check membership status for all organizations on page load
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
                // Update the button state for organizations
                updateOrgButtonState(`.join-button[data-org-id="${orgId}"]`, data.position_id);
            }
        } catch (error) {
            console.error('Error checking organization membership:', error);
        }
    });

    // Event Delegation for Join Event Buttons
    document.addEventListener('click', async (event) => {
        if (event.target.classList.contains('join-event-button')) {
            const eventId = event.target.dataset.eventId;

            try {
                // Fetch event details
                const eventResponse = await fetch(`/fetch-event-details/${eventId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                console.log(`Fetching event details for event ID: ${eventId}`); // Debug log

                if (!eventResponse.ok) {
                    const errorData = await eventResponse.json();
                    console.error(`Error fetching event details: ${errorData.message}`); // Debug log
                    throw new Error(errorData.message || 'Failed to fetch event details.');
                }

                const event = await eventResponse.json();
                console.log('Event details fetched:', event); // Debugging log

                // Check if the event has ended
                const eventEnded = new Date(event.event_end_date) < new Date();

                if (eventEnded) {
                    showErrorModal('This event has already ended.');
                    return;
                }

                // Ensure the organization ID is available
                const orgId = event.organization_id; // Make sure this is the correct property name

                if (!orgId) {
                    console.error('Organization ID is undefined.'); // Debug log
                    showErrorModal('Unable to determine organization membership status.');
                    return;
                }

                // Check if the user is a member of the organization
                const membershipUrl = `/check-organization-membership/${orgId}`;
                console.log(`Checking membership status at URL: ${membershipUrl}`); // Debug log

                const membershipResponse = await fetch(membershipUrl, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });

                if (!membershipResponse.ok) {
                    console.error(`Error checking membership status: ${membershipResponse.status} ${membershipResponse.statusText}`); // Debug log
                    throw new Error('Failed to check membership status.');
                }

                const membershipData = await membershipResponse.json();
                console.log('Membership status:', membershipData); // Debug log

                // Check if the user is a member (hasJoined should be true)
                if (!membershipData.hasJoined) {
                    // User is not a member, show an error message
                    showErrorModal('You must be a member of the organization to join this event.');
                    return;
                }

                // Populate the modal with event and organization details
                const eventDetails = `
                    <hr>
                    <br>
                    <p class="text-lg font-semibold">${event.organization_name}</p>
                    <br>
                    <p class="text-lg font-semibold">Event: ${event.event_name}</p>
                    <br>
                    <p><strong>What:</strong> ${event.event_name}</p>
                    <p><strong>Description:</strong> ${event.event_description || 'No description available.'}</p>
                    <p><strong>When:</strong> ${new Date(event.event_start_date).toLocaleString()} - ${new Date(event.event_end_date).toLocaleString()}</p>
                    <p><strong>Where:</strong> ${event.event_location}</p>
                `;
                document.getElementById('eventDetails').innerHTML = eventDetails;

                // Set the event ID in the modal
                document.getElementById('confirmJoinButton').dataset.eventId = eventId;

                // Open the modal
                console.log('Opening Join Event modal'); // Debugging log
                document.getElementById('joinEventModal').classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error); // Debugging log
                showErrorModal(error.message || 'Failed to fetch event details. Please try again.');
            }
        }
    });

    // Event Delegation for Join Organization Buttons
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
                    document.getElementById('orgDetails').innerHTML = `
                        <hr>
                        <br>
                        <p class="text-lg font-semibold">${orgData.org_name}</p>
                        <br>
                        <p><strong>Description:</strong> ${orgData.org_bio || 'No description available.'}</p>
                    `;

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

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Failed to join the organization.');
            }

            if (data.success) {
                // Update the button state for organizations
                updateOrgButtonState(`.join-button[data-org-id="${orgId}"]`, data.position_id);

                // Show success modal
                showSuccessModal(data.message);
                closeModal('joinOrgModal');
            } else {
                showErrorModal(data.message || 'Failed to join the organization.');
            }
        } catch (error) {
            console.error('Error joining organization:', error);
            showErrorModal('An error occurred while joining the organization.');
        }
    });

    // Confirm Join Event
    document.getElementById('confirmJoinButton').addEventListener('click', async () => {
        const eventId = document.getElementById('confirmJoinButton').dataset.eventId;
        const confirmationCheckbox = document.getElementById('confirmationCheckbox');

        if (!confirmationCheckbox.checked) {
            alert('Please confirm that you want to join this event.');
            return;
        }

        try {
            const response = await fetch(`/join-event/${eventId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            });

            const data = await response.json();

            if (!response.ok) {
                // Handle 403 Forbidden error
                if (response.status === 403) {
                    showErrorModal('You must be a member of the organization to join this event.');
                    return;
                }
                throw new Error(data.message || 'Failed to join the event.');
            }

            if (data.success) {
                // Update the button state for events
                updateEventButtonState(`.join-event-button[data-event-id="${eventId}"]`, true);

                // Show success modal
                showSuccessModal('You have successfully joined the event!');
                closeModal('joinEventModal');
            } else {
                showErrorModal(data.message || 'Failed to join the event.');
            }
        } catch (error) {
            console.error('Error joining event:', error);
            showErrorModal('An error occurred while joining the event.');
        }
    });

    // Infinite Scroll and Filter Logic
    let offset = 10; // Initial offset
    const postsContainer = document.getElementById('posts-container');
    let isLoading = false;
    let hasMorePosts = true;
    let currentFilter = 'all'; // Default filter

    // Load Posts Function
    function loadPosts(filter = currentFilter) {
    if (isLoading || !hasMorePosts) return;

    isLoading = true;
    postsContainer.insertAdjacentHTML('beforeend', '<div class="text-center py-4">Loading...</div>');

    fetch(`/fetch-more-posts?offset=${offset}&filter=${filter}`)
        .then(response => response.json())
        .then(posts => {
            const loadingIndicator = postsContainer.querySelector('.text-center');
            if (loadingIndicator) loadingIndicator.remove();

            if (posts.length > 0) {
                posts.forEach(post => {
                    const postHtml = `
                        <div class="border-[#dddddd] bg-[#ffffff] rounded-md shadow-lg border border-1 mb-5">
                            <div class="flex flex-row mt-5 ml-5">
                                <!-- Organization Logo -->
                                <div class="mr-5">
                                    <a href="">
                                        <img src="{{ asset('storage/logos/${post.organization.org_file_path}') }}" alt="Organization Logo" class="w-20 h-20 rounded-full object-cover border-2 border-[#dddddd] flex-shrink-0 max-w-[80px] max-h-[80px]">
                                    </a>
                                </div>
                                <div class="mt-4 break-words w-[500px]">
                                    <p class="font-bold">${post.organization.org_name}</p>
                                    <p>${post.formatted_date}</p>
                                </div>
                                <div class="align-right">
                                    <button class="join-event-button bg-[#ffffff] border border-1 border-[#35408e] rounded-md w-[150px] h-[30px] mt-4 
                                            hover:bg-[#35408e] hover:border-[#35408e] hover:text-[#ffffff] transition duration-200" 
                                            data-post-id="${post.post_id}" 
                                            data-event-id="${post.event.event_id}">
                                        Join Event
                                    </button>
                                </div>
                            </div>
                            <div class="caption p-5">
                                <p class="text-[22px]">${post.event.event_name}</p>
                                <br>
                                <p class="break-words">${post.post_content}</p>
                            </div>
                            ${post.media ? `
                                <div class="flex justify-center items-center">
                                    <div class="border rounded-md mt-5 mb-5 media-container">
                                        <img src="{{ asset('storage/posts/${post.media.file_name}') }}" alt="Event" class="media-container">
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    postsContainer.insertAdjacentHTML('beforeend', postHtml);

                    // Check participation status for the newly loaded post
                    const eventId = post.event.event_id;
                    fetch(`/check-event-participation/${eventId}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                updateEventButtonState(
                                    `.join-event-button[data-event-id="${eventId}"]`,
                                    data.hasJoined,
                                    data.eventEnded
                                );
                            }
                        })
                        .catch(error => {
                            console.error('Error checking event participation:', error);
                        });
                });
                offset += 10;
            } else {
                hasMorePosts = false;
            }
            isLoading = false;
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            const loadingIndicator = postsContainer.querySelector('.text-center');
            if (loadingIndicator) loadingIndicator.remove();
            postsContainer.insertAdjacentHTML('beforeend', '<div class="text-center py-4 text-red-500">Failed to load posts. Please try again.</div>');
            isLoading = false;
        });
}

    // Filter Posts
    document.querySelectorAll('.filter-button').forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.dataset.filter;

            // Remove 'selected' class from all buttons
            document.querySelectorAll('.filter-button').forEach(btn => {
                btn.classList.remove('selected');
                btn.classList.remove('bg-[#35408e]', 'text-white'); // Remove selected styles
            });

            // Add 'selected' class and styles to the clicked button
            button.classList.add('selected');
            button.classList.add('bg-[#35408e]', 'text-white'); // Add selected styles

            // Reset posts and load new filtered posts
            postsContainer.innerHTML = '';
            offset = 0;
            hasMorePosts = true;
            currentFilter = filter;
            loadPosts(filter);
        });
    });

    // Infinite Scroll
    window.addEventListener('scroll', () => {
        const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
        if (scrollTop + clientHeight >= scrollHeight - 100) {
            loadPosts(currentFilter);
        }
    });

    // Load initial posts
    loadPosts(currentFilter);
</script>
</body>
</html>